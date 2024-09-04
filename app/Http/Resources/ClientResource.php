<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'surnom' => $this->surnom,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            })
        ];
    }
}
