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
        $response = $next($request);
        if ($response instanceof Response) {
            $originalData = $response->getOriginalContent();
            $status = $response->getStatusCode();
           if ($originalData['data'] instanceof JsonResponse) {
               $status = $originalData['data']->status();
               $originalData = $originalData['data']->original;
           }
            $formattedData = [
                'status' => $status,
                'data' => $originalData['data'] ?? null,
                'message' => $originalData['message'] ?? $this->getDefaultMessage($status),
                'success' => 300 > $status && $status >= 200,
            ];
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
