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

        $this->addOptionalParams($request, $requestOptions, $params);

        return $params;
    }

    private function addOptionalParams($request, $requestOptions, &$params)
    {
        if ($this->shouldAddLogIP($requestOptions)) {
            $params['ip'] = $request->ip();
        }

        if ($this->shouldAddLogUserAgent($requestOptions)) {
            $params['user_agent'] = $request->header('User-Agent');
        }

        if ($this->shouldAddLogReferer($requestOptions)) {
            $params['referer'] = $request->header('Referer');
        }

        if ($this->shouldAddLogUserId($requestOptions)) {
            $this->addUserIdParam($params);
        }
    }

    private function shouldAddLogIP($requestOptions): bool
    {
        return isset($requestOptions['log_ip']) && $requestOptions['log_ip'];
    }

    private function shouldAddLogUserAgent($requestOptions): bool
    {
        return isset($requestOptions['log_user_agent']) && $requestOptions['log_user_agent'];
    }

    private function shouldAddLogReferer($requestOptions): bool
    {
        return isset($requestOptions['log_referer']) && $requestOptions['log_referer'];
    }

    private function shouldAddLogUserId($requestOptions): bool
    {
        return isset($requestOptions['log_user_id']) && $requestOptions['log_user_id'];
    }

    private function addUserIdParam(&$params)
    {
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

    protected function isHttpRequest($request): bool
    {
        return $request instanceof \Illuminate\Http\Request;
    }
}
