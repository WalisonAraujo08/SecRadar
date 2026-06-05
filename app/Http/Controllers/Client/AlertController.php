<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlertController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $alerts = $user->alerts()->with('scanResult.monitoredEmail')->paginate(25);
        return view('client.alerts', compact('alerts', 'user'));
    }

    public function markRead(int $id)
    {
        $alert = Auth::user()->alerts()->findOrFail($id);
        DB::table('alerts')->where('id', $alert->id)->update(['read' => 1]);
        return back();
    }

    public function markAllRead()
    {
        DB::table('alerts')
            ->where('user_id', Auth::id())
            ->where('read', 0)
            ->update(['read' => 1]);
        return back()->with('success', 'Todos os alertas marcados como lidos.');
    }
}
