<?php

namespace App\Repositories\Card;

use App\Models\Card;
use App\Repositories\BaseRepository;

class CanUseExternalId extends BaseRepository
{
    /**
     * Id externo
     *
     * @var string
     */
    protected string $externalId;

    /**
     * Setar a model do cartão
     *
     * @return void
     */
    public function setModel(): void
    {
        $this->model = Card::class;
    }

    public function __construct(string $externalId)
    {
        $this->externalId = $externalId;

        parent::__construct();
    }

    /**
     * [ANÁLISE]
     *
     * - Repostory com responsabilidade errada, visto que está realizando uma validação.
     *   A responsabilidade do repository é apenas de se comunicar com o banco de dados e ele não deve fazer nenhuma validação.
     *   De acordo com o padrão do projeto, a validação de regra de negócio tem que ficar na camada de Domain
     */
    public function handle(): bool
    {
        $user = $this->builder
            ->where('external_id', $this->externalId)
            ->first();

        return is_null($user);
    }
}
