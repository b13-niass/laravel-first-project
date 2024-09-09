<?php

namespace App\Exceptions;

use App\Trait\ApiResponseTrait;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;
    /**
     * Une liste des exceptions qui ne devraient pas être rapportées.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * Une liste des exceptions pour lesquelles le rapport ne devrait pas être enregistré.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Enregistre les exceptions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Log des exceptions (vous pouvez personnaliser cette partie)
        Log::error($exception);

        // Traitement des exceptions spécifiques
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'data' => null,
                'message' => "Cette élément n'existe pas",
            ], \Illuminate\Http\Response::HTTP_OK);
        }

        // Traitement des exceptions spécifiques
        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'data' => null,
                'message' => "Cette élément n'existe pas",
            ], \Illuminate\Http\Response::HTTP_OK);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return response()->json(['errors' => $exception->errors()], 422);
        }

        return parent::render($request, $exception);
    }

    /**
     * Rapporte l'exception si besoin.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        // Vous pouvez personnaliser la manière dont les exceptions sont rapportées
        parent::report($exception);
    }
}
