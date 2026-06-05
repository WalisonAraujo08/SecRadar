<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    private string $token = "";
    private string $planId = "";
    private string $baseUrl = 'https://api.mercadopago.com';

    public function __construct()
    {
        $this->token  = config('mercadopago.access_token', '');
        $this->planId = config('mercadopago.plan_id_base', '');
    }

    public function createSubscription(User $user, string $cardToken, string $planType = 'personal', float $amount = 29.90): array
    {
        $reason = match($planType) {
            'corporate' => 'SecRadar Azuron — Plano Corporativo',
            default     => 'SecRadar Azuron — Plano Pessoal',
        };

        $response = Http::withToken($this->token)
            ->post("{$this->baseUrl}/preapproval", [
                'preapproval_plan_id' => $this->planId,
                'payer_email'         => $user->email,
                'card_token_id'       => $cardToken,
                'back_url'            => route('subscription.callback'),
                'external_reference'  => $user->id . '|' . $planType,
                'reason'              => $reason,
                'transaction_amount'  => $amount,
            ]);

        return $response->json();
    }

    public function addExtraEmailCharge(User $user): bool
    {
        $sub = $user->subscription;
        if (!$sub || $sub->isCorporate()) return false;

        $newAmount = 29.90 + (($sub->extra_emails + 1) * 8.90);

        $response = Http::withToken($this->token)
            ->patch("{$this->baseUrl}/preapproval/{$sub->mp_subscription_id}", [
                'transaction_amount' => round($newAmount, 2),
                'reason'             => 'SecRadar — E-mail de monitoramento adicional',
            ]);

        if ($response->ok()) {
            $sub->increment('extra_emails');
            $sub->update(['plan_amount' => $newAmount]);
            return true;
        }

        Log::error('MP addExtraEmail failed', ['response' => $response->body()]);
        return false;
    }

    public function removeExtraEmailCharge(User $user): bool
    {
        $sub = $user->subscription;
        if (!$sub || $sub->extra_emails <= 0 || $sub->isCorporate()) return false;

        $newAmount = 29.90 + (($sub->extra_emails - 1) * 8.90);

        $response = Http::withToken($this->token)
            ->patch("{$this->baseUrl}/preapproval/{$sub->mp_subscription_id}", [
                'transaction_amount' => round($newAmount, 2),
            ]);

        if ($response->ok()) {
            $sub->decrement('extra_emails');
            $sub->update(['plan_amount' => $newAmount]);
            return true;
        }
        return false;
    }

    public function cancelSubscription(Subscription $sub): bool
    {
        $response = Http::withToken($this->token)
            ->patch("{$this->baseUrl}/preapproval/{$sub->mp_subscription_id}", [
                'status' => 'cancelled',
            ]);

        return $response->ok();
    }

    public function handleWebhook(array $payload): void
    {
        $type = $payload['type'] ?? '';
        if (!in_array($type, ['subscription_preapproval', 'payment'])) return;

        $mpId = $payload['data']['id'] ?? null;
        if (!$mpId) return;

        if ($type === 'subscription_preapproval') {
            $detail = Http::withToken($this->token)
                ->get("{$this->baseUrl}/preapproval/{$mpId}")
                ->json();

            $sub = Subscription::where('mp_subscription_id', $mpId)->first();

            if (!$sub) {
                $ref = $detail['external_reference'] ?? null;
                if (!$ref) return;

                // Extrai user_id e plan_type da referência
                [$userId, $planType] = array_pad(explode('|', $ref), 2, 'personal');
                $amount = $detail['transaction_amount'] ?? 29.90;

                Subscription::create([
                    'user_id'            => $userId,
                    'mp_subscription_id' => $mpId,
                    'mp_payer_id'        => $detail['payer_id'] ?? null,
                    'status'             => $detail['status'] ?? 'pending',
                    'plan_type'          => $planType,
                    'plan_amount'        => $amount,
                    'email_limit'        => $planType === 'corporate' ? 20 : 6,
                    'next_billing_date'  => $detail['next_payment_date'] ?? null,
                ]);
                return;
            }

            $sub->update([
                'status'            => $detail['status'] ?? $sub->status,
                'next_billing_date' => $detail['next_payment_date'] ?? $sub->next_billing_date,
            ]);
        }
    }
}
