<?php

namespace Citsk\Library;

use Citsk\Exceptions\DatabaseException;

class ServiceAPI extends MySQLHelper
{

    /**
     * @param string $src
     *
     * @return void
     */
    public function loadReferencesFromFile(string $src): array
    {

        $csvData = file_get_contents($src);
        $lines   = explode(PHP_EOL, $csvData);
        $result  = [];

        foreach ($lines as $line) {
            $result[] = str_getcsv($line, ",");
        }

        unset($result[0]);

        return $result;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function addReference(array $data): void
    {

        $insert = [
            "label"         => $data[0],
            "city"          => $data[1],
            "district"      => $data[2],
            "tax_id"        => $data[3],
            "government_id" => $data[4],
        ];

        $this->setDbTable("refs")->add($insert);
    }

    /**
     * @param int $responsibleId
     * @param array $params
     *
     * @return void
     */
    public function createUser(int $responsibleId, array $params): void
    {

        $accessList         = "";
        $passwordHash       = Shared::getRandomPasswordHash();
        $passwordDoubleHash = password_hash($passwordHash, PASSWORD_DEFAULT);

        $insert = [
            "login"          => "'{$params['login']}'",
            "password"       => "'$passwordDoubleHash'",
            "responsible_id" => $responsibleId,
            "role"           => $params['user_role'] ?? 2,
        ];

        $userId = $this->setDbTable("users")
            ->skipArgs()
            ->add($insert)
            ->getInsertedId();

        if (!$userId) {
            throw new DatabaseException("Create user failed");
        }

        $accessList = "login: {$params['login']}, password: $passwordHash \n";

        $this->writeToFile($accessList);
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function createResponsible(array $params): int
    {

        $responsibleId = $this->setDbTable("responsibles")
            ->add($params)
            ->getInsertedId();

        if (!$responsibleId) {
            throw new DatabaseException("Create responsible failed");
        }

        return $responsibleId;
    }

    /**
     * @return void
     */
    private function writeToFile(string $inputData): void
    {

        $fileStream = fopen($_SERVER['DOCUMENT_ROOT'] . "/crypto-apps_access.txt", 'a');

        fwrite($fileStream, $inputData);
        fclose($fileStream);
    }

}
