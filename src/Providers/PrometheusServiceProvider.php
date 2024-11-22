<?php

namespace Eudovic\PrometheusPHP\Providers;

use Eudovic\PrometheusPHP\Metrics\Logs\LocalLogs;
use Eudovic\PrometheusPHP\Metrics\Logs\LogMetrics;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class PrometheusServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/prometheus.php',
            'prometheus'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/prometheus.php' => config_path('prometheus.php'),
        ], 'prometheus-php-config');

        $this->dbListener();
        $this->requestListener();
        
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    private function dbListener()
    {
        DB::listen(function (QueryExecuted $query) {
            $executionTime = $query->time / 1000;
            $querySql = $query->sql;
            LogMetrics::log(config('prometheus.metrics_storage'), 'summary', 'db_query_execution_seconds', $executionTime, ['query' => $querySql]);
        });
    }

    private function requestListener()
    {
        $request = request();
        $path = $request->path();
        $method = $request->method();
        $executionTime = microtime(true) - LARAVEL_START;
        LogMetrics::log(config('prometheus.metrics_storage'), 'summary', 'http_request_execution_seconds', $executionTime, ['path' => $path, 'method' => $method]);
    }
}