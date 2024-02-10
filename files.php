<?php

use src\controllers\FileController;
use src\importers\FileImporter;
use src\middleware\CORSMiddleware;
use src\router\Route;

if ($_ENV['FILES_ENABLED']) {
    $filesPath = getPublicDir();
    $files = FileImporter::scanFiles(BASEDIR, PUBLICDIR);

    foreach ($files as $filePath => $file) {
        $route = Route::create()
            ->setPath($file)
            ->setVar('filePath', $filePath)
            ->setCallable([FileController::class, 'serveFile'])
            ->setMethods(['GET']);

        if ($_ENV['FILES_CORS']) {
            $route->setMiddleware([
                [CORSMiddleware::class, 'execute']
            ]);
        }

        if ($_ENV['FILES_HIDE_ROUTE']) {
            $route->setHide();
        }

        $routes[] = $route;
    }
}
