<?php

namespace src\httpclient;

use src\generic\KeyValuePair;

class Cookie extends KeyValuePair
{
    public function __construct(string $key, string $value)
    {
        parent::__construct($key, $value);
    }

    public function getCookieString(): string
    {
        return sprintf(
            '%s=%s',
            replaceNonAsciiChars($this->key),
            replaceNonAsciiChars($this->value)
        );
    }
}
