<?php

namespace App\Http\Resources;

use App\Paginators\Paginator;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BasePaginatorCollection extends ResourceCollection
{
    public function __construct($resource, string $order = null) // Esse argumento $order pode dar erro, visto que é do tipo string e tem um valor default de null
    {
        $resource = Paginator::fromLengthAwarePaginator($resource);

        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
