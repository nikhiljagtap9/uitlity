<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAccount extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Automatically generate UUID when creating a new item
    public static function boot()
    {
        parent::boot();
        /*
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
        */

        static::creating(function ($model) {
            $model->loan_id = self::generateUuid();
        });
    }
    private static function generateUuid()
    {
        do {
            $prefix = 'LOAN';
            $length = 16;
            $numericUUID = str_pad((string)random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
            $uniqueUUID = $prefix . $numericUUID;
            $exists = LoanAccount::where('loan_id', $uniqueUUID)->exists();
        } while ($exists);
        return $uniqueUUID;
    }


    public function getTransactionAmount()
    {
        return $this->debit ?: - ($this->credit);
    }

    public function loanEntries()
    {
        return $this->hasMany(LoanEntry::class, 'loan_id', 'loan_id');
    }

    public function getPrevTotalBalance()
    {
        if ($this->loanEntries()->count() == 0) {
            return $this->total_balance;
        } else {
            if ($ledger = $this->loanEntries()->latest('id')->first())
                return $ledger->balance;
        }
        /*
        if ($this->loanEntries()->count() == 0) {
            return $this->total_balance;
        } elseif ($this->loanEntries()->count() == 1) {
            if ($ledger = $this->loanEntries()->latest()->get())
                return $ledger[0]->balance;
        } else {
            if ($ledger = $this->loanEntries()->latest()->take(2)->get())
                return $ledger[1]->balance;
        }
                */
        return null;
    }
    public function getlastEntryDate()
    {
        $ledger = $this->loanEntries()->latest('id')->first();
        return $ledger->entry_date;
    }

    public function getCurrentInterestBalance()
    {
        $ledger = $this->loanEntries()->latest('id')->first();
        $last_date = $ledger->entry_date;
        $end_date =  date("Y-m-d");
        $total_interest = Interest::where('loan_id', $ledger->loan_id)->where('interest_date', '>=', $last_date)->where('interest_date', '<=', $end_date)->sum('total_interest');
        $bank_interest = Interest::where('loan_id', $ledger->loan_id)->where('interest_date', '>=', $last_date)->where('interest_date', '<=', $end_date)->sum('bank_interest');
        $nbfc_interest = Interest::where('loan_id', $ledger->loan_id)->where('interest_date', '>=', $last_date)->where('interest_date', '<=', $end_date)->sum('nbfc_interest');

        $total_outstanding = round($ledger->interest_balance + $total_interest, 2);
        $bank_outstanding = round($ledger->bank_interest_balance + $bank_interest, 2);
        $nbfc_outstanding = round($ledger->nbfc_interest_balance + $nbfc_interest, 2);
        $arr = array(
            'total_interest' => $total_outstanding,
            'bank_interest' => $bank_outstanding,
            'nbfc_interest' => $nbfc_outstanding
        );
        return $arr;
    }

    public function getPrevPrincipalBalance()
    {
        if ($this->loanEntries()->count() == 0) {
            return $this->bank_balance;
        } else {
            if ($ledger = $this->loanEntries()->latest('id')->first())
                return $ledger->principal_balance;
        }
        return null;
    }
    public function getPrevInterestBalance()
    {
        if ($this->loanEntries()->count() == 0) {
            return $this->bank_balance;
        } else {
            if ($ledger = $this->loanEntries()->latest('id')->first())
                return $ledger->interest_balance;
        }
        return null;
    }
    public function getPrevBankBalance()
    {
        if ($this->loanEntries()->count() == 0) {
            return $this->bank_balance;
        } else {
            if ($ledger = $this->loanEntries()->latest('id')->first())
                return $ledger->bank_balance;
        }
        return null;
    }
    public function getPrevNbfcBalance()
    {
        if ($this->loanEntries()->count() == 0) {
            return $this->nbfc_balance;
        } else {
            if ($ledger = $this->loanEntries()->latest('id')->first())
                return $ledger->nbfc_balance;
        }
        return null;
    }
}
