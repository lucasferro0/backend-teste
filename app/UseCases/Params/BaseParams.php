<?php

namespace App\UseCases\Params;

abstract class BaseParams
{
    /**
     * [ANÁLISE]
     *
     * - Reforço o ponto de que não há necessidade desse método mágino
     */



    /**
     * Obter uma propriedade da classe
     *
     * @param  string  $prop
     *
     * @return mixed
     */
    public function __get(string $prop): mixed
    {
        return $this->{$prop};
    }

    /**
     * Array de propriedades
     *
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
