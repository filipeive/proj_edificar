<?php
namespace App\Http\Controllers\Admin;

use App\Models\Cell;
use App\Models\CommitmentPackage;
use App\Models\Contribution;
use App\Models\User;
use App\Models\UserCommitment;
use App\Notifications\MemberCreatedNotification;
use App\Services\Sms\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PackageController
{
    public function index(): View
    {
        $user = auth()->user();
        if ($user->isResponsavelPacote()) {
            $packages = CommitmentPackage::where('responsible_id', $user->id)
                ->orderBy('order')
                ->paginate(24)
                ->withQueryString();
        } else {
            $packages = CommitmentPackage::orderBy('order')
                ->paginate(24)
                ->withQueryString();
        }
        return view('admin.packages.index', ['packages' => $packages]);
    }

    public function create(): View
    {
        if (auth()->user()->isResponsavelPacote()) {
            abort(403, 'Responsável de pacote não pode criar novos pacotes.');
        }

        $users = \App\Models\User::whereIn('role', ['super_admin', 'admin', 'comissao_obra', 'responsavel_pacote', 'secretaria', 'tesouraria', 'pastor_senior'])
            ->orderBy('name')
            ->get();
        return view('admin.packages.create', compact('users'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->isResponsavelPacote()) {
            abort(403, 'Responsável de pacote não pode criar novos pacotes.');
        }

        $validated = $request->validate([
            'name' => 'required|unique:commitment_packages|string|max:255',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            'description' => 'nullable|string',
            'whatsapp_link' => 'nullable|url',
            'sms_template' => 'nullable|string',
            'whatsapp_template' => 'nullable|string',
            'order' => 'required|integer',
            'responsible_id' => 'nullable|exists:users,id',
        ]);

        CommitmentPackage::create($validated);

        return redirect()->route('packages.index')
            ->with('success', 'Pacote criado com sucesso!');
    }

    public function show(CommitmentPackage $package): View
    {
        $user = auth()->user();
        if ($user->isResponsavelPacote() && $package->responsible_id !== $user->id) {
            abort(403, 'Acesso negado a este pacote.');
        }

        return view(
            'admin.packages.show',
            [
                'package' => $package->load('userCommitments.user.cell.supervision.zone'),
                'commitments' => $package->userCommitments()
                    ->active()
                    ->leftJoinSub(
                        Contribution::selectRaw('user_id, package_id, SUM(amount) as total_verified')
                            ->where('status', 'verificada')
                            ->groupBy('user_id', 'package_id'),
                        'verified_totals',
                        function ($join) {
                            $join->on('verified_totals.user_id', '=', 'user_commitments.user_id')
                                ->on('verified_totals.package_id', '=', 'user_commitments.package_id');
                        }
                    )
                    ->select('user_commitments.*')
                    ->when(request('search'), function ($query, $search) {
                        $query->whereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                    })
                    ->when(request('campaign_status'), function ($query, $status) {
                        if ($status === 'pending') {
                            $query->whereRaw('COALESCE(verified_totals.total_verified, 0) = 0');
                        } elseif ($status === 'partial') {
                            $query->whereRaw('COALESCE(verified_totals.total_verified, 0) > 0')
                                ->whereRaw('COALESCE(verified_totals.total_verified, 0) < user_commitments.committed_amount');
                        } elseif ($status === 'paid') {
                            $query->whereRaw('COALESCE(verified_totals.total_verified, 0) = user_commitments.committed_amount');
                        } elseif ($status === 'surplus') {
                            $query->whereRaw('COALESCE(verified_totals.total_verified, 0) > user_commitments.committed_amount');
                        }
                    })
                    ->with('user.cell.supervision.zone')
                    ->paginate(24)
                    ->withQueryString(),
                'commitmentUserIds' => $package->userCommitments()->active()->pluck('user_id'),
                'commitmentPhones' => $package->userCommitments()
                    ->active()
                    ->with('user')
                    ->get()
                    ->pluck('user.phone')
                    ->filter()
                    ->implode(', '),
                'allPackages' => CommitmentPackage::orderBy('order')->get(),
                'availableCells' => Cell::orderBy('name')->get(),
                'users' => User::active()
                    ->with([
                        'commitments' => function ($q) {
                            $q->whereNull('end_date')->orWhere('end_date', '>', now());
                        },
                        'commitments.package'
                    ])
                    ->orderBy('name')
                    ->get(),
            ]
        );
    }

    public function edit(CommitmentPackage $package): View
    {
        if (auth()->user()->isResponsavelPacote()) {
            abort(403, 'Responsável de pacote não pode editar pacotes.');
        }

        $user = auth()->user();
        if ($user->isResponsavelPacote() && $package->responsible_id !== $user->id) {
            abort(403, 'Acesso negado a este pacote.');
        }

        $users = \App\Models\User::whereIn('role', ['super_admin', 'admin', 'comissao_obra', 'responsavel_pacote', 'secretaria', 'tesouraria', 'pastor_senior'])
            ->orderBy('name')
            ->get();
        return view('admin.packages.edit', ['package' => $package, 'users' => $users]);
    }

    public function update(Request $request, CommitmentPackage $package)
    {
        if (auth()->user()->isResponsavelPacote()) {
            abort(403, 'Responsável de pacote não pode editar pacotes.');
        }

        $user = auth()->user();
        if ($user->isResponsavelPacote() && $package->responsible_id !== $user->id) {
            abort(403, 'Acesso negado a este pacote.');
        }

        $validated = $request->validate([
            'name' => "required|unique:commitment_packages,name,{$package->id}|string|max:255",
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            'description' => 'nullable|string',
            'whatsapp_link' => 'nullable|url',
            'sms_template' => 'nullable|string',
            'whatsapp_template' => 'nullable|string',
            'order' => 'required|integer',
            'is_active' => 'boolean',
            'responsible_id' => 'nullable|exists:users,id',
        ]);

        $package->update($validated);

        return redirect()->route('packages.index')
            ->with('success', 'Pacote atualizado com sucesso!');
    }

    public function destroy(CommitmentPackage $package)
    {
        if (auth()->user()->isResponsavelPacote()) {
            abort(403, 'Responsável de pacote não pode excluir pacotes.');
        }

        $user = auth()->user();
        if ($user->isResponsavelPacote() && $package->responsible_id !== $user->id) {
            abort(403, 'Acesso negado a este pacote.');
        }

        if ($package->userCommitments()->exists()) {
            return back()->with('error', 'Não pode deletar pacote com membros comprometidos!');
        }

        $package->delete();

        return redirect()->route('packages.index')
            ->with('success', 'Pacote deletado com sucesso!');
    }

    public function assignMember(Request $request, CommitmentPackage $package)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'committed_amount' => 'required|numeric|min:0',
        ]);

        $user = \App\Models\User::findOrFail($validated['user_id']);

        // Check if user has active commitment
        $activeCommitment = $user->getActiveCommitment();
        if ($activeCommitment) {
            $activeCommitment->update(['end_date' => now()]);
        }

        \App\Models\UserCommitment::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'committed_amount' => $validated['committed_amount'],
            'start_date' => now(),
            'cell_id' => $user->cell_id,
        ]);

        return back()->with('success', 'Membro adicionado ao pacote com sucesso!');
    }

    public function updateMember(Request $request, CommitmentPackage $package)
    {
        $authUser = auth()->user();
        $isAuthorized = $authUser->isAdmin() ||
            $authUser->isSecretaria() ||
            $authUser->isComissaoObra() ||
            ($authUser->isResponsavelPacote() && $package->responsible_id === $authUser->id);

        if (!$isAuthorized) {
            return back()->with('error', 'Não tem permissão para editar membros deste pacote.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'phone' => 'nullable|moz_phone',
            'cell_id' => 'nullable|exists:cells,id',
            'committed_amount' => 'required|numeric|min:0',
        ]);

        $user = \App\Models\User::findOrFail($validated['user_id']);

        // Update user data if applicable
        $user->update([
            'phone' => $validated['phone'],
            'cell_id' => $validated['cell_id']
        ]);

        // Update the commitment for this user and package
        $commitment = \App\Models\UserCommitment::where('user_id', $user->id)
            ->where('package_id', $package->id)
            ->active()
            ->first();

        if (!$commitment) {
            // Fallback to any latest commitment for this package if no active one found
            $commitment = \App\Models\UserCommitment::where('user_id', $user->id)
                ->where('package_id', $package->id)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if ($commitment) {
            $commitment->update([
                'committed_amount' => $validated['committed_amount'],
                'cell_id' => $validated['cell_id'],
                'end_date' => null // Ensure it's active if we are editing it
            ]);
        } else {
            return back()->with('error', 'Compromisso não encontrado para este membro neste pacote.');
        }

        return back()->with('success', 'Dados do membro atualizados com sucesso!');
    }

    public function export(CommitmentPackage $package)
    {
        $filename = 'membros_' . \Illuminate\Support\Str::slug($package->name) . '_' . now()->format('Y_m_d') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PackageMembersExport($package), $filename);
    }

    public function exportPdf(Request $request, CommitmentPackage $package)
    {
        $user = auth()->user();
        if ($user->isResponsavelPacote() && $package->responsible_id !== $user->id) {
            abort(403, 'Acesso negado a este pacote.');
        }

        $now = now();
        if ($now->day <= 5) {
            $startDate = $now->copy()->subMonth()->startOfMonth()->addDays(19);
            $endDate = $now->copy()->startOfMonth()->addDays(4);
        } else {
            $startDate = $now->copy()->startOfMonth()->addDays(19);
            $endDate = $now->copy()->addMonth()->startOfMonth()->addDays(4);
        }

        $statusFilter = $request->query('export_status', 'all');
        $sortBy = $request->query('sort_by', 'name_asc');

        $paidByUser = \App\Models\Contribution::verified()
            ->where('package_id', $package->id)
            ->whereBetween('contribution_date', [$startDate, $endDate])
            ->selectRaw('user_id, SUM(amount) as total_paid, MAX(contribution_date) as last_paid_date')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $rows = $package->userCommitments()
            ->with(['user.cell.supervision.zone', 'user.cell.supervision.zone.pastor'])
            ->get()
            ->map(function ($commitment) use ($paidByUser) {
                $user = $commitment->user;
                $cell = $user->cell;
                $supervision = $cell?->supervision;
                $zone = $supervision?->zone;

                $paidInfo = $paidByUser->get($user->id);
                $committed = (float) $commitment->committed_amount;
                $paid = (float) ($paidInfo->total_paid ?? 0);
                $progress = $committed > 0 ? round(($paid / $committed) * 100, 1) : 0;

                if ($paid <= 0) {
                    $status = 'pending';
                    $statusLabel = 'Pendente';
                } elseif ($paid < $committed) {
                    $status = 'partial';
                    $statusLabel = 'Parcial';
                } elseif ($paid > $committed) {
                    $status = 'surplus';
                    $statusLabel = 'Pago c/ Acréscimo';
                } else {
                    $status = 'paid';
                    $statusLabel = 'Pago';
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone ?? 'N/A',
                    'cell' => $cell->name ?? 'N/A',
                    'supervision' => $supervision->name ?? 'N/A',
                    'zone' => $zone->name ?? 'N/A',
                    'pastor' => $zone?->pastor?->name ?? 'N/A',
                    'committed_value' => $committed,
                    'paid_value' => $paid,
                    'progress' => max(0, $progress),
                    'status' => $status,
                    'status_label' => $statusLabel,
                    'committed' => number_format($committed, 2, ',', '.') . ' MT',
                    'paid' => number_format($paid, 2, ',', '.') . ' MT',
                    'paid_date' => !empty($paidInfo?->last_paid_date)
                        ? \Illuminate\Support\Carbon::parse($paidInfo->last_paid_date)->format('d/m/Y')
                        : '-',
                ];
            });

        if ($statusFilter !== 'all') {
            $rows = $rows->where('status', $statusFilter)->values();
        }

        $rows = match ($sortBy) {
            'name_desc' => $rows->sortByDesc('name', SORT_NATURAL | SORT_FLAG_CASE)->values(),
            'committed_desc' => $rows->sortByDesc('committed_value')->values(),
            'paid_desc' => $rows->sortByDesc('paid_value')->values(),
            'progress_desc' => $rows->sortByDesc('progress')->values(),
            default => $rows->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values(),
        };

        $summary = [
            'members' => $rows->count(),
            'committed_total' => (float) $rows->sum('committed_value'),
            'paid_total' => (float) $rows->sum('paid_value'),
            'pending_total' => (float) $rows->sum(fn($row) => max(0, $row['committed_value'] - $row['paid_value'])),
            'by_status' => [
                'pending' => $rows->where('status', 'pending')->count(),
                'partial' => $rows->where('status', 'partial')->count(),
                'paid' => $rows->where('status', 'paid')->count(),
                'surplus' => $rows->where('status', 'surplus')->count(),
            ],
            'applied_status' => $statusFilter,
            'sort_by' => $sortBy,
        ];

        $pdf = Pdf::loadView('admin.packages.export-pdf', [
            'package' => $package,
            'rows' => $rows,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'summary' => $summary,
        ])->setPaper('a4', 'landscape');

        $filename = 'membros_' . \Illuminate\Support\Str::slug($package->name) . '_' . now()->format('Y_m_d') . '.pdf';
        return $pdf->download($filename);
    }

    public function sendBulkSms(Request $request, CommitmentPackage $package, SmsService $smsService)
    {
        $membros = $package->userCommitments()->with('user')->get();
        $phones = $membros->pluck('user.phone')->filter()->toArray();

        if (empty($phones)) {
            return back()->with('error', 'Nenhuns membros com telefone encontrados neste pacote.');
        }

        $template = $package->sms_template ?? "Olá [NOME], lembrete de contribuição para o Projetor Edificar.";

        $successCount = 0;
        foreach ($membros as $membro) {
            if ($membro->user->phone) {
                $message = str_replace('[NOME]', $membro->user->name, $template);
                if ($smsService->send($membro->user->phone, $message)) {
                    $successCount++;
                }
            }
        }

        return back()->with('success', "SMS enviado com sucesso para $successCount membros!");
    }

    public function sendMemberSms(Request $request, CommitmentPackage $package, User $user, SmsService $smsService)
    {
        $authUser = auth()->user();
        $isAuthorized = $authUser->isAdmin() ||
            $authUser->isSecretaria() ||
            $authUser->isComissaoObra() ||
            ($authUser->isResponsavelPacote() && $package->responsible_id === $authUser->id);

        if (!$isAuthorized) {
            return back()->with('error', 'Não tem permissão para enviar SMS deste pacote.');
        }

        $commitment = UserCommitment::where('user_id', $user->id)
            ->where('package_id', $package->id)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>', now());
            })
            ->first();

        if (!$commitment) {
            return back()->with('error', 'Membro não pertence a este pacote.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        if (!$user->phone) {
            return back()->with('error', 'Este membro não possui telefone registado.');
        }

        $message = str_replace('[NOME]', $user->name, $validated['message']);

        if ($smsService->send($user->phone, $message)) {
            return back()->with('success', "SMS enviado para {$user->name}!");
        }

        return back()->with('error', 'Falha ao enviar SMS. Verifique o provedor/configuração.');
    }

    public function storeQuickMember(Request $request, CommitmentPackage $package)
    {
        $authUser = auth()->user();
        $isAuthorized = $authUser->isAdmin() ||
            $authUser->isComissaoObra() ||
            ($authUser->isResponsavelPacote() && $package->responsible_id === $authUser->id);

        if (!$isAuthorized) {
            return back()->with('error', 'Não tem permissão para criar membros neste pacote.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|moz_phone',
            'cell_id' => 'required|exists:cells,id',
            'committed_amount' => 'nullable|numeric|min:0',
        ]);

        $email = $this->generateAutoEmail($validated['name']);
        $plainPassword = Str::random(8);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $email,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($plainPassword),
            'cell_id' => $validated['cell_id'],
            'role' => 'membro',
            'is_active' => true,
        ]);

        UserCommitment::create([
            'user_id' => $newUser->id,
            'package_id' => $package->id,
            'committed_amount' => $validated['committed_amount'] ?? $package->min_amount,
            'start_date' => now(),
            'cell_id' => $newUser->cell_id,
        ]);

        if ($newUser->wantsNotification('member_created')) {
            $newUser->notify(new MemberCreatedNotification($newUser, $plainPassword));
        }

        return back()
            ->with('success', 'Membro criado e adicionado ao pacote com sucesso!')
            ->with('info', "Credenciais geradas — Email: {$email} | Senha: {$plainPassword}");
    }

    private function generateAutoEmail(string $name): string
    {
        $base = Str::slug($name, '.');
        $base = $base !== '' ? $base : 'membro';
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'edificar.local';

        $email = "{$base}@{$host}";
        $suffix = 1;

        while (User::where('email', $email)->exists()) {
            $email = "{$base}{$suffix}@{$host}";
            $suffix++;
        }

        return $email;
    }

    public function removeMember(CommitmentPackage $package, \App\Models\User $user)
    {
        $authUser = auth()->user();
        $isAuthorized = $authUser->isAdmin() ||
            $authUser->isSecretaria() ||
            $authUser->isComissaoObra() ||
            ($authUser->isResponsavelPacote() && $package->responsible_id === $authUser->id);

        if (!$isAuthorized) {
            return back()->with('error', 'Não tem permissão para remover membros.');
        }

        $commitment = \App\Models\UserCommitment::where('user_id', $user->id)
            ->where('package_id', $package->id)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>', now());
            })
            ->first();

        if ($commitment) {
            $commitment->update(['end_date' => now()]);
            return back()->with('success', 'Membro removido do pacote com sucesso!');
        }

        return back()->with('error', 'Membro não encontrado ou já removido.');
    }

    public function changePackage(Request $request, CommitmentPackage $package, \App\Models\User $user)
    {
        $request->validate([
            'new_package_id' => 'required|exists:commitment_packages,id',
            'committed_amount' => 'required|numeric|min:0'
        ]);

        $newPackage = CommitmentPackage::findOrFail($request->new_package_id);

        // Remove from current
        $currentCommitment = \App\Models\UserCommitment::where('user_id', $user->id)
            ->where('package_id', $package->id)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>', now());
            })
            ->first();

        if ($currentCommitment) {
            $currentCommitment->update(['end_date' => now()]);
        }

        // Add to new
        \App\Models\UserCommitment::create([
            'user_id' => $user->id,
            'package_id' => $newPackage->id,
            'committed_amount' => $request->committed_amount,
            'start_date' => now(),
            'cell_id' => $user->cell_id, // Preserve cell info
        ]);

        return back()->with('success', "Membro movido para o pacote {$newPackage->name} com sucesso!");
    }

    public function bulkRemoveMembers(Request $request, CommitmentPackage $package)
    {
        $authUser = auth()->user();
        if (!$authUser->isAdmin() && !$authUser->isSecretaria()) {
            return back()->with('error', 'Apenas Admin e Secretaria podem realizar remoção em massa.');
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        \App\Models\UserCommitment::whereIn('user_id', $request->user_ids)
            ->where('package_id', $package->id)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>', now());
            })
            ->update(['end_date' => now()]);

        return back()->with('success', 'Membros selecionados foram removidos do pacote.');
    }
}
