<?php

use src\controllers\FileController;
use src\filesystem\FileImporter;
use src\filesystem\Filesystem;
use src\middleware\CORSMiddleware;
use src\router\Route;
use src\util\Strings;

if ($_ENV['FILES_ENABLED']) {
    $filesPath = getFileDir();
    $files = FileImporter::scanFiles(BASEDIR, FILEDIR);

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
