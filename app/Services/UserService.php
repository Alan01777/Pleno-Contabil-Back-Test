<?php

namespace App\Services;

use App\Repositories\Resources\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Exceptions\NullValueException;

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

        try {
            $currentUser = $this->userRepository->update($currentUser->id, $data);
            } catch (NullValueException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

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
