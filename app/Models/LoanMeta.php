<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanMeta extends Model
{
    use HasFactory;

    protected $table = 'loan_meta';

    public function loanEntries()
    {
        return $this->hasMany(LoanEntry::class, 'loan_id', 'object_id');
    }
}
