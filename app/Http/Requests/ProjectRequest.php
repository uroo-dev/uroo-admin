<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['nullable', 'exists:clients,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['development', 'testing', 'revision', 'completed', 'archived'])],
            'platform' => ['nullable', 'string', 'max:100'],
            'tech_stack' => ['nullable', 'array'],
            'tech_stack.*' => ['string', 'max:50'],
            'storage_usage' => ['nullable', 'integer', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Project name is required.',
            'status.in' => 'Status must be one of: development, testing, revision, completed, archived.',
            'deadline.after_or_equal' => 'Deadline must be after or equal to the start date.',
        ];
    }
}
