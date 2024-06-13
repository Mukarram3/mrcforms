<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $backupPath = public_path() . "/backup/";
        $previousBackup = null;

// Get the name of the previous backup file
        $backupFiles = glob($backupPath . "backup-*.zip");
        if (!empty($backupFiles)) {
            rsort($backupFiles); // Sort files by name to get the latest backup first
            $previousBackup = $backupFiles[0]; // Get the latest backup file name
        }

        $filename = "backup-" . Carbon::now()->format('Y-m-d') . ".zip";
        $filePath = $backupPath . $filename;

// Create a new backup
        $command = "mysqldump --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  | gzip > " . $filePath;

        $returnVar = NULL;
        $output  = NULL;

        exec($command, $output, $returnVar);

// Remove the previous backup file if it exists and it's not the same as the newly created backup
        if ($previousBackup && $previousBackup !== $filePath && file_exists($previousBackup)) {
            unlink($previousBackup);
        }
    }
}
