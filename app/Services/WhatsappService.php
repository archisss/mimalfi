<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected string $token;
    protected string $url;

    public function __construct()
    {
        $this->token = env('WHATSAPP_TOKEN');
        $this->url = 'https://graph.facebook.com/v22.0/624771680729325/messages';
    }

    public function sendTemplateMessage(string $to, string $templateName = 'recibo_abono', array $components = [], string $languageCode = 'es_MX'): array
{
    $response = Http::withToken($this->token)->post($this->url, [
        'messaging_product' => 'whatsapp',
        'to' => $to,
        'type' => 'template',
        'template' => [
            'name' => $templateName,
            'language' => [
                'code' => $languageCode,
            ],
            'components' => $components,
        ],
    ]);

    return [
        'status' => $response->status(),
        'body' => $response->json(),
    ];
}

    public function sendTemplateMessage1(string $to, string $templateName = 'SU abono se registro gracias', string $languageCode = 'es-MX'): array
    {
        $response = Http::withToken($this->token)->post($this->url, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode,
                ],
            ],
        ]);

        return [
            'status' => $response->status(),
            'body' => $response->json(),
        ];
    }
}
