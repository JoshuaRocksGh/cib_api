<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KorporRequestController extends Controller
{
    //

    public function send_korpor_request(Request $request) {

        $validator = Validator::make($request->all(), [

            'user_mandate' => 'required',
            'userID' => 'required' ,
            'account_mandate' => 'required',
            'postBy' => 'required',
            'credit_account' => 'required',
            'account_no' => 'required',
            'customer_no' => 'required',
            'user_alias' => 'required',
            'currency' => 'required',
            'amount' => 'required',
            'receiver_address' => 'required',
            'receiver_name' => 'required',
            'currCode' => 'required' ,
            'narration' => 'required'


        ]);



        if ($validator->fails()) {
            return response()->json([
                'responseCode' => '422',
                'message' => 'Error validation error',
                'error' => $validator->errors(),
                'data' => null
            ], 200);
        }

        $receiver_name = $request->receiver_name;
        $receiver_address = $request->receiver_address;
        $userID = $request->userID;
        $account_no = $request->account_no;
        $credit_account_number = $request->credit_account;
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
        $currCode = $request->currCode;
        $documentRef = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 2) . time());
        $post_date = Carbon::now();
        $post_date = $post_date->toDateTimeString();

        // $data = [

        //         'request_type' => 'KORP',
        //         'request_status' => 'P',
        //         'beneficiary_name' => $receiver_name ,
        //         'beneficiary_address' => $receiver_address ,
        //         'user_id' => $postBy,
        //         'user_name' => $user_alias,
        //         'customer_no' => $customer_no,
        //         'debit_tel' => $customerTel,
        //         'account_no' => $account_no,
        //         'currency' => $currency,
        //         'currency_2' => $currCode,
        //         'amount' => $amount,
        //         'account_mandate' => $account_mandate,
        //         'CREDITACCOUNTNUMBER' => $credit_account_number,
        //         'narration' => $narration,
        //         'postedby' => $postBy,
        //         'transBy' => $transBy,
        //         'waitinglist' => 'not approved',
        //         'post_date' => $post_date,
        //         'documentRef' => $documentRef ,

        // ];

        // return $data ;

        // return false ;

        $query_result = DB::table('tb_corp_bank_req')->insert(
            [
                'request_type' => 'KORP',
                'request_status' => 'P',
                'beneficiary_name' => $receiver_name ,
                'BENEFICIARYADDRESS' => $receiver_address ,
                'user_id' => $userID,
                'user_name' => $user_alias,
                'customer_no' => $customer_no,
                'debit_tel' => $customerTel,
                'account_no' => $account_no,
                'currency' => $currency,
                'currency_2' => $currCode,
                'amount' => $amount,
                'account_mandate' => $account_mandate,
                'CREDITACCOUNTNUMBER' => $credit_account_number,
                'narration' => $narration,
                'postedby' => $postBy,
                'transBy' => $transBy,
                'waitinglist' => 'not approved',
                'post_date' => $post_date,
                'documentRef' => $documentRef ,
            ]
        );

        $amount = number_format($amount, 2);

        if($query_result) {
            return response()->json([
                'responseCode' => '000' ,
                'message' => "E-Korpor Transfer (From: $account_no ~ To $credit_account_number ~ Amount: $currency $amount) pending for approval" ,
                'data' => null
            ],200);
        }else {
            return response()->json([
                'responseCode' => '888',
                'message' => "Transfer ( From: $account_no ~ To: $credit_account_number ~ Amount: $currency $amount) pending for approval ",
                "data" => null
            ], 200);
        }




    }
}
