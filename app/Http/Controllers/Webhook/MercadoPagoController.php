<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoController extends Controller
{
    public function __construct(private MercadoPagoService $mp) {}

    public function handle(Request $request)
    {
        // Valida assinatura do webhook
        $signature = $request->header('X-Signature');
        $secret    = config('mercadopago.webhook_secret');

        if ($secret && $signature) {
            [$ts, $hash] = array_pad(explode(',', $signature, 2), 2, null);
            $ts   = str_replace('ts=', '', $ts ?? '');
            $hash = str_replace('v1=', '', $hash ?? '');
            $body = $request->getContent();
            $expected = hash_hmac('sha256', "id={$request->input('data.id')}&ts={$ts}", $secret);

            if (!hash_equals($expected, $hash)) {
                Log::warning('MP webhook: invalid signature');
                return response('Unauthorized', 401);
            }
        }

        try {
            $this->mp->handleWebhook($request->all());
        } catch (\Throwable $e) {
            Log::error('MP webhook error: ' . $e->getMessage());
        }

        return response('OK', 200);
    }
}
