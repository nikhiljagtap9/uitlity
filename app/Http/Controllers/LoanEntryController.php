<?php

namespace App\Http\Controllers;

use App\Exports\LoanEntryExport;
use App\Models\LoanAccount;
use App\Models\LoanEntry;
use PDF;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LoanEntryController extends Controller
{
    public function export($loan_id, $name = '')
    {
        //return Excel::download(new LoanEntryExport($loan_id, $name), 'Statement.xlsx');
    }

    public function generatePDF($loan_id, $name = '')
    {
        /** */

        $loan_account = LoanAccount::where('loan_id', $loan_id)->get()[0];
        $loan_entries = LoanEntry::where('loan_id', $loan_id)->orderBy('id', 'asc')->get();

        $data = ['loan_account' => $loan_account, 'loan_entries' => $loan_entries, 'name' => $name];

        $pdf = PDF::loadView('loan_entries/showpdf', $data);

        //return $pdf->view('statement.pdf');
        return $pdf->stream();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($loan_id, $name = '')
    {
        $loan_account = LoanAccount::where('loan_id', $loan_id)->get()[0];
        $loan_entries = LoanEntry::where('loan_id', $loan_id)->orderBy('id', 'asc')->paginate(50);
        return view('loan_entries/show', ['loan_account' => $loan_account, 'loan_entries' => $loan_entries, 'name' => $name]);
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
     * @param  \App\Models\LoanEntry  $loanEntry
     * @return \Illuminate\Http\Response
     */
    public function show($loan_id, $name = '')
    {
        $loan_account = LoanAccount::where('loan_id', $loan_id)->get()[0];
	$loan_entries = LoanEntry::where('loan_id', $loan_id)->orderBy('id', 'asc')->paginate(50);
        return view('loan_entries/show', ['loan_account' => $loan_account, 'loan_entries' => $loan_entries, 'name' => $name]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LoanEntry  $loanEntry
     * @return \Illuminate\Http\Response
     */
    public function edit(LoanEntry $loanEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LoanEntry  $loanEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoanEntry $loanEntry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LoanEntry  $loanEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(LoanEntry $loanEntry)
    {
        //
    }
}
