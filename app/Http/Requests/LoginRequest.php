<?php

namespace App\Http\Requests;

use App\Rules\CustomPassword;
use App\Trait\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;


class LoginRequest extends FormRequest
{
    use ApiResponseTrait;
    /**
     * Détermine si l'utilisateur est autorisé à faire cette demande.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Autorisation de validation
    }

    /**
     * Règles de validation pour la demande.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'login' => 'required|string|email',
            'password' => ["required","string", new CustomPassword()],
        ];

        return $rules;
    }

    /**
     * Messages de validation personnalisés.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'surnom.required' => 'Le surnom est obligatoire.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendResponse('failed', $validator->errors(), 'Validation errors', Response::HTTP_LENGTH_REQUIRED)
        );
    }
}
