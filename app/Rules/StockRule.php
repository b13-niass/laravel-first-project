<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class StockRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
            preg_match('/articles\.(\d+)\.qteVente/', $attribute, $matches);
            if ($matches[1] >= 0) {
                $index = $matches[1];
                $articleId = request()->input('articles.' . $index . '.article_id');
                $article = DB::table('articles')->find($articleId);

                // Check if the article exists and validate quantity
                if ($article && $value > $article->qte) {
                    $fail("L'article avec l'ID {$articleId} a une quantité vendue ({$value}) supérieure à la quantité en stock ({$article->qte}).");
                }
            }
    }
}
