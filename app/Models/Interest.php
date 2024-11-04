<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function loan_account()
    {
        return $this->belongsTo(LoanAccount::class, 'loan_id', 'loan_id');
    }
}
