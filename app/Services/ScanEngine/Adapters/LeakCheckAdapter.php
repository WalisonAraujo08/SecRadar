<?php

namespace App\Services\ScanEngine\Adapters;

use App\Services\ScanEngine\Contracts\AdapterInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeakCheckAdapter implements AdapterInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://leakcheck.io/api/v2';

    public function __construct()
    {
        $this->apiKey = config('scan_sources.leakcheck.api_key', '');
    }

    public function check(string $email): array
    {
        if (empty($this->apiKey)) return [];

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->timeout(10)->get("{$this->baseUrl}/query/{$email}");

            if (!$response->ok()) return [];

            $data = $response->json();
            if (!($data['success'] ?? false) || empty($data['result'])) return [];

            return collect($data['result'])->map(fn($b) => [
                'source_key'   => 'src_a2',
                'breach_name'  => 'Incidente de Segurança #' . abs(crc32($b['name'] ?? 'unknown')),
                'data_exposed' => $this->mapFields($b['fields'] ?? []),
                'severity'     => $this->calcSeverity($b['fields'] ?? []),
                'breach_date'  => $b['date'] ?? null,
            ])->toArray();

        } catch (\Throwable $e) {
            Log::error('LeakCheckAdapter error: ' . $e->getMessage());
            return [];
        }
    }

    private function mapFields(array $fields): array
    {
        $map = [
            'email'    => 'email',
            'password' => 'senha',
            'phone'    => 'telefone',
            'username' => 'nome',
            'address'  => 'endereco',
            'name'     => 'nome',
        ];
        return array_values(array_unique(array_filter(
            array_map(fn($f) => $map[strtolower($f)] ?? null, $fields)
        )));
    }

    private function calcSeverity(array $fields): string
    {
        $lc = array_map('strtolower', $fields);
        if (in_array('password', $lc)) return 'critical';
        if (array_intersect(['phone', 'address'], $lc)) return 'high';
        return 'medium';
    }
}
