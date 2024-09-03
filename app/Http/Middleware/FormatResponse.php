<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormatResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Proceed with the request and get the response
        $response = $next($request);

        // Check if the response is an instance of JsonResponse or Response
        if ($response instanceof Response) {
            $originalData = $response->getOriginalContent();

            // Format the response data
            $formattedData = [
                'status' => $response->status(),
                'data' => $originalData['data'] ?? null,
                'message' => $originalData['message'] ?? $this->getDefaultMessage($response->status()),
                'success' => $response->isSuccessful(),
            ];

            // Return a new JSON response with the formatted data
            return response()->json($formattedData, $response->status());
        }

        return $response;
    }

    /**
     * Get the default message based on the status code.
     *
     * @param int $statusCode
     * @return string
     */
    protected function getDefaultMessage(int $statusCode): string
    {
        return match ($statusCode) {
            200 => 'La requête a été effectuée avec succès.',
            201 => 'La ressource a été créée avec succès.',
            400 => 'Mauvaise requête.',
            401 => 'Non autorisé.',
            403 => 'Interdit.',
            404 => 'Ressource non trouvée.',
            500 => 'Erreur interne du serveur.',
            default => 'Une erreur inattendue s\'est produite.',
        };
    }
}
