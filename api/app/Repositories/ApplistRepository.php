<?php

namespace App\Repositories;

use App\Http\Resources\BaseCollection;
use App\Interfaces\CrudRepositoryInterface;
use App\Interfaces\IResourceCollection;
use App\Interfaces\ResourceModel;
use App\Models\Application\Application;

class ApplistRepository implements CrudRepositoryInterface
{

    public function findAll(): IResourceCollection
    {
        return new BaseCollection(Application::with(["organization", "signature"])->get());
    }

    public function findByFilter(array $filter): IResourceCollection
    {
        $query = Application::with(["organization", "signature"]);

        return new BaseCollection(isset($params['per_page']) ? $query->paginate($params['per_page']) : $query->get());
    }

    public function findById(int $id): ResourceModel
    {
        return Application::findOrFail($id);
    }

    public function update(int $id, array $fields): ResourceModel
    {

    }

    public function create(array $fields): ResourceModel
    {

    }

    public function delete($id): int
    {

    }
}
