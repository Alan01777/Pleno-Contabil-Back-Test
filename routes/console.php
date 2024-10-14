<?php
use App\Models\UserPushToken;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Http;

Schedule::call(function () {
    $tokens = UserPushToken::all();

    // Loop through the tokens and send the notification
    foreach ($tokens as $token) {
        // Prepare the POST data
        $postData = [
            'to' => $token->token,
            'title' => 'Seu DAS ja está disponível para pagamento!',
            'body' => 'Um novo DAS está disponível no seu App!',
            'data' => ['extra' => 'data']
        ];

        // Send the POST request
        Http::post('https://exp.host/--/api/v2/push/send', $postData);
    }
})->everyMinute();