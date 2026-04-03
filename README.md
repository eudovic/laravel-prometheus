# Laravel Prometheus

Atualmente, a biblioteca cria métricas pré-configuradas com base no arquivo de configuração. A evolução do pacote é permitir cada vez mais personalização de métricas.

## Instalação

```sh
composer require eudovic/laravel-prometheus
```

## Publicar o Arquivo de Configuração do Prometheus PHP

Este comando publica o arquivo de configuração do pacote Prometheus PHP no diretório de configuração da aplicação. Isso permite que você personalize as configurações do pacote Prometheus PHP de acordo com as necessidades da sua aplicação.

### Uso

```sh
php artisan vendor:publish --tag=prometheus-php-config
```

A opção `--tag=prometheus-php-config` especifica que apenas o arquivo de configuração do pacote Prometheus PHP deve ser publicado.

```php
<?php
return [
    'enable_auth_route' => true,
    'metrics_enabled' => [
        'system_cpu_load_1m' => true,
        'system_cpu_load_5m' => true,
        'system_cpu_load_15m' => true,
        'system_memory_usage_bytes' => true,
        'system_disk_free_bytes' => true,
        'system_disk_total_bytes' => true,
        'db_query_performance' => true,
        'http_request_performance' => true,
        'application_errors' => true,
        'job_performance' => false,
    ],
    'request_metrics_options' => [
        'log_ip' => true,
        'log_user_agent' => true,
        'log_referer' => true,
        'log_user_id' => true,
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
```

## Autenticando o Endpoint

- No arquivo de configuração, defina `enable_auth_route` como `true`.
- Rode `php artisan migrate`.
- Rode `php artisan eudovic:prometheus-make-token` para obter o token.

## Versionamento e Release

Este projeto usa tags Git no formato SemVer (`vMAJOR.MINOR.PATCH`).

Exemplo da próxima versão: `v1.2.1`.

### Como criar a tag `v1.2.1`

1. Garanta que você está na branch correta e com o repositório atualizado.

```sh
git checkout main
git pull origin main
```

2. Garanta que não existem mudanças pendentes.

```sh
git status
```

3. Crie a tag anotada da versão.

```sh
git tag -a v1.2.1 -m "Release v1.2.1"
```

4. Envie a tag para o repositório remoto.

```sh
git push origin v1.2.1
```

5. (Opcional, recomendado) Publique um Release no GitHub usando essa tag.

### Comandos úteis

Listar tags:

```sh
git tag --list
```

Ver detalhes da tag:

```sh
git show v1.2.1
```

Excluir tag local (se criou errado):

```sh
git tag -d v1.2.1
```

Excluir tag remota:

```sh
git push origin :refs/tags/v1.2.1
```


