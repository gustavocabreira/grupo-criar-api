<?php

namespace App\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;

class ShowCityRequest extends FormRequest
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
            'includes.*' => ['string', 'in:state'],
        ];
    }
}
