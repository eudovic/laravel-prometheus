<?php

namespace Eudovic\PrometheusPHP\Metrics\Logs;

use Illuminate\Support\Facades\Config;

class LocalLogs
{
    public static function log(string $type, string $key, string $value, array $params = [])
    {

        if (!$type) {
            $type = 'gauge';
        }

        $filePath = storage_path('logs/query_log.json');
        if (!file_exists($filePath)) {
            file_put_contents($filePath, json_encode([]));
        }

        $logData = json_decode(file_get_contents($filePath), true);
        $logData['app'][] = [
            'type' => $type,
            'key' => $key,
            'value' => $value,
            'params' => $params,
        ];
        file_put_contents($filePath, json_encode($logData));
    }

    public static function path()
    {
        return config('prometheus.metrics_storage_options.local.path');
    }

    public static function read(string $path): ?array
    {
        if (!file_exists($path)) {
            return null;
        }

        $content = file_get_contents($path);
        return json_decode($content, true) ?: null;
    }

    public static function clear(string $path): void
    {
        if (file_exists($path)) {
            file_put_contents($path, json_encode(['app' => []]));
        }
    }

    private static function isMetricEnabled(string $metric): bool
    {
        $metricsEnabled = Config::get('prometheus.metrics_enabled');
        return $metricsEnabled[$metric] ?? false;
    }
}
