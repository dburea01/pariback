<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResultBetResource extends JsonResource
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
            'event_id' => $this->id,
            'phase_id' => $this->phase_id,
            'team1_id' => $this->team1_id,
            'team2_id' => $this->team2_id,
            'date' => $this->date,
            'status' => $this->status,
            'score_team1' => $this->score_team1,
            'score_team2' => $this->score_team2,

            'bettors' => $this->bettors,
        ];
    }
}
