<?php

namespace src\database\objects;

class Where
{
    public string $key;
    public string $comparator;
    public mixed $value;
    public bool $escapeKey;

    public function __construct(string $key, string $comparator, null|bool|int|float|string|array $value, bool $escapeKey)
    {
        $this->key = $key;
        $this->comparator = $comparator;
        $this->value = $value;
        $this->escapeKey = $escapeKey;
    }
}
