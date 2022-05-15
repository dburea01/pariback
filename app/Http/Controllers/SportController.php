<?php
namespace App\Http\Controllers;

use App\Models\Sport;
use App\Http\Requests\StoreSportRequest;
use App\Http\Requests\UpdateSportRequest;
use App\Http\Resources\SportResource;
use App\Repositories\SportRepository;

class SportController extends Controller
{
    private $sportRepository;

    public function __construct(SportRepository $sportRepository)
    {
        $this->sportRepository = $sportRepository;
    }

    public function index()
    {
        $sports = $this->sportRepository->index();

        return SportResource::collection($sports);
    }

    public function show(Sport $sport)
    {
        return new SportResource($sport);
    }

    public function store(StoreSportRequest $request)
    {
        try {
            $sport = $this->sportRepository->store($request->all());
            return new SportResource($sport);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the sport.']);
        }
    }

    public function update(UpdateSportRequest $request, Sport $sport)
    {
        try {
            $sportUpdated = $this->sportRepository->update($sport, $request->all());
            return new SportResource($sportUpdated);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to update the sport.']);
        }
    }

    public function destroy(Sport $sport)
    {
        $this->authorize('delete', $sport);

        try {
            $this->sportRepository->destroy($sport);
            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the sport.']);
        }
    }
}
