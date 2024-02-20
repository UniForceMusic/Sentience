<?php

use src\controllers\PagesController;
use src\importers\PageImporter;
use src\middleware\CORSMiddleware;
use src\router\Route;

if (env('PAGES_ENABLED', true)) {
    $pages = PageImporter::scanPages(
        BASEDIR,
        PAGESDIR,
        env('PAGES_ALLOWED_FILE_EXTENSIONS', ['.php', '.html', '.htm'])
    );

    foreach ($pages as $filePath => $path) {
        $route = Route::create()
            ->setPath($path)
            ->setVar('filePath', $filePath)
            ->setCallable([PagesController::class, 'loadPage'])
            ->setMethods(['*']);

        if (env('PAGES_CORS', true)) {
            $route->setMiddleware([
                [CORSMiddleware::class, 'execute']
            ]);
        }

        if (env('PAGES_HIDE_ROUTE', false)) {
            $route->setHide();
        }

        $routes[] = $route;
    }
}
