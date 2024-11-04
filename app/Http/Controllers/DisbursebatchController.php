<?php

namespace App\Http\Controllers;

use App\Models\Disbursebatch;
use App\Models\Disbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Services\CkycService;
use App\Services\KycService;

class DisbursebatchController extends Controller
{

    protected $ckycService;
    protected $KycApiService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct(CkycService $ckycService, KycService $KycApiService)
    {
        $this->ckycService = $ckycService;
        $this->KycApiService = $KycApiService;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Disbursebatch::select('*')->where('loan_status', 'active');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('batch.show', [$row]) . '" class="btn btn-primary">View</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $batches = Disbursebatch::orderBy('id', 'desc')->paginate(50);
        return view('disbursebatch.index', ['batches' => $batches]);
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
     * @param  \App\Models\Disbursebatch  $disbursebatch
     * @return \Illuminate\Http\Response
     */
    public function show(Disbursebatch $disbursebatch, Request $request)
    {
	// Retrieve filter parameters
        $type = $request->input('type'); // 'pan' or 'ckyc'
        $lowerValue = $request->input('lower_value');
        $upperValue = $request->input('upper_value');
        $currentTab = $request->input('current_tab', 'tab2');

        //dd($type,$lowerValue,$upperValue);    


        $collection_count = Disbursement::where('batch_id', $disbursebatch->uuid)->count();
        $pending_count = Disbursement::where('batch_id', $disbursebatch->uuid)->where('status', 'Pending')->count();
        $collections = Disbursement::where('batch_id', $disbursebatch->uuid)->get();
        $approved_count = Disbursement::where('batch_id', $disbursebatch->uuid)->where('status', 'Approved')->count();
        $bre_results = Disbursement::select(DB::raw('COUNT(*) as status_count,SUM(loan_amount) as loan_amount,SUM(sanction_amount) as sanction_amount,SUM(nbfc_sanction_amount) as nbfc_sanction_amount,SUM(bank_sanction_amount) as bank_sanction_amount,status'))
        ->groupBy('status')
        ->where('batch_id', $disbursebatch->uuid)
        ->get();
        $results = Disbursement::select(DB::raw(' Business_Type, SUM(sanction_amount) as sanction_amount, SUM(nbfc_sanction_amount) as nbfc_sanction_amount, SUM(bank_sanction_amount) as bank_sanction_amount'))
        ->where('batch_id', $disbursebatch->uuid)
        ->whereIn('status', ['Done', 'Approved'])
        ->groupBy('Business_Type')
	->get();


	$dataCounts = Disbursement::select(DB::raw('COUNT(*) as total_messages,message'))
		->groupBy('message')
		->get();

        $approvedList =  Disbursement::select(DB::raw('	NBFC_Reference_Number, customer_name, loan_amount, sanction_amount, bank_sanction_amount,status'))
        ->where('batch_id', $disbursebatch->uuid)
        ->whereIn('status', ['Done', 'Approved'])
        //->groupBy('Business_Type','loan_account_number')
        ->get();

        $rejectedList =  Disbursement::select(DB::raw('	 pan_match_score, ckyc_match_score,udyam_match_score, NBFC_Reference_Number, customer_name, loan_amount, sanction_amount, bank_sanction_amount,status,message'))
        ->where('batch_id', $disbursebatch->uuid)
        ->whereIn('status', ['Rejected', 'Pending']);
        //->groupBy('Business_Type','loan_account_number')
	//->get();


	if ($type === 'pan') {
            $rejectedList->whereBetween('pan_match_score', [$lowerValue, $upperValue]);
        } elseif ($type === 'ckyc') {
            $rejectedList->whereBetween('ckyc_match_score', [$lowerValue, $upperValue]);
        } elseif ($type === 'udyam') {
            $rejectedList->whereBetween('udyam_match_score', [$lowerValue, $upperValue]);
        }
        $rejectedList = $rejectedList->get();

        $currentTab = $request->input('current_tab', 'tab1'); // Default to tab1

//dd($rejectedList);

	return view(
            'disbursebatch.show',
            [
                'batch' => $disbursebatch,
                'collections' => $collections,
                'results' => $results,
                'collection_count' => $collection_count,
                'pending_count' => $pending_count,
                'approved_count' => $approved_count,
		'bre_results' => $bre_results,
		 'dataCounts'=>  $dataCounts,
		'approvedList'=> $approvedList,
		'rejectedList' => $rejectedList,
		'rejectedList' => $rejectedList,
                'currentTab' => $currentTab,
            ]
        );
    }

    public function processRejectedChunks(Request $request)
{
    $selectedLoans = $request->input('selectedLoans');
    if (empty($selectedLoans) || count($selectedLoans) == 0) {
            return redirect()->back()->with('error', 'No loans were selected.');
        }

    $offset = $request->input('offset', 0);
    $chunkSize = 5; // Define your chunk size
    
    $loans = Disbursement::whereIn('NBFC_Reference_Number', $selectedLoans)
        ->offset($offset)
        ->limit($chunkSize)
        ->get();

    foreach ($loans as $loan) {
        $this->callAPIs($loan);
    }

    $nextOffset = $offset + $chunkSize;
    $moreData = $nextOffset < count($selectedLoans);

    return response()->json([
        'moreData' => $moreData,
        'nextOffset' => $nextOffset,
    ]);
}

    public function processRejectedChunksold(Request $request)
    {
    	// Retrieve the selected loans from query parameters
        $selectedLoans = $request->input('selectedLoans');
	if( !$selectedLoans){
		return redirect()->back()->with('error', 'No loans were selected.');
	}
        // Check if selected_loans is an array
        if (is_array($selectedLoans)) {
            // If it's an array, just assign it directly
            $selectedLoans = $selectedLoans;
        } else {
            // If it's a string (i.e., from the query string), explode it
            $selectedLoans = explode(',', $selectedLoans);
        }
        // Validate if any loans were selected
        if (empty($selectedLoans) || count($selectedLoans) == 0) {
            return redirect()->back()->with('error', 'No loans were selected.');
        }

        $user = auth()->user(); // Get the authenticated user

        // Iterate over each selected loan ID
        foreach ($selectedLoans as $loanId) {
            // Find the loan based on the loan ID
            $loan = Disbursement::where('NBFC_Reference_Number', $loanId)->first();
	//	dd($loan);
	    $this->callAPIs($loan);
            // if ($loan) {
            //     // Update the existing loan with approval information
            //     $loan->update([
            //         'status' => 'Approved',
            //         'approved_by' => $user->name,
            //         'approved_at' => now(),
            //     ]);
            // } else {
            //     // Optional: Log an error or return a warning if the loan was not found
            //     // Log::warning("Loan with ID {$loanId} not found.");
            // }
        }

        return redirect()->back()->with('success', 'Selected loans have been approved.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Disbursebatch  $disbursebatch
     * @return \Illuminate\Http\Response
     */
    public function edit(Disbursebatch $disbursebatch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Disbursebatch  $disbursebatch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Disbursebatch $disbursebatch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Disbursebatch  $disbursebatch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Disbursebatch $disbursebatch)
    {
	$disbursements = Disbursement::where('batch_id',$disbursebatch->uuid)->delete();
        $disbursebatch->delete();
        $input['deleted_by'] = Auth::user()->name;
        $disbursebatch->update($input);
        return redirect(route('disbursebatch.index'))->with('success', 'Batch '.$disbursebatch->uuid .' deleted successfully!');
    }

    public function callAPIs($data){

        if (!$data->ckyc_match_score && config('global.ckyc_check')==true) {
            if(!$data->ckyc){
                $data->update(['message'=>'CKYC number not available', 'status' => 'Rejected']);
                return;
            }
        $response = $this->ckycService->ckycverify($data->ckyc, $data->dob,$data);
        $arr = json_decode($response,TRUE);
        $personal_detail = isset($arr['PID']['PID_DATA']['PERSONAL_DETAILS'])?$arr['PID']['PID_DATA']['PERSONAL_DETAILS']:null;
        $full_name = isset($personal_detail['FULLNAME'])?$personal_detail['FULLNAME']:null;
        $pan_no = isset($personal_detail['PAN'])?$personal_detail['PAN']:null;
        $image_details = isset($arr['PID']['PID_DATA']['IMAGE_DETAILS'])?$arr['PID']['PID_DATA']['IMAGE_DETAILS']:null;
        if($pan_no){
            if($data->PAN!=$pan_no){
                $data->update(['message'=>'Invalid CKYC Details, PAN Not Matching', 'status' => 'Rejected']);
                return ;
            }
        }
        if($full_name){
            $percentage = 0;
            $roundedPercentage = $this->nameMatchPercent(strtolower($data->CUSTOMER_NAME), strtolower($full_name));
            $data->update(['ckyc_match_score'=> $roundedPercentage]);
            if ($roundedPercentage < 75) {
                $data->update(['message'=>'CKYC Verification Failed, Name Match Per. is ' . $roundedPercentage . '%', 'status' => 'Rejected']);
                return;
            }
        }
    }

    if(strtolower($data->Business_Type) == 'msme' && config('global.udyam_check')==true){
        if(!$data->Udyam_no){
            $data->update(['message'=>'Udyam number not available', 'status' => 'Rejected']);
            return ;
        }
        if (!$data->udyam_match_score) {
            $udyog_details = $this->KycApiService->udyamVerification($data->Udyam_no, $data);
            if ($udyog_details === false) {
                $data->update(['message'=>'Udyam API Failed', 'status' => 'Rejected']);
                return ;
            } else {
                $percentage = 0;
                $udyaogPer = $this->nameMatchPercent(strtolower($data->CUSTOMER_NAME), strtolower($udyog_details['nameOfEnterprise']));
                $data->update(['udyam_match_score'=>$udyaogPer]);
                if ($udyaogPer < 75) {
                    $data->update(['message'=>'Udyam Aadhaar Verification Failed, Name Match Per. is ' . $udyaogPer . '%', 'status' => 'Rejected']);
                    return ;
                }
            }
        }
    }
    $user = auth()->user();
    	$data->update([
                     'status' => 'Approved',
                     'approved_by' => $user->name,
		     'approved_at' => now(),
		     'message'=>'Bre approved manually'
                 ]);
    }

    private function nameMatchPercent($name1, $name2) {
        // Convert names to lowercase and split them into words
        $name1Words = explode(" ", strtolower($name1));
        $name2Words = explode(" ", strtolower($name2));
    
        // Sort the words to ignore the order
        sort($name1Words);
        sort($name2Words);
    
        // Initialize total similarity and match counts
        $totalSimilarity = 0;
        $wordCount = count($name1Words) + count($name2Words); // Total number of words in both names
    
        // Compare each word from name2 against each word in name1
        foreach ($name1Words as $word1) {
            $bestMatch = 0;
            foreach ($name2Words as $word2) {
                similar_text($word1, $word2, $similarity);
                if ($similarity > $bestMatch) {
                    $bestMatch = $similarity;
                }
            }
            $totalSimilarity += $bestMatch;
        }
    
        // Calculate the percentage similarity
        $averageSimilarity = ($totalSimilarity / count($name1Words)); // Average similarity across name1
        return round($averageSimilarity, 2);
       }
    
}


