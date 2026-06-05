<?php

namespace App\Services\ScanEngine\Adapters;

use App\Services\ScanEngine\Contracts\AdapterInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocalDatabaseAdapter implements AdapterInterface
{
    public function check(string $email): array
    {
        try {
            if (!$this->tableExists()) return [];

            $matches = DB::table('breach_matches')
                ->where('email', strtolower(trim($email)))
                ->where('notified', false)
                ->get();

            if ($matches->isEmpty()) return [];

            // Marca como notificado
            DB::table('breach_matches')
                ->where('email', strtolower(trim($email)))
                ->update(['notified' => true]);

            return $matches->groupBy('breach_name')->map(function ($group, $breachName) {
                $hasPassword = $group->whereNotNull('password')->isNotEmpty();
                $hasPhone    = $group->whereNotNull('phone')->isNotEmpty();
                $hasCpf      = $group->whereNotNull('cpf')->isNotEmpty();

                $dataExposed = ['email'];
                if ($hasPassword) $dataExposed[] = 'senha';
                if ($hasPhone)    $dataExposed[] = 'telefone';
                if ($hasCpf)      $dataExposed[] = 'cpf';

                return [
                    'source_key'   => 'src_local',
                    'breach_name'  => 'Incidente de Segurança #' . abs(crc32($breachName)),
                    'data_exposed' => $dataExposed,
                    'severity'     => $group->first()->severity ?? 'medium',
                    'breach_date'  => null,
                ];
            })->values()->toArray();

        } catch (\Throwable $e) {
            Log::error('LocalDatabaseAdapter: ' . $e->getMessage());
            return [];
        }
    }

    private function tableExists(): bool
    {
        try {
            DB::table('breach_matches')->limit(1)->get();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
