<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSportRequest;
use App\Http\Requests\UpdateSportRequest;
use App\Http\Resources\SportResource;
use App\Models\Sport;
use App\Repositories\SportRepository;
use App\Services\ImageService;

class SportController extends Controller
{
    private $sportRepository;

    private $imageService;

    public function __construct(SportRepository $sportRepository, ImageService $imageService)
    {
        $this->sportRepository = $sportRepository;
        $this->imageService = $imageService;
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
            $imageName = $this->imageName($request->id, $request->icon);
            $this->imageService->uploadImage($imageName, $request->icon);
            $sport = $this->sportRepository->store($request->all(), $imageName);

            return new SportResource($sport);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the sport.'.$th->getMessage()]);
        }
    }

    public function update(UpdateSportRequest $request, Sport $sport)
    {
        try {
            if ($request->has('icon')) {
                $imageName = $this->imageName($sport->id, $request->icon);
                $this->imageService->uploadImage($imageName, $request->icon);
            } else {
                $imageName = '';
            }
            $sportUpdated = $this->sportRepository->update($sport, $request->all(), $imageName);

            return new SportResource($sportUpdated);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to update the sport.'.$th->getMessage()]);
        }
    }

    public function destroy(Sport $sport)
    {
        $this->authorize('delete', $sport);

        try {
            $this->imageService->deleteImage($sport->icon);
            $this->sportRepository->destroy($sport);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the sport.']);
        }
    }

    public function imageName(string $sportId, $image)
    {
        return 'sport_'.$sportId.'.'.$image->getClientOriginalExtension();
    }
}
