<?php
namespace Citsk\Models;

use Citsk\Models\DatabaseModel;
use Citsk\Models\Structure\CommonStructure;

class CommonModel extends DatabaseModel
{

    /**
     * @var string|null
     */
    private $dbTable = null;

    /**
     * @param string|null $dbTable
     */
    public function __construct(?string $dbTable = null)
    {

        if ($dbTable) {
            $this->dbTable = $dbTable;
        }

        parent::__construct();

    }

    /**
     * @param string $dbTable
     *
     * @return void
     */
    public function truncateTable(string $dbTable): void
    {
        $this->customQuery("TRUNCATE $dbTable");
    }

    /**
     * @return CommonStructure
     */
    public function getSignatures(): CommonStructure
    {
        $select = [
            "id",
            "label",
        ];

        $rows = $this->setDbTable("signature_type")
            ->select($select)
            ->getRows();

        return new CommonStructure($rows);
    }

    /**
     * @param int $id
     * @param string $eventId
     * @param string $dbTable
     *
     * @return void
     */
    public function setLog(int $id, int $eventId, string $dbTable = "applist_log"): void
    {

        global $USER;

        $insert = [
            "entity_id" => $id,
            "event_id"  => $eventId,
            "user_id"   => $USER->userId,
        ];

        if ($eventId == 2) {
            $filter = [
                "entity_id" => $id,
                "event_id"  => 2,
            ];

            $this->setDbTable($dbTable)->delete($filter);
        }

        $this->setDbTable($dbTable)->add($insert);
    }

    /**
     * @param int $id
     * @param string|null $dbTable
     *
     * @return void
     */
    public function removeItem(int $id, string $dbTable = null): void
    {
        $filter = [
            "id" => $id,
        ];

        $this->setDbTable($dbTable ?? $this->dbTable)->delete($filter);
    }

    /**
     * @param int $id
     * @param string $dbTable
     *
     * @return string|null
     */
    public function getTableFieldLabelById(int $id, string $dbTable): ?string
    {

        $select = "label";

        $filter = [
            "id" => $id,
        ];

        return $this->setDbTable($dbTable)->select($select, $filter)->getColumn();
    }

    /**
     * @return CommonStructure
     */
    public function getSessions(): CommonStructure
    {
        $select = [
            "connection.id",
            "connection.created",
            "connection.ip_address" => "ip",
            "user.login",
        ];

        $join = [
            "users user" => "user.id = connection.user_id",
        ];

        $sort = [
            "connection.created" => "desc",
        ];

        $rows = $this->setDbTable("connections connection")
            ->select($select, null, $join)
            ->setSorting($sort)
            ->getRows();

        return new CommonStructure($rows, "SessionList");
    }
}
