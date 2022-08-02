<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BetResource extends JsonResource
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
            'owner' => new UserResource($this->user),
            'phase' => new PhaseResource($this->phase),
            'title' => $this->title,
            'description' => $this->description,
            'stake' => $this->stake,
            'status' => $this->status,
            'points_good_score' => $this->points_good_score,
            'points_good_1n2' => $this->points_good_1n2,
            'bettors_count' => $this->bettors_count,
        ];
    }
}
