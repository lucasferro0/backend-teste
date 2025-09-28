<?php

namespace App\UseCases\Account;

use Throwable;
use App\UseCases\BaseUseCase;
use App\Repositories\Account\UpdateStatus as RepositoryUpdateStatus;
use App\Integrations\Banking\Account\UpdateStatus as IntegrationUpdateStatus;

class Active extends BaseUseCase
{
    /**
     * Id do usuário
     *
     * @var string
     */
    protected string $userId;

    /**
     * Conta
     *
     * @var array
     */
    protected array $account;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Atualiza no banco de dados
     *
     * @return void
     */
    protected function updateDatabase(): void
    {
        /**
         * [ANÁLISE]
         *
         * - Causará um erro na constraint check pois tem um check status in ('ACTIVE', 'BLOCK') e está sendo passado active tudo minúsculo
         */
        (new RepositoryUpdateStatus($this->userId, 'active'))->handle();
    }

    /**
     * Atualiza a conta
     *
     * @return void
     */
    protected function updateStatus(): void
    {
        $this->account = (new IntegrationUpdateStatus($this->userId, 'active'))->handle();
    }

    /**
     * [ANÁLISE]
     *
     * - Faltou ter controle de transação no banco de dados
     *   Caso a api do banco devolva status code de erro, deve ser feito um DB::rollBack();
     *
     * - A conta no nosso banco de dados é buscada várias vezes nesse método
     *   A primeira vez é buscada aqui $this->updateDatabase();
     *   A segunda vez é buscada aqui $this->updateStatus();
     *   Isso causa problema de performance.
     */
    public function handle(): void
    {
        try {
            $this->updateDatabase();
            $this->updateStatus();
        } catch (Throwable $th) {
            $this->defaultErrorHandling(
                $th,
                [
                    'userId' => $this->userId,
                ]
            );
        }
    }
}
