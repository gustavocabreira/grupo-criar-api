<?php

namespace App\Http\Requests\Campaign\Discount;

use Illuminate\Foundation\Http\FormRequest;

class AssignDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discounts' => ['required', 'array'],
            'discounts.*' => ['exists:discounts,id'],
        ];
    }
}
