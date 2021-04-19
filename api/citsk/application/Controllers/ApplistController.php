<?php

namespace Citsk\Controllers;

use Citsk\Interfaces\Controllerable;
use Citsk\Interfaces\IController;
use Citsk\Models\Applist;
use Citsk\Models\Identity;
use Citsk\Models\Reference;

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

        $filter = [
            'is_active'      => isset($_POST['isActive']) ? boolval($_POST['isActive']) : 1,
            'reception_date' => $_POST['receptionDate'],
        ];

        $payload = $this->model->getApplist(null, $filter);
        $this->dataResponse($payload);
    }

    /**
     * @return void
     */
    public function getTrash(): void
    {

        $this->checkAdminAccess();
        $this->dataResponse($this->model->getApplist(null, ['is_active' => 0]));
    }

    /**
     * @return void
     */
    public function getLogs(): void
    {

        $this->checkAdminAccess();
        $this->dataResponse($this->model->getLogs());
    }

    /**
     * @return void
     */
    public function getHistory(): void
    {

        $this->setHTTPMethod("get");
        $this->dataResponse($this->model->getApplicationHistory($_GET['id']));
    }

    /**
     * @return void
     */
    public function add(): void
    {
        // if ((int) date('w') !== 5) {
        //     $this->errorResponse(108);
        // }

        $this->model->isReceptionDateExists($_POST['signatureTypeId'], $_POST['receptionDate']);

        if (!$_POST['referenceId']) {
            unset($_POST['referenceId']);
        }

        if (is_string($_POST['referenceId'])) {
            $referenceId = (new Reference)->addReference(['label' => $_POST['referenceId']]);
        }

        $params = isset($referenceId)
        ? array_merge($this->getBindingParams(), [
            'reference_id' => $referenceId ?? $_POST['referenceId'],
        ])
        : $this->getBindingParams();

        $id = $this->model->addApplication($params);
        $this->model->setLog($id, 1);

        $this->dataResponse($this->model->getApplist($id));
    }

    /**
     * @return void
     */
    public function update(): void
    {

        $payload    = $this->getBindingParams();
        $difference = $this->model->getApplicationDifference($_POST['id'], $payload);

        if ($difference) {
            $this->model->isReceptionDateExists($_POST['signatureTypeId'], $_POST['receptionDate'], $_POST['id']);
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
    public function remove(): void
    {

        $callback = function ($id) {
            isset($_POST['completelyRemove']) ? $this->model->deleteApplication($id) : $this->disableCallback($id);
        };

        is_array($_POST['id']) ? array_walk($_POST['id'], $callback) : $callback($_POST['id']);

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
