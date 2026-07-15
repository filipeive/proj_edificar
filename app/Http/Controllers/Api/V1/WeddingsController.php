<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\WeddingResource;
use App\Models\Wedding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeddingsController extends BaseApiController
{
    /**
     * Display a listing of weddings.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Wedding::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('groom_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('bride_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('location', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $weddings = $query->orderBy('date', 'desc')->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            WeddingResource::collection($weddings),
            'Lista de casamentos recuperada.',
            [
                'current_page' => $weddings->currentPage(),
                'last_page' => $weddings->lastPage(),
                'per_page' => $weddings->perPage(),
                'total' => $weddings->total(),
            ]
        );
    }

    /**
     * Store a newly created wedding.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'location' => 'required|string|max:255',
            'godparents' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,cancelled',
            'observations' => 'nullable|string',
        ]);

        $wedding = Wedding::create($validated);

        return $this->sendResponse(new WeddingResource($wedding), 'Casamento criado com sucesso.', [], 201);
    }

    /**
     * Display the specified wedding.
     */
    public function show(Wedding $wedding): JsonResponse
    {
        return $this->sendResponse(new WeddingResource($wedding), 'Detalhes do casamento carregados.');
    }

    /**
     * Update the specified wedding.
     */
    public function update(Request $request, Wedding $wedding): JsonResponse
    {
        $validated = $request->validate([
            'groom_name' => 'sometimes|required|string|max:255',
            'bride_name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'time' => 'sometimes|required',
            'location' => 'sometimes|required|string|max:255',
            'godparents' => 'nullable|string',
            'status' => 'sometimes|required|in:scheduled,completed,cancelled',
            'observations' => 'nullable|string',
        ]);

        $wedding->update($validated);

        return $this->sendResponse(new WeddingResource($wedding), 'Casamento atualizado com sucesso.');
    }

    /**
     * Remove the specified wedding.
     */
    public function destroy(Wedding $wedding): JsonResponse
    {
        $wedding->delete();

        return $this->sendResponse(null, 'Casamento excluído com sucesso.');
    }
}
