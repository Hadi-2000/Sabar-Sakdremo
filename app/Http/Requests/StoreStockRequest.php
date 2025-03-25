<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockRequest extends FormRequest
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
            'nama'=> 'required|string|min: 1',
            'jumlah' => 'required|string|min:2'
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'nama'=> trim(strip_tags($this->nama)),
        ]);
    } 
}
    