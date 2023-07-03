<?php

namespace src\httpclient;

use src\generic\KeyValuePair;

class Header extends KeyValuePair
{
    public function getHeaderString(): string
    {
        return sprintf(
            '%s: %s',
            urlencode($this->key),
            urlencode($this->value)
        );
    }
}

?>