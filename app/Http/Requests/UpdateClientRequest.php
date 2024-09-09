<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumber;
use App\Trait\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;

class UpdateClientRequest extends FormRequest
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
        $id = $this->route('id'); // Récupère l'ID depuis la route

        $rules = [
            'surnom' => [
                'required',
                'string',
                'max:255',
                'unique:clients,surnom,' . $id
            ],
            'telephone' => [
                'required',
                'string',
                new PhoneNumber(),
                'unique:clients,telephone,' . $id
            ],
            'adresse' => 'nullable|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg,svg|max:40'
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
            'surnom.string' => 'Le surnom doit être une chaîne de caractères.',
            'surnom.max' => 'Le surnom ne doit pas dépasser 255 caractères.',
            'surnom.unique' => 'Ce surnom est déjà pris.',

            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'telephone.phone_number' => "Le numéro de téléphone n'est pas correcte.",

            'adresse.string' => 'L’adresse doit être une chaîne de caractères.',
            'adresse.max' => 'L’adresse ne doit pas dépasser 255 caractères.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendResponse('failed', $validator->errors(), 'Validation errors', Response::HTTP_LENGTH_REQUIRED)
        );
    }
}
