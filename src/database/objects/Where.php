<?php

namespace src\database\objects;

class Where
{
    public string $key;
    public string $comparator;
    public bool $escapeKey;

    public function __construct(string $key, string $comparator, bool $escapeKey)
    {
        $this->key = $key;
        $this->comparator = $comparator;
        $this->escapeKey = $escapeKey;
    }
}