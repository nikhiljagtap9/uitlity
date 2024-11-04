<?php

namespace App\Console\Commands;

use App\Models\Interest;
use App\Models\InterestMonth;
use App\Models\LoanAccount;
use App\Models\LoanEntry;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InterestGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backdated Interest';

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
        Log::info('Daily Interest job started.');
        $startdate = '2024-09-23';
        $enddate = '2024-10-07';

        $bank_roi = config('global.bank_interest');
     //   $nbfc_roi = config('global.nbfc_interest');
        $loan_accounts = LoanAccount::where('loan_status', 'active')
            // ->where('bank_loan_date', '>=', $startdate)
            ->where('bank_loan_date', '<=', $enddate)
            ->get();
        foreach ($loan_accounts as $account) {
            $start_date = Carbon::parse($startdate);
	    $end_date = Carbon::parse($enddate);
	    $nbfc_roi = $account->nbfc_interest;
            while ($start_date->lte($end_date)) {
                $loan_entries = LoanEntry::where('loan_id', $account->loan_id)->latest('id')->first();
                $balance1 =  $loan_entries->balance;//200398.8
                $bank_balance1 = $loan_entries->bank_balance;//160024.30
                $nbfc_balance1 = $loan_entries->nbfc_balance;//40079.75

                $principal_balance1 =  $loan_entries->principal_balance;
                $principal_nbfc_balance1 =  $loan_entries->principal_nbfc_balance;
                
                $total_interest = ($balance1 * ($nbfc_roi / 365)) / 100;//126.278
                $nbfc_interest = ($nbfc_balance1 * ($nbfc_roi / 365)) / 100;//25.2557

                $nbfc_loan_date = Carbon::parse($account->nbfc_loan_date);
                $bank_loan_date = Carbon::parse($account->bank_loan_date);

                /**
                 * Only NBFC Date Interest
                 */
                if ($start_date->gte($nbfc_loan_date) && $start_date->lt($bank_loan_date)) {

                    /** Only NBFC Date Interest */
                    Interest::create([
                        'loan_id' => $account->loan_id,
                        'interest_date' => $start_date->toDateString(),
                        'bank_roi' => $bank_roi,
                        'nbfc_roi' => $nbfc_roi,
                        'total_interest' => $total_interest,
                        'bank_interest' => 0,
                        'nbfc_interest' => $nbfc_interest,
                        'interest_type' => 'daily'
                    ]);
                }
                /**
                 * Bank and NBFC Interest
                 */
                if ($start_date->gte($bank_loan_date)) {

                    /** Both NBFC and Bank Date */
                    $principal_bank_balance1 =  $loan_entries->principal_bank_balance;
                    $bank_interest = ($bank_balance1 * ($bank_roi / 365)) / 100;//41.84

                    Interest::create([
                        'loan_id' => $account->loan_id,
                        'interest_date' => $start_date->toDateString(),
                        'bank_roi' => $bank_roi,
                        'nbfc_roi' => $nbfc_roi,
                        'total_interest' => $total_interest,
                        'bank_interest' => $bank_interest,
                        'nbfc_interest' => $nbfc_interest,
                        'interest_type' => 'daily'
                    ]);
                }
                /**
                 * Check Date in Month END
                 */
                $endofMonth = $start_date->copy()->endOfMonth();
                $startOfMonth = $start_date->copy()->startOfMonth();
                if ($start_date->toDateString() == $endofMonth->toDateString()) {
                    /** */
                    $start1 = $startOfMonth->toDateString();
                    $total_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start1)->where('interest_date', '<=', $endofMonth->toDateString())->sum('total_interest');
                    $bank_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start1)->where('interest_date', '<=', $endofMonth->toDateString())->sum('bank_interest');
                    $nbfc_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start1)->where('interest_date', '<=', $endofMonth->toDateString())->sum('nbfc_interest');
                    InterestMonth::create([
                        'loan_id' => $account->loan_id,
                        'interest_date' => $endofMonth->toDateString(),
                        'interest_month' => $endofMonth->format('m'),
                        'interest_year' => $endofMonth->format('Y'),
                        'bank_roi' => $bank_roi,
                        'nbfc_roi' => $nbfc_roi,
                        'total_interest' => $total_interest,
                        'bank_interest' => $bank_interest,
                        'nbfc_interest' => $nbfc_interest,
                        'interest_type' => 'monthly'
                    ]);

                    LoanEntry::create([
                        'loan_id' => $account->loan_id,
                        'entry_month' => $endofMonth->format('m'),
                        'entry_year' => $endofMonth->format('Y'),
                        'entry_date' => $endofMonth->format('Y-m-d'),
                        'description' => 'Interest of ' . $endofMonth->format('M') . '-' . $endofMonth->format('Y'),
                        'total_debit' => $total_interest,
                        'debit' => $total_interest,
                        'bank_debit' => $bank_interest,
                        'nbfc_debit' => $nbfc_interest,
                        'balance' => $balance1 + $total_interest,
                        'bank_balance' => $bank_balance1 + $bank_interest,
                        'nbfc_balance' => $nbfc_balance1 + $nbfc_interest,
                        'head' => 'interest',
                        'principal_balance' => $loan_entries->principal_balance,
                        'principal_bank_balance' => $loan_entries->principal_bank_balance,
                        'principal_nbfc_balance' => $loan_entries->principal_nbfc_balance,
                        'interest_balance' => $loan_entries->interest_balance + $total_interest,
                        'interest_bank_balance' => $loan_entries->interest_bank_balance + $bank_interest,
                        'interest_nbfc_balance' => $loan_entries->interest_nbfc_balance + $nbfc_interest
                    ]);
                    $total = $balance1 + $total_interest;
                    if ($total == 0) {
                        $account->update(['loan_status' => 'closed']);
                    }
                }
                $start_date->addDay();
            }
        }
        Log::info('Interest job completed.');
        return 0;
    }
}
