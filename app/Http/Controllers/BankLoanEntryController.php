<?php

namespace App\Http\Controllers;

use App\Models\BankLoanEntry;
use App\Models\LoanAccount;
use App\Models\LoanEntry;
use Illuminate\Http\Request;

class BankLoanEntryController extends Controller
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
     * @param  \App\Models\BankLoanEntry  $bankLoanEntry
     * @return \Illuminate\Http\Response
     */
    public function show($loan_id)
    {
        $loan_account = LoanAccount::where('loan_id', $loan_id)->get()[0];
        $loan_entries = LoanEntry::where('loan_id', $loan_id)->get();
        return view('loan_entries/show', ['loan_account' => $loan_account, 'loan_entries' => $loan_entries]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BankLoanEntry  $bankLoanEntry
     * @return \Illuminate\Http\Response
     */
    public function edit(BankLoanEntry $bankLoanEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BankLoanEntry  $bankLoanEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankLoanEntry $bankLoanEntry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankLoanEntry  $bankLoanEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankLoanEntry $bankLoanEntry)
    {
        //
    }
}
