<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $API_USERS = [
            "collector" => "gp2026",
            "admin" => "collector_admin_2026"
        ];

        $user = $request->getUser();
        $pass = $request->getPassword();

        if (!$user || !isset($API_USERS[$user]) || $API_USERS[$user] !== $pass) {
            return response()->json(["error" => "Authentication required"], 401, [
                'WWW-Authenticate' => 'Basic realm="Collector API"'
            ]);
        }

        return $next($request);
    }
}
