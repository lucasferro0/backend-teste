<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * [ANÁLISE]
     *
     * - Não há limitação de caracteres na validação de campo
     *   Poderá ocorrer um erro de tamanho de coluna excedido no banco de dados ao realizar o store por exemplo dos campos name
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
