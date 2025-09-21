<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup {--compress : Compress the backup}';
    protected $description = 'Create a backup of the database';

    public function handle()
    {
        $this->info('Creating database backup...');

        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        // Ensure backup directory exists
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($database),
            escapeshellarg($path)
        );

        $process = Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Backup failed: ' . $process->getErrorOutput());
            return 1;
        }

        // Compress if requested
        if ($this->option('compress')) {
            $compressedPath = $path . '.gz';
            $compressCommand = "gzip {$path}";
            $compressProcess = Process::fromShellCommandline($compressCommand);
            $compressProcess->run();

            if ($compressProcess->isSuccessful()) {
                $filename = $filename . '.gz';
                $this->info("Database backup compressed: {$filename}");
            }
        }

        // Clean up old backups (keep last 7 days)
        $this->cleanOldBackups();

        $this->info("Database backup created successfully: {$filename}");
        activity()->log("Database backup created: {$filename}");

        return 0;
    }

    private function cleanOldBackups()
    {
        $files = Storage::files('backups');
        $cutoffDate = now()->subDays(7);

        foreach ($files as $file) {
            $fileTime = Storage::lastModified($file);
            if ($fileTime < $cutoffDate->timestamp) {
                Storage::delete($file);
                $this->line("Deleted old backup: " . basename($file));
            }
        }
    }
}
