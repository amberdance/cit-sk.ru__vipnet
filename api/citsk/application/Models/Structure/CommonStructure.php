<?php

namespace Citsk\Models\Structure;

class CommonStructure extends StructureBase
{

    /**
     * @param array $rows
     *
     * @return void
     */
    public function abstractStructure(array $rows): void
    {

        foreach ($rows as $row) {
            $this->structure[] = [
                "id"    => (int) $row['id'],
                "label" => $row['label'] ?? $row['value'] ?? $row['title'] ?? null,
            ];
        }
    }

    /**
     * @param array $rows
     *
     * @return void
     */
    public function SessionList(array $rows): void
    {

        foreach ($rows as $row) {
            $this->structure[] = [
                "id"      => intval($row['id']),
                "login"   => $row['login'],
                "created" => $row['created'],
                "ip"      => $row['ip'],
            ];
        }
    }
}
