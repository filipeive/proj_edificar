<?php
namespace App\Http\Controllers\Admin;

use App\Models\CommitmentPackage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController {
    public function index(): View {
        $packages = CommitmentPackage::orderBy('order')->get();
        return view('admin.packages.index', ['packages' => $packages]);
    }

    public function create(): View {
        return view('admin.packages.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|unique:commitment_packages|string|max:255',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            'description' => 'nullable|string',
            'order' => 'required|integer',
        ]);

        CommitmentPackage::create($validated);

        return redirect()->route('packages.index')
            ->with('success', 'Pacote criado com sucesso!');
    }

    public function show(CommitmentPackage $package): View {
        return view('admin.packages.show', 
            ['package' => $package->load('userCommitments')]);
    }

    public function edit(CommitmentPackage $package): View {
        return view('admin.packages.edit', ['package' => $package]);
    }

    public function update(Request $request, CommitmentPackage $package) {
        $validated = $request->validate([
            'name' => "required|unique:commitment_packages,name,{$package->id}|string|max:255",
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            'description' => 'nullable|string',
            'order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $package->update($validated);

        return redirect()->route('packages.index')
            ->with('success', 'Pacote atualizado com sucesso!');
    }

    public function destroy(CommitmentPackage $package) {
        if ($package->userCommitments()->exists()) {
            return back()->with('error', 'Não pode deletar pacote com membros comprometidos!');
        }

        $package->delete();

        return redirect()->route('packages.index')
            ->with('success', 'Pacote deletado com sucesso!');
    }
}