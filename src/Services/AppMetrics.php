<?php

namespace Eudovic\PrometheusPHP\Services;

use Eudovic\PrometheusPHP\Metrics\Logs\LocalLogs;
use Eudovic\PrometheusPHP\Metrics\Types\Gauge;
use Eudovic\PrometheusPHP\Metrics\Types\Summary;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AppMetrics
{
    private const METRICS_STORAGE_LOCAL = 'local';
    private const DB_QUERY_METRIC_KEY = 'db_query_execution_seconds';
    private const HTTP_REQUEST_METRIC_KEY = 'http_request_execution_seconds';
    private const APP_ERROR_METRIC_KEY = 'application_errors';

    public static function collectMetrics(): string
    {
        try {

            $metrics = '';
            $logPath = config('prometheus.metrics_storage_options.local.path');

            if (self::isMetricEnabled('db_query_performance')) {
                $dbMetrics = self::processMetrics(self::DB_QUERY_METRIC_KEY, $logPath, fn($m) => $m['params']['query']);
                foreach ($dbMetrics as $metric) {
                    $metrics .= Summary::addMetric(self::DB_QUERY_METRIC_KEY, $metric, 'Tempo de execução das queries do banco de dados');
                }
            }

            if (self::isMetricEnabled('http_request_performance')) {
                $requestMetrics = self::processMetrics(self::HTTP_REQUEST_METRIC_KEY, $logPath, fn($m) => $m['params']['path'] . ' ' . $m['params']['method']);
                foreach ($requestMetrics as $metric) {
                    $metrics .= Summary::addMetric(self::HTTP_REQUEST_METRIC_KEY, $metric, 'Tempo de execução das requisições HTTP');
                }
            }

            if (self::isMetricEnabled('application_errors')) {
                $errorMetrics = self::processMetrics(self::APP_ERROR_METRIC_KEY, $logPath, fn($m) => $m['params']['exception']);
                foreach ($errorMetrics as $metric) {
                    $metrics .= Summary::addMetric(self::APP_ERROR_METRIC_KEY, $metric, 'Erros na aplicação');
                }
            }

            if (self::isMetricEnabled('job_performance')) {
                $jobMetrics = self::processMetrics('job_execution_seconds', $logPath, fn($m) => $m['params']['job']);
                foreach ($jobMetrics as $metric) {
                    $metrics .= Summary::addMetric('job_execution_seconds', $metric, 'Tempo de execução dos jobs');
                }
            }

            if (self::isLocalStorageEnabled()) {
                LocalLogs::clear($logPath);
            }

            return $metrics;
        } catch (Exception $e) {
            Log::error('Error collecting metrics', ['error' => $e->getMessage()]);
        }
    }

    private static function processMetrics(string $metricKey, string $logPath, callable $groupKeyCallback): array
    {
        $logs = LocalLogs::read($logPath);
        if (!$logs || empty($logs['app'])) {
            return [];
        }
    
        $grouped = [];
        $output = [];
        foreach ($logs['app'] as $metric) {
            if ($metric['key'] !== $metricKey) {
                continue;
            }
    
            $groupKey = $groupKeyCallback($metric);
            $grouped[$groupKey][] = [
                'value' => $metric['value'],
                'params' => $metric['params'],
            ];
        }
    
        foreach ($grouped as $payload) {
           $output[] = MetricsProcessor::process($metricKey, $payload);
        }

        return $output;
    }
    

    public static function isMetricEnabled(string $metric): bool
    {
        $metricsEnabled = Config::get('prometheus.metrics_enabled');
        return $metricsEnabled[$metric] ?? false;
    }

    private static function isLocalStorageEnabled(): bool
    {
        return config('prometheus.metrics_storage') === self::METRICS_STORAGE_LOCAL;
    }
}
