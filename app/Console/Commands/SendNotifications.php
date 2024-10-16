<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserPushToken;
use Illuminate\Support\Facades\Http;

class SendNotifications extends Command
{
    protected $signature = 'send:notifications {type}';

    protected $description = 'Send notifications to all users';

    public function handle()
    {
        $type = $this->argument('type');

        $tokens = UserPushToken::all();

        // Loop through the tokens and send the notification
        foreach ($tokens as $token) {
            // Get the message
            $message = $this->getMessage($type);

            // Prepare the POST data
            $postData = [
                'to' => $token->token,
                'title' => $message['title'],
                'body' => $message['body'],
                'data' => ['extra' => 'data']
            ];

            // Send the POST request
            Http::post('https://exp.host/--/api/v2/push/send', $postData);
        }
    }

    private function getMessage($type)
    {
        switch ($type) {
            case 'DAS':
                return [
                    'title' => 'Data de vencimento do DAS se aproximando!',
                    'body' => 'Não se esqueça de verificar o aplicativo para os detalhes do seu pagamento DAS. Mantenha-se à frente de suas finanças!'
                ];
            case 'PARCELAMENTO':
                return [
                    'title' => 'Alerta de data de vencimento da parcela!',
                    'body' => 'A data de vencimento da sua parcela está próxima. Verifique o aplicativo para ver os detalhes do seu pagamento. Mantenha seus pagamentos em dia!'
                ];
            case 'PIS':
                return [
                    'title' => 'Pagamento do PIS vence em breve!',
                    'body' => 'Seu pagamento do PIS vence em breve. Visite o aplicativo para garantir que seu pagamento esteja pronto. Mantenha-se financeiramente organizado!'
                ];
            case 'COFINS':
                return [
                    'title' => 'Data de vencimento do COFINS se aproximando!',
                    'body' => 'Seu pagamento do COFINS vence em breve. Verifique o aplicativo para os detalhes do pagamento. Evite multas por atraso!'
                ];
            case 'ICMS':
                return [
                    'title' => 'Alerta de pagamento do ICMS!',
                    'body' => 'A data de vencimento do seu pagamento do ICMS está se aproximando. Visite o aplicativo para ver os detalhes do seu pagamento. Mantenha-se em dia com seus pagamentos!'
                ];
            case 'FOLHAS':
                return [
                    'title' => 'Suas folhas de pagamento estão disponíveis!',
                    'body' => 'Suas folhas de pagamento agora estão disponíveis no aplicativo. Confira agora. Mantenha sua folha de pagamento organizada!'
                ];
            case 'FGTS':
                return [
                    'title' => 'Alerta de data de vencimento do FGTS!',
                    'body' => 'Seu pagamento do FGTS vence em breve. Verifique o aplicativo para garantir que seu pagamento esteja pronto. Evite qualquer multa por atraso no pagamento!'
                ];
            default:
                return [
                    'title' => 'Novo arquivo adicionado!',
                    'body' => 'Um novo arquivo foi adicionado à sua conta. Verifique o aplicativo para visualizá-lo. Mantenha-se atualizado com sua conta!'
                ];
        }
    }
}
