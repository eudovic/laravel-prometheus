<?php

namespace Eudovic\PrometheusPHP\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Eudovic\PrometheusPHP\Metrics\Logs\LogMetrics;
use Eudovic\PrometheusPHP\Services\AppMetrics;

class Handler extends ExceptionHandler
{
    public function report(Throwable $exception)
    {
        parent::report($exception);

        // Registrar erros no Prometheus
        $this->logErrorToPrometheus($exception);
    }

    private function logErrorToPrometheus(Throwable $exception): void
    {
        $errorMessage = substr($exception->getMessage(), 0, 200);
        $errorClass = get_class($exception);
        $errorFile = $exception->getFile();
        $errorLine = $exception->getLine();

        if (AppMetrics::isMetricEnabled('application_errors')) {
            LogMetrics::log(
                'local',
                'gauge',
                'application_errors',
                1,
                [
                    'exception' => $errorClass,
                    'file' => $errorFile,
                    'line' => $errorLine,
                    'message' => $errorMessage,
                ]
            );
        }
    }
}
