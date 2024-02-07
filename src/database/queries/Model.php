<?php

namespace src\database\queries;

trait Model
{
    protected ?string $model = null;

    public function model(string $model): static
    {
        $this->model = $model;
        $this->table = $model::getTable();

        $queryBuilder = $this->database->getQueryBuilder();

        $columns = [];

        foreach ($model::getColumnNames() as $column) {
            $columns[] = $queryBuilder->getColumnWithNamespace($this->table, $column, true);
        }

        $this->columns($columns, false);

        return $this;
    }
}
