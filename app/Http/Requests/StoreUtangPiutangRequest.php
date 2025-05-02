<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUtangPiutangRequest extends FormRequest
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
            'nama_pelanggan' => 'required|string|min:1',
            'alamat_pelanggan' => 'nullable|string|min:2',
            'keterangan' => 'nullable|string|min:1',
            'jumlah' => 'required_unless:ambil,Stock|numeric|min:1',
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'nama_pelanggan' => trim(strip_tags($this->nama_pelanggan)),
            'alamat_pelanggan' => trim(strip_tags($this->alamat_pelanggan)),
            'keterangan' => trim(strip_tags($this->keterangan)),
        ]);
    }
}
