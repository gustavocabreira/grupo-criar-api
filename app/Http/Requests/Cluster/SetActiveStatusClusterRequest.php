<?php

namespace App\Http\Requests\Cluster;

use Illuminate\Foundation\Http\FormRequest;

class SetActiveStatusClusterRequest extends FormRequest
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
