<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncCkyc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ckyc';

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
        $filePath = '';
        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->error('CSV file not found or not readable.');
            return 1;
        }
        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = null;

            // Read the CSV file line by line
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data = array_combine($header, $row);
                }
            }

            fclose($handle);
        }
        return 0;
    }
}
