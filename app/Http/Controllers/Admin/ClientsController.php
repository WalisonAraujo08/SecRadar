<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class ClientsController extends Controller
{
    public function index()
    {
        $clients = User::where('is_admin', false)
            ->with(['subscription', 'monitoredEmails'])
            ->withCount(['scanResults', 'alerts'])
            ->latest()
            ->paginate(20);

        return view('admin.clients', compact('clients'));
    }

    public function show(int $id)
    {
        $client = User::where('is_admin', false)
            ->with(['subscription', 'monitoredEmails', 'alerts.scanResult'])
            ->findOrFail($id);

        return view('admin.client-detail', compact('client'));
    }

    public function toggle(int $id)
    {
        $client = User::where('is_admin', false)->findOrFail($id);
        $client->update(['is_active' => !$client->is_active]);
        return back()->with('success', 'Status do cliente atualizado.');
    }
}
