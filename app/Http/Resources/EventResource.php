<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'team1' => new TeamLightResource($this->team1),
            'team2' => new TeamLightResource($this->team2),
            'date' => $this->date,
            'location' => $this->location,
            'status' => $this->status,
            'score_team1' => $this->score_team1,
            'score_team2' => $this->score_team2,
            'phase' => new PhaseResource($this->phase),
            'started' => $this->started,
        ];
    }
}
