<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id'              => 'required|exists:clients,id',
            'due_date'               => 'required|date',
            'items'                  => 'required|array|min:1',
            'items.*.description'    => 'required|string|max:255',
            'items.*.quantity'       => 'required|numeric|min:0.01',
            'items.*.rate'           => 'required|numeric|min:0',
            'tax_percent'            => 'nullable|numeric|min:0|max:100',
            'discount_percent'       => 'nullable|numeric|min:0|max:100',
            'paid_amount'            => 'nullable|numeric|min:0',
            'notes'                  => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'               => 'Tambahkan minimal 1 item tagihan.',
            'items.min'                    => 'Tambahkan minimal 1 item tagihan.',
            'items.*.description.required' => 'Nama item wajib diisi.',
            'items.*.quantity.required'    => 'Qty wajib diisi.',
            'items.*.quantity.min'         => 'Qty harus lebih dari 0.',
            'items.*.rate.required'        => 'Harga wajib diisi.',
            'items.*.rate.min'             => 'Harga tidak boleh negatif.',
        ];
    }
}
