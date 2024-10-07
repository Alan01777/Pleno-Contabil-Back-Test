<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\AuthRequest;
use App\Http\Requests\v1\PasswordResetRequest;
use App\Http\Requests\v1\PasswordTokenResetRequest;
use App\Http\Requests\v1\SendPasswordResetEmailRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Create user
     *
     * @param \App\Http\Requests\v1\AuthRequest $request
     * @return JsonResponse [string] message
     */
    public function register(AuthRequest $request): JsonResponse
    {
        return $this->authService->register($request);
    }

    /**
     * Login user and create token
     *
     * @param \App\Http\Requests\v1\AuthRequest $request
     * @return JsonResponse [string] message
     */
    public function login(AuthRequest $request): JsonResponse
    {
        return $this->authService->login($request);
    }

    /**
     * Get the authenticated User
     *
     * @param Request $request
     * @return JsonResponse [json] user object
     */
    public function user(Request $request): JsonResponse
    {
        return $this->authService->user($request);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param Request $request
     * @return JsonResponse [string] message
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->authService->logout($request);
    }

    public function sendPasswordRecoveryToken(SendPasswordResetEmailRequest $request)
    {
        return $this->authService->passwordRecovery($request);
    }

    public function validateToken(PasswordTokenResetRequest $request)
    {
        return $this->authService->validateToken($request);
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        return $this->authService->resetPassword($request);
    }
}