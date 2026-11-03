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
                'default' => "Paz Líder {lider}! [AÇÃO IMEDIATA]: Novo visitante alocado para a Célula {celula}: {visitante} (Tel: {telefone}, Bairro: {bairro}). Favor fazer contato de acolhimento e integrá-lo no próximo encontro!",
                'tags' => ['{lider}', '{celula}', '{visitante}', '{telefone}', '{bairro}', '{supervisao}', '{zona}'],
            ],
            'visitor_supervisor' => [
                'id' => 'visitor_supervisor',
                'key' => 'sms.template.visitor_supervisor',
                'title' => 'Rede: Ação Supervisor',
                'category' => 'visitor_network',
                'category_label' => 'Rede de Visitantes',
                'description' => 'Enviado ao supervisor para orientar e acompanhar o acolhimento do visitante pelo líder.',
                'default' => "Paz Supervisor {supervisor}! [AÇÃO]: O visitante {visitante} ({telefone}, bairro {bairro}) foi alocado à célula {celula}. Acompanhe o líder {lider} ({lider_telefone}) para acolhimento e feedback.",
                'tags' => ['{supervisor}', '{visitante}', '{telefone}', '{bairro}', '{celula}', '{supervisao}', '{lider}', '{lider_telefone}', '{zona}'],
            ],
            'visitor_pastor' => [
                'id' => 'visitor_pastor',
                'key' => 'sms.template.visitor_pastor',
                'title' => 'Rede: Informe Pastoral',
                'category' => 'visitor_network',
                'category_label' => 'Rede de Visitantes',
                'description' => 'Enviado ao pastor de zona para conhecimento e cobertura pastoral.',
                'default' => "Paz Pastor {pastor}! [INFORME PASTORAL]: O visitante {visitante} ({telefone}, bairro {bairro}) foi alocado à Célula {celula} (Zona: {zona}). Líder e Supervisor foram acionados para acolhimento.",
                'tags' => ['{pastor}', '{visitante}', '{telefone}', '{bairro}', '{celula}', '{supervisao}', '{zona}'],
            ],
            'visitor_welcome_cell' => [
                'id' => 'visitor_welcome_cell',
                'key' => 'sms.template.visitor_welcome_cell',
                'title' => 'Visitante: Boas-vindas com Célula',
                'category' => 'visitor_welcome',
                'category_label' => 'Boas-vindas & Acolhimento',
                'description' => 'Enviado ao visitante dando as boas-vindas da igreja e indicando sua célula e líder.',
                'default' => "Olá {visitante}! A família Life Church alegra-se com a sua visita! Conectamos você à Célula {celula}{lider_info}. Seja muito bem-vindo(a)!",
                'tags' => ['{visitante}', '{celula}', '{lider_info}', '{lider}', '{lider_telefone}'],
            ],
            'visitor_zone_pastor' => [
                'id' => 'visitor_zone_pastor',
                'key' => 'sms.template.visitor_zone_pastor',
                'title' => 'Rede: Novo Visitante na Zona (sem célula)',
                'category' => 'visitor_network',
                'category_label' => 'Rede de Visitantes',
                'description' => 'Enviado ao pastor de zona quando visitante é registrado na zona mas ainda não tem célula.',
                'default' => "Paz Pastor {pastor}! [NOVO VISITANTE]: {visitante} ({telefone}, bairro {bairro}) foi registrado na sua Zona ({zona}). Favor encaminhar ao supervisor para atribuição de célula.",
                'tags' => ['{pastor}', '{visitante}', '{telefone}', '{bairro}', '{zona}'],
            ],
            'visitor_welcome_general' => [
                'id' => 'visitor_welcome_general',
                'key' => 'sms.template.visitor_welcome_general',
                'title' => 'Visitante: Boas-vindas Inicial Geral',
                'category' => 'visitor_welcome',
                'category_label' => 'Boas-vindas & Acolhimento',
                'description' => 'Enviado ao visitante quando registrado sem célula definida.',
                'default' => "Olá {visitante}! Agradecemos pela sua visita à Life Church! Em breve entraremos em contato para acolher e orar por si. Deus abençoe!",
                'tags' => ['{visitante}'],
            ],
            'general_broadcast' => [
                'id' => 'general_broadcast',
                'key' => 'sms.template.general_broadcast',
                'title' => 'Modelo Base: Comunicado Geral',
                'category' => 'general',
                'category_label' => 'Comunicados Gerais',
                'description' => 'Modelo sugerido para avisos e convites disparados em massa.',
                'default' => "Paz {nome}! Lembramos de nosso encontro nesta semana na Life Church. Sua presença é muito importante. Deus abençoe!",
                'tags' => ['{nome}'],
            ],
        ];
    }

    /**
     * Dashboard / Painel da Central de SMS.
     */
    public function index(Request $request)
    {
        $query = SmsMessage::with(['sender', 'recipientUser'])->latest();

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

        // KPIs
        $baseKpi = SmsMessage::query();
        $totalSent = (clone $baseKpi)->where('status', 'sent')->count();
        $totalFailed = (clone $baseKpi)->where('status', 'failed')->count();
        $totalPending = (clone $baseKpi)->where('status', 'pending')->count();
        $totalToday = (clone $baseKpi)->whereDate('created_at', Carbon::today())->count();
        $totalAll = $totalSent + $totalFailed + $totalPending;
        $successRate = ($totalSent + $totalFailed > 0) ? round(($totalSent / ($totalSent + $totalFailed)) * 100) : 100;

        // Dados para o formulário de novo envio
        $zones = Zone::orderBy('name')->get();
        $supervisions = Supervision::with('zone')->orderBy('name')->get();
        $cells = Cell::with(['supervision.zone', 'leader'])->orderBy('name')->get();

        // Contagens prévias
        $counts = [
            'all_members'      => User::where('is_active', true)->whereNotNull('phone')->where('phone', '!=', '')->count(),
            'cell_leaders'     => User::whereIn('id', Cell::whereNotNull('leader_id')->pluck('leader_id'))->whereNotNull('phone')->where('phone', '!=', '')->count(),
            'supervisors'      => User::whereIn('id', Supervision::whereNotNull('supervisor_id')->pluck('supervisor_id'))->whereNotNull('phone')->where('phone', '!=', '')->count(),
            'pastors'          => User::whereIn('role', ['pastor', 'pastor_senior', 'pastor_zona'])->whereNotNull('phone')->where('phone', '!=', '')->count(),
            'pending_visitors' => Visitor::pending()->whereNotNull('phone')->where('phone', '!=', '')->count(),
            'all_visitors'     => Visitor::whereNotNull('phone')->where('phone', '!=', '')->count(),
        ];

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
            'totalToday',
            'totalAll',
            'successRate',
            'zones',
            'supervisions',
            'cells',
            'counts',
            'gatewayInfo',
            'templates'
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
                $users = User::where('is_active', true)->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                foreach ($users as $u) {
                    $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'membro'];
                }
                break;

            case 'cell_members':
                if ($request->filled('cell_id')) {
                    $users = User::where('cell_id', $request->cell_id)->where('is_active', true)->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                    foreach ($users as $u) {
                        $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'membro'];
                    }
                }
                break;

            case 'supervision_members':
                if ($request->filled('supervision_id')) {
                    $cellIds = Cell::where('supervision_id', $request->supervision_id)->pluck('id');
                    $users = User::whereIn('cell_id', $cellIds)->where('is_active', true)->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                    foreach ($users as $u) {
                        $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'membro'];
                    }
                }
                break;

            case 'zone_members':
                if ($request->filled('zone_id')) {
                    $supervisionIds = Supervision::where('zone_id', $request->zone_id)->pluck('id');
                    $cellIds = Cell::whereIn('supervision_id', $supervisionIds)->pluck('id');
                    $users = User::whereIn('cell_id', $cellIds)->where('is_active', true)->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                    foreach ($users as $u) {
                        $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'membro'];
                    }
                }
                break;

            case 'cell_leaders':
                $leaderIds = Cell::whereNotNull('leader_id')->pluck('leader_id');
                $users = User::whereIn('id', $leaderIds)->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                foreach ($users as $u) {
                    $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'lider'];
                }
                break;

            case 'supervisors':
                $supervisorIds = Supervision::whereNotNull('supervisor_id')->pluck('supervisor_id');
                $users = User::whereIn('id', $supervisorIds)->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                foreach ($users as $u) {
                    $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'supervisor'];
                }
                break;

            case 'pastors':
                $users = User::whereIn('role', ['pastor', 'pastor_senior', 'pastor_zona'])->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                foreach ($users as $u) {
                    $recipients[] = ['phone' => $u->phone, 'name' => $u->name, 'id' => $u->id, 'type' => 'pastor'];
                }
                break;

            case 'pending_visitors':
                $visitors = Visitor::pending()->whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
                foreach ($visitors as $v) {
                    $recipients[] = ['phone' => $v->phone, 'name' => $v->name, 'id' => $v->id, 'type' => 'visitante'];
                }
                break;

            case 'all_visitors':
                $visitors = Visitor::whereNotNull('phone')->where('phone', '!=', '')->get(['id', 'name', 'phone']);
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
}
