<?php

namespace Eudovic\PrometheusPHP\Http\Middleware;

use Closure;
use Eudovic\PrometheusPHP\Models\MetricsTokens;

class AuthMetricMiddleware
{
    public function handle($request, Closure $next)
    {
        if (config('prometheus.enable_auth_route') && !$this->checkToken($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

    private function checkToken($request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return false;
        }

        $token = str_replace('Bearer ', '', $token);

        $token = MetricsTokens::where('auth_token', $token)->first();

        if (!$token) {
            return false;
        }

        return true;
    }
}
