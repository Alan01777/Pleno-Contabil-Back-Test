<?php

namespace App\Services;

use App\Repositories\Resources\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Exceptions\NullValueException;
use Illuminate\Support\Facades\Storage;

class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUser()
    {
        if (!Auth::check()) {
            return response()->json(401);
        }
        $id = Auth::id();
        $currentUser = $this->userRepository->getById($id);

        return $currentUser;
    }

    public function updateUser(array $data)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $currentUser = Auth::user();
        $oldUser = $currentUser;

        try {
            $currentUser = $this->userRepository->update($currentUser->id, $data);

            // Get all files in the old directory and its subdirectories
            $files = Storage::disk('minio')->allFiles($oldUser->razao_social);

            // Move each file to the new directory
            foreach ($files as $file) {
                $newPath = str_replace($oldUser->razao_social, $currentUser->razao_social, $file);
                Storage::disk('minio')->move($file, $newPath);
            }

            // Delete the old directory
            Storage::disk('minio')->deleteDirectory($oldUser->razao_social);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json($currentUser, 200);
    }

    public function deleteUser(int $id)
    {
        if (!Auth::check()) {
            return response()->json(401);
        }
        $currentUser = Auth::user();

        if ($currentUser->id != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->userRepository->delete($id);

        return response()->json(['message' => 'User deleted successfully']);
    }
}
