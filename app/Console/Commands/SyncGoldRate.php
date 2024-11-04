<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\KycService; // Use your KycService here

class SyncGoldRate extends Command
{
    // The name and signature of the command
    protected $signature = 'sync:goldrate';

    // The console command description
    protected $description = 'Sync gold rates using KycService';

    protected $kycService;

    // Constructor to inject KycService
    public function __construct(KycService $kycService)
    {
        parent::__construct();
        $this->kycService = $kycService; // Assign the service to a class property
    }

    // The main logic of the command
    public function handle()
    {
        $this->info('Starting gold rate sync...');

        try {
            // Call the service method to update gold rates
           $res =  $this->kycService->goldRateUpdate();
                dd($res);
            // Inform the user the sync is completed
            $this->info('Gold rate sync completed successfully.');
        } catch (\Exception $e) {
            // Handle errors and log them if necessary
            $this->error('Failed to sync gold rates: ' . $e->getMessage());
        }
    }
}

