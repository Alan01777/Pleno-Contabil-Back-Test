<?php

namespace App\Repositories\Resources;

use App\Models\UserPushToken;
use App\Models\User;

class UserPushTokenRepository
{
    protected $user, $userPushToken;

    public function __construct(User $user, UserPushToken $userPushToken)
    {
        $this->user = $user;
        $this->userPushToken = $userPushToken;
    }


    public function storeToken($data)
    {
        $user = $this->user->find($data['user_id']);

        if (!$user) {
            return null;
        }

        return $this->userPushToken->updateOrCreate(
            ['user_id' => $data['user_id']],
            ['token' => $data['token']]
        );
    }

    public function getRazaoSocialAndToken($userDirectory)
    {
        $user = User::where('directory', $userDirectory)->first();
        $razaoSocial = $user->razao_social;
        $token = UserPushToken::where('user_id', $user->id)->first()->token;

        return [$razaoSocial, $token];
    }
}