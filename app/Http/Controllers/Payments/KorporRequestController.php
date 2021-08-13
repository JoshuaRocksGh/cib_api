<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KorporRequestController extends Controller
{
    //

    public function send_korpor_request(Request $request) {

        $validator = Validator::make($request->all(), [

            'user_mandate' => 'required',
            'account_mandate' => 'required',
            'postBy' => 'required',
            'destinationAccountId' => 'required',
            'account_no' => 'required',
            'customer_no' => 'required',
            'user_alias' => 'required',
            'currency' => 'required',
            'amount' => 'required',
            'receiver_address' => 'required',
            'receiver_name' => 'required',


        ]);

        return response()->json([
            "responseCode" => "000",
            "meaasge" => "Api Response",
            "data" => $request
        ],200);

        return false ;

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => '422',
                'message' => 'Error validation error',
                'error' => $validator->errors(),
                'data' => null
            ], 200);
        }



    }
}
