<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginationResource extends JsonResource
{

    /**
     * @param mixed $request
     *
     * @return array
     */
    public function toArray($request): array
    {

        return [
            'total'         => $this->total(),
            'count'         => $this->count(),
            'per_page'      => $this->perPage(),
            'current_page'  => $this->currentPage(),
            'total_pages'   => $this->lastPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'last_page_url' => $this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
        ];
    }
}
