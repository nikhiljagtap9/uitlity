<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Collection;
use App\Models\Disbursement;
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
        $allcount = Disbursement::count();
        $approved = Disbursement::where('status', 'Approved')->count();
        $rejected = Disbursement::where('status', 'Rejected')->count();
        $duplicate = Disbursement::where('status', 'duplicate')->count();
        $pending = Disbursement::where('status', 'pending')->count();
       
        $count = array(
            'allcount' => $allcount,
            'approved' => $approved,
            'rejected' => $rejected,
            'duplicate' => $duplicate,
            'pending' => $pending,
        );
        //dd($count);

        return view('batch.dashboard', ['count' => $count]);
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
