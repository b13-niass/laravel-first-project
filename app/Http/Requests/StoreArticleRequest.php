<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use App\Models\User;
use App\Trait\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StoreArticleRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'libelle' => 'required|string|unique:articles,libelle|max:255',
            'prix' => 'required|numeric',
            'qte' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'libelle.required' => 'Le libelle est obligatoire',
            'libelle.unique' => 'Ce libelle est déjà utilisé',
            'libelle.max' => 'Le libelle ne doit pas dépasser 255 caractères',
            'prix.required' => 'Le prix est obligatoire',
            'prix.numeric' => 'Le prix doit être un nombre',
            'qte.required' => 'La quantité est obligatoire',
            'qte.integer' => 'La quantité doit être un entier'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendResponse(StateEnum::ECHEC, $validator->errors(), 'Validation errors', Response::HTTP_LENGTH_REQUIRED)
        );
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            $this->sendResponse(StateEnum::ECHEC,null, "Vous n'êtes pas authorisés à faire cette action", Response::HTTP_LENGTH_REQUIRED)
        );
    }
}
