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
        $backupDir = storage_path('app/public/backups/');

        if (! is_dir($backupDir)) {
            mkdir($backupDir);
        }

        $this->createDatabaseBackups(
            $backupDir
        );
        $this->createInvoiceBackups(
            $backupDir
        );
    }

    private function createDatabaseBackups
    (
        string $backupDir
    ): void
    {
        $currentTimeStamp = implode("_", explode(" ", now()));
        $dbConnection = config('database.default');
        $databaseBackupTempFile = 'database_backup_'. $currentTimeStamp . '.temp.sql';
        $databaseBackupFile = 'database_backup_'. $currentTimeStamp .'.sql';
        $databaseBackupPath = $backupDir
            .$databaseBackupFile;
        $databaseBackupTempPath = $backupDir
            .$databaseBackupTempFile;

        if ($dbConnection === 'sqlite') {
            $this->createSqliteDatabaseBackups(
                $databaseBackupTempPath
            );
        }

        if ($dbConnection === 'mysql') {
            $this->createMysqlDatabaseBackups(
                $databaseBackupTempPath
            );
        }

        $this->copyFromTempFileAndDelete(
            $databaseBackupTempPath,
            $databaseBackupPath
        );
    }

    private function createMysqlDatabaseBackups
    (
        string $databaseBackupTempPath
    ):void
    {
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
                ' > '.$databaseBackupTempPath
        );
    }

    private function createSqliteDatabaseBackups
    (
        string $databaseBackupTempPath
    ): void
    {
        $databaseDir = base_path('database/');
        $databaseFile = 'database.sqlite';
        $databasePath = $databaseDir.$databaseFile;

        exec(
            'sqlite3 '.$databasePath.' .dump > '.$databaseBackupTempPath
        );
    }

    private function createInvoiceBackups(
        string $backupDir
    ): void
    {
        $currentTimeStamp = implode("_", explode(" ", now()));
        $zip = new ZipArchive;
        $zipFileName = 'backup_'. $currentTimeStamp  .'.zip';
        $zipTempFileName = 'backup_'. $currentTimeStamp .'.temp.zip';
        $invoiceBackupTempPath = $backupDir.$zipTempFileName;
        $invoiceBackupPath = $backupDir.$zipFileName;

        if ($zip->open($invoiceBackupTempPath, ZipArchive::CREATE) === true) {
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

        $this->copyFromTempFileAndDelete(
            $invoiceBackupTempPath,
            $invoiceBackupPath
        );
    }

    private function copyFromTempFileAndDelete(
        string $path,
        string $target,
    ): void
    {
        File::copy($path, $target);
        File::delete($path);
    }

    public function failed(\Throwable $e): void
    {
        Log::error($e->getMessage());
    }
}
