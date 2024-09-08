<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'montant' => $this->montant,
            'montant_verse' => $this->montant_verse,
            'montant_du' => $this->montant_du,
            'client' => $this->whenLoaded('client', function () {
                return new ClientResource($this->client);
            }),
            'articles' => ArticleResource::collection($this->whenLoaded('articles'))
        ];
    }
}
