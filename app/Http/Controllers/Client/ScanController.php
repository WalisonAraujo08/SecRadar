<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Jobs\RunScanJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ScanController extends Controller
{
    public function index()
    {
        $user    = Auth::user()->load(['monitoredEmails', 'scanResults']);
        $results = $user->scanResults()->with('monitoredEmail')->latest()->paginate(20);
        return view('client.scan', compact('user', 'results'));
    }

    public function start(Request $request)
    {
        $user = Auth::user();

        // Limita varredura manual a 1 por vez (cooldown 5 min)
        $cacheKey = "scan_cooldown_{$user->id}";
        if (Cache::has($cacheKey)) {
            return back()->with('error', 'Aguarde alguns minutos antes de iniciar outra varredura manual.');
        }

        Cache::put($cacheKey, true, now()->addMinutes(5));
        RunScanJob::dispatch($user->id)->onQueue('high');

        return back()->with('success', '✔ Varredura iniciada! Os resultados aparecerão em instantes.');
    }

    public function status()
    {
        $user       = Auth::user();
        $inProgress = Cache::has("scan_cooldown_{$user->id}");

        return response()->json([
            'in_progress'  => $inProgress,
            'last_scan'    => $user->monitoredEmails->max('last_scanned_at')?->diffForHumans(),
            'total_results'=> $user->scanResults()->count(),
        ]);
    }
}
