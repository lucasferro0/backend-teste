<?php

namespace App\UseCases\User;

use Throwable;
use App\UseCases\BaseUseCase;
use App\Repositories\Token\Create as create_token;

class Login extends BaseUseCase
{
    /**
     * @var string
     */
    protected string $id;

    /**
     * Token de acesso
     *
     * @var string
     */
    protected string $token;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Criação de token de acesso
     *
     * @return void
     */
    protected function createToken(): void
    {
        /**
         * [ANÁLISE]
         *
         * - Alias para classe deixando fora do padrão recomendado na PSR12.
         *   Nome de classe deve ficar em PascalCase
         */
        $this->token = (new create_token($this->id))->handle();
    }

    /**
     * Cria um usuário MANAGER e a empresa
     */
    public function handle()
    {
        try {
            $this->createToken();
        } catch (Throwable $th) {
            /**
             * [ANÁLISE]
             *
             * - Se esse método estivesse funcionando, estaria sendo criado log caso não encontrasse o usuário na chamada (new create_token($this->id))->handle();
             *   pois lá tem um findOrFail();
             *
             * - Não é interessante criar log nesses casos.
             *
             * - Deve criar log para coisas que realmente são erros, quando é um erro inesperado.
             */
            $this->defaultErrorHandling(
                $th,
                [
                    'id' => $this->id,
                ]
            );
        }

        return [
            'token' => $this->token,
        ];
    }
}
