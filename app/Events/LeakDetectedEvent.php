<?php

namespace App\Events;

use App\Models\ScanResult;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeakDetectedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ScanResult $result) {}
}
