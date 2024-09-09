<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use App\Models\User;
use App\Rules\CustomPassword;
use App\Trait\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Validator as Valid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AccountForClientRequest extends FormRequest
{
    use ApiResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = User::find(Auth::user()->id);
        return Gate::allows("isBoutiquier", $user);
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     * @throws AuthorizationException
     */
    protected function failedAuthorization()
    {
        // Use the abort helper to throw an HttpException with a JSON response
        abort(
            $this->sendResponse(StateEnum::ECHEC,null, "Cette action n'est pas autorisée", Response::HTTP_FORBIDDEN)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|unique:users,login',
            'role_id' => 'required|numeric|exists:clients,id', // Update roles as per your application
            'active' => 'required|boolean',
            'client_id' => 'required|numeric|exists:clients,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg,svg|max:40',
            'password' => ["required", "string","confirmed", new CustomPassword()], // Update password validation as needed
        ];
    }

    public function messages(): array{
        return [
            'nom.required' => 'Le nom est obligatoire',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères',
            'prenom.required' => 'Le prénom est obligatoire',
            'prenom.max' => 'Le prénom ne doit pas dépasser 255 caractères',
            'login.required' => 'Le login est obligatoire',
            'login.unique' => 'Ce login est déjà utilisé',
            'role_id.required' => 'Le rôle est obligatoire',
            'role_id.numeric' => 'Le rôle doit être un nombre',
            'role_id.exists' => 'Ce rôle n\'existe pas',
            'active.required' => 'L\'état est obligatoire',
            'active.boolean' => 'L\'état doit être un booléen',
            'client_id.required' => "le client est obligatoire",
            'client_id.exists' => "Ce client n'existe pas",
            'photo.required' => 'Le photo est obligatoire',
            'photo.image' => 'Le format de la photo doit être une image',
            'photo.mimes' => 'Le format de la photo doit être jpeg, png, jpg, svg',
            'photo.max' => 'La taille de la photo ne doit pas dépasser',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères',
            'password.confirmed' => 'Les mots de passe doit être confirmé',
            'password.custom' => 'Le mot de passe doit contenir au moins 5 caractères, une lettre majuscule, une lettre minuscule, un nombre, un caractères special'
            ];
    }

    public function failedValidation(Valid $validator)
    {
        throw new HttpResponseException(
            $this->sendResponse(StateEnum::ECHEC, $validator->errors(), 'Erreur Validation', Response::HTTP_LENGTH_REQUIRED)
        );
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'active' => filter_var($this->active, FILTER_VALIDATE_BOOLEAN)
        ]);
    }
}
