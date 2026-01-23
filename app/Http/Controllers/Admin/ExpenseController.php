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

    public function create(): View
    {
        return view('admin.expenses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category' => 'required|string',
            'scope' => 'required|in:eclesiastico,edificar',
        ]);

        Expense::create([
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'category' => $validated['category'],
            'scope' => $validated['scope'],
        ]);

        return redirect()->route('expenses.index')->with('success', 'Despesa registada com sucesso!');
    }

    public function edit(Expense $expense): View
    {
        return view('admin.expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category' => 'required|string',
            'scope' => 'required|in:eclesiastico,edificar',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Despesa atualizada com sucesso!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Despesa removida com sucesso!');
    }
}
