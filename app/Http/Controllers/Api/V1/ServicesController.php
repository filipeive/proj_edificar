<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServicesController extends BaseApiController
{
    /**
     * Display a listing of services.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Service::query()->with(['preacher', 'zoneParticipations.zone']);

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('theme', 'LIKE', '%' . $search . '%')
                    ->orWhere('preacher_name', 'LIKE', '%' . $search . '%');
            });
        }

        $services = $query->orderBy('date', 'desc')->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            ServiceResource::collection($services),
            'Lista de cultos recuperada.',
            [
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'per_page' => $services->perPage(),
                'total' => $services->total(),
            ]
        );
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'service_type' => 'required|string',
            'preacher_id' => 'nullable|exists:users,id',
            'preacher_name' => 'required|string|max:255',
            'theme' => 'required|string|max:255',
            'message' => 'nullable|string',
            'observations' => 'nullable|string',
            'adults_members' => 'required|integer|min:0',
            'adults_visitors' => 'required|integer|min:0',
            'adults_salvations' => 'required|integer|min:0',
            'children_members' => 'required|integer|min:0',
            'children_visitors' => 'required|integer|min:0',
            'children_salvations' => 'required|integer|min:0',
            'special_offerings_total' => 'nullable|numeric|min:0',
        ]);

        $service = Service::create($validated);
        $service->load(['preacher', 'zoneParticipations.zone']);
        return $this->sendResponse(new ServiceResource($service), 'Culto cadastrado com sucesso.', [], 201);
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): JsonResponse
    {
        $service->load(['preacher', 'zoneParticipations.zone']);
        return $this->sendResponse(new ServiceResource($service), 'Dados do culto carregados.');
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, Service $service): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'sometimes|required|date',
            'service_type' => 'sometimes|required|string',
            'preacher_id' => 'nullable|exists:users,id',
            'preacher_name' => 'sometimes|required|string|max:255',
            'theme' => 'sometimes|required|string|max:255',
            'message' => 'nullable|string',
            'observations' => 'nullable|string',
            'adults_members' => 'sometimes|required|integer|min:0',
            'adults_visitors' => 'sometimes|required|integer|min:0',
            'adults_salvations' => 'sometimes|required|integer|min:0',
            'children_members' => 'sometimes|required|integer|min:0',
            'children_visitors' => 'sometimes|required|integer|min:0',
            'children_salvations' => 'sometimes|required|integer|min:0',
            'special_offerings_total' => 'nullable|numeric|min:0',
        ]);

        $service->update($validated);
        $service->load(['preacher', 'zoneParticipations.zone']);
        return $this->sendResponse(new ServiceResource($service), 'Dados do culto atualizados com sucesso.');
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return $this->sendResponse(null, 'Culto removido com sucesso.');
    }
}
