<?php

namespace src\httpclient;

use src\generic\KeyValuePair;

class Header extends KeyValuePair
{
    public function __construct(string $key, string $value)
    {
        parent::__construct($key, $value);
    }

    public function getHeaderString(): string
    {
        return sprintf(
            '%s: %s',
            stripNonAscii($this->key),
            stripNonAscii($this->value)
        );
    }
}

?>