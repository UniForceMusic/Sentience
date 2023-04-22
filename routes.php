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
    Route::create()
        ->setPath('/')
        ->setCallable([ExampleController::class, 'example'])
        ->setMethods(['GET', 'POST', 'PUT', 'DELETE'])
        ->setMiddleware([ExampleMiddleware::class]),

    Route::create()
        ->setPath('/stringfunction-example')
        ->setCallable('exampleFunction')
        ->setMethods(['GET', 'POST']),

    Route::create()
        ->setPath('/lambda-example')
        ->setCallable(
            function (Request $request) {
                Response::ok(['success' => true]);
            }
        )
        ->setMethods(['GET', 'POST'])
];

?>