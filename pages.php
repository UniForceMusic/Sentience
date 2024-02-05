<?php

use src\controllers\PagesController;
use src\filesystem\PageImporter;
use src\middleware\CORSMiddleware;
use src\router\Route;
use src\util\Strings;

if ($_ENV['PAGES_ENABLED']) {
    $pagesPath = getPagesDir();
    $pages = PageImporter::scanPages(BASEDIR, PAGESDIR);

    foreach ($pages as $page) {
        $path = Strings::strip($pagesPath, $page);
        $path = Strings::beforeSubstr($path, '.');

        if (is_file($page) && str_starts_with(basename($page), 'index.')) {
            $path = rtrim($path, 'index');
        }

        $route = Route::create()
            ->setPath($path)
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
