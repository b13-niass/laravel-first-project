<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\CustomPassword;
use App\Rules\PhoneNumber;
use App\Trait\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator as Valid;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
        $user = User::find(Auth::user()->id);
        return Gate::allows("isBoutiquier", $user);
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
                'unique:clients,surnom'
            ],
            'telephone' => [
                'required',
                'string',
                new PhoneNumber(),
                'unique:clients,telephone'
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
                            'login' => 'required|string|unique:users,login',
                            'active' => 'required|boolean',
                            'password' => ["required", "string","confirmed", new CustomPassword()], // Update password validation as needed
                            'photo' => 'required|image|mimes:jpeg,png,jpg,svg|max:40'
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
            'surnom.required' => 'Le surnom est requis.',
            'surnom.string' => 'Le surnom doit être une chaîne de caractères.',
            'surnom.max' => 'Le surnom ne peut pas dépasser 255 caractères.',
            'surnom.unique' => 'Le surnom existe déjà.',
            'adresse.string' => 'L’adresse doit être une chaîne de caractères.',
            'adresse.max' => 'L’adresse ne peut pas dépasser 255 caractères.',
            'telephone.required' => 'Le numéro de téléphone est requis.',
            'telephone.unique' => 'Le numéro de téléphone existe déjà.',
            'user.nom.required_with' => 'Le nom est requis lorsque l\'utilisateur est fourni.',
            'user.nom.string' => 'Le nom doit être une chaîne de caractères.',
            'user.prenom.required_with' => 'Le prénom est requis lorsque l\'utilisateur est fourni.',
            'user.prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'user.login.required_with' => 'Le login est requis lorsque l\'utilisateur est fourni.',
            'user.login.string' => 'Le login doit être une chaîne de caractères.',
            'user.login.email' => 'Le login doit être une adresse email valide.',
            'user.login.unique' => 'Le login existe déjà.',
            'user.active.required_with' => 'Le statut actif est requis lorsque l\'utilisateur est fourni.',
            'user.active.boolean' => 'Le statut actif doit être un booléen.',
            'user.password.required_with' => 'Le mot de passe est requis lorsque l\'utilisateur est fourni.',
            'user.password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'Le format de l\'image doit être JPG, JPEG, PNG ou GIF.',
            'photo.max' => 'La taille de l\'image doit être inférieure à 40Ko.',
        ];
    }

    public function failedValidation(Valid $validator)
    {
        throw new HttpResponseException(
            $this->sendResponse('failed', $validator->errors(), 'Validation errors', Response::HTTP_LENGTH_REQUIRED)
        );
    }

    /**
     * Prépare les données pour la validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
//        \Log::info('Request data before merging:', $this->all());
        if ($this->has('user')) {
            // Retrieve the existing 'user' data from the request or initialize as an empty array if not present
            $userData = $this->input('user', []);

            // Update or add the 'active' key, converting it to a boolean
            $userData['active'] = filter_var($this->input('user.active', false), FILTER_VALIDATE_BOOLEAN);

            // Merge the updated 'user' data back into the request
            $this->merge([
                'user' => $userData
            ]);
        }
//        \Log::info('Request data after merging:', $this->all());
    }
}
