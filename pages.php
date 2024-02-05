<?php

use src\controllers\PagesController;
use src\filesystem\PageImporter;
use src\middleware\CORSMiddleware;
use src\router\Route;
use src\util\Strings;

if ($_ENV['PAGES_ENABLED']) {
    $pages = PageImporter::scanPages(BASEDIR, PAGESDIR);

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
