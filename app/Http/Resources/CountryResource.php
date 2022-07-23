<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'local_name' => $this->local_name,
            'english_name' => $this->english_name,
            'position' => $this->position,
            'icon' => $this->icon,
            'icon_url' => asset('storage/'.$this->icon),
            'status' => $this->status,
        ];
    }
}
