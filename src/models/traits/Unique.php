<?php

namespace src\models\traits;

use src\database\queries\Query;

trait Unique
{
    protected array $unique = [];

    public function checkUnique(): bool
    {
        $uniqueProperties = [];

        foreach ($this->unique as $property) {
            $uniqueProperties[$property] = $this->columns[$property];
        }

        $query = $this->database->query()
            ->table($this->table)
            ->columns(array_values($uniqueProperties));

        foreach ($uniqueProperties as $property => $column) {
            $query = $query->where(
                $column,
                Query::EQUALS,
                $this->{$property}
            );
        }

        return $query->exists();
    }
}
