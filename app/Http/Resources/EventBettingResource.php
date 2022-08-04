<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventBettingResource extends JsonResource
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
            'score_team1' => $this->score_team1,
            'score_team2' => $this->score_team2,
            'bettor' => new BettorResource($this->bettor),
            'event' => new EventResource($this->event)
        ];
    }
}
