<?php

namespace src\httpclient;

use src\generic\KeyValuePair;

class QueryParameter extends KeyValuePair
{
    public function getQueryString(): string
    {
        return sprintf(
            '%s=%s',
            urlencode($this->key),
            urlencode($this->value)
        );
    }
}

?>