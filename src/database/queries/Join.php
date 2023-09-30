<?php

namespace src\database\queries;

use src\database\objects\Join as JoinObject;

trait Join
{
    protected array $joins = [];

    public function join(string $joinType, string $joinTable, string $parentTableColumnName, string $joinTableColumnName)
    {
        $this->joins[] = new JoinObject(
            $joinType,
            $joinTable,
            $parentTableColumnName,
            $joinTableColumnName
        );

        return $this;
    }
}
