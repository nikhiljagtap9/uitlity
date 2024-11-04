<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CkycService;
use App\Services\KycService;
use Carbon\Carbon;
use App\Models\Disbursement;

class CkycVerify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Ckyc:Verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
	protected $KycApiService;
	protected $ckycService;
    public function __construct(CkycService $ckycService, KycService $KycApiService)
    {
        parent::__construct();
	$this->ckycService = $ckycService;
        $this->KycApiService = $KycApiService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$loan_accounts = Disbursement::where('batch_id', 'BATCH616516932672579')->where('status','API Failed')->get();
	//	$loan_accounts = Disbursement::where('id',23393)->get();
	//	dd($loan_accounts);
		foreach($loan_accounts as $data){
		//dd($data);
			if ($data->CUSTOMER_NAME) {
			
			if ($data->ckyc) {
		$response = $this->ckycService->ckycverify($data->ckyc, date('d-m-Y',strtotime($data->dob)),$data);
		//dd($response);
		if ($response === false) {
			$data->update([
				'status'=>'API Failed',
				'message'=>'API FAILED',
			]);
		}else{
			$arr = json_decode($response,TRUE);
			if(isset($arr['CKYC_INQ']['ERROR'])){
				$data->update([
                                'status'=>'Rejected',
                                'message'=>$arr['CKYC_INQ']['ERROR'],
                        ]);
			}
			else{
			//if(isset($arr["ERROR"])
			$personal_detail = isset($arr['PID']['PID_DATA']['PERSONAL_DETAILS'])?$arr['PID']['PID_DATA']['PERSONAL_DETAILS']:null;
			$full_name = isset($personal_detail['FULLNAME'])?$personal_detail['FULLNAME']:null;
			$pan_no = isset($personal_detail['PAN'])?$personal_detail['PAN']:null;
			$image_details = isset($arr['PID']['PID_DATA']['IMAGE_DETAILS'])?$arr['PID']['PID_DATA']['IMAGE_DETAILS']:null;
			
			if($pan_no){
				if($data->PAN!=$pan_no){
					$data->update([
						'status'=>'Rejected',
						'message'=>'Invalid CKYC Details, PAN Not Matching',
					]);
				}else{
					$data->update([
						'status'=>'Success',
						'message'=>'PAN Matched',
					]);
				}
			}
			if($full_name){
				$percentage = 0;
				similar_text(strtolower($data->TITLE.' '.$data->CUSTOMER_NAME), strtolower($full_name), $percentage);
				$roundedPercentage = round($percentage, 2); // Rounds to 2 decimal places
				//dd($roundedPercentage);
				if ($roundedPercentage < 75) {
					$data->update([
						'status'=>'Rejected',
						'message'=>'CKYC Verification Failed, Name Match ' . $roundedPercentage . '%',
					]);
				}else{
					$data->update([
						'status'=>'Success',
						'message'=>'CKYC Verification Sucessful, Name Match ' . $roundedPercentage . '%',
					]);
				}
			}
			}
		}
	}


			}else{
				$data->update([
					'status'=>'Failed',
					'message'=>'Customer Name Not Available'
				]);
			}			
		}

        return 0;
    }
}

