<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ContainsValidObject;
use App\Trait\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UpdateArticleStockRequest extends FormRequest
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
            'articles' => ['required', 'array', new ContainsValidObject()],
            'articles.*.id' => 'required|integer',
            'articles.*.qte' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'articles.required' => 'Le champ « articles » est obligatoire.',
            'articles.array' => 'Le champ « articles » doit être un tableau.',

            'articles.*.id.required' => 'Chaque article doit avoir un identifiant.',
            'articles.*.id.integer' => 'L\'identifiant de chaque article doit être un entier.',

            'articles.*.qte.required' => 'Chaque article doit avoir une quantité.',
            'articles.*.qte.integer' => 'La quantité de chaque article doit être un entier.',
            'articles.*.qte.min' => 'La quantité de chaque article doit être d\'au moins 1.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendResponse('failed', $validator->errors(), 'Validation errors', Response::HTTP_LENGTH_REQUIRED)
        );
    }
}
