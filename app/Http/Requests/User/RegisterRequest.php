<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * [ANÁLISE]
 *
 * - É importante nunca herdar diretamente de classes do laravel
 *   Pensando nisso, é importante criarmos classes genércias nossas e elas que devem herdar da classe do laravel.
 *   Esse ponto é importante para caso a gente precise realizar alterações futuramente,
 *   dessa forma, vamos alterar apenas a classe pai e a alteração já pode refletir nas classes filhas.
 *
 *   Exemplo:
 *   No lugar de RegisterRequest herdar de FormRequest,
 *   poderia ser criada uma classe chamada BaseFormRequest, e a classe BaseFormRequest hedaria de FormRequest.
 *   E aí, a classe RegisterRequest bem como outros form requests que fossem criados, deveriam herdar de BaseFormRequest
 *
 * - O nome dos FormRequest devem ser melhor escritos
 *   É interessante utilizar um padrão no nome do FormRequest que permita identificar a entidade daquele form request e a que método se refere a validação dele.
 *
 *   Exemplo:
 *   RegisterRequest -> UserStoreRequest
 *   UpdateRequest   -> UserUpdateRequest
 *
 * - As regras de validação devem ser usadas dentro de array
 *   Usar regras de validação como string era utilizado no laravel 5, atualmente é recomendado utilizar como array. Exemplo:   'field' => ['required', 'string']
 *
 * - É importante utilizar a lib do lucascudo para deixar as mensagens de erros de validação em português.
 *   Instalando a lib e configurando corretamente, podemos deixar sem declarar o método public function messages(),
 *   pois as mensagens de erro de validação de campo já ficarão por padrão em português, visto que o laravel pegará as mensagens do arquivo backend-test/lang/pt_BR/validation.php
 *
 * - Além disso, faltou declarar o método public function attributes()
 *   É importante utilizar o método attributes para dar alias para os campos. Exemplo: 'user_document_number' => '[Número de documento do usuário]'
 *
 *  - Essa analise acima vale para os outros FormRequests
 *
 * - Não há limitação de caracteres na validação de campo
 *   Poderá ocorrer um erro de tamanho de coluna excedido no banco de dados ao realizar o store por exemplo dos campos user_name, company_document_number, email, user_document_number
 */
class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_document_number'    => 'required|regex:/[0-9]{11}/i',
            'user_name'               => 'required',
            'company_document_number' => 'required|regex:/[0-9]{14}/i',
            'company_name'            => 'required',
            'email'                   => 'required|email',
            'password'                => 'required',
        ];
    }
}
