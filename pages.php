<?php

use src\controllers\PagesController;
use src\importers\PageImporter;
use src\middleware\CORSMiddleware;
use src\router\Route;

if ($_ENV['PAGES_ENABLED']) {
    $pages = PageImporter::scanPages(
        BASEDIR,
        PAGESDIR,
        $_ENV['PAGES_ALLOWED_FILE_EXTENSIONS']
    );

    foreach ($pages as $filePath => $path) {
        $route = Route::create()
            ->setPath($path)
            ->setVar('filePath', $filePath)
            ->setCallable([PagesController::class, 'loadPage'])
            ->setMethods(['*']);

        if ($_ENV['PAGES_CORS']) {
            $route->setMiddleware([
                [CORSMiddleware::class, 'execute']
            ]);
        }

        if ($_ENV['PAGES_HIDE_ROUTE']) {
            $route->setHide();
        }

        $routes[] = $route;
    }
}
