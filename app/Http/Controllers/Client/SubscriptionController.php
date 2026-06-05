<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Jobs\RunScanJob;
use App\Models\MonitoredEmail;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function __construct(private MercadoPagoService $mp) {}

    public function index()
    {
        $user = Auth::user()->load('subscription');
        $mpPublicKey = config('mercadopago.public_key');
        return view('client.subscription', compact('user', 'mpPublicKey'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'card_token' => 'required|string',
            'plan_type'  => 'required|in:personal,corporate',
            'amount'     => 'required|numeric',
        ]);

        $user     = Auth::user();
        $planType = $request->plan_type;
        $amount   = (float) $request->amount;

        $result = $this->mp->createSubscription($user, $request->card_token, $planType, $amount);

        if (isset($result['id'])) {
            return response()->json(['redirect' => route('subscription.callback')]);
        }

        return response()->json(['error' => 'Falha ao criar assinatura.'], 422);
    }

    public function callback(Request $request)
    {
        $user = Auth::user()->load('subscription');

        if ($user->hasActiveSubscription()) {
            if (!$user->monitoredEmails()->where('is_primary', true)->exists()) {
                MonitoredEmail::create([
                    'user_id'    => $user->id,
                    'email'      => $user->email,
                    'is_primary' => true,
                ]);
                RunScanJob::dispatch($user->id)->onQueue('high');
            }

            return redirect()->route('client.dashboard')
                ->with('success', '🛡 SecRadar ativado! Seus dados estão sendo monitorados agora.');
        }

        return view('client.subscription-pending', ['user' => $user]);
    }

    public function cancel(Request $request)
    {
        $user = Auth::user();
        $sub  = $user->subscription;

        if ($sub && $this->mp->cancelSubscription($sub)) {
            $sub->update(['status' => 'cancelled']);
            return back()->with('success', 'Assinatura cancelada.');
        }

        return back()->with('error', 'Não foi possível cancelar. Entre em contato com o suporte.');
    }
}
