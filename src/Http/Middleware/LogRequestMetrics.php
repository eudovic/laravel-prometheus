<?php

namespace Eudovic\PrometheusPHP\Http\Middleware;

use Closure;
use Eudovic\PrometheusPHP\Metrics\Logs\LogMetrics;
use Illuminate\Support\Facades\Log;

class LogRequestMetrics
{
    public function handle($request, Closure $next)
    {
        if (!$this->isHttpRequest($request)) {
            return $next($request);
        }

        $startTime = microtime(true);
        $response = $next($request);
        $executionTime = microtime(true) - $startTime;

        $params = $this->buildLogParams($request, $response, $executionTime);

        LogMetrics::log(
            config('prometheus.metrics_storage'),
            'summary',
            'http_request_execution_seconds',
            $executionTime,
            $params
        );

        return $response;
    }

    protected function buildLogParams($request, $response, $executionTime): array
    {
        $requestOptions = config('prometheus.request_metrics_options') ?? [];
        $params = [
            'path' => $request->path(),
            'method' => $request->method(),
            'status' => $response->getStatusCode(),
            'execution_time' => $executionTime,
        ];

        if (isset($requestOptions['log_ip']) && $requestOptions['log_ip']) {
            $params['ip'] = $request->ip();
        }

        if (isset($requestOptions['log_user_agent']) && $requestOptions['log_user_agent']) {
            $params['user_agent'] = $request->header('User-Agent');
        }

        if (isset($requestOptions['log_referer']) && $requestOptions['log_referer']) {
            $params['referer'] = $request->header('Referer');
        }

        if (isset($requestOptions['log_user_id']) && $requestOptions['log_user_id']) {
            $guards = array_keys(config('auth.guards'));
            foreach ($guards as $guard) {
                try {
                    if ($user = auth()->guard($guard)->user()) {
                        $params['user_id'] = $user->id;
                        break;
                    }
                } catch (\Exception $e) {
                    // Log the exception or handle it as needed
                    Log::error("Error fetching user ID for guard {$guard}: " . $e->getMessage());
                }
            }
        }

        return $params;
    }

    protected function isHttpRequest($request): bool
    {
        return $request instanceof \Illuminate\Http\Request;
    }
}
