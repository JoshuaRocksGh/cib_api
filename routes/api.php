<?php

use App\Http\Controllers\BULK\ImportExcelController;
use App\Http\Controllers\Transfers\OwnAccountGoForPendingController;
use App\Http\Controllers\Transfers\SameBankGoForPendingController;
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
Route::post('/own-account-gone-for-pending', [OwnAccountGoForPendingController::class, 'OwnAccountGoForPending'])->name('own-account-gone-for-pending');
Route::post('/same-bank-gone-for-pending', [SameBankGoForPendingController::class, 'sameBankGoForPending'])->name('same-bank-gone-for-pending');
