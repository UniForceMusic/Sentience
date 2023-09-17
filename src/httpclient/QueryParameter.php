<?php

namespace src\httpclient;

use src\generic\KeyValuePair;

class QueryParameter extends KeyValuePair
{
    public function __construct(string $key, string $value)
    {
        parent::__construct($key, $value);
    }

    public function getQueryString(): string
    {
        return sprintf(
            '%s=%s',
            urlencode($this->key),
            urlencode($this->value)
        );
    }
}
