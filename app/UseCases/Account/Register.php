<?php

namespace App\UseCases\Account;

use Throwable;
use App\UseCases\BaseUseCase;
use App\Repositories\User\Find;
use App\Repositories\Account\Create;
use App\Exceptions\InternalErrorException;
use App\Integrations\Banking\Account\Create as BankingCreate;

class Register extends BaseUseCase
{
    /**
     * Id do usuário
     *
     * @var string
     */
    protected string $userId;

    /**
     * Id da empresa
     *
     * @var string
     */
    protected string $companyId;

    /**
     * Usuário
     *
     * @var array
     */
    protected array $user;

    /**
     * Conta
     *
     * @var array
     */
    protected array $account;

    public function __construct(string $userId, string $companyId)
    {
        $this->userId    = $userId;
        $this->companyId = $companyId;
    }

    /**
     * Encontra o usuário
     *
     * @return void
     */
    protected function findUser(): void
    {
        $user = (new Find($this->userId, $this->companyId))->handle();
        if (is_null($user)) { // Não está seguindo o padrão do restante do projeto que usa a camada de Domain para validar regra de negócio
            throw new InternalErrorException(
                'USER_NOT_FOUND',
                146001001
            );
        }

        $this->user = $user;
    }

    /**
     * Cria a conta
     *
     * @return void
     */
    protected function register(): void
    {
        $this->account = (new BankingCreate(
            $this->user['name'],
            $this->user['document_number'],
            $this->user['email']
        ))->handle();
    }

    /**
     * Registra no banco de dados
     *
     * @return void
     */
    protected function store(): void
    {
        (new Create($this->userId, $this->account['data']['id']))->handle();
    }


    /**
     * [ANÁLISE]
     *
     * - Nesse método a ordem de execução correta seria findUser() -> store() -> register()
     *   Digo isso, pois não há necessidade de sempre mandar request criando a conta lá e depois tentar criar a conta aqui,
     *   sendo que pode ocorrer um erro depois de mandar o request ao criar a conta no nosso banco de dados e dessa forma, a conta ia ficar criada na api do banco
     *
     * - Além disso, deixando na ordem correta de execução mencionada em cima, também tem que usar controle de transações com DB::beginTransaction(),
     *   Dessa forma, conseguimos garantir que caso a api do banco devolva um response com status code de erro e não crie a conta lá,
     *   nós podemos dar DB::rollBack() na conta que criamos no nosso banco de dados.
     */
    public function handle(): void
    {
        try {
            $this->findUser();
            $this->register();
            $this->store();
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
