<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Responses\DefaultResponse;

/**
 * [ANÁLISE]
 *
 * - Muitos comentários no código
 *   A tendência de ter comentários no código é eles ficarem desatualizados. Não é uma boa prática ter comentários no código.
 *
 *   À medida que ocorre a evolução no código, ocorre alterações, e os comentários são ficados para trás.
 *   Se está tendo comentário para algum trecho de código, ele não está clean code o suficiente. Então é um sinal de que precisa ser refatorado.
 *
 *   Um ponto importante é que comentários podem sim ser utilizados,
 *   porém para determinados códigos que são realmente complexos, apenas em alguns cenários.
 *   Por exemplo: para melhorar o autocomplete do intelisense.
 *   O intelisense é o melhor amigo do dev, o dev deve usar e abusar do intelisense.
 *
 *   Um outro ponto, é que comentários nesse caso desse controller, se tornam redundantes, pois já temos type-hint no retorno do método.
 *   Dessa forma, estamos apenas inchando o código.
 */

class HealthCheckController extends Controller
{
    /**
     * Healthcheck
     *
     * POST api/healthcheck
     *
     * @return JsonResponse
     */
    public function healthCheck(): JsonResponse
    {
        return $this->response(new DefaultResponse());
    }
}
