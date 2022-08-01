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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'token' => $this->when($request->user()->isAdmin(), $this->token),
            'invitation_sent_at' => $this->invitation_sent_at,
        ];
    }
}
