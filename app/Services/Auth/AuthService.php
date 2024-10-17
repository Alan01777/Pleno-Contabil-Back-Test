<?php

namespace App\Services\Auth;

use App\Http\Requests\v1\SendPasswordResetEmailRequest;
use App\Http\Requests\v1\AuthRequest;
use App\Http\Requests\v1\PasswordResetRequest;
use App\Http\Requests\v1\PasswordTokenResetRequest;
use App\Mail\RecoverPasswordEmail;
use App\Repositories\Resources\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

        // Convert "razao_social" and "nome_fantasia" to uppercase
        if (isset($data['razao_social'])) {
            $data['razao_social'] = strtoupper($data['razao_social']);
        }
        if (isset($data['nome_fantasia'])) {
            $data['nome_fantasia'] = strtoupper($data['nome_fantasia']);
        }

        $user = $this->userRepository->create($data);

        if ($user->save()) {
            // Create a directory in the MinIO bucket for the new user
            $userDirectory = $user->razao_social;
            Storage::disk('minio')->makeDirectory($userDirectory);

            // Create subdirectories
            $subdirectories = [
                'PESSOAL/CONTRATOS',
                'FISCAL/DAS',
                'FISCAL/PARCELAMENTO',
                'FISCAL/PIS',
                'FISCAL/COFINS',
                'FISCAL/ICMS',
                'PESSOAL/FOLHAS',
                'PESSOAL/FGTS',
                'CERTIDOES',
                'EMPRESA',
                'FATURAMENTOS'
            ];
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
                'message' => 'Dados Incorretos'
            ], 401);
        }

        $user = $request->user();

        // Revoke all tokens for the user
        $user->tokens()->each(function ($token, $key) {
            $token->delete();
        });

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



    public function passwordRecovery(SendPasswordResetEmailRequest $request)
    {
        $data = $request->validated();
        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // Generate a password reset token
        $token = app('auth.password.broker')->createToken($user);

        // Send the email
        Mail::to($data['email'])->send(new RecoverPasswordEmail($user->razao_social, $token));

        return response()->json(['message' => 'Password recovery email sent.']);
    }

    public function validateToken(PasswordTokenResetRequest $request)
    {
        $tokenData = $this->userRepository->getResetToken($request->token);

        if (!$tokenData) {
            return response()->json(['message' => 'Invalid token.'], 400);
        }

        return response()->json(['message' => 'Token is valid.'], 200);
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        $token = $request->header('Authorization');

        $tokenData = $this->userRepository->getResetToken($token);

        if (!$tokenData) {
            return response()->json(['message' => 'Invalid token.'], 400);
        }

        $user = $this->userRepository->findByEmail($tokenData->email);

        if (!$user) {
            return response()->json(['message' => 'User does not exist.'], 404);
        }

        $data = $request->validated();

        $this->userRepository->updatePassword($user, $data['password']);

        // Delete the token
        $this->userRepository->deleteResetToken($user->email);

        return response()->json(['message' => 'Password has been updated.']);
    }
}
