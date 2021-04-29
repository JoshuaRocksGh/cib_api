<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatementRequestGoForPendingController extends Controller
{
    public function statementRequestGoForPending(Request $request)
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


        /*
        $account_no = $request->account_no;
        $destinationAccountId = $request->destinationAccountId;
        $currency = $request->currency;
        $amount = $request->amount;
        $narration = $request->narration;
        $postBy = $request->postBy;
        $appBy = '';
        $customerTel = $request->telno;
        $transBy = $request->postBy;

        */

        $documentRef = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 2) . time());

        $user_id = $request->user_id;
        $user_alias = $request->user_alias;
        $customer_no = $request->customer_no;

        $customer_no = $customer_no;
        $user_name = $user_alias;
        $user_id = $user_id;
        $account_no = $request->statementAccount;
        $type = $request->type;
        $trans_start = $request->trans_start_;
        $trans_end = $request->trans_end_;
        $branch_code = $request->pickupBranch;

        $post_date = Carbon::now();
        $post_date = $post_date->toDateTimeString();

        $request_type = 'STR';
        $request_status = 'P';

        $query_result = DB::table('tb_corp_bank_req')->insert(
            [
                'request_type' => $request_type,
                'type' => $type,
                'request_status' => $request_status,
                'user_id' => $user_id,
                'user_name' => $user_name,
                'customer_no' => $customer_no,
                'account_no' => $account_no,
                // 'account_mandate' => $query_acc_mandate,
                'trans_start' => $trans_start,
                'trans_end' => $trans_end,
                'branch_code' => $branch_code,
                'waitinglist' => 'not approved',
                'post_date' => $post_date
            ]
        );



        if ($query_result) {
            return response()->json([
                'responseCode' => '000',
                'message' => "Statement request on: $account_no pending for approval.",
                "data" => null
            ], 200);
        } else {
            return response()->json([
                'responseCode' => '888',
                'message' => 'Statement request failed.',
                "data" => null
            ], 200);
        }
    }
}
