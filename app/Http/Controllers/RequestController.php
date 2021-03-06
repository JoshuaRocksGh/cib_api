<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    //
    public function all_approval_request(Request $request) {

        $customer_no = $request->query('customerNumber');
        $request_status = $request->query('requestStatus');


        $db_query = DB::table('tb_corp_bank_req')
                    ->where('request_status', $request_status )
                    ->where('customer_no', $customer_no)
                    ->orderByDesc('request_id')
                    ->get();

        return response()->json([
            'responseCode' => '000',
            'message' => "Transfer ( From: $customer_no  To: $request_status) pending for approval",
            "data" => $db_query
        ], 200);


    }

    public function get_detail_pending_request(Request $request) {

        $customer_no = $request->query('customer_no');
        $request_id = $request->query('request_id');


        $db_query = DB::table('tb_corp_bank_req')->where('request_id', $request_id )->where('customer_no', $customer_no)->first();

        return response()->json([
            'responseCode' => '000',
            'message' => "Transfer ( From: $customer_no  To: $request_id) pending for approval",
            "data" => $db_query
        ], 200);


    }

}
