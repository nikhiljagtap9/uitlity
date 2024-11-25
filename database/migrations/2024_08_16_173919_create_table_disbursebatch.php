<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDisbursebatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disbursebatch', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->decimal('total_loan_amount', 12, 2);
            $table->decimal('total_sanction_amount', 12, 2);
            $table->decimal('nbfc_sanction_amount', 12, 2);
            $table->decimal('bank_sanction_amount', 12, 2);
            $table->string('status')->default('Pending');
            $table->string('pf_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disbursebatch');
    }
}
