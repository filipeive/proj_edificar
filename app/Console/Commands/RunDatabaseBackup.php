<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class RunDatabaseBackup extends Command
{
    protected $signature = 'database:backup';
    protected $description = 'Cria um backup do banco de dados em storage/app/backups';

    public function handle(): int
    {
        $driver = config('database.default');
        $timestamp = now()->format('Y_m_d_His');
        $backupDir = storage_path('app/backups');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        if ($driver === 'sqlite') {
            $path = config('database.connections.sqlite.database');
            if (!file_exists($path)) {
                $this->error('Base de dados SQLite não encontrada.');
                return self::FAILURE;
            }
            $filename = "backup_{$timestamp}.sqlite";
            copy($path, $backupDir . DIRECTORY_SEPARATOR . $filename);
            $this->info('Backup SQLite criado com sucesso.');
            return self::SUCCESS;
        }

        if ($driver !== 'mysql') {
            $this->error('Backup automático disponível apenas para MySQL/SQLite.');
            return self::FAILURE;
        }

        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $filename = "backup_{$database}_{$timestamp}.sql";
        $fullPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $process = new Process([
            'mysqldump',
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $username,
            $database,
        ]);
        $process->setEnv(['MYSQL_PWD' => $password]);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Não foi possível gerar o backup. Verifique se o mysqldump está instalado.');
            return self::FAILURE;
        }

        file_put_contents($fullPath, $process->getOutput());

        $this->info('Backup MySQL criado com sucesso.');
        return self::SUCCESS;
    }
}
