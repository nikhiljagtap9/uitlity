<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Collection;
use App\Models\Interest;
use App\Models\LoanAccount;
use App\Models\LoanEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $interest = 13416.67;
        echo $bank_interest = $this->bank_interest($interest);
        echo '<br>';
        echo $nbfc_interest = $this->nbfc_interest($interest);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('collection.create');
    }
    private function bank_interest($interest)
    {
        //formula to calculate bank interest
        //$bank_int = 0;
        $bank_interest = config('global.bank_interest');
        $nbfc_interest = config('global.nbfc_interest');
        $bank_share = env('BANK_SHARE', 0.8);
        $nbfc_share = env('NBFC_SHARE', 0.2);
        //(11.18*0.8/((11.18*0.8+23*0.2) * 6000)
        $bank_int =  ($bank_interest * $bank_share / ($bank_interest * $bank_share + $nbfc_interest * $nbfc_share) * $interest);
        return round($bank_int, 2);
    }
    private function nbfc_interest($interest)
    {
        //formula to calculate bank interest
        //$bank_int = 0;
        $bank_interest = config('global.bank_interest');
        $nbfc_interest = config('global.nbfc_interest');
        $bank_share = env('BANK_SHARE', 0.8);
        $nbfc_share = env('NBFC_SHARE', 0.2);
        //(23 * 0.2 / (11.18 * 0.8 + 23 * 0.2) * 6000)
        $nbfc_int =  ($nbfc_interest * $nbfc_share / ($bank_interest * $bank_share + $nbfc_interest * $nbfc_share) * $interest);
        return round($nbfc_int, 2);
    }
    public function test()
    {
        $principal = 0;
        $interest = 1275.25;
        echo $total = round($principal + $interest, 2);
        echo '<br>';
        echo $total_interest_due = 1575.25;
        echo '<br>';
        echo $bank_interest_due = 481.16;
        echo '<br>';
        echo $nbfc_interest_due = 315.00;
        echo '<br>';
        echo $total_bank_nbfc_due = round($bank_interest_due + $nbfc_interest_due, 2);
        echo '<br>';

        if ($total_interest_due == $total) {
            echo 'case1';
            $final_principal = 0;
            $final_interest = $total;
            $final_bank_interest = $bank_interest_due;
            $final_nbfc_interest = $nbfc_interest_due;
        } elseif ($total_interest_due < $total) {
            echo 'case2';
            if ($total_bank_nbfc_due == $total) {
                echo '=>case7';
                $final_principal = $total - $total_interest_due;
                $final_interest = $total_interest_due;
                $final_bank_interest = $bank_interest_due;
                $final_nbfc_interest = $nbfc_interest_due;
            }
            if ($total_bank_nbfc_due < $total) {
                echo '=>case8';
                $final_principal = $total - $total_interest_due;
                $final_interest = $total_interest_due;
                $final_bank_interest = $bank_interest_due;
                $final_nbfc_interest = $nbfc_interest_due;
            }
            if ($total_bank_nbfc_due > $total) {
                echo '=>case9';
                $final_principal = $total - $total_interest_due;
                $final_interest = $total_interest_due;
                $final_bank_interest = $bank_interest_due;
                $final_nbfc_interest = $nbfc_interest_due;
            }
        } elseif ($total_interest_due > $total) {
            echo 'case3';
            $final_principal = 0;
            $final_interest = $total;
            if ($total_bank_nbfc_due == $total) {
                echo '=>case4';

                $final_bank_interest = $bank_interest_due;
                $final_nbfc_interest = $nbfc_interest_due;
            }
            if ($total_bank_nbfc_due < $total) {
                echo '=>case5';
                $final_bank_interest = $bank_interest_due;
                $final_nbfc_interest = $nbfc_interest_due;
            }
            if ($total_bank_nbfc_due > $total) {
                echo '=>case6';
                $final_bank_interest = $this->bank_interest($final_interest);
                $final_nbfc_interest = $this->nbfc_interest($final_interest);
            }
        }
        $mfl_sprade = abs(round($final_interest - ($final_bank_interest + $final_nbfc_interest), 2));
        //$mfl_sprade = round($total - ($final_principal + $final_interest), 2);

        $arr = array(
            'final_interest' => $final_interest,
            'final_principal' => $final_principal,
            'final_bank_interest' => $final_bank_interest,
            'final_nbfc_interest' => $final_nbfc_interest,
            'mfl_sprade' => $mfl_sprade
        );
        dd($arr);
    }
    public function calculateInterestnew($principal, $interest, $total_interest_due, $bank_interest_due, $nbfc_interest_due)
    {
        $total = round($principal + $interest, 2);
        $total_bank_nbfc_due = round($bank_interest_due + $nbfc_interest_due, 2);

        if ($total_interest_due == $total) {
            $final_principal = 0;
            $final_interest = $total;
            $final_bank_interest = $bank_interest_due;
            $final_nbfc_interest = $nbfc_interest_due;
        } elseif ($total_interest_due < $total) {
            if ($total_bank_nbfc_due == $total) {
                $final_principal = $total - $total_interest_due;
                $final_interest = $total_interest_due;
                $final_bank_interest = $bank_interest_due;
                $final_nbfc_interest = $nbfc_interest_due;
            }
            if ($total_bank_nbfc_due < $total) {
                $final_principal = $total - $total_interest_due;
                $final_interest = $total_interest_due;
                $final_bank_interest = $bank_interest_due;
                $final_nbfc_interest = $nbfc_interest_due;
            }
            if ($total_bank_nbfc_due > $total) {
                $final_principal = $total - $total_interest_due;
                $final_interest = $total_interest_due;
                $final_bank_interest = $bank_interest_due;
                $final_nbfc_interest = $nbfc_interest_due;
            }
        } elseif ($total_interest_due > $total) {
            $final_principal = 0;
            $final_interest = $total;
            if ($total_bank_nbfc_due == $total) {
                $final_bank_interest = $bank_interest_due;
                $final_nbfc_interest = $nbfc_interest_due;
            }
            if ($total_bank_nbfc_due < $total) {
                $final_bank_interest = $bank_interest_due;
                $final_nbfc_interest = $nbfc_interest_due;
            }
            if ($total_bank_nbfc_due > $total) {
                $final_bank_interest = $this->bank_interest($final_interest);
                $final_nbfc_interest = $this->nbfc_interest($final_interest);
            }
        }
        $mfl_sprade = abs(round($final_interest - ($final_bank_interest + $final_nbfc_interest), 2));
        //$mfl_sprade = round($total - ($final_principal + $final_interest), 2);
        $arr = array(
            'final_interest' => $final_interest,
            'final_principal' => $final_principal,
            'final_bank_interest' => $final_bank_interest,
            'final_nbfc_interest' => $final_nbfc_interest,
            'mfl_sprade' => $mfl_sprade
        );
        return $arr;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storenew(Request $request)
    {
        $bank_share = env('BANK_SHARE', 0.8);
        $nbfc_share = env('NBFC_SHARE', 0.2);
        // Validate the request
        $batch = Batch::create([
            'status' => 'Pending',
            'pf_number' => Auth::user()->name
        ]);
        // Handle the uploaded file
        $file = $request->file('upload');
        // Process the CSV file
        $headerRow = true;
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $total_principal = $total_bank_principal = $total_nbfc_principal = 0;
            $total_interest = $total_bank_interest = $total_nbfc_interest = $total_mfl_sprade = 0;

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Process each row of the CSV file
                // For example, you can save the data to the database
                // $data is an array of values from each row of the CSV
                if ($headerRow) {
                    $headerRow = false;
                    //$mapping = $data;
                    continue;
                }
                
                // if($partner_id == "PhonePe"){
                //     $principal_paid = number_format($amount*0.6,2);
                //     $interest_paid = number_format($amount*0.4,2);
                //     $total_nbfc_principal = number_format($principal_paid*0.2,2);
                //     $total_nbfc_interest = number_format($interest_paid*0.2,2);
                // }else{
                //     $principal_paid = isset($data[11])? $data[11] : Null;
                //     $interest_paid = isset($data[12])?$data[12]:Null;
                //     $total_nbfc_principal = isset($data[11])?number_format($data[11]*0.2,2):Null;
                //     $total_nbfc_interest = isset($data[12])?number_format($data[12]*0.2,2):Null;
                // }
                $partner_id = $data[0];
                $loan_booking_date = $data[1];
                $loan_id = $data[2];
                $amount = $data[3];
                $co_lending_loan_amount = $data[4];
                $BOM_share_co_lending = $data[5];
                $request_id = $data[6];
                $IRR_Reducing = $data[7];
                $transaction_date = $data[9];
                $BOM_expected_principal = $data[10];
                $BOM_expected_interest = $data[11];
                $other_charge = $data[12];
                $penal_charge = $data[13];
                $EDI_Rec = $data[14];
                $Prin_Rec = $data[15];
                $Int_Rec = $data[16];

                $date = Carbon::createFromFormat('d-m-Y', $transaction_date);
                $year = $date->year;
                $month = $date->month;
                $final_principal = $principal = $Prin_Rec;
                $final_interest = $interest = $Int_Rec;
                $loan_account = LoanAccount::where('mfl_ref_no', $loan_id)->get();

                $nbfc_principal = $Prin_Rec - $BOM_expected_principal;
                $nbfc_interest = $Int_Rec-$BOM_expected_interest;
                // dd($data);
                
                 
                    if (isset($loan_account[0]->id)) {

                      
                        //Valid Entry process
                        $total_principal = $total_principal + $final_principal;
                        $total_interest = $total_interest + $final_interest;
                        
                        $total_bank_principal = $total_bank_principal+$BOM_expected_principal;
                        $total_bank_interest = $total_bank_interest+$BOM_expected_interest ;

                        $total_nbfc_principal = $total_nbfc_principal + $nbfc_principal;
                        $total_nbfc_interest = $total_nbfc_interest + $nbfc_interest;
                        // dd($final_interest,$total_bank_principal,$total_bank_interest);
                        
                        Collection::create([
                            'FORACID' => $request_id,
                            'REQ_NUMBER' => $loan_id,
                            'PRINCIPAL_AMT' => $Prin_Rec,
                            'INTEREST_AMT' => $Int_Rec,
                            'batch_id' => $batch->uuid,
                            'final_principal' => $final_principal,
                            'final_interest' => $final_interest,
                            'bank_principal' => $BOM_expected_principal,
                            'nbfc_principal' => $Prin_Rec - $BOM_expected_principal,
                            'bank_interest' => $BOM_expected_interest,
                            'nbfc_interest' => $Int_Rec-$BOM_expected_interest,
                            'mfl_sprade' => 0,
                            'status' => 'Pending',
                            'pf_number' => Auth::user()->name,
                            'loan_account_number' => $loan_account[0]->loan_account_number,
                            'Partner_ID' => $partner_id,
                            'MFL_Loan_Amount' => $amount,
                            "YEAR" => $year,
                            "MONTH" => $month,
                            'Other_charges' => $other_charge,
                            'Penal_charge' => $penal_charge,
                            'loan_booking_date' => $loan_booking_date,
                            'co_lending_loan_amount' => $co_lending_loan_amount,
                            'BOM_share_co_lending' => $BOM_share_co_lending,
                            'IRR_Reducing' => $IRR_Reducing
                        ]);
                    } else {
                        Collection::create([
                            'SOL_ID' => Null,
                            'FORACID' => $request_id,
                            'REQ_NUMBER' => $loan_id,
                            'PRINCIPAL_AMT' => $Prin_Rec,
                            'INTEREST_AMT' => $Int_Rec,
                            'batch_id' => $batch->uuid,
                            'status' => 'Failed',
                            'comment' => 'MFL REF NO NOT FOUND',
                            'pf_number' => Auth::user()->name
                        ]);
                        continue;
                    }
                
                
            }
            $batch->update([
                'total_principal' => $total_principal,
                'total_interest' => $total_interest,
                'total_bank_principal' => $total_bank_principal,
                'total_nbfc_principal' => $total_nbfc_principal,
                'total_bank_interest' => $total_bank_interest,
                'total_nbfc_interest' => $total_nbfc_interest,
                'total_mfl_sprade' => $total_mfl_sprade

            ]);
            fclose($handle);
        }
        return redirect(route('batch.show', [$batch]))->with('success', 'CSV file Uploaded successfully.');
    }
    public function store(Request $request)
    {
        $bank_share = env('BANK_SHARE', 0.8);
        $nbfc_share = env('NBFC_SHARE', 0.2);
        // Validate the request
        $batch = Batch::create([
            'status' => 'Pending',
            'pf_number' => Auth::user()->name
        ]);
        // Handle the uploaded file
        $file = $request->file('upload');

        // Process the CSV file
        $headerRow = true;
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $mapping = [];
            $total_principal = $total_bank_principal = $total_nbfc_principal = 0;
            $total_interest = $total_bank_interest = $total_nbfc_interest = $total_mfl_sprade = 0;

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Process each row of the CSV file
                // For example, you can save the data to the database
                // $data is an array of values from each row of the CSV
                if ($headerRow) {
                    $headerRow = false;
                    //$mapping = $data;
                    continue;
                }

                $sol_id = $data[0];
                $FORACID = $data[1];
                $mfl_ref_no = $data[2];
                $month = $data[3];
		$year = $data[4];
                $final_principal = $principal = (float)$data[5];
                $final_interest = $interest = (float)$data[6];
		$total = $principal + $interest;
		$bnk_princ = (float)$data[7];
		$bnk_int = (float)$data[8];
		$nbfc_princ = (float)$data[9];
                $nbfc_int = (float)$data[10];
		$transaction_date = $data[11];
                $loan_account = LoanAccount::where('mfl_ref_no', $mfl_ref_no)->get();
		if (isset($loan_account[0]->id)) {
                    $account = $loan_account[0];
                    $loan_entries = LoanEntry::where('loan_id', $account->loan_id)->latest('id')->first();
		    $bank_interest_due = round($loan_entries->interest_bank_balance, 2);
                    $nbfc_interest_due = round($loan_entries->interest_nbfc_balance, 2);
                    $total_interest_due = round($loan_entries->interest_balance, 2);

                    /**
                     * Calculate Interest As per given rule
		     */
                    $arr = $this->calculateInterest( $account, $principal, $interest);
                   // dd($arr);

                    $final_interest = $arr['final_interest'];
                    $final_principal = $arr['final_principal'];
                    $final_bank_interest = $arr['final_bank_interest'];
                    $final_nbfc_interest = $arr['final_nbfc_interest'];
                    $mfl_sprade = $arr['mfl_sprade'];

                    /** Calculate Bank/NBFC Principal Share */
                    $bank_principal = $final_principal * $bank_share;
                    $nbfc_principal = $final_principal * $nbfc_share;
                    /** Calculate Bank/NBFC Interst Share */
                    //$bank_interest = $this->bank_interest($final_interest);
                    //$nbfc_interest = $this->nbfc_interest($final_interest);

                    $total_principal = $total_principal + (float)$data[5];
                    $total_interest = $total_interest + (float)$data[6];

                    $total_bank_principal = $total_bank_principal + $bnk_princ;
                    $total_nbfc_principal = $total_nbfc_principal + $nbfc_princ;

                    $total_bank_interest = $total_bank_interest + $bnk_int;
                    $total_nbfc_interest = $total_nbfc_interest + $nbfc_int;

                    $total_mfl_sprade = $total_mfl_sprade + $mfl_sprade;

                    Collection::create([
                        'FORACID' => $FORACID,
                        'REQ_NUMBER' => $mfl_ref_no,
                        'MONTH' => $month,
                        'YEAR' => $year,
                        'PRINCIPAL_AMT' => $principal,
                        'INTEREST_AMT' => $interest,
                        'batch_id' => $batch->uuid,
                        'final_principal' => $final_principal,
                        'final_interest' => $final_interest,
                        'bank_principal' => $bnk_princ,
                        'nbfc_principal' => $nbfc_princ,
                        'bank_interest' => $bnk_int,
                        'nbfc_interest' => $nbfc_int,
			'mfl_sprade' => $mfl_sprade,
			'transaction_date' => $transaction_date,
			'calc_bank_interest' => $final_interest,
			'calc_nbfc_interest' =>  $final_bank_interest,
			'calc_bank_principal' => $final_principal*0.8,
			'calc_nbfc_principal' =>  $final_principal*0.2,
                        'status' => 'Pending',
                        'pf_number' => Auth::user()->name
                    ]);
                } else {
                    Collection::create([
                        'SOL_ID' => $sol_id,
                        'FORACID' => $FORACID,
                        'REQ_NUMBER' => $mfl_ref_no,
                        'MONTH' => $month,
                        'YEAR' => $year,
                        'PRINCIPAL_AMT' => $principal,
                        'INTEREST_AMT' => $interest,
                        'batch_id' => $batch->uuid,
                        'status' => 'Failed',
                        'comment' => 'MFL REF NO NOT FOUND',
                        'pf_number' => Auth::user()->name
                    ]);
                    continue;
                }
            }
            $batch->update([
                'total_principal' => $total_principal,
                'total_interest' => $total_interest,
                'total_bank_principal' => $total_bank_principal,
                'total_nbfc_principal' => $total_nbfc_principal,
                'total_bank_interest' => $total_bank_interest,
                'total_nbfc_interest' => $total_nbfc_interest,
                'total_mfl_sprade' => $total_mfl_sprade

            ]);
            //CBS CALL
            // API CALL
            //$utr_number = 'UTR12345678';
            //$loan_account = LoanAccount::where('mfl_ref_no', $data[2])->get()[0];

            // $batch->update([
            //    'utr_number' => $utr_number
            //]);
            //Return UTR REF NO.
            //Update UTR REF No. in batch and insert entries into total,bank and nbfc loan entries table
            //
            fclose($handle);
        }

        return redirect(route('batch.show', [$batch]))->with('success', 'CSV file Uploaded successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function show(Collection $collection)
    {
        dd($collection);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function edit(Collection $collection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Collection $collection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function destroy(Collection $collection)
    {
        //
    }

    public function calculateInterest($account, $principal, $interest)
    {
        echo 'Total Recieved:' . $total = round($principal + $interest, 2);
        echo '<br>';
        $loan_entries = LoanEntry::where('loan_id', $account->loan_id)->latest('id')->first();
        echo 'Total Interest Due -' . $interest_balance = round($loan_entries->interest_balance, 2);
        $interest_bank_balance = round($loan_entries->interest_bank_balance, 2);
        $interest_nbfc_balance = round($loan_entries->interest_nbfc_balance, 2);
        echo '<br>';
        echo 'Total Interest Due Bank+NBFC -' . $total_interest_balance = $interest_bank_balance + $interest_nbfc_balance;
        echo '<br>';
        //Calculate Interest til Date from last entry
        //$last_date = $loan_entries->entry_date;
        //$end_date =  date("Y-m-d");
        //$end_date = '2023-12-16';
        //$total_interest = Interest::where('loan_id', $loan_account->loan_id)->where('interest_date', '>=', $last_date)->where('interest_date', '<=', $end_date)->sum('total_interest');
        //$bank_interest_1 = Interest::where('loan_id', $loan_account->loan_id)->where('interest_date', '>=', $last_date)->where('interest_date', '<=', $end_date)->sum('bank_interest');
        //$nbfc_interest_1 = Interest::where('loan_id', $loan_account->loan_id)->where('interest_date', '>=', $last_date)->where('interest_date', '<=', $end_date)->sum('nbfc_interest');
        if ($interest_balance == $total) {
            echo 'case1';
            /**
             * If Interest greater than Total amount received
             * total will consider as for interest
             * Due interest 10000
             * Total 10000 (interest+principal)
             */
            $final_principal = 0;
            $final_interest = $total;
            $final_bank_interest = $interest_bank_balance;
            $final_nbfc_interest = $interest_nbfc_balance;
        } else if ($interest_balance > $interest && $interest_balance < $total) {
            echo 'case2';
            /**
             * If Interest greater than Total amount received
             * total will consider as for interest
             * Total interest 5500
             * Total 5000+4000 (interest+principal)
             * Interest 5000 and principal 3500
             */
            $final_principal = $total - $interest_balance;
            $final_interest = $interest_balance;
            $final_bank_interest = $interest_bank_balance;
            $final_nbfc_interest = $interest_nbfc_balance;
        } else if ($interest_balance <= $interest) {
            echo 'case4';
            /**
             * If Total Interest less than or equal to interest
             * Due Interest 10000
             * Total 12000+6000 (interest+principal)
             */
            $final_principal = $principal;
            $final_interest = $interest_balance;
            $final_bank_interest = $interest_bank_balance;
            $final_nbfc_interest = $interest_nbfc_balance;
        } elseif ($total_interest_balance <= $total) {
            echo 'case5';
            $final_principal = $total - $total_interest_balance;
            $final_interest = $total_interest_balance;
            $final_bank_interest = $interest_bank_balance;
            $final_nbfc_interest = $interest_nbfc_balance;
        } else if ($interest_balance > $interest && $interest_balance > $total) {
            echo 'case3';
            /**
             * If Interest greater than Total amount received
             * total will consider as for interest
             * Due interest 10000
             * Total 5000+4000 (interest+principal)
             * Interest 4000 and principal 4000
             */
            $final_principal = 0;
            $final_interest = $total;
            $final_bank_interest = $this->bank_interest($final_interest);
            $final_nbfc_interest = $this->nbfc_interest($final_interest);
        }
        //echo '<br>' . $total . '-' . $final_principal . '-' . $final_bank_interest . '-' . $final_nbfc_interest;
        $mfl_sprade = round($total - ($final_principal + $final_bank_interest + $final_nbfc_interest), 2);
        $arr = array(
            'final_interest' => $final_interest,
            'final_principal' => $final_principal,
            'final_bank_interest' => $final_bank_interest,
            'final_nbfc_interest' => $final_nbfc_interest,
            'mfl_sprade' => $mfl_sprade
        );
        return $arr;
    }

    public function testCsv()
    {
        //$filename = $this->argument('filename');
        $filename = 'sample_BOM_Master_June 2024.csv';
        $filePath = storage_path('app/' . $filename);

        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->error('CSV file not found or not readable.');
            return 1;
        }
        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = null;

            // Read the CSV file line by line
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data = array_combine($header, $row);

                    $loan_id = str_replace('B', 'LOAN', $data['BankLoanAcNo']);
                    $ckyc = str_replace('CKYC', '', $data['Number_CKYC']);
                    $ucic = str_replace('MF', '', $data['UCIC_M']);

                    $loan_account = LoanAccount::where('mfl_ref_no', $data['Loan Code'])->where('loan_id', $loan_id)->get()[0];
                    if (isset($loan_account->id)) {
                        $loan_account->update([
                            'ckyc_no' => $ckyc,
                            'ucic' => $ucic,
                        ]);
                    }
                }
            }

            fclose($handle);
        }
    }
}
