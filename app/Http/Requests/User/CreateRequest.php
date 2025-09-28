<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * [ANÁLISE]
     *
     * - Não há limitação de caracteres na validação de campo
     *   Poderá ocorrer um erro de tamanho de coluna excedido no banco de dados ao realizar o store por exemplo do campo name
     */
    public function rules(): array
    {
        return [
            'document_number' => 'required|regex:/[0-9]{11}/i',
            'name'            => 'required',
            'email'           => 'required|email',
            'password'        => 'required',
            'type'            => 'required|in:USER,VIRTUAL,MANAGER'
        ];
    }
}
