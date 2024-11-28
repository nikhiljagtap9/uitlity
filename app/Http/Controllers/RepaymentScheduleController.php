<?php

namespace App\Http\Controllers;

use App\Models\LoanAccount;
use App\Models\RepaymentSchedule;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class RepaymentScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function calculateSchedule($loan_account, $type = '')
    {
        /***
         *
         */
        $tenureMonths = $loan_account->loan_tenure;
        $annualRate = $loan_account->bank_interest;
        $bank_date = Carbon::parse($loan_account->bank_loan_date);
        $nbfc_date = Carbon::parse($loan_account->nbfc_loan_date);
        $start = Carbon::parse($loan_account->nbfc_loan_date);
        $closureDate = $start->addMonths($tenureMonths);
        $closureDate = $closureDate->subDay();

        // Step 1: Calculate number of quarters/half year depend on yearly
        if ($loan_account->Repayment_Type == 'Quaterly') {
            $repay_duration = 3;
            $divideby = 4;
        } else if ($loan_account->Repayment_Type == 'Half Yearly') {
            $repay_duration = 6;
            $divideby = 2;
        } else if ($loan_account->Repayment_Type == 'Annually') {
            $repay_duration = 12;
            $divideby = 1;
        } else if ($loan_account->Repayment_Type == 'Monthly') {
            $repay_duration = 1;
            $divideby = 12;
        } else if ($loan_account->Repayment_Type == 'Bi-Monthly') {
            $repay_duration = 0.5;
            $divideby = 24;
        }

        $totalFreq = floor($tenureMonths / $repay_duration);
        //$totalFreq_ceil = ceil($tenureMonths / $repay_duration);

        // Step 2: Calculate quarterly rate and principal repayment
        $interestRate = $annualRate / $divideby / 100; // Divide by 4 for quarterly rate

        $principal = $principalRepayment = $loan_account->bank_sanction_amount;

        $schedule = [];
        for ($i = 1; $i <= $totalFreq; $i++) {
            /***
             *
             */
            $principalRepayment = 0;
            // Calculate interest for the current quarter
            $quarterlyInterest = $principal * $interestRate;
            $quarterEndDate =  $nbfc_date->copy()->addMonths($i * $repay_duration);
            $totalPayment = $quarterlyInterest + $principalRepayment;
            if ($i == 1 && $type == 'bank') {
                //Calculate first schedule days difference for bank schedule
                $bankDaysDifference = $quarterEndDate->diffInDays($bank_date, true) + 1;
                $partialPeriodInterestRate = ($annualRate / 365) * $bankDaysDifference / 100;
                $quarterlyInterest = $principal * $partialPeriodInterestRate;
                $totalPayment = $quarterlyInterest + $principalRepayment;
            }
            if ($i == $totalFreq) {
                //Calculate last schdeule days difference
                $principalRepayment = $principal;
                if ($quarterEndDate->lt($closureDate)) {
                    $DaysDifference = $quarterEndDate->diffInDays($closureDate, true) + 1;
                    //$partialPeriodInterestRate = ($annualRate / 12) * $remainingMonths / 100;
                    $quarterlyInterest = ($annualRate / 365) * $DaysDifference / 100;
                    // Partial interest rate

                    $quarterlyInterest = $principalRepayment * $quarterlyInterest;
                }
                $totalPayment = $quarterlyInterest + $principalRepayment;
            }
            $schedule[] = [
                'quarter' => $i,
                'quarter_end_date' => $quarterEndDate->format('d-M-Y'),
                'beginning_balance' => round($principal, 2),
                'quarter_interest' => round($quarterlyInterest, 2),
                'principal_repayment' => round($principalRepayment, 2),
                'total_payment' => round($totalPayment, 2),
                'ending_balance' => round($principal - $principalRepayment, 2),
            ];
        }
        return $schedule;
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RepaymentSchedule  $repaymentSchedule
     * @return \Illuminate\Http\Response
     */
    public function show($loan_id)
    {
        $loan_account = LoanAccount::where('loan_id', $loan_id)->get()[0];

        $schedule = $this->calculateSchedule($loan_account, 'bank');

        $bank_date = Carbon::parse($loan_account->bank_loan_date);
        $nbfc_date = Carbon::parse($loan_account->nbfc_loan_date);

        dd($schedule, $loan_account);
        $start = Carbon::parse($loan_account->nbfc_loan_date);
        $closureDate = $start->addMonths($loan_account->loan_tenure);
        $closureDate = $closureDate->subDay();
        $nbfcDaysDifference = $nbfc_date->diffInDays($closureDate, true) + 1;
        $bankDaysDifference = $bank_date->diffInDays($closureDate, true) + 1;
        $nbfc_daily = round($loan_account->nbfc_interest / 365, 3);
        $bank_daily = round($loan_account->bank_interest / 365, 3);

        $total_interest = ($loan_account->sanction_limit * $nbfc_daily * $nbfcDaysDifference) / 100;
        $bank_interest = ($loan_account->bank_sanction_amount * $bank_daily * $bankDaysDifference) / 100;
        $nbfc_interest = ($loan_account->nbfc_sanction_amount * $nbfc_daily * $nbfcDaysDifference) / 100;

        return view('repayment_schedule.show', [
            'loan_account' => $loan_account,
            'closureDate' => $closureDate,
            'total_interest' => $total_interest,
            'bank_interest' => $bank_interest,
            'nbfc_interest' => $nbfc_interest
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RepaymentSchedule  $repaymentSchedule
     * @return \Illuminate\Http\Response
     */
    public function edit(RepaymentSchedule $repaymentSchedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RepaymentSchedule  $repaymentSchedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RepaymentSchedule $repaymentSchedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RepaymentSchedule  $repaymentSchedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(RepaymentSchedule $repaymentSchedule)
    {
        //
    }
}
