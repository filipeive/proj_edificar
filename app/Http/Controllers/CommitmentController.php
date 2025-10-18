<?php

namespace App\Http\Controllers;

use App\Models\CommitmentPackage;
use App\Models\UserCommitment;
use App\Models\Cell;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommitmentController
{
    public function index(): View
    {
        $packages = CommitmentPackage::where('is_active', true)
            ->orderBy('order')
            ->get();

        $userCommitment = auth()->user()->getActiveCommitment();

        return view('commitments.index', [
            'packages' => $packages,
            'currentCommitment' => $userCommitment,
        ]);
    }

    /**
     * Altera o compromisso do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /*   public function choose(Request $request) {
        $validated = $request->validate([
            'package_id' => 'required|exists:commitment_packages,id',
        ]);

        $user = auth()->user();
        $package = CommitmentPackage::find($validated['package_id']);

        // Encerrar compromisso anterior
        $activeCommitment = $user->getActiveCommitment();
        if ($activeCommitment) {
            $activeCommitment->update(['end_date' => now()]);
        }

        // Criar novo compromisso
        UserCommitment::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'start_date' => now(),
        ]);

        return redirect()->route('commitments.index')
            ->with('success', 'Pacote atualizado com sucesso! Você escolheu: ' . $package->name);
    } */
    public function choose(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:commitment_packages,id',
        ]);

        $user = auth()->user();
        $package = CommitmentPackage::find($validated['package_id']);

        // 2. Checagem de célula (necessário após a migração)
        if (!$user->cell_id) {
            return back()->with('error', 'O seu perfil deve estar associado a uma célula para assumir um compromisso.');
        }

        // 3. Definir o valor do compromisso (Min_amount como padrão)
        $commitmentValue = $package->min_amount;

        // 4. Encerrar o compromisso anterior ATIVO
        $activeCommitment = $user->getActiveCommitment();

        // Lógica do TOGGLE:
        if ($activeCommitment && $activeCommitment->package_id === $package->id) {
            // Se o usuário clicou no pacote que JÁ está ativo: Não faça nada, é apenas um display de "Ativo".
            return redirect()->route('commitments.index')
                ->with('info', 'O pacote "' . $package->name . '" já está ativo.');
        }

        // 5. Se houver um compromisso ativo DIFERENTE, encerre-o
        if ($activeCommitment) {
            // Para evitar a colisão de data no mesmo segundo (SQLSTATE[23000]), 
            // usaremos um pequeno atraso de tempo ou apenas a data atual
            $activeCommitment->update(['end_date' => now()]);
        }

        // 6. Criar novo compromisso
        // Verificamos se já existe um compromisso 'pendente' ou 'ativo' com o mesmo pacote
        // Isso é uma salvaguarda contra múltiplos cliques rápidos, mas a lógica acima já previne a duplicidade do mesmo pacote.

        UserCommitment::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'start_date' => now(),
            'committed_amount' => $commitmentValue,
            'cell_id' => $user->cell_id,
        ]);

        return redirect()->route('commitments.index')
            ->with('success', 'Pacote atualizado com sucesso! Você escolheu: ' . $package->name);
    }

    public function current(): View
    {
        $commitment = auth()->user()->getActiveCommitment();

        return view('commitments.current', ['commitment' => $commitment]);
    }
}
