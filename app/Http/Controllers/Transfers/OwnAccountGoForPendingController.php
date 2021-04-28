<?php

namespace App\Http\Controllers\Transfers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnAccountGoForPendingController extends Controller
{
    public function OwnAccountGoForPending(Request $request)
    {

        // return Auth::user();
        // return $request;
        $account_no = $request->payerAccount;
        $destinationAccountId = $request->benAccount;
        $amount = $request->transAmount;
        $narration = $request->confirmNarration;
        $postBy = $request->user_id;
        $appBy = '';
        $customerTel = $request->telno;
        $transBy = $request->user_id;
        $customer_no = $request->customer_no;
        $user_alias = $request->user_alias;
        $documentRef = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 2) . time();


        // $funds_trans = new ApiFundsTransfer();

        // return $funds_trans->call_own_account_transfer($account_no, $destinationAccountId, $amount, $documentRef, $narration, $postBy, $appBy, $customerTel, $transBy);

        $post_date = Carbon::now();
        $post_date = $post_date->toDateTimeString();

        // return $data;


        $query_result = DB::table('tb_corp_bank_req')->insert(
            [
                'request_type' => 'OWN',
                'request_status' => 'P',
                'user_id' => $request->user_id,
                'user_name' => $request->user_alias,
                'customer_no' => $request->user_customer_no,
                'debit_tel' => $customerTel,
                'account_no' => $account_no,
                'amount' => $amount,
                'account_mandate' => null,
                'CREDITACCOUNTNUMBER' => $destinationAccountId,
                'narration' => $narration,
                'postedby' => $postBy,
                'transBy' => $transBy,
                'waitinglist' => 'not approved',
                'post_date' => $post_date
            ]
        );

        if ($query_result) {
            return response()->json([
                'responseCode' => '000',
                'message' => "Transfer ( From: $account_no ~ To: $destinationAccountId ~ Amount: $amount) pending for approval ",
                "data" => null
            ], 200);
        } else {
            return response()->json([
                'responseCode' => '888',
                'message' => 'Transfer between own accounts failed',
                "data" => null
            ], 200);
        }
    }
}