<?php

namespace Modules\Credential\Requests;

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
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:hosting,vps,ssh,database,cpanel,cloud,ftp,api_key,email',
            'provider' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
            'host_ip' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:2048',
            'database_name' => 'nullable|string|max:255',
            'database_user' => 'nullable|string|max:255',
            'database_password' => 'nullable|string|max:2048',
            'ssh_key' => 'nullable|string',
            'auth_url' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'expires_at' => 'nullable|date',
            'is_favorite' => 'boolean',
            'client_id' => 'nullable|exists:clients,id',
        ];
    }
}