<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['monitoredEmails', 'alerts.scanResult', 'subscription']);

        $stats = [
            'emails_monitored' => $user->monitoredEmails->count(),
            'total_breaches'   => $user->scanResults()->count(),
            'critical_alerts'  => $user->alerts()->where('severity', 'critical')->where('read', false)->count(),
            'last_scan'        => $user->monitoredEmails->max('last_scanned_at'),
        ];

        $recentAlerts = $user->alerts()->with('scanResult.monitoredEmail')
            ->latest()->limit(10)->get();

        return view('client.dashboard', compact('user', 'stats', 'recentAlerts'));
    }

    public function downloadAgent()
    {
        $user = Auth::user();

        // Gera token do agente se não existir
        if (!$user->agent_token) {
            $token = $user->generateAgentToken();
            // Token limpo salvo em sessão para exibir UMA ÚNICA VEZ
            session(['agent_token_display' => $token]);
        }

        return view('client.download-agent', ['user' => $user]);
    }
}
