<?php

namespace App\Services\Auth;

use App\Http\Requests\v1\AuthRequest;
use App\Repositories\Resources\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user.
     *
     * @param \App\Http\Requests\v1\AuthRequest $request
     * @return JsonResponse
     */
    public function register(AuthRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->userRepository->create($data);

        if ($user->save()) {
            // Create a directory in the MinIO bucket for the new user
            $userDirectory = $user->razao_social;
            Storage::disk('minio')->makeDirectory($userDirectory);

            // Create subdirectories
            $subdirectories = ['PESSOAL/CONTRATOS', 'FISCAL/DAS', 'FISCAL/PARCELAMENTO', 'FISCAL/PIS', 'FISCAL/COFINS', 'FISCAL/ICMS', 'PESSOAL/FOLHAS', 'PESSOAL/FGTS', 'PESSOAL/CERTIDOES'];
            foreach ($subdirectories as $subdirectory) {
                Storage::disk('minio')->makeDirectory($userDirectory . '/' . $subdirectory);
            }

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;
            
            return response()->json([
                'message' => 'Successfully created user!',
                'accessToken' => $token,
            ], 201);
        } else {
            return response()->json(['error' => 'Provide proper details']);
        }
    }

    /**
     * Log in a user.
     *
     * @param \App\Http\Requests\v1\AuthRequest $request
     * @return JsonResponse
     */
    public function login(AuthRequest $request): JsonResponse
    {
        $data = $request->validated();

        $credentials = array_intersect_key($data, array_flip(['email', 'password']));
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'accessToken' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(500, ['error' => 'Not Authenticated!']);
        }
        return response()->json($request->user());
    }

    /**
     * Log out the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(500, ['error' => 'Not Authenticated!']);
        }
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}