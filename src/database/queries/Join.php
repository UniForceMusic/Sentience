<?php

namespace src\database\queries;

use src\database\objects\Join as JoinObject;

trait Join
{
    protected array $joins = [];

    public function join(string $joinType, string $joinTable, string $relationTableColumnName, string $joinTableColumnName)
    {
        $this->joins[] = new JoinObject(
            $joinType,
            $joinTable,
            $relationTableColumnName,
            $joinTableColumnName
        );

        return $this;
    }
}
