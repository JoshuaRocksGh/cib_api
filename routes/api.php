<?php

use App\Http\Controllers\Accounts\AccountController;
use App\Http\Controllers\BULK\BulkKorporController;
use App\Http\Controllers\BULK\BulkTransferController;
use App\Http\Controllers\BULK\ImportExcelController;
use App\Http\Controllers\PendingRequestController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Requests\StatementRequestGoForPendingController;
use App\Http\Controllers\Transfers\OwnAccountGoForPendingController;
use App\Http\Controllers\Transfers\SameBankGoForPendingController;
// use App\Http\Controllers\ApprovalRequestController;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/import', [ImportExcelController::class, 'import'])->name('import');
Route::post('/bulk-korpor-upload', [BulkKorporController::class, 'bulk_korpor_upload'])->name('bulk-korpor-upload');

Route::post('/own-account-gone-for-pending', [wn::class, 'OwnAccountGoForPending'])->name('own-account-gone-for-pending');
Route::post('/same-bank-gone-for-pending', [SameBankGoForPendingController::class, 'sameBankGoForPending'])->name('same-bank-gone-for-pending');
Route::post('/statement-request-gone-for-pending', [StatementRequestGoForPendingController::class, 'statementRequestGoForPending'])->name('statement-request-gone-for-pending');

Route::get('/get-bulk-upload-list', [BulkTransferController::class, 'get_bulk_upload_list'])->name('get-bulk-upload-list');
Route::get('/get-bulk-upload-detail-list-api', [BulkTransferController::class, 'get_bulk_upload_detail_list_api'])->name('get-bulk-upload-detail-list-api');
Route::get('/pending-request-api' , [RequestController::class, 'all_approval_request'])->name('pending-request-api');
Route::post('/approve-request-api', [ApprovalRequestController::class, 'approve_request'])->name('approve-request-api');
Route::post('/approved-request-api', [ApprovedRequestController::class, 'approved_request'])->name('approved-request-api');

Route::post('/account/getAccounts', [AccountController::class, 'get_accounts'])->name('getAccounts');
