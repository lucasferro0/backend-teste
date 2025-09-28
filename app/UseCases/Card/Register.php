<?php

namespace App\UseCases\Card;

use Throwable;
use App\UseCases\BaseUseCase;
use App\Repositories\Card\Create;
use App\Domains\Card\Register as RegisterDomain;
use App\Integrations\Banking\Card\Register as LinkCard;

class Register extends BaseUseCase
{
    /**
     * Id do usuário
     *
     * @var string
     */
    protected string $userId;

    /**
     * Id do cartão
     *
     * @var string
     */
    protected string $cardId;

    /**
     * PIN do cartão
     *
     * @var string
     */
    protected string $pin;

    /**
     * Cartão
     *
     * @var array
     */
    protected array $card;

    public function __construct(string $userId, string $pin, string $cardId)
    {
        $this->userId = $userId;
        $this->pin    = $pin;
        $this->cardId = $cardId;
    }

    /**
     * Checa se é possível registrar
     *
     * @return RegisterDomain
     */
    protected function checkIfCanRegister(): RegisterDomain
    {
        return (new RegisterDomain($this->userId, $this->pin, $this->cardId))->handle();
    }

    /**
     * Registra o cartão
     *
     * @return void
     */
    protected function register(RegisterDomain $domain): void
    {
        $this->card = (new LinkCard($domain))->handle();
    }

    /**
     * Registra no banco de dados
     *
     * @return void
     */
    protected function store(RegisterDomain $domain): void
    {
        (new Create($domain))->handle();
    }

    /**
     * [ANÁLISE]
     *
     * - Nesse método a ordem de execução correta seria checkIfCanRegister() -> store() -> register()
     *   Digo isso, pois não há necessidade de sempre mandar request criando o cartão lá e depois tentar criar o cartão aqui,
     *   sendo que pode ocorrer um erro depois de mandar o request ao criar o cartão no nosso banco de dados e dessa forma, o cartão ia ficar criado na api do banco
     *
     * - Além disso, deixando na ordem correta de execução mencionada em cima, também tem que usar controle de transações com DB::beginTransaction(),
     *   Dessa forma, conseguimos garantir que caso a api do banco devolva um response com status code de erro e não crie o cartão lá,
     *   nós podemos dar DB::rollBack() no cartão que criamos no nosso banco de dados.
     */
    public function handle(): array
    {
        try {
            $domain = $this->checkIfCanRegister();
            $this->register($domain);
            $this->store($domain);
        } catch (Throwable $th) {
            $this->defaultErrorHandling(
                $th,
                [
                    'userId' => $this->userId,
                    'pin'    => $this->pin,
                    'cardId' => $this->cardId,
                ]
            );
        }

        return $this->card; // Possível vulnerabilidade, visto que está devolvendo o que a api do banco devolve para a nossa api
    }
}
