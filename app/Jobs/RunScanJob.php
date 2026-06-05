<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ScanEngine\ScanEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(public int $userId) {}

    public function handle(ScanEngine $engine): void
    {
        $user = User::with('monitoredEmails')->find($this->userId);

        if (!$user || !$user->hasActiveSubscription()) return;

        foreach ($user->monitoredEmails as $email) {
            if ($email->status !== 'active') continue;
            $engine->scanEmail($email);
        }
    }
}
