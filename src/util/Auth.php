<?php

namespace src\util;

class Auth
{
    public static function getBasicAuthHeader(string $username, string $password): string
    {
        $userPassString = sprintf(
            '%s:%s',
            $username,
            $password
        );

        return sprintf(
            'Basic %s',
            base64_encode($userPassString)
        );
    }
}

?>