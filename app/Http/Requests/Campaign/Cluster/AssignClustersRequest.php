<?php

namespace App\Http\Requests\Campaign\Cluster;

use Illuminate\Foundation\Http\FormRequest;

class AssignClustersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clusters' => ['required', 'array'],
            'clusters.*' => ['integer', 'exists:clusters,id'],
        ];
    }
}
