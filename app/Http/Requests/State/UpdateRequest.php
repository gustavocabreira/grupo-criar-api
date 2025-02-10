<?php

namespace App\Http\Requests\State;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $state = $this->route('state');

        return [
            'name' => ['required', 'string', 'max:255'],
            'acronym' => ['required', 'string', 'size:2', 'unique:states,acronym,'.$state->id],
        ];
    }
}
