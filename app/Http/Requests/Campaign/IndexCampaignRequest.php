<?php

namespace App\Http\Requests\Campaign;

use Illuminate\Foundation\Http\FormRequest;

class IndexCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
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
            'includes.*' => ['string', 'in:activeDiscounts,discounts'],
            'perPage' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
