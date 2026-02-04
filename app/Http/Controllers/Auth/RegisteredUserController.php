<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cell;
use App\Models\User;
use App\Models\UserCommitment;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $cells = Cell::with('leader')->get();
        return view('auth.register', ['cells' => $cells]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'moz_phone'],
            'cell_id' => ['required', 'exists:cells,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'cell_id' => $validated['cell_id'],
            'role' => 'membro',
            'password' => Hash::make($validated['password']),
        ]);

        // Atribuir pacote padrão (Pacote 1)
        $defaultPackage = \App\Models\CommitmentPackage::find(1);
        if ($defaultPackage) {
            UserCommitment::create([
                'user_id' => $user->id,
                'package_id' => $defaultPackage->id,
                'cell_id' => $user->cell_id,
                'committed_amount' => $defaultPackage->min_amount,
                'start_date' => now(),
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard.membro', absolute: false));
    }
}
