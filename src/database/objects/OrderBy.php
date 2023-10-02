<?php

namespace src\database\objects;

class OrderBy
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    public string $columnName;
    public string $orderType;

    public function __construct(string $columnName, string $orderType)
    {
        $this->columnName = $columnName;
        $this->orderType = $orderType;
    }
}
