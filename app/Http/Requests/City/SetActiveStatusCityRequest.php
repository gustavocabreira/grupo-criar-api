<?php

namespace App\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;

class SetActiveStatusCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => ['required', 'boolean'],
        ];
    }
}
