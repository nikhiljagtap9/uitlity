<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\JobProgressController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\DisbursebatchController;
use App\Http\Controllers\DisbursementController;
use App\Http\Controllers\LoanAccountController;
use App\Http\Controllers\SettingController;
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

/*Route::get('/', function () {
   // return view('welcome');
}); */
Route::get('/', [LoginController::class, 'logout'])->name('logout');

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', function () {
    return redirect()->route('disbursement.create');
})->name('home');

Route::get('/job-progress/{jobId}', [JobProgressController::class, 'show'])->name('jobprogress');

Route::get('/startJob', [Controller::class, 'startJob'])->name('startJob');

Route::group(array('middleware' => ['auth']), function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::resource('batch', BatchController::class);

    Route::resource('disbursement', DisbursementController::class);
    Route::resource('disbursebatch', DisbursebatchController::class);

    Route::post('/runBre', [DisbursementController::class, 'runBre'])->name('disbursement.runBre');
    Route::post('/processChunks', [DisbursementController::class, 'processChunks'])->name('processChunks');
    Route::get('/processChunksTesting', [DisbursementController::class, 'processChunksTesting'])->name('processChunksTesting');
    Route::post('/processRejectedChunks', [DisbursebatchController::class, 'processRejectedChunks'])->name('processRejectedChunks');

    Route::get('/dashboard', [BatchController::class, 'dashboard'])->name('dashboard');
    Route::get('/nbfc_dashboard', [DashboardController::class, 'getLoanData'])->name('nbfc_dashboard');
});

Route::get('/customerSelection', [DisbursementController::class, 'customerSelection'])->name('customerSelection');
Route::get('/customerSelectionBharatPay', [DisbursementController::class, 'customerSelectionBharatPay'])->name('customerSelectionBharatPay');
Route::get('/ckyctest', [DisbursementController::class, 'ckyctest'])->name('ckyctest');

Route::group(array('middleware' => ['auth', 'admin']), function () {});

Route::group(array('middleware' => ['auth', 'admin', 'branch_manager']), function () {});

Route::get('/exportCSV', [LoanAccountController::class, 'exportCSV'])->name('exportCSV');

Route::get('/loginwithtoken/{name}', [LoginController::class, 'loginwithtoken'])->name('loginwithtoken');

Route::get('/job-progress/view/{jobId}', [JobProgressController::class, 'showJobProgress']);

/**
 *
 *
 * */
Route::group(array('middleware' => ['auth']), function () {
    Route::get('/upload_file', [UploadFileController::class, 'createcsv'])->name('createcsv');

    Route::post('/upload_file', [UploadFileController::class, 'exportToCSV'])->name('upload_excel');
    Route::get('/download-sample', [UploadFileController::class, 'downloadSampleCSV'])->name('download.sample');
    Route::get('/downloadErrorFile', [UploadFileController::class, 'downloadErrorFile'])->name('downloadErrorFile');
});
//Route::get('/approve-loans', [DisbursebatchController::class, 'approveLoans'])->name('approveLoans');


Route::get('/getData/{disbursebatch}', [DisbursebatchController::class, 'show'])->name('disbursebatch.getData');
Route::get('/generate-csv', [DisbursementController::class, 'generateCSV'])->name('genereateCSV');
Route::get('/download-sample-excel', function () {
    $filePath = public_path('files/sample.xlsx');
    return response()->download($filePath, 'sample.xlsx');
});
