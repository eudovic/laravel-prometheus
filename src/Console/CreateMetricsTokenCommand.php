<?php
namespace Eudovic\PrometheusPHP\Console;

use Eudovic\PrometheusPHP\Models\MetricsTokens;
use Illuminate\Console\Command;

class CreateMetricsTokenCommand extends Command
{
    protected $signature = 'eudovic:prometheus-make-token';
    protected $description = 'Cria um token de autenticação para acesso aos métricas do Prometheus';

    public function handle()
    {

        MetricsTokens::truncate();
        
        $token = MetricsTokens::create([
            'auth_token' => bin2hex(random_bytes(32)),
        ]);

        $this->info('Token created: ' . $token->auth_token);
        return 0;
    }
}
