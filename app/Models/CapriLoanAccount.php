<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CapriLoanAccount extends Model
{
    use HasFactory;

    protected $table = 'loan_accounts';
    protected $guarded = [];

    // Use custom connection for Capri database
   // protected $connection = 'capri_mysql';

    public static function getDataFromCapri($status)
    {
     // Fetch loan account data based on status ('active' or 'closed')
          //  return DB::connection('capri_mysql') // Explicitly use the capri database connection
            return DB::table('loan_accounts')
                ->where('loan_status', $status)
                ->selectRaw('COUNT(id) as total')
                ->selectRaw('SUM(bank_sanction_amount) as total_bank_sanction')
                ->selectRaw('SUM(nbfc_sanction_amount) as total_nbfc_sanction')
                ->get()[0];



    /*    return DB::connection('capri_mysql') // Explicitly use the capri database connection
            ->table('loan_accounts')
            ->selectRaw('count(id) as total, loan_status, SUM(bank_sanction_amount) as bank_sanction_amount, SUM(nbfc_sanction_amount) as nbfc_sanction_amount')
            ->groupBy('loan_status')
            ->get();
     */
    }
}

class MuthootGoldAccount extends Model
{
    use HasFactory;

    protected $table = 'loan_accounts';
    protected $guarded = [];

    // Use custom connection for Capri database
    protected $connection = 'muthoot_mysql';

    public static function getDataFromMuthootGold($status)
    {
        /*return DB::connection('muthoot_mysql') // Explicitly use the capri database connection
            ->table('loan_accounts')
            ->selectRaw('count(id) as total, loan_status, SUM(bank_sanction_amount) as bank_sanction_amount, SUM(nbfc_sanction_amount) as nbfc_sanction_amount')
            ->groupBy('loan_status')
            ->get();

         */


         return DB::connection('muthoot_mysql') // Explicitly use the capri database connection
                ->table('loan_accounts')
                ->where('loan_status', $status)
                ->selectRaw('COUNT(id) as total')
                ->selectRaw('SUM(bank_sanction_amount) as total_bank_sanction')
                ->selectRaw('SUM(nbfc_sanction_amount) as total_nbfc_sanction')
                ->get()[0];

    }
}
class LendingKartLoanAccount extends Model
{
    use HasFactory;

    //protected $table = 'loan_accounts';
    protected $guarded = [];

    // Use custom connection for Capri database
    protected $connection = 'capri_mysql';

    public static function getLendingKartLoanData($partnerId)
    {
        $query = "WITH q0 AS (
                    SELECT COUNT(1) AS total, loan_status,
                    IFNULL(SUM(bank_sanction_amount), 0) AS bank_sanction_amount,
                    IFNULL(SUM(nbfc_sanction_amount), 0) AS nbfc_sanction_amount
                    FROM data_store.all_loans_dataset
                    WHERE loan_status IN ('pending-for-sanction', 'pending-for-documentation', 'pending-for-approval', 'pending-for-bank-disbursal', 'rejected', 'active')
                    AND partner_id = ?
                    GROUP BY loan_status
                  )
                  SELECT * FROM q0";

        // Execute the raw query
        return DB::connection('lendingkart_mysql')->select($query, [$partnerId]);
    }
}
