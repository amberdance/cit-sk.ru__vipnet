<?php

namespace Citsk\Controllers;

use Citsk\Interfaces\Controllerable;
use Citsk\Interfaces\IController;
use Citsk\Models\Applist;
use Citsk\Models\Identity;

class ApplistController extends Controller implements Controllerable, IController
{

    /**
     * @var Applist
     */
    protected $model;

    /**
     * @var Identity
     */
    protected $user;

    public function initializeController(): void
    {
        $this->model = new Applist;
        $this->user  = new Identity;
    }

    /**
     * @return void
     */
    public function getList(): void
    {
        if (isset($_POST['isActive'])) {
            $this->checkAdminAccess();
        }

        $isActiveFilter = isset($_POST['isActive']) ? boolval($_POST['isActive']) : 1;
        $payload        = $this->model->getApplist(null, $isActiveFilter);
        $this->dataResponse($payload);
    }

    /**
     * @return void
     */
    public function getTrash(): void
    {
        $this->checkAdminAccess();
        $payload = $this->model->getApplist(null, 0);
        $this->dataResponse($payload);
    }

    /**
     * @return void
     */
    public function getLogs(): void
    {

        $this->checkAdminAccess();
        $payload = $this->model->getLogs();

        $this->dataResponse($payload);
    }

    /**
     * @return void
     */
    public function add(): void
    {
        if ((int) date('w') !== 5) {
            $this->errorResponse(108);
        }

        $this->model->checkIsReceptionDateExists($_POST['signatureTypeId'], $_POST['receptionDate']);
        $id = $this->model->addApplication($this->getBindingParams($_POST));
        $this->model->setLog($id, 1, "applist_log");
        $payload = $this->model->getApplist($id);
        $this->dataResponse($payload);
    }

    /**
     * @return void
     */
    public function update(): void
    {

        $payload    = $this->getBindingParams($_POST);
        $difference = $this->model->getApplicationDifference($_POST['id'], $payload);

        if ($difference) {
            $this->model->checkIsReceptionDateExists($_POST['signatureTypeId'], $_POST['receptionDate'], $_POST['id']);
            $this->model->updateApplicationHistory($_POST['id'], $difference);
            $this->model->updateApplication($_POST['id'], $payload);
            $this->model->setLog($_POST['id'], 2, "applist_log");

            $this->successResponse();
        }

        $this->successResponse();
    }

    /**
     * @return void
     */
    public function getHistory(): void
    {
        $this->setHTTPMethod("get");
        $payload = $this->model->getApplicationHistory($_GET['id']);
        $this->dataResponse($payload);
    }

    /**
     * @return void
     */
    public function remove(): void
    {

        $id = $_POST['id'];

        if (is_array($id)) {
            array_walk($id, function ($id) {
                isset($_POST['completelyRemove']) ? $this->model->deleteApplication($id) : $this->disableCallback($id);

            });
        } else {
            isset($_POST['completelyRemove']) ? $this->model->deleteApplication($id) : $this->disableCallback($id);
        }

        $this->successResponse();
    }

    /**
     * @return void
     */
    public function clearLogs(): void
    {
        $this->setHTTPMethod("get");
        $this->checkAdminAccess();
        $this->model->truncateTable("applist_log");
        $this->model->truncateTable("applist_history");
        $this->successResponse();
    }

    /**
     * @param int $id
     *
     * @return void
     */
    private function disableCallback(int $id): void
    {

        $update = [
            "is_active" => 0,
        ];

        $this->model->updateApplication($id, $update);
        $this->model->setLog($id, 3, "applist_log");

    }
}
