<?php

namespace src\database\queries;

use PDOStatement;

trait Delete
{
    public function delete(): PDOStatement
    {
        $queryBuilder = $this->database->getQueryBuilder();
        [$query, $params] = $queryBuilder->delete(
            $this->table,
            $this->where,
            $this->orderBys,
            $this->limit,
        );

        return $this->database->exec($query, $params);
    }

    public function deleteSql(): string
    {
        $queryBuilder = $this->database->getQueryBuilder();
        [$query, $params] = $queryBuilder->delete(
            $this->table,
            $this->where,
            $this->orderBys,
            $this->limit,
        );

        return $query;
    }
}
