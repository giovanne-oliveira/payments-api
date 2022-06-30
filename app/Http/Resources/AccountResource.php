<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'accountId' => $this->id,
            'accountOwner' => new UserResource($this->owner()),
            'isActive' => $this->isActive,
            'balance' => $this->balance,
        ];
    }
}
