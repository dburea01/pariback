<?php
namespace App\Http\Controllers;

use App\Models\EventBetting;
use App\Http\Requests\StoreEventBettingRequest;
use App\Http\Requests\UpdateEventBettingRequest;
use App\Http\Resources\EventBettingResource;
use App\Models\Bet;
use App\Models\Bettor;
use App\Repositories\BettorRepository;
use App\Repositories\EventBettingRepository;
use App\Services\BettorService;

class EventBettingController extends Controller
{
    public $eventBettingRepository;

    public $bettorService;

    public $bettorRepository;

    public function __construct(EventBettingRepository $eventBettingRepository, BettorService $bettorService, BettorRepository $bettorRepository)
    {
        $this->eventBettingRepository = $eventBettingRepository;
        $this->bettorService = $bettorService;
        $this->bettorRepository = $bettorRepository;
    }

    public function index(Bet $bet, Bettor $bettor)
    {
        $eventBettingsOfTheBettor = $this->eventBettingRepository->index($bet, $bettor);

        return EventBettingResource::collection($eventBettingsOfTheBettor);
    }

    public function store(Bet $bet, Bettor $bettor, StoreEventBettingRequest $request)
    {
        try {
            $eventBetting = $this->eventBettingRepository->store($bettor, $request->all());

            return new EventBettingResource($eventBetting);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the eventBetting. ' . $th->getMessage()]);
        }
    }

    public function show(Bet $bet, Bettor $bettor, EventBetting $eventBetting)
    {
        return new EventBettingResource($eventBetting);
    }

    public function destroy(Bet $bet, Bettor $bettor, EventBetting $eventBetting)
    {
        try {
            $this->eventBettingRepository->destroy($eventBetting);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the eventBetting. ' . $th->getMessage()]);
        }
    }
}
