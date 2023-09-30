<?php

namespace src\database\queries;

trait Columns
{
    protected array $columns = [];
    protected bool $escapeColumns = true;

    public function columns(array $columns = [], bool $escapeColumns = false): static
    {
        $this->columns = $columns;
        $this->escapeColumns = $escapeColumns;

        return $this;
    }
}
