<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreParticipationRequest;
use App\Http\Resources\ParticipationResource;
use App\Models\Competition;
use App\Models\Participation;
use App\Models\Team;
use App\Repositories\ParticipationRepository;
use App\Repositories\TeamRepository;
use Illuminate\Http\Request;

class ParticipationController extends Controller
{
    private $participationRepository;

    private $teamRepository;

    public function __construct(
        ParticipationRepository $participationRepository,
        TeamRepository $teamRepository
    ) {
        $this->participationRepository = $participationRepository;
        $this->teamRepository = $teamRepository;
    }

    public function index(Request $request)
    {
        $participations = $this->participationRepository->index($request->all());

        return ParticipationResource::collection($participations);
    }

    public function store(StoreParticipationRequest $request)
    {
        try {
            $participation = $this->participationRepository->store($request->all());

            return new ParticipationResource($participation);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the participation.' . $th->getMessage()]);
        }
    }

    public function destroy(Participation $participation)
    {
        try {
            $this->participationRepository->destroy($participation);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the participation.' . $th->getMessage()]);
        }
    }
}
