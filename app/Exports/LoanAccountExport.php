<?php

namespace App\Exports;

use App\Models\LoanAccount;
use Maatwebsite\Excel\Concerns\FromCollection;

class LoanAccountExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return LoanAccount::all();
    }
}
