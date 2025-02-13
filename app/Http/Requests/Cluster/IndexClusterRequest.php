<?php

namespace App\Http\Requests\Cluster;

use Illuminate\Foundation\Http\FormRequest;

class IndexClusterRequest extends FormRequest
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
            'includes' => ['sometimes', 'array'],
            'includes.*' => ['string', 'in:cities,activeCities'],
            'perPage' => ['sometimes', 'integer', 'min:1'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
