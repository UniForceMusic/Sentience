<?php

namespace src\database\queries;

trait Model
{
    protected ?string $model = null;

    public function model(string $model): static
    {
        $this->model = $model;
        $this->table = $model::getTable();

        return $this;
    }
}
