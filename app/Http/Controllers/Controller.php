<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use DispatchesJobs;
    use ValidatesRequests;
    use AuthorizesRequests;

    /**
     * [ANÁLISE]
     *
     * - Não é interessante realizar alterações nesse arquivo, visto que em versões posteriores do laravel, pode ocorrer mudanças.
     *   Reforço a ideia de criar classes próprias genéricas que herdam de classes do laravel.
     *
     * - É interessante criar métodos para padronizar response body, dessa forma podemos reforçar o padrão.
     *   Podemos ter métodos que reforçam o padrão do response body da api para os cenários mais comuns.
     *
     *   Exemplo:
     *   $this->responseIndex($data)  --> Devolve dados com paginação sempre no mesmo padrão
     *   $this->responseStore($data)  --> Devolve um response body como ['message' => 'Usuário registrado com sucesso', 'data' => ['id' => 1111, 'name' => 'Lucas']]
     */

    /**
     * Helper para ser usado na resposta de todas as controllers filhas
     *
     * @param  DefaultResponse $response
     *
     * @return JsonResponse
     */
    public function response(DefaultResponse $response): JsonResponse
    {
        $jsonOptions = JSON_UNESCAPED_UNICODE + JSON_PRESERVE_ZERO_FRACTION;
        return response()->json($response->toArray(), $response->code, [], $jsonOptions);
    }
}
