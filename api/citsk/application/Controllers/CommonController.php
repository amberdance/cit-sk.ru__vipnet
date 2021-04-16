<?php

namespace Citsk\Controllers;

use Citsk\Controllers\Controller;
use Citsk\Interfaces\Controllerable;
use Citsk\Models\CommonModel;
use Citsk\Models\Identity;

final class CommonController extends Controller implements Controllerable
{

    /**
     * @var CommonModel
     */
    protected $model;

    /**
     * @var Identity
     */
    protected $user;

    /**
     * @return void
     */
    public function initializeController(): void
    {
        $this->model = new CommonModel;
        $this->user  = new Identity;
    }

    /**
     * @return void
     */
    public function getSignatures(): void
    {
        $payload = $this->model->getSignatures();
        $this->dataResponse($payload);
    }

    /**
     * @return void
     */
    public function getSessions(): void
    {
        $this->checkAdminAccess();

        $payload = $this->model->getSessions();
        $this->dataResponse($payload);
    }

    /**
     * @return void
     */
    public function clearSessions(): void
    {
        $this->setHTTPMethod("get");
        $this->checkAdminAccess();
        $this->model->truncateTable("connections");
        $this->successResponse();
    }

    /**
     * @return void
     */
    public function getChoice(): void
    {
        $this->model->setDbTable('notFoundComponent');
        $this->model->skipArgs()->update(['visited' => 'visited + 1']);
        $payload = $this->model->select(['stayed', 'leaved', 'visited'])->getRows();
        $this->dataResponse($payload[0]);
    }

    /**
     * @return void
     */
    public function setChoice(): void
    {
        $field = null;

        if (isset($_GET['stayed'])) {
            $field = 'stayed';
        }

        if (isset($_GET['leaved'])) {
            $field = 'leaved';
        }

        $params = [
            $field => "$field + 1",
        ];

        $this->model->skipArgs()->setDbTable("notFoundComponent")->update($params);
    }
}
