<?php

namespace src\database\queries;

use src\database\objects\Where as WhereDTO;

trait Where
{
    protected array $where = [];

    public function where(string $key, string $comparator, bool|int|float|string $value, bool $escapeKey = true): static
    {
        $this->where[] = new WhereDTO($key, $comparator, $value, $escapeKey);

        return $this;
    }

    public function and (): static
    {
        $this->where[] = 'AND';

        return $this;
    }

    public function or (): static
    {
        $this->where[] = 'OR';

        return $this;
    }
}

?>