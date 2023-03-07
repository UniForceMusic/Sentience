<?php

use src\router\Route;
use src\controllers\TestController;

$routes = [
    Route::create("/", [TestController::class, "test"], ["GET"]),
    Route::create("/test/{int}", [TestController::class, "test"], ["GET"])
];

?>