<?php

namespace App\Services\ScanEngine\Adapters;

use App\Services\ScanEngine\Contracts\AdapterInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BreachDirectoryAdapter implements AdapterInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://breachdirectory.p.rapidapi.com';

    public function __construct()
    {
        $this->apiKey = config('scan_sources.breachdir.api_key', '');
    }

    public function check(string $email): array
    {
        if (empty($this->apiKey)) return [];

        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key'  => $this->apiKey,
                'X-RapidAPI-Host' => 'breachdirectory.p.rapidapi.com',
            ])->timeout(10)->get($this->baseUrl, ['func' => 'auto', 'term' => $email]);

            if (!$response->ok()) return [];

            $data = $response->json();
            if (!($data['found'] ?? false) || empty($data['result'])) return [];

            $hasPassword = collect($data['result'])->whereNotNull('password')->isNotEmpty()
                || collect($data['result'])->whereNotNull('sha1')->isNotEmpty();

            return [[
                'source_key'   => 'src_a4',
                'breach_name'  => 'Incidente de Segurança #' . abs(crc32($email . 'bdir')),
                'data_exposed' => array_filter(['email', $hasPassword ? 'senha' : null]),
                'severity'     => $hasPassword ? 'critical' : 'medium',
                'breach_date'  => null,
            ]];

        } catch (\Throwable $e) {
            Log::error('BreachDirectoryAdapter error: ' . $e->getMessage());
            return [];
        }
    }
}
