<?php

namespace App\Services\ScanEngine\Contracts;

interface AdapterInterface
{
    /**
     * Verifica um e-mail e retorna array de vazamentos encontrados.
     * source_key é INTERNO e nunca exposto ao cliente.
     *
     * @return array<array{source_key: string, breach_name: string, data_exposed: array, severity: string, breach_date: ?string}>
     */
    public function check(string $email): array;
}
