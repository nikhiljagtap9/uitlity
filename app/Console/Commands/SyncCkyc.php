<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CkycService;
use App\Services\KycService;
use Carbon\Carbon;
use App\Models\LoanAccount;

class SyncUdyam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Sync:Udyam';

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
		$loan_accounts = LoanAccount::where('batch_id', 'BATCH075337564468675')->where('business_name','!=',' ')->get();
		//dd($loan_accounts);
		foreach($loan_accounts as $data){
			//dd($data);
			if ($data->business_name) {
			if ($data->udyog_uaadhaar_number) {
			$udyog_details = $this->KycApiService->udyamVerification($data->udyog_uaadhaar_number, $data);
			//dd($udyog_details);
				if ($data->udyog_uaadhaar_number != $udyog_details['udyamRegistrationNumber']) {
					$data->update([
						'udyog_uaadhaar_status'=>'Failed',
						'udyog_uaadhaar_comment'=>'Invalid Udyog Aadhar Number'
					]);
				} else {
					$percentage = 0;
					similar_text(strtolower($data->business_name), strtolower($udyog_details['nameOfEnterprise']), $percentage);
					$udyaogPer = round($percentage, 2); // Rounds to 2 decimal places
					if ($udyaogPer < 75) {
						$data->update([
							'udyog_uaadhaar_status'=>'Failed',
							'udyog_uaadhaar_comment'=>'Udyam Aadhaar Verification Failed, Name Match Per. is ' . $udyaogPer . '%'
						]);
					}else{
							$data->update([
									'udyog_uaadhaar_status'=>'Success',
									'udyog_uaadhaar_comment'=>'Udyam Aadhaar Verification Completed, Name Match Per. is ' . $udyaogPer . '%'
							]);
						}
				}
			}
		}else{
			$data->update([
				'udyog_uaadhaar_status'=>'Failed',
				'udyog_uaadhaar_comment'=>'Business Name Not Available'
			]);
	}		}
        return 0;
    }
}

