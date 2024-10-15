<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserPushToken;
use Illuminate\Support\Facades\Http;

class SendNotifications extends Command
{
    protected $signature = 'send:notifications';

    protected $description = 'Send notifications to all users';

    public function handle()
    {
        $tokens = UserPushToken::all();

        // Loop through the tokens and send the notification
        foreach ($tokens as $token) {
            // Prepare the POST data
            $postData = [
                'to' => $token->token,
                'title' => 'Um novo arquivo foi adicionado!',
                'body' => 'Um novo arquivo está disponível no seu App!',
                'data' => ['extra' => 'data']
            ];

            // Send the POST request
            Http::post('https://exp.host/--/api/v2/push/send', $postData);
        }
    }
}