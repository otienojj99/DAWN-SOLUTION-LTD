<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'    => trim((string) $this->input('name', '')),
            'text'    => trim((string) $this->input('text', '')),
            'website' => trim((string) $this->input('website', '')),  // honeypot
        ]);
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'rating'     => ['required', 'integer', 'min:1', 'max:5'],
            'name'       => ['required', 'string', 'min:2', 'max:100'],
            'text'       => ['required', 'string', 'min:10', 'max:1000'],
            'website'    => ['nullable', 'string', 'max:255'],  // honeypot
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Please choose a star rating.',
            'rating.min'      => 'Please choose a star rating.',
            'name.required'   => 'Please enter your name.',
            'text.required'   => 'Please write a review.',
            'text.min'        => 'Please write at least 10 characters.',
            'text.max'        => 'Review is too long (max 1000 characters).',
        ];
    }
}