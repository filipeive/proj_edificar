<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController
{
    public function index()
    {
        $user = auth()->user();

        // Redirecionar baseado no role
        return match ($user->role) {
            'super_admin', 'admin', 'pastor_senior' => redirect()->route('dashboard.admin'),
            'comissao_obra' => redirect()->route('edificar.dashboard'),
            'responsavel_pacote' => redirect()->route('packages.dashboard'),
            'pastor_zona' => redirect()->route('dashboard.pastor'),
            'supervisor' => redirect()->route('dashboard.supervisor'),
            'lider_celula' => redirect()->route('dashboard.lider'),
            'secretaria' => redirect()->route('dashboard.secretaria'),
            'tesouraria' => redirect()->route('financial.dashboard'),
            'administracao' => redirect()->route('dashboard.administracao'),
            'membro' => redirect()->route('dashboard.membro'),
            default => redirect()->route('dashboard.membro'),
        };
    }
}
