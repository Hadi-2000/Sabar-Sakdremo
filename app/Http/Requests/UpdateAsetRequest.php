<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAsetRequest extends FormRequest
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
            'nama'=> 'required|string|min:1',
            'deskripsi' => 'required|string|min:2',
            'satuan' => 'required|string|min:1',
            'harga_satuan' => 'required|string|min:1'
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'nama'=> trim(strip_tags($this->nama)),
            'deskripsi' => trim(strip_tags($this->deskripsi)),
            'satuan' => trim($this->satuan),
            'harga_satuan' => trim($this->harga_satuan)
        ]);
    }
}
