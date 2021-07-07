<?php

namespace App\Http\Controllers\Transfers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SameBankGoForPendingController extends Controller
{
    public function sameBankGoForPending(Request $request)
    {

        // return Auth::user();
        // return $request;

/*

        return [
            'responseCode' => '44',
            'message' => "testing reeusting",
            'data' => [
                "account_no" => $request->account_no,
                "destinationAccountId" => $request->destinationAccountId,
                "currency" => $request->currency,
                "amount" => $request->amount,
                "narration" => $request->narration,
                "postBy" => $request->postBy,
                "customerTel" => $request->customerTel,
                "transBy" => $request->transBy,
                "customer_no" => $request->customer_no,
                "user_alias" => $request->user_alias
            ],

        ];

        */

       $validator = Validator::make($request->all(), [
        'user_mandate' => 'required',
        'account_mandate' => 'required',
        'postBy' => 'required',
        'destinationAccountId' => 'required',
        'account_no' => 'required',
        'customer_no' => 'required',
        'user_alias' => 'required',
        'currency' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'responseCode' => '422',
            'message' => 'Error validation error',
            'error' => $validator->errors(),
            'data' => null
        ], 200);
    }

        $account_no = $request->account_no;
        $destinationAccountId = $request->destinationAccountId;
        $currency = $request->currency;
        $account_mandate = $request->account_mandate;
        $amount = $request->amount;
        $narration = $request->narration;
        $postBy = $request->postBy;
        $appBy = '';
        $customerTel = $request->telno;
        $transBy = $request->postBy;
        $customer_no = $request->customer_no;
        $user_alias = $request->user_alias;
        $documentRef = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 2) . time());


        // $funds_trans = new ApiFundsTransfer();

        // return $funds_trans->call_own_account_transfer($account_no, $destinationAccountId, $amount, $documentRef, $narration, $postBy, $appBy, $customerTel, $transBy);

        $post_date = Carbon::now();
        $post_date = $post_date->toDateTimeString();

        // return $data;


        $query_result = DB::table('tb_corp_bank_req')->insert(
            [
                'request_type' => 'SAB',
                'request_status' => 'P',
                'user_id' => $postBy,
                'user_name' => $user_alias,
                'customer_no' => $customer_no,
                'debit_tel' => $customerTel,
                'account_no' => $account_no,
                'currency' => $currency,
                'amount' => $amount,
                'account_mandate' => $account_mandate,
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
                'message' => "Transfer ( From: $account_no ~ To: $destinationAccountId ~ Amount: $currency $amount) pending for approval ",
                "data" => null
            ], 200);
        } else {
            return response()->json([
                'responseCode' => '888',
                'message' => "Transfer (From: $account_no ~ To: $destinationAccountId ) failed",
                "data" => null
            ], 200);
        }
    }
}
