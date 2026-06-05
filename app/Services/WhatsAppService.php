<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $phoneId;
    private string $token;

    public function __construct()
    {
        $this->phoneId = config('services.whatsapp.phone_id', '');
        $this->token   = config('services.whatsapp.token', '');
    }

    public function sendAlert(string $toPhone, string $clientName, string $severity): bool
    {
        if (empty($this->phoneId) || empty($this->token)) return false;

        // Normaliza telefone
        $phone = preg_replace('/\D/', '', $toPhone);
        if (!str_starts_with($phone, '55')) $phone = '55' . $phone;

        $severityLabel = match($severity) {
            'critical' => 'CRÍTICO — Ação imediata necessária',
            'high'     => 'ALTO — Recomendamos ação urgente',
            'medium'   => 'MÉDIO — Monitore suas contas',
            default    => 'BAIXO — Sem ação imediata necessária',
        };

        try {
            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/v19.0/{$this->phoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to'                => $phone,
                    'type'              => 'template',
                    'template'          => [
                        'name'     => config('services.whatsapp.template_alert', 'secradar_alert'),
                        'language' => ['code' => 'pt_BR'],
                        'components' => [
                            // Variáveis do corpo {{1}} e {{2}}
                            [
                                'type'       => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $clientName],
                                    ['type' => 'text', 'text' => $severityLabel],
                                ],
                            ],
                        ],
                    ],
                ]);

            if (!$response->ok()) {
                Log::error('WhatsApp sendAlert failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            Log::error('WhatsApp exception: ' . $e->getMessage());
            return false;
        }
    }
}