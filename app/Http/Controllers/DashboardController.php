<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController {
    public function index() {
        $user = auth()->user();

        // Redirecionar baseado no role
        return match($user->role) {
            'admin' => redirect()->route('dashboard.admin'),
            'pastor_zona' => redirect()->route('dashboard.pastor'),
            'supervisor' => redirect()->route('dashboard.supervisor'),
            'lider_celula' => redirect()->route('dashboard.lider'),
            'membro' => redirect()->route('dashboard.membro'),
            default => redirect()->route('dashboard.membro'),
        };
    }
}