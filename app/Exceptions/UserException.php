<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UserException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(): JsonResponse
    {
        $statusCode = $this->getCode();

        return response()->json([
            'data' => null,
            'message' => $this->getMessage(),
        ], $statusCode);
    }

}
