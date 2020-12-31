<?php

namespace Citsk\Models\Structure;

class CommonStructure extends Structure
{

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
    public function sessionList(array $rows): void
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
