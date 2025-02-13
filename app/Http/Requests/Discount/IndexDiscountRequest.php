<?php

namespace App\Http\Requests\Discount;

use Illuminate\Foundation\Http\FormRequest;

class IndexDiscountRequest extends FormRequest
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
            'includes.*' => ['string', 'in:campaigns,activeCampaigns'],
            'perPage' => ['sometimes', 'integer', 'min:1'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'name' => ['sometimes', 'string'],
        ];
    }
}
