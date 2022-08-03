<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreBetRequest;
use App\Http\Requests\UpdateBetRequest;
use App\Http\Resources\BetResource;
use App\Models\Bet;
use App\Repositories\BetRepository;
use App\Services\BettorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BetController extends Controller
{
    private $betRepository;
    private $bettorService;

    public function __construct(
        BetRepository $betRepository,
        BettorService $bettorService
    ) {
        $this->betRepository = $betRepository;
        $this->bettorService = $bettorService;
    }

    public function index(Request $request)
    {
        $bets = $this->betRepository->index($request->all());

        return BetResource::collection($bets);
    }

    public function store(StoreBetRequest $request)
    {
        try {
            $bet = $this->betRepository->store($request->all());

            return new BetResource($bet);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the bet.' . $th->getMessage()]);
        }
    }

    public function show(Bet $bet)
    {
        $this->authorize('view', $bet);
        $bet = Bet::withCount('bettors')->find($bet->id);

        return new BetResource($bet);
    }

    public function update(UpdateBetRequest $request, Bet $bet)
    {
        // see the authorization in the UpdateBetRequest
        try {
            $bet = $this->betRepository->update($bet, $request->all());

            return new BetResource($bet);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to update the bet.' . $th->getMessage()]);
        }
    }

    public function destroy(Bet $bet)
    {
        $this->authorize('delete', $bet);
        try {
            $bet = $this->betRepository->destroy($bet);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the bet.' . $th->getMessage()]);
        }
    }

    public function activate(Bet $bet)
    {
        $this->authorize('activate', $bet);

        if ($bet->status !== 'DRAFT') {
            return response()->json(['error' => trans('messages.bet_already_activated')], 422);
        }
        DB::beginTransaction();
        try {
            $this->betRepository->modifyStatus($bet, 'INPROGRESS');
            $this->bettorService->sendInvitationBettors($bet);
            DB::commit();

            return response()->json(['success' => trans('messages.bet_activated_successfully')], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['error' => trans('messages.bet_not_activated', ['msg_error' => $th->getMessage()])], 422);
        }
    }
}
