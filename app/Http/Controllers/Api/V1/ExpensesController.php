<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpensesController extends BaseApiController
{
    /**
     * Display a listing of expenses.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Expense::query()->with('requisition', 'user');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }

        $expenses = $query->orderBy('date', 'desc')->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            ExpenseResource::collection($expenses),
            'Lista de despesas recuperada.',
            [
                'current_page' => $expenses->currentPage(),
                'last_page' => $expenses->lastPage(),
                'per_page' => $expenses->perPage(),
                'total' => $expenses->total(),
            ]
        );
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'requisition_id' => 'nullable|exists:requisitions,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category' => 'required|string|max:255',
            'scope' => 'required|in:comissao_obra,regular',
        ]);

        $validated['user_id'] = $request->user()->id;

        $expense = Expense::create($validated);

        return $this->sendResponse(new ExpenseResource($expense), 'Despesa lançada com sucesso.', [], 201);
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense): JsonResponse
    {
        $expense->load('requisition', 'user');
        return $this->sendResponse(new ExpenseResource($expense), 'Dados da despesa carregados.');
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, Expense $expense): JsonResponse
    {
        $validated = $request->validate([
            'requisition_id' => 'nullable|exists:requisitions,id',
            'description' => 'sometimes|required|string',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'date' => 'sometimes|required|date',
            'category' => 'sometimes|required|string|max:255',
            'scope' => 'sometimes|required|in:comissao_obra,regular',
        ]);

        $expense->update($validated);

        return $this->sendResponse(new ExpenseResource($expense), 'Despesa atualizada com sucesso.');
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Expense $expense): JsonResponse
    {
        $expense->delete();

        return $this->sendResponse(null, 'Despesa removida com sucesso.');
    }
}
