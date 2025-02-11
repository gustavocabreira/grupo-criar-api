<?php

namespace App\Http\Requests\Discount;

use Illuminate\Foundation\Http\FormRequest;

class CreateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'max:255'],
            'value' => ['nullable', 'numeric', 'min:0', 'required_without:percentage'],
            'percentage' => ['nullable', 'numeric', 'min:0', 'max:100', 'required_without:value'],
        ];
    }
}
