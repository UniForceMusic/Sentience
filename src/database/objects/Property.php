<?php

namespace src\database\objects;

class Property
{
    public string $name;
    public string $type;
    public bool $allowsNull;

    public function __construct(string $name, string $type, bool $allowsNull)
    {
        $this->name = $name;
        $this->type = $type;
        $this->allowsNull = $allowsNull;
    }
}
