<?php

namespace App\Providers;

use App\Events\LeakDetectedEvent;
use App\Listeners\LeakDetectedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        LeakDetectedEvent::class => [
            LeakDetectedListener::class,
        ],
    ];
}
