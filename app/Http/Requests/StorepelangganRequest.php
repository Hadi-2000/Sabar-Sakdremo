<?php

namespace App\Http\Requests;

use App\Rules\ValidePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class StorepelangganRequest extends FormRequest
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
            'nama' => 'required|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'no_telepon' => ['nullable', new ValidePhoneNumber()],
            'utangPiutang'=> 'nullable|string',
            'total'=> ['nullable', 'numeric', 'regex:/^\d{1,15}(\.\d{1,2})?$/'],
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'nama' => trim(strip_tags($this->nama)),
            'alamat' => trim(strip_tags($this->alamat)),
            'utangPiutang'=> 'nullable|string',
            'total'=> trim($this->total),
        ]);
    }
}
