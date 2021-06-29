<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalRequestController extends Controller
{
    //
    public function approve_request(Request $request){

        $request_id = $request->request_id;
        $customer_no = $request->customer_no;


        $db_query = DB::table('tb_corp_bank_req')->where('request_id', $request_id)->where('customer_no', $customer_no)->get();

        return response()->json([
            'responseCode' => '000',
            'message' => "Pending Request Details Successful",
            "data" => $db_query
        ], 200);
    }
}
