<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhaseRequest;
use App\Http\Resources\PhaseResource;
use App\Models\Competition;
use App\Models\Phase;
use App\Repositories\PhaseRepository;

class PhaseController extends Controller
{
    private $phaseRepository;

    public function __construct(PhaseRepository $phaseRepository)
    {
        $this->phaseRepository = $phaseRepository;
    }

    public function index(Competition $competition)
    {
        $phases = $this->phaseRepository->index($competition->id);

        return PhaseResource::collection($phases);
    }

    public function store(Competition $competition, StorePhaseRequest $request)
    {
        try {
            $phase = $this->phaseRepository->store($competition, $request->all());

            return new PhaseResource($phase);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the phase.'.$th->getMessage()]);
        }
    }

    public function show(Competition $competition, Phase $phase)
    {
        return new PhaseResource($phase);
    }

    public function update(Competition $competition, Phase $phase, StorePhaseRequest $request)
    {
        try {
            $phase = $this->phaseRepository->update($phase, $request->all());

            return new PhaseResource($phase);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to update the phase.'.$th->getMessage()]);
        }
    }

    public function destroy(Competition $competition, Phase $phase)
    {
        try {
            $phase = $this->phaseRepository->destroy($phase);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the phase.'.$th->getMessage()]);
        }
    }
}
