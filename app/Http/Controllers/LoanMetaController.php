<?php

namespace App\Http\Controllers;

use App\Models\Disbursement;
use App\Models\LoanAccount;
use App\Models\LoanEntry;
use App\Models\LoanMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoanMetaController extends Controller
{
    public function syncLoanMeta()
    {
        $disbursements = Disbursement::where('status', 'Disbursed')->get();
        foreach ($disbursements as $disbursement) {
            /**
             * Create Loan Account and update loan account table
             */
            $bank_roi = config('global.bank_interest');
            $nbfc_roi = config('global.nbfc_interest');
            LoanAccount::create([
                'mfl_ref_no' => $disbursement->mfl_loan_id,
                'ucic' => $disbursement->customer_id,
                'bank_interest' => $bank_roi,
                'nbfc_interest' => $disbursement->interest_rate,
                'sanction_limit' => $disbursement->loan_amount,
                'bank_sanction_amount' => $disbursement->bank_sanction_amount,
                'nbfc_sanction_amount' => $disbursement->nbfc_sanction_amount,
                'total_balance' => $disbursement->sanction_amount,
                'bank_balance' => $disbursement->bank_sanction_amount,
                'loan_tenure' => $disbursement->loan_tenure,
                'bank_loan_date' => date('Y-m-d'),
                'nbfc_loan_date' => date('Y-m-d'),
                'loan_status' => 'active',
                'nbfc_balance' => $disbursement->nbfc_sanction_amount,
                'utr_bom_pos_update' => $disbursement->utr_bom_pos_update,
                'loan_account_number' => $disbursement->loan_account_number,
                'job_type' => '',
                'pan_number' => $disbursement->pan_card,
                'address_proof_number' => '',
                'address_proof_type' => '',
                'identiry_proof_number' => '',
                'identiry_proof_type' => '',
                'postal_code' => '',
                'state_code' => $disbursement->loan_city_state_code,
                'city_code' => $disbursement->loan_city,
                'address1' => $disbursement->business_addr_line1,
                'email' => '',
                'mobile_number' => $disbursement->mobile_number,
                'caste' => '',
                'community' => '',
                'ckyc_no' => $disbursement->ckyc,
                'date_of_birth' => $disbursement->dob,
                'gender' => $disbursement->mfl_loan_id,
                'customer_name' => $disbursement->mfl_loan_id,
                'customer_title' => $disbursement->mfl_loan_id,
                'sol_id' => $disbursement->mfl_loan_id,
                'batch_id' => $disbursement->mfl_loan_id,
                'customer_id' => $disbursement->mfl_loan_id,
                'title' => $disbursement->title,
                'business_name' => $disbursement->business_name,
                'dob' => '',
                'loan_city' => $disbursement->loan_city,
                'business_addr_line1' => $disbursement->business_addr_line1,
                'business_addr_line2' => $disbursement->business_addr_line2,
                'business_lat' => $disbursement->business_lat,
                'business_long' => $disbursement->business_long,
                'business_zipcode' => $disbursement->business_zipcode,
                'business_city' => $disbursement->business_city,
                'pan_card' => $disbursement->pan_card,
                'business_gst_number' => $disbursement->business_gst_number,
                'loan_city_state' => $disbursement->loan_city_state,
                'loan_city_state_code' => $disbursement->loan_city_state_code,
                'loan_amount' => $disbursement->loan_amount,
                'sanction_amount' => $disbursement->sanction_amount,
                'interest_rate' => $disbursement->interest_rate,
                'processing_fees' => $disbursement->processing_fees,
                'udyog_uaadhaar_number' => $disbursement->udyog_uaadhaar_number,
                'ckyc' => $disbursement->ckyc,
                'credit_score' => $disbursement->credit_score,
                'vendor' => $disbursement->vendor,
                'score_band' => $disbursement->score_band,
                'tpv' => $disbursement->tpv,
                'vintage_month' => $disbursement->vintage_month,
                'home_addr_line1' => $disbursement->home_addr_line1,
                'home_addr_line2' => $disbursement->home_addr_line2,
                'home_addr_line3' => $disbursement->home_addr_line3,
                'home_city' => $disbursement->home_city,
                'home_zipcode' => $disbursement->home_zipcode,
                'kyc_docuement_link' => $disbursement->kyc_docuement_link,
                'turnover' => $disbursement->turnover,
                'business_start_date' => $disbursement->business_start_date,
                'status' => 'active'
            ]);
            $disbursement->update(['status' => 'Done']);
        }
    }
    public function loanmeta()
    {
        $loan_metas = LoanMeta::select('object_id')->groupBy('object_id')->limit(100)->get();

        foreach ($loan_metas as $loan_meta) {
            $loan_id = $loan_meta->object_id;
            $loan_account = LoanAccount::where('loan_id', $loan_id)->get();
            if (isset($loan_account[0]->id)) {
                /** */
            } else {
                $loanmeta = LoanMeta::select('meta_key', 'meta_value')
                    ->where('object_id', $loan_meta->object_id)->get()
                    ->keyBy('meta_key') // key every setting by its name
                    ->transform(function ($row) {
                        return $row->meta_value; // return only the value
                    })
                    ->toArray();
                //dd($loanmeta);
                $bank_date = isset($loanmeta['bank_date']) ? Carbon::parse($loanmeta['bank_date'])->toDateString() : Carbon::parse($loanmeta['sanction_date'])->toDateString();
                LoanAccount::create([
                    'loan_id' => $loan_id,
                    'mfl_ref_no' => $loanmeta['mfl_ref_no'],
                    'bank_interest' => isset($loanmeta['bank_roi']) ? $loanmeta['bank_roi'] : 9.545,
                    'nbfc_interest' => isset($loanmeta['nbfc_roi']) ? $loanmeta['nbfc_roi'] : 23,
                    'sanction_limit' => $loanmeta['sanction_limit'],
                    'bank_sanction_amount' => $loanmeta['sanction_limit'] * 0.8,
                    'nbfc_sanction_amount' => $loanmeta['sanction_limit'] * 0.2,
                    'total_balance' => $loanmeta['sanction_limit'],
                    'bank_balance' => $loanmeta['sanction_limit'] * 0.8,
                    'loan_tenure' => $loanmeta['repayment_periodinmonths'],
                    'bank_loan_date' => $bank_date,
                    'nbfc_loan_date' => Carbon::parse($loanmeta['sanction_date'])->toDateString(),
                    'loan_status' => isset($loanmeta['loan_status']) ? $loanmeta['loan_status'] : 'active',
                    'nbfc_balance' => $loanmeta['sanction_limit'] * 0.2,
                    'nbfc_backdate' => 1,
                    'bank_backdate' => 1
                ]);

                LoanEntry::create([
                    'loan_id' => $loan_id,
                    'entry_date' => $bank_date,
                    'entry_month' => 06,
                    'entry_year' => 2024,
                    'bank_date' => $bank_date,
                    'entry_timestamp' => $bank_date,
                    'debit' => $loanmeta['sanction_limit'],
                    'total_debit' => $loanmeta['sanction_limit'],
                    'bank_debit' => $loanmeta['sanction_limit'] * 0.8,
                    'nbfc_debit' => $loanmeta['sanction_limit'] * 0.2,
                    'balance' => $loanmeta['sanction_limit'],
                    'bank_balance' => $loanmeta['sanction_limit'] * 0.8,
                    'nbfc_balance' => $loanmeta['sanction_limit'] * 0.2,
                    'description' => 'Loan Disbursed',
                    'head' => 'pricipal',
                    'jnl_no' => isset($loanmeta['utr_ref']) ? $loanmeta['utr_ref'] : 'UTR123456',
                    'principal_balance' => $loanmeta['sanction_limit'],
                    'principal_bank_balance' => $loanmeta['sanction_limit'] * 0.8,
                    'principal_nbfc_balance' => $loanmeta['sanction_limit'] * 0.2,
                ]);
            }
        }
        echo 'done';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loanmetas = LoanMeta::groupBy('object_id');
        $loanMetas = LoanMeta::all([
            'meta_key',
            'meta_value'
        ])
            ->keyBy('meta_key') // key every setting by its name
            ->transform(function ($setting) {
                return $setting->val; // return only the value
            })
            ->toArray();
        print_r($loanMetas);
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
     * @param  \App\Models\LoanMeta  $loanMeta
     * @return \Illuminate\Http\Response
     */
    public function show(LoanMeta $loanMeta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LoanMeta  $loanMeta
     * @return \Illuminate\Http\Response
     */
    public function edit(LoanMeta $loanMeta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LoanMeta  $loanMeta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoanMeta $loanMeta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LoanMeta  $loanMeta
     * @return \Illuminate\Http\Response
     */
    public function destroy(LoanMeta $loanMeta)
    {
        //
    }
}
