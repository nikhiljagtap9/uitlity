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
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;    //for csv
use Symfony\Component\HttpFoundation\StreamedResponse; //for csv


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
                if ($data['pan']) {
                    $pan = $data['pan'];
                    $count = Disbursement::where('pan', $pan)->where('batch_id', $batch->uuid)->count();
                    if ($count > 0) {
                        $datatostore['status'] = 'Duplicate';
                    } else {
                        $count = Disbursement::where('pan', $pan)->where('status', '!=', 'Rejected')->count();
                        if ($count > 0) {
                            $datatostore['status'] = 'Duplicate';
                        }
                    }
                }
                $datatostore['pf_number'] = Auth::user()->name;
                $datatostore['partner_id'] = $request->partner_id;
                $datatostore['product_id'] = $request->product_id;
                $datatostore['batch_id'] = $batch->uuid;
                $datatostore['pan'] = $data['pan'];
                $datatostore['full_name'] = $data['full_name'];
                $datatostore['epic_number'] = $data['epic_number'];
                $datatostore['driving_lic_number'] = $data['driving_lic_number'];
                $datatostore['date_of_birth'] = $data['date_of_birth'];
                $datatostore['ckyc_number'] = str_replace('ckyc', '', $data['ckyc_number']);
                $datatostore['aadhar_number'] = str_replace('aadhar', '', $data['aadhar_number']);
                $datatostore['udyam_aadhar'] = str_replace('udyam', '', $data['udyam_aadhar']);
                //dd($datatostore);

                DB::enableQueryLog();
                Disbursement::create($datatostore);

                $queries = DB::getQueryLog();
            }
        }
        // dd($total_loan_amount,$total_sanction_amount,$nbfc_sanction_amount,$bank_sanction_amount);
        /*$batch->update([
            'total_count' => '30'

        ]); */
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
        $start = Carbon::parse($disbursement->nbfc_loan_date);
        $closureDate = $start->addMonths($disbursement->loan_tenure);
        $closureDate = $closureDate->subDay();

        return view('disbursement.show', ['loan_account' => $disbursement, 'closureDate' => $closureDate]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Disbursement  $disbursement
     * @return \Illuminate\Http\Response
     */
    public function edit(Disbursement $disbursement)
    {
        return view('disbursement.edit', ['disbursement' => $disbursement]);
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

        //$request->validate([
        //    'file' => 'required|file|mimes:jpg,png,pdf,jpeg|max:2048', // 2MB max
        //]);

        $utr_bom_pos_update = $request->utr_bom_pos_update;
        if ($utr_bom_pos_update) {
            $disbursement->update([
                'utr_bom_pos_update' => $utr_bom_pos_update
            ]);
        }
        //$fileName1 = $fileName2 = $fileName3 = $fileName4 = NULL;
        //dd($request->utr_bom_pos_update);
        $folderPath = 'uploads/' . $disbursement->lapp_id;
        Storage::disk('public')->makeDirectory($folderPath);

        $fileName1 = $fileName2 = $fileName3 = $fileName4 = NULL;

        if ($request->hasFile('upload_1')) {
            $file1 = $request->file('upload_1');
            $fileName1 = $file1->getClientOriginalName();
            $filePath1 = $file1->store('uploads/' . $disbursement->lapp_id . '/' . $fileName1, 'public');
            $disbursement->update([
                'file_1' => $fileName1,
            ]);
        }

        if ($request->hasFile('upload_2')) {
            $file2 = $request->file('upload_2');
            $fileName2 = $file2->getClientOriginalName();
            $filePath2 = $file2->store('uploads/' . $disbursement->lapp_id . '/' . $fileName2, 'public');
            $disbursement->update([
                'file_2' => $fileName2,
            ]);
        }

        if ($request->hasFile('upload_3')) {
            $file3 = $request->file('upload_3');
            $fileName3 = $file3->getClientOriginalName();
            $filePath3 = $file3->store('uploads/' . $disbursement->lapp_id . '/' . $fileName3, 'public');
            $disbursement->update([
                'file_3' => $fileName3,
            ]);
        }

        if ($request->hasFile('upload_4')) {
            $file4 = $request->file('upload_4');
            $fileName4 = $file4->getClientOriginalName();
            $filePath4 = $file4->store('uploads/' . $disbursement->lapp_id . '/' . $fileName4, 'public');
            $disbursement->update([
                'file_4' => $fileName4,
            ]);
        }

        return redirect()->back()->with('success', 'Application Updated');
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

       // $request->batch_id = 'BATCH213539923240252';

        /**
         *
         */
        // Get the current offset from the request, default to 0 if not set
        $Disbursebatch = Disbursebatch::where('uuid', $request->batch_id)->get()[0];
       // $Disbursebatch = Disbursebatch::where('uuid', 'BATCH213539923240252')->get()[0];
        
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
                    'message' => 'BRE Matched',
                    'status' => 'Matched'
                ]);
            }
        }
        // Check if there are more items to process
        $moreData = Disbursement::where('status', 'Pending')->where('batch_id', $request->batch_id)->exists();
        if (!$moreData) {
            $Disbursebatch->update(['status' => 'Matched']);
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
                            'status' => 'Matched'
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
        if ($data->ckyc) {
            $response = $this->ckycService->ckycverify($data->ckyc, $data->dob, $data);
            if ($response == false) {
                $arr['message'] = 'CKYC API Failed';
                $arr['status'] = 'API Failed';
                return $arr;
            }
            $arr = json_decode($response, TRUE);
            if (isset($arr['CKYC_INQ']['ERROR'])) {

                $arr['message'] = $arr['CKYC_INQ']['ERROR'];
                $arr['status'] = 'Rejected';
                return $arr;
            }

            $personal_detail = isset($arr['PID']['PID_DATA']['PERSONAL_DETAILS']) ? $arr['PID']['PID_DATA']['PERSONAL_DETAILS'] : NULL;
            $full_name = isset($personal_detail['FULLNAME']) ? $personal_detail['FULLNAME'] : NULL;
            $pan_no = isset($personal_detail['PAN']) ? $personal_detail['PAN'] : NULL;
            $image_details = isset($arr['PID']['PID_DATA']['IMAGE_DETAILS']) ? $arr['PID']['PID_DATA']['IMAGE_DETAILS'] : NULL;
            if ($pan_no) {
                if ($data->pan != $pan_no) {
                    $arr['message'] = 'Invalid CKYC Details, PAN Not Matching';
                    $arr['status'] = 'Rejected';
                    return $arr;
                }
            }
            if ($full_name) {
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
        $arr['status'] = 'Approved';
        return $arr;
    }


    private function apiVerify($data)
    {
    $arr = [
        'status' => 'Rejected', // Default initial status
        'message' => []        // Array to collect all messages
    ];

    $voterRejected = false; // Track Voter Card rejection
    $panRejected = false;   // Track PAN rejection
    $CKYCRejected = false;   // Track CKYC rejection
    $drivingLicRejected = false; // Track driving Lic rejection
    $aadharRejected = false; // Track aadhar rejection
    $udyamaadharRejected = false; // Track udyam aadhar rejection
    
    // Voter Card Verification
    if ($data->epic_number) {
        $voting_details = $this->KycApiService->voterCardVerification($data->epic_number, $data);
        if ($voting_details === false) {
            $arr['message'][] = 'Voting Card Verification Failed';
            $voterRejected = true;
        } else {
            $roundedPercentage = '';
            $roundedPercentage = $this->nameMatchPercent(strtolower($data->full_name), strtolower($voting_details['name']));
            $data->update(['voting_card_match_score' => $roundedPercentage]);
            if ($roundedPercentage < 75) {
                $arr['message'][] = 'Voting Card Verification Failed, Name Match Per. is ' . $roundedPercentage . '%';
                $voterRejected = true;
            }
            else {
                $arr['status'] = 'Matched';
            }
        }
    } else {
        $arr['message'][] = 'Voting Card Not Available';
        $voterRejected = true;
    }

    // PAN Verification (only if Voter Card verification fails)
    if ($voterRejected) {
        if ($data->pan) {
            $pan_details = $this->KycApiService->PanVerification($data->pan, $data);
            if ($pan_details === false) {
                $arr['message'][] = 'PAN Verification Failed';
                $panRejected = true;
            } else {
                $roundedPercentage = '';
                $roundedPercentage = $this->nameMatchPercent(strtolower($data->full_name), strtolower($pan_details['fullName']));
                $data->update(['pan_match_score' => $roundedPercentage]);
                if ($roundedPercentage < 75) {
                    $arr['message'][] = 'PAN Verification Failed, Name Match Per. is ' . $roundedPercentage . '%';
                    $panRejected = true;
                } else {
                    $arr['status'] = 'Matched';
                }
            }
        } else {
            $arr['message'][] = 'PAN Number Not Available';
            $panRejected = true;
        }
    }

    // CKYC Verification (only if both Voter Card and PAN verification fail)
   /* if ($voterRejected && $panRejected) {
        if ($data->ckyc_number) {
            $response = $this->ckycService->ckycverify($data->ckyc_number, $data->date_of_birth, $data);
            if ($response == false) {
                $arr['message'][] = 'CKYC API Failed';
                $arr['status'] = 'API Failed';
                $CKYCRejected = true;
            }
            $arr = json_decode($response, TRUE);
            if (isset($arr['CKYC_INQ']['ERROR'])) {
                $arr['message'][] = $arr['CKYC_INQ']['ERROR'];
                $arr['status'] = 'Rejected';
                $CKYCRejected = true;
            }

            $personal_detail = isset($arr['PID']['PID_DATA']['PERSONAL_DETAILS']) ? $arr['PID']['PID_DATA']['PERSONAL_DETAILS'] : NULL;
            $full_name = isset($personal_detail['FULLNAME']) ? $personal_detail['FULLNAME'] : NULL;
            $pan_no = isset($personal_detail['PAN']) ? $personal_detail['PAN'] : NULL;
            $image_details = isset($arr['PID']['PID_DATA']['IMAGE_DETAILS']) ? $arr['PID']['PID_DATA']['IMAGE_DETAILS'] : NULL;
            if ($pan_no) {
                if ($data->pan != $pan_no) {
                    $arr['message'][] = 'Invalid CKYC Details, PAN Not Matching';
                    $arr['status'] = 'Rejected';
                    $CKYCRejected = true;
                }
            }
            if ($full_name) {
                $percentage = 0;
                $roundedPercentage = $this->nameMatchPercent(strtolower($data->CUSTOMER_NAME), strtolower($full_name));
                $data->update(['ckyc_match_score' => $roundedPercentage]);
                // $roundedPercentage = round($percentage, 2); // Rounds to 2 decimal places
                //dd($roundedPercentage);
                if ($roundedPercentage < 75) {
                    $arr['message'][] = 'Pan Verification Failed, Name Match Per. is ' . $roundedPercentage . '%';
                    $arr['status'] = 'Rejected';
                    $CKYCRejected = true;
                }else{
                    $arr['status'] = 'Matched';   
                }
            }
        } else {
            $arr['message'][] = 'CKYC Number Not Available';
            $arr['status'] = 'Rejected';
            $CKYCRejected = true;
        }
    } */

     // Driving Lic (only if Voter Card, PAN , CKYC verification fail)
    if ($voterRejected && $panRejected && $CKYCRejected) {
        if ($data->driving_lic_number) {
            $driving_details = $this->KycApiService->drivingLicenceVerification($data->driving_lic_number, $data);
            if ($driving_details === false) {
                $arr['message'][] = 'Driving Licence Verification Failed';
                $drivingLicRejected = true; 
            } else {
                $percentage = 0;
                $roundedPercentage = $this->nameMatchPercent(strtolower($data->full_name), strtolower($driving_details['name']));
                $data->update(['driving_lic_match_score' => $roundedPercentage]);
                if ($roundedPercentage < 75) {
                    $arr['message'][] = 'Driving Licence Verification Failed, Name Match Per. is ' . $roundedPercentage . '%';
                    $drivingLicRejected = true; 
                }else{
                    $arr['status'] = 'Matched';
                }
            }
        } else {
            $arr['message'][] = 'Driving Licence Number Not Available';
            $drivingLicRejected = true; 
        }
    }

    // Aadhaar Verification (only if Voter Card, PAN , CKYC, Driving verification fail)
    if ($voterRejected && $panRejected && $CKYCRejected && $drivingLicRejected) {
        if ($data->aadhar_number) {
            $aadhar_details = $this->KycApiService->aadharVerification($data->aadhar_number, $data);
            if ($aadhar_details === false) {
                $arr['message'][] = 'Aadhaar Verification Failed';
                $aadharRejected = true;
            } else {
                $aadharPer = 100; // Assuming 100% match for Aadhaar
                $data->update(['aadhar_match_score' => $aadharPer]);
                $arr['message'][] = 'Aadhaar Verification Passed';
                $arr['status'] = 'Matched';
            }
        } else {
            $arr['message'][] = 'Aadhaar Number Not Available';
            $aadharRejected = true;
        }
    }

	// Udaym Aadhaar Verification (only if Voter Card, PAN , CKYC, Driving, Aadhar verification fail)
    if ($voterRejected && $panRejected && $CKYCRejected && $drivingLicRejected && $aadharRejected) {
        if ($data->udyam_no) {
                $udyog_details = $this->KycApiService->udyamVerification($data->udyam_no, $data);
                if ($udyog_details === false) {
                    $arr['message'] = 'Udyam Aadhaar Verification Failed';
                } else {
                    $percentage = 0;
                    $udyaogPer = $this->nameMatchPercent(strtolower($data->full_name), strtolower($udyog_details['nameOfEnterprise']));
                    $data->update(['udyam_match_score',$udyaogPer]);
                    if ($udyaogPer < 75) {
                        $arr['message'] = 'Udyam Aadhaar Verification Failed, Name Match Per. is ' . $udyaogPer . '%';
                    }else{
                        $arr['status'] = 'Matched';
                    }
                }
        }else {
            $arr['message'][] = 'Udyam Aadhaar Not Available';
        }
    }
 

    return $arr;
}


    private function apiVerifyOld($data)
    {
       // $arr = array();
        $arr = [
            'status' => 'Approved', // Default status
            'messages' => []        // Array to collect all messages
        ];

        // Voter Card
        if ($data->epic_number) {
            $voting_details = $this->KycApiService->voterCardVerification($data->epic_number, $data);
            if ($voting_details === false) {
                $arr['message'][] = 'Voting Card Verification Failed';
                $arr['status'] = 'Rejected';
            //    return $arr;
            } else {
                $percentage = 0;
       		    $roundedPercentage = $this->nameMatchPercent(strtolower($data->full_name), strtolower($voting_details['name']));
                $data->update(['voting_card_match_score' => $roundedPercentage]);
                if ($roundedPercentage < 75) {
                    $arr['message'][] = 'Voting Card Verification Failed, Name Match Per. is ' . $roundedPercentage . '%';
                    $arr['status'] = 'Rejected';
                //    return $arr;
                }
            }
        } else {
            $arr['message'][] = 'Voting Card Not Available';
            $arr['status'] = 'Rejected';
        //    return $arr;
	    }

        $arr['status'] = 'Approved';
        return $arr;

       

        //pan verify
        if ($data->pan) {
            $pan_details = $this->KycApiService->PanVerification($data->pan, $data);

            if ($pan_details === false) {
                $arr['message'][] = 'Pan Verification Failed';
                $arr['status'] = 'Rejected';
            //    return $arr;
            } else {
                $percentage = 0;
       		    $roundedPercentage = $this->nameMatchPercent(strtolower($data->full_name), strtolower($pan_details['fullName']));
		        //$roundedPercentage = round($percentage, 2); // Rounds to 2 decimal places
              //  dd($roundedPercentage);
                $data->update(['pan_match_score' => $roundedPercentage]);
                if ($roundedPercentage < 75) {
                    $arr['message'][] = 'Pan Verification Failed, Name Match Per. is ' . $roundedPercentage . '%';
                    $arr['status'] = 'Rejected';
                //    return $arr;
                }
            }
        } else {
            $arr['message'][] = 'PAN Number Not Available';
            $arr['status'] = 'Rejected';
        //    return $arr;
	    }

        // ckyc verify
    /*    if ($data->ckyc_number) {
            $response = $this->ckycService->ckycverify($data->ckyc_number, $data->date_of_birth, $data);
            if ($response == false) {
                $arr['message'][] = 'CKYC API Failed';
                $arr['status'] = 'API Failed';
                return $arr;
            }
            $arr = json_decode($response, TRUE);
            if (isset($arr['CKYC_INQ']['ERROR'])) {

                $arr['message'][] = $arr['CKYC_INQ']['ERROR'];
                $arr['status'] = 'Rejected';
                return $arr;
            }

            $personal_detail = isset($arr['PID']['PID_DATA']['PERSONAL_DETAILS']) ? $arr['PID']['PID_DATA']['PERSONAL_DETAILS'] : NULL;
            $full_name = isset($personal_detail['FULLNAME']) ? $personal_detail['FULLNAME'] : NULL;
            $pan_no = isset($personal_detail['PAN']) ? $personal_detail['PAN'] : NULL;
            $image_details = isset($arr['PID']['PID_DATA']['IMAGE_DETAILS']) ? $arr['PID']['PID_DATA']['IMAGE_DETAILS'] : NULL;
            if ($pan_no) {
                if ($data->pan != $pan_no) {
                    $arr['message'][] = 'Invalid CKYC Details, PAN Not Matching';
                    $arr['status'] = 'Rejected';
                    return $arr;
                }
            }
            if ($full_name) {
                $percentage = 0;
                $roundedPercentage = $this->nameMatchPercent(strtolower($data->CUSTOMER_NAME), strtolower($full_name));
                $data->update(['ckyc_match_score' => $roundedPercentage]);
                // $roundedPercentage = round($percentage, 2); // Rounds to 2 decimal places
                //dd($roundedPercentage);
                if ($roundedPercentage < 75) {
                    $arr['message'][] = 'Pan Verification Failed, Name Match Per. is ' . $roundedPercentage . '%';
                    $arr['status'] = 'Rejected';
                }
            }
        } else {
            $arr['message'][] = 'CKYC Number Not Available';
            $arr['status'] = 'Rejected';
            return $arr;
        }
    //    $arr['status'] = 'Approved';
    //    return $arr;
       */ 

        // Driving Lic
        if ($data->driving_lic_number) {
            $driving_details = $this->KycApiService->drivingLicenceVerification($data->driving_lic_number, $data);
            if ($driving_details === false) {
                $arr['message'][] = 'Driving Licence Verification Failed';
                $arr['status'] = 'Rejected';
             //   return $arr;
            } else {
                $percentage = 0;
       		    $roundedPercentage = $this->nameMatchPercent(strtolower($data->full_name), strtolower($driving_details['name']));
                $data->update(['driving_lic_match_score' => $roundedPercentage]);
                if ($roundedPercentage < 75) {
                    $arr['message'][] = 'Driving Licence Verification Failed, Name Match Per. is ' . $roundedPercentage . '%';
                    $arr['status'] = 'Rejected';
                //    return $arr;
                }
            }
        } else {
            $arr['message'][] = 'Driving Licence Number Not Available';
            $arr['status'] = 'Rejected';
          //  return $arr;
	    }

        // Aadhar verify	
        if ($data->aadhar_number) {
            $aadhar_details = $this->KycApiService->aadharVerification($data->aadhar_number, $data);
            if ($aadhar_details === false) {
                $arr['message'][] = 'Aadhaar Verification Failed';
                $arr['status'] = 'Rejected';
            //    return $arr;
            } else {
                $aadharPer = 100;
                $data->update(['aadhar_match_score' => $aadharPer]);
            }
        }else {
            $arr['message'][] = 'Aadhaar Number Not Available';
            $arr['status'] = 'Rejected';
           // return $arr;
        }    

         // Concatenate messages for final output
        if (empty($arr['messages'])) {
            $arr['messages'][] = 'All verifications passed.';
        }

       // $arr['status'] = 'Approved';
        return $arr;
       
    }

    private function checkBre($data)
    {
        
       // $arr = array();
        /**
         * BRE
         */
       /* if (!$data->ckyc_number) {
            $arr['message'] = 'Ckyc number empty';
            $arr['status'] = 'Rejected';
            return $arr;
        }

        if (!$data->pan) {
            $arr['message'] = 'Pan number empty';
            $arr['status'] = 'Rejected';
            return $arr;
        } */

        $formattedDate = Carbon::createFromFormat('d-m-Y', $data->date_of_birth)->format('Y-m-d');
        // check api verify
        $arr = $this->apiVerify($data);
        
        
        if ($arr['status'] == 'Rejected') {
            return $arr;
        }

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

    public function calc_ltv()
    {
        $gold_rate = 6510;
        $net_weight = 54;
        $security_value = ($gold_rate * $net_weight);
        $sanction_amount = 257500;
        $ltv = ($sanction_amount / $security_value) * 100;
        dd($ltv);
    }

    private function nameMatchPercent($name1, $name2)
    {
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

    public function generateCSV(Request $request)
    {
        //dd($request->all()); // Check if batch_id and status are being received
        $status = $request->input('status') ? $request->input('status') : 'ALL';
        $fileName = $status.'_Statement_' . date('d-m-Y') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($request) {
            $file = fopen('php://output', 'w');

            // Add the header of the CSV
            $columns = [
                'App ID',
                'Customer Name',
                'Message',
                'PAN Match(%)',
                'CKYC Match(%)',
                'Aadhar Match(%)',
                'Driving Licence Match(%)',
                'Voter Match(%)',
                'Status'
            ];
            fputcsv($file, $columns);

        $batchId = $request->input('batch_id');
        $status = $request->input('status');
        $loan_accounts = Disbursement::when($batchId, function ($query, $batchId) {
            return $query->where('batch_id', $batchId);
        })
        ->when($status, function ($query, $status) {
            return $query->where('status', $status);
        })
        ->get();
        
        foreach ($loan_accounts as $loan_account) {

            fputcsv($file, [
                $loan_account->lapp_id ?? 'NA',
                $loan_account->full_name ?? 'NA',
                $loan_account->message ?? 'NA',
                $loan_account->pan_match_score ?? 'NA',
                $loan_account->ckyc_match_score ?? 'NA',
                $loan_account->aadhar_match_score ?? 'NA',
                $loan_account->driving_lic_match_score ?? 'NA',
                $loan_account->voting_card_match_score ?? 'NA',
                $loan_account->status
            ]);
        }
        fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
