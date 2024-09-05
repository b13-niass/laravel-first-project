<?php

namespace App\Http\Resources;

use App\Trait\MyImageTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use MyImageTrait;
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
            'photo' => $this->getImageLocalAsBase64($this->photo),
            $this->mergeWhen($this->relationLoaded('role'), [
                'role' => new RoleResource($this->role),
            ]),
        ];
    }
}
