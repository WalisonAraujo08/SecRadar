<?php

namespace App\Services\ScanEngine\Adapters;

use App\Services\ScanEngine\Contracts\AdapterInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeakLookupAdapter implements AdapterInterface
{
    // LeakLookup.com — plano gratuito com 10 consultas/dia
    private string $baseUrl = 'https://leak-lookup.com/api/search';

    public function check(string $email): array
    {
        $apiKey = config('scan_sources.leaklookup.api_key', '');
        if (empty($apiKey)) return [];

        try {
            $response = Http::timeout(10)->post($this->baseUrl, [
                'key'    => $apiKey,
                'type'   => 'email_address',
                'query'  => $email,
            ]);

            if (!$response->ok()) return [];

            $data = $response->json();
            if (($data['error'] ?? '') === 'true') return [];

            $results = [];
            foreach ($data['message'] ?? [] as $source => $entries) {
                $results[] = [
                    'source_key'   => 'src_a5',
                    'breach_name'  => 'Incidente de Segurança #' . abs(crc32($source)),
                    'data_exposed' => ['email', 'senha'],
                    'severity'     => 'critical',
                    'breach_date'  => null,
                ];
            }
            return $results;

        } catch (\Throwable $e) {
            Log::error('LeakLookupAdapter error: ' . $e->getMessage());
            return [];
        }
    }
}