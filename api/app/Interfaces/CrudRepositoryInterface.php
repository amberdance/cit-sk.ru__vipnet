<?php

namespace App\Interfaces;

use App\Interfaces\ResourceModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CrudRepositoryInterface
{

    /**
     * @param array $fields
     *
     * @return ResourceModel
     */
    public function create(array $fields): ResourceModel;

    /**
     * @param int $id
     * @param array $fields
     *
     * @return ResourceModel
     */
    public function update(int $id, array $fields): ResourceModel;

    /**
     * @param int $id
     *
     * @return int
     */
    public function delete($id): int;

    /**
     * @return Collection
     */
    public function findAll(): Collection;

    /**
     * @param array $filter
     *
     * @return Collection
     */
    public function findByFilter(array $filter): Collection;

    /**
     * @param int $id
     *
     * @return ResourceModel
     */
    public function findById(int $id): ResourceModel;
}
