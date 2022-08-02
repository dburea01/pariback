<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBettorRequest;
use App\Http\Resources\BettorResource;
use App\Models\Bet;
use App\Models\Bettor;
use App\Models\User;
use App\Repositories\BettorRepository;
use App\Repositories\UserRepository;

class BettorController extends Controller
{
    private $bettorRepository;

    private $userRepository;

    public function __construct(
        BettorRepository $bettorRepository,
        UserRepository $userRepository
    ) {
        $this->bettorRepository = $bettorRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Bet $bet)
    {
        $bettors = $this->bettorRepository->getBettorsOfBet($bet);

        return BettorResource::collection($bettors);
    }

    public function store(Bet $bet, StoreBettorRequest $request)
    {
        // see the form validation for the authorizations
        $user = User::where('email', $request->email)->first();

        try {
            if (! $user) {
                $user = $this->userRepository->insert([
                    'name' => $request->name,
                    'email' => $request->email,
                    'status' => 'CREATED',
                ]);
            }

            $bettor = $this->bettorRepository->store([
                'bet_id' => $bet->id,
                'user_id' => $user->id,
            ]);

            return new BettorResource($bettor);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the bettor.'.$th->getMessage()]);
        }
    }

    public function destroy(Bet $bet, Bettor $bettor)
    {
        $this->authorize('delete', [Bettor::class, $bet]);
        try {
            $this->bettorRepository->destroy($bettor);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the bettor.'.$th->getMessage()]);
        }
    }
}
