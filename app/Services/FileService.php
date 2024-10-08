<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use League\Flysystem\FileNotFoundException;

class FileService {
    private $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('minio');
    }

    public function getFile(String $path)
    {
        try {
            if (!Storage::disk('minio')->exists($path)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            $file = Storage::disk('minio')->get($path);
            $name = basename($path);
            $headers = [
                'Content-Type' => $this->disk->mimeType($path),
                'Content-Disposition' => 'attachment; filename=' . $name,
            ];

            return response()->make($file, 200, $headers);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function listFilesInDirectory(String $directory = null)
    {
        if (!$directory) {
            $user = Auth::user();
            $directory = $user->razao_social;
        }

        $files = $this->disk->listContents($directory)->toArray();

        $formattedFiles = array_map(function ($file) {
            $formattedFile = [
                'name' => basename($file['path']),
                'type' => $file['type'],
                'path' => $file['path'],
                'last_modified' => $file['lastModified'],
            ];

            if ($file['type'] === 'file') {
                $formattedFile['size'] = $file['fileSize'];
            }

            return $formattedFile;
        }, $files);

        return $formattedFiles;
    }
}