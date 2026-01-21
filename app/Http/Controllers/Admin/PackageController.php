<?php
namespace App\Http\Controllers\Admin;

use App\Models\CommitmentPackage;
use App\Services\Sms\SmsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController
{
    public function index(): View
    {
        $user = auth()->user();
        if ($user->isResponsavelPacote()) {
            $packages = CommitmentPackage::where('responsible_id', $user->id)->get();
        } else {
            $packages = CommitmentPackage::orderBy('order')->get();
        }
        return view('admin.packages.index', ['packages' => $packages]);
    }

    public function create(): View
    {
        $users = \App\Models\User::whereIn('role', ['admin', 'comissao_obra', 'responsavel_pacote', 'secretaria', 'tesouraria', 'pastor_senior'])
            ->orderBy('name')
            ->get();
        return view('admin.packages.create', compact('users'));
    }

    public function store(Request $request)
    {
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
            ['package' => $package->load('userCommitments.user.cell.supervision.zone')]
        );
    }

    public function edit(CommitmentPackage $package): View
    {
        $user = auth()->user();
        if ($user->isResponsavelPacote() && $package->responsible_id !== $user->id) {
            abort(403, 'Acesso negado a este pacote.');
        }

        $users = \App\Models\User::whereIn('role', ['admin', 'comissao_obra', 'responsavel_pacote', 'secretaria', 'tesouraria', 'pastor_senior'])
            ->orderBy('name')
            ->get();
        return view('admin.packages.edit', ['package' => $package, 'users' => $users]);
    }

    public function update(Request $request, CommitmentPackage $package)
    {
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
            'phone' => 'nullable|string|max:20',
            'cell_id' => 'nullable|exists:cells,id',
            'committed_amount' => 'required|numeric|min:0',
        ]);

        $user = \App\Models\User::findOrFail($validated['user_id']);

        // Update user data if applicable
        $user->update([
            'phone' => $validated['phone'],
            'cell_id' => $validated['cell_id']
        ]);

        // Update the active commitment for this user and package
        $commitment = \App\Models\UserCommitment::where('user_id', $user->id)
            ->where('package_id', $package->id)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>', now());
            })
            ->first();

        if ($commitment) {
            $commitment->update([
                'committed_amount' => $validated['committed_amount'],
                'cell_id' => $validated['cell_id']
            ]);
        }

        return back()->with('success', 'Dados do membro atualizados com sucesso!');
    }

    public function export(CommitmentPackage $package)
    {
        $filename = 'membros_' . \Illuminate\Support\Str::slug($package->name) . '_' . now()->format('Y_m_d') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PackageMembersExport($package), $filename);
    }

    public function sendBulkSms(Request $request, CommitmentPackage $package, SmsService $smsService)
    {
        $membros = $package->userCommitments()->with('user')->get();
        $phones = $membros->pluck('user.phone')->filter()->toArray();

        if (empty($phones)) {
            return back()->with('error', 'Nenhuns membros com telefone encontrados neste pacote.');
        }

        $template = $package->whatsapp_template ?? "Olá [NOME], lembrete de contribuição para o Projetor Edificar.";

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
}