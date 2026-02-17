<?php
namespace App\Http\Controllers\Admin;

use App\Models\Supervision;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Support\Facades\DB;
use App\Models\Cell;
use App\Models\Contribution;

class SupervisionController
{
    public function merge(Supervision $supervision): View
    {
        $targetSupervisions = Supervision::with('zone')
            ->where('id', '!=', $supervision->id)
            ->orderBy('name')
            ->get();

        return view('admin.supervisions.merge', [
            'sourceSupervision' => $supervision,
            'targetSupervisions' => $targetSupervisions
        ]);
    }

    public function processMerge(Request $request, Supervision $supervision)
    {
        $validated = $request->validate([
            'target_supervision_id' => 'required|exists:supervisions,id|different:supervision'
        ]);

        $targetSupervision = Supervision::findOrFail($validated['target_supervision_id']);

        try {
            DB::transaction(function () use ($supervision, $targetSupervision) {
                // 1. Move Cells (Handle name collisions if necessary, but unique is per supervision, so should be fine unless exactly same name exists)
                // However, Cell unique constraint is ['name', 'supervision_id']. So moving might cause collision if target has cell with same name.

                $cells = $supervision->cells;
                foreach ($cells as $cell) {
                    $exists = Cell::where('name', $cell->name)
                        ->where('supervision_id', $targetSupervision->id)
                        ->exists();

                    if ($exists) {
                        // Append suffix to avoid unique constraint violation
                        $cell->update([
                            'name' => $cell->name . ' (Mesclada)',
                            'supervision_id' => $targetSupervision->id
                        ]);
                    } else {
                        $cell->update(['supervision_id' => $targetSupervision->id]);
                    }
                }

                // 2. Move Contributions
                $supervision->contributions()->update(['supervision_id' => $targetSupervision->id]);

                // 3. Delete Source Supervision
                $supervision->delete();
            });

            return redirect()->route('supervisions.index')
                ->with('success', "Supervisão '{$supervision->name}' mesclada com '{$targetSupervision->name}' e excluída com sucesso.");

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao mesclar supervisões: ' . $e->getMessage());
        }
    }

    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Supervision::with('zone', 'cells', 'supervisor');

        if ($user->isPastorZona()) {
            $query->where('zone_id', $user->getZoneId());
        } elseif ($user->isSupervisor()) {
            $query->where('supervisor_id', $user->id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('zone', function ($qz) use ($search) {
                        $qz->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('supervisor', function ($qs) use ($search) {
                        $qs->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $supervisions = $query->paginate(24)->withQueryString();
        return view('admin.supervisions.index', ['supervisions' => $supervisions]);
    }

    public function create(): View
    {
        $user = auth()->user();
        $zonesQuery = Zone::query();

        if ($user->isPastorZona()) {
            $zonesQuery->where('id', $user->getZoneId());
        }

        $zones = $zonesQuery->orderBy('name')->get();
        $supervisors = \App\Models\User::where('role', 'supervisor')
            ->orWhere(function ($query) {
                $query->where('role', 'admin') // Allow admins for testing/fallback
                    ->orWhere('role', 'pastor_zona');
            })->orderBy('name')->get();

        $subSupervisors = \App\Models\User::whereIn('role', ['lider_celula', 'timoteo', 'sub_supervisor'])
            ->orderBy('name')
            ->get();

        return view('admin.supervisions.create', [
            'zones' => $zones,
            'supervisors' => $supervisors,
            'subSupervisors' => $subSupervisors
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'sub_supervisor_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $supervision = Supervision::create($validated);

        if (!empty($validated['sub_supervisor_id'])) {
            \App\Models\User::where('id', $validated['sub_supervisor_id'])->update(['role' => 'sub_supervisor']);
        }

        return redirect()->route('supervisions.index')
            ->with('success', 'Supervisão criada com sucesso!');
    }

    public function show(Supervision $supervision): View
    {
        $user = auth()->user();
        $query = Supervision::orderBy('name');

        if ($user->isPastorZona()) {
            $query->where('zone_id', $user->getZoneId());
        }

        $availableSupervisions = $query->where('id', '!=', $supervision->id)->get();
        $availableZones = Zone::orderBy('name')->where('id', '!=', $supervision->zone_id)->get();

        return view(
            'admin.supervisions.show',
            [
                'supervision' => $supervision->load('zone'),
                'cells' => $supervision->cells()
                    ->with('leader')
                    ->paginate(20)
                    ->withQueryString(),
                'availableSupervisions' => $availableSupervisions,
                'availableZones' => $availableZones
            ]
        );
    }

    public function edit(Supervision $supervision): View
    {
        $user = auth()->user();
        $zonesQuery = Zone::query();

        if ($user->isPastorZona()) {
            $zonesQuery->where('id', $user->getZoneId());
        }

        $zones = $zonesQuery->orderBy('name')->get();
        $supervisors = \App\Models\User::where('role', 'supervisor')
            ->orWhere(function ($query) {
                $query->where('role', 'admin')
                    ->orWhere('role', 'pastor_zona');
            })->orderBy('name')->get();

        $subSupervisors = \App\Models\User::whereIn('role', ['lider_celula', 'timoteo', 'sub_supervisor'])
            ->orderBy('name')
            ->get();

        return view('admin.supervisions.edit', [
            'supervision' => $supervision,
            'zones' => $zones,
            'supervisors' => $supervisors,
            'subSupervisors' => $subSupervisors
        ]);
    }

    public function update(Request $request, Supervision $supervision)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'sub_supervisor_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $supervision->update($validated);

        if (!empty($validated['sub_supervisor_id'])) {
            \App\Models\User::where('id', $validated['sub_supervisor_id'])->update(['role' => 'sub_supervisor']);
        }

        return redirect()->route('supervisions.index')
            ->with('success', 'Supervisão atualizada com sucesso!');
    }

    public function destroy(Supervision $supervision)
    {
        if ($supervision->cells()->exists()) {
            return back()->with('error', 'Não pode deletar supervisão com células!');
        }

        $supervision->delete();

        return redirect()->route('supervisions.index')
            ->with('success', 'Supervisão deletada com sucesso!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        $supervisions = Supervision::whereIn('id', $ids)->get();
        $deletedCount = 0;
        $skippedCount = 0;

        /** @var Supervision $supervision */
        foreach ($supervisions as $supervision) {
            if ($supervision->cells()->exists()) {
                $skippedCount++;
                continue;
            }
            $supervision->delete();
            $deletedCount++;
        }

        $message = "{$deletedCount} supervisões excluídas.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} foram puladas por possuírem células.";
        }

        return back()->with('success', $message);
    }

    public function reassignZone(Request $request, Supervision $supervision)
    {
        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
        ]);

        $supervision->update($validated);

        return back()->with('success', 'Supervisão transferida com sucesso para a nova zona!');
    }

    public function storeQuickSupervisor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|moz_phone',
            'zone_id' => 'required|exists:zones,id',
        ]);

        $email = $this->generateAutoEmail($validated['name']);
        $plainPassword = \Illuminate\Support\Str::random(8);

        $newUser = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $email,
            'phone' => $validated['phone'] ?? null,
            'password' => \Illuminate\Support\Facades\Hash::make($plainPassword),
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        if ($newUser->wantsNotification('member_created')) {
            $newUser->notify(new \App\Notifications\MemberCreatedNotification($newUser, $plainPassword));
        }

        return back()
            ->with('success', 'Supervisor criado com sucesso!')
            ->with('info', "Credenciais geradas — Email: {$email} | Senha: {$plainPassword}");
    }

    private function generateAutoEmail(string $name): string
    {
        $base = \Illuminate\Support\Str::slug($name, '.');
        $base = $base !== '' ? $base : 'supervisor';
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'edificar.local';

        $email = "{$base}@{$host}";
        $suffix = 1;

        while (\App\Models\User::where('email', $email)->exists()) {
            $email = "{$base}{$suffix}@{$host}";
            $suffix++;
        }

        return $email;
    }
}
