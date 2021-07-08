<?php

namespace App\Http\Controllers\Transfers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RtgsGoForPendingController extends Controller
{
    public function RtgsGoForPending(Request $request)
    {



        $validator = Validator::make($request->all(), [
            'customer_no' => 'required',
            'account_mandate' => 'required',
            'user_id' => 'required',
            'user_alias' => 'required',
            'account_no' => 'required',
            'currency' => 'required',
            'bank_code' => 'required',
            'bank_name' => 'required',
            'bene_account' => 'required',
            'bene_name' => 'required',
            'bene_address' => 'required',
            'amount' => 'required',
            'bene_tel' => 'required',
            'narration' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => '422',
                'message' => 'Error validation error',
                'error' => $validator->errors(),
                'data' => null
            ], 200);
        }
        // return $request;

        $user_id = $request->user_id;
        $user_alias = $request->user_alias;
        $customer_no = $request->customer_no;
        $account_mandate = $request->account_mandate;
        $debitAccountNumber = $request->account_no;
        $creditAccountNumber = $request->bene_account;
        $bankCode = substr($request->bene_account, 0, 3);
        $bankName =  strtoupper($request->bene_name);
        $beneficiaryName = $request->bene_name;
        $amount = $request->amount;
        $currency = $request->currency;
        $approvedBy = null;
        $beneficiaryAddress = $request->bene_address;
        $documentRef = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 2) . time());
        $postedBy = $user_id;
        $narration = $request->narration;
        $ex1 = '';
        $ex2 = '';
        $ex3 = '';
        $user_id = $user_id;

        // PLEASE SEND IT TO APPROVAL

        $query_result = DB::table('tb_corp_bank_req')->insert(
            [
                'request_type' => 'RTGS',
                'request_status' => 'P',
                'account_no'  => $debitAccountNumber,
                'creditAccountNumber' => $creditAccountNumber,
                'bankCode' => $bankCode,
                'bank_name' => $bankName,
                'beneficiaryName' => $beneficiaryName,
                'user_id' => $user_id,
                'amount' => $amount,
                'currency' => $currency,
                'beneficiaryAddress' => $beneficiaryAddress,
                'documentRef' => $documentRef,
                'account_mandate' => $account_mandate,
                'postedBy' => $postedBy,
                'narration' => $narration,
                'ex1' => $ex1,
                'ex2' => $ex2,
                'ex3' => $ex3,
                'waitinglist' => 'not approved',
                'user_name' => $user_alias,
                'customer_no' => $customer_no,
                'account_mandate' => $account_mandate,

            ]
        );

        if ($query_result) {
            return [
                'responseCode' => '000',
                'status' => 'success',
                'message' => 'Transfer to other local bank pending approval',
                'data' => null
            ];
        } else {
            return [
                'responseCode' => '999',
                'status' => 'failed',
                'message' => 'Unsuccessful transfer',
                'data' => null
            ];
        }

    }
}
