<?php

namespace App\Domains;

use App\Traits\Instancer;

abstract class BaseDomain
{
    use Instancer;

    /**
     * [ANÁLISE]
     *
     * - Não há sentido declarar esse magic method, para acessar variáveis protected.
     *   Dessa forma, está quebrando o princípio do encapsulamento da programação orientada a objetos.
     *   Se o objetivo é acessar propriedades de fora da classe, então elas devem estar public e aí esse método não teria utilidade.
     *   Além disso, usando esse método mágico perdemos o autocomplete do intelisense, dessa forma, dificulta ainda mais o desenvolvimento do dev.
     */

    /**
     * Obter uma propriedade da classe
     *
     * @param string $prop
     *
     * @return mixed
     */
    public function __get(string $prop): mixed
    {
        return $this->{$prop};
    }
}
