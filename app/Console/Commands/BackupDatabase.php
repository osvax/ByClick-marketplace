<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup {--path= : Custom destination file}';

    protected $description = 'Create a database backup in storage/backups';

    /**
     * Back up SQLite by copying the file and MySQL/MariaDB via mysqldump.
     */
    public function handle(): int
    {
        $connection = (string) config('database.default');
        $config = (array) config('database.connections.'.$connection);
        $driver = (string) ($config['driver'] ?? $connection);
        $directory = storage_path('backups');
        File::ensureDirectoryExists($directory);

        $destination = (string) ($this->option('path')
            ?: $directory.'/'.$connection.'-'.now()->format('Ymd-His').'.sql');

        if ($driver === 'sqlite') {
            $database = (string) ($config['database'] ?? '');

            if ($database === '' || $database === ':memory:' || ! is_file($database)) {
                $this->error('SQLite database file was not found.');

                return self::FAILURE;
            }

            File::copy($database, $destination);
            $this->info('SQLite backup created: '.$destination);

            return self::SUCCESS;
        }

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            $this->error('Unsupported database driver: '.$driver);

            return self::FAILURE;
        }

        $process = new Process([
            (string) config('database.dump_binary', 'mysqldump'),
            '--single-transaction',
            '--quick',
            '--lock-tables=false',
            '--host='.($config['host'] ?? '127.0.0.1'),
            '--port='.($config['port'] ?? '3306'),
            '--user='.($config['username'] ?? 'root'),
            '--database='.($config['database'] ?? ''),
        ]);

        // Avoid putting the password in the process command line.
        if (! empty($config['password'])) {
            $process->setEnv(['MYSQL_PWD' => (string) $config['password']]);
        }

        $process->run();

        if (! $process->isSuccessful()) {
            $this->error('Backup failed: '.$process->getErrorOutput());

            return self::FAILURE;
        }

        File::put($destination, $process->getOutput());

        if (File::size($destination) === 0) {
            File::delete($destination);
            $this->error('Backup file is empty.');

            return self::FAILURE;
        }

        $this->info('Database backup created: '.$destination);

        return self::SUCCESS;
    }
}
