<?php

namespace App\Interfaces;

use App\Interfaces\ResourceModel;

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
     * @return ResourceModel
     */
    public function findById(int $id): ResourceModel;
}
