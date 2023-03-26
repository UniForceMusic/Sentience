<?php

use src\app\Request;
use src\app\Response;
use src\router\Route;
use src\controllers\ExampleController;

$routes = [
    Route::create('/', [ExampleController::class, 'example'], ['GET', 'POST', 'PUT', 'DELETE']),

    Route::create(
        '/example',
        function (Request $request) {
            Response::ok(['success' => true]);
        },
        ['GET', 'POST']
    )
];

?>