<?php

use src\app\Request;
use src\app\Response;
use src\middleware\ExampleMiddleware;
use src\router\Route;
use src\controllers\ExampleController;
use src\util\Methods;

function exampleFunction(Request $request)
{
    Response::ok(['success' => true]);
}

$routes = [
    Route::create()
        ->setPath('/api')
        ->setCallable([ExampleController::class, 'exampleHttp'])
        ->setMethods(Methods::WILDCARD)
        ->setMiddleware([
            [ExampleMiddleware::class, 'execute']
        ])
        ->setHide(),

    Route::create()
        ->setPath('//api/stringfunction-example/')
        ->setCallable('exampleFunction')
        ->setMethods([Methods::GET, Methods::POST]),

    Route::create()
        ->setPath('/api/lambda-example')
        ->setCallable(
            function (Request $request) {
                Response::ok(['success' => true]);
            }
        )
        ->setMethods([Methods::GET, Methods::POST]),

    Route::create()
        ->setPath('/api/example/{id}')
        ->setCallable(
            function (Request $request) {
                Response::ok($request->getVars());
            }
        )
        ->setMethods([Methods::GET, Methods::POST, Methods::PUT, Methods::DELETE]),
];
