<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class CreateBackup implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $zipFileName = 'backup.zip';
        $zip = new ZipArchive;
        $dbConnection = config('database.default');
        $backupDir = storage_path('app/public/backups/');
        $databaseBackupFile = 'database_backup.sql';
        $databaseBackupPath = $backupDir
            .$databaseBackupFile;

        if (! is_dir($backupDir)) {
            mkdir($backupDir);
        }

        if ($dbConnection === 'sqlite') {
            $databaseDir = base_path('database/');
            $databaseFile = 'database.sqlite';
            $databasePath = $databaseDir.$databaseFile;

            exec(
                'sqlite3 '.$databasePath.' .dump > '.$databaseBackupPath
            );
        }

        if ($dbConnection === 'mysql') {
            $baseConfig = 'database.connections.mysql';
            $dbHost = config($baseConfig.'.host');
            $dbDatabase = config($baseConfig.'.database');
            $dbUserName = config($baseConfig.'.username');
            $dbPassword = config($baseConfig.'.password');

            exec(
                'mysqldump -h'.$dbHost.
                ' -u '.$dbUserName.
                ' -p '.$dbPassword.
                ' '.$dbDatabase.
                ' > '.$databaseBackupPath
            );
        }

        $invoiceBackupPath = $backupDir.$zipFileName;

        if ($zip->open($invoiceBackupPath, ZipArchive::CREATE) === true) {
            $invoiceFiles = File::files(
                storage_path(
                    'app/public/invoices/'
                )
            );

            foreach ($invoiceFiles as $invoiceFile) {
                $zip->addFile($invoiceFile, basename($invoiceFile));
            }

            $zip->close();
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error($e->getMessage());
    }
}
