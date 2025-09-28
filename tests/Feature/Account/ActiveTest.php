<?php

namespace Tests\Feature\Account;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\Http;
use Tests\Providers\Banking\AccountProvider;
use Symfony\Component\HttpFoundation\Response;

class ActiveTest extends TestCase
{
    /**
     * [ANÁLISE]
     * - Eu recomendaria seguir o nome do método de teste como snake_case, pois o nome do método de teste deve ser o mais descritivo possível.
     *   Dessa forma, ficará mais legível.
     *
     * - Não é uma boa prática utilizar a factory chamando o método factory da model User::factory()
     *   Dessa forma, perdemos a vantagem do autocomplete do intelisense, e ficamos sem saber quais métodos existem na classe.
     *   Aí precisamos entrar na classe para saber quais existem, e isso atralha e causa uma má experiência de desenvolvimento.
     */
    public function testActive()
    {
        $user    = User::factory()->user()->create();
        $account = Account::factory()->registered()->create(
            [
                'user_id' => $user->id,
            ]
        );
        $token = $user->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $bankingResponse = app(AccountProvider::class)->active();

        $urlAuth   = config('auth.banking_base_url') . 'auth/vexpenses/token';
        $urlActive = config('auth.banking_base_url') . "accounts/$account->external_id/status/active";

        Http::fake(
            [
                $urlAuth      => Http::response(
                    [],
                    Response::HTTP_OK
                ),
                $urlActive  => Http::response(
                    $bankingResponse,
                    Response::HTTP_OK,
                ),
                '*'           => Http::response(
                    [],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                ),
            ]
        );

        $response = $this->put("/api/users/$user->id/account/active", [], $headers); // Poderia ter utilizado o método ->withHeaders();

        Http::assertSentInOrder(
            [
                $urlAuth,
                $urlActive,
            ]
        );

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'PUT',
                'code'    => 200,
                'data'    => null,
            ],
            true
        );
    }
}
