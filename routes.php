<?php

use src\router\Route;
use src\controllers\TestController;

$routes = [
    Route::create("/", [TestController::class, "test"], ["GET"]),
    Route::create("/test/{testId}", [TestController::class, "test"], ["GET"]),
    Route::create("/user/{userId}", [TestController::class, "test"], ["GET"])
];

?>