<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BetLightResource extends JsonResource
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
            'title' => $this->title,
            'status' => $this->status,
            'points_good_score' => $this->points_good_score,
            'points_good_1n2' => $this->points_good_1n2,
        ];
    }
}
