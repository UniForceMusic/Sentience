<?php

use src\controllers\FileController;
use src\importers\FileImporter;
use src\middleware\CORSMiddleware;
use src\router\Route;

if (env('FILES_ENABLED', true)) {
    $files = FileImporter::scanFiles(BASEDIR, PUBLICDIR);

    foreach ($files as $filePath => $file) {
        $route = Route::create()
            ->setPath($file)
            ->setVar('filePath', $filePath)
            ->setCallable([FileController::class, 'serveFile'])
            ->setMethods(['GET']);

        if (env('FILES_CORS', true)) {
            $route->setMiddleware([
                [CORSMiddleware::class, 'execute']
            ]);
        }

        if (env('FILES_HIDE_ROUTE', false)) {
            $route->setHide();
        }

        $routes[] = $route;
    }
}
