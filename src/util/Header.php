<?php

namespace src\util;

class Header
{
    public static function serveFile()
    {
        header('Content-Transfer-Encoding: binary');
    }

    public static function contentLength(string $content)
    {
        header(sprintf('Content-Length: %s', strlen($content)));
    }
}
