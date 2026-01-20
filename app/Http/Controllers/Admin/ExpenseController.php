<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::with(['user', 'requisition.user'])->latest()->paginate(20);
        $totalExpenses = Expense::sum('amount');
        return view('admin.expenses.index', compact('expenses', 'totalExpenses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category' => 'required|string',
        ]);

        Expense::create([
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'category' => $validated['category'],
        ]);

        return back()->with('success', 'Despesa registada com sucesso!');
    }
}
