<?php

namespace App\Http\Requests\Cluster\City;

use Illuminate\Foundation\Http\FormRequest;

class RemoveCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cities' => ['required', 'array'],
            'cities.*' => ['exists:cities,id'],
        ];
    }
}
