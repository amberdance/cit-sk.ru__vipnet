<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface CrudRepositoryInterface
{

    /**
     * @param array $fields
     *
     * @return Model
     */
    public function create(array $fields): Model;

    /**
     * @param int $id
     * @param array $fields
     *
     * @return Model
     */
    public function update(int $id, array $fields): Model;

    /**
     * @param int $id
     *
     * @return int
     */
    public function delete($id): int;

    /**
     * @param array $ids
     *
     * @return int
     */
    public function massDelete(array $ids): int;

    /**
     * @return IResourceCollection
     */
    public function findAll(): IResourceCollection;

    /**
     * @param array $filter
     *
     * @return IResourceCollection
     */
    public function findByFilter(array $filter): IResourceCollection;

    /**
     * @param int $id
     *
     * @return Model
     */
    public function findById(int $id): Model;
}
