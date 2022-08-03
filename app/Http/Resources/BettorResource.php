<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BettorResource extends JsonResource
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
            'user' => new UserResource($this->user),
            'invitation_sent_at' => $this->invitation_sent_at,
            $this->mergeWhen($request->user() && $request->user()->isAdmin(), [
                'token' => $this->token,
                'status' => $this->status,
            ]),
        ];
    }
}
