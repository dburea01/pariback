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
            'bet' => new BetLightResource($this->bet),
            'bettor' => new UserResource($this->user),

            'quantity_points_bet' => $this->quantity_points_bet,
            'results_bettor' => ResultBettorResource::collection($this->results_bettor)
        ];
    }
}
