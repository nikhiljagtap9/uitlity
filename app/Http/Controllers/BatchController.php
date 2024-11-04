<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Collection;
use App\Models\LoanAccount;
use App\Models\LoanEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BatchController extends Controller
{
    public function dashboard()
    {
        /**
         *Dashboard shows all counts
         */
        // $batch = Batch::where('status', 'Done')->get();
        $batch = Batch::where('status', 'Done')->get();

        //$loan_account = LoanAccount::where('loan_status', 'active')->get();
        $bank_loan_date = LoanAccount::orderBy('id', 'DESC')->get()[0]['bank_loan_date'];
        $lastdisbused = LoanAccount::where('loan_status', 'active')->where('bank_loan_date', $bank_loan_date)
            ->selectRaw('SUM(sanction_limit) as total_sanction_limit')->get()[0];
        //dd($lastdisbused->total_sanction_limit);
        $allcount = LoanAccount::count();
        $loanaccount = LoanAccount::where('loan_status', 'active')
            ->selectRaw('COUNT(*) as loan_count')
            ->selectRaw('SUM(sanction_limit) as total_sanction_limit')
            //->selectRaw('SUM(gold_quantity) as total_gold_quantity')
            ->selectRaw('SUM(bank_sanction_amount) as total_bank_sanction')
            ->selectRaw('SUM(nbfc_sanction_amount) as total_nbfc_sanction')
            ->get()[0];
        //dd($loanaccount);
        //$total_security = $loanaccount->total_gold_quantity * (config('global.benchmark_rate') / 10);
        $total_ltv = 0;
        $bank_ltv = 0;
        $nbfc_ltv = 0;

        //dd($loanaccount[0]->loan_count);
        $loan_account_closed = LoanAccount::where('loan_status', 'closed')->count();
        $sma0 = LoanAccount::where('loan_status', 'active')->where('classification', 'SMA0')->count();
        $sma1 = LoanAccount::where('loan_status', 'active')->where('classification', 'SMA1')->count();
        $sma2 = LoanAccount::where('loan_status', 'active')->where('classification', 'SMA2')->count();
        $npa = LoanAccount::where('loan_status', 'active')->where('classification', 'NPA')->count();
        //dd($sma0);
        $loan_entries = LoanEntry::selectRaw('SUM(debit) as total_debit')
            ->selectRaw('SUM(credit) as total_credit')
            ->get()[0];

        $count = array(
            'allcount' => $allcount,
            'accounts' => $loanaccount->loan_count,
            'accounts_closed' => $loan_account_closed,
            'sma0' => $sma0,
            'sma1' => $sma1,
            'sma2' => $sma2,
            'npa' => $npa,
            'total_disbursement' => $loanaccount->total_sanction_limit,
            'bank_disbursement' => $loanaccount->total_bank_sanction,
            'nbfc_disbursement' => $loanaccount->total_nbfc_sanction,
            'total_principal' => $batch->sum('total_principal'),
            'total_bank_principal' => $batch->sum('total_bank_principal'),
            'total_nbfc_principal' => $batch->sum('total_nbfc_principal'),
            'total_interest' => $batch->sum('total_interest'),
            'total_bank_interest' => $batch->sum('total_bank_interest'),
            'total_nbfc_interest' => $batch->sum('total_nbfc_interest'),
            'total_mfl_sprade' => $batch->sum('total_mfl_sprade'),
            'total_debit' => $loan_entries->total_debit,
            'total_credit' => $loan_entries->total_credit,
            'total_ltv' => $total_ltv,
            'bank_ltv' => $bank_ltv,
            'nbfc_ltv' => $nbfc_ltv,
            'lastdisbursed' => $lastdisbused->total_sanction_limit,
        );
        //dd($count);
        /*
        $data = [
            'labels' => ['January', 'February', 'March', 'April', 'May'],
            'data' => [65, 59, 80, 81, 56],
        ];
        */
        $data = LoanAccount::selectRaw("date_format(bank_loan_date, '%Y-%m-%d') as bank_loan_date, count(*) as aggregate")
            ->whereDate('bank_loan_date', '>=', now()->subDays(30))
            ->groupBy('bank_loan_date')
            ->get();
        //dd($data);
        $data2 = LoanAccount::selectRaw("date_format(bank_loan_date, '%Y-%m-%d') as bank_loan_date, SUM(bank_sanction_amount) as aggregate2")
            ->whereDate('bank_loan_date', '>=', now()->subDays(30))
            ->groupBy('bank_loan_date')
            ->get();

        return view('batch.dashboard', ['count' => $count, 'data' => $data, 'data2' => $data2]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Batch::select('*')->where('loan_status', 'active');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('batch.show', [$row]) . '" class="btn btn-primary">View</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $batches = Batch::orderBy('id', 'desc')->paginate(50);
        return view('batch.index', ['batches' => $batches]);
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
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function show(Batch $batch)
    {
        $collections = Collection::where('batch_id', $batch->uuid)->paginate(50);
        $results = Collection::select(DB::raw('SUM(PRINCIPAL_AMT) as principal,SUM(INTEREST_AMT) as interest,loan_account_number'))
            ->groupBy('loan_account_number')
            ->where('batch_id', $batch->uuid)
            ->where('status', 'Pending')
            ->get();
        return view('batch.show', ['batch' => $batch, 'collections' => $collections, 'results' => $results]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function edit(Batch $batch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Batch $batch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Batch $batch)
    {
        //
    }
}
