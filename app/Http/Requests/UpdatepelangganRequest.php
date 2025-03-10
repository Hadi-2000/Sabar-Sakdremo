<?php

namespace App\Http\Requests;

use App\Rules\ValidePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class UpdatepelangganRequest extends FormRequest
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
        ];
    }
}
