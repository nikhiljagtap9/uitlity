<?php

namespace App\Console\Commands;

use App\Models\Interest;
use App\Models\InterestMonth;
use App\Models\LoanAccount;
use App\Models\LoanEntry;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DailyInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate Daily Interest and Insert into DB';

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
        $bank_roi = config('global.bank_interest');
        $nbfc_roi = config('global.nbfc_interest');
        $loan_accounts = LoanAccount::where('loan_status', 'active')->get();
        foreach ($loan_accounts as $account) {
            /**
             * Calculate NBFC Backdated Interest
             */
            $nbfc_backdate = $account->nbfc_backdate;
            if ($account->nbfc_backdate == 1) {
                $nbfc_start_date = Carbon::parse($account->nbfc_loan_date);
                $nbfc_start_date1 = Carbon::parse($account->nbfc_loan_date);

                $nbfc_end_date = Carbon::parse($account->bank_loan_date)->subDay();

                while ($nbfc_start_date1->lte($nbfc_end_date)) {
                    $loan_entries = LoanEntry::where('loan_id', $account->loan_id)->latest('id')->first();

                    $balance1 =  $loan_entries->balance;
                    $bank_balance1 = $loan_entries->bank_balance;
                    $nbfc_balance1 = $loan_entries->nbfc_balance;

                    $principal_balance1 =  $loan_entries->principal_balance;
                    $principal_nbfc_balance1 =  $loan_entries->principal_nbfc_balance;
                    $nbfc_balance1 = $loan_entries->nbfc_balance;
                    $total_interest = ($principal_balance1 * ($nbfc_roi / 365)) / 100;
                    $nbfc_interest = ($principal_nbfc_balance1 * ($nbfc_roi / 365)) / 100;
                    Interest::create([
                        'loan_id' => $account->loan_id,
                        'interest_date' => $nbfc_start_date1->toDateString(),
                        'bank_roi' => $bank_roi,
                        'nbfc_roi' => $nbfc_roi,
                        'total_interest' => $total_interest,
                        'bank_interest' => 0,
                        'nbfc_interest' => $nbfc_interest,
                        'interest_type' => 'daily'
                    ]);

                    $nbfcendOfMonth = $nbfc_start_date->endOfMonth();
                    $nbfc_start_date2 = Carbon::parse($account->nbfc_loan_date);
                    if ($nbfc_start_date1->toDateString() == $nbfcendOfMonth->toDateString()) {
                        $startOfMonth = $nbfc_start_date2->startOfMonth();
                        $start1 = $startOfMonth->toDateString();
                        $total_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start1)->where('interest_date', '<=', $nbfcendOfMonth->toDateString())->sum('total_interest');
                        $bank_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start1)->where('interest_date', '<=', $nbfcendOfMonth->toDateString())->sum('bank_interest');
                        $nbfc_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start1)->where('interest_date', '<=', $nbfcendOfMonth->toDateString())->sum('nbfc_interest');
                        InterestMonth::create([
                            'loan_id' => $account->loan_id,
                            'interest_date' => $nbfcendOfMonth->toDateString(),
                            'interest_month' => $nbfcendOfMonth->format('m'),
                            'interest_year' => $nbfcendOfMonth->format('Y'),
                            'bank_roi' => $bank_roi,
                            'nbfc_roi' => $nbfc_roi,
                            'total_interest' => $total_interest,
                            'bank_interest' => $bank_interest,
                            'nbfc_interest' => $nbfc_interest,
                            'interest_type' => 'monthly'
                        ]);

                        LoanEntry::create([
                            'loan_id' => $account->loan_id,
                            'entry_month' => $nbfcendOfMonth->format('m'),
                            'entry_year' => $nbfcendOfMonth->format('Y'),
                            'entry_date' => $nbfcendOfMonth->format('Y-m-d'),
                            'description' => 'Interest of ' . $nbfcendOfMonth->format('M') . '-' . $nbfcendOfMonth->format('Y'),
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
                    $nbfc_start_date1->addDay();
                }
                $account->update(['nbfc_backdate' => 0]);
            }
            /**
             * Calculate BANK Backdated Interest
             */
            $bank_backdate = $account->bank_backdate;
            if ($account->bank_backdate == 1) {
                $bank_start_date = Carbon::parse($account->bank_loan_date);
                $bank_start_date1 = $bank_start_date->copy();
                //$bank_end_date = Carbon::now()->subDay();
                $bank_end_date = Carbon::parse('2024-06-28');
                while ($bank_start_date1->lte($bank_end_date)) {
                    //echo $bank_start_date1->toDateString();
                    //echo '<br>';
                    //echo $bank_end_date->toDateString();
                    //echo '<br>';
                    $loan_entries = LoanEntry::where('loan_id', $account->loan_id)->latest('id')->first();

                    $balance1 =  $loan_entries->balance;
                    $bank_balance1 = $loan_entries->bank_balance;
                    $nbfc_balance1 = $loan_entries->nbfc_balance;

                    $principal_balance1 =  $loan_entries->principal_balance;
                    $principal_nbfc_balance1 =  $loan_entries->principal_nbfc_balance;
                    $principal_bank_balance1 =  $loan_entries->principal_bank_balance;

                    $total_interest = ($principal_balance1 * ($nbfc_roi / 365)) / 100;
                    $nbfc_interest = ($principal_nbfc_balance1 * ($nbfc_roi / 365)) / 100;
                    $bank_interest = ($principal_bank_balance1 * ($bank_roi / 365)) / 100;

                    Interest::create([
                        'loan_id' => $account->loan_id,
                        'interest_date' => $bank_start_date1->toDateString(),
                        'bank_roi' => $bank_roi,
                        'nbfc_roi' => $nbfc_roi,
                        'total_interest' => $total_interest,
                        'bank_interest' => $bank_interest,
                        'nbfc_interest' => $nbfc_interest,
                        'interest_type' => 'daily'
                    ]);

                    $bankendOfMonth = $bank_start_date1->copy()->endOfMonth();
                    $startOfMonth = $bank_start_date1->copy()->startOfMonth();

                    if ($bank_start_date1->toDateString() == $bankendOfMonth->toDateString()) {

                        $start1 = $startOfMonth->toDateString();
                        $total_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start1)->where('interest_date', '<=', $bankendOfMonth->toDateString())->sum('total_interest');
                        $bank_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start1)->where('interest_date', '<=', $bankendOfMonth->toDateString())->sum('bank_interest');
                        $nbfc_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start1)->where('interest_date', '<=', $bankendOfMonth->toDateString())->sum('nbfc_interest');
                        InterestMonth::create([
                            'loan_id' => $account->loan_id,
                            'interest_date' => $bankendOfMonth->toDateString(),
                            'interest_month' => $bankendOfMonth->format('m'),
                            'interest_year' => $bankendOfMonth->format('Y'),
                            'bank_roi' => $bank_roi,
                            'nbfc_roi' => $nbfc_roi,
                            'total_interest' => $total_interest,
                            'bank_interest' => $bank_interest,
                            'nbfc_interest' => $nbfc_interest,
                            'interest_type' => 'monthly'
                        ]);

                        LoanEntry::create([
                            'loan_id' => $account->loan_id,
                            'entry_month' => $bankendOfMonth->format('m'),
                            'entry_year' => $bankendOfMonth->format('Y'),
                            'entry_date' => $bankendOfMonth->format('Y-m-d'),
                            'description' => 'Interest of ' . $bankendOfMonth->format('M') . '-' . $bankendOfMonth->format('Y'),
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
                    $bank_start_date1->addDay();
                }
                $account->update(['bank_backdate' => 0]);
            }
            continue;
            //Calculate Current Day Interest
            $start = date('Y-m-d');

            $carbonDate = Carbon::parse($start);

            // Get the last date of the month
            $endOfMonth = $carbonDate->copy()->endOfMonth();
            //Get the start of the month
            $startOfMonth = $carbonDate->copy()->startOfMonth();
            // Loop through each date from the given date to the end of the month
            $currentDate = Carbon::parse($start);

            $loan_entries = LoanEntry::where('loan_id', $account->loan_id)->latest('id')->first();
            $balance1 =  $loan_entries->balance;
            $principal_balance1 =  $loan_entries->principal_balance;
            $bank_balance1 = $loan_entries->bank_balance;
            $nbfc_balance1 = $loan_entries->nbfc_balance;

            $total_interest = ($principal_balance1 * ($nbfc_roi / 365)) / 100;
            $bank_interest = ($bank_balance1 * ($bank_roi / 365)) / 100;
            $nbfc_interest = ($nbfc_balance1 * ($nbfc_roi / 365)) / 100;

            Interest::create([
                'loan_id' => $account->loan_id,
                'interest_date' => $currentDate->toDateString(),
                'bank_roi' => $bank_roi,
                'nbfc_roi' => $nbfc_roi,
                'total_interest' => $total_interest,
                'bank_interest' => $bank_interest,
                'nbfc_interest' => $nbfc_interest,
                'interest_type' => 'daily'
            ]);
            if ($currentDate->toDateString() == $endOfMonth->toDateString()) {
                $start1 = $startOfMonth->toDateString();
                $total_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start1)->where('interest_date', '<=', $endOfMonth->toDateString())->sum('total_interest');
                $bank_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start1)->where('interest_date', '<=', $endOfMonth->toDateString())->sum('bank_interest');
                $nbfc_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start1)->where('interest_date', '<=', $endOfMonth->toDateString())->sum('nbfc_interest');
                InterestMonth::create([
                    'loan_id' => $account->loan_id,
                    'interest_date' => $endOfMonth->toDateString(),
                    'interest_month' => $endOfMonth->format('m'),
                    'interest_year' => $endOfMonth->format('Y'),
                    'bank_roi' => $bank_roi,
                    'nbfc_roi' => $nbfc_roi,
                    'total_interest' => $total_interest,
                    'bank_interest' => $bank_interest,
                    'nbfc_interest' => $nbfc_interest,
                    'interest_type' => 'monthly'
                ]);

                LoanEntry::create([
                    'loan_id' => $account->loan_id,
                    'entry_month' => $endOfMonth->format('m'),
                    'entry_year' => $endOfMonth->format('Y'),
                    'entry_date' => $endOfMonth->format('Y-m-d'),
                    'description' => 'Interest of ' . $endOfMonth->format('M') . '-' . $endOfMonth->format('Y'),
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
        }
        Log::info('Daily Interest job completed.');

        return 0;
    }
}
