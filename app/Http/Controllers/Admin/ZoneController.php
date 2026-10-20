<?php
namespace App\Http\Controllers\Admin;

use App\Models\Zone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceZoneParticipation;
use App\Models\QuarterlyReport;
use App\Models\Visitor;
use Illuminate\Validation\Rule;
use App\Services\CongregationContext;

class ZoneController
{
    public function merge(Zone $zone): View
    {
        $targetZones = Zone::where('id', '!=', $zone->id)->orderBy('name')->get();
        return view('admin.zones.merge', ['sourceZone' => $zone, 'targetZones' => $targetZones]);
    }

    public function processMerge(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'target_zone_id' => 'required|exists:zones,id|different:zone'
        ]);

        $targetZone = Zone::findOrFail($validated['target_zone_id']);

        try {
            DB::transaction(function () use ($zone, $targetZone) {
                // 1. Move Supervisions (handle duplicate names)
                $sourceSupervisions = $zone->supervisions()->get();
                foreach ($sourceSupervisions as $sourceSupervision) {
                    $existingSupervision = $targetZone->supervisions()
                        ->where('name', $sourceSupervision->name)
                        ->first();

                    if ($existingSupervision) {
                        // Merge cells into existing supervision
                        $sourceSupervision->cells()->update(['supervision_id' => $existingSupervision->id]);
                        $sourceSupervision->delete();
                    } else {
                        $sourceSupervision->update(['zone_id' => $targetZone->id]);
                    }
                }

                // 2. Move Contributions
                $zone->contributions()->update(['zone_id' => $targetZone->id]);

                // 3. Move Events
                $zone->events()->update(['zone_id' => $targetZone->id]);

                // 4. Move Visitors
                Visitor::where('zone_id', $zone->id)->update(['zone_id' => $targetZone->id]);


                // 5. Move Service Zone Participations
                // Handle duplicate conflicts: if target already has participation for same service
                $participations = ServiceZoneParticipation::where('zone_id', $zone->id)->get();
                foreach ($participations as $participation) {
                    $exists = ServiceZoneParticipation::where('service_id', $participation->service_id)
                        ->where('zone_id', $targetZone->id)
                        ->exists();

                    if ($exists) {
                        // Conflict: Delete source participation
                        $participation->delete();
                    } else {
                        $participation->update(['zone_id' => $targetZone->id]);
                    }
                }

                // 6. Move Quarterly Reports
                $reports = QuarterlyReport::where('zone_id', $zone->id)->get();
                foreach ($reports as $report) {
                    $exists = QuarterlyReport::where('year', $report->year)
                        ->where('quarter', $report->quarter)
                        ->where('zone_id', $targetZone->id)
                        ->exists();

                    if ($exists) {
                        // Conflict: Delete source report
                        $report->delete();
                    } else {
                        $report->update(['zone_id' => $targetZone->id]);
                    }
                }

                // 7. Delete Source Zone
                $zone->delete();
            });

            return redirect()->route('zones.index')
                ->with('success', "Zona '{$zone->name}' mesclada com '{$targetZone->name}' e excluída com sucesso.");

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao mesclar zonas: ' . $e->getMessage());
        }
    }

    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Zone::with('supervisions', 'pastor');

        // Pastor de Zona can only see zones they are responsible for
        if ($user->role === 'pastor_zona') {
            $query->where('pastor_id', $user->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%$search%")
                ->orWhereHas('pastor', fn($q) => $q->where('name', 'LIKE', "%$search%"));
        }

        $zones = $query->paginate(8)->withQueryString();
        return view('admin.zones.index', ['zones' => $zones]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Nenhuma zona selecionada.');
        }

        // Check for supervisions before deleting
        if (Zone::whereIn('id', $ids)->has('supervisions')->exists()) {
            return back()->with('error', 'Algumas zonas selecionadas possuem supervisões vinculadas.');
        }

        if (Zone::whereIn('id', $ids)->has('contributions')->exists()) {
            return back()->with('error', 'Algumas zonas selecionadas possuem contribuições financeiras vinculadas.');
        }

        if (Zone::whereIn('id', $ids)->has('quarterlyReports')->exists()) {
            return back()->with('error', 'Algumas zonas selecionadas possuem relatórios trimestrais vinculados.');
        }

        if (Zone::whereIn('id', $ids)->has('events')->exists()) {
            return back()->with('error', 'Algumas zonas selecionadas possuem eventos vinculados.');
        }

        Zone::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' zonas excluídas com sucesso.');
    }

    public function create(): View
    {
        // Encontra todos os usuários que são elegíveis para serem Pastores de Zona
        $pastors = User::whereIn('role', [
            'pastor_zona',
            'subpastor_zona',
            'pastor',
            'subpastor',
            'pastor_senior',
            'admin',
            'super_admin',
        ])->orderBy('name')->get();
        return view('admin.zones.create', ['pastors' => $pastors]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('zones', 'name')->where('congregation_id', app(CongregationContext::class)->id())],
            'description' => 'nullable|string',
            'pastor_id' => 'nullable|exists:users,id',
            'show_in_teaching_services' => 'nullable|boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'boundary' => ['nullable', 'json', function ($attribute, $value, $fail) {
                if (!$this->isValidBoundary($value)) {
                    $fail('Desenhe um limite de zona válido usando um polígono fechado.');
                }
            }],
        ]);

        $validated['boundary'] = filled($validated['boundary'] ?? null) ? json_decode($validated['boundary'], true) : null;

        Zone::create($validated);

        return redirect()->route('zones.index')
            ->with('success', 'Zona criada com sucesso!');
    }

    public function show(Zone $zone): View
    {
        $zone->load(['supervisions.cells.leader', 'pastor']);

        $cells = $zone->cells()->with(['leader', 'supervision'])->paginate(24)->withQueryString();
        $members = $zone->members()->paginate(24)->withQueryString();

        $availableZones = Zone::orderBy('name')->where('id', '!=', $zone->id)->get();

        return view('admin.zones.show', [
            'zone' => $zone,
            'cells' => $cells,
            'members' => $members,
            'availableZones' => $availableZones
        ]);
    }

    public function edit(Zone $zone): View
    {
        // Encontra todos os usuários que são elegíveis para serem Pastores de Zona
        $pastors = User::whereIn('role', [
            'pastor_zona',
            'subpastor_zona',
            'pastor',
            'subpastor',
            'pastor_senior',
            'admin',
            'super_admin',
        ])->orderBy('name')->get();
        return view('admin.zones.edit', ['zone' => $zone, 'pastors' => $pastors]);
    }

    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('zones', 'name')->where('congregation_id', app(CongregationContext::class)->id())->ignore($zone->id)],
            'description' => 'nullable|string',
            'pastor_id' => 'nullable|exists:users,id',
            'show_in_teaching_services' => 'nullable|boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'boundary' => ['nullable', 'json', function ($attribute, $value, $fail) {
                if (!$this->isValidBoundary($value)) {
                    $fail('Desenhe um limite de zona válido usando um polígono fechado.');
                }
            }],
        ]);

        $validated['boundary'] = filled($validated['boundary'] ?? null) ? json_decode($validated['boundary'], true) : null;

        $zone->update($validated);

        return redirect()->route('zones.index')
            ->with('success', 'Zona atualizada com sucesso!');
    }

    private function isValidBoundary(string $value): bool
    {
        $geometry = json_decode($value, true);
        if (!is_array($geometry) || !in_array($geometry['type'] ?? null, ['Polygon', 'MultiPolygon'], true)) {
            return false;
        }

        $coordinates = $geometry['coordinates'] ?? null;
        if (!is_array($coordinates) || $coordinates === []) {
            return false;
        }

        $firstRing = $geometry['type'] === 'Polygon'
            ? ($coordinates[0] ?? [])
            : ($coordinates[0][0] ?? []);

        return is_array($firstRing) && count($firstRing) >= 4 && $firstRing[0] === end($firstRing);
    }

    public function destroy(Zone $zone)
    {
        if ($zone->supervisions()->exists()) {
            return back()->with('error', 'Não pode excluir: Esta zona possui supervisões vinculadas.');
        }

        if ($zone->contributions()->exists()) {
            return back()->with('error', 'Não pode excluir: Existem contribuições financeiras registradas nesta zona.');
        }

        if ($zone->quarterlyReports()->exists()) {
            return back()->with('error', 'Não pode excluir: Existem relatórios trimestrais vinculados a esta zona.');
        }

        if ($zone->events()->exists()) {
            return back()->with('error', 'Não pode excluir: Existem eventos agendados nesta zona.');
        }

        $zone->delete();

        return redirect()->route('zones.index')
            ->with('success', 'Zona deletada com sucesso!');
    }
}
