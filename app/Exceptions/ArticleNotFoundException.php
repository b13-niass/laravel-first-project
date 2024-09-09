<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ArticleNotFoundException extends ModelNotFoundException
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(): JsonResponse
    {
        $statusCode = $this->getCode(); // Obtient le code de statut défini lors de la levée de l'exception

        return response()->json([
            'data' => null,
            'message' => $this->getMessage(),
        ], $statusCode);
    }

}
