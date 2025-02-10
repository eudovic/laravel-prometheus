<?php

namespace Eudovic\PrometheusPHP\Metrics\Logs;

class LogMetrics
{
    public static function log(
        string $storage,
        string $type,
        string $name,
        string|float $value,
        array $params = []
        )
    {
        if (self::isLocalStorage($storage)) {
            LocalLogs::log($type, $name, $value, $params);
            return;
        }

        if ($storage == 'redis') {
            //not implemented yet
        }
    }

    private static function isLocalStorage(string $storage): bool
    {
        return !$storage || $storage == 'local';
    }
}
