<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDisbursement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disbursement', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->nullable();
            $table->string('mfl_loan_id')->unique();
            $table->string('customer_id')->nullable();
            $table->string('title')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('business_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('dob')->nullable();
            $table->string('loan_city')->nullable();
            $table->text('business_addr_line1')->nullable();
            $table->text('business_addr_line2')->nullable();
            $table->string('business_lat')->nullable();
            $table->string('business_long')->nullable();
            $table->string('business_zipcode')->nullable();
            $table->string('business_city')->nullable();
            $table->string('pan_card')->nullable();
            $table->string('business_gst_number')->nullable();
            $table->string('loan_city_state')->nullable();
            $table->string('loan_city_state_code')->nullable();
            $table->string('loan_amount')->nullable();
            $table->decimal('sanction_amount', 12, 2)->nullable();
            $table->decimal('interest_rate')->nullable();
            $table->string('loan_tenure')->nullable();
            $table->decimal('processing_fees', 12, 2)->nullable();
            $table->decimal('nbfc_sanction_amount', 12, 2)->nullable();
            $table->decimal('bank_sanction_amount', 12, 2)->nullable();
            $table->string('udyog_uaadhaar_number')->nullable();
            $table->string('ckyc')->nullable();
            $table->string('credit_score')->nullable();
            $table->string('vendor')->nullable();
            $table->string('score_band')->nullable();
            $table->string('tpv')->nullable();
            $table->string('vintage_month')->nullable();
            $table->text('home_addr_line1')->nullable();
            $table->text('home_addr_line2')->nullable();
            $table->text('home_addr_line3')->nullable();
            $table->string('home_city')->nullable();
            $table->string('home_zipcode')->nullable();
            $table->string('kyc_docuement_link')->nullable();
            $table->string('turnover')->nullable();
            $table->string('business_start_date')->nullable();
            $table->string('status')->default('Pending');
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
        Schema::dropIfExists('disbursement');
    }
}
