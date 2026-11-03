<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cell;
use App\Models\Congregation;
use App\Models\Setting;
use App\Models\SmsMessage;
use App\Models\Supervision;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Zone;
use App\Services\CongregationContext;
use App\Services\Sms\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsCenterController extends Controller
{
    protected SmsService $smsService;
    protected CongregationContext $congregationContext;

    public function __construct(SmsService $smsService, CongregationContext $congregationContext)
    {
        $this->smsService = $smsService;
        $this->congregationContext = $congregationContext;
    }

    /**
     * Lista de templates padrão do sistema.
     */
    public static function getDefaultTemplates(): array
    {
        return [
            'visitor_leader' => [
                'id' => 'visitor_leader',
                'key' => 'sms.template.visitor_leader',
                'title' => 'Rede: Ação Líder de Célula',
                'category' => 'visitor_network',
                'category_label' => 'Rede de Visitantes',
                'description' => 'Enviado ao líder da célula quando um novo visitante é alocado à sua célula.',
                'default' => "Paz, Líder {lider}. Temos um novo visitante direcionado para a sua Célula {celula}: {visitante}{contato}. Por favor, faça contato de acolhimento e convide-o para o próximo encontro da célula.",
                'tags' => ['{lider}', '{celula}', '{visitante}', '{contato}', '{a_convite_de}', '{quem_convidou}', '{telefone}', '{bairro}', '{supervisao}', '{zona}'],
            ],
            'visitor_supervisor' => [
                'id' => 'visitor_supervisor',
                'key' => 'sms.template.visitor_supervisor',
                'title' => 'Rede: Ação Supervisor',
                'category' => 'visitor_network',
                'category_label' => 'Rede de Visitantes',
                'description' => 'Enviado ao supervisor para orientar e acompanhar o acolhimento do visitante pelo líder.',
                'default' => "Paz, Supervisor {supervisor}. O visitante {visitante}{contato} foi direcionado para a Célula {celula}. Solicitamos o seu acompanhamento junto ao {lider_info} para assegurar a integração.",
                'tags' => ['{supervisor}', '{visitante}', '{contato}', '{a_convite_de}', '{quem_convidou}', '{celula}', '{supervisao}', '{lider}', '{lider_info}', '{zona}'],
            ],
            'visitor_pastor' => [
                'id' => 'visitor_pastor',
                'key' => 'sms.template.visitor_pastor',
                'title' => 'Rede: Informe Pastoral',
                'category' => 'visitor_network',
                'category_label' => 'Rede de Visitantes',
                'description' => 'Enviado ao pastor de zona para conhecimento e cobertura pastoral.',
                'default' => "Paz, Pastor {pastor}. Informamos que o visitante {visitante}{contato} foi acolhido e alocado à Célula {celula}, na Zona {zona}. O líder e o supervisor já foram acionados para o acolhimento.",
                'tags' => ['{pastor}', '{visitante}', '{contato}', '{a_convite_de}', '{quem_convidou}', '{celula}', '{supervisao}', '{zona}'],
            ],
            'visitor_welcome_cell' => [
                'id' => 'visitor_welcome_cell',
                'key' => 'sms.template.visitor_welcome_cell',
                'title' => 'Visitante: Boas-vindas pós-feedback (com Célula)',
                'category' => 'visitor_welcome',
                'category_label' => 'Boas-vindas & Acolhimento',
                'description' => 'Enviado ao visitante após o líder registrar feedback de contato ou integração, confirmando o acolhimento na célula.',
                'default' => "Paz e graça, {visitante}! Que alegria termos falado consigo. Seja muito bem-vindo à nossa Célula {celula}{lider_info}. Esperamos por si no próximo encontro!",
                'tags' => ['{visitante}', '{visitante_primeiro_nome}', '{celula}', '{lider_info}', '{lider}', '{lider_completo}', '{lider_telefone}', '{zona}'],
            ],
            'visitor_zone_pastor' => [
                'id' => 'visitor_zone_pastor',
                'key' => 'sms.template.visitor_zone_pastor',
                'title' => 'Rede: Novo Visitante na Zona (sem célula)',
                'category' => 'visitor_network',
                'category_label' => 'Rede de Visitantes',
                'description' => 'Enviado ao pastor de zona quando visitante é registrado na zona mas ainda não tem célula.',
                'default' => "Paz, Pastor {pastor}. Um novo visitante foi registrado na Zona {zona}: {visitante}{contato}. Favor orientar o supervisor para o devido acolhimento e encaminhamento à célula.",
                'tags' => ['{pastor}', '{visitante}', '{contato}', '{a_convite_de}', '{quem_convidou}', '{telefone}', '{bairro}', '{zona}'],
            ],
            'visitor_welcome_general' => [
                'id' => 'visitor_welcome_general',
                'key' => 'sms.template.visitor_welcome_general',
                'title' => 'Visitante: Boas-vindas pós-feedback (Geral)',
                'category' => 'visitor_welcome',
                'category_label' => 'Boas-vindas & Acolhimento',
                'description' => 'Enviado ao visitante após feedback de contato quando não possui célula vinculada.',
                'default' => "Paz e graça, {visitante}! Foi uma honra falarmos consigo. Seja muito bem-vindo à Life Church. Estamos à sua inteira disposição para orar e caminhar juntos!",
                'tags' => ['{visitante}', '{visitante_primeiro_nome}', '{zona}'],
            ],
            'general_broadcast' => [
                'id' => 'general_broadcast',
                'key' => 'sms.template.general_broadcast',
                'title' => 'Modelo Base: Comunicado Geral',
                'category' => 'general',
                'category_label' => 'Comunicados Gerais',
                'description' => 'Modelo sugerido para avisos e convites disparados em massa.',
                'default' => "Paz e graça, {nome}! Lembramos de nosso encontro nesta semana na Life Church. Sua presença é muito especial para nós. Esperamos por si!",
                'tags' => ['{nome}'],
            ],
        ];
    }

    /**
     * Dashboard / Painel da Central de SMS.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isScoped = $this->shouldScopeByPastoralZone($user);
        $userZoneName = null;

        $query = SmsMessage::with(['sender', 'recipientUser'])->latest();
        $this->applyPastoralScope($query, $user);

        // Filtro por Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por Contexto
        if ($request->filled('context')) {
            $query->where('context', $request->context);
        }

        // Filtro por Período
        $period = $request->get('period', 'all');
        if ($period === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period === '7days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($period === '30days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        // Busca Textual
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $messages = $query->paginate(20)->withQueryString();

        // KPIs (também com escopo pastoral aplicado)
        $baseKpi = SmsMessage::query();
        $this->applyPastoralScope($baseKpi, $user);
        $totalSent = (clone $baseKpi)->where('status', 'sent')->count();
        $totalFailed = (clone $baseKpi)->where('status', 'failed')->count();
        $totalPending = (clone $baseKpi)->where('status', 'pending')->count();
        $totalToday = (clone $baseKpi)->whereDate('created_at', Carbon::today())->count();
        $totalAll = $totalSent + $totalFailed + $totalPending;
        $successRate = ($totalSent + $totalFailed > 0) ? round(($totalSent / ($totalSent + $totalFailed)) * 100) : 100;

        // Dados para o formulário de novo envio (escopados se pastor de zona)
        if ($isScoped) {
            $scope = $this->getPastoralScopeData($user);
            $managedZoneIds = $scope['managedZoneIds'];
            $zones = Zone::whereIn('id', $managedZoneIds)->orderBy('name')->get();
            $userZoneName = $zones->pluck('name')->implode(', ');
            $supervisions = Supervision::whereIn('id', $scope['supervisionIds'])->with('zone')->orderBy('name')->get();
            $cells = Cell::whereIn('id', $scope['cellIds'])->with(['supervision.zone', 'leader'])->orderBy('name')->get();

            $counts = [
                'all_members'      => User::whereIn('cell_id', $scope['cellIds'])->where('is_active', true)->whereNotNull('phone')->where('phone', '!=', '')->count(),
                'cell_leaders'     => User::whereIn('id', Cell::whereIn('id', $scope['cellIds'])->whereNotNull('leader_id')->pluck('leader_id'))->whereNotNull('phone')->where('phone', '!=', '')->count(),
                'supervisors'      => User::whereIn('id', Supervision::whereIn('id', $scope['supervisionIds'])->whereNotNull('supervisor_id')->pluck('supervisor_id'))->whereNotNull('phone')->where('phone', '!=', '')->count(),
                'pastors'          => User::where('id', $user->id)->whereNotNull('phone')->where('phone', '!=', '')->count(),
                'pending_visitors' => Visitor::where(function($q) use ($managedZoneIds, $scope) {
                                          $q->whereIn('zone_id', $managedZoneIds)->orWhereIn('cell_id', $scope['cellIds']);
                                      })->pending()->whereNotNull('phone')->where('phone', '!=', '')->count(),
                'all_visitors'     => Visitor::where(function($q) use ($managedZoneIds, $scope) {
                                          $q->whereIn('zone_id', $managedZoneIds)->orWhereIn('cell_id', $scope['cellIds']);
                                      })->whereNotNull('phone')->where('phone', '!=', '')->count(),
            ];
        } else {
            $zones = Zone::orderBy('name')->get();
            $supervisions = Supervision::with('zone')->orderBy('name')->get();
            $cells = Cell::with(['supervision.zone', 'leader'])->orderBy('name')->get();

            $counts = [
                'all_members'      => User::where('is_active', true)->whereNotNull('phone')->where('phone', '!=', '')->count(),
                'cell_leaders'     => User::whereIn('id', Cell::whereNotNull('leader_id')->pluck('leader_id'))->whereNotNull('phone')->where('phone', '!=', '')->count(),
                'supervisors'      => User::whereIn('id', Supervision::whereNotNull('supervisor_id')->pluck('supervisor_id'))->whereNotNull('phone')->where('phone', '!=', '')->count(),
                'pastors'          => User::whereIn('role', ['pastor', 'pastor_senior', 'pastor_zona'])->whereNotNull('phone')->where('phone', '!=', '')->count(),
                'pending_visitors' => Visitor::pending()->whereNotNull('phone')->where('phone', '!=', '')->count(),
                'all_visitors'     => Visitor::whereNotNull('phone')->where('phone', '!=', '')->count(),
            ];
        }

        $driver = $this->smsService->getDriver();
        $gatewayInfo = [
            'driver' => $driver,
            'configured' => ($driver === 'httpsms' && config('services.httpsms.key') && config('services.httpsms.from'))
                || ($driver === 'mocean' && config('services.sms.api_key'))
                || ($driver === 'log'),
            'sender' => config('services.httpsms.from') ?? config('services.sms.sender_id', 'LIFECHURCH'),
        ];

        // Carregar templates com valores atuais
        $templates = self::getDefaultTemplates();
        foreach ($templates as $id => &$tpl) {
            $tpl['current'] = Setting::get($tpl['key'], $tpl['default']);
        }

        return view('admin.sms.index', compact(
            'messages',
            'totalSent',
            'totalFailed',
            'totalPending',
            'totalToday',
            'totalAll',
            'successRate',
            'zones',
            'supervisions',
            'cells',
            'counts',
            'gatewayInfo',
            'templates',
            'userZoneName',
            'isScoped'
        ));
    }

    /**
     * Enviar novo SMS avulso ou em massa.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'target_type'    => 'required|in:manual,all_members,cell_members,supervision_members,zone_members,cell_leaders,supervisors,pastors,pending_visitors,all_visitors',
            'manual_phone'   => 'required_if:target_type,manual|nullable|string',
            'cell_id'        => 'required_if:target_type,cell_members|nullable|exists:cells,id',
            'supervision_id' => 'required_if:target_type,supervision_members|nullable|exists:supervisions,id',
            'zone_id'        => 'required_if:target_type,zone_members|nullable|exists:zones,id',
            'message'        => 'required|string|max:1000',
        ], [
            'message.required' => 'O texto da mensagem é obrigatório.',
            'manual_phone.required_if' => 'Informe o número de telefone do destinatário.',
            'cell_id.required_if' => 'Selecione a célula de destino.',
            'supervision_id.required_if' => 'Selecione a supervisão de destino.',
            'zone_id.required_if' => 'Selecione a zona pastoral de destino.',
        ]);

        $recipients = $this->resolveRecipients($request);

        if (empty($recipients)) {
            return back()->with('error', 'Nenhum destinatário válido com número de telefone encontrado para este grupo.');
        }

        $meta = [
            'context' => $request->target_type === 'manual' ? 'custom' : 'broadcast',
            'user_id' => Auth::id(),
            'congregation_id' => $this->congregationContext->getActiveCongregationId(),
        ];

        $summary = $this->smsService->sendBulk($recipients, $request->message, $meta);

        if ($summary['sent'] > 0 && $summary['failed'] === 0) {
            $msg = $summary['total'] === 1
                ? "Mensagem SMS enviada com sucesso para {$recipients[0]['phone']}!"
                : "Sucesso! {$summary['sent']} mensagens SMS enviadas com êxito.";
            return redirect()->route('sms.index', ['tab' => 'history'])->with('success', $msg);
        } elseif ($summary['sent'] > 0 && $summary['failed'] > 0) {
            return redirect()->route('sms.index', ['tab' => 'history'])->with('warning', "Envio concluído: {$summary['sent']} enviados com sucesso, {$summary['failed']} falharam.");
        } else {
            return redirect()->route('sms.index', ['tab' => 'history'])->with('error', "Falha ao enviar mensagens. Verifique a configuração e saldo do gateway de SMS.");
        }
    }

    /**
     * Reenviar um SMS existente.
     */
    public function resend(SmsMessage $smsMessage)
    {
        $success = $this->smsService->resend($smsMessage);

        if ($success) {
            return back()->with('success', "SMS para {$smsMessage->phone} reenviado com sucesso!");
        }

        return back()->with('error', "Falha ao reenviar SMS: " . ($smsMessage->error_message ?? 'Erro desconhecido.'));
    }

    /**
     * Endpoint AJAX para obter a contagem de destinatários dinamicamente.
     */
    public function getRecipientsCount(Request $request)
    {
        $recipients = $this->resolveRecipients($request);
        return response()->json([
            'count' => count($recipients),
            'target_type' => $request->target_type,
        ]);
    }

    /**
     * Atualizar templates de mensagens SMS.
     */
    public function updateTemplates(Request $request)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isPastorSenior()) {
            return redirect()->route('sms.index', ['tab' => 'templates'])
                ->with('error', 'Apenas a Administração ou Pastor Sénior podem alterar os modelos globais de SMS.');
        }

        $templates = self::getDefaultTemplates();
        $updatedCount = 0;

        foreach ($templates as $id => $tpl) {
            $fieldName = "template_{$id}";
            if ($request->has($fieldName)) {
                $content = trim($request->input($fieldName));
                if (!empty($content)) {
                    Setting::set($tpl['key'], $content, 'string', 'sms_templates');
                    $updatedCount++;
                }
            }
        }

        return redirect()->route('sms.index', ['tab' => 'templates'])
            ->with('success', "{$updatedCount} template(s) de SMS salvos com sucesso!");
    }

    /**
     * Restaurar templates para os valores padrão originais.
     */
    public function resetTemplates(Request $request)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isPastorSenior()) {
            return redirect()->route('sms.index', ['tab' => 'templates'])
                ->with('error', 'Apenas a Administração ou Pastor Sénior podem restaurar os modelos globais de SMS.');
        }

        $templates = self::getDefaultTemplates();

        if ($request->filled('template_id') && isset($templates[$request->template_id])) {
            $tpl = $templates[$request->template_id];
            Setting::set($tpl['key'], $tpl['default'], 'string', 'sms_templates');
            return redirect()->route('sms.index', ['tab' => 'templates'])
                ->with('success', "Template '{$tpl['title']}' restaurado para o padrão original.");
        }

        foreach ($templates as $tpl) {
            Setting::set($tpl['key'], $tpl['default'], 'string', 'sms_templates');
        }

        return redirect()->route('sms.index', ['tab' => 'templates'])
            ->with('success', 'Todos os templates foram restaurados para os padrões originais com sucesso!');
    }

    /**
     * Resolve a lista de destinatários com nome e telefone com base nos filtros do formulário.
     */
    protected function resolveRecipients(Request $request): array
    {
        $user = auth()->user();
        $isScoped = $this->shouldScopeByPastoralZone($user);
        $scope = $isScoped ? $this->getPastoralScopeData($user) : null;

        $target = $request->target_type;
        $recipients = [];

        switch ($target) {
            case 'manual':
                $phones = preg_split('/[\s,;]+/', $request->manual_phone, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($phones as $p) {
                    $cleaned = preg_replace('/[^0-9]/', '', $p);
                    if (!empty($cleaned)) {
                        $recipients[] = [
                            'phone' => $cleaned,
                            'name'  => null,
                            'type'  => 'custom',
                        ];
                    }
                }
                break;

            case 'all_members':
                $query = User::where('is_active', true)->whereNotNull('phone')->where('phone', '!=', '');
                if ($isScoped) {
                    $query->whereIn('cell_id', $scope['cellIds']);
                }
                $users = $query->get(['id', 'name', 'phone']);
                foreach ($users as $u) {
                    $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'membro'];
                }
                break;

            case 'cell_members':
                if ($request->filled('cell_id')) {
                    if (!$isScoped || $scope['cellIds']->contains((int)$request->cell_id)) {
                        $users = User::where('cell_id', $request->cell_id)->where('is_active', true)->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                        foreach ($users as $u) {
                            $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'membro'];
                        }
                    }
                }
                break;

            case 'supervision_members':
                if ($request->filled('supervision_id')) {
                    if (!$isScoped || $scope['supervisionIds']->contains((int)$request->supervision_id)) {
                        $cellIds = Cell::where('supervision_id', $request->supervision_id)->pluck('id');
                        $users = User::whereIn('cell_id', $cellIds)->where('is_active', true)->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                        foreach ($users as $u) {
                            $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'membro'];
                        }
                    }
                }
                break;

            case 'zone_members':
                if ($request->filled('zone_id')) {
                    if (!$isScoped || $scope['managedZoneIds']->contains((int)$request->zone_id)) {
                        $supervisionIds = Supervision::where('zone_id', $request->zone_id)->pluck('id');
                        $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
                        $users = User::whereIn('cell_id', $cellIds)->where('is_active', true)->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                        foreach ($users as $u) {
                            $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'membro'];
                        }
                    }
                }
                break;

            case 'cell_leaders':
                $leaderQuery = Cell::whereNotNull('leader_id');
                if ($isScoped) {
                    $leaderQuery->whereIn('id', $scope['cellIds']);
                }
                $leaderIds = $leaderQuery->pluck('leader_id');
                $users = User::whereIn('id', $leaderIds)->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                foreach ($users as $u) {
                    $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'lider'];
                }
                break;

            case 'supervisors':
                $superQuery = Supervision::whereNotNull('supervisor_id');
                if ($isScoped) {
                    $superQuery->whereIn('id', $scope['supervisionIds']);
                }
                $supervisorIds = $superQuery->pluck('supervisor_id');
                $users = User::whereIn('id', $supervisorIds)->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                foreach ($users as $u) {
                    $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'supervisor'];
                }
                break;

            case 'pastors':
                $pastorQuery = User::whereIn('role', ['pastor', 'pastor_senior', 'pastor_zona'])->whereNotNull('phone')->where('phone', '!=', '');
                if ($isScoped) {
                    $pastorQuery->where('id', $user->id);
                }
                $users = $pastorQuery->get(['id', 'name', 'phone']);
                foreach ($users as $u) {
                    $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'pastor'];
                }
                break;

            case 'pending_visitors':
                $visitorQuery = Visitor::pending()->whereNotNull('phone')->where('phone', '!=', '');
                if ($isScoped) {
                    $visitorQuery->whereIn('id', $scope['visitorIds']);
                }
                $visitors = $visitorQuery->get(['id', 'name', 'phone']);
                foreach ($visitors as $v) {
                    $recipients[] = ['phone' => $v->phone, 'name' => $v->name, 'id' => $v->id, 'type' => 'visitante'];
                }
                break;

            case 'all_visitors':
                $visitorQuery = Visitor::whereNotNull('phone')->where('phone', '!=', '');
                if ($isScoped) {
                    $visitorQuery->whereIn('id', $scope['visitorIds']);
                }
                $visitors = $visitorQuery->get(['id', 'name', 'phone']);
                foreach ($visitors as $v) {
                    $recipients[] = ['phone' => $v->phone, 'name' => $v->name, 'id' => $v->id, 'type' => 'visitante'];
                }
                break;
        }

        // Remover duplicados pelo telefone
        $unique = [];
        $result = [];
        foreach ($recipients as $item) {
            $formatted = preg_replace('/[^0-9]/', '', $item['phone']);
            if (!isset($unique[$formatted])) {
                $unique[$formatted] = true;
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Verifica se o usuário deve ter escopo restrito à sua zona (pastor de zona ou supervisor).
     */
    protected function shouldScopeByPastoralZone(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return false;
        }

        return ($user->isPastorZona() || $user->isSupervisor()) && !$user->isAdmin() && !$user->isPastorSenior();
    }

    /**
     * Obtém IDs de zonas, supervisões, células, membros e visitantes sob gestão do pastor/supervisor.
     */
    protected function getPastoralScopeData(?User $user = null): array
    {
        $user = $user ?? auth()->user();
        $managedZoneIds = $user->getManagedZoneIds();
        $supervisionIds = Supervision::whereIn('zone_id', $managedZoneIds)->pluck('id');
        $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');

        $visitorIds = Visitor::where(function ($q) use ($managedZoneIds, $cellIds) {
            $q->whereIn('zone_id', $managedZoneIds)
                ->orWhereIn('cell_id', $cellIds);
        })->pluck('id');

        $zoneUserIds = User::where(function ($q) use ($cellIds, $supervisionIds, $user) {
            $q->whereIn('cell_id', $cellIds)
                ->orWhereIn('id', Cell::whereIn('id', $cellIds)->whereNotNull('leader_id')->select('leader_id'))
                ->orWhereIn('id', Supervision::whereIn('id', $supervisionIds)->whereNotNull('supervisor_id')->select('supervisor_id'))
                ->orWhere('id', $user->id);
        })->pluck('id');

        return [
            'managedZoneIds' => $managedZoneIds,
            'supervisionIds' => $supervisionIds,
            'cellIds'        => $cellIds,
            'visitorIds'     => $visitorIds,
            'zoneUserIds'    => $zoneUserIds,
        ];
    }

    /**
     * Aplica o escopo pastoral na consulta de mensagens SMS.
     */
    protected function applyPastoralScope($query, ?User $user = null): void
    {
        $user = $user ?? auth()->user();
        if (!$this->shouldScopeByPastoralZone($user)) {
            return;
        }

        $scope = $this->getPastoralScopeData($user);
        $visitorIds = $scope['visitorIds'];
        $zoneUserIds = $scope['zoneUserIds'];

        $query->where(function ($q) use ($user, $visitorIds, $zoneUserIds) {
            // Mensagens enviadas pelo próprio pastor/supervisor
            $q->where('user_id', $user->id)
                // Ou onde o destinatário é um membro/líder/supervisor ou ele próprio da sua zona
                ->orWhere(function ($sub) use ($zoneUserIds) {
                    $sub->whereNotNull('recipient_id')
                        ->whereIn('recipient_id', $zoneUserIds);
                })
                // Ou mensagens da rede de visitantes vinculadas aos visitantes da sua zona
                ->orWhere(function ($sub) use ($visitorIds) {
                    $sub->where('reference_type', Visitor::class)
                        ->whereIn('reference_id', $visitorIds);
                });
        });
    }
}
