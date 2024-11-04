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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RepaymentSchedule  $repaymentSchedule
     * @return \Illuminate\Http\Response
     */
    public function show($loan_id)
    {
        $loan_account = LoanAccount::where('loan_id', $loan_id)->get()[0];
        $bank_date = Carbon::parse($loan_account->bank_loan_date);
        $nbfc_date = Carbon::parse($loan_account->nbfc_loan_date);

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
