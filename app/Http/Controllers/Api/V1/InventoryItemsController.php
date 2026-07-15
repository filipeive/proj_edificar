<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\InventoryItemResource;
use App\Models\InventoryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryItemsController extends BaseApiController
{
    /**
     * Display a listing of inventory items.
     */
    public function index(Request $request): JsonResponse
    {
        $query = InventoryItem::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('location', 'LIKE', '%' . $search . '%');
            });
        }

        $items = $query->orderBy('name', 'asc')->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            InventoryItemResource::collection($items),
            'Lista de bens patrimoniais recuperada.',
            [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ]
        );
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:novo,bom,regular,ruim',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchased_at' => 'nullable|date',
            'value' => 'nullable|numeric|min:0',
        ]);

        $item = InventoryItem::create($validated);

        return $this->sendResponse(new InventoryItemResource($item), 'Bem patrimonial cadastrado com sucesso.', [], 201);
    }

    /**
     * Display the specified inventory item.
     */
    public function show(InventoryItem $inventoryItem): JsonResponse
    {
        return $this->sendResponse(new InventoryItemResource($inventoryItem), 'Dados do patrimônio carregados.');
    }

    /**
     * Update the specified inventory item.
     */
    public function update(Request $request, InventoryItem $inventoryItem): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:255',
            'quantity' => 'sometimes|required|integer|min:1',
            'condition' => 'sometimes|required|in:novo,bom,regular,ruim',
            'location' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'purchased_at' => 'nullable|date',
            'value' => 'nullable|numeric|min:0',
        ]);

        $inventoryItem->update($validated);

        return $this->sendResponse(new InventoryItemResource($inventoryItem), 'Dados do patrimônio atualizados com sucesso.');
    }

    /**
     * Remove the specified inventory item.
     */
    public function destroy(InventoryItem $inventoryItem): JsonResponse
    {
        $inventoryItem->delete();

        return $this->sendResponse(null, 'Patrimônio removido com sucesso.');
    }
}
