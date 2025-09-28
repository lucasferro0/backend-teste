<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UseCases\Card\Register;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Responses\DefaultResponse;
use App\Integrations\Banking\Card\Find;

class CardController extends Controller
{
    /**
     * [ANÁLISE]
     *
     * - Fugiu do padrão e o controller está chamando uma classe Integration direto.
     *   Se for seguir o padrão, deveria chamar uma UseCase e o UseCase deveria chamar o Repository e o Integration
     */
    public function show(string $userId): JsonResponse
    {
        $response = (new Find($userId))->handle();

        return $this->response(
            new DefaultResponse($response['data'])
        );
    }

    /**
     * [ANÁLISE]
     *
     * - De acordo com o código, o card_id é o id externo do cartão, porém não é interessante usar o id externo do cartão
     *   Pois ele é o id lá na api do banco e não é interessante expor esse id.
     *   O client da nossa api deve saber apenas do id do cartão de fato, que é a pk da tabela card.
     *   Dessa forma, temos controle sobre esse id.
     *   E vamos garantir que ele sempre vai existir e vai respeitar o nosso padrão.
     */
    public function register(string $userId, Request $request): JsonResponse
    {
        $response = (new Register($userId, $request->pin, $request->card_id))->handle();

        return $this->response(
            new DefaultResponse($response['data'])
        );
    }
}
