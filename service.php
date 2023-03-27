<?php

/**
 * A place to define your global variables
 * These variables will be injected as properties in the contoller
 * A good use case would be putting the db connection variable here
 * 
 * These variables will only be accessible to methods defined in a controller
 * If you want to access these variables use $GLOBALS['service'] to retrieve them
 */

$globalVar = 'placeholder';

$service = [
    'globalVar' => $globalVar
];

?>