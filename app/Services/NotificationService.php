<?php

namespace App\Services;

use App\Models\ScanResult;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        private WhatsAppService $whatsApp
    ) {}

    public function notifyLeak(ScanResult $result): void
    {
        $user = $result->user;

        // E-mail
        if (!$result->notified_email) {
            try {
                Mail::send(
                    'emails.alert-leak',
                    ['result' => $result, 'user' => $user],
                    function ($m) use ($user, $result) {
                        $label = $result->severity === 'critical' ? '⚠ CRÍTICO' : '⚡ ALERTA';
                        $m->to($user->email, $user->name)
                          ->subject("{$label} — SecRadar detectou uma exposição de dados");
                    }
                );
                $result->update(['notified_email' => true]);
            } catch (\Throwable $e) {
                Log::error('Email notification failed: ' . $e->getMessage());
            }
        }

        // WhatsApp
        if (!$result->notified_whatsapp && $user->phone) {
            $sent = $this->whatsApp->sendAlert(
                $user->phone,
                $user->name,
                $result->severity
            );
            if ($sent) $result->update(['notified_whatsapp' => true]);
        }
    }
}
