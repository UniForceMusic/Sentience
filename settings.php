<?php

define("SYNTAXREPORTING", true);
define("ERRORREPORTING", true);
define("BASEDIR", __DIR__ . DIRECTORY_SEPARATOR);

if (!SYNTAXREPORTING && ERRORREPORTING) {
    error_reporting(E_ERROR | E_WARNING);
}

if (!SYNTAXREPORTING && !ERRORREPORTING) {
    error_reporting(0);
}

?>