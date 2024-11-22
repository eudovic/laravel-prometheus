<?php

return [
    'metrics_enabled' => [
        'system_cpu_load_1m' => true,
        'system_cpu_load_5m' => true,
        'system_cpu_load_15m' => true,
        'system_memory_usage_bytes' => true,
        'system_disk_free_bytes' => true,
        'system_disk_total_bytes' => true,
        'db_query_performance' => true,
        'http_request_performance' => true,
    ],
    'stages_enabled' => [
        'local' => true,
        'staging' => true,
        'production' => true,
    ],
    'metrics_storage' => 'local',
    'metrics_storage_options' => [
        'local' => [
            'path' => storage_path('logs/query_log.json'),
        ],
        'redis' => [
            'host' => env('REDIS_HOST'),
            'port' => env('REDIS_PORT'),
            'password' => env('REDIS_PASSWORD'),
            'db' => env('REDIS_DB'),
        ],
    ]
];
