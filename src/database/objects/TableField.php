<?php

namespace src\database\objects;

class TableField
{
    public string $type;
    public bool $allowsNull;

    public function __construct(string $type, bool $allowsNull)
    {
        $this->type = $type;
        $this->allowsNull = $allowsNull;
    }
}
