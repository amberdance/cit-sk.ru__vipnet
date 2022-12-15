<?php

namespace App\Repositories;

use App\Http\Resources\BaseCollection;
use App\Interfaces\CrudRepositoryInterface;
use App\Interfaces\IResourceCollection;
use App\Models\Signature;
use Illuminate\Database\Eloquent\Model;

class SignatureRepository implements CrudRepositoryInterface
{

    public function findAll(): IResourceCollection
    {
        return new BaseCollection(Signature::select(["id", "label"])->get());
    }

    public function findByFilter(array $filter): IResourceCollection
    {
        return $this->findAll();
    }

    public function findById(int $id): Model
    {
        return Signature::findOrFail($id);
    }

    public function update(int $id, array $fields): Model
    {
        //
    }

    public function create(array $fields): Model
    {
        //
    }

    public function delete($id): int
    {
        return Signature::destroy($id);
    }

    public function massDelete(array $ids): int
    {
        return Signature::whereIn("id", $ids)->delete();
    }
}
