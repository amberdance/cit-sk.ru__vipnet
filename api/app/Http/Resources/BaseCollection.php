<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCollection extends ResourceCollection
{

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Database\Eloquent\Collection
     */
    public function toArray($request)
    {

        $result = [];

        if ($this->resource instanceof Paginator) {
            $result = [
                'items'      => $this->collection,
                'pagination' => new PaginationResource($this),
            ];
        } else {
            $result = $this->collection;
        }

        return $result;
    }
}
