<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'login' => $this->login,
            'active' => $this->active,
            'photo' => $this->photo,
            $this->mergeWhen($this->relationLoaded('role'), [
                'role' => new RoleResource($this->role),
            ]),
        ];
    }
}
