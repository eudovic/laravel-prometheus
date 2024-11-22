# Laravel Prometheus
Atualmente o biblioteca apenas cria metricas pre configuradas vide arquivo de configuração, porem brevemente ela deve evoluir apra a personalização de métricas.
## Instalação
composer require eudovic/laravel-prometheus

## Publicar o Arquivo de Configuração do Prometheus PHP

Este comando publica o arquivo de configuração do pacote Prometheus PHP no diretório de configuração da aplicação. Isso permite que você personalize as configurações do pacote Prometheus PHP de acordo com as necessidades da sua aplicação.

### Uso

```sh
php artisan vendor:publish --tag=prometheus-laravel-config
```

A opção `--tag=prometheus-laravel-config` especifica que apenas o arquivo de configuração do pacote Prometheus PHP deve ser publicado.

```php
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
        //Redis was not implemented yet
        'redis' => [
            'host' => env('REDIS_HOST'),
            'port' => env('REDIS_PORT'),
            'password' => env('REDIS_PASSWORD'),
            'db' => env('REDIS_DB'),
        ],
    ]
];


````
