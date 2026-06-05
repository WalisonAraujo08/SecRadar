<?php

namespace App\Services\ScanEngine\Adapters;

use App\Services\ScanEngine\Contracts\AdapterInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DehashedAdapter implements AdapterInterface
{
    private string $email;
    private string $apiKey;
    private string $baseUrl = 'https://api.dehashed.com';

    public function __construct()
    {
        $this->email  = config('scan_sources.dehashed.email', '');
        $this->apiKey = config('scan_sources.dehashed.api_key', '');
    }

    public function check(string $targetEmail): array
    {
        if (empty($this->apiKey) || empty($this->email)) return [];

        try {
            $response = Http::withBasicAuth($this->email, $this->apiKey)
                ->withHeaders(['Accept' => 'application/json'])
                ->timeout(10)
                ->get("{$this->baseUrl}/search", ['query' => $targetEmail, 'size' => 20]);

            if (!$response->ok()) return [];

            $entries = $response->json('entries') ?? [];
            if (empty($entries)) return [];

            // Agrupar por database_name para não criar duplicatas por registro
            $grouped = collect($entries)->groupBy('database_name');

            return $grouped->map(function ($group, $dbName) {
                $hasPassword = $group->whereNotNull('password')->isNotEmpty()
                    || $group->whereNotNull('hashed_password')->isNotEmpty();
                $hasPhone    = $group->whereNotNull('phone')->isNotEmpty();

                return [
                    'source_key'   => 'src_a3',
                    'breach_name'  => 'Incidente de Segurança #' . abs(crc32($dbName)),
                    'data_exposed' => array_values(array_filter([
                        'email',
                        $hasPassword ? 'senha' : null,
                        $hasPhone    ? 'telefone' : null,
                    ])),
                    'severity'     => $hasPassword ? 'critical' : ($hasPhone ? 'high' : 'medium'),
                    'breach_date'  => null,
                ];
            })->values()->toArray();

        } catch (\Throwable $e) {
            Log::error('DehashedAdapter error: ' . $e->getMessage());
            return [];
        }
    }
}
