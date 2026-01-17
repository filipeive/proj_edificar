<?php
namespace App\Http\Controllers\Admin;

use App\Models\CommitmentPackage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController
{
    public function index(): View
    {
        $packages = CommitmentPackage::orderBy('order')->get();
        return view('admin.packages.index', ['packages' => $packages]);
    }

    public function create(): View
    {
        return view('admin.packages.create');
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
        ]);

        CommitmentPackage::create($validated);

        return redirect()->route('packages.index')
            ->with('success', 'Pacote criado com sucesso!');
    }

    public function show(CommitmentPackage $package): View
    {
        return view(
            'admin.packages.show',
            ['package' => $package->load('userCommitments')]
        );
    }

    public function edit(CommitmentPackage $package): View
    {
        return view('admin.packages.edit', ['package' => $package]);
    }

    public function update(Request $request, CommitmentPackage $package)
    {
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
        ]);

        $package->update($validated);

        return redirect()->route('packages.index')
            ->with('success', 'Pacote atualizado com sucesso!');
    }

    public function destroy(CommitmentPackage $package)
    {
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
}