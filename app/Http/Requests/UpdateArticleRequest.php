<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Trait\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UpdateArticleRequest extends FormRequest
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
            'qte' => 'required|numeric|min:1'
        ];
    }

    public function messages()
    {
        return [
            'qte.required' => 'Le champ qte est requis.',
            'qte.numeric' => 'Le champ qte doit être un nombre.',
            'qte.min' => 'Le champ qte doit être supérieur ou égal à 1.'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendResponse('failed', $validator->errors(), 'Validation errors', Response::HTTP_LENGTH_REQUIRED)
        );
    }
}
