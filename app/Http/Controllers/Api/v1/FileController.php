<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\FileService;

class FileController extends Controller
{
    private $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function getFile(String $path)
    {
        return $this->fileService->getFile($path);
    }

    public function listFilesInDirectory(String $directory = null)
    {
        return $this->fileService->listFilesInDirectory($directory);
    }
}