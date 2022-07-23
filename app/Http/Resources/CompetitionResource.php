<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
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
            'country_id' => $this->country_id,
            'sport_id' => $this->sport_id,
            'sport' => new SportResource($this->sport),
            'short_name' => $this->short_name,
            'name' => $this->name,
            'position' => $this->position,
            'icon' => $this->icon,
            'icon_url' => asset('storage/'.$this->icon),
            'status' => $this->status,
        ];
    }
}
