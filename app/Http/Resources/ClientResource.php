<?php

namespace App\Http\Resources;

use App\Trait\MyImageTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
                'surnom' => $this->surnom,
                'telephone' => $this->telephone,
                'adresse' => $this->adresse,
                'user' => $this->whenLoaded('user', function () {
                    return new UserResource($this->user);
                }),
                'qrcode' => $this->qrcode
            ];
    }
}
