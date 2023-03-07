<?php

namespace src\controllers;

class Controller
{
    public function __construct(...$serviceArgs)
    {
        foreach ($serviceArgs as $key => $value) {
            $this->$key = $value;
        }
    }
}

?>