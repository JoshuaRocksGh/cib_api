<?php

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use App\Models\ApiGeneralCalls;
use App\Models\ApprovedFunc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RequestApprovalController extends Controller
{
    public function request_approval(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_mandate' => 'required',
            'user_id' => 'required',
            'request_id' => 'required',
            'customer_no' => 'required',
            'user_alias' => 'required',
            'deviceIp' => 'required',
            'authToken' => 'required',
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

        $request_id = $request->request_id;
        $user_mandate = $request->user_mandate;
        $user_id = $request->user_id;
        $user_alias = $request->user_alias;
        $customer_no = $request->customer_no;
        $deviceIp = $request->customer_no;
        $authToken = $request->authToken;

        $request_query = DB::table('tb_corp_bank_req')
                        ->where('customer_no', $customer_no)
                        ->where('request_id', $request_id)
                        ->first();

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
        //     'data' => $request_query
        // ]);


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
        $postBy = $request_query->postedby;
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



        if(strpos(strtoupper(trim($approvers)), strtoupper(trim($user_id))) !== false){
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



        $result = $approve_func->approved_req($request_type_check, $request_id, $user_panel, $account_mandate, $check_mandate, $comment, $comment_by,$user_id, $approvers);



        // return $request_type_check;

        //  return response()->json([
        //     'data' => $result
        // ]);

        # CALL EXTERNAL API
        $api_request = new ApiGeneralCalls();

        if ($result['responseCode'] != '000') {
            return $result;
        } else {


            switch ($request_type_check) {

                    // THIRD PARTY TRANSFER
                case "TPT":
                    // return 'TPT';

                    $req_result = $api_request->call_same_bank_transfer($request_id, $request_type_check, $result['check_mandate'], $comment, $comment_by, $debitAccountNumber, $creditAccountNumber, $amount, $documentRef, $narration, $postBy, $appBy, $customerTel, $transBy, $deviceIp, $currency, $authToken, $approvers);
                    // $req_result = $api_request->statement_req($request_id, $request_type_check, $result['check_mandate'], $comment, $comment_by, $account_no, $start_date, $end_date, $type, $user_alias);
                    return $req_result;
                    break;

                    // OWN BANK TRANSFER
                case "OWN":
                    // return 'OBT';

                    $check_mandate_i = $result['check_mandate'];

                    $req_result = $api_request->call_own_account_transfer($request_id, $request_type_check, $result['check_mandate'], $comment, $comment_by, $debitAccountNumber, $creditAccountNumber, $amount, $documentRef, $narration, $postBy, $appBy, $customerTel, $transBy, $deviceIp, $currency, $authToken, $approvers);


                    // $req_result = $api_request->statement_req($request_id, $request_type_check, $result['check_mandate'], $comment, $comment_by, $account_no, $start_date, $end_date, $type, $user_alias);
                    return $req_result;
                    break;

                    // OTHER BANK TRANSFER
                case "OTBT":
                    // return 'OTBT';

                    $req_result = $api_request->call_other_bank_transfer($request_id, $request_type_check, $result['check_mandate'], $comment, $comment_by, $debitAccountNumber, $creditAccountNumber, $bankCode, $amount, $narration, $documentRef, $postedBy, $approvedBy, $beneficiaryName, $beneficiaryAddress, $ex1, $ex2, $ex3);
                    // $req_result = $api_request->statement_req($request_id, $request_type_check, $result['check_mandate'], $comment, $comment_by, $account_no, $start_date, $end_date, $type, $user_alias);
                    return $req_result;
                    break;

                    // STATEMENT REQUEST
                case "STR":
                    // return 'STR -> ' . $result['check_mandate'];

                    $req_result = $api_request->statement_req($request_id, $request_type_check, $result['check_mandate'], $comment, $comment_by, $account_no, $start_date, $end_date, $type, $user_alias);
                    return $req_result;
                    break;

                    // CHEQUEBOOK REQUEST
                case "CHQR":
                    // return 'CHQR -> ' . $result['check_mandate'];

                    $req_result = $api_request->cheque_book($request_id, $type,  $result['check_mandate'], $comment, $comment_by, $account_no, $branch_code, $leaflet, $user_alias);
                    return $req_result;
                    break;

                    // STOP CHQUEBOOK REQUEST
                case "STPR":
                    // return 'STPR -> ' . $result['check_mandate'];

                    $req_result = $api_request->stop_cheque($request_id, $type, $result['check_mandate'], $comment, $comment_by, $account_no, $cheque_from_No, $cheque_to_No, $date_issued, $beneficiary_name, $amount);

                    return $req_result;
                    break;

                    // STOP STANDING REQUEST
                case "STST":
                    // return 'STPR -> ' . $result['check_mandate'];

                    $req_result = $api_request->call_cancel_standing_orders($request_id, $request_type,  $result['check_mandate'], $comment, $comment_by, $debitAccountNumber, $order_number, $postedBy);
                    // $req_result = $api_request->stop_cheque($result['check_mandate'], $account_no, $cheque_from_No, $cheque_to_No, $date_issued, $beneficiary_name, $amount);
                    return $req_result;
                    break;

                    // COMPLAINTS
                case "COMPL":
                    // return 'COMPL -> ' . $result['check_mandate'];

                    $req_result = $api_request->complaints($request_id, $type,  $result['check_mandate'], $comment, $comment_by, $debitAccountNumber, $service_type, $narration, '', '');

                    return $req_result;
                    break;

                    //StANDING ORDER
                case "SO":
                    // return 'SO -> ' . $result['check_mandate'];

                    return $req_result = $api_request->standing_order($request_id, $type,  $result['check_mandate'], $comment, $comment_by, $debitAccountNumber, $creditAccountNumber, $start_date, $end_date, $frequency, $amount, $narration, $beneficiaryBankCode, $postBy, $channelCode);
                    // return $req_result;
                    break;

                    //
                case "RTGS":
                    // return 'RTGS -> ' . $result['check_mandate'];

                    $req_result = $api_request->rtgs($request_id, $type,  $result['check_mandate'], $comment, $comment_by, $debitAccountNumber, $creditAccountNumber, $beneficiaryName, $beneficiaryAddress, $beneficiaryBankCode, $narration, $amount);

                    return $req_result;
                    break;
                case "DTRA":
                    // return 'OTBT';

                    $req_result = $api_request->call_other_bank_transfer($request_id, $request_type_check, $result['check_mandate'], $comment, $comment_by, $debitAccountNumber, $creditAccountNumber, $bankCode, $amount, $narration, $documentRef, $postedBy, $approvedBy, $beneficiaryName, $beneficiaryAddress, $ex1, $ex2, $ex3);

                    return $req_result;

                    break;
                case "SLF":
                    return 'SLF -> ' . $result['check_mandate'];
                    // $api_request = new ApiRequestFunction();
                    // return $req_result = $api_request->rtsg($request_id, $type,  $result['check_mandate'], $comment, $comment_by, $debitAccountNumber, $creditAccountNumber, $beneficiaryName, $beneficiaryAddress, $beneficiaryBankCode, $narration, $amount);
                    // return $req_result;
                    break;
                case "BULK":
                    // return 'BULK -> ' . $result['check_mandate'];
                    Session::put('batch_no', $batch_no);


                    return $req_result = $api_request->call_Bulk_($request_id, $request_type_check, $check_mandate, $comment, $comment_by, $debitAccountNumber, $bankCode, $channelCode, $debitCurrency, $debitNarration, $postedBy, $approvedBy);

                    // $req_result = $api_request->stop_cheque($result['check_mandate'], $account_no, $cheque_from_No, $cheque_to_No, $date_issued, $beneficiary_name, $amount );
                    // return $req_result;
                    break;

                default:
                    echo "Request not found";
            }
        }




    }
}
