<?php

define("SYNTAXREPORTING", true);
define("ERRORREPORTING", true);
define("BASEDIR", __DIR__ . DIRECTORY_SEPARATOR);

if (!SYNTAXREPORTING) {
    if (ERRORREPORTING) {
        error_reporting(E_ERROR | E_WARNING);
    } else {
        error_reporting(0);
    }
}

?>