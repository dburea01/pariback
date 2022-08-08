<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserBetRequest;
use App\Http\Resources\BetResource;
use App\Http\Resources\ResultBetResource;
use App\Http\Resources\UserBetResource;
use App\Models\Bet;
use App\Models\User;
use App\Models\UserBet;
use App\Repositories\BettorRepository;
use App\Repositories\UserBetRepository;
use App\Services\BetService;
use App\Services\BettorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBetController extends Controller
{
    public $userBetRepository;

    public $bettorService;

    public $betService;

    public $bettorRepository;

    public function __construct(UserBetRepository $userBetRepository, BettorService $bettorService, BetService $betService, BettorRepository $bettorRepository)
    {
        $this->userBetRepository = $userBetRepository;
        $this->bettorService = $bettorService;
        $this->bettorRepository = $bettorRepository;
        $this->betService = $betService;
    }

    public function index(Bet $bet, Request $request)
    {
        $this->authorize('viewAny', [UserBet::class, $bet]);
        $userBets = $this->userBetRepository->index($bet, $request->all());

        return UserBetResource::collection($userBets);
    }

    public function store(Bet $bet, StoreUserBetRequest $request)
    {
        try {
            $userBet = $this->userBetRepository->store($bet, $request->all());

            return new UserBetResource($userBet);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the userBet. '.$th->getMessage()]);
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
            return response()->json(['error' => 'Impossible to delete the userBet. '.$th->getMessage()]);
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
            return response()->json(['error' => 'Impossible to create the userBet with this token. '.$th->getMessage()]);
        }
    }

    public function deleteUserBetWithToken(Bet $bet, string $token, UserBet $userBet, Request $request)
    {
        if (null !== $token && isset($request->user_id)) {
            Auth::login(User::find($request->user_id));
        }

        $this->authorize('delete', [UserBet::class, $bet]);
        try {
            $userBet = $this->userBetRepository->destroy($userBet);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the userBet with this token. '.$th->getMessage()]);
        }
    }

    public function getBetResultsDetailsWithToken(Bet $bet, string $token)
    {
        // TODO : authorizations
        $betResultsDetails = $this->betService->bettorsWithRank($bet);

        return ResultBetResource::collection($betResultsDetails);
    }
}
