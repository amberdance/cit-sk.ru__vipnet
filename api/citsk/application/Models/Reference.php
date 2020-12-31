<?php

namespace Citsk\Models;

use Citsk\Models\Structure\ReferenceStructure;

class Reference extends Common
{

    /**
     * @var string
     */
    private $dbTable;

    public function __construct()
    {
        $this->dbTable = "refs";
        parent::__construct($this->dbTable);
    }

    /**
     * @return ReferenceStructure
     */
    /**
     * @param int|null $refId
     * @param bool $isFullList
     *
     * @return ReferenceStructure
     */
    public function getReferences(?int $refId = null, $isFullList = true): ReferenceStructure
    {

        $result = [];

        $select = [
            "id",
            "tax_id",
            "government_id",
            "created",
            "label",
            "city",
            "district",
        ];

        $filter = null;

        if ($refId) {
            $filter = [
                "id" => $refId,
            ];
        }

        $references = $this->setDbTable($this->dbTable)
            ->getList($select, $filter)
            ->setLimit(50)
            ->getRows();

        foreach ($references as $key => $value) {
            $result[]              = $value;
            $result[$key]['notes'] = $this->getReferenceNote($value['id'])->structure;
        }

        $structureType = $isFullList ? "theFullList" : "theShortList";

        return new ReferenceStructure($result, $structureType);
    }

    /**
     * @return int
     */
    public function addReference(array $params): int
    {

        return $this->setDbTable($this->dbTable)
            ->add($this->getTableFields($params))
            ->getInsertedId();

    }

    /**
     * @param int $id
     * @param array $params
     *
     * @return void
     */
    public function updateReference(int $id, array $params): void
    {

        $this->setDbTable($this->dbTable)->update($this->getTableFields($params), ['id' => $id]);
    }

    /**
     * @param int $id
     * @param array $params
     *
     * @return void
     */
    public function updateNote(int $id, array $params): void
    {

        $this->setDbTable("refs_note")->update($params, ['id' => $id]);
    }

    /**
     * @param int $id
     * @param array $params
     *
     * @return void
     */
    public function addReferenceNote(array $params): void
    {

        $this->setDbTable("refs_note")->add($params);

    }

    /**
     * @param int $id
     *
     * @return ReferenceStructure
     */
    public function getReferenceNote(int $id): ReferenceStructure
    {
        $select = [
            "id",
            "ref_id",
            "created",
            "content",
        ];

        $filter = [
            "ref_id" => $id,
        ];

        $rows = $this->setDbTable("refs_note")
            ->getList($select, $filter)
            ->getRows();

        return new ReferenceStructure($rows, "theNote");
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function getTableFields(array $params): array
    {
        $fields = [
            "label"         => $params['label'],
            "city"          => $params['city'],
            "district"      => $params['district'],
            "tax_id"        => $params['tax_id'],
            "government_id" => $params['government_id'],
        ];

        return $fields;
    }
}
