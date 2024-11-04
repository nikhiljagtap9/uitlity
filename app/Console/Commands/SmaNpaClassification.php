<?php

namespace App\Console\Commands;

use App\Models\LoanAccount;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SmaNpaClassification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SmaNpa:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Classification of SMA/NPA Accounts';

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
        $loan_accounts = LoanAccount::where('loan_status', 'active')->get();
        foreach ($loan_accounts as $account) {
            //Get Loan closure date from Tenure
            $tenure = $account->loan_tenure;
            $bank_loan_date = $account->bank_loan_date;
            $start = Carbon::parse($bank_loan_date);

            $closureDate = $start->addMonths($tenure);

            $currentDate = Carbon::now();

            // Calculate the difference in days
            $daysDifference = $currentDate->diffInDays($closureDate, true); // false for signed difference
            //echo $daysDifference;
            $category = 'STD';
            if ($daysDifference >= 1 && $daysDifference <= 30) {
                $category = 'SMA0';
            }
            if ($daysDifference > 30 && $daysDifference <= 60) {
                $category = 'SMA1';
            }
            if ($daysDifference > 60 && $daysDifference <= 90) {
                $category = 'SMA2';
            }
            if ($daysDifference > 90) {
                $category = 'NPA';
            }
            $account->update(['classification' => $category]);
        }
        return 0;
    }
}
