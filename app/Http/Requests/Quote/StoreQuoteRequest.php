<?php

namespace App\Http\Requests\Quote;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'full_name' => trim((string) $this->input('fullName', '')),
            'phone'     => trim((string) $this->input('phone', '')),
            'email'     => strtolower(trim((string) $this->input('email', ''))),
            'company'   => trim((string) $this->input('company', '')),
            'interest'  => trim((string) $this->input('interest', '')),
            'details'   => trim((string) $this->input('details', '')),
            'website'   => trim((string) $this->input('website', '')),  // honeypot
        ]);
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'min:2', 'max:150'],
            'phone'     => ['required', 'string', 'min:7', 'max:30', 'regex:/^[0-9+()\s-]+$/'],
            'email'     => ['required', 'email:rfc,dns', 'max:255'],
            'company'   => ['nullable', 'string', 'max:150'],
            'interest'  => ['required', 'string', 'max:100'],
            'details'   => ['nullable', 'string', 'max:2000'],
            'website'   => ['nullable', 'string', 'max:255'],  // honeypot — must be empty
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Please enter your full name.',
            'phone.required'     => 'Please enter a phone number we can reach you on.',
            'phone.regex'        => 'Phone number contains invalid characters.',
            'email.required'     => 'Please enter your email address.',
            'email.email'        => 'That does not look like a valid email.',
            'interest.required'  => 'Please tell us what you need a quote for.',
        ];
    }
}