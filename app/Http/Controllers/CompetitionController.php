<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompetitionRequest;
use App\Http\Requests\UpdateCompetitionRequest;
use App\Http\Resources\CompetitionResource;
use App\Models\Competition;
use App\Repositories\CompetitionRepository;
use App\Services\ImageService;

class CompetitionController extends Controller
{
    private $competitionRepository;

    private $imageService;

    public function __construct(CompetitionRepository $competitionRepository, ImageService $imageService)
    {
        $this->competitionRepository = $competitionRepository;
        $this->imageService = $imageService;
    }

    public function index()
    {
        $competitions = $this->competitionRepository->index();

        return CompetitionResource::collection($competitions);
    }

    public function show(Competition $competition)
    {
        return new CompetitionResource($competition);
    }

    public function store(StoreCompetitionRequest $request)
    {
        try {
            $imageName = $this->imageName($request->short_name, $request->icon);
            $this->imageService->uploadImage($imageName, $request->icon);
            $competition = $this->competitionRepository->store($request->all(), $imageName);

            return new CompetitionResource($competition);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the competition.'.$th->getMessage()]);
        }
    }

    public function update(UpdateCompetitionRequest $request, Competition $competition)
    {
        try {
            if ($request->has('icon')) {
                $imageName = $this->imageName($competition->short_name, $request->icon);
                $this->imageService->deleteImage($competition->icon);
                $this->imageService->uploadImage($imageName, $request->icon);
            } else {
                $imageName = $competition->icon;
            }

            $competitionUpdated = $this->competitionRepository->update($competition, $request->all(), $imageName);

            return new CompetitionResource($competitionUpdated);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to update the competition.'.$th->getMessage()]);
        }
    }

    public function destroy(Competition $competition)
    {
        $this->authorize('delete', $competition);

        try {
            $this->imageService->deleteImage($competition->icon);
            $this->competitionRepository->destroy($competition);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the competition.'.$th->getMessage()]);
        }
    }

    public function imageName(string $competition, $image)
    {
        return 'competition_'.$competition.'.'.$image->getClientOriginalExtension();
    }
}
