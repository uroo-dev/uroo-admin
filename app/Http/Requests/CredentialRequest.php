<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CredentialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label'    => 'required|string|max:255',
            'type'     => 'required|string|max:100',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:2048',
        ];
    }
}
