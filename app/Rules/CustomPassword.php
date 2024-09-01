<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CustomPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Create a Validator instance with the password rules
        $validator = Validator::make([$attribute => $value], [
            $attribute => [
                'required',
                Password::min(5) // Minimum length of 8 characters
                    ->letters() // Must include letters
                    ->mixedCase() // Must include mixed case letters
                    ->numbers() // Must include numbers
                    ->symbols(), // Must include symbols
            ],
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Get the first error message
            $message = $validator->errors()->first();
            $fail($message);
        }
    }
}
