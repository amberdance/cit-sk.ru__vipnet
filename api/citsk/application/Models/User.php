<?php

namespace Citsk\Models;

use Citsk\Library\Shared;
use stdClass;

final class User extends DatabaseModel
{

    /**
     * @var Identity
     */
    public $identity;

    public function __construct()
    {
        $this->identity = new Identity;
        parent::__construct();
    }

    /**
     * @return stdClass|null
     */
    public function getUserData(string $login): ?stdClass
    {

        $select = [
            "id",
            "password",
            "role",
            "responsible_id",
        ];

        $filter = [
            "login"      => $login,
            "is_blocked" => 0,
        ];

        $userData = $this->setDbTable("users")
            ->select($select, $filter)
            ->getRow(5);

        return $userData;

    }
    /**
     * @return void
     */
    public function setConnection(): void
    {
        $ipAddress = Shared::getIpAdress();

        $fields = [
            "created"    => "CURRENT_TIMESTAMP()",
            "user_id"    => $this->identity->userId,
            "ip_address" => "'$ipAddress'",
        ];

        $this->setDbTable("connections")->skipArgs()->add($fields);
    }

    public function unsetConnection(): void
    {

        $filter = [
            $this->identity->userId,
        ];

        $this->setDbTable("connections")->delete($filter);
    }
}
