<?php

namespace Citsk\Models\Structure;

class ReferenceStructure extends StructureBase
{

    /**
     * @param array $rows
     *
     * @return void
     */
    public function FullMeta(array $rows): void
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
    public function ShortMeta(array $rows): void
    {
        foreach ($rows as $row) {
            $this->structure[] = [
                "id"    => intval($row['id']),
                "taxId" => intval($row['tax_id']),
                "label" => $row['label'],
                'notes' => $row['notes'] ?? [],
            ];
        }
    }

    /**
     * @param array $rows
     *
     * @return void
     */
    public function TheNote(array $rows): void
    {
        foreach ($rows as $row) {
            $this->structure[] = [
                "id"      => intval($row['id']),
                "created" => $row['created'],
                "refId"   => intval($row['ref_id']),
                "created" => $row['created'],
                "note"    => $row['content'],
            ];
        }
    }
}
