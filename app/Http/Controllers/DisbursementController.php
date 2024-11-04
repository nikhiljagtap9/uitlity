<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Bharatpay;
use App\Models\Cibilscorebucket;
use App\Models\Disbursebatch;
use App\Models\Disbursement;
use App\Models\Ntc;
use App\Models\Scoreband;
use App\Models\Segments;
use App\Models\Tenure;
use App\Models\Tpvindex;
use App\Services\CbsService;
use App\Services\CkycService;
use App\Services\KycService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\GoldRate;

class DisbursementController extends Controller
{
    protected $ckycService;
    protected $cbsApiService;
    protected $KycApiService;
    public function __construct(CkycService $ckycService, CbsService $cbsApiService, KycService $KycApiService)
    {
        $this->ckycService = $ckycService;
        $this->cbsApiService = $cbsApiService;
        $this->KycApiService = $KycApiService;
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
        return view('disbursement.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $path = $request->file('upload')->store('csv_files');

        $batch = Disbursebatch::create([
            'status' => 'Pending',
            'pf_number' => Auth::user()->name
        ]);
        // Process the CSV file
        $file = fopen(storage_path('app/' . $path), 'r');
        if (!file_exists(storage_path('app/' . $path)) || !is_readable(storage_path('app/' . $path))) {
            $this->error('CSV file not found or not readable.');
            return 1;
        }
        $header = null;
        $total_loan_amount = $total_sanction_amount = $nbfc_sanction_amount = $bank_sanction_amount = 0;
        $datatostore = [];
        while ($row = fgetcsv($file)) {
            // Process each row
            if (!$header) {
                $header = $row;
            } else {
                $data = array_combine($header, $row);
                $datatostore['status'] = 'Pending';
                if ($data['Nbfc_reference_number']) {
                    $NBFC_Reference_Number = $data['Nbfc_reference_number'];
                    $count = Disbursement::where('NBFC_Reference_Number', $NBFC_Reference_Number)->where('batch_id', $batch->uuid)->count();
                    if ($count > 0) {
                        $datatostore['status'] = 'Duplicate';
                    } else {
                        $count = Disbursement::where('NBFC_Reference_Number', $NBFC_Reference_Number)->where('status','!=', 'Rejected')->count();
                        if ($count > 0) {
                            $datatostore['status'] = 'Duplicate';
                        }
                    }
                }
                $datatostore['pf_number'] = Auth::user()->name;
                $datatostore['partner_id'] = $request->partner_id;
                $datatostore['product_id'] = $request->product_id;
                $datatostore['batch_id'] = $batch->uuid;

                $total_loan_amount = (float)$data['Sanction_limit'] + $total_loan_amount;
                $total_sanction_amount = (float)$data['POS'] + $total_sanction_amount;
                $nbfc_sanction_amount = ((float)$data['POS'] *0.2 ) + $nbfc_sanction_amount;
                $bank_sanction_amount = ((float)$data['POS']*0.8) + $bank_sanction_amount;
                $datatostore['nbfc_sanction_amount'] = (float)$data['POS']*0.2;
                $datatostore['SANCTION_DATE'] = $data['Sanction_date'];
                $datatostore['Market_Rate'] = str_replace(',', '', $data['Market_rate']);
                $datatostore['Total_Value'] = str_replace(',', '', $data['Total_value']);
                $datatostore['Date_Disbursement'] = $data['Date_disbursement'];
                $datatostore['Maturity_Date'] = $data['Maturity_date'];
                $datatostore['EMI_START_DATE'] = $data['Emi_start_date'];
                $datatostore['cersai_date'] = $data['Cersai_date'];
                $datatostore['sanction_amount'] = $data['POS'];
                $datatostore['dob'] = $data['Dob'];
                $datatostore['bank_sanction_amount'] = (float)$data['POS']*0.8;
                $datatostore['SEC_ID_TYPE'] = $data['Sec-id-type'];
                $datatostore['NBFC_Reference_Number'] = $data['Nbfc_reference_number'];
                $datatostore['CGCL_Customer_Number'] = $data['Cgcl_customer_number'];
                $datatostore['CGCL_Account_Number'] = $data['Cgcl_account_number'];
                $datatostore['TITLE'] = $data['Title'];
                $datatostore['CUSTOMER_NAME'] = $data['Customer_name'];
                $datatostore['FIRST_NAME'] = $data['First_name'];
                $datatostore['MIDDLE_NAME'] = $data['Middle_name'];
                $datatostore['LAST_NAME'] = $data['Last_name'];
                $datatostore['GENDER'] = $data['Gender'];
                $datatostore['MOBILE_NO'] =str_replace('mo','',$data['Mobile_no']);
                $datatostore['EMAIL'] = $data['City'];
                $datatostore['AGE'] = $data['Age'];
                $datatostore['ADD1'] = $data['Add1'];
                $datatostore['ADD2'] = $data['Add2'];
                $datatostore['CITY'] = $data['City'];
                $datatostore['STATE'] = $data['State'];
                $datatostore['ZIPCODE'] = $data['Zipcode'];
                $datatostore['RESI_STATUS'] = $data['Resi-status'];
                $datatostore['MOTHER_NAME'] = $data['Mother_name'];
                $datatostore['NATIONALITY_CODE'] = $data['Nationality-code'];
                $datatostore['loan_amount'] = $data['Sanction_limit'];
                $datatostore['LOAN_BOOKING_DATE'] = $data['Sanction_date'];
                $datatostore['LOAN_TENURE'] = $data['Loan_tenure'];
                $datatostore['REMAINING_LOAN_TENURE'] = $data['Remaining_loan_tenure'];
                $datatostore['Total_Weight_Valuer'] = $data['Total_weight_valuer'];
                $datatostore['Gross_weight'] = $data['Gross_weight'];
                $datatostore['Gold_Value'] = $data['Gold_value'];
                $datatostore['Gold_Rate'] = $data['Gold_rate'];
                $datatostore['Net_Weight'] = $data['Net_weight'];
                $datatostore['Total_Weight'] = $data['Total_weight'];
                $datatostore['LTV'] = $data['LTV'];
                $datatostore['Gold_Purity'] = $data['Gold_purity'];
                $datatostore['PAN'] = $data['Pan'];
                $datatostore['ckyc'] = str_replace('ckyc','',$data['Ckyc_number']);
                $datatostore['CKYC_DATE'] = $data['Ckyc date'];
                $datatostore['POS'] = $data['POS'];
                $datatostore['INSURANCE_FINANCED'] = $data['Insurance_financed'];
                $datatostore['AADHAR_NO'] = $data['Aadhar_no'];
                $datatostore['Pos_Including_insurance'] = $data['Pos_including_insurance'];
                $datatostore['Name_Valuer'] = $data['Name_valuer'];
                $datatostore['Role_Valuer'] = $data['Role_valuer'];
                $datatostore['Repayment_Type'] = $data['Repayment_type'];
                $datatostore['Account_status'] = $data['Account_status'];
                $datatostore['Collateral_Description'] = $data['Collateral_description'];
                $datatostore['Business_Type'] = $data['Business_type'];
                $datatostore['Valuation_Date'] = $data['Valuation_date'];
                $datatostore['Realizable_Security_Value'] = $data['Realizable_security_value'];
                $datatostore['customer_selection'] = '';
                $datatostore['REPAY_DAY'] = $data['Repay_day'];
                $datatostore['MORATORIUM'] = $data['Moratorium'];
                $datatostore['DISBURSMENT_DETAIL'] = $data['Disbursal_mode'];
                $datatostore['Disbursal_Mode'] = $data['Disbursal_mode'];
                $datatostore['Assets_ID'] = $data['Assets_id'];
                $datatostore['Security_Interest_ID'] = $data['Security_interest_id'];
                $datatostore['CIC'] = $data['CIC'];
                $datatostore['CGCL_ROI'] = $data['CGCL_ROI'];
                $datatostore['Udyam_no'] = isset($data['Udyam_no'])?$data['Udyam_no']:NULL;
                 //dd($datatostore);

                DB::enableQueryLog();
                Disbursement::create($datatostore);

                $queries = DB::getQueryLog();
            }
        }
        // dd($total_loan_amount,$total_sanction_amount,$nbfc_sanction_amount,$bank_sanction_amount);
        $batch->update([
            'total_loan_amount' => $total_loan_amount,
            'total_sanction_amount' => $total_sanction_amount,
            'nbfc_sanction_amount' => $nbfc_sanction_amount,
            'bank_sanction_amount' => $bank_sanction_amount

        ]);
        fclose($file);
        return redirect(route('disbursebatch.show', [$batch->uuid]))->with('success', 'CSV file uploaded successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Disbursement  $disbursement
     * @return \Illuminate\Http\Response
     */
    public function show(Disbursement $disbursement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Disbursement  $disbursement
     * @return \Illuminate\Http\Response
     */
    public function edit(Disbursement $disbursement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Disbursement  $disbursement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Disbursement $disbursement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Disbursement  $disbursement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Disbursement $disbursement)
    {
        //
    }

    public function processChunks(Request $request)
    {
        
        /**
         *
         */
        // Get the current offset from the request, default to 0 if not set
        $Disbursebatch = Disbursebatch::where('uuid', $request->batch_id)->get()[0];
        //$Disbursebatch = Disbursebatch::where('uuid', 'BATCH858527906698338')->get()[0];
        $offset = $request->input('offset', 0);
        // Define the chunk size
        $chunkSize = 10;
        // Fetch the chunk of data
        $chunk = Disbursement::where('status', 'Pending')->where('batch_id', $request->batch_id)->take($chunkSize)->get();

        // Process each item in the chunk
        foreach ($chunk as $disbursement) {
            // Your processing logic here
            $res = $this->checkBre($disbursement);
            if ($res['status'] == 'Rejected') {
                $disbursement->update([
                    'message' => $res['message'],
                    'status' => 'Rejected'
                ]);
            } else {
                $disbursement->update([
                    'message' => 'BRE Approved',
                    'status' => 'Approved'
                ]);
            }
        }
        // Check if there are more items to process
        $moreData = Disbursement::where('status', 'Pending')->where('batch_id', $request->batch_id)->exists();
        if (!$moreData) {
            $Disbursebatch->update(['status' => 'Approved']);
        }
        // Return response indicating the next step
        return response()->json([
            'moreData' => $moreData,
            'nextOffset' => $offset + $chunkSize
        ]);
    }
    public function runBre(Request $request)
    {
        Disbursement::where('status', 'Pending')->where('batch_id', $request->batch_id)->chunk(
            1000,
            function ($disbursements) {
                foreach ($disbursements as $disbursement) {
                    /**
                     * Process BRE on each application
                     */
                    $res = $this->checkBre($disbursement);
                    if ($res['status'] == 'Rejected') {
                        $disbursement->update([
                            'message' => $res['message'],
                            'status' => 'Rejected'
                        ]);
                    } else {
                        $disbursement->update([
                            'message' => 'BRE Approved',
                            'status' => 'Approved'
                        ]);
                    }
                }
            }
        );

        Batch::where('uuid', $request->batch_id)->update(['status' => 'Approved']);
        return redirect(route('disbursebatch.show', [$request->batch_id]))->with('success', 'BRE processed successfully!');
    }
    private function apiVerifytest($data)
    {
       /*     if ($pan_details === false) {
                $arr['message'] = 'Pan Verification Failed';
                $arr['status'] = 'Rejected';
                return $arr;
            } else {
                $percentage = 0;
                similar_text(strtolower($data->CUSTOMER_NAME), strtolower($pan_details['fullName']), $percentage);
                $roundedPercentage = round($percentage, 2); // Rounds to 2 decimal places
                if ($roundedPercentage < 75) {
                    $arr['message'] = 'Pan Verification Failed, Name Match Per. is ' . $roundedPercentage . '%';
                    $arr['status'] = 'Rejected';
                    return $arr;
                }
            }
        } else {
            $arr['message'] = 'PAN Number Not Available';
            $arr['status'] = 'Rejected';
            return $arr;
	}
	
	*/
	    if ($data->ckyc) {
            $response = $this->ckycService->ckycverify($data->ckyc, $data->dob,$data);
	    if($response==false){
	    	$arr['message'] = 'CKYC API Failed';
                $arr['status'] = 'API Failed';
                return $arr;

	    }
	    $arr = json_decode($response,TRUE);
	    if(isset($arr['CKYC_INQ']['ERROR'])){
			
 		$arr['message'] = $arr['CKYC_INQ']['ERROR'];
            	$arr['status'] = 'Rejected';
            	return $arr;
                        }
                        
	    $personal_detail =isset($arr['PID']['PID_DATA']['PERSONAL_DETAILS'])?$arr['PID']['PID_DATA']['PERSONAL_DETAILS']:NULL;
            $full_name = isset($personal_detail['FULLNAME'])?$personal_detail['FULLNAME']:NULL;
            $pan_no = isset($personal_detail['PAN'])?$personal_detail['PAN']:NULL;
            $image_details = isset($arr['PID']['PID_DATA']['IMAGE_DETAILS'])?$arr['PID']['PID_DATA']['IMAGE_DETAILS']:NULL;
            if($pan_no){
                                if($data->PAN!=$pan_no){
                                        $arr['message'] = 'Invalid CKYC Details, PAN Not Matching';
                                        $arr['status'] = 'Rejected';
                                        return $arr;
                                }
                        }
                        if($full_name){
                $percentage = 0;
                similar_text(strtolower($data->CUSTOMER_NAME), strtolower($full_name), $percentage);
                $roundedPercentage = round($percentage, 2); // Rounds to 2 decimal places
                                //dd($roundedPercentage);
                if ($roundedPercentage < 75) {
                    $arr['message'] = 'Pan Verification Failed, Name Match Per. is ' . $roundedPercentage . '%';
                    $arr['status'] = 'Rejected';
                }
                        }

        } else {
            $arr['message'] = 'CKYC Number Not Available';
            $arr['status'] = 'Rejected';
            return $arr;
	}

	/*
        if ($data->Udyam_no) {
            $udyog_details = $this->KycApiService->udyamVerification($data->UDYAM_REGN_NO, $data);
            if ($udyog_details === false) {
                $arr['message'] = 'Udyam Aadhaar Verification Failed';
                $arr['status'] = 'Rejected';
                return $arr;
            } else {
                $percentage = 0;
                similar_text(strtolower($data->CUSTOMER_NAME), strtolower($udyog_details['nameOfEnterprise']), $percentage);
                $udyaogPer = round($percentage, 2); // Rounds to 2 decimal places

                if ($udyaogPer < 75) {
                    $arr['message'] = 'Udyam Aadhaar Verification Failed, Name Match Per. is ' . $udyaogPer . '%';
                    $arr['status'] = 'Rejected';
                    return $arr;
                }
            }
    }
        */

	 
        $arr['status'] = 'Approved';
        return $arr;
}

private function apiVerify($data)
    {
/*
        if ($data->PAN) {
            $pan_details = $this->KycApiService->PanVerification($data->PAN, $data);
            if ($pan_details === false) {
                $arr['message'] = 'Pan Verification Failed';
                $arr['status'] = 'Rejected';
                return $arr;
            } else {
                $percentage = 0;
    //            similar_text(strtolower($data->CUSTOMER_NAME), strtolower($pan_details['fullName']), $percentage);
       		$roundedPercentage = $this->nameMatchPercent(strtolower($data->CUSTOMER_NAME), strtolower($pan_details['fullName']));
		//$roundedPercentage = round($percentage, 2); // Rounds to 2 decimal places
		$data->update(['pan_match_score',$roundedPercentage]);
                if ($roundedPercentage < 75) {
                    $arr['message'] = 'Pan Verification Failed, Name Match Per. is ' . $roundedPercentage . '%';
                    $arr['status'] = 'Rejected';
                    return $arr;
                }
            }
        } else {
            $arr['message'] = 'PAN Number Not Available';
            $arr['status'] = 'Rejected';
            return $arr;
	}

 */
	    if ($data->ckyc) {
            $response = $this->ckycService->ckycverify($data->ckyc, $data->dob,$data);
	    if($response==false){
	    	$arr['message'] = 'CKYC API Failed';
                $arr['status'] = 'API Failed';
                return $arr;

	    }
	    $arr = json_decode($response,TRUE);
	    if(isset($arr['CKYC_INQ']['ERROR'])){

 		$arr['message'] = $arr['CKYC_INQ']['ERROR'];
            	$arr['status'] = 'Rejected';
            	return $arr;
                        }

	    $personal_detail =isset($arr['PID']['PID_DATA']['PERSONAL_DETAILS'])?$arr['PID']['PID_DATA']['PERSONAL_DETAILS']:NULL;
            $full_name = isset($personal_detail['FULLNAME'])?$personal_detail['FULLNAME']:NULL;
            $pan_no = isset($personal_detail['PAN'])?$personal_detail['PAN']:NULL;
            $image_details = isset($arr['PID']['PID_DATA']['IMAGE_DETAILS'])?$arr['PID']['PID_DATA']['IMAGE_DETAILS']:NULL;
            if($pan_no){
                                if($data->PAN!=$pan_no){
                                        $arr['message'] = 'Invalid CKYC Details, PAN Not Matching';
                                        $arr['status'] = 'Rejected';
                                        return $arr;
                                }
                        }
                        if($full_name){
				$percentage = 0;
				$roundedPercentage = $this->nameMatchPercent(strtolower($data->CUSTOMER_NAME), strtolower($full_name));
				$data->update(['ckyc_match_score',$roundedPercentage]);
               // similar_text(strtolower($data->CUSTOMER_NAME), strtolower($full_name), $percentage);
               // $roundedPercentage = round($percentage, 2); // Rounds to 2 decimal places
                                //dd($roundedPercentage);
                if ($roundedPercentage < 75) {
                    $arr['message'] = 'Pan Verification Failed, Name Match Per. is ' . $roundedPercentage . '%';
                    $arr['status'] = 'Rejected';
                }
                        }

        } else {
            $arr['message'] = 'CKYC Number Not Available';
            $arr['status'] = 'Rejected';
            return $arr;
	}
/*	
        if ($data->Udyam_no) {
            $udyog_details = $this->KycApiService->udyamVerification($data->Udyam_no, $data);
            if ($udyog_details === false) {
                $arr['message'] = 'Udyam Aadhaar Verification Failed';
                $arr['status'] = 'Rejected';
                return $arr;
            } else {
                $percentage = 0;
               // similar_text(strtolower($data->CUSTOMER_NAME), strtolower($udyog_details['nameOfEnterprise']), $percentage);
                //$udyaogPer = round($percentage, 2); // Rounds to 2 decimal places
		$udyaogPer = $this->nameMatchPercent(strtolower($data->CUSTOMER_NAME), strtolower($udyog_details['nameOfEnterprise']));
		$data->update(['udyam_match_score',$udyaogPer]);
                if ($udyaogPer < 75) {
                    $arr['message'] = 'Udyam Aadhaar Verification Failed, Name Match Per. is ' . $udyaogPer . '%';
                    $arr['status'] = 'Rejected';
                    return $arr;
                }
            }
    }
 */


        $arr['status'] = 'Approved';
        return $arr;
    }
    private function checkBre($data)
    {
        /**
         * PhonePay BRE
	 */
	    if($data->POS <= 0){
	   	$arr['message'] = 'Principal Outstanding can not be equal to or less than 0';
                $arr['status'] = 'Rejected';
                return $arr; 
	    } 
	    
	    if($data->sanction_amount <= 0){
                $arr['message'] = 'Sanction Amount can not be equal to or less than 0';
                $arr['status'] = 'Rejected';
                return $arr;
            }


	if(!$data->ckyc){
            $arr['message'] = 'Ckyc number empty';
            $arr['status'] = 'Rejected';
            return $arr;
        }	
	    $arr = array();
//		dd(strtolower($data->Business_Type));
	    if(strtolower($data->Business_Type) != 'agri' && strtolower($data->Business_Type) != 'msme'){
		$arr['message'] = 'Invalid Business Type';
                $arr['status'] = 'Rejected';
                return $arr;
	    }
	    
	    if((float)$data->POS > (float)$data->loan_amount){
	    	$arr['message'] = 'Principal Outstanding greater than Sanction Limit';
                $arr['status'] = 'Rejected';
                return $arr;
	    } 
	    if((float)$data->loan_amount<25000.00){
                $arr['message'] = 'Sanction Amount Less Than Min Limit';
                $arr['status'] = 'Rejected';
		return $arr;
	    }
	    $total_sanction_amt_pan_agri = 0;
	    $total_sanction_amt_pan_msme = 0;
	    $total_sanction_amt_ckyc_agri = 0;
            $total_sanction_amt_cky_msme = 0;
	    $total_combined_pan = 0;
	    $total_combined_ckyc = 0;

	    if($data->PAN){
	    $total_sanction_amt_pan_agri = Disbursement::select(DB::raw('SUM(loan_amount) as total_sanction_amt'))
		->where('PAN', $data->PAN)
		->where('Business_Type','Agri')
		->where('status', '!=','Duplicate')
                ->where('status', '!=','Rejected')
		->get()[0]->total_sanction_amt;
	    $total_sanction_amt_pan_msme = Disbursement::select(DB::raw('SUM(loan_amount) as total_sanction_amt'))
                ->where('PAN', $data->PAN)
		->where('Business_Type','Msme')
		->where('status', '!=','Duplicate')
		->where('status', '!=','Rejected')
		->get()[0]->total_sanction_amt;
	   }
	    $total_sanction_amt_ckyc_agri = Disbursement::select(DB::raw('SUM(loan_amount) as total_sanction_amt'))
                ->where('ckyc', $data->ckyc)
                ->where('Business_Type','Agri')
		->where('status', '!=','Duplicate')
		->where('status', '!=','Rejected')
                ->get()[0]->total_sanction_amt;

            $total_sanction_amt_ckyc_msme = Disbursement::select(DB::raw('SUM(loan_amount) as total_sanction_amt'))
                ->where('ckyc', $data->ckyc)
                ->where('Business_Type','Msme')
		->where('status', '!=','Duplicate')
		->where('status', '!=','Rejected')
                ->get()[0]->total_sanction_amt;

	    $total_combined_pan = $total_sanction_amt_pan_agri + $total_sanction_amt_pan_msme ;
	    $total_combined_ckyc = $total_sanction_amt_ckyc_agri + $total_sanction_amt_ckyc_msme; 
	   
	    if(strtolower($data->Business_Type) == 'agri'){
		    if((float)$total_sanction_amt_pan_agri>500000.00 ||(float)$total_sanction_amt_ckyc_agri>500000.00){

			    $arr['message'] = 'Sanction Amount Greater Than 5 lakhs';
                     $arr['status'] = 'Rejected';
                     return $arr;
		    }

		    if( (float)$total_combined_pan>2500000.00 || (float)$total_combined_ckyc>2500000.00){
		    $arr['message'] = 'Combined Sanction Amount Greater Than 25 lakhs';
                     $arr['status'] = 'Rejected';
                     return $arr;
		    }
	    }

	     if(strtolower($data->Business_Type) == 'msme'){
                    if((float)$total_sanction_amt_pan_msme>2500000.00 ||(float)$total_sanction_amt_ckyc_msme>2500000.00){
                     $arr['message'] = 'Sanction Amount Greater Than 25 lakhs';
                     $arr['status'] = 'Rejected';
                     return $arr;
                    }

                    if( (float)$total_combined_pan>2500000.00 || (float)$total_combined_ckyc>2500000.00){
                    $arr['message'] = 'Combined Sanction Amount Greater Than 25 lakhs';
                     $arr['status'] = 'Rejected';
                     return $arr;
                    }
            }

	 if ($data->dob) {
            $age = Carbon::parse($data->dob)->age;

            if ($age < 18) {
                $arr['message'] = 'Age less than 18';
                $arr['status'] = 'Rejected';
                return $arr;
            }
        }
	
	 if($data->Gold_Purity != 18 &&  $data->Gold_Purity != 20 && $data->Gold_Purity != 22 && $data->Gold_Purity != 24){
	 	$arr['message'] = 'Gold Purity should be 18/20/22/24 carat';
                $arr['status'] = 'Rejected';
                return $arr;
	 }

	 if($data->REMAINING_LOAN_TENURE<3){
	 	$arr['message'] = 'Remaining tenure less than 3 month';
                $arr['status'] = 'Rejected';
                return $arr;
	 }

	if($data->REMAINING_LOAN_TENURE > $data->LOAN_TENURE){
                $arr['message'] = 'Remaining tenure greater than Loan tenure';
                $arr['status'] = 'Rejected';
                return $arr;
	}

        if ($data->AGE) {
            $age = $data->AGE;
            if ($age < 18) {
                $arr['message'] = '(Age) less than 18';
                $arr['status'] = 'Rejected';
                return $arr;
            }
        } else {
            $arr['message'] = 'Invalid Age';
            $arr['status'] = 'Rejected';
            return $arr;
        }
        $loan_tenure = $data->LOAN_TENURE;
        if ($data->LOAN_TENURE) {
            if ($loan_tenure < 6) {
                $arr['message'] = 'Loan Tenure less than 6 month';
                $arr['status'] = 'Rejected';
                return $arr;
            } else if ($loan_tenure > 12) {
                $arr['message'] = 'Loan Tenure greater than 12 month';
                $arr['status'] = 'Rejected';
                return $arr;
            }
	}
	$formattedDate = Carbon::createFromFormat('d-m-Y',$data->SANCTION_DATE)->format('Y-m-d');
	$gold_rate = GoldRate::where(DB::raw('Date(created_at)'),'=',Date($formattedDate))->get()[0]['22k_gold_rate'];
        //dd($gold_rate);
        $net_weight = $data->Net_Weight;
        $security_value = ($gold_rate * $net_weight);
        $sanction_amount = $data->loan_amount;
        $ltv = round(($sanction_amount/$security_value)*100,2);
        $data->update(['LTV'=>$ltv]);
        if($ltv > 75){
            $arr['message'] = 'LTV more than 75%';
            $arr['status'] = 'Rejected';
            return $arr;
        }
        //commented for testing
        /* $arr = $this->apiVerify($data);
         if ($arr['status'] == 'Rejected') {
             return $arr;
         }
	*/
        $arr['status'] = 'Approved';
        return $arr;
    }
    private function checkBharatPayBre($data)
    {
        $mca = 10000;

        /**
         * BharatPay BRE
         */
        $arr = array();
        if ($data->dob) {
            $age = Carbon::parse($data->dob)->age;
            if ($age < 21 && $age > 65) {
                $arr['message'] = 'Invalid Age Group';
                $arr['status'] = 'Rejected';
                return $arr;
            }
        } else {
            $arr['message'] = 'Invalid Age';
            $arr['status'] = 'Rejected';
            return $arr;
        }
        if ($data->credit_score) {
            if ($data->credit_score < 700) {
                $arr['message'] = 'Credit Score less than 700';
                $arr['status'] = 'Rejected';
                return $arr;
            }
        } else {
            $arr['message'] = 'Credit Score Invalid';
            $arr['status'] = 'Rejected';
            return $arr;
        }
        $loan_tenure = round($data->loan_tenure / 30, 2);
        if ($loan_tenure) {
            if ($loan_tenure < 3) {
                $arr['message'] = 'Loan Tenure less than 3 month';
                $arr['status'] = 'Rejected';
                return $arr;
            } else if ($loan_tenure > 15) {
                $arr['message'] = 'Loan Tenure greater than 18 month';
                $arr['status'] = 'Rejected';
                return $arr;
            }
        }
        $customer_selection  = $this->customerSelectionBharatPay($data);
        if ($customer_selection['loan_status'] == 'Rejected') {
            $arr['message'] = 'As per customer selection criteria';
            $arr['status'] = 'Rejected';
            return $arr;
        }
        $arr = $this->apiVerify($data);
        if ($arr['status'] == 'Rejected') {
            return $arr;
        }
        $arr['status'] = 'Approved';
        return $arr;
    }
    private function demographics($pincode)
    {
        /**
         *
         */
        /*
        $pincodeCounts = Disbursement::select('business_zipcode', DB::raw('count(*) as total'))
            ->groupBy('business_zipcode')
            ->get();
        $totalDisbursement = Disbursement::count();

        // Calculate the percentage of each pincode
        $pincodePercentages = $pincodeCounts->map(function ($item) use ($totalDisbursement) {
            $item->percentage = ($item->total / $totalDisbursement) * 100;
            return $item;
        });

        dd($pincodePercentages);
        */
        $pincodeCount = Disbursement::where('POSTAL_CODE', $pincode)->where('status', 'Done')->count();

        // Step 2: Calculate the total number of customers
        $totalCustomers = Disbursement::count();

        // Step 3: Calculate the percentage
        if ($totalCustomers > 0) {
            $percentage = ($pincodeCount / $totalCustomers) * 100;
        } else {
            $percentage = 0; // Handle case where there are no customers
        }
        return $percentage;
    }
    public function customerSelectionBharatPay($data = null)
    {
        /***
         * Customer Selection algorithm BharatPay
         */
        $tpv = $data->tpv;
        $risk_segment = $data->Risk_Segment; //REGULAR_ETC/REPEAT/REGULAR_NTC
        $risk_group = $data->Risk_Group;
        $pin_code_colour = $data->Pincode_Color;
        $request_tenure = round($data->loan_tenure / 30, 2);
        $credit_score = $data->credit_score;

        if ($credit_score < 300) {
            $request_tenure_tag = $request_tenure;
            $credit_score_tag = 300;
        } else {
            $credit_score_tag = 700;
            if ($risk_segment == 'REPEAT') {
                $request_tenure_tag = '4_15';
            } else {

                $request_tenure_tag = '4_12';
            }
        }
        //dd($request_tenure_tag);
        $bharatpay = Bharatpay::where('risk_segment', $risk_segment)
            ->where('risk_group', $risk_group)
            ->where('pin_code_colour', $pin_code_colour)
            ->where('requested_tenure', $request_tenure_tag)
            ->where('credit_score', $credit_score_tag)
            ->get()[0];

        /**
         * TPV Based Eligibility
         */
        $calculated_amount = $tpv * $bharatpay->tpv_multiplier * $request_tenure;

        $cal_loan_amount = round(min($calculated_amount, $bharatpay->max_limit), -3);

        if ($cal_loan_amount > 10000) {
            $loan_status = 'Approved';
        } else {
            $loan_status = 'Rejected';
        }
        $arr = array(
            'tpv_multiplier' => $bharatpay->tpv_multiplier,
            'max_limit' => $bharatpay->max_limit,
            'calculated_amount' => $calculated_amount,
            'cal_loan_amount' => $cal_loan_amount,
            'loan_status' => $loan_status
        );
        return $arr;
    }

    public function customerSelection($data = null)
    {
        /**
         * Customer selection algorithm PhonePay
         */
        //Testing variable
        $tpv = $data->tpv;
        $score_band = $data->ANCHOR_SCORE_BAND;
        $vintage_month = $data->ANCHOR_VINTAGE_MONTH;

        $loan_amount = $data->loan_amount;
        $tenure = round($data->loan_tenure / 30, 2);


        $credit_score = $data->credit_score;

        /**
         * CIBIL DUMMY DATA
         */
        $history_month_cnt_non_cc_cd_gl = 80;
        $history_month_cnt = 122;
        $max_dpd_non_cc_last_12mo = 0;


        //1. Defination of Score bands
        $scoreband = Scoreband::where('score_band', $score_band)->get()[0];
        $active_days = '>=' . $scoreband->active_days_per_month;
        $avg_monthly_tpv = '>=' . $scoreband->avg_monthly_tpv;

        //2. Defination of final Segmentation
        $tag =  $this->getTag($tpv);
        $segment = Segments::where('score', $score_band)->get()[0];
        $segment_tag = $segment[$tag];

        //3. Line Assignment Multipliers
        $tpvIndex = Tpvindex::where('score', $score_band)->get()[0];
        $tpvIndex_tag = $tpvIndex[$tag];

        //4. Max Tenure Grid
        //Max Tranche Tenure
        $tenure_grid = Tenure::where('grid', $segment_tag)->get()[0];
        $vintage_month_tag = $this->pp_engagment_age($vintage_month);
        $max_tranche_tenure = $tenure_grid[$vintage_month_tag];
        // Max Tenure
        $max_tenure = MIN($max_tranche_tenure, $tenure);

        //5. Max Loan Cap Grid
        if ($credit_score >= 650) {
            $cibilscorebucket = Cibilscorebucket::where('segment', $segment_tag)->get()[0];
            $grid_tag = $this->gridCap($credit_score);
            $max_loan_cap_grid = $cibilscorebucket[$grid_tag];
        } else {
            $cibilscorebucket = Ntc::where('segment', $segment_tag)->get()[0];
            $max_loan_cap_grid = $cibilscorebucket->guardrail;
        }


        //6. Thick-Thin Multiplier
        if ($credit_score >= 760 && $history_month_cnt_non_cc_cd_gl > 24) {
            $thin_multiplier = 1.4;
        } elseif ($credit_score > 760 && $history_month_cnt > 12) {
            $thin_multiplier = 1.2;
        } elseif ($credit_score >= 650) {
            $thin_multiplier = 1;
        } elseif ($credit_score < 300) {
            $thin_multiplier = 0.8;
        } else {
            $thin_multiplier = 0;
        }


        /**
         * calculated_amount_3m
         */
        //echo $max_dpd_non_cc_last_12mo;
        $calculated_amount_3m = $this->calculate_amount(3, $tpv, $max_tenure, $tpvIndex_tag, $thin_multiplier, $max_loan_cap_grid, $loan_amount, $max_dpd_non_cc_last_12mo, $credit_score);
        /**
         * calculated_amount_6m
         */
        $calculated_amount_6m = $this->calculate_amount(6, $tpv, $max_tenure, $tpvIndex_tag, $thin_multiplier, $max_loan_cap_grid, $loan_amount, $max_dpd_non_cc_last_12mo, $credit_score);
        /**
         * calculated_amount_9m
         */
        $calculated_amount_9m = $this->calculate_amount(9, $tpv, $max_tenure, $tpvIndex_tag, $thin_multiplier, $max_loan_cap_grid, $loan_amount, $max_dpd_non_cc_last_12mo, $credit_score);
        /**
         * calculated_amount_12m
         */
        $calculated_amount_12m = $this->calculate_amount(12, $tpv, $max_tenure, $tpvIndex_tag, $thin_multiplier, $max_loan_cap_grid, $loan_amount, $max_dpd_non_cc_last_12mo, $credit_score);
        /**
         * calculated_amount_15m
         */
        $calculated_amount_15m = $this->calculate_amount(15, $tpv, $max_tenure, $tpvIndex_tag, $thin_multiplier, $max_loan_cap_grid, $loan_amount, $max_dpd_non_cc_last_12mo, $credit_score);
        /**
         * calculated_amount_18m
         */
        $calculated_amount_18m = $this->calculate_amount(18, $tpv, $max_tenure, $tpvIndex_tag, $thin_multiplier, $max_loan_cap_grid, $loan_amount, $max_dpd_non_cc_last_12mo, $credit_score);

        $calculated_tenure = min($tenure, $max_tranche_tenure);

        //NTC
        $ntcs = Ntc::where('segment', $segment_tag)->get()[0];
        $ntc = $ntcs->guardrail;
        //
        //7. EDI to TPV cap
        //Loan Amount Calculation
        //$max_loan_amount = $tpv * $tpvIndex_tag * $max_tranche_tenure;

        /**
         * Offer Calculation
         */
        if ($credit_score < 300 && ($tpv >= 10000 && $tpv <= 20000)) {
            $minAmount = 10000;
            $maxAmount = $calculated_amount_3m;
            $tenure = 3;
        } elseif ($max_tenure == 3) {
            $minAmount = 10000;
            $maxAmount = $calculated_amount_3m;
            $tenure = 3;
        }

        $arr = array(
            'tpv' => $tpv,
            'score_band' => $score_band,
            'vintage_month' => $vintage_month,
            'credit_score' => $credit_score,
            'requested_tenure' => $tenure,
            'Segment' => $segment_tag,
            'Score Multiplier' => $tpvIndex_tag,
            'Max Tranche Tenure' => $max_tranche_tenure,
            'Max Tenure' => $max_tenure,
            'max_loan_cap_grid' => $max_loan_cap_grid,
            'thin_multiplier' => $thin_multiplier,
            'calculated_amount_3m' => $calculated_amount_3m,
            'calculated_amount_6m' => $calculated_amount_6m,
            'calculated_amount_9m' => $calculated_amount_9m,
            'calculated_amount_12m' => $calculated_amount_12m,
            'calculated_amount_15m' => $calculated_amount_15m,
            'calculated_amount_18m' => $calculated_amount_18m,
            'calculated_tenure' => $calculated_tenure
        );
        $data->update([
            'customer_selection' => json_encode($arr)
        ]);
        return $arr;
    }

    private function calculate_amount($month, $tpv, $max_tenure, $tpvIndex_tag, $thin_multiplier, $max_loan_cap_grid, $loan_amount, $max_dpd_non_cc_last_12mo, $credit_score)
    {
        /**
         * calculated_amount_Xm
         */
        $cal_amount = min($tpv * min($max_tenure, $month) * $tpvIndex_tag * $thin_multiplier, $max_loan_cap_grid);
        if ($max_loan_cap_grid < 300000) {
            $calculated_amount = min($loan_amount, $cal_amount);
        } elseif ($max_loan_cap_grid > 300000 && $max_dpd_non_cc_last_12mo <= 7 && $credit_score >= 750) {
            $calculated_amount = min($loan_amount, $cal_amount);
        } elseif ($max_loan_cap_grid > 300000 && $max_dpd_non_cc_last_12mo >= 7 && $credit_score <= 750) {
            $calculated_amount = min($cal_amount, 300000);
        } else {
            $calculated_amount = min($cal_amount, 300000);
        }
        // Round down to the nearest 1000
        $roundedNumber = floor($calculated_amount / 1000) * 1000;

        //dd($roundedNumber);
        $calculated_amount = round($roundedNumber, 2);

        return min($calculated_amount, $loan_amount);
    }
    private function pp_engagment_age($pp_engagment_age)
    {
        if ($pp_engagment_age >= 3 && $pp_engagment_age < 12) {
            $tag = 'age_3_12';
        } else if ($pp_engagment_age >= 12 && $pp_engagment_age < 18) {
            $tag = 'age_12_18';
        } elseif ($pp_engagment_age >= 18 && $pp_engagment_age < 24) {
            $tag = 'age_18_24';
        } else if ($pp_engagment_age >= 24 && $pp_engagment_age < 36) {
            $tag = 'age_24_36';
        } else if ($pp_engagment_age >= 36) {
            $tag = 'age_36';
        }
        return $tag;
    }
    private function gridCap($cibil_score)
    {
        if ($cibil_score >= 650 && $cibil_score <= 720) {
            $tag = 'cibil_650_720';
        } else if ($cibil_score >= 721 && $cibil_score <= 760) {
            $tag = 'cibil_721_760';
        } elseif ($cibil_score > 760) {
            $tag = 'cibil_760';
        }
        return $tag;
    }
    private function getTag($loan_amount)
    {
        if ($loan_amount > 70000) {
            $tag = 'CS_71';
        } else if ($loan_amount > 60000 && $loan_amount <= 70000) {
            $tag = 'CS_60_70';
        } else if ($loan_amount > 50000 && $loan_amount <= 60000) {
            $tag = 'CS_50_60';
        } else if ($loan_amount > 35000 && $loan_amount <= 50000) {
            $tag = 'CS_35_50';
        } else if ($loan_amount > 25000 && $loan_amount <= 35000) {
            $tag = 'CS_25_35';
        } else if ($loan_amount > 20000 && $loan_amount <= 25000) {
            $tag = 'CS_20_25';
        } else if ($loan_amount > 15000 && $loan_amount <= 20000) {
            $tag = 'CS_15_20';
        } else if ($loan_amount > 10000 && $loan_amount <= 15000) {
            $tag = 'CS_10_15';
        }
        return $tag;
    }
    public function ckyctest()
    {
        /**
         *
         */
        $data = Disbursement::where('lapp_id', 'APP0870169450604062')->get()[0];
        $this->ckycService->ckycverify('30029224870882', '20-05-1997', $data);
    }


    public function PanEnquiryTest()
    {
        /**
         *
         */
        $data = Disbursement::where('lapp_id', 'APP6060266946002484')->get()[0];
        $this->cbsApiService->PanEnquiry('xxxxxx', $data);
    }


    public function PanVerifyTest()
    {
        /**
         *
         */
        $data = Disbursement::where('lapp_id', 'APP6060266946002484')->get()[0];
        //dd($data)
        $pan_details = $this->KycApiService->PanVerification('xxxxxxxx', $data);
        //$pan_details = $this->KycApiService->udyamVerification('UDYAM-BR-24-000978', $data);
        //dd($pan_details);
        if ($pan_details === false) {
            $arr['message'] = 'Udyam Verification Failed';
            $arr['status'] = 'Rejected';
        } else {
            /**
             *
             */
            return $pan_details;
        }
    }

    public function calc_ltv(){
        $gold_rate = 6510;
        $net_weight = 54;
        $security_value = ($gold_rate * $net_weight);
        $sanction_amount = 257500;
        $ltv = ($sanction_amount/$security_value)*100;
        dd($ltv);
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
