<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscriptionActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && !$user->hasActiveSubscription()) {
            return redirect()->route('subscription.index')
                ->with('info', 'Ative sua assinatura para acessar o painel SecRadar.');
        }

        return $next($request);
    }
}
