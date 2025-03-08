<?php

namespace App\Http\Requests;

use App\Rules\ValidePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StorePegawaiRequest extends FormRequest
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
            'nama'=> 'required|string|min:2',
            'alamat'=> 'required|string|min:2',
            'no_telp'=> ['nullable',new ValidePhoneNumber()],
            'jumlah_hidden'=>  'required|numeric|min:1',
        ];
    }
}
