<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreBetRequest;
use App\Http\Requests\UpdateBetRequest;
use App\Http\Resources\BetResource;
use App\Models\Bet;
use App\Repositories\BetRepository;
use App\Repositories\CompetitionRepository;
use Illuminate\Http\Request;

class BetController extends Controller
{
    private $betRepository;

    private $competitionRepository;

    private $bettorService;

    public function __construct(
        BetRepository $betRepository,
        CompetitionRepository $competitionRepository,
        // BettorService $bettorService
    ) {
        $this->betRepository = $betRepository;
        $this->competitionRepository = $competitionRepository;
        // $this->bettorService = $bettorService;
    }

    public function index(Request $request)
    {
        // $this->authorize('viewAny', Bet::class);
        $bets = $this->betRepository->index($request->all());

        return BetResource::collection($bets);
    }

    public function store(StoreBetRequest $request)
    {
        // $this->authorize('create', Bet::class);
        try {
            $bet = $this->betRepository->store($request->all());

            return new BetResource($bet);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the bet.' . $th->getMessage()]);
        }
    }

    public function show(Bet $bet)
    {
        return new BetResource($bet);
    }

    public function update(UpdateBetRequest $request, Bet $bet)
    {
        // $this->authorize('update', $bet);
        try {
            $bet = $this->betRepository->update($bet, $request->all());

            return new BetResource($bet);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to update the bet.' . $th->getMessage()]);
        }
    }

    public function destroy(Bet $bet)
    {
        // $this->authorize('delete', $bet);
        try {
            $bet = $this->betRepository->destroy($bet);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the bet.' . $th->getMessage()]);
        }
    }
}
