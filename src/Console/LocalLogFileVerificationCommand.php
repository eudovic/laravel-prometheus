<?php
namespace Eudovic\PrometheusPHP\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LocalLogFileVerificationCommand extends Command
{
    protected $signature = 'eudovic:prometheus-local-log-verify';
    protected $description = "Verifica se o arquivo app\storage\logs\query_log.json existe," . PHP_EOL
                        . "cria se necessário e pergunta se deseja apagar o conteúdo.";


    public function handle()
    {
        $logFilePath = storage_path('logs/query_log.json');

        if (!File::exists($logFilePath)) {
            if ($this->confirm('O arquivo query_log.json não existe. Deseja criá-lo?')) {
                File::put($logFilePath, '');
                $this->info('Arquivo query_log.json criado com sucesso.');
            } else {
                $this->info('Arquivo query_log.json não foi criado.');
            }
        } else {
            $fileSize = File::size($logFilePath) / (1024 * 1024); // Convert bytes to MB
            $this->info('O arquivo query_log.json já existe. Tamanho: ' . number_format($fileSize, 2) . ' MB.');
            
            if ($this->confirm('Deseja apagar o conteúdo dele?')) {
                File::put($logFilePath, '');
                $this->info('Conteúdo do arquivo query_log.json apagado com sucesso.');
            } else {
                $this->info('Conteúdo do arquivo query_log.json não foi apagado.');
            }
        }
    }
}
