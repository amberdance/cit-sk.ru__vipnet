<?php

namespace Citsk\Controllers;

use Citsk\Controllers\Controller;
use Citsk\Interfaces\Controllerable;
use Citsk\Models\CommonModel;
use Citsk\Models\Identity;

final class CommonController extends Controller implements Controllerable
{

    /**
     * @var Common
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
}
