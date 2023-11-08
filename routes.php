<?php

use src\app\Request;
use src\app\Response;
use src\router\Route;
use src\controllers\ExampleController;
use src\controllers\FileController;
use src\middleware\ExampleMiddleware;

function exampleFunction(Request $request)
{
    Response::ok(['success' => true]);
}

$routes = [
    Route::create()
        ->setFilePath(sprintf('/%s/{filePath}', FILEDIR))
        ->setCallable([FileController::class, 'serveFile'])
        ->setMethods(['GET']),

    Route::create()
        ->setPath('/')
        ->setCallable([ExampleController::class, 'exampleHttp'])
        ->setMethods(['*'])
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
        ->setMethods(['GET', 'POST']),

    Route::create()
        ->setPath('/example/{id}')
        ->setCallable(
            function (Request $request) {
                Response::ok($request->getVars());
            }
        )
        ->setMethods(['GET', 'POST', 'PUT', 'DELETE']),
];
