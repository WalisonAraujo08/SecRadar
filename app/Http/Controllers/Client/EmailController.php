<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Jobs\RunScanJob;
use App\Models\MonitoredEmail;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{
    public function __construct(private MercadoPagoService $mp) {}

    public function index()
    {
        $user   = Auth::user()->load('monitoredEmails');
        $emails = $user->monitoredEmails()->orderBy('is_primary', 'desc')->get();
        return view('client.emails', compact('user', 'emails'));
    }

    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = Auth::user();

        // Limite de segurança
        if ($user->monitoredEmails()->count() >= 6) {
            return back()->with('error', 'Limite de 10 e-mails monitorados atingido.');
        }

        // Verificar duplicata
        if ($user->monitoredEmails()->where('email', $request->email)->exists()) {
            return back()->with('error', 'Este e-mail já está sendo monitorado.');
        }

        // Cobrar adicional via Mercado Pago
        if (!$this->mp->addExtraEmailCharge($user)) {
            return back()->with('error', 'Não foi possível adicionar o e-mail. Verifique sua assinatura.');
        }

        $monitored = MonitoredEmail::create([
            'user_id'  => $user->id,
            'email'    => $request->email,
            'is_primary' => false,
        ]);

        // Dispara varredura imediata no novo e-mail
        RunScanJob::dispatch($user->id)->onQueue('high');

        return back()->with('success', "E-mail {$request->email} adicionado! Varredura iniciada.");
    }

    public function destroy(int $id)
    {
        $user  = Auth::user();
        $email = MonitoredEmail::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        if ($email->is_primary) {
            return back()->with('error', 'O e-mail principal não pode ser removido.');
        }

        // Ajusta cobrança no MP
        $this->mp->removeExtraEmailCharge($user);

        $email->update(['status' => 'removed']);

        return back()->with('success', 'E-mail removido do monitoramento.');
    }
}
