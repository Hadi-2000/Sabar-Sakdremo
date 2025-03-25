<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePenitipanBarangRequest extends FormRequest
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
        return [
            'nama_pelanggan' => 'required|string',
            'barang' => 'required|string|min:1',
            'jumlah_hidden' => 'nullable|numeric|min:1',
            'jumlah' => 'nullable|numeric|min:1',
            'status'=>'required|string|min:1',
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'nama_pelanggan' => trim(strip_tags($this->nama_pelanggan)),
            'barang' => trim(strip_tags($this->barang))
        ]);
    }
}
