<?php

namespace src\database\objects;

use DateTime;

class Where
{
    public string $key;
    public string $comparator;
    public mixed $value;
    public bool $escapeKey;

    public function __construct(string $key, string $comparator, bool|int|float|string $value, bool $escapeKey)
    {
        $this->key = $key;
        $this->comparator = $comparator;
        $this->value = $value;
        $this->escapeKey = $escapeKey;
    }
}