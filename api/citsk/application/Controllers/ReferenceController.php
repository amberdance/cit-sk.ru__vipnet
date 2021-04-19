<?php

namespace Citsk\Controllers;

use Citsk\Interfaces\Controllerable;
use Citsk\Interfaces\IController;
use Citsk\Models\Identity;
use Citsk\Models\Reference;

class ReferenceController extends Controller implements Controllerable, IController
{
    /**
     * @var Reference
     */
    protected $model;

    /**
     * @var Identity
     */
    protected $user;

    public function initializeController(): void
    {
        $this->model = new Reference;
        $this->user  = new Identity;
    }

    /**
     * @return void
     */
    public function getList(): void
    {
        $payload = isset($_POST['full'])
        ? $this->model->getReferences()
        : $this->model->getReferences(null, false);

        $this->dataResponse($payload);
    }

    /**
     * @return void
     */
    public function searchItem(): void
    {
        $payload = $this->model->searchReference($_GET['keyword']);
        $this->dataResponse($payload);
    }

    /**
     * @return void
     */
    public function add(): void
    {
        $this->checkAdminAccess();

        $id = $this->model->addReference($this->getBindingParams());

        if ($_POST['note']) {
            $this->addNote($id);
            $this->dataResponse($this->model->getReferences($id));
        }

        $this->successResponse(['id' => $id]);

    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function addNote(int $id): void
    {

        $params = [
            "ref_id"  => $id,
            "content" => $_POST['note'],
        ];

        $this->model->addReferenceNote($params);
    }

    /**
     * @return void
     */
    public function update(): void
    {

        $this->checkAdminAccess();
        $this->model->updateReference($_POST['id'], $this->getBindingParams());

        if ($_POST['note']) {
            $this->addNote($_POST['id']);
            $this->dataResponse($this->model->getReferences($_POST['id']));
        }

        $this->successResponse();
    }

    /**
     * @return void
     */
    public function updateNote(): void
    {

        $this->checkAdminAccess();

        $params = [
            "content" => $_POST['note'],
        ];

        $this->model->updateNote($_POST['id'], $params);
        $this->successResponse();
    }

    public function remove(): void
    {
        $id = $_POST['id'];

        if (is_array($id)) {
            array_walk($id, function ($id) {
                $this->model->removeItem($id);
            });
        } else {
            $this->model->removeItem($id);
        }

        $this->successResponse();
    }

    /**
     * @return void
     */
    public function removeNote(): void
    {

        $this->model->removeItem($_POST['id'], "refs_note");
        $this->successResponse();
    }
}
