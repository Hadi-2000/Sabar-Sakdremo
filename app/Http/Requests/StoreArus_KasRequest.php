<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArus_KasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //variable untuk required|string
        return [
            'keterangan' => 'required|string|min:1',
            'jenis_kas' => 'required|string|min:2',
            'jenis_transaksi' => 'required|string|min:1',
            'jumlah_hidden' => 'required|numeric|min:1',
        ];
    }
    public function messages(): array
    {
        return [
            'keterangan.required' => 'Keterangan kas harus diisi',
            'jenis_kas.required' => 'Jenis kas harus diisi',
            'jenis_transaksi.required' => 'Jenis transaksi harus diisi'
        ];
    }
}
