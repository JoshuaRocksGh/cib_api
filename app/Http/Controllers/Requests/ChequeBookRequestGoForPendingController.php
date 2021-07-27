<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            'user_alias' => 'required',
            'accountNumber' => 'required',
            'branch' => 'required',
            'deviceIP' => 'required',
            'entrySource' => 'required',
            'numberOfLeaves' => 'required',


        ]);

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => '422',
                'message' => 'Error in validation',
                'error' => $validator->errors(),
                'data' => null
            ], 200);
        }

        return $request;
    }
}