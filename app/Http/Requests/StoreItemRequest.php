<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'category' => 'nullable|in:Açougue e Peixaria,Laticínios e Frios,Mercearia,Padaria,Bebidas,Limpeza,Higiene e Beleza,Pet Shop,Utilidades Domésticas',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string',
        ];
    }
}
