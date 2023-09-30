<?php

namespace src\database\objects;

class Join
{
    public const LEFT_JOIN = 'LEFT JOIN';
    public const RIGHT_JOIN = 'RIGHT JOIN';
    public const INNER_JOIN = 'INNER JOIN';
    public const OUTER_JOIN = 'FULL OUTER JOIN';

    public string $joinType;
    public string $joinTable;
    public string $relationTableColumnName;
    public string $joinTableColumnName;

    public function __construct(string $type, string $joinTable, string $relationTableColumnName, string $joinTableColumnName)
    {
        $this->joinType = $type;
        $this->joinTable = $joinTable;
        $this->relationTableColumnName = $relationTableColumnName;
        $this->joinTableColumnName = $joinTableColumnName;
    }
}
