<?php

namespace App\Services\ScanEngine;

use App\Events\LeakDetectedEvent;
use App\Models\MonitoredEmail;
use App\Models\ScanResult;
use App\Services\ScanEngine\Adapters\HibpAdapter;
use App\Services\ScanEngine\Adapters\LocalDatabaseAdapter;
use Illuminate\Support\Facades\Log;

class ScanEngine
{
    private array $adapters;

    public function __construct(
        private LocalDatabaseAdapter $localDb,
        private HibpAdapter          $hibp,
    ) {
        $this->adapters = [
            $this->localDb,  // Base própria do Telegram — gratuita e ilimitada
            $this->hibp,     // HIBP Pwned Passwords — gratuita e ilimitada
        ];
    }

    public function scanEmail(MonitoredEmail $monitored): array
    {
        $newResults = [];

        foreach ($this->adapters as $adapter) {
            try {
                $breaches = $adapter->check($monitored->email);

                foreach ($breaches as $breach) {
                    $exists = ScanResult::where('monitored_email_id', $monitored->id)
                        ->where('source_key', $breach['source_key'])
                        ->where('breach_name', $breach['breach_name'])
                        ->exists();

                    if ($exists) continue;

                    $result = ScanResult::create([
                        'user_id'            => $monitored->user_id,
                        'monitored_email_id' => $monitored->id,
                        'source_key'         => $breach['source_key'],
                        'breach_name'        => $breach['breach_name'],
                        'data_exposed'       => $breach['data_exposed'],
                        'severity'           => $breach['severity'],
                        'detected_at'        => now(),
                        'breach_date'        => $breach['breach_date'] ?? null,
                    ]);

                    event(new LeakDetectedEvent($result));
                    $newResults[] = $result;
                }

            } catch (\Throwable $e) {
                Log::error('ScanEngine error: ' . get_class($adapter) . ' — ' . $e->getMessage());
            }
        }

        $monitored->update(['last_scanned_at' => now()]);
        return $newResults;
    }
}