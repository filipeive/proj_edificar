<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryItemController extends Controller
{
    public function index()
    {
        $items = InventoryItem::latest()->get();
        return view('inventory.index', compact('items'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'condition' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'purchased_at' => 'nullable|date',
            'value' => 'nullable|numeric|min:0',
        ]);

        InventoryItem::create($validated);

        return redirect()->route('inventory-items.index')
            ->with('success', 'Item adicionado ao inventário com sucesso!');
    }

    public function show(InventoryItem $inventoryItem)
    {
        return view('inventory.show', compact('inventoryItem'));
    }

    public function edit(InventoryItem $inventoryItem)
    {
        return view('inventory.edit', compact('inventoryItem'));
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'condition' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'purchased_at' => 'nullable|date',
            'value' => 'nullable|numeric|min:0',
        ]);

        $inventoryItem->update($validated);

        return redirect()->route('inventory-items.index')
            ->with('success', 'Item atualizado com sucesso!');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();
        return redirect()->route('inventory-items.index')
            ->with('success', 'Item removido do inventário.');
    }
}
