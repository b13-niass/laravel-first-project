<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use App\Models\User;
use App\Rules\PhoneNumber;
use App\Trait\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClientByPhoneRequest extends FormRequest
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
            'telephone' => ['required', new PhoneNumber()]
        ];
    }
    public function messages(): array{
        return [
            'telephone.required' => 'Le numéro de téléphone est requis',
            'telephone.phone_number' => 'Le numéro de téléphone n\'est pas valide'
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendResponse(StateEnum::ECHEC, $validator->errors(), 'Erreur Validation', Response::HTTP_LENGTH_REQUIRED)
        );
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            $this->sendResponse(StateEnum::ECHEC,null, "Vous n'êtes pas authorisés à faire cette action", Response::HTTP_LENGTH_REQUIRED)
        );
    }
}
