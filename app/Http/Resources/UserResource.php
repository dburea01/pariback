<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,

            $this->mergeWhen($request->user() && $request->user()->isAdmin(), [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'email_verified_at' => $this->email_verified_at,
                'status' => $this->status,
            ]),
        ];
    }
}
