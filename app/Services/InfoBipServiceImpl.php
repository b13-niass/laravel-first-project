<?php

namespace App\Services;

use App\Services\Interfaces\MessageService;
use Infobip\Api\SendSmsApi;
use Infobip\Configuration;

class InfoBipServiceImpl implements MessageService
{
    private $client;

    public function __construct()
    {
        $this->configure();
    }

    public function configure()
    {
        $config = (new Configuration())
            ->setHost(env('URL_BASE_PATH'))// Exemple: 'Bearer'
            ->setApiKey('Authorization', 'App ' . env('API_KEY')); // Votre clé API ici

        $this->client = new SendSmsApi(null, $config);
    }

    public function send($to, $subject, $body)
    {
        // Assurez-vous que 'to', 'from' et 'text' sont au format attendu par Infobip
        return $this->client->sendSmsMessage([
            'messages' => [
                [
                    'destinations' => [['to' => $to]],
                    'from' => $subject,
                    'text' => $body
                ]
            ]
        ]);
    }
}
