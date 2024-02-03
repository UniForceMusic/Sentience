<?php

use src\controllers\PagesController;
use src\filesystem\PageImporter;
use src\middleware\CORSMiddleware;
use src\router\Route;

if ($_ENV['PAGES_ENABLED']) {
    $pages = PageImporter::scanPages(
        BASEDIR,
        PAGESDIR
    );

    foreach ($pages as $page) {
        $removeAbsolutePath = str_replace(
            PAGESDIR,
            '',
            $page
        );

        $removeIndex = str_replace(
            'index.php',
            '',
            $removeAbsolutePath
        );

        $removePhp = str_replace(
            '.php',
            '',
            $removeIndex
        );

        $route = Route::create()
            ->setPath($removePhp)
            ->setVar('pagePath', $page)
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
