<?php
namespace App\Http\Controllers;

use App\Models\Bettor;
use App\Http\Requests\StoreBettorRequest;
use App\Http\Requests\UpdateBettorRequest;
use App\Http\Resources\BettorResource;
use App\Models\Bet;
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
        $bettors = $this->bettorRepository->index($bet);

        return BettorResource::collection($bettors);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBettorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Bet $bet, StoreBettorRequest $request)
    {
        // $this->authorize('create', [Bettor::class, $bet]);
        $user = User::where('email', $request->email)->first();

        try {
            if (!$user) {
                $user = $this->userRepository->insert([
                    'name' => $request->name,
                    'email' => $request->email,
                    'status' => 'CREATED'
                ]);
            }

            $bettor = $this->bettorRepository->store([
                'bet_id' => $bet->id,
                'user_id' => $user->id,
            ]);

            return new BettorResource($bettor);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the bettor.' . $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bettor  $bettor
     * @return \Illuminate\Http\Response
     */
    public function show(Bettor $bettor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bettor  $bettor
     * @return \Illuminate\Http\Response
     */
    public function edit(Bettor $bettor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBettorRequest  $request
     * @param  \App\Models\Bettor  $bettor
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBettorRequest $request, Bettor $bettor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bettor  $bettor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bettor $bettor)
    {
        //
    }
}
