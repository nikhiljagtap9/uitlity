<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\JobProgressController;
use App\Http\Controllers\BankLoanEntryController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\CbsApiController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DisbursebatchController;
use App\Http\Controllers\DisbursementController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\LoanAccountController;
use App\Http\Controllers\LoanEntryController;
use App\Http\Controllers\LoanMetaController;
use App\Http\Controllers\NbfcLoanEntryController;
use App\Http\Controllers\RepaymentScheduleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TestController;
use App\Models\LoanMeta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UploadFileController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home',function(){return redirect()->route('disbursement.create');})->name('home');

Route::get('/job-progress/{jobId}', [JobProgressController::class, 'show'])->name('jobprogress');

Route::get('/startJob', [Controller::class, 'startJob'])->name('startJob');

Route::group(array('middleware' => ['auth']), function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::resource('loan_account', LoanAccountController::class);
    Route::resource('loan_entries', LoanEntryController::class);
    Route::resource('repayment_schedule', RepaymentScheduleController::class);
    Route::resource('collection', CollectionController::class);
    Route::resource('batch', BatchController::class);
    Route::resource('cbsapi', CbsApiController::class);

    Route::resource('loan_meta', LoanMetaController::class);
    Route::resource('interest', InterestController::class);
    Route::resource('nbfc_loan_entries', NbfcLoanEntryController::class);
    Route::resource('bank_loan_entries', BankLoanEntryController::class);

    Route::resource('disbursement', DisbursementController::class);
    Route::resource('disbursebatch', DisbursebatchController::class);

    Route::post('/disbursment', [CbsApiController::class, 'disbursment'])->name('disbursment');
    Route::post('/runBre', [DisbursementController::class, 'runBre'])->name('disbursement.runBre');
    Route::post('/processChunks', [DisbursementController::class, 'processChunks'])->name('processChunks');
    Route::post('/processRejectedChunks', [DisbursebatchController::class, 'processRejectedChunks'])->name('processRejectedChunks');

    Route::get('/loan_entries/{loan_id}/{name?}', [LoanEntryController::class, 'index'])->name('loan_entries.list');
    Route::get('/classification/{classification}', [LoanAccountController::class, 'classification'])->name('classification');
    Route::get('/closed', [LoanAccountController::class, 'closed'])->name('closed');

    Route::get('/dashboard', [BatchController::class, 'dashboard'])->name('dashboard');
    Route::get('/nbfc_dashboard', [DashboardController::class, 'getLoanData'])->name('nbfc_dashboard');


    Route::get('loanaccount/export', [LoanAccountController::class, 'export'])->name('export');
    Route::get('/download/{filePath}', [LoanAccountController::class, 'download'])->where('filePath', '.*');

    Route::resource('setting', SettingController::class);

    Route::get('generatepdf/{loan_id}/{name?}', [LoanEntryController::class, 'generatePDF'])->name('generatepdf');
    Route::get('loanentry/export/{loan_id}/{name?}', [LoanEntryController::class, 'export'])->name('generatecsv');
});

Route::get('/customerSelection', [DisbursementController::class, 'customerSelection'])->name('customerSelection');
Route::get('/customerSelectionBharatPay', [DisbursementController::class, 'customerSelectionBharatPay'])->name('customerSelectionBharatPay');
Route::get('/ckyctest', [DisbursementController::class, 'ckyctest'])->name('ckyctest');
//Route::get('/PanVerifyTest', [DisbursementController::class, 'PanVerifyTest'])->name('PanVerifyTest');
Route::get('/PanVerifyTest', [TestController::class, 'PanVerifyTest'])->name('PanVerifyTest');

Route::get('/pantest', [TestController::class, 'PanEnquiryTest'])->name('pantest');

Route::group(array('middleware' => ['auth', 'admin']), function () {});

Route::group(array('middleware' => ['auth', 'admin', 'branch_manager']), function () {});

Route::get('/exportCSV', [LoanAccountController::class, 'exportCSV'])->name('exportCSV');

Route::get('/downloadZip/{loan_id}', [LoanAccountController::class, 'downloadZip'])->name('downloadZip');

Route::get('/loginwithtoken', [LoginController::class, 'loginwithtoken'])->name('loginwithtoken');

Route::get('/job-progress/view/{jobId}', [JobProgressController::class, 'showJobProgress']);

Route::get('/experian-test', [TestController::class, 'cibil']);
/**
 *
 *
 * */
//Route::post('/upload_excel', [TestController::class, 'exportToCSV'])->name('uploadexcel');
//Route::get('/upload_excel', [TestController::class, 'createcsv'])->name('createcsv');
Route::get('/test_interest', [InterestController::class, 'test'])->name('test');

Route::group(array('middleware' => ['auth']), function () {
	Route::get('/upload_file', [UploadFileController::class, 'createcsv'])->name('createcsv');

	Route::post('/upload_file', [UploadFileController::class, 'exportToCSV'])->name('upload_excel');
	Route::get('/download-sample', [UploadFileController::class, 'downloadSampleCSV'])->name('download.sample');
	Route::get('/downloadErrorFile', [UploadFileController::class, 'downloadErrorFile'])->name('downloadErrorFile');  
});
//Route::get('/approve-loans', [DisbursebatchController::class, 'approveLoans'])->name('approveLoans');


Route::get('/getData/{disbursebatch}', [DisbursebatchController::class,'show'])->name('disbursebatch.getData');

