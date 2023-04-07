<?php

use src\app\Request;
use src\app\Response;
use src\router\Route;
use src\controllers\ExampleController;
use src\middleware\ExampleMiddleware;

function exampleFunction(Request $request)
{
    Response::ok(['success' => true]);
}

$routes = [
    Route::create('/', [ExampleController::class, 'example'], ['GET', 'POST', 'PUT', 'DELETE'], [ExampleMiddleware::class]),
    Route::create('/stringfunction-example', 'exampleFunction', ['GET', 'POST']),
    Route::create(
        '/lambda-example',
        function (Request $request) {
            Response::ok(['success' => true]);
        },
        ['GET', 'POST']
    ),
];

?>