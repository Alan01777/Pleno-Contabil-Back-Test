<?php

namespace App\Services;

use App\Repositories\Resources\UserPushTokenRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UserPushTokenService
{
    protected $userPushTokenRepository;

    public function __construct(UserPushTokenRepository $userPushTokenRepository)
    {
     $this->userPushTokenRepository = $userPushTokenRepository;   
    }

    public function storeToken(Request $request)
    {
        $data = $request->validate(['token' => 'required|string|max:255']);

        $data['user_id'] = Auth::id();

        $this->userPushTokenRepository->storeToken($data);
    }

    public function handleMinioNotification($request)
    {
        // Parse the MinIO event data
        $eventData = $request->input('Records')[0];
        $fileName = $eventData['s3']['object']['key'];
        $userDirectory = explode('/', $fileName)[0];

        // Get the razao_social field and the user's push token
        [$razaoSocial, $token] = $this->userPushTokenRepository->getRazaoSocialAndToken($userDirectory);

        if ($razaoSocial === $userDirectory) {
            // Prepare the POST data
            $postData = [
                'to' => $token,
                'title' => 'Novo arquivo disponível!',
                'body' => 'Um novo arquivo está disponível no seu App!',
                'data' => ['extra' => 'data']
            ];

            // Send the POST request
            Http::post('https://exp.host/--/api/v2/push/send', $postData);
        }
    }
}