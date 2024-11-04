<?php

namespace App\Console\Commands;

use App\Models\LoanAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class KycDoc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:kycdoc';

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
        Log::info('File moved started');

        $sourceDisk = 'external1';
        $destinationDisk = 'external2';


        $sourcePath = 'testingkyc/';


        //dd($source);
        // Get all files in the source directory
        $files = Storage::disk('external1')->files($sourcePath);
        //$files = scandir($source);

        if (empty($files)) {
            $this->info('No files found in the source directory.');
            return 0;
        }
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filename = basename($file); // Get the file name
                $folder_name = explode(' ', $filename)[0];
                //dd($folder_name);
                $loan_account = LoanAccount::where('ucic', $folder_name)->get()[0];
                if (isset($loan_account->loan_id)) {
                    $loan_id = $loan_account->loan_id;
                    if (!Storage::exists($loan_id)) {
                        Storage::disk($destinationDisk)->makeDirectory($loan_id);
                    }
                    $destinationPath = $loan_id . '/';
                    $fileContent = Storage::disk($sourceDisk)->get($sourcePath . basename($file));

                    //dd($fileContent);
                    Storage::disk($destinationDisk)->put($destinationPath . basename($file), $fileContent);

                    //Delete files from source
                    //Storage::disk($sourceDisk)->delete($sourcePath);

                    /*
                    if (Storage::move($source . '/' . $file, $loan_id . '/' . $filename)) {
                        $this->info("Moved file: $file to ");
                    }
                    */
                    Log::info('File moved successfully');
                }
            }
        }

        return 0;
    }
}
