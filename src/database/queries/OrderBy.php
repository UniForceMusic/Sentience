<?php

namespace src\database\queries;

use src\database\objects\OrderBy as OrderbyObject;

trait OrderBy
{
    protected $orderBys = [];

    public function orderBy(string $columnName, string $orderType): static
    {
        $this->orderBys[] = new OrderbyObject(
            $columnName,
            $orderType
        );

        return $this;
    }
}
