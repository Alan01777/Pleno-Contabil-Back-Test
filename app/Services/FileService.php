<?php

namespace App\Services;

use App\Repositories\Resources\FileRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileService
{
    private $disk;
    private $fileRepository;
    public function __construct(FileRepository $fileRepository)
    {
        $this->disk = Storage::disk('minio');
        $this->fileRepository = $fileRepository;
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


    public function uploadFile($request)
    {
        if (!$request->hasFile('file')) {
            throw new \Exception('No file uploaded');
        }

        $file = $request->file('file');
        $user = Auth::user();
        $root = $user->razao_social;
        // Get the original name of the file
        $name = $user->razao_social . ' - ' . $file->getClientOriginalName();
        $path = $root . $request->path;

        Storage::putFileAs($path, $file, $name);

        return $this->fileRepository->uploadFile([
            'name' => $name,
            'user_id' => $user->id,
            'path' => $path
        ]);
    }
}
