<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the phone number starts with "77" or "76" and has exactly 9 digits
        if (!preg_match('/^(77|76)\d{7}$/', $value)) {
            $fail("L'attribut doit commencer par '77' ou '76' et contenir exactement 9 chiffres.");
        }
    }
}
