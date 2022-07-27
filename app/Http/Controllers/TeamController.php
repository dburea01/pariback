<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Repositories\TeamRepository;
use App\Services\ImageService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    private $teamRepository;

    private $imageService;

    public function __construct(TeamRepository $teamRepository, ImageService $imageService)
    {
        $this->teamRepository = $teamRepository;
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $teams = $this->teamRepository->index($request->all());

        return TeamResource::collection($teams);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTeamRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTeamRequest $request)
    {
        try {
            $imageName = $this->imageName($request->short_name, $request->icon);
            $this->imageService->uploadImage($imageName, $request->icon);
            $team = $this->teamRepository->store($request->all(), $imageName);

            return new TeamResource($team);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the team.'.$th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        return new TeamResource($team);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTeamRequest  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTeamRequest $request, Team $team)
    {
        try {
            if ($request->has('icon')) {
                $imageName = $this->imageName($team->short_name, $request->icon);
                $this->imageService->deleteImage($team->icon);
                $this->imageService->uploadImage($imageName, $request->icon);
            } else {
                $imageName = $team->icon;
            }

            $teamUpdated = $this->teamRepository->update($team, $request->all(), $imageName);

            return new TeamResource($teamUpdated);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to update the team.'.$th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);

        try {
            $this->imageService->deleteImage($team->icon);
            $this->teamRepository->destroy($team);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the team.'.$th->getMessage()]);
        }
    }

    public function imageName(string $shortName, $image)
    {
        return 'team_'.$shortName.'.'.$image->getClientOriginalExtension();
    }
}
