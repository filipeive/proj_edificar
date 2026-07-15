<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\PackageResource;
use App\Models\CommitmentPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackagesController extends BaseApiController
{
    /**
     * Display a listing of packages.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CommitmentPackage::query()->with('responsible');

        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $packages = $query->orderBy('order', 'asc')->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            PackageResource::collection($packages),
            'Lista de pacotes de compromissos recuperada.',
            [
                'current_page' => $packages->currentPage(),
                'last_page' => $packages->lastPage(),
                'per_page' => $packages->perPage(),
                'total' => $packages->total(),
            ]
        );
    }

    /**
     * Store a newly created package.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'whatsapp_link' => 'nullable|url',
            'sms_template' => 'nullable|string',
            'whatsapp_template' => 'nullable|string',
            'is_active' => 'required|boolean',
            'order' => 'required|integer',
            'responsible_id' => 'required|exists:users,id',
        ]);

        $package = CommitmentPackage::create($validated);

        return $this->sendResponse(new PackageResource($package), 'Pacote de compromisso criado com sucesso.', [], 201);
    }

    /**
     * Display the specified package.
     */
    public function show(CommitmentPackage $package): JsonResponse
    {
        $package->load('responsible');
        return $this->sendResponse(new PackageResource($package), 'Dados do pacote carregados.');
    }

    /**
     * Update the specified package.
     */
    public function update(Request $request, CommitmentPackage $package): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'min_amount' => 'sometimes|required|numeric|min:0',
            'max_amount' => 'sometimes|required|numeric|min:0',
            'description' => 'nullable|string',
            'whatsapp_link' => 'nullable|url',
            'sms_template' => 'nullable|string',
            'whatsapp_template' => 'nullable|string',
            'is_active' => 'sometimes|required|boolean',
            'order' => 'sometimes|required|integer',
            'responsible_id' => 'sometimes|required|exists:users,id',
        ]);

        $package->update($validated);

        return $this->sendResponse(new PackageResource($package), 'Pacote atualizado com sucesso.');
    }

    /**
     * Remove the specified package.
     */
    public function destroy(CommitmentPackage $package): JsonResponse
    {
        $package->delete();

        return $this->sendResponse(null, 'Pacote de compromisso excluído com sucesso.');
    }
}
