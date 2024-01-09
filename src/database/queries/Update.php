<?php

namespace src\database\queries;

use PDOStatement;

trait Update
{
    public function update(): PDOStatement
    {
        $queryBuilder = $this->database->getQueryBuilder();
        [$query, $params] = $queryBuilder->update(
            $this->table,
            $this->values,
            $this->where,
        );

        return $this->database->exec($query, $params);
    }

    public function updateSql(): string
    {
        $queryBuilder = $this->database->getQueryBuilder();
        [$query, $params] = $queryBuilder->update(
            $this->table,
            $this->values,
            $this->where,
        );

        return $query;
    }
}
