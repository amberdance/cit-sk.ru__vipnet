<?php

namespace Citsk\Models;

use Citsk\Exceptions\DatabaseException;
use Citsk\Models\Structure\ApplistStructure;

class Applist extends CommonModel
{

    /**
     * @var String
     */
    public $dbTable;

    public function __construct()
    {
        $this->dbTable = "applist";
        parent::__construct($this->dbTable);
    }

    /**
     * @param int|null $id
     * @param int $isActive
     *
     * @return ApplistStructure
     */
    public function getApplist(?int $id = null, $isActive = 1): ApplistStructure
    {

        $select = [
            "applist.id",
            "applist.created",
            "applist.person_count",
            "applist.note",
            "DATE_FORMAT(applist.reception_date, '%Y-%m-%d %H:%i')" => "reception_date",
            "signature.label"                                       => "signature_label",
            "signature.id"                                          => "signature_id",
            "ref.id"                                                => "ref_id",
            "ref.label",
            "ref.city",
            "ref.district",
            "ref.tax_id",
            "ref.government_id",
        ];

        $filter = [
            "is_active" => $isActive,
        ];

        $join = [
            "signature_type signature" => "signature.id = applist.signature_type_id",
            "refs ref"                 => "ref.id = applist.reference_id",
        ];

        $sort = [
            'applist.reception_date' => 'desc',
        ];

        if ($id) {
            $filter["applist.id"] = $id;
        }

        $rows = $this->setDbTable($this->dbTable)
            ->select($select, $filter, $join)
            ->setSorting($sort)
            ->getRows();

        return new ApplistStructure($rows, 'Applist');
    }

    /**
     * @return int
     */
    public function addApplication(array $params): int
    {

        return $this->setDbTable($this->dbTable)->add($params)->getInsertedId();
    }

    /**
     * @param int $signatureTypeId
     * @param string $receptionDate
     * @param int|null $id
     *
     * @return void
     */
    public function checkIsReceptionDateExists(int $signatureTypeId, string $receptionDate, int $id = null): void
    {

        $select = "id";

        $filter = [
            "reception_date"    => $receptionDate,
            "is_active"         => 1,
            "signature_type_id" => $signatureTypeId == 1 ? 1 : "!= 1",
        ];

        if ($id) {
            $filter['id'] = "!= $id";
        }

        $isExistsDuplicate = $this->setDbTable("applist")->select($select, $filter)->getRowCount();

        if (boolval($isExistsDuplicate)) {
            throw new DatabaseException('Duplicate entry', 102);
        }
    }

    /**
     * @param int $id
     * @param array $params
     *
     * @return void
     */
    public function updateApplication(int $id, array $params): void
    {

        $this->setDbTable($this->dbTable)->update($params, ['id' => $id]);
    }

    /**
     * @param int $id
     * @param array $incomingValues
     *
     * @return array
     */
    public function getApplicationDifference(int $id, array $incomingValues): array
    {
        $select = [
            "person_count",
            "reception_date",
            "signature_type_id",
            "reference_id",
        ];

        $filter = [
            "id" => $id,
        ];

        $incomingValues['reception_date'] .= ":00";

        $oldValues = $this->setDbTable("applist")
            ->select($select, $filter)
            ->getRows();

        unset($incomingValues['id']);
        ksort($incomingValues);
        ksort($oldValues[0]);

        $difference = array_diff_assoc($incomingValues, $oldValues[0]);

        if (empty($difference)) {
            return [];
        }

        return [
            "old_value" => $oldValues[0],
            "new_value" => $difference,
        ];
    }

    /**
     * @param int $id
     * @param array $difference
     *
     * @return void
     */
    public function updateApplicationHistory(int $id, array $difference): void
    {

        $insert = [
            "applist_id" => $id,
            "old_value"  => [],
            "new_value"  => [],
        ];

        $filter = [
            "applist_id" => $id,
        ];

        $propertyKeys = array_keys($difference['new_value']);

        foreach ($propertyKeys as $key) {
            $insert["old_value"][] = [
                "property" => $key,
                "value"    => $difference['old_value'][$key],
            ];

            $insert["new_value"][] = [
                "property" => $key,
                "value"    => $difference['new_value'][$key],
            ];
        }

        $insert['old_value'] = "'" . serialize($insert['old_value']) . "'";
        $insert['new_value'] = "'" . serialize($insert['new_value']) . "'";

        $this->setDbTable("applist_history")->skipArgs()->delete($filter)->add($insert);
    }

    /**
     * @param int|null $id
     *
     * @return array|null
     */
    public function getApplistTableFields(?int $id = null): ?array
    {
        $filter = null;

        if ($id) {
            $filter = [
                "id" => $id,
            ];
        }

        $rows = $this->setDbTable($this->dbTable)
            ->select(null, $filter)
            ->getRows();

        $rows[0]['applist_id'] = intval($rows[0]['id']);
        unset($rows[0]['id']);

        return $rows[0];
    }

    /**
     * @param int $id
     *
     * @return ApplistStructure
     */
    public function getApplicationHistory(int $id)
    {
        $select = [
            "old_value",
            "new_value",
        ];

        $filter = [
            "applist_id" => $id,
        ];

        $result = [];

        $rows = $this->setDbTable("applist_history")
            ->select($select, $filter)
            ->getRows()[0];

        $rows['old_value'] = unserialize($rows['old_value']);
        $rows['new_value'] = unserialize($rows['new_value']);

        foreach ($rows['old_value'] as $row) {
            $result['oldData'][] = [
                "property" => $row['property'],
                "value"    => $this->getPropertyValue($row),
            ];
        }

        foreach ($rows['new_value'] as $row) {
            $result['newData'][] = [
                "property" => $row['property'],
                "value"    => $this->getPropertyValue($row),
            ];
        }

        return $result;
    }

    /**
     * @return ApplistStructure
     */
    public function getLogs(): ApplistStructure
    {
        $select = [
            "log.created",
            "log.id",
            "log.entity_id"          => "applist_id",
            "event.label"            => "event",
            "user.id"                => "user_id",
            "responsible.id"         => "responsible_id",
            "responsible.surname"    => "surname",
            "responsible.patronymic" => "patronymic",
            "responsible.name"       => "name",

        ];

        $join = [
            "event event"              => "event.id = log.event_id",
            "applist applist"          => "applist.id = log.entity_id",
            "users user"               => "user.id = log.user_id",
            "responsibles responsible" => "responsible.id = user.responsible_id",
        ];

        $sort = [
            "log.created" => "desc",
        ];

        $rows = $this->setDbTable("applist_log log")
            ->select($select, null, $join)
            ->setSorting($sort)
            ->getRows();

        return new ApplistStructure($rows, "LogList");
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function deleteApplication(int $id): void
    {
        $filter = [
            "list.id" => $id,
        ];

        $fields = [
            "list",
            "log",
            "history",
        ];

        $join = [
            "applist_log log"         => "log.entity_id = list.id",
            "applist_history history" => "history.applist_id",
        ];

        $this->setDbTable("applist list")->deleteWithJoin($fields, $filter, $join);
    }

    /**
     * @param array $data
     *
     * @return string|null
     */
    private function getPropertyValue(array $data): ?string
    {

        $result = $data['value'];

        if ($data['property'] == 'reference_id') {
            $result = $this->getTableFieldLabelById($data['value'], "refs");
        };

        if ($data['property'] == 'signature_type_id') {
            $result = $this->getTableFieldLabelById($data['value'], "signature_type");
        };

        return $result;
    }

    /**
     * @param mixed $params
     *
     * @return array
     */
    private function getBindingParams($params): array
    {
        $result = [
            "person_count"      => $params['person_count'],
            "reception_date"    => $params['reception_date'],
            "signature_type_id" => $params["signature_type_id"],
        ];

        if ($params['reference_id']) {
            $params['reference_id'];
        }

        return $result;
    }
}
