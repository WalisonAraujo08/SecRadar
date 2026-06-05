<?php

namespace App\Services\ScanEngine\Adapters;

use App\Services\ScanEngine\Contracts\AdapterInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HibpAdapter implements AdapterInterface
{
    /**
     * Verifica se o e-mail aparece em senhas comprometidas.
     * USA APENAS a API pública gratuita e ilimitada do HIBP Pwned Passwords.
     * Nunca envia a senha real — só os primeiros 5 chars do hash SHA1.
     */
    public function check(string $email): array
    {
        try {
            // Busca senhas associadas ao e-mail no banco local (vindas do Telegram)
            $passwords = DB::table('breach_matches')
                ->where('email', strtolower(trim($email)))
                ->whereNotNull('password')
                ->pluck('password');

            if ($passwords->isEmpty()) return [];

            $results = [];

            foreach ($passwords as $password) {
                $pwned = $this->checkPassword($password);
                if ($pwned > 0) {
                    $results[] = [
                        'source_key'   => 'src_hibp_free',
                        'breach_name'  => 'Incidente de Segurança #' . abs(crc32($email)),
                        'data_exposed' => ['email', 'senha'],
                        'severity'     => 'critical',
                        'breach_date'  => null,
                        'extra'        => "Senha comprometida em {$pwned} vazamentos conhecidos",
                    ];
                    break; // Um resultado por e-mail é suficiente
                }
            }

            return $results;

        } catch (\Throwable $e) {
            Log::error('HibpAdapter error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica se uma senha foi comprometida.
     * Envia apenas os primeiros 5 chars do hash SHA1 — a senha NUNCA sai do servidor.
     * Retorna quantas vezes a senha apareceu em vazamentos (0 = nunca vazou).
     */
    private function checkPassword(string $password): int
    {
        try {
            // Gera hash SHA1 da senha
            $hash   = strtoupper(sha1($password));
            $prefix = substr($hash, 0, 5);   // Primeiros 5 chars
            $suffix = substr($hash, 5);       // Resto — fica local, nunca enviado

            // Consulta API pública — envia só o prefixo
            $response = Http::withHeaders([
                'User-Agent' => 'SecRadar-Monitor/1.0',
            ])->timeout(5)->get("https://api.pwnedpasswords.com/range/{$prefix}");

            if (!$response->ok()) return 0;

            // Procura o sufixo na resposta
            foreach (explode("\n", $response->body()) as $line) {
                [$hashSuffix, $count] = array_pad(explode(':', trim($line)), 2, 0);
                if (strtoupper($hashSuffix) === $suffix) {
                    return (int) $count;
                }
            }

            return 0;

        } catch (\Throwable $e) {
            Log::error('HIBP password check error: ' . $e->getMessage());
            return 0;
        }
    }
}