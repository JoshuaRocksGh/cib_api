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



        // return [
        //     'responseCode' => '44',
        //     'message' => "testing resulting",
        //     'data' => [
        //         "account_no" => $request->account_no,
        //         "destinationAccountId" => $request->destinationAccountId,
        //         "amount" => $request->amount,
        //         "narration" => $request->narration,
        //         "postBy" => $request->postBy,
        //         "customerTel" => $request->customerTel,
        //         "transBy" => $request->transBy,
        //         "customer_no" => $request->customer_no,
        //         "user_alias" => $request->user_alias
        //     ],

        // ];

        //   return $data;

        $account_no = $request->account_no;
        $destinationAccountId = $request->destinationAccountId;
        $amount = $request->amount;
        $currency = $request->currency;
        $narration = $request->narration;
        $postBy = $request->postBy;
        $appBy = '';
        $customerTel = $request->telno;
        $transBy = $request->postBy;
        $user_id = $request->user_id;
        $customer_no = $request->customer_no;
        $user_alias = $request->user_alias;
        $documentRef = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 2) . time());


        // $funds_trans = new ApiFundsTransfer();

        // return $funds_trans->call_own_account_transfer($account_no, $destinationAccountId, $amount, $documentRef, $narration, $postBy, $appBy, $customerTel, $transBy);

        $post_date = Carbon::now();
        $post_date = $post_date->toDateTimeString();



        $query_acc_mandate = DB::table('vw_ibank_mandate')->where('acct_link', $account_no)->value('mandate');


        $query_result = DB::table('tb_corp_bank_req')->insert(
            [
                'request_type' => 'OWN',
                'request_status' => 'P',
                'user_id' => $user_id,
                'user_name' => $user_alias,
                'customer_no' => $customer_no,
                'debit_tel' => $customerTel,
                'account_no' => $account_no,
                'amount' => $amount,
                'currency' => $currency,
                'account_mandate' => $query_acc_mandate,
                'CREDITACCOUNTNUMBER' => $destinationAccountId,
                'narration' => $narration,
                'postedby' => $postBy,
                'transBy' => $transBy,
                'waitinglist' => 'not approved',
                'post_date' => $post_date,
                'documentRef' => $documentRef
            ]
        );


        $amount = number_format($amount, 2);

        if ($query_result) {
            return response()->json([
                'responseCode' => '000',
                'message' => "Transfer ( From: $account_no ~ To: $destinationAccountId ~ Amount: $currency $amount) pending approval ",
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
