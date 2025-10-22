<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contribution;
use App\Models\Cell;
use App\Models\Supervision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (strlen($query) < 3) {
            return response()->json(['results' => []]);
        }

        $user = Auth::user();
        $results = [
            'Membros' => [],
            'Contribuições' => []
        ];

        // 1. PESQUISAR MEMBROS
        $membersQuery = User::query()
            ->select('id', 'name', 'email', 'role')
            ->where('name', 'LIKE', '%' . $query . '%')
            ->where('is_active', true)
            ->limit(5);

        // Aplicar filtros de hierarquia
        if ($user->role === 'admin') {
            // Admin vê todos
            $membersQuery = $membersQuery;
        } elseif ($user->role === 'pastor_zona') {
            // Pastor vê membros da sua zona
            $supervisions = Supervision::where('zone_id', $user->zone_id)->pluck('id');
            $cells = Cell::whereIn('supervision_id', $supervisions)->pluck('id');
            $membersQuery = $membersQuery->whereIn('cell_id', $cells);
        } elseif ($user->role === 'supervisor') {
            // Supervisor vê membros da sua supervisão
            $cells = Cell::where('supervision_id', $user->supervision_id)->pluck('id');
            $membersQuery = $membersQuery->whereIn('cell_id', $cells);
        } elseif ($user->role === 'lider_celula') {
            // Líder vê membros da sua célula
            $membersQuery = $membersQuery->where('cell_id', $user->cell_id);
        } elseif ($user->role === 'membro') {
            // Membro vê apenas a si mesmo
            $membersQuery = $membersQuery->where('id', $user->id);
        }

        $results['Membros'] = $membersQuery->get()->toArray();

        // 2. PESQUISAR CONTRIBUIÇÕES
        $contributionsQuery = Contribution::query()
            ->select('id', 'amount', 'contribution_date', 'status', 'user_id')
            ->with('user:id,name')
            ->limit(5);

        // Pesquisar por valor ou data
        $contributionsQuery = $contributionsQuery->where(function ($q) use ($query) {
            $q->whereRaw('CAST(amount AS CHAR) LIKE ?', ['%' . $query . '%'])
              ->orWhereRaw('DATE_FORMAT(contribution_date, "%d/%m/%Y") LIKE ?', ['%' . $query . '%']);
        });

        // Aplicar filtros de hierarquia
        if ($user->role === 'admin') {
            // Admin vê tudo
            $contributionsQuery = $contributionsQuery;
        } elseif ($user->role === 'pastor_zona') {
            // Pastor vê contribuições da sua zona
            $supervisions = Supervision::where('zone_id', $user->zone_id)->pluck('id');
            $cells = Cell::whereIn('supervision_id', $supervisions)->pluck('id');
            $contributionsQuery = $contributionsQuery->whereIn('user_id', 
                User::whereIn('cell_id', $cells)->pluck('id')
            );
        } elseif ($user->role === 'supervisor') {
            // Supervisor vê contribuições da sua supervisão
            $cells = Cell::where('supervision_id', $user->supervision_id)->pluck('id');
            $contributionsQuery = $contributionsQuery->whereIn('user_id',
                User::whereIn('cell_id', $cells)->pluck('id')
            );
        } elseif ($user->role === 'lider_celula') {
            // Líder vê contribuições da sua célula
            $contributionsQuery = $contributionsQuery->whereIn('user_id',
                User::where('cell_id', $user->cell_id)->pluck('id')
            );
        } elseif ($user->role === 'membro') {
            // Membro vê apenas suas próprias contribuições
            $contributionsQuery = $contributionsQuery->where('user_id', $user->id);
        }

        $results['Contribuições'] = $contributionsQuery->get()->toArray();

        return response()->json(['results' => $results]);
    }
}