<?php

namespace App\UseCases\User;

use Throwable;
use App\UseCases\BaseUseCase;
use App\UseCases\Params\User\UpdateParams;
use App\Domains\User\Update as UpdateDomain;
use App\Repositories\User\Update as UpdateRepository;

/**
 * [ANÁLISE]
 *
 * - Falta de padronização no nome da camada use case
 *   Se o padrão adotado no projeto é de utilizar use case, é interessante manter um padrão que permita identificar facilmente e buscar facilmente os use cases.
 *   Dessa forma, um padrão como UserUpdateUseCase seria muito mais fácil de buscar, além de que teria o sufixo UseCase reforçando o padrão.
 */
class Update extends BaseUseCase
{
    /**
     * @var UpdateParams
     */
    protected UpdateParams $params;

    /**
     * Usuário
     *
     * @var array
     */
    protected array $user; // Tem variáveis como essa que são utilizadas no return do handle e outras não são utilizadas. Ao meu ver, isso é uma falta de padronização.

    /**
     * [ANÁLISE]
     *
     * - Um ponto interessante é que o service pattern se encaixaria no lugar do use case, melhoraria a reutilização de código.
     */

    public function __construct(
        UpdateParams $params
    ) {
        $this->params = $params;
    }

    /**
     * Valida o usuário
     *
     * @return UpdateDomain
     */
    protected function validateUser(): UpdateDomain
    {
        return (new UpdateDomain(
            $this->params->id,
            $this->params->companyId,
            $this->params->name,
            $this->params->email,
            $this->params->password,
            $this->params->type
        ))->handle();
    }

    /**
     * Modifica o usuário
     *
     * @param UpdateDomain $domain
     *
     * @return void
     */
    protected function updateUser(UpdateDomain $domain): void
    {
        $this->user = (new UpdateRepository($domain))->handle();
    }

    /**
     * Modifica um usuário
     */
    public function handle()
    {
        /**
         * [ANÁLISE]
         *
         * - Notei que há bastante try catch nos use cases
         *   Não é necessário reinventar a roda ...
         *   Podemos utilizar o error handler pattern que o laravel fornece para tratar todas as exceptions da aplicação.
         *
         * - Fora isso, notei que há várias chamadas para $this->defaultErrorHandling(), onde é criado log.
         *   Na aplicação do laravel, a responsabilidade de gravar log é do Handler, já tem muita coisa feita.
         *   E podemos utilizar diversos drivers para logs, ou podemos criar o nosso próprio driver.
         */
        try {
            $userDomain = $this->validateUser();
            $this->updateUser($userDomain);
        } catch (Throwable $th) {
            $this->defaultErrorHandling(
                $th,
                [
                    'params' => $this->params->toArray(),
                ]
            );
        }

        return $this->user;
    }
}
