<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChequeBookRequestGoForPendingController extends Controller
{
    //
    public function ChequeRequestGoForPending(Request $request)
    {

        $validator  = Validator::make($request->all(), [
            'customer_no' => 'required',
            'account_mandate' => 'required',
            'user_id' => 'required',
            'user_name' => 'required',
            'account_no' => 'required',
            'branch_code' => 'required',
            'leaflet' => 'required',
            'postedBy' => 'required' ,
            'transBy' => 'required'


        ]);


        if ($validator->fails()) {
            return response()->json([
                'responseCode' => '422',
                'message' => 'Error in validation',
                'error' => $validator->errors(),
                'data' => null
            ], 200);


        }


        $request_type = 'CHQR';
        $request_status = 'P';
        $customer_no = $request->customer_no;
        $user_name = $request->user_name;
        $user_id = $request->user_id;
        $account_no = $request->account_no;
        $leaflet = $request->leaflet;
        $branch_code = $request->branch_code;
        $account_mandate = $request->account_mandate;
        $postedBy = $request->postedBy;
        $transBy = $request->transBy;

        $documentRef = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 2) . time());
        $post_date = Carbon::now();
        $post_date = $post_date->toDateTimeString();

        // $query = [
        //     'request_type' => $request_type,
        //         'request_status' => $request_status,
        //         'user_id' => $user_id,
        //         'user_name' => $user_name,
        //         'customer_no' => $customer_no,
        //         'account_no' => $account_no,
        //         'account_mandate' => $account_mandate,
        //         'leaflet' => $leaflet,
        //         'branch_code' => $branch_code,
        //         'waitinglist' => 'not approved',
        //         'post_date' => $post_date
        // ];

        $query_result = DB::table('tb_corp_bank_req')->insert(
            [
                'request_type' => $request_type,
                'request_status' => $request_status,
                'user_id' => $user_id,
                'user_name' => $user_name,
                'customer_no' => $customer_no,
                'account_no' => $account_no,
                'account_mandate' => $account_mandate,
                'leaflet' => $leaflet,
                'branch_code' => $branch_code,
                'waitinglist' => 'not approved',
                'post_date' => $post_date ,
                'documentRef' => $documentRef ,
                'postedby' => $postedBy,
                'transBy' => $transBy
            ]
        );


        if ($query_result) {
            return response()->json([
                'responseCode' => '000',
                'status' => 'Success' ,
                'message' => 'Cheque Book Request Pending Approval',
                'data' => null,
            ],200);
        } else {
            return response()->json([
                'responscode' => '999' ,
                'status' => 'Failed' ,
                'message' => 'Cheque Book Request Failed' ,
                'data' => null,

            ],200);
        }

    }
}
