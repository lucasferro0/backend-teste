<?php

namespace App\Integrations\Banking\Card;

use App\Integrations\Banking\Gateway;
use App\Exceptions\InternalErrorException;
use App\Domains\Card\Register as RegisterDomain;
use App\Repositories\Card\FindByUser as FindCardByUser;
use App\Repositories\Account\FindByUser as FindAccountByUser;

class Register extends Gateway
{
    /**
     * Id externo do cartão
     *
     * @var string
     */
    protected string $externalCardId;

    /**
     * Id externo da conta
     *
     * @var string
     */
    protected string $externalAccountId;

    /**
     * Dados necessários para o registro do cartão
     *
     * @var RegisterDomain
     */
    protected RegisterDomain $domain;

    public function __construct(RegisterDomain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * [ANÁLISE]
     *
     * - Fora do padrão do projeto, de acordo com o padrão do Integration proposto, não é responsabilidade dele chamar um Repository
     *   O Integration deve apenas mandar request
     */
    protected function findAccountData(): void
    {
        $account = (new FindAccountByUser($this->domain->userId))->handle();

        if (is_null($account)) {
            throw new InternalErrorException(
                'ACCOUNT_NOT_FOUND',
                161001001
            );
        }

        $this->externalAccountId = $account['external_id'];
    }

    /**
     * [ANÁLISE]
     *
     * - Método não utilizado findCardData() (código morto)
     *
     * - Fora do padrão do projeto, de acordo com o padrão do Integration proposto, não é responsabilidade dele chamar um Repository
     *   O Integration deve apenas mandar request
     */
    protected function findCardData(): void
    {
        $account = (new FindCardByUser($this->domain->userId))->handle();

        if (is_null($account)) {
            throw new InternalErrorException(
                'CARD_NOT_FOUND',
                149001001
            );
        }

        $this->externalCardId = $account['external_id'];
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
            method: 'post',
            url:    $url,
            action: 'REGISTER_CARD',
            params: [
                'pin' => $this->domain->pin,
                'id'  => $this->domain->cardId,
            ]
        );

        return $this->formatDetailsResponse($request);
    }
}
