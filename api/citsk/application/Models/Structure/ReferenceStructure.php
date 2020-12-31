<?php

namespace Citsk\Models\Structure;

class ReferenceStructure extends Structure
{

    /**
     * @param array $rows
     *
     * @return void
     */
    public function theFullList(array $rows): void
    {

        foreach ($rows as $row) {
            $this->structure[] = [
                "id"           => intval($row['id']),
                "taxId"        => $row['tax_id'],
                "governmentId" => $row['government_id'],
                "created"      => $row['created'],
                "label"        => $row['label'],
                "city"         => $row['city'],
                "district"     => $row['district'],
                "notes"        => $row['notes'],
            ];
        }
    }

    /**
     * @param array $rows
     *
     * @return void
     */
    public function theShortList(array $rows): void
    {
        foreach ($rows as $row) {
            $this->structure[] = [
                "id"    => intval($row['id']),
                "taxId" => intval($row['tax_id']),
                "label" => $row['label'],
            ];
        }
    }

    /**
     * @param array $rows
     *
     * @return void
     */
    public function theNote(array $rows): void
    {
        foreach ($rows as $row) {
            $this->structure[] = [
                "id"      => intval($row['id']),
                "refId"   => intval($row['ref_id']),
                "created" => $row['created'],
                "note" => $row['content'],
            ];
        }
    }
}
