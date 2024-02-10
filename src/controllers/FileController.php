<?php

namespace src\controllers;

use src\app\Request;
use src\app\Response;
use src\filesystem\File;
use src\util\Headers;

class FileController extends Controller
{
    public function serveFile(Request $request): void
    {
        $filePath = $request->getVar('filePath');

        if (!file_exists($filePath)) {
            Response::notFound();
        }

        $fileContents = File::read($filePath);
        $mimeType = File::mimeType($filePath);

        if (!$mimeType) {
            Response::unsupportedMediaType();
        }

        Headers::serveFile();
        Headers::contentLength($fileContents);
        Headers::cacheControl(Headers::CACHE_PUBLIC, $_ENV['FILE_CACHE']);
        Response::ok(
            $fileContents,
            $mimeType
        );
    }
}
