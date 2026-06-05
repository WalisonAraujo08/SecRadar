<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

// Middleware para rotas do agente desktop
class AgentTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Agent-Token');
        if (!$token) return response()->json(['error' => 'Unauthorized'], 401);

        $user = User::findByAgentToken($token);
        if (!$user || !$user->is_active) return response()->json(['error' => 'Unauthorized'], 401);

        $request->attributes->set('agent_user', $user);
        return $next($request);
    }
}
