<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * [ANÁLISE]
     *
     * - Não há limitação de caracteres na validação de campo
     *   Poderá ocorrer um erro de tamanho de coluna excedido no banco de dados ao realizar o store por exemplo dos campos name, email
     */
    public function rules(): array
    {
        return [
            'name'     => 'sometimes|nullable',
            'email'    => 'sometimes|nullable|email',
            'password' => 'sometimes|nullable',
            'type'     => 'sometimes|nullable|in:USER,VIRTUAL,MANAGER'
        ];
    }
}
