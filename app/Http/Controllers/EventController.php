<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\StorePhaseRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\PhaseResource;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Phase;
use App\Repositories\EventRepository;

class EventController extends Controller
{
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function index(Phase $phase)
    {
        $events = $this->eventRepository->index($phase);

        return EventResource::collection($events);
    }

    public function store(Phase $phase, StoreEventRequest $request)
    {
        try {
            $event = $this->eventRepository->store($phase, $request->all());

            return new EventResource($event);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the event.' . $th->getMessage()]);
        }
    }

    public function show(Phase $phase, Event $event)
    {
        return new EventResource($event);
    }

    public function update(Phase $phase, Event $event, UpdateEventRequest $request)
    {
        try {
            $event = $this->eventRepository->update($event, $request->only(['score_team1', 'score_team2', 'location', 'date', 'status']));

            return new EventResource($event);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to update the event.' . $th->getMessage()]);
        }
    }

    public function destroy(Phase $phase, Event $event)
    {
        try {
            $this->eventRepository->destroy($event);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the event.' . $th->getMessage()]);
        }
    }
}
