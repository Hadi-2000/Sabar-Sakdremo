<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePerbaikanRequest extends FormRequest
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
            'nama' => 'required|string|min:1',
            'mesin' => 'required|string|min:1',
            'keterangan' => 'required|string|min:2',
            'status' => 'required|string|min:1',
            'jumlah' => 'required'
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'nama' => trim(strip_tags($this->nama)),
            'mesin' => trim(strip_tags($this->mesin)),
            'keterangan' =>  trim(strip_tags($this->keterangan))
        ]);
    }
}
