<?php

namespace src\database\queries;

use PDO;
use PDOStatement;
use src\exceptions\ModelException;
use src\models\Model as DatabaseModel;

trait Select
{
    public function select(): PDOStatement
    {
        $queryBuilder = $this->database->getQueryBuilder();
        [$query, $params] = $queryBuilder->select(
            $this->table,
            $this->columns,
            $this->escapeColumns,
            $this->joins,
            $this->where,
            $this->orderBys,
            $this->limit,
            $this->offset,
        );

        return $this->database->exec($query, $params);
    }

    public function selectAssoc(): ?array
    {
        $statement = $this->select();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function selectAssocs(): ?array
    {
        $statement = $this->select();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectModel(): ?DatabaseModel
    {
        if (!$this->model) {
            throw new ModelException('no model supplied');
        }

        $statement = $this->select();

        if ($statement->rowCount() < 1) {
            return null;
        }

        $assoc = $statement->fetch(PDO::FETCH_ASSOC);

        $model = new $this->model($this->database);
        return $model->hydrateByAssoc($statement, $assoc);
    }

    public function selectModels(): array
    {
        if (!$this->model) {
            throw new ModelException('no model supplied');
        }

        $statement = $this->select();

        if ($statement->rowCount() < 1) {
            return [];
        }

        $assocs = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            function (array $assoc) use ($statement): DatabaseModel {
                $model = new $this->model($this->database);
                return $model->hydrateByAssoc($statement, $assoc);
            },
            $assocs
        );
    }

    public function count(): int
    {
        $statement = $this->select();

        return $statement->rowCount();
    }

    public function exists(): bool
    {
        $count = $this->count();

        return ($count > 0);
    }

    public function selectSql(): string
    {
        $queryBuilder = $this->database->getQueryBuilder();
        [$query, $params] = $queryBuilder->select(
            $this->table,
            $this->columns,
            $this->escapeColumns,
            $this->joins,
            $this->where,
            $this->orderBys,
            $this->limit,
            $this->offset,
        );

        return $query;
    }
}
