<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Events\EnrollMemberAction;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventsController extends BaseApiController
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user();
        $query = Event::query()->with('eventType', 'zone', 'cell');

        // Apply Scoping Based on Role
        if ($currentUser->isPastorZona()) {
            $query->whereIn('zone_id', $currentUser->getManagedZoneIds());
        } elseif ($currentUser->isSupervisor()) {
            $query->whereHas('cell', function ($q) use ($currentUser) {
                $q->whereIn('supervision_id', $currentUser->getManagedSupervisionIds());
            });
        }

        // Apply search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', '%' . $searchTerm . '%');
        }

        $events = $query->orderBy('date', 'desc')->paginate($request->input('per_page', 15));

        return $this->sendResponse(
            EventResource::collection($events),
            'Lista de eventos recuperada.',
            [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ]
        );
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,id',
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'end_date' => 'nullable|date',
            'zone_id' => 'nullable|exists:zones,id',
            'cell_id' => 'nullable|exists:cells,id',
            'description' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $event = Event::create($validated);

        return $this->sendResponse(new EventResource($event), 'Evento criado com sucesso.', [], 201);
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event): JsonResponse
    {
        $event->load('eventType', 'zone', 'cell');
        return $this->sendResponse(new EventResource($event), 'Dados do evento carregados.');
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'event_type_id' => 'sometimes|required|exists:event_types,id',
            'name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'end_date' => 'nullable|date',
            'zone_id' => 'nullable|exists:zones,id',
            'cell_id' => 'nullable|exists:cells,id',
            'description' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $event->update($validated);

        return $this->sendResponse(new EventResource($event), 'Evento atualizado com sucesso.');
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event): JsonResponse
    {
        $event->delete();

        return $this->sendResponse(null, 'Evento removido com sucesso.');
    }

    /**
     * Enroll a member in a course.
     */
    public function enroll(Request $request, Course $course, EnrollMemberAction $action): JsonResponse
    {
        try {
            $enrollment = $action->execute($request->user(), $course);
            return $this->sendResponse($enrollment, 'Matrícula no curso realizada com sucesso.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 400);
        }
    }
}
