<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\ScanResult;
use App\Models\Subscription;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_clients'   => User::where('is_admin', false)->count(),
            'active_subs'     => Subscription::where('status', 'authorized')->count(),
            'total_breaches'  => ScanResult::count(),
            'alerts_today'    => Alert::whereDate('created_at', today())->count(),
            'mrr'             => Subscription::where('status', 'authorized')->sum('plan_amount'),
        ];

        $recentClients = User::where('is_admin', false)
            ->with('subscription')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentClients'));
    }
}
