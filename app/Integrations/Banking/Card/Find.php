<?php

namespace App\Integrations\Banking\Card;

use App\Integrations\Banking\Gateway;
use App\Repositories\Account\FindByUser;
use App\Exceptions\InternalErrorException;

class Find extends Gateway
{
    /**
     * Id externo da conta
     *
     * @var string
     */
    protected string $externalAccountId;

    /**
     * Id do usuário
     *
     * @var string
     */
    protected string $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * [ANÁLISE]
     *
     * - Fora do padrão do projeto, de acordo com o padrão do Integration proposto, não é responsabilidade dele chamar um Repository
     *   O Integration deve apenas mandar request
     */
    protected function findAccountData(): void
    {
        $account = (new FindByUser($this->userId))->handle();

        if (is_null($account)) {
            throw new InternalErrorException(
                'ACCOUNT_NOT_FOUND',
                161001001
            );
        }

        $this->externalAccountId = $account['external_id'];
    }

    /**
     * Constroi a url da request
     *
     * @return string
     */
    protected function requestUrl(): string
    {
        return "account/$this->externalAccountId/card";
    }

    /**
     * [ANÁLISE]
     *
     * - O método $this->findAccountData();
     *   Temos uma consulta apenas para pegar o external_id. Além de que a lógica dessa consulta de cartão está errada.
     *   Deveria ser devolvido os dados do banco de dados e não os dados do response body da api do banco.
     *   Caso fosse necessário ter algum dado da api do banco, ele poderia ser mergeado com os dados do nosso banco de dados
     */
    public function handle(): array
    {
        $this->findAccountData();

        $url = $this->requestUrl();

        $request = $this->sendRequest(
            method: 'get',
            url:    $url,
            action: 'FIND_CARD',
            params: []
        );

        return $this->formatDetailsResponse($request);
    }
}
