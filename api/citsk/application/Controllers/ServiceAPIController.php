<?php
namespace Citsk\Controllers;

use Citsk\Controllers\Controller;
use Citsk\Interfaces\Controllerable;
use Citsk\Library\Identity;
use Citsk\Library\ServiceAPI;

class ServiceAPIController extends Controller implements Controllerable
{

    /**
     * @var ServiceAPI
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

        $this->user = new Identity;
        $this->checkAdminAccess();

        $this->model = new ServiceAPI;
    }

    /**
     * @return void
     */
    public function createUser(): void
    {
        $userList = [
            "user1",
            "user2",
            "user3",
            "user4",
        ];

        array_walk($userList, function ($login) {
            $this->createUserCallback($login);
        });

        $this->successResponse();
    }

    /**
     * @param mixed $login
     *
     * @return void
     */
    private function createUserCallback($login): void
    {
        $responsibleParams = [
            "name"       => $login,
            "patronymic" => "пользователь",
            "surname"    => "пользователь",
        ];

        $userParams = [
            "login" => $login,
        ];

        $responsibleId = $this->model->createResponsible($responsibleParams);
        $this->model->createUser($responsibleId, $userParams);

    }

    public function loadReferences(): void
    {
        set_time_limit(333);

        $this->model->setDbTable("refs");
        $this->model->customQuery("truncate refs");
        $this->model->customQuery("alter table refs AUTO_INCREMENT = 1");

        $data = $this->model->loadReferencesFromFile($_SERVER['DOCUMENT_ROOT'] . "/template.csv");

        array_walk($data, function ($key) {
            $this->model->addReference($key);
        });
    }
}
