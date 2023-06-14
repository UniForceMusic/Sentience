<?php

namespace src\database\queries;

use src\database\objects\Where as WhereDTO;

trait Where
{
    protected array $whereConditions = [];
    protected array $whereValues = [];

    public function where(string $key, string $comparator, bool|int|float|string $value): static
    {
        $this->whereConditions[] = new WhereDTO($key, $comparator);
        $this->whereValues[] = $value;

        return $this;
    }

    public function and (): static
    {
        $this->whereConditions[] = 'AND';

        return $this;
    }

    public function or (): static
    {
        $this->whereConditions[] = 'OR';

        return $this;
    }
}

?>