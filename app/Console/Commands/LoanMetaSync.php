<?php

namespace App\Console\Commands;

use App\Models\LoanAccount;
use App\Models\LoanEntry;
use App\Models\LoanMeta;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LoanMetaSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loanmeta:generate';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $loan_metas = LoanMeta::select('object_id')->groupBy('object_id')->get();
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
                    'entry_month' => Carbon::parse($loanmeta['bank_date'])->month,
                    'entry_year' => Carbon::parse($loanmeta['bank_date'])->year,
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
                    'head' => 'principal',
                    'jnl_no' => isset($loanmeta['utr_ref']) ? $loanmeta['utr_ref'] : '123456',
                    'principal_balance' => $loanmeta['sanction_limit'],
                    'principal_bank_balance' => $loanmeta['sanction_limit'] * 0.8,
                    'principal_nbfc_balance' => $loanmeta['sanction_limit'] * 0.2,
                ]);
            }
        }
        return 0;
    }
}
