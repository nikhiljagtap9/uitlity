<?php

namespace App\Http\Controllers;

use App\Models\LoanAccount;
use App\Models\NbfcLoanEntry;
use Illuminate\Http\Request;

class NbfcLoanEntryController extends Controller
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
     * @param  \App\Models\NbfcLoanEntry  $nbfcLoanEntry
     * @return \Illuminate\Http\Response
     */
    public function show($loan_id)
    {
        $loan_account = LoanAccount::where('loan_id', $loan_id)->get()[0];
        $loan_entries = NbfcLoanEntry::where('loan_id', $loan_id)->get();
        return view('loan_entries/show', ['loan_account' => $loan_account, 'loan_entries' => $loan_entries]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NbfcLoanEntry  $nbfcLoanEntry
     * @return \Illuminate\Http\Response
     */
    public function edit(NbfcLoanEntry $nbfcLoanEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NbfcLoanEntry  $nbfcLoanEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NbfcLoanEntry $nbfcLoanEntry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NbfcLoanEntry  $nbfcLoanEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(NbfcLoanEntry $nbfcLoanEntry)
    {
        //
    }
}
