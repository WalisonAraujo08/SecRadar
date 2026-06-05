<?php

namespace App\Listeners;

use App\Events\LeakDetectedEvent;
use App\Models\Alert;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LeakDetectedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'high';
    public int $tries = 3;

    public function __construct(private NotificationService $notifications) {}

    public function handle(LeakDetectedEvent $event): void
    {
        $result = $event->result;

        // Evita duplicata — só cria alerta se não existir para este scan_result
        $exists = Alert::where('scan_result_id', $result->id)->exists();
        if ($exists) return;

        Alert::create([
            'user_id'        => $result->user_id,
            'scan_result_id' => $result->id,
            'severity'       => $result->severity,
        ]);

        $this->notifications->notifyLeak($result);
    }
}