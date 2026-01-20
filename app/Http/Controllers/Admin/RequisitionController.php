<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequisitionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Requisition::with(['user', 'approver'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if (!auth()->user()->isAdmin() && !auth()->user()->isTesouraria()) {
            $query->where('user_id', auth()->id());
        }

        $requisitions = $query->paginate(15);

        $stats = [
            'pending' => Requisition::pending()->count(),
            'approved' => Requisition::approved()->count(),
        ];

        return view('admin.requisitions.index', compact('requisitions', 'stats'));
    }

    public function create(): View
    {
        return view('admin.requisitions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:500',
            'category' => 'required|string',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')->store('requisitions', 'public');
        }

        Requisition::create([
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'proof_file' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('requisitions.index')
            ->with('success', 'Requisição criada com sucesso! Aguarde a aprovação.');
    }

    public function approve(Requisition $requisition)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isTesouraria()) {
            abort(403);
        }

        $requisition->update([
            'status' => Requisition::STATUS_APPROVED,
            'approver_id' => auth()->id(),
        ]);

        // Criar despesa automaticamente
        Expense::create([
            'requisition_id' => $requisition->id,
            'description' => $requisition->description,
            'amount' => $requisition->amount,
            'date' => now(),
            'category' => $requisition->category,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Requisição aprovada e despesa registada!');
    }

    public function reject(Request $request, Requisition $requisition)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isTesouraria()) {
            abort(403);
        }

        $request->validate(['rejection_reason' => 'required|string']);

        $requisition->update([
            'status' => Requisition::STATUS_REJECTED,
            'approver_id' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('info', 'Requisição rejeitada.');
    }
}
