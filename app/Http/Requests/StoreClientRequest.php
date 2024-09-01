<?php

namespace App\Http\Requests;

use App\Rules\CustomPassword;
use App\Rules\PhoneNumber;
use App\Trait\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator as Valid;
use Illuminate\Support\Facades\Validator;

class StoreClientRequest extends FormRequest
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
            // Define 'user' object as optional
            'user' => [
                'sometimes',
                'array',
                function ($attribute, $value, $fail) {
                    if (is_array($value)) {
                        // Define validation rules for 'user' properties
                        $userValidator = Validator::make($value, [
                            'nom' => 'required|string|max:255',
                            'prenom' => 'required|string|max:255',
                            'login' => 'required|string|unique:users,login|email',
                            'role' => 'required|string|in:ADMIN,BOUTIQUIER', // Update roles as per your application
                            'password' => ["required", "string","confirmed", new CustomPassword()], // Update password validation as needed
                        ]);

                        // If validation fails, return the first error
                        if ($userValidator->fails()) {
                            $fail($userValidator->errors()->first());
                        }
                    }
                }
            ],
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

    public function failedValidation(Valid $validator)
    {
        throw new HttpResponseException(
            $this->sendResponse('failed', $validator->errors(), 'Validation errors', 400)
        );
    }
}
