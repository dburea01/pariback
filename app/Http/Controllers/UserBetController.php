<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreEventBettingRequest;
use App\Http\Requests\StoreUserBetRequest;
use App\Http\Resources\BetResource;
use App\Http\Resources\UserBetResource;
use App\Models\Bet;
use App\Models\User;
use App\Models\UserBet;
use App\Repositories\BettorRepository;
use App\Repositories\UserBetRepository;
use App\Services\BettorService;
use Illuminate\Http\Request;

class UserBetController extends Controller
{
    public $userBetRepository;

    public $bettorService;

    public $bettorRepository;

    public function __construct(UserBetRepository $userBetRepository, BettorService $bettorService, BettorRepository $bettorRepository)
    {
        $this->userBetRepository = $userBetRepository;
        $this->bettorService = $bettorService;
        $this->bettorRepository = $bettorRepository;
    }

    public function index(Bet $bet, Request $request)
    {
        $this->authorize('viewAny', [UserBet::class, $bet]);
        $userBets = $this->userBetRepository->index($bet, $request->all());

        return UserBetResource::collection($userBets);
    }

    public function store(Bet $bet, StoreUserBetRequest $request)
    {
        // $this->authorize('create', [UserBet::class, $bet]);
        try {
            $userBet = $this->userBetRepository->store($bet, $request->all());

            return new UserBetResource($userBet);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the userBet. ' . $th->getMessage()]);
        }
    }

    public function show(Bet $bet, UserBet $userBet)
    {
        $this->authorize('view', [UserBet::class, $bet]);

        return new UserBetResource($userBet);
    }

    public function destroy(Bet $bet, UserBet $userBet)
    {
        $this->authorize('delete', [UserBet::class, $bet]);
        try {
            $this->userBetRepository->destroy($userBet);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the userBet. ' . $th->getMessage()]);
        }
    }

    public function getBetDetailsWithToken(Bet $bet, string $token)
    {
        return new BetResource($bet);
    }

    public function postUserBetWithToken(Bet $bet, string $token, StoreUserBetRequest $request)
    {
        try {
            $userBet = $this->userBetRepository->store($bet, $request->all());

            return new UserBetResource($userBet);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the userBet with this token. ' . $th->getMessage()]);
        }
    }
}
