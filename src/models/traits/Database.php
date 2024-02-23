<?php

namespace src\models\traits;

use PDOStatement;
use src\database\querybuilders\QueryBuilderInterface;
use src\database\Database as DatabaseInterface;

trait Database
{
    protected DatabaseInterface $database;
    protected ?QueryBuilderInterface $queryBuilder = null;

    protected function getColumnType(PDOStatement $statement, array $data, string $key): ?string
    {
        $index = 0;
        foreach ($data as $columnName => $columnValue) {
            if ($columnName == $key) {
                return $statement->getColumnMeta($index)['native_type'];
            }

            $index++;
        }
    }

    protected function castFromDatabaseToModel(string $nativeType, mixed $value): mixed
    {
        if (!$this->queryBuilder) {
            $this->queryBuilder = $this->database->getQueryBuilder();
        }

        return $this->queryBuilder->castFromDatabase($nativeType, $value);
    }

    protected function castFromModelToDatabase(mixed $value): mixed
    {
        if (!$this->queryBuilder) {
            $this->queryBuilder = $this->database->getQueryBuilder();
        }

        return $this->queryBuilder->castToDatabase($value);
    }
}
