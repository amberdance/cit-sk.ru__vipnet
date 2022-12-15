<?php

namespace App\Http\Resources\User;

use App\Interfaces\IResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection implements IResourceCollection
{

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Database\Eloquent\Collection
     */
    public function toArray($request)
    {

        return parent::toArray($request);

    }
}
