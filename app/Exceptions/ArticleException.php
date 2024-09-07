<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleException extends Exception
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
