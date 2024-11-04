<?php

namespace App\Imports;

use App\Models\Disbursebatch;
use App\Models\Disbursement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class DisbursementImport implements ToModel, WithChunkReading, ShouldQueue
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $batch = Disbursebatch::create([
            'status' => 'Pending',
            'pf_number' => Auth::user()->name
        ]);
        $row['status'] = 'Pending';
        if ($row['mfl_loan_id']) {
            $mfl_loan_id = $row['mfl_loan_id'];
            $count = Disbursement::where('mfl_loan_id', $mfl_loan_id)->where('batch_id', $batch->uuid)->count();
            if ($count > 0) {
                $row['status'] = 'Duplicate';
            } else {
                $count = Disbursement::where('mfl_loan_id', $mfl_loan_id)->where('status', 'Approved')->count();
                if ($count > 0) {
                    $row['status'] = 'Duplicate';
                }
            }
        }

        $row['batch_id'] = $batch->uuid;

        //Disbursement::create($data);
        return new Disbursement($row);
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
