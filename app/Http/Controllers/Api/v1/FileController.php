<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class FileController extends Controller
{
    public function getFile(String $path)
    {
        $file = Storage::disk('minio')->get($path);
        $name = basename($path);
        $headers = ['Content-Type' => Storage::disk('minio')->mimetype($path)];

        return response($file, 200, $headers)->header('Content-Disposition', 'attachment; filename=' . $name);
    }

    public function listFilesInDirectory(String $directory)
    {
        $files = Storage::disk('minio')->listContents($directory)->toArray();

        // Format the files array to only include the necessary details
        $formattedFiles = array_map(function ($file) {
            $formattedFile = [
                'name' => basename($file['path']),
                'type' => $file['type'],
                'last_modified' => $file['lastModified'],
            ];

            if ($file['type'] === 'file') {
                $formattedFile['size'] = $file['fileSize'];
            }

            return $formattedFile;
        }, $files);

        return response()->json($formattedFiles);
    }
}
