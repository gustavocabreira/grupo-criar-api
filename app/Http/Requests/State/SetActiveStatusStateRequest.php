<?php

namespace App\Http\Requests\State;

use Illuminate\Foundation\Http\FormRequest;

class SetActiveStatusStateRequest extends FormRequest
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
