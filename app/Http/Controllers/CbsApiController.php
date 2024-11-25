<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\CbsApi;
use App\Models\Collection;
use App\Models\Disbursebatch;
use App\Models\Disbursement;
use App\Models\Interest;
use App\Models\InterestMonth;
use App\Models\LoanAccount;
use App\Models\LoanEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;

class CbsApiController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('CBS_API'), // Base URI of the third-party API
        ]);
    }
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
    public function createLoanAccount($batch_id)
    {
        $disbursements = Disbursement::where('batch_id', $batch_id)->where('status', 'Disbursed')->get();
        foreach ($disbursements as $disbursement) {
            /**
             * Create Loan Account and update loan account table
             */
            $bank_roi = config('global.bank_interest');
            $nbfc_roi = config('global.nbfc_interest');
            $loan_account = LoanAccount::create([
                'mfl_ref_no' => $disbursement->NBFC_Reference_Number,
                'bank_interest' => $bank_roi,
                'nbfc_interest' => $disbursement->CGCL_ROI,
                'sanction_limit' => $disbursement->sanction_amount,
                'bank_sanction_amount' => $disbursement->bank_sanction_amount,
                'nbfc_sanction_amount' => $disbursement->nbfc_sanction_amount,
                'total_balance' => $disbursement->sanction_amount,
                'bank_balance' => $disbursement->bank_sanction_amount,
                'loan_tenure' => $disbursement->LOAN_TENURE,
                'bank_loan_date' => date('Y-m-d'),
                'nbfc_loan_date' => Carbon::parse($disbursement->LOAN_BOOKING_DATE)->toDateString(),
                'loan_status' => 'active',
                'nbfc_balance' => $disbursement->nbfc_sanction_amount,
                'utr_bom_pos_update' => $disbursement->utr_bom_pos_update,
                'loan_account_number' => $disbursement->loan_account_number,
                'job_type' => '',
                'pan_number' => $disbursement->PAN,
                'postal_code' => $disbursement->ZIPCODE,
                'state_code' => $disbursement->STATE,
                'city_code' => $disbursement->CITY,
                'address1' => $disbursement->ADD1,
                'address2' => $disbursement->ADD2,
                'email' => $disbursement->EMAIL,
                'mobile_number' => $disbursement->MOBILE_NO,
                'caste' => '',
                'community' => '',
                'ckyc_no' => $disbursement->ckyc,
                'date_of_birth' => $disbursement->dob,
                'AGE' => $disbursement->AGE,
                'gender' => $disbursement->GENDER,
                'customer_name' => $disbursement->CUSTOMER_NAME,
                'customer_title' => $disbursement->TITLE,
                'batch_id' => $disbursement->batch_id,
                'dob' => $disbursement->dob,
                'loan_amount' => $disbursement->sanction_amount,
                'interest_rate' => $disbursement->interest_rate,
                'udyog_uaadhaar_number' => $disbursement->Udyam_no,
                'ckyc' => $disbursement->ckyc,
                'credit_score' => $disbursement->credit_score,
                'status' => 'active',
                'lapp_id'=> $disbursement->lapp_id,
                'ltv' => $disbursement->LTV,

                'CGCL_Customer_Number' => $disbursement->CGCL_Customer_Number,
                'CGCL_Account_Number' => $disbursement->CGCL_Account_Number,
                'DISBURSMENT_DETAIL' => $disbursement->DISBURSMENT_DETAIL,
                'FIRST_NAME' => $disbursement->FIRST_NAME,
                'MIDDLE_NAME' => $disbursement->MIDDLE_NAME,
                'LAST_NAME' => $disbursement->LAST_NAME,
                'MOTHER_NAME' => $disbursement->MOTHER_NAME,
                'RESI_STATUS' => $disbursement->RESI_STATUS,
                'NATIONALITY_CODE' => $disbursement->NATIONALITY_CODE,
                'SEC_ID_TYPE' => $disbursement->SEC_ID_TYPE,
                'AADHAR_NO' => $disbursement->AADHAR_NO,
                'CKYC_DATE' => $disbursement->CKYC_DATE,
                'SANCTION_DATE' => $disbursement->SANCTION_DATE,
                'POS' => $disbursement->POS,
                'INSURANCE_FINANCED' => $disbursement->INSURANCE_FINANCED,
                'REMAINING_LOAN_TENURE' => $disbursement->REMAINING_LOAN_TENURE,
                'Total_Weight' => $disbursement->Total_Weight,
                'Name_Valuer' => $disbursement->Name_Valuer,
                'Role_Valuer' => $disbursement->Role_Valuer,
                'Gross_weight' => $disbursement->Gross_weight,
                'Total_Weight_Valuer' => $disbursement->Total_Weight_Valuer,
                'Gold_Value' => $disbursement->Gold_Value,
                'Net_Weight' => $disbursement->Net_Weight,
                'Gold_Rate' => $disbursement->Gold_Rate,
                'Market_Rate' => $disbursement->Market_Rate,
                'Total_Value' => $disbursement->Total_Value,
                'Repayment_Type' => $disbursement->Repayment_Type,
                'Date_Disbursement' => $disbursement->Date_Disbursement,
                'Maturity_Date' => $disbursement->Maturity_Date,
                'Account_status' => $disbursement->Account_status,
                'Gold_Purity' => $disbursement->Gold_Purity,
                'Disbursal_Mode' => $disbursement->Disbursal_Mode,
                'Collateral_Description' => $disbursement->Collateral_Description,
                'Business_Type' => $disbursement->Business_Type,
                'Valuation_Date' => $disbursement->Valuation_Date,
                'Realizable_Security_Value' => $disbursement->Realizable_Security_Value,
                'REPAY_DAY' => $disbursement->REPAY_DAY,
                'EMI_START_DATE' => $disbursement->EMI_START_DATE,
                'MORATORIUM' => $disbursement->MORATORIUM,
                'Assets_ID' => $disbursement->Assets_ID,
                'Security_Interest_ID' => $disbursement->Security_Interest_ID,
                'cersai_date' => $disbursement->cersai_date,
                'CIC' => $disbursement->CIC

            ]);

            LoanEntry::create([
                'loan_id' => $loan_account->loan_id,
                'entry_date' => $loan_account->bank_loan_date,
                'entry_month' => Carbon::parse($loan_account['bank_loan_date'])->month,
                'entry_year' => Carbon::parse($loan_account['bank_loan_date'])->year,
                'bank_date' => $loan_account->bank_loan_date,
                'entry_timestamp' => date('Y-m-d'),
                'debit' => $loan_account->bank_sanction_amount,
                'total_debit' => $loan_account->bank_sanction_amount,
                'bank_debit' => $loan_account->bank_sanction_amount,
                'nbfc_debit' => $loan_account->nbfc_sanction_amount,
                'balance' => $loan_account->total_balance,
                'bank_balance' => $loan_account->bank_balance,
                'nbfc_balance' => $loan_account->nbfc_balance,
                'description' => 'Loan Disbursed',
                'head' => 'principal',
                'jnl_no' => '123456',
                'principal_balance' => $loan_account->bank_sanction_amount,
                'principal_bank_balance' => $loan_account->bank_sanction_amount,
                'principal_nbfc_balance' => $loan_account->nbfc_sanction_amount,
            ]);
            $disbursement->update(['status' => 'Done']);
        }
    }
    public function disbursment(Request $request)
    {
        
        $data = $request->all();
        $batch_id = $data['batch_id'];
        $batch = Disbursebatch::where('uuid', $data['batch_id'])->where('status', 'Approved')->get()[0];
        // $disbursement = Disbursement::select(DB::raw("SUM(loan_amount) as loan_amount, SUM(sanction_amount) as sanction_amount, SUM(nbfc_sanction_amount) as nbfc_sanction_amount, SUM(bank_sanction_amount) as bank_sanction_amount"))
        //     ->where('status', 'Approved')->get()[0];
        $results = Disbursement::select(DB::raw('Business_Type,SUM(sanction_amount) as sanction_amount, SUM(nbfc_sanction_amount) as nbfc_sanction_amount, SUM(bank_sanction_amount) as bank_sanction_amount'))
                
                ->where('batch_id', $batch_id)
                ->where('status', 'Approved')
                ->groupBy('Business_Type')
                ->get();
	$i = 12;
	$depositAccount = "";
//	dd($results);
        foreach ($results as $result) {
            /**
             * 
             */
            if(strtolower($result->Business_Type)=="agri"){
		    $loan_account_number = config('global.loan_account_number_agri');
		    $depositAccount = config('global.to_loan_account_number_agri');
            }
            else{
		    $loan_account_number = config('global.loan_account_number_msme');
		    $depositAccount = config('global.to_loan_account_number_msme'); 
	    }

//		dd($result->Business_Type, $loan_account_number); 
            $amount = $result->bank_sanction_amount;
	    if ($amount) {
     //           $journalNo = $this->disbursmentByTransafer($batch_id, $amount,$loan_account_number, $depositAccount);
	      	   $journalNo='1234567';
		    $i++;
		   if ($journalNo) {
			   $journalNo = $journalNo.$i;
                    DB::transaction(
                        function () use ($journalNo, $batch_id, $loan_account_number, $batch,$result) {
                            if($result->Business_Type=="Agri"){
                                $batch->update(['journalNoAGRI' => $journalNo, 'status' => 'Disbursed', 'loan_account_number_agri' => $loan_account_number]);
                            }
                            else{
                                $batch->update(['journalNoMSME' => $journalNo, 'status' => 'Disbursed', 'loan_account_number_msme' => $loan_account_number]);
                            }
                            Disbursement::where('batch_id', $batch_id)->where('Business_Type',$result->Business_Type)->where('status', 'Approved')->update(['status' => 'Disbursed', 'utr_bom_pos_update' => $journalNo, 'loan_account_number' => $loan_account_number]);
                            $this->createLoanAccount($batch_id);
                        }
                    );
                    
                }
            }
        }       
        return redirect(route('disbursebatch.show', [$batch_id]))->with('success', '₹' . $amount . ' Disbursed successfully to account number ' . $loan_account_number . '! Data will be process in background will available soon for download.');
    }
    public function disbursmentByTransafer($batch_id, $amount,$loan_account_number, $depositAccount)
    {
        /***
         * {"endpoint":"http:\/\/10.128.5.24:9993\/OTAPI\/COLENDING\/disbursmentByTransafer","body":{"data":{"requestId":"req_kBsQplcWgtoe","requestType":"DisbursementByTransfer","fromLoanAccount":"60492665728","requestMaker":"38839","requestChecker1":"29871","requestBranch":"43","currencyCode":"INR","currencyAmount":"160020","toDepositAccount":"60403783973"},"reqTimestamp":"kBsQplcWgtoe","vendor":"COLENDING","requestId":"req_kBsQplcWgtoe","client":"BOM","chksum":"13456"},"headers":{"accept":"application\/json","content-type":"application\/json"},"data_format":"body","timeout":"5000"}
         */
        //$loan_account_number = config('global.loan_account_number');
        try {
            $requestId = 'req' . time();
            $arr = array(
                "data" => array(
                    'requestId' => $requestId,
                    'requestType' => 'DisbursementByTransfer',
                    'fromLoanAccount' => $loan_account_number,
                    'requestMaker' => '37510',
                    'requestChecker1' => '34517',
                    'requestBranch' => '2',
                    'currencyCode' => 'INR',
                    'currencyAmount' => $amount,
                    'toDepositAccount' => $depositAccount
                ),
                "reqTimestamp" => time(),
                "vendor" => "COLENDING",
                "requestId" => $requestId,
                "client" => "BOM",
                "chksum" => "13456"
	);
    
	    dd($arr);
	    /*
            $response = $this->client->request('POST', '/OTAPI/COLENDING/disbursmentByTransafer', [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => $arr
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);

           /*
 $response = '{"vendor":"COLENDING","client":"BOM","requestId":"req_kBsQplcWgtoe","resTimestamp":"20240610174139276","data":{"requestId":"req_kBsQplcWgtoe","requestType":"DisbursementByTransfer","status":"S","responseCode":"00","responseMessage":"Success","journalNo":"019391172"},"chksum":"13456"}';
            $data = json_decode($response, TRUE);
*/
	    //dd($data['data']['journalNo']);
            $journalNo = isset($data['data']['journalNo'])?$data['data']['journalNo']:NULL;
            if ($journalNo) {
                CbsApi::create([
                    'batch_id' => $batch_id,
                    'cbs_api' => env('CBS_API') . '/OTAPI/COLENDING/disbursmentByTransafer',
                    'request' => json_encode($arr),
                    'response' => json_encode($data),
                    'pf_number' => Auth::user()->name,
                    'status' => 'Success'
                ]);
                return $journalNo;
            } else {
                CbsApi::create([
                    'batch_id' => $batch_id,
                    'cbs_api' => env('CBS_API') . '/OTAPI/COLENDING/disbursmentByTransafer',
                    'request' => json_encode($arr),
                    'response' => json_encode($data),
                    'pf_number' => Auth::user()->name,
                    'status' => 'Failed'
                ]);
                return false;
            }
        } catch (RequestException $e) {
            /** */
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $errorMessage = $response->getReasonPhrase();
                $errorBody = json_decode($response->getBody()->getContents(), true);

                $data = response()->json(['error' => $errorMessage, 'details' => $errorBody], $statusCode);
            } else {

                $data = response()->json(['error' => 'An unexpected error occurred'], 500);
            }
            CbsApi::create([
                'batch_id' => $batch_id,
                'cbs_api' => env('CBS_API') . '/OTAPI/COLENDING/disbursmentByTransafer',
                'request' => json_encode($arr),
                'response' => $data,
                'pf_number' => Auth::user()->name,
                'status' => 'Failed'
            ]);

            return false;
        }
    }
    public function cbsneftapi($batch_id, $amount, $loan_account_number)
    {
        /**
         * Collection to NBFC Account NEFT/RTGS
         */
        try {
            $requestId = 'req' . time();
            $arr = array(
                "data" => array(
                    'requestId' => $requestId,
                    'transactionType' => 'NEFT',
                    'requestChecker1' => '29871',
                    'requestMaker' => '38839',
                    'requestBranch' => '43',
                    'amount' => $amount,
                    'from_Account_No' => '60492596660',
                    'to_AccountNo' => $loan_account_number,
                    'detailsOfPayment' => $batch_id . "-Collection",
                    'ifscCode' => '',
                    'ecs_cust_name' => '',
                    'mobileNumber' => '',
                    'beneNameAddress1' => '',
                    'requestType' => 'NEFTRTGSByTransfer'
                ),
                "reqTimestamp" => time(),
                "vendor" => "COLENDING",
                "requestId" => $requestId,
                "client" => "BOM",
                "chksum" => "13456"
            );
            //dd(json_encode($arr));
            $response = $this->client->request('POST', '/NEFTRTGSByTransfer', [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => $arr
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            CbsApi::create([
                'batch_id' => $batch_id,
                'cbs_api' => 'NEFTRTGSByTransfer',
                'request' => json_encode($arr),
                'response' => $data,
                'pf_number' => Auth::user()->name,
                'status' => 'Success'
            ]);
            return true;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $errorMessage = $response->getReasonPhrase();
                $data1 = json_decode($response->getBody()->getContents(), true);

                $data = response()->json(['error' => $errorMessage, 'details' => $data1], $statusCode);
            } else {
                $data = response()->json(['error' => 'An unexpected error occurred'], 500);
            }
            CbsApi::create([
                'batch_id' => $batch_id,
                'cbs_api' => 'NEFTRTGSByTransfer',
                'request' => json_encode($arr),
                'response' => $data,
                'pf_number' => Auth::user()->name,
                'status' => 'Failed'
            ]);
            return false;
        }
    }
    public function testcbs()
    {
        $this->cbsapi('BATCH123456', 20000);
    }
    public function cbsapi($batch_id, $amount)
    {
        /** Payment Posting
         * Collection to Loan account Credit
         */
        try {
            $requestId = 'req' . time();
            $arr = array(
                "data" => array(
                    'request_id' => $requestId,
                    'requestChecker1' => '29871',
                    'requestMaker' => '38839',
                    'requestBranch' => '43',
                    'amount' => $amount,
                    'from_Account_No' => '60492596660',
                    'to_AccountNo' => '60492665728',
                    'statementNarrative' => $batch_id . "-Collection",
                    'fromAccountType' => 'DEP',
                    'toAccountType' => 'LOAN',
                    'requestType' => 'PaymentPosting'
                ),
                "reqTimestamp" => time(),
                "vendor" => "COLENDING",
                "request_id" => $requestId,
                "client" => "BOM",
                "chksum" => "13456"
            );
            //dd($this->client->getConfig('base_uri'));
            $response = $this->client->request('POST', '/PaymentPosting', [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => $arr
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            CbsApi::create([
                'batch_id' => $batch_id,
                'cbs_api' => 'PaymentPosting',
                'request' => json_encode($arr),
                'response' => $data,
                'pf_number' => Auth::user()->name,
                'status' => 'Success'
            ]);
            return true;
            //return <utr_number from response data>;
        } catch (RequestException $e) {
            //dd($e);
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $errorMessage = $response->getReasonPhrase();
                $errorBody = json_decode($response->getBody()->getContents(), true);

                $data = response()->json(['error' => $errorMessage, 'details' => $errorBody], $statusCode);
            } else {

                $data = response()->json(['error' => 'An unexpected error occurred'], 500);
            }
            CbsApi::create([
                'batch_id' => $batch_id,
                'cbs_api' => 'NEFTRTGSByTransfer',
                'request' => json_encode($arr),
                'response' => $data,
                'pf_number' => Auth::user()->name,
                'status' => 'Failed'
            ]);

            return false;
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
        $data = $request->all();
        $batch_id = $data['batch_id'];
        $cbs_api = $data['cbs_api'];
        $batch = Batch::where('uuid', $data['batch_id'])->get()[0];
	$utr_number = '';
	$closing_date_time = Null;
        $amount = $batch->total_principal + $batch->total_interest;
        if ($cbs_api == 'collection') {
            //$utr_number =  $this->cbsapi($batch_id, $amount);

            $results = Collection::select(DB::raw('SUM(PRINCIPAL_AMT) as principal,SUM(INTEREST_AMT) as interest,loan_account_number'))
                ->groupBy('loan_account_number')
                ->where('batch_id', $batch_id)
                ->where('status', 'Pending')
                ->get();
            //dd($results);
            $i = 12;
            foreach ($results as $result) {

                $total_amount = $result->principal + $result->interest;
                //$this->cbsneftapi($batch_id, $total_amount, $result->loan_account_number);
                $utr_number = 'UTRC123456' . $i;
                $i++;
                $collections = Collection::where('batch_id', $batch_id)
                    ->where('status', 'Pending')
                    ->where('loan_account_number', $result->loan_account_number)
                    ->get();
                if ($utr_number) {

                    foreach ($collections as $collection) {
                        $month_name = date("F", mktime(0, 0, 0, $collection->MONTH, 10));
                        $loan_account = LoanAccount::where('mfl_ref_no', $collection['REQ_NUMBER'])->get()[0];

                        $loan_entries = LoanEntry::where('loan_id', $loan_account->loan_id)->latest('id')->first();


                        //Calculate Interest til Date from last entry
                        $last_date = $loan_entries->entry_date;
                        //$end_date =  date("Y-m-d");
                        $end_date = '2024-09-02';

                        $total_interest = Interest::where('loan_id', $loan_account->loan_id)->where('interest_date', '>=', $last_date)->where('interest_date', '<=', $end_date)->sum('total_interest');
                        $bank_interest = Interest::where('loan_id', $loan_account->loan_id)->where('interest_date', '>', $last_date)->where('interest_date', '<=', $end_date)->sum('bank_interest');
                        $nbfc_interest = Interest::where('loan_id', $loan_account->loan_id)->where('interest_date', '>=', $last_date)->where('interest_date', '<=', $end_date)->sum('nbfc_interest');


                        //Calculate total outstanding till Date
                        $total_outstanding = round($loan_entries->balance + $total_interest, 2);
                        $bank_outstanding = $loan_entries->principal_bank_balance + $bank_interest;
                        $nbfc_outstanding = round($loan_entries->nbfc_balance + $nbfc_interest, 2);

			$total = $collection->final_principal + $collection->final_interest;
                        if ($total >= $bank_outstanding) {
			    $loan_status = 'closed';
 			    $closing_date_time  = date('Y-m-d H:i:s');
                            $endofMonth = Carbon::parse($end_date);
                            $bank_roi = config('global.bank_interest');
                            $nbfc_roi = $loan_account->nbfc_interest;
                            InterestMonth::create([
                                'loan_id' => $loan_account->loan_id,
                                'interest_date' => $endofMonth->toDateString(),
                                'interest_month' => $endofMonth->format('m'),
                                'interest_year' => $endofMonth->format('Y'),
                                'bank_roi' => $bank_roi,
                                'nbfc_roi' => $nbfc_roi,
                                'total_interest' => $total_interest,
                                'bank_interest' => $bank_interest,
                                'nbfc_interest' => $nbfc_interest,
                                'interest_type' => 'monthly'
                            ]);
                            $balance1 =  $loan_entries->balance;
                            $bank_balance1 = $loan_entries->bank_balance;
                            $nbfc_balance1 = $loan_entries->nbfc_balance;
                            $principal_balance1 =  $loan_entries->principal_balance;
                            $principal_nbfc_balance1 =  $loan_entries->principal_nbfc_balance;
                            $nbfc_balance1 = $loan_entries->nbfc_balance;
                            $total_interest = ($principal_balance1 * ($nbfc_roi / 365)) / 100;
                            $nbfc_interest = ($principal_nbfc_balance1 * ($nbfc_roi / 365)) / 100;
                            LoanEntry::create([
                                'loan_id' => $loan_account->loan_id,
                                'entry_month' => $endofMonth->format('m'),
                                'entry_year' => $endofMonth->format('Y'),
                                'entry_date' => $endofMonth->format('Y-m-d'),
                                'description' => 'Interest of ' . $endofMonth->format('M') . '-' . $endofMonth->format('Y'),
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
                        } else {
                            $loan_status = 'active';
			}
                        $loan_entries = LoanEntry::where('loan_id', $loan_account->loan_id)->latest('id')->first();
                        DB::transaction(
                            function () use ($loan_account, $collection, $month_name, $utr_number, $loan_entries, $loan_status, $closing_date_time) {
                                $balance1 =  $loan_entries->balance - $collection->final_principal;
                                $bank_balance1 = $loan_entries->bank_balance - $collection->bank_principal;
                                $nbfc_balance1 = $loan_entries->nbfc_balance - $collection->nbfc_principal;


                                //$loan_entries_principal = LoanEntry::where('loan_id', $loan_account->loan_id)->where('head', 'principal')->latest('id')->first();
                                //$loan_entries_interest = LoanEntry::where('loan_id', $loan_account->loan_id)->where('head', 'interest')->latest('id')->first();

                                $pricipal = $loan_entries->principal_balance - $collection->final_principal;
                                $pricipal_bank = $loan_entries->principal_bank_balance - $collection->bank_principal;
                                $pricipal_nbfc = $loan_entries->principal_nbfc_balance - $collection->nbfc_principal;

                                $interest = 0;
                                $interest_bank = 0;
                                $interest_nbfc = 0;
                                //$day = date('d');
                                $day = 02;
                                    LoanEntry::create([
                                        'loan_id' => $loan_account->loan_id,
                                        'entry_month' => $collection->MONTH,
                                        'entry_year' => $collection->YEAR,
                                        'entry_date' => $collection->YEAR . '-' . $collection->MONTH . '-' . $day,
                                        'description' => 'Principal-' . $month_name . '-' . $collection->YEAR,
                                        'total_credit' => $collection->PRINCIPAL_AMT,
                                        'credit' => $collection->PRINCIPAL_AMT,
                                        'bank_credit' => $collection->bank_principal,
                                        'nbfc_credit' => $collection->nbfc_principal,
                                        'balance' => $balance1,
                                        'bank_balance' => $bank_balance1,
                                        'nbfc_balance' => $nbfc_balance1,
                                        'jnl_no' => $utr_number,
                                        'head' => 'principal',
                                        'principal_balance' => $pricipal,
                                        'principal_bank_balance' => $pricipal_bank,
                                        'principal_nbfc_balance' => $pricipal_nbfc,
                                        'interest_balance' => $loan_entries->interest_balance,
                                        'interest_bank_balance' => $loan_entries->interest_bank_balance,
                                        'interest_nbfc_balance' => $loan_entries->interest_nbfc_balance,
                                        'collection_id' => $collection->id,
                                        'pf_number' => Auth::user()->name
                                    ]);
                                $balance2 =  $balance1 - $collection->final_interest;
                                $bank_balance2 =  $bank_balance1 - $collection->bank_interest;
                                $nbfc_balance2 =  $nbfc_balance1 - $collection->nbfc_interest;

                                $interest = $loan_entries->interest_balance - $collection->final_interest;
                                $interest_bank = $loan_entries->interest_bank_balance - $collection->bank_interest;
                                $interest_nbfc = $loan_entries->interest_nbfc_balance - $collection->nbfc_interest;

                                LoanEntry::create([
                                    'loan_id' => $loan_account->loan_id,
                                    'entry_month' => $collection->MONTH,
                                    'entry_year' => $collection->YEAR,
                                    'entry_date' => $collection->YEAR . '-' . $collection->MONTH . '-' . $day,
                                    'description' => 'Interest-' . $month_name . '-' . $collection->YEAR,
                                    'total_credit' => $collection->INTEREST_AMT,
                                    'credit' => $collection->INTEREST_AMT,
                                    'bank_credit' => $collection->bank_interest,
                                    'nbfc_credit' => $collection->nbfc_interest,
                                    'balance' => $balance2,
                                    'bank_balance' => $bank_balance2,
                                    'nbfc_balance' => $nbfc_balance2,
                                    'jnl_no' => $utr_number,
                                    'head' => 'interest',
                                    'principal_balance' => $pricipal,
                                    'principal_bank_balance' => $pricipal_bank,
                                    'principal_nbfc_balance' => $pricipal_nbfc,
                                    'interest_balance' => $interest,
                                    'interest_bank_balance' => $interest_bank,
                                    'interest_nbfc_balance' => $interest_nbfc,
                                    'collection_id' => $collection->id,
                                    'pf_number' => Auth::user()->name
                                ]);


                                /**
                                 * Muthoot Sprade entry
                                 *
                                 */
                                /*
                        LoanEntry::create([
                            'loan_id' => $loan_account->loan_id,
                            'entry_month' => $collection->MONTH,
                            'entry_year' => $collection->YEAR,
                            'entry_date' => $collection->YEAR . '-' . $collection->MONTH . '-' . date('d'),
                            'description' => 'NBFC Sprade-' . $month_name . '-' . $collection->YEAR,
                            'total_credit' => $collection->mfl_sprade,
                            'credit' => $collection->mfl_sprade,
                            'nbfc_credit' => $collection->mfl_sprade,
                            'balance' => $balance2,
                            'bank_balance' => $bank_balance2,
                            'nbfc_balance' => $nbfc_balance2 + $collection->mfl_sprade,
                            'jnl_no' => $utr_number,
                            'head' => 'nbfc sprade',
                            'principal_balance' => $pricipal,
                            'principal_bank_balance' => $pricipal_bank,
                            'principal_nbfc_balance' => $pricipal_nbfc,
                            'interest_balance' => $interest,
                            'interest_bank_balance' => $interest_bank,
                            'interest_nbfc_balance' => $interest_nbfc
                        ]);
                        */
                                $collection->update(['status' => 'Done']);
                                $loan_account->update(['loan_status' => $loan_status, 'closing_date' => $closing_date_time]);
                            }
                        );
                    }
                } else {
                    return redirect()->back()->with('error', 'CBS API FAILED PLEASE CHECK LOG FOR MORE DETAILS');
                }
            }
            $batch->update(['status' => 'Done']);
            return redirect()->back()->with('success', 'Amount Credited to collection account');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\cbs_api  $cbs_api
     * @return \Illuminate\Http\Response
     */
    public function show(CbsApi $cbs_api)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\cbs_api  $cbs_api
     * @return \Illuminate\Http\Response
     */
    public function edit(CbsApi $cbs_api)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\cbs_api  $cbs_api
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CbsApi $cbs_api)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\cbs_api  $cbs_api
     * @return \Illuminate\Http\Response
     */
    public function destroy(CbsApi $cbs_api)
    {
        //
    }
}
