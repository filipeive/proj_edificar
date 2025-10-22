<?php

namespace App\Http\Controllers;

use App\Models\CommitmentPackage;
use App\Models\UserCommitment;
use App\Models\Cell;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

// Modelos de Notificação
use App\Notifications\CommitmentChosenNotification;

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

        if (!$user->cell_id) {
            return back()->with('error', 'O seu perfil deve estar associado a uma célula para assumir um compromisso.');
        }

        $commitmentValue = $package->min_amount;
        $activeCommitment = $user->getActiveCommitment();

        if ($activeCommitment && $activeCommitment->package_id === $package->id) {
            return redirect()->route('commitments.index')
                ->with('info', 'O pacote "' . $package->name . '" já está ativo.');
        }

        if ($activeCommitment) {
            $activeCommitment->update(['end_date' => now()]);
        }

        // Criar novo compromisso e capturar o model
        $userCommitment = UserCommitment::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'start_date' => now(),
            'committed_amount' => $commitmentValue,
            'cell_id' => $user->cell_id,
        ]);

        // Notificar o próprio utilizador como confirmação
        $user->notify(new CommitmentChosenNotification($userCommitment));

        // Notificar todos os admins (pode ajustar para notificar supervisores/pastores conforme necessidade)
        User::where('role', 'admin')->get()->each(function (User $admin) use ($userCommitment) {
            $admin->notify(new CommitmentChosenNotification($userCommitment));
        });

        return redirect()->route('commitments.index')
            ->with('success', 'Pacote atualizado com sucesso! Você escolheu: ' . $package->name);
    }


    public function current(): View
    {
        $commitment = auth()->user()->getActiveCommitment();

        return view('commitments.current', ['commitment' => $commitment]);
    }
}
