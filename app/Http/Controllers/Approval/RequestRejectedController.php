<?php

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use App\Models\ApiGeneralCalls;
use App\Models\ApprovedFunc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequestRejectedController extends Controller
{
    //
    public function reject_approval(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "authToken" => 'required',
            "deviceIp" => 'required',
            "user_mandate" => 'required',
            "user_id" => 'required',
            "request_id" => 'required',
            "user_alias" => 'required',
            "customer_no" => 'required' ,
            "reject_narration" => 'required'
        ]);


        // if ($validator->fails()) {
        //     return response()->json([
        //         'responseCode' => '422',
        //         'message' => 'Error validation error',
        //         'error' => $validator->errors(),
        //         'data' => null
        //     ], 200);
        // }else {
        //     return response()->json([
        //         'responseCode' => '000',
        //         'message' => 'Validation Successful',
        //         // 'error' => $validator->errors(),
        //         'data' => null
        //     ], 200);
        // }


        $request_id = $request->request_id;
        $user_mandate = $request->user_mandate;
        $user_id = $request->user_id;
        $user_alias = $request->user_alias;
        $customer_no = $request->customer_no;
        $deviceIp = $request->deviceIp;
        $authToken = $request->authToken;
        $reject_narration = $request->reject_narration;

        $request_query = DB::table('tb_corp_bank_req')
        ->where('customer_no', $customer_no)
        ->where('request_id', $request_id)
        ->first();


        // return response()->json([
        //     'responseCode' => '422',
        //     'message' => "Database Query",
        //     'data' => $request_query
        // ], 200);

        if (is_null($request_query)) {
            return response()->json([
                'responseCode' => '422',
                'message' => "Customer ($customer_no) with request doest not exits",
                'data' => null
            ], 200);
        }

        if (trim($request_query->request_status) == "A") {
            return response()->json([
                'responseCode' => '422',
                'message' => "Request has already been Approved",
                'data' => null
            ], 200);
        }


        if (trim($request_query->request_status) == "R") {
            return response()->json([
                'responseCode' => '422',
                'message' => "Request has already been Rejected",
                'data' => null
            ], 200);
        }


        // return response()->json([
        //     'responseCode' => '422',
        //     'message' => "Database Query",
        //     'data' => $request_query
        // ], 200);

        $request_type_check = $request_query->request_type;

        $date_issued = trim($request_query->date_issued);
        $date_issued = strtotime($date_issued);
        $date_issued = date("d-M-y", $date_issued);


        $beneficiary_name = $request_query->beneficiary_name;
        // $amount = $request_query->amount;
        $cheque_to_No = $request_query->cheque_to;
        $cheque_from_No = $request_query->cheque_from;
        $branch_code = $request_query->branch_code;
        $leaflet = $request_query->leaflet;

        $start_date = trim($request_query->trans_start);
        $start_date = strtotime($start_date);
        $start_date = date("d-M-y", $start_date);

        $end_date = $request_query->trans_end;
        $end_date = strtotime($end_date);
        $end_date = date("d-M-y", $end_date);

        $request_id = trim($request_query->request_id);
        $request_type = trim($request_query->request_type);
        $account_no = trim($request_query->account_no);

        $type = trim($request_query->type);
        $comment_by_old = trim($request_query->comment_1_by);
        $comment_old = trim($request_query->comment_1);

        $comment_1 = trim($request_query->comment_1);
        $comment_by = $user_id;

        $user_alias = $user_alias;

        $user_panel = $user_mandate;
        $account_mandate = trim($request_query->account_mandate);
        $check_mandate = trim($request_query->check_mandate);

        // OTHER BANK TRANSFER
        $debitAccountNumber = $request_query->account_no;
        $creditAccountNumber = $request_query->creditaccountnumber;
        $bankCode = $request_query->bankcode;
        $bankName = $request_query->bank_name;
        $beneficiaryName =  $request_query->beneficiaryname;
        $amount = $request_query->amount;
        $currency =  $request_query->currency;
        $approvedBy = $user_id;
        $beneficiaryAddress = $request_query->beneficiaryaddress;
        $documentRef = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 2) . time();
        $postedBy = $user_id;
        $narration = $request_query->narration;
        $ex1 = $request_query->ex1;
        $ex2 = $request_query->ex2;
        $ex3 = $request_query->ex3;

        // OWN ACCOUNT
        $customerTel = $request_query->debit_tel;
        $postBy = $user_id;
        $approvedBy = $user_id;
        $transBy = $request_query->transby;
        $appBy = null;

        $user_id = $user_id;
        $approvers = $request_query->approvers;

        // BULK
        $channelCode  = 'NET';
        $debitCurrency = null;
        $debitNarration = $request_query->narration;
        $postedBy = $request_query->user_name;
        $approvedBy = $user_alias;
        $batch_no = $request_query->batch;

        //  STANDING ORDER
        $frequency = $request_query->frequency;
        $beneficiaryBankCode = $request_query->bankcode;

        // COMPLAINTS
        $service_type = $request_query->bankcode;

        // STOP STANDING ORDER
        $order_number = $request_query->order_number;

        // return [
        //     'start'=> $start_date,
        //     'end'=> $end_date,
        //     'account_no'=>$account_no,
        //     'type'=>$type,
        //     'user_id'=>$user_alias
        // ];



        if (strpos(strtoupper(trim($approvers)), strtoupper(trim($user_id))) !== false) {
            return response()->json([
                'responseCode' =>  '422',
                'status' => 'NOT_ALLOWED',
                'message' =>  "User ($user_id) has already approve this request",
                'data' => NULL
            ], 200);
        }


        if (empty($comment_by_old)) {
            $comment = $comment_by . '=> ' . $comment_1;
            $comment = $comment_by;
        } else {
            $comment = $comment_old . ':' . $comment_by . '=> ' . $comment_1;
            $comment_by = $comment_by_old . ':' . $comment_by;
        }

        # USER AND  ACCOUNT MANDATE CHECK
        $approve_func = new ApprovedFunc();


        $result = $approve_func->approved_req($request_type_check, $request_id, $user_panel, $account_mandate, $check_mandate, $comment, $comment_by, $user_id, $approvers);



        // return response()->json([
        //     'responseCode' => '422',
        //     'message' => "Query",
        //     'data' => $result
        // ], 200);


        // return $request_type_check;

        //  return response()->json([
        //     'data' => $result
        // ]);


        # CALL EXTERNAL API
        $api_request = new ApiGeneralCalls();

        // return response()->json([
        //     'responseCode' => '422',
        //     'message' => "Query",
        //     'data' => $api_request
        // ], 200);


        if ($result['responseCode'] != '000') {
            return $result ;
        }else {

            switch ($request_type_check) {
                case "SAB":

                    $req_result = $api_request->call_same_bank_transfer($request_id, $request_type_check, $result['check_mandate'], $comment, $comment_by, $debitAccountNumber, $creditAccountNumber, $amount, $documentRef, $narration, $postBy, $appBy, $customerTel, $transBy, $deviceIp, $currency, $authToken, $approvers);

                    return $req_result;
                    break;

                default:
                    echo "Request not found" ;
            }
        }

    }
}
