<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->includes)) {
            $this->merge([
                'includes' => explode(',', $this->includes),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'perPage' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'includes' => ['nullable', 'array'],
            'includes.*' => ['string', 'in:attachments'],
        ];
    }
}
