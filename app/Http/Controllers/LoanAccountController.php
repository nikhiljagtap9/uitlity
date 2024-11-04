<?php

namespace App\Http\Controllers;

use App\Exports\LoanAccountExport;
use App\Jobs\ExportLoanAccounts;
use App\Models\JobStatus;
use App\Models\LoanAccount;
use App\Models\LoanEntry;
use App\Models\LoanMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;
use ZipArchive;

class LoanAccountController extends Controller
{
    public function test()
    {
        $jobId = Str::uuid()->toString();

        $jobStatus = JobStatus::create([
            'job_id' => $jobId,
            'status' => 'pending',
            'progress' => 0,
        ]);
        ExportLoanAccounts::dispatch($jobStatus->id);
    }
    public function exportCSV(Request $request)
    {
        $fileName = 'loan_accounts_' . date('d-m-Y') . '.csv';

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
                'ID',
                'LAPP_ID',
                'LOAN_ID',
                'MFL_REF_NO',
                'UCIC',
                'BANK_INTEREST',
                'NBFC_INTEREST',
                'SANCTION_LIMIT',
                'BANK_SANCTION_AMOUNT',
                'NBFC_SANCTION_AMOUNT',
                'TOTAL_BALANCE',
                'BANK_BALANCE',
                'LOAN_TENURE',
                'BANK_LOAN_DATE',
                'NBFC_LOAN_DATE',
                'LOAN_STATUS',
                'CREATED_AT',
                'UPDATED_AT',
                'NBFC_BALANCE',
                'CLASSIFICATION',
                'NBFC_BACKDATE',
                'BANK_BACKDATE',
                'UTR_BOM_POS_UPDATE',
                'LOAN_ACCOUNT_NUMBER',
                'JOB_TYPE',
                'PAN_NUMBER',
                'POSTAL_CODE',
                'STATE_CODE',
                'CITY_CODE',
                'ADDRESS1',
                'ADDRESS2',
                'EMAIL',
                'MOBILE_NUMBER',
                'CASTE',
                'COMMUNITY',
                'CKYC_NO',
                'DATE_OF_BIRTH',
                'GENDER',
                'CUSTOMER_NAME',
                'CUSTOMER_TITLE',
                'LTV',
                'BATCH_ID',
                'TITLE',
                'DOB',
                'AGE',
                'PAN_CARD',
                'LOAN_AMOUNT',
                'SANCTION_AMOUNT',
                'INTEREST_RATE',
                'PROCESSING_FEES',
                'UDYOG_UAADHAAR_NUMBER',
                'CKYC',
                'CREDIT_SCORE',
                'STATUS',
                'CGCL_CUSTOMER_NUMBER',
                'CGCL_ACCOUNT_NUMBER',
                'DISBURSMENT_DETAIL',
                'FIRST_NAME',
                'MIDDLE_NAME',
                'LAST_NAME',
                'MOTHER_NAME',
                'REMI_STATUS',
                'NATIONALITY_CODE',
                'SEC_ID_TYPE',
                'AADHAR_NO',
                'CKYC_DATE',
                'SANCTION_DATE',
                'POS',
                'INSURANCE_FINANCED',
                'REMAINING_LOAN_TENURE',
                'TOTAL_WEIGHT',
                'NAME_VALUER',
                'ROLE_VALUER',
                'GROSS_WEIGHT',
                'TOTAL_WEIGHT_VALUER',
                'GOLD_VALUE',
                'NET_WEIGHT',
                'GOLD_RATE',
                'MARKET_RATE',
                'TOTAL_VALUE',
                'REPAYMENT_TYPE',
                'DATE_DISBURSEMENT',
                'MATURITY_DATE',
                'ACCOUNT_STATUS',
                'GOLD_PURITY',
                'DISBURSAL_MODE',
                'COLLATERAL_DESCRIPTION',
                'BUSINESS_TYPE',
                'VALUATION_DATE',
                'REALIZABLE_SECURITY_VALUE',
                'REPAY_DAY',
                'EMI_START_DATE',
                'MORATORIUM',
                'ASSETS_ID',
                'SECURITY_INTEREST_ID',
                'CERSAI_DATE',
                'CIC',


            ];
            fputcsv($file, $columns);

            // Add the data
            $loan_status = 'active';
            if (isset($request->loan_status)) {
                $loan_status = $request->loan_status;
            }
            if (isset($request->start_date)) {
                $loan_accounts = LoanAccount::where('bank_loan_date', '>=', $request->start_date)
                    ->where('bank_loan_date', '<=', $request->end_date)
                    ->where('loan_status', $loan_status)
                    ->get();
            } else {
                $loan_accounts = LoanAccount::where('loan_status', $loan_status)->get();
            }
            foreach ($loan_accounts as $loan_account) {
                fputcsv($file, [
                    $loan_account->id,
                    $loan_account->lapp_id,
                    $loan_account->loan_id,
                    $loan_account->mfl_ref_no,
                    $loan_account->ucic,
                    $loan_account->bank_interest,
                    $loan_account->nbfc_interest,
                    $loan_account->sanction_limit,
                    $loan_account->bank_sanction_amount,
                    $loan_account->nbfc_sanction_amount,
                    $loan_account->total_balance,
                    $loan_account->bank_balance,
                    $loan_account->loan_tenure,
                    $loan_account->bank_loan_date,
                    $loan_account->nbfc_loan_date,
                    $loan_account->loan_status,
                    $loan_account->created_at,
                    $loan_account->updated_at,
                    $loan_account->nbfc_balance,
                    $loan_account->classification,
                    $loan_account->nbfc_backdate,
                    $loan_account->bank_backdate,
                    $loan_account->utr_bom_pos_update,
                    $loan_account->loan_account_number,
                    $loan_account->job_type,
                    $loan_account->pan_number,
                    $loan_account->postal_code,
                    $loan_account->state_code,
                    $loan_account->city_code,
                    $loan_account->address1,
                    $loan_account->address2,
                    $loan_account->email,
                    $loan_account->mobile_number,
                    $loan_account->caste,
                    $loan_account->community,
                    $loan_account->ckyc_no,
                    $loan_account->date_of_birth,
                    $loan_account->gender,
                    $loan_account->customer_name,
                    $loan_account->customer_title,
                    $loan_account->ltv,
                    $loan_account->batch_id,
                    $loan_account->title,
                    $loan_account->dob,
                    $loan_account->AGE,
                    $loan_account->pan_card,
                    $loan_account->loan_amount,
                    $loan_account->sanction_amount,
                    $loan_account->interest_rate,
                    $loan_account->processing_fees,
                    $loan_account->udyog_uaadhaar_number,
                    $loan_account->ckyc,
                    $loan_account->credit_score,
                    $loan_account->status,
                    $loan_account->CGCL_Customer_Number,
                    $loan_account->CGCL_Account_Number,
                    $loan_account->DISBURSMENT_DETAIL,
                    $loan_account->FIRST_NAME,
                    $loan_account->MIDDLE_NAME,
                    $loan_account->LAST_NAME,
                    $loan_account->MOTHER_NAME,
                    $loan_account->RESI_STATUS,
                    $loan_account->NATIONALITY_CODE,
                    $loan_account->SEC_ID_TYPE,
                    $loan_account->AADHAR_NO,
                    $loan_account->CKYC_DATE,
                    $loan_account->SANCTION_DATE,
                    $loan_account->POS,
                    $loan_account->INSURANCE_FINANCED,
                    $loan_account->REMAINING_LOAN_TENURE,
                    $loan_account->Total_Weight,
                    $loan_account->Name_Valuer,
                    $loan_account->Role_Valuer,
                    $loan_account->Gross_weight,
                    $loan_account->Total_Weight_Valuer,
                    $loan_account->Gold_Value,
                    $loan_account->Net_Weight,
                    $loan_account->Gold_Rate,
                    $loan_account->Market_Rate,
                    $loan_account->Total_Value,
                    $loan_account->Repayment_Type,
                    $loan_account->Date_Disbursement,
                    $loan_account->Maturity_Date,
                    $loan_account->Account_status,
                    $loan_account->Gold_Purity,
                    $loan_account->Disbursal_Mode,
                    $loan_account->Collateral_Description,
                    $loan_account->Business_Type,
                    $loan_account->Valuation_Date,
                    $loan_account->Realizable_Security_Value,
                    $loan_account->REPAY_DAY,
                    $loan_account->EMI_START_DATE,
                    $loan_account->MORATORIUM,
                    $loan_account->Assets_ID,
                    $loan_account->Security_Interest_ID,
                    $loan_account->cersai_date,
                    $loan_account->CIC,
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function export(Request $request)
    {
        //return Excel::download(new LoanAccountExport, 'LoanAccounts.xlsx');
        $filePath = 'exports/LoanAccountExport.xlsx';
        //Excel::store(new LoanAccountExport($request->start_date, $request->end_date), $filePath, 'public');
        return Excel::download(new LoanAccountExport($request->start_date, $request->end_date), 'LoanAccountExport.csv', \Maatwebsite\Excel\Excel::CSV);
        // Return a response to notify that the export has been queued
        //return response()->json(['message' => 'Export is being processed', 'filePath' => $filePath]);
    }
    public function download($filePath)
    {
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath);
        }
        return response()->json(['message' => 'File not found'], 404);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->loan_id) {
            $loan_accounts = LoanAccount::where('loan_id', 'LIKE', '%' . $request->loan_id)->where('loan_status', 'active')->paginate(50);
        } elseif ($request->mfl_ref_no) {
            $loan_accounts = LoanAccount::where('mfl_ref_no', 'LIKE',  $request->mfl_ref_no)->where('loan_status', 'active')->paginate(50);
        } else {
            if ($request->start_date) {
                $loan_accounts = LoanAccount::where('loan_status', 'active')
                    ->where('bank_loan_date', '>=', $request->start_date)
                    ->where('bank_loan_date', '<=', $request->end_date)
                    ->paginate(50);
                //dd($loan_accounts);
            } else {
                $loan_accounts = LoanAccount::where('loan_status', 'active')->paginate(50);
            }
        }
        if ($request->start_date) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        } else {
            $now = Carbon::now();

            // Get the previous date
            $previousDate = $now->copy()->subDay();
            $thirtyDaysAgo = $now->subDays(30);

            $start_date = $previousDate->toDateString();
            $end_date = $thirtyDaysAgo->toDateString();
        }
        //$loan_accounts = LoanAccount::paginate(10);
        return view('loan_account/index', ['loan_accounts' => $loan_accounts, 'loan_id' => $request->loan_id, 'mfl_ref_no' => $request->mfl_ref_no, 'start_date' => $start_date, 'end_date' => $end_date]);
    }
    public function index1(Request $request)
    {

        if ($request->ajax()) {
            if ($request->loan_id) {
                $data = LoanAccount::select('*')->where('loan_id', 'LIKE', '%' . $request->loan_id)->where('loan_status', 'active');
            } elseif ($request->mfl_ref_no) {
                $data = LoanAccount::select('*')->where('mfl_ref_no', 'LIKE',  $request->mfl_ref_no)->where('loan_status', 'active');
            } else {
                $data = LoanAccount::select('*')->where('loan_status', 'active');
            }
            //$data = LoanAccount::select('*')->where('loan_status', 'active');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('loan_id', function ($row) {
                    return str_replace('LOAN', '', $row->loan_id);
                })
                ->editColumn('total_balance', function ($row) {
                    $loan_entries = LoanEntry::where('loan_id', $row->loan_id)->latest('id')->first();

                    return number_format($loan_entries->balance, 2);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">
                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    Menu
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" target="_blank"
                                        href="' . route('loan_entries.list', [$row->loan_id]) . '">Total
                                        Ledger</a>
                                    <a class="dropdown-item" target="_blank"
                                        href="' . route('loan_entries.list', [$row->loan_id, 'bank']) . '">Bank
                                        Ledger</a>
                                    <a class="dropdown-item" target="_blank"
                                        href="' . route('loan_entries.list', [$row->loan_id, 'nbfc']) . '">NBFC
                                        Ledger</a>
                                    <a class="dropdown-item" target="_blank"
                                        href=" ' . route('repayment_schedule.show', [$row->loan_id]) . '">Replayment
                                        Schedule</a>
                                </div>
                            </div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $loan_accounts = LoanAccount::paginate(10);
        return view('loan_account/index', ['loan_accounts' => $loan_accounts, 'loan_id' => $request->loan_id, 'mfl_ref_no' => $request->mfl_ref_no]);
    }

    public function closed(Request $request)
    {
        if ($request->ajax()) {
            $data = LoanAccount::select('*')->where('loan_status', 'closed');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('loan_id', function ($row) {
                    return str_replace('LOAN', '', $row->loan_id);
                })
                ->editColumn('total_balance', function ($row) {
                    return number_format($row->getPrevTotalBalance(), 2);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">
                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    Menu
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" target="_blank"
                                        href="' . route('loan_entries.list', [$row->loan_id]) . '">Total
                                        Ledger</a>
                                    <a class="dropdown-item" target="_blank"
                                        href="' . route('loan_entries.list', [$row->loan_id, 'bank']) . '">Bank
                                        Ledger</a>
                                    <a class="dropdown-item" target="_blank"
                                        href="' . route('loan_entries.list', [$row->loan_id, 'nbfc']) . '">NBFC
                                        Ledger</a>
                                    <a class="dropdown-item" target="_blank"
                                        href=" ' . route('repayment_schedule.show', [$row->loan_id]) . '">Replayment
                                        Schedule</a>
                                </div>
                            </div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $loan_accounts = LoanAccount::where('loan_status', 'closed')->paginate(10);
        return view('loan_account/closed', ['loan_accounts' => $loan_accounts]);
    }

    public function classificationOld($classification = 'STD', Request $request)
    {
        if ($request->ajax()) {
            $data = LoanAccount::select('*')->where('loan_status', 'active')->where('classification', $classification);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('loan_id', function ($row) {
                    return str_replace('LOAN', '', $row->loan_id);
                })
                ->editColumn('total_balance', function ($row) {
                    return number_format($row->getPrevTotalBalance(), 2);
                })
                ->editColumn('total_principal', function ($row) {
                    return number_format($row->getPrevPrincipalBalance(), 2);
                })
                ->editColumn('total_interest', function ($row) {
                    //if ($classification != 'NPA') {
                    return number_format($row->getPrevInterestBalance(), 2);
                    //}
                })
                ->editColumn('closure_date', function ($row) {
                    $tenure = $row->loan_tenure;
                    $bank_loan_date = $row->bank_loan_date;
                    $start = Carbon::parse($bank_loan_date);

                    $closureDate = $start->addMonths($tenure);
                    return $closureDate->format('d-m-Y');
                })
                ->editColumn('DPD', function ($row) {
                    $tenure = $row->loan_tenure;
                    $bank_loan_date = $row->bank_loan_date;
                    $start = Carbon::parse($bank_loan_date);

                    $closureDate = $start->addMonths($tenure);

                    $currentDate = Carbon::now();

                    // Calculate the difference in days
                    return $daysDifference = $currentDate->diffInDays($closureDate, true); // false for signed difference
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">
                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    Menu
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" target="_blank"
                                        href="' . route('loan_entries.show', [$row->loan_id]) . '">View</a>
                                    <a class="dropdown-item" target="_blank"
                                        href="' . route('loan_entries.list', [$row->loan_id]) . '">Total
                                        Ledger</a>
                                    <a class="dropdown-item" target="_blank"
                                        href="' . route('loan_entries.list', [$row->loan_id, 'bank']) . '">Bank
                                        Ledger</a>
                                    <a class="dropdown-item" target="_blank"
                                        href="' . route('loan_entries.list', [$row->loan_id, 'nbfc']) . '">NBFC
                                        Ledger</a>
                                    <a class="dropdown-item" target="_blank"
                                        href=" ' . route('repayment_schedule.show', [$row->loan_id]) . '">Replayment
                                        Schedule</a>
                                </div>
                            </div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $loan_accounts = LoanAccount::where('loan_status', 'active')->where('classification', $classification)->paginate(10);
        return view('loan_account/classification', ['loan_accounts' => $loan_accounts, 'classification' => $classification]);
    }

    public function classification($classification = 'STD', Request $request)
    {
        $loan_accounts = LoanAccount::where('loan_status', 'active')->where('classification', $classification)->paginate(50);
        return view('loan_account/classification', ['loan_accounts' => $loan_accounts, 'classification' => $classification]);
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
     * @param  \App\Models\LoanAccount  $loanAccount
     * @return \Illuminate\Http\Response
     */
    public function show($loan_id)
    {
        $loan_account = LoanAccount::where('loan_id', $loan_id)->get()[0];

        $loan_meta = LoanMeta::select('meta_key', 'meta_value')
            ->where('object_id', $loan_id)->get()
            ->keyBy('meta_key') // key every setting by its name
            ->transform(function ($row) {
                return $row->meta_value; // return only the value
            })
            ->toArray();
        $address = array(11 => 'Aadhaar Number');
        $start = Carbon::parse($loan_account->nbfc_loan_date);
        $closureDate = $start->addMonths($loan_account->loan_tenure);
        $closureDate = $closureDate->subDay();
        return view('loan_account.show', ['loan_account' => $loan_account, 'loan_meta' => $loan_meta, 'address' => $address, 'closureDate' => $closureDate]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LoanAccount  $loanAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(LoanAccount $loanAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LoanAccount  $loanAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoanAccount $loanAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LoanAccount  $loanAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(LoanAccount $loanAccount)
    {
        //
    }

    public function downloadZip($loan_id)
    {
        $zip = new ZipArchive;
        $fileName = $loan_id . '.zip';

        // The path to the folder you want to zip
        $folderPath = storage::disk('external2')->path($loan_id);
        // The temporary file path for the zip file
        $tempFilePath = storage_path($fileName);

        if ($zip->open($tempFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folderPath));
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($folderPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        } else {
            return response()->json(['error' => 'Could not create zip file'], 500);
        }

        return response()->download($tempFilePath)->deleteFileAfterSend(true);
    }
}

