<?php

namespace App\Http\Requests\Concerns;

trait ValidatesContactFields
{
    protected function contactFieldRules(): array
    {
        return [
            'email' => ['nullable', 'email:strict', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9][0-9\s\-()]{7,18}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Please enter a valid email address.',
            'phone.regex' => 'Please enter a valid phone number (e.g. +8801712345678 or 01712345678).',
        ];
    }
}
