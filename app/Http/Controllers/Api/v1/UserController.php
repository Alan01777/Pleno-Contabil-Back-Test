<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Requests\v1\UserRequest;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getUser()
    {
        return $this->userService->getUser();
    }

    public function updateUser(UserRequest $request)
    {
        $data = $request->validated();

        return $this->userService->updateUser($data);
    }

    public function deleteUser(int $id)
    {
        return $this->userService->deleteUser($id);
    }
}
