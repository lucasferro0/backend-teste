<?php

namespace App\Paginators;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * [ANÁLISE]
 *
 * - Não acho necessário ter um Paginator próprio.
 *   Os Paginators que o laravel fornece, já atendem bastante às demandas.
 */
class Paginator extends LengthAwarePaginator
{
    /**
     * Create a new paginator instance.
     *
     * @param Collection $objects
     * @param int        $total
     * @param int        $perPage
     * @param int|null   $currentPage
     * @param array      $options (path, query, fragment, pageName)
     * @return void
     */
    public function __construct(
        Collection $objects,
        $total,
        $perPage,
        $currentPage = null,
        array $options = []
    ) {
        parent::__construct($objects, $total, $perPage, $currentPage, $options);
    }

    /**
     * [ANÁLISE]
     *
     * - Essa lógica para colocar null caso o array esteja vazio pode dificultar a vida do frontend.
     *   Pensando em um BFF, o ideal seria caso o array estivesse vazio, devolver o array vazio.
     *   Como estamos mostrando uma lista, facilita para o frontend.
     */
    public function toArray()
    {
        $array = parent::toArray();

        $array['to'] += 0;

        $array['data'] = null;
        if ($array['total'] > 0) {
            $array['data'] = [
                'objects' => $this->items->toArray() ?: null
            ];
        }

        return $array;
    }

    /**
     * Cria uma nova instancia de paginação a partir de um LengthAwarePaginator
     *
     * @param  LengthAwarePaginator $paginator
     * @param  string|null          $order
     *
     * @return void
     */
    public static function fromLengthAwarePaginator(LengthAwarePaginator $paginator)
    {
        $collection = $paginator->getCollection();

        return new self(
            $collection->values(),
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
            $paginator->getOptions()
        );
    }
}
