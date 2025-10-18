<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cell;
use App\Models\User;
use App\Models\UserCommitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController
{
    public function index(): View
    {
        $users = User::with('cell')->paginate(10);
        return view('admin.users.index', ['users' => $users]);
    }

    public function create(): View
    {
        $cells = Cell::all();
        $roles = ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin'];
        return view('admin.users.create', ['cells' => $cells, 'roles' => $roles]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|string',
            'role' => 'required|in:membro,lider_celula,supervisor,pastor_zona,admin',
            'cell_id' => 'nullable|exists:cells,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'Utilizador criado com sucesso!');
    }

    public function show(User $user): View
    {
        return view('admin.users.show', ['user' => $user->load('cell', 'commitments')]);
    }

    public function edit(User $user): View
    {
        $cells = Cell::all();
        $roles = ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin'];
        return view('admin.users.edit', [
            'user' => $user,
            'cells' => $cells,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'nullable|string',
            'role' => 'required|in:membro,lider_celula,supervisor,pastor_zona,admin',
            'cell_id' => 'nullable|exists:cells,id',
            'is_active' => 'boolean',
        ]);

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'Utilizador atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Não pode deletar admin!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilizador deletado com sucesso!');
    }
    public function createFromContext(Request $request): View
    {
        $user = auth()->user();
        $cell = $request->input('cell_id') ? Cell::find($request->input('cell_id')) : null;

        // Validar permissões
        if ($user->role === 'lider_celula') {
            $cell = $user->cell;
        } elseif ($user->role === 'supervisor') {
            $cell = null; // Supervisor pode escolher qualquer célula da sua supervisão
        } elseif ($user->role === 'pastor_zona') {
            $cell = null; // Pastor pode escolher qualquer célula da sua zona
        } elseif ($user->role !== 'admin') {
            abort(403, 'Você não tem permissão para criar membros');
        }

        // Obter células disponíveis baseado no role
        $availableCells = collect();
        if ($user->role === 'admin') {
            $availableCells = Cell::with('supervision')->get();
        } elseif ($user->role === 'lider_celula') {
            $availableCells = collect([$user->cell]);
        } elseif ($user->role === 'supervisor') {
            $availableCells = Cell::where('supervision_id', $user->cell->supervision_id)->get();
        } elseif ($user->role === 'pastor_zona') {
            $supervisionIds = $user->cell->supervision->zone->supervisions->pluck('id');
            $availableCells = Cell::whereIn('supervision_id', $supervisionIds)->get();
        }

        return view('admin.users.create-from-context', [
            'availableCells' => $availableCells,
            'selectedCell' => $cell,
            'userRole' => $user->role,
        ]);
    }

    public function storeFromContext(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string',
            'cell_id' => 'required|exists:cells,id',
            'password' => 'required|min:6|confirmed',
            'package_id' => 'nullable|exists:commitment_packages,id',
        ]);

        // Validar que o utilizador tem permissão para criar membro nesta célula
        $cell = Cell::find($validated['cell_id']);
        $this->validateCellPermission($user, $cell);

        // Criar utilizador
        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => bcrypt($validated['password']),
            'cell_id' => $validated['cell_id'],
            'role' => 'membro',
            'is_active' => true,
        ]);

        // Atribuir pacote padrão ou o escolhido
        $packageId = $validated['package_id'] ?? 1; // Pacote 1 por padrão
        UserCommitment::create([
            'user_id' => $newUser->id,
            'package_id' => $packageId,
            'start_date' => now(),
        ]);

        // Atualizar contagem de membros da célula
        $cell->update(['member_count' => $cell->getMembersCount()]);

        return redirect()->route('users.show', $newUser)
            ->with('success', 'Membro criado com sucesso! Pode agora registar contribuições em seu nome.');
    }

    private function validateCellPermission($user, $cell)
    {
        if ($user->role === 'lider_celula') {
            if ($cell->id !== $user->cell_id) {
                abort(403, 'Você só pode criar membros na sua célula');
            }
        } elseif ($user->role === 'supervisor') {
            if ($cell->supervision_id !== $user->cell->supervision_id) {
                abort(403, 'Você só pode criar membros nas suas células');
            }
        } elseif ($user->role === 'pastor_zona') {
            $supervisionIds = $user->cell->supervision->zone->supervisions->pluck('id');
            if (!$supervisionIds->contains($cell->supervision_id)) {
                abort(403, 'Você só pode criar membros na sua zona');
            }
        } elseif ($user->role !== 'admin') {
            abort(403, 'Você não tem permissão para criar membros');
        }
    }
}
