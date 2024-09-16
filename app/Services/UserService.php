<?php

namespace App\Services;

use App\Repositories\Resources\UserRepository;
use Illuminate\Support\Facades\Auth;

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

    public function updateUser(int $id, array $data)
    {
        if (!Auth::check()) {
            return response()->json(401);
        }

        $currentUser = Auth::user();

        if ($currentUser->id != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $currentUser = $this->userRepository->update($id, $data);

        return $currentUser;
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
