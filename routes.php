<?php

use src\router\Route;

$routes = [
    Route::create("/", ["TestController", "test"], ["GET"]),
    Route::create("/test/{int}", ["TestController", "test"], ["GET"])
];

?>