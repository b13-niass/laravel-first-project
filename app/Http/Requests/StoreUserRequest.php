<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use App\Models\User;
use App\Rules\CustomPassword;
use App\Trait\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StoreUserRequest extends FormRequest
{
    use ApiResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = User::find(Auth::user()->id);
//        dd(Gate::allows("isAdmin", $user));
        return Gate::allows("isAdmin", $user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'login' => 'required|unique:users,login|email',
            'role' => ['required','string', 'in:ADMIN,BOUTIQUIER'],
            'password' => ["required","string","confirmed", new CustomPassword()],
//            'photo' => 'required|image|mimes:jpeg,png,jpg,svg|max:40',
            'active' => ['required','boolean']
        ];
    }

    public function messages(): array{
        return [
            'nom.required' => 'Le nom est requis',
            'nom.string' => 'Le nom doit être une chaîne de caractères',
            'nom.max' => 'Le nom ne doit pas dépasser 100 caractères',
            'prenom.required' => 'Le prénom est requis',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères',
            'prenom.max' => 'Le prénom ne doit pas dépasser 100 caractères',
            'login.required' => 'Le login est requis',
            'login.unique' => 'Ce login est déjà utilisé',
            'login.email' => 'Le login doit être une adresse email valide',
            'role.required' => 'Le rôle est requis',
            'role.string' => 'Le rôle doit',
            'role.in' => 'Le rôle doit être ADMIN ou BOUTIQUIER',
            'password.required' => 'Le mot de passe est requis',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
            'password.custom_password' => 'Le mot de passe doit contenir une lettre majuscule',
            'active.required' => 'L\'état est requis',
            'active.boolean' => 'L\'état doit être un booléen',
//            'photo.required' => 'La photo est requise',
//            'photo.image' => 'Le format de la photo doit être une image',
//            'photo.mimes' => 'Le format de la photo doit être JPG, JPEG, PNG ou GIF',
//            'photo.max' => 'La taille de la photo doit être inférieure à 40Ko'
        ];
    }
    protected function failedValidation(Validator $validator)
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

    protected function failedAuthorization()
    {
        return response()->json([
            'data' => null,
            'message' => "Vous n'êtes pas authorizé à acceder à cette ressource",
        ], Response::HTTP_FORBIDDEN);
    }

}
