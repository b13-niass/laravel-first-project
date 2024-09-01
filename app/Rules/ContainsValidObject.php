<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContainsValidObject implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if (!is_array($value)) {
            $fail("$attribute doit contenir au moins un objet avec un « id » et un « qte » valides.");
            return;
        }

        $hasValidItem = false;

        foreach ($value as $item) {
            if (is_array($item) && isset($item['id']) && isset($item['qte'])) {
                if (is_int($item['id']) && is_int($item['qte']) && $item['qte'] > 0) {
                    $hasValidItem = true;
                    break;
                }
            }
        }

        if (!$hasValidItem) {
            $fail("L'attribute doit contenir au moins un objet avec un « id » et un « qte » valides.");
        }
    }
}
