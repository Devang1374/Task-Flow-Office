<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        
         $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        // If validation fails or returns false, trigger the fail callback
        if (!$response->successful() || !$response->json('success')) {
            $fail('The reCAPTCHA verification failed. Please try again.');
        }
    }
}
