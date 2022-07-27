<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
            'short_name' => $this->short_name,
            'name' => $this->name,
            'city' => $this->city,
            'status' => $this->status,
            'icon_url' => asset('storage/'.$this->icon),
            'country' => new CountryResource($this->country),
            'sport' => new SportResource($this->sport),
        ];
    }
}
