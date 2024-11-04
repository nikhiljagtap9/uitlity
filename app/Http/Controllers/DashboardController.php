<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\MuthootLoanAccount;
use App\Models\CapriLoanAccount;
use App\Models\MuthootGoldAccount;
use App\Models\LendingKartLoanAccount;

class DashboardController extends Controller
{

        public function getLoanData()
        {

                //$user=Auth::user();
                //dd($user);

            // Fetch data from the `muthoot_msme` database
		$muthootMSMEDataActive = DB::connection('muthoot_msme_mysql')
			->table('loan_accounts')
			->where('loan_status', 'active')
            ->selectRaw('COUNT(id) as total')
            ->selectRaw('SUM(bank_sanction_amount) as total_bank_sanction')
            ->selectRaw('SUM(nbfc_sanction_amount) as total_nbfc_sanction')
            ->get()[0];

		$muthootMSMEDataClosed = DB::connection('muthoot_msme_mysql')
			->table('loan_accounts')
			->where('loan_status', 'closed')
            ->selectRaw('COUNT(id) as total')
            ->selectRaw('SUM(bank_sanction_amount) as total_bank_sanction')
            ->selectRaw('SUM(nbfc_sanction_amount) as total_nbfc_sanction')
            ->get()[0];

        // Fetch data from the `capri` database dynamically
        $capriDataActive = CapriLoanAccount::getDataFromCapri('active');
        $capriDataClosed = CapriLoanAccount::getDataFromCapri('closed');

        $muthootGoldActive = MuthootGoldAccount::getDataFromMuthootGold('active');
        $muthootGoldClosed = MuthootGoldAccount::getDataFromMuthootGold('closed');

		 // Fetch custom loan data using partner_id
          //$partnerId = 'mas';
        $loanData = LendingKartLoanAccount::getLendingKartLoanData('lendingkart');
       //dd($loanData[0]->loan_status);

        $masLoanData = LendingKartLoanAccount::getLendingKartLoanData('mas');
        //dd($masloanData);

        $loantapLoanData = LendingKartLoanAccount::getLendingKartLoanData('loantap');
        //dd($loantapLoanData);


        // Prepare data to pass to the view
        $count = [
            'muthoot_active' => [
                'muthootMSME_total_active_acc' => $muthootMSMEDataActive->total,
                'muthootMSME_bank_sanction_amount_active' => $muthootMSMEDataActive->total_bank_sanction,
                'muthootMSME_nbfc_sanction_amount_active' => $muthootMSMEDataActive->total_nbfc_sanction,
            ],
            'muthoot_closed' => [
                'muthootMSME_total_closed_acc' => $muthootMSMEDataClosed->total,
                'muthootMSME_bank_sanction_amount_closed' => $muthootMSMEDataClosed->total_bank_sanction,
                'muthootMSME_nbfc_sanction_amount_closed' => $muthootMSMEDataClosed->total_nbfc_sanction,
            ],
            'capri_active' => [
                'capri_total_active_acc' => $capriDataActive->total,
                'capri_bank_sanction_amount_active' => $capriDataActive->total_bank_sanction,
                'capri_nbfc_sanction_amount_active' => $capriDataActive->total_nbfc_sanction,
            ],
            'capri_closed' => [
                'capri_total_closed_acc' => $capriDataClosed->total,
                'capri_bank_sanction_amount_closed' => $capriDataClosed->total_bank_sanction,
                'capri_nbfc_sanction_amount_closed' => $capriDataClosed->total_nbfc_sanction,
            ],

			 'muthoot_gold_active' => [
                 'muthoot_gold_total_active_acc' => $muthootGoldActive->total,
                 'muthoot_gold_sanction_amount_active' => $muthootGoldActive->total_bank_sanction,
                 'muthoot_gold_nbfc_sanction_amount_active' => $muthootGoldActive->total_nbfc_sanction,
             ],
             'muthoot_gold_closed' => [
                 'muthoot_gold_total_closed_acc' => $muthootGoldClosed->total,
                 'muthoot_gold_bank_sanction_closed' => $muthootGoldClosed->total_bank_sanction,
                 'muthoot_gold_nbfc_sanction_closed' => $muthootGoldClosed->total_nbfc_sanction,
             ],

             'loanData'=> $loanData,
             'masLoanData'=>$masLoanData,
             'loantapLoanData'=>$loantapLoanData
        ];

        // Return the data to the view
        return view('loan_data', compact('count'));
        }
}



          // Fetch data from the `muthoot_msme` database
  
        // Fetch data from the `capri` database dynamically
        //$muthootGoldActive = MuthootGoldAccount::getDataFromMuthootGold('active');
        //$muthootGoldClosed = MuthootGoldAccount::getDataFromMuthootGold('closed');



            // 'muthoot_gold_active' => [
            //     'muthoot_gold_total_active_acc' => $muthootGoldActive->total,
            //     'muthoot_gold_sanction_amount_active' => $muthootGoldActive->total_bank_sanction,
            //     'muthoot_gold_nbfc_sanction_amount_active' => $muthootGoldActive->total_nbfc_sanction,
            // ],
            // 'muthoot_gold_closed' => [
            //     'capri_total_closed_acc' => $muthootGoldClosed->total,
            //     'capri_bank_sanction_amount_closed' => $muthootGoldClosed->total_bank_sanction,
            //     'capri_nbfc_sanction_amount_closed' => $muthootGoldClosed->total_nbfc_sanction,
            // ],


   


            // Fetch data from the `muthoot_msme` database
           // $muthootData = MuthootLoanAccount::selectRaw('count(id) as total, loan_status, SUM(bank_sanction_amount) as bank_sanction_amount, SUM(nbfc_sanction_amount) as nbfc_sanction_amount')
           //     ->groupBy('loan_status')
           //     ->get();

            // Fetch data from the `capri` database dynamically
          //  $capriData = CapriLoanAccount::getDataFromCapri();

            // Return the data to the view
           
