<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\MinistryResource;
use App\Models\MinisterialEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MinistriesController extends BaseApiController
{
    /**
     * Display a listing of ministerial enrollments.
     */
    public function index(Request $request): JsonResponse
    {
        $query = MinisterialEnrollment::query()->with('course', 'zone');

        // Apply filters or search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('full_name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            MinistryResource::collection($enrollments),
            'Lista de inscrições ministeriais recuperada.',
            [
                'current_page' => $enrollments->currentPage(),
                'last_page' => $enrollments->lastPage(),
                'per_page' => $enrollments->perPage(),
                'total' => $enrollments->total(),
            ]
        );
    }

    /**
     * Store a newly created ministerial enrollment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'course_class_id' => 'nullable|exists:course_classes,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'is_church_member' => 'required|boolean',
            'zone_id' => 'nullable|exists:zones,id',
            'cell_name' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $enrollment = MinisterialEnrollment::create($validated);

        return $this->sendResponse(new MinistryResource($enrollment), 'Inscrição ministerial criada com sucesso.', [], 201);
    }

    /**
     * Display the specified ministerial enrollment.
     */
    public function show(MinisterialEnrollment $ministry): JsonResponse
    {
        $ministry->load('course', 'zone');
        return $this->sendResponse(new MinistryResource($ministry), 'Detalhes da inscrição ministerial carregados.');
    }

    /**
     * Update the specified ministerial enrollment.
     */
    public function update(Request $request, MinisterialEnrollment $ministry): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => 'sometimes|required|exists:courses,id',
            'course_class_id' => 'nullable|exists:course_classes,id',
            'full_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'is_church_member' => 'sometimes|required|boolean',
            'zone_id' => 'nullable|exists:zones,id',
            'cell_name' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'status' => 'sometimes|required|string',
        ]);

        $ministry->update($validated);

        return $this->sendResponse(new MinistryResource($ministry), 'Inscrição ministerial atualizada com sucesso.');
    }

    /**
     * Remove the specified ministerial enrollment.
     */
    public function destroy(MinisterialEnrollment $ministry): JsonResponse
    {
        $ministry->delete();

        return $this->sendResponse(null, 'Inscrição ministerial removida com sucesso.');
    }
}
