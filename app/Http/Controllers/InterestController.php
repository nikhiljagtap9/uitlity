<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use App\Models\InterestMonth;
use App\Models\LoanAccount;
use App\Models\LoanEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Monthly Interest Calculations
        $loan_accounts = LoanAccount::where('loan_status', 'active')->get();
        $start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');
        foreach ($loan_accounts as $account) {
            $bank_roi = 9.545;
            $nbfc_roi = 23;
            /*
            $loan_entries = LoanEntry::where('loan_id', $account->loan_id)->latest('id')->first();
            $balance1 =  $loan_entries->balance;
            $bank_balance1 = $loan_entries->bank_balance;
            $nbfc_balance1 = $loan_entries->nbfc_balance;
            */
            $total_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start_date)->where('interest_date', '<=', $end_date)->sum('total_interest');
            $bank_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start_date)->where('interest_date', '<=', $end_date)->sum('bank_interest');
            $nbfc_interest = Interest::where('loan_id', $account->loan_id)->where('interest_date', '>=', $start_date)->where('interest_date', '<=', $end_date)->sum('nbfc_interest');
            InterestMonth::create([
                'loan_id' => $account->loan_id,
                'interest_date' => date('Y-m-d'),
                'interest_month' => date('m'),
                'interest_year' => date('Y'),
                'bank_roi' => $bank_roi,
                'nbfc_roi' => $nbfc_roi,
                'total_interest' => $total_interest,
                'bank_interest' => $bank_interest,
                'nbfc_interest' => $nbfc_interest,
                'interest_type' => 'monthly'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Daily Interest calculations
        $loan_accounts = LoanAccount::where('loan_status', 'active')->get();
        foreach ($loan_accounts as $account) {
            $bank_roi = 9.545;
            $nbfc_roi = 23;
            /*
            $loan_entries = LoanEntry::where('loan_id', $account->loan_id)->latest('id')->first();
            $balance1 =  $loan_entries->balance;
            $bank_balance1 = $loan_entries->bank_balance;
            $nbfc_balance1 = $loan_entries->nbfc_balance;
            */
            $balance1 =  100000;
            $bank_balance1 = 80000;
            $nbfc_balance1 = 20000;

            $total_interest = ($balance1 * ($nbfc_roi / 365)) / 100;
            $bank_interest = ($bank_balance1 * ($bank_roi / 365)) / 100;
            $nbfc_interest = ($nbfc_balance1 * ($nbfc_roi / 365)) / 100;

            $month_number = date('m');
            $daysInMonth = Carbon::now()->month($month_number)->daysInMonth;

            while ($daysInMonth > 0) {
                Interest::create([
                    'loan_id' => $account->loan_id,
                    'interest_date' => date('Y-m-d'),
                    'bank_roi' => $bank_roi,
                    'nbfc_roi' => $nbfc_roi,
                    'total_interest' => $total_interest,
                    'bank_interest' => $bank_interest,
                    'nbfc_interest' => $nbfc_interest,
                    'interest_type' => 'daily'
                ]);
                $daysInMonth--;
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function show($loan_id)
    {
        $loan_accounts = LoanAccount::where('loan_id', $loan_id)->get()[0];
        $monthly_interest = InterestMonth::where('loan_id', $loan_id)->orderBy('id', 'desc')->get();
        return view('interest.show', ['monthly_interest' => $monthly_interest]);
    }

    public function checkNBFCLoanDate()
    {
        //
    }
    /**
     * Calculate monthly Interest from bank loan date or specified date
     *
     */
    public function test()
    {
        $bank_roi = config('global.bank_interest');
        $nbfc_roi = config('global.nbfc_interest');
        $bank_share = env('BANK_SHARE', 0.8);
        $nbfc_share = env('NBFC_SHARE', 0.2);
        $loan_id = 'LOAN32887387832743714';
        $account = LoanAccount::where('loan_id', $loan_id)->get()[0];
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
        /*
        $bank_backdate = $account->bank_backdate;
        if ($account->bank_backdate == 1) {
            $bank_start_date = Carbon::parse($account->bank_loan_date);
            $bank_start_date1 = $bank_start_date->copy();
            $bank_end_date = Carbon::now()->subDay();
            while ($bank_start_date1->lte($bank_end_date)) {
                echo $bank_start_date1->toDateString();
                echo '<br>';
                echo $bank_end_date->toDateString();
                echo '<br>';
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
            */
        //$start = $account->nbfc_loan_date;
        $start = $account->bank_loan_date;
        //$start = '2024-07-01';
        //$start = date('Y-m-d');
        $carbonDate = Carbon::parse($start);

        // Get the last date of the month
        $endOfMonth = $carbonDate->endOfMonth();
        //$endOfMonth = Carbon::parse('2023-11-10');

        // Loop through each date from the given date to the end of the month
        $currentDate = Carbon::parse($start);

        $loan_entries = LoanEntry::where('loan_id', $account->loan_id)->latest('id')->first();
        $balance1 =  $loan_entries->balance;
        $principal_balance1 =  $loan_entries->principal_balance;
        $principal_bank_balance1 =  $loan_entries->principal_bank_balance;
        $principal_nbfc_balance1 =  $loan_entries->principal_nbfc_balance;

        $interest_balance1 =  $loan_entries->interest_balance;
        $interest_bank_balance1 =  $loan_entries->interest_bank_balance;
        $interest_nbfc_balance =  $loan_entries->interest_nbfc_balance;

        $bank_balance1 = $loan_entries->bank_balance;
        $nbfc_balance1 = $loan_entries->nbfc_balance;


        $total_interest = ($principal_balance1 * ($nbfc_roi / 365)) / 100;
        $bank_interest = ($principal_bank_balance1 * ($bank_roi / 365)) / 100;
        $nbfc_interest = ($principal_nbfc_balance1 * ($nbfc_roi / 365)) / 100;

        $month_number = date('m');
        $daysInMonth = Carbon::now()->month($month_number)->daysInMonth;

        while ($currentDate->lte($endOfMonth)) {
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
                //$start1 = '2023-11-01';
                if ($nbfc_backdate == 1) {
                    $start1 = $account->nbfc_loan_date;
                    $nbfc_backdate = 0;
                } else {
                    $start1 = $start;
                }

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
                $interest_balance = $total_interest + $interest_balance1;
                $interest_bank = $bank_interest + $interest_bank_balance1;
                $interest_nbfc = $nbfc_interest + $interest_nbfc_balance;
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
                    'interest_balance' => $interest_balance,
                    'interest_bank_balance' => $interest_bank,
                    'interest_nbfc_balance' => $interest_nbfc,
                    'head' => 'interest',
                    'principal_balance' => $principal_balance1,
                    'principal_bank_balance' => $principal_bank_balance1,
                    'principal_nbfc_balance' => $principal_nbfc_balance1
                ]);
            }
            $currentDate->addDay();
        }

        echo 'Monthly Interest Updated';
    }

    public function test2()
    {
        dd(env('BANK_SHARE'));
        dd(config('global.bank_interest'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function edit(Interest $interest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Interest $interest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Interest $interest)
    {
        //
    }
}
