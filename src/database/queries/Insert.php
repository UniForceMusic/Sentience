<?php

namespace src\database\queries;

use PDOStatement;

trait Insert
{
    public function insert(): PDOStatement
    {
        $queryBuilder = $this->database->getQueryBuilder();
        [$query, $params] = $queryBuilder->insert(
            $this->table,
            $this->values,
        );

        return $this->database->exec($query, $params);
    }

    public function insertWithLastId(string $primaryKey = null): int
    {
        $this->insert();

        return $this->database->getLastInsertedId($primaryKey);
    }

    public function insertSql(): string
    {
        $queryBuilder = $this->database->getQueryBuilder();
        [$query, $params] = $queryBuilder->insert(
            $this->table,
            $this->values,
        );

        return $query;
    }
}
