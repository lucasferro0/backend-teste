<?php

namespace App\Domains\Company;

use App\Domains\BaseDomain;

/**
 * [ANÁLISE]
 *
 * - Se nenhuma validação é necessária, então não tem motivo para essa classe existir.
 *   Isso prejudica desempenho e ainda deixa código morto no projeto
 */
class Update extends BaseDomain
{
    /**
     * Id da empresa
     *
     * @var string
     */
    protected string $id;

    /**
     * Nome
     *
     * @var string
     */
    protected string $name;

    public function __construct(string $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }

    /**
     * Checa se é possível modificar a empresa
     *
     * @return self
     */
    public function handle(): self
    {
        // Nenhuma validação necessária

        return $this;
    }
}
