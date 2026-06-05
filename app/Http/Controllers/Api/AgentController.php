<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\RunScanJob;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AgentController extends Controller
{
    /** Status retornado ao agente desktop */
    public function status(Request $request)
    {
        $user = $request->attributes->get('agent_user');

        $user->update(['agent_last_seen_at' => now()]);

        $hasAlert = Alert::where('user_id', $user->id)
            ->where('seen_by_agent', false)
            ->exists();

        $criticalCount = Alert::where('user_id', $user->id)
            ->where('seen_by_agent', false)
            ->where('severity', 'critical')
            ->count();

        return response()->json([
            'has_alert'      => $hasAlert,
            'critical_count' => $criticalCount,
            'subscription'   => $user->hasActiveSubscription(),
            'dashboard_url'  => route('client.dashboard'),
        ]);
    }

    /** Agente solicita varredura manual */
    public function triggerScan(Request $request)
    {
        $user     = $request->attributes->get('agent_user');
        $cacheKey = "agent_scan_{$user->id}";

        if (Cache::has($cacheKey)) {
            return response()->json(['queued' => false, 'reason' => 'cooldown']);
        }

        Cache::put($cacheKey, true, now()->addMinutes(5));
        RunScanJob::dispatch($user->id)->onQueue('high');

        return response()->json(['queued' => true]);
    }

    /** Agente informa que viu os alertas (ícone volta a verde) */
    public function markAlertsSeenByAgent(Request $request)
    {
        $user = $request->attributes->get('agent_user');

        Alert::where('user_id', $user->id)
            ->where('seen_by_agent', false)
            ->update(['seen_by_agent' => true]);

        return response()->json(['ok' => true]);
    }
}
