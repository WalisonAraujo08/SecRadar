<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScanResult;

class ScanLogsController extends Controller
{
    public function index()
    {
        $logs = ScanResult::with(['user', 'monitoredEmail'])
            ->orderByDesc('detected_at')
            ->paginate(50);

        return view('admin.scan-logs', compact('logs'));
    }
}
