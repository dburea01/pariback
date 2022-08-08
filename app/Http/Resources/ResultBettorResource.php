<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResultBettorResource extends JsonResource
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
            // 'bet_id' => $this->bet_id,
            // 'user_id' => $this->user_id,

            'score_team1' => $this->score_team1,
            'score_team2' => $this->score_team2,
            'points_for_this_user_bet' => $this->points_for_this_user_bet,
            'event' => new EventResource($this->event),
        ];
    }
}
