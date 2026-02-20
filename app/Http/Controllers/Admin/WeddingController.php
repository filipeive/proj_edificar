<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use Illuminate\Http\Request;

class WeddingController extends Controller
{
    public function feed(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $weddings = Wedding::whereBetween('date', [$start, $end])->get();

        $events = $weddings->map(function ($wedding) {
            $color = '#f97316'; // orange-500 (scheduled)
            if ($wedding->status === 'completed') {
                $color = '#22c55e'; // green-500
            } elseif ($wedding->status === 'cancelled') {
                $color = '#ef4444'; // red-500
            }

            return [
                'id' => $wedding->id,
                'title' => explode(' ', $wedding->groom_name)[0] . ' & ' . explode(' ', $wedding->bride_name)[0],
                'start' => $wedding->date->format('Y-m-d') . ($wedding->time ? 'T' . $wedding->time->format('H:i:s') : ''),
                'url' => route('weddings.edit', $wedding),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'status' => $wedding->status,
                    'location' => $wedding->location,
                    'time' => $wedding->time ? $wedding->time->format('H:i') : null,
                    'godparents' => $wedding->godparents,
                    'groom_name' => $wedding->groom_name,
                    'bride_name' => $wedding->bride_name,
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $date = $request->has('date')
            ? \Carbon\Carbon::parse($request->date)
            : now();

        $year = $date->year;

        // Start Query for Weddings
        $query = Wedding::query();

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('groom_name', 'like', "%{$search}%")
                    ->orWhere('bride_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fetch Weddings for List/Grid Views (Paginated)
        $weddings = $query->orderBy('date', 'desc')->paginate(12);

        // Sidebar Data
        $upcoming = Wedding::where('date', '>=', now())
            ->where('status', '!=', 'completed')
            ->orderBy('date')
            ->limit(5)
            ->get();

        $completedCount = Wedding::where('status', 'completed')->whereYear('date', $year)->count();
        $upcomingCount = Wedding::where('status', '!=', 'completed')->where('date', '>=', now())->count();
        $cancelledCount = Wedding::where('status', 'cancelled')->whereYear('date', $year)->count();
        $totalCount = Wedding::whereYear('date', $year)->count();

        return view('admin.weddings.index', compact(
            'weddings',
            'date',
            'upcoming',
            'completedCount',
            'upcomingCount',
            'cancelledCount',
            'totalCount'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $year = $request->input('year', now()->year);
        $weddings = Wedding::whereYear('date', $year)->orderBy('date')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.weddings.pdf', compact('weddings', 'year'));
        return $pdf->download('Calendario_Casamentos_' . $year . '.pdf');
    }

    public function create()
    {
        return view('admin.weddings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'nullable',
            'location' => 'nullable|string|max:255',
            'godparents' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        Wedding::create($validated);

        return redirect()->route('weddings.index')->with('success', 'Casamento agendado com sucesso!');
    }

    public function show(Wedding $wedding)
    {
        return view('admin.weddings.show', compact('wedding'));
    }

    public function edit(Wedding $wedding)
    {
        return view('admin.weddings.edit', compact('wedding'));
    }

    public function update(Request $request, Wedding $wedding)
    {
        $validated = $request->validate([
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'nullable',
            'location' => 'nullable|string|max:255',
            'godparents' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,cancelled',
            'observations' => 'nullable|string',
        ]);

        $wedding->update($validated);

        return redirect()->route('weddings.index')->with('success', 'Casamento atualizado com sucesso!');
    }

    public function destroy(Wedding $wedding)
    {
        $wedding->delete();
        return redirect()->route('weddings.index')->with('success', 'Casamento removido com sucesso!');
    }

    public function testEmail()
    {
        try {
            \Illuminate\Support\Facades\Mail::raw('Este é um email de teste do sistema Life Church.', function ($message) {
                $message->to(auth()->user()->email)
                    ->subject('Teste de Envio de Email');
            });
            return back()->with('success', 'Email de teste enviado para ' . auth()->user()->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao enviar email: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete weddings
     */
    public function bulkDestroy(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Apenas administradores podem realizar esta ação.');
        }

        $validated = $request->validate([
            'wedding_ids' => 'required|array',
            'wedding_ids.*' => 'exists:weddings,id'
        ]);

        $deletedCount = Wedding::whereIn('id', $validated['wedding_ids'])->delete();

        return redirect()->route('weddings.index')
            ->with('success', "{$deletedCount} casamento(s) removido(s) com sucesso!");
    }
}
