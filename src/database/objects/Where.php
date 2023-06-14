<?php

namespace src\database\objects;

class Where
{
    public string $key;
    public string $comparator;

    public function __construct(string $key, string $comparator)
    {
        $this->key = $key;
        $this->comparator = $comparator;
    }
}