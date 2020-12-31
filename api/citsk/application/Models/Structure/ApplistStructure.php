<?php

namespace Citsk\Models\Structure;

class ApplistStructure extends Structure
{

    /**
     * @param array $rows
     *
     * @return void
     */
    public function theApplist(array $rows): void
    {
        foreach ($rows as $row) {
            $this->structure[] = [
                "id"              => intval($row['id']),
                "personCount"     => intval($row['person_count']),
                "taxId"           => intval($row['tax_id']),
                "governmentId"    => intval($row['government_id']),
                "signatureTypeId" => intval($row['signature_id']),
                "referenceId"     => intval($row['ref_id']),
                "receptionDate"   => $row['reception_date'],
                "signatureType"   => $row['signature_label'],
                "label"           => $row['label'],
            ];
        }
    }

    /**
     * @param array $rows
     *
     * @return void
     */
    public function theLogList(array $rows): void
    {
        foreach ($rows as $row) {
            $this->structure[] = [
                "id"         => intval($row['id']),
                "applistId"  => intval($row['applist_id']),
                "created"    => $row['created'],
                "event"      => $row['event'],
                "name"       => $row['name'],
                "surname"    => $row['surname'],
                "patronymic" => $row['patronymic'],
            ];
        }
    }

    /**
     * @param array $rows
     *
     * @return void
     */
    public function history(array $rows): void
    {
        foreach ($rows as $row) {
            $this->structure[] = [
                "oldData" => $row['old_value'],
                "newData" => $row['new_value'],
            ];
        }
    }
}
