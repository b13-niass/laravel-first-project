<?php

namespace App\Http\Requests;

use App\Enums\StateEnum;
use App\Models\User;
use App\Rules\StockRule;
use App\Trait\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AddDetteRequest extends FormRequest
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

    public function rules(): array
    {
        return [
            'montant' => [
                'required',
                'numeric',
                'min:1',
            ],
            'client_id' => [
                'required',
                'exists:clients,id',
            ],
            'articles' => [
                'required',
                'array',
                'min:1',
            ],
            'articles.*.article_id' => [
                'required',
                'exists:articles,id',
            ],
            'articles.*.qteVente' => [
                'required',
                'numeric',
                new StockRule()
            ],
            'articles.*.prixVente' => [
                'required',
                'numeric',
                'min:0',
            ],
            'paiement' => 'sometimes',
            'paiement.montant' => [
                'required_with:paiement',
                'numeric',
                function ($attribute, $value, $fail) {
                    $totalDette = request()->input('montant');
//                    dd($value);
                    if ($value > $totalDette) {
                        $fail('Le montant du paiement ne peut pas être supérieur au montant de la dette.');
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'montant.required' => 'Le montant de la dette est requis.',
            'montant.numeric' => 'Le montant de la dette doit être un nombre.',
            'montant.min' => 'Le montant de la dette doit être supérieur ou égal à zéro.',

            'client_id.required' => 'Le client est requis.',
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',

            'articles.required' => 'Le tableau des articles est requis.',
            'articles.array' => 'Le format des articles est invalide.',
            'articles.min' => 'Le tableau des articles doit contenir au moins un article.',

            'articles.*.article_id.required' => 'L\'ID de l\'article est requis.',
            'articles.*.article_id.exists' => 'L\'ID de l\'article sélectionné n\'existe pas.',

            'articles.*.qteVente.required' => 'La quantité vendue est requise.',
            'articles.*.qteVente.numeric' => 'La quantité vendue doit être un nombre.',

            'articles.*.prixVente.required' => 'Le prix de vente est requis.',
            'articles.*.prixVente.numeric' => 'Le prix de vente doit être un nombre.',
            'articles.*.prixVente.min' => 'Le prix de vente doit être supérieur ou égal à zéro.',

            'paiement.montant.numeric' => 'Le montant du paiement doit être un nombre.',
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
