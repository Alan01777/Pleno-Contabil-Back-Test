<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserPushTokenService;

class UserPushTokenController extends Controller
{
    protected $userPushTokenService;

    public function __construct(UserPushTokenService $userPushTokenService)
    {
        $this->userPushTokenService = $userPushTokenService;
    }


    public function storeToken(Request $request)
    {
        return $this->userPushTokenService->storeToken($request);
    }


    public function handleWebhook(Request $request)
    {
        $this->userPushTokenService->handleMinioNotification($request);

        return response()->json(['message' => 'Webhook handled'], 200);
    }
}
