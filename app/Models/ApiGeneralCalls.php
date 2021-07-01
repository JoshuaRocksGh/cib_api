<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ApiGeneralCalls extends Model
{
    

    public function call_own_account_transfer($request_id, $request_type_check, $check_mandate, $comment, $comment_by, $accountId, $destinationAccountId, $amount, $documentRef, $narration, $postBy, $appBy, $customerTel, $transBy, $deviceIp, $currency, $authToken)
    {

        return response()->json([
            'data' => [
                    'accountId' => $accountId,
                    'destinationAccountId' => $destinationAccountId,
                    'amount' => $amount,
                    'documentRef' => $documentRef,
                    'narration' => $narration,
                    'postBy' => $postBy,
                    'appBy' => $appBy,
                    'customerTel' => $customerTel,
                    'transBy' => $transBy
                ]
        ]);

        $documentRef = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 2) . time();
       

        $api_response = new ApiBaseResponse();

        $data = [

            "amount" => $amount,
            "authToken" => $authToken,
            "creditAccount" => $destinationAccountId,
            "currency" => $currency,
            "debitAccount" => $accountId,
            "deviceIp" => $deviceIp,
            "entrySource" => 'C',
            "narration" => $narration,
            "secPin" => '1234',
            "userName" => $postBy,
           // "category" => null,

        ];


        try {

            $response = Http::post(env('API_BASE_URL') . "/user/login", $data);

            if ($response->ok()) { // API response status code is 200

                $result = json_decode($response->body());


                if ($result->responseCode == '000') { // API responseCode is 000

                    $result_data = $result->data;

                    // return (string) json_decode($result_data);

                    // CHECK FOR USER TYPE PERSONAL OR CORPORATE
                    /*
                    if ($result_data->c_type == 'C') {
                        return  $base_response->api_response('900', 'This is a corporate user not allowed here',  NULL);
                    }
                    */

                    $user_detail = $result->data;
                    $customerType = $user_detail->customerType;

                    // if ($customerType != "I") {
                    //     return  $base_response->api_response("422", "Corporate users not allowed",  null);
                    // }


                    session([
                        "userId" => $user_detail->userId,
                        "userAlias" => $user_detail->userAlias,
                        "updateFlag" => $user_detail->updateFlag,
                        "setPin" => $user_detail->setPin,
                        "changePassword" => $user_detail->changePassword,
                        "email" => $user_detail->email,
                        "firstTimeLogin" => $user_detail->firstTimeLogin,
                        "userToken" => $user_detail->userToken,
                        "customerNumber" => $user_detail->customerNumber,
                        "customerPhone" => $user_detail->customerPhone,
                        "updateUrl" => $user_detail->updateUrl,
                        // "c_type" => $user_detail->c_type,
                        "lastLogin" => $user_detail->lastLogin,
                        "customerType" => $user_detail->customerType,
                        "checkerMaker" => $user_detail->checkerMaker,
                        "checkerMaker" => 'M',
                        "userMandate" => 'A' ,
                        "headers"=>[
                            "x-api-key"=> "123",
                            "x-api-secret"=> "123",
                            "x-api-source"=> "123",
                            "x-api-token"=> "123"
                            ]

                    ]);

                    $api_headers = [

                    ];
                    // return session();
                    // return session()->get('customerPhone');

                    $authToken = session()->get('userToken');

                    $userID = session()->get('userId');
                    // return $authToken;
                    // return session();

                    // return redirect()->route('home');

                    // return $result_data->user_id;


                    /*

                    // return $result_data->user_id;
                    try {
                        $id = DB::table('users')->insert([
                            'email' => $result_data->email,
                            'user_id' => $result_data->user_id,
                            'customer_no' => $result_data->customer_no,
                            'f_login' => $result_data->f_login,
                            'c_type' => $result_data->c_type,
                        ]);
                        // dd($id);
                    } catch (\Exception $th) {
                        //  return $th->getMessage();
                         DB::table('tb_error_logs')->insert([
                            'platform' => 'ONLINE_INTERNET_BANKING',
                            'user_id' => 'AUTH',
                            'message' => (string) $th->getMessage()
                        ]);

                         return $th->getMessage();
                    }

               */

              return  $base_response->api_response($result->responseCode, $result->message,  $result->data);
                    return  $base_response->api_response($result->responseCode, $result->message,  $result->data); // return API BASERESPONSE

                } else {  // API responseCode is not 000

                    return $base_response->api_response($result->responseCode, $result->message,  $result->data); // return API BASERESPONSE

                }
            } else { // API response status code not 200

                DB::table('tb_error_logs')->insert([
                    'platform' => 'ONLINE_INTERNET_BANKING',
                    'user_id' => 'AUTH',
                    'code' => $response->status(),
                    'message' => $response->body()
                ]);

                return $base_response->api_response('500', 'API SERVER ERROR',  NULL); // return API BASERESPONSE

            }
        } catch (\Exception $e) {

            DB::table('tb_error_logs')->insert([
                'platform' => 'ONLINE_INTERNET_BANKING',
                'user_id' => 'AUTH',
                'message' => (string) $e->getMessage()
            ]);

            return $base_response->api_response('500', 'CONNECTION SERVER ERROR',  NULL); // return API BASERESPONSE



        }

        

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('API_URL') .  "/account/$accountId/transfer",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "accountId=$accountId&destinationAccountId=$destinationAccountId&amount=$amount&documentRef=$documentRef&narration=$narration&postBy=$postBy&appBy=$appBy&customerTel=$customerTel&transBy=$transBy",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "x-api-key: " . env('X_API_KEY'),
                "x-api-secret: " . env('X_API_SECRET')
            ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);


        if ($err) {
            return json_encode([
                'message' => "cURL Error #:" . $err,
                'responseCode' => '404',
            ]);
        } else {

            // $response = json_decode($response);
            // return $response;

            $result = json_decode($response);

            $res_date = Carbon::now();
            $res_date = $res_date->toDateTimeString();

            if ($result->responseCode == '000' || $result->responseCode == '200') {

                $approve_req = DB::table('tb_corp_bank_req')->where('request_id', $request_id)->update(['check_mandate' => $check_mandate, 'request_status' => 'A', 'waitinglist' => 'approved', 'comment_1' => $comment, 'DOCUMENTREF' => $documentRef, 'comment_1_by' => $comment_by, 'res_message' => $result->message, 'res_date' => $res_date]);

                $request = Auth::user()->user_alias . ' => ' . 'After approval received this response: => ' . $result->message;

                $this->request_logs($request, $request_type_check, $result->message);


                return [
                    'responseCode' =>  '000',
                    'status' => 'approved',
                    'message' =>  $result->message
                ];
            } else {
                return [
                    'responseCode' =>  '666',
                    'status' => 'did not work',
                    'message' =>  $result->message
                ];
            }
        }
    }





    public function call_third_party_transfer($request_id, $request_type_check, $check_mandate, $comment, $comment_by, $accountId, $destinationAccountId, $amount, $documentRef, $narration, $postBy, $appBy, $customerTel, $transBy)
    {

        $documentRef = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 2) . time();
        // return [
        //     $accountId,
        //     $destinationAccountId,
        //     $amount,
        //     $documentRef,
        //     $narration,
        //     $postBy,
        //     $appBy,
        //     $customerTel,
        //     $transBy
        // ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('API_URL') .  "/account/$accountId/transfer",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "accountId=$accountId&destinationAccountId=$destinationAccountId&amount=$amount&documentRef=$documentRef&narration=$narration&postBy=$postBy&appBy=$appBy&customerTel=$customerTel&transBy=$transBy",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "x-api-key: " . env('X_API_KEY'),
                "x-api-secret: " . env('X_API_SECRET')
            ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);


        if ($err) {
            return json_encode([
                'message' => "cURL Error #:" . $err,
                'responseCode' => '404',
            ]);
        } else {

            // $response = json_decode($response);
            // return $response;

            $result = json_decode($response);

            $res_date = Carbon\Carbon::now();
            $res_date = $res_date->toDateTimeString();

            if ($result->responseCode == '000' || $result->responseCode == '200') {

                $approve_req = DB::table('tb_corp_bank_req')->where('request_id', $request_id)->update(['check_mandate' => $check_mandate, 'request_status' => 'A', 'waitinglist' => 'approved', 'comment_1' => $comment, 'DOCUMENTREF' => $documentRef, 'comment_1_by' => $comment_by, 'res_message' => $result->message, 'res_date' => $res_date]);

                $request =  Auth::user()->user_alias . ' => ' . 'After approval received this response: => ' . $result->message;

                $this->request_logs($request, $request_type_check, $result->message);


                return [
                    'responseCode' =>  '000',
                    'status' => 'approved',
                    'message' =>  $result->message
                ];
            } else {
                return [
                    'responseCode' =>  '666',
                    'status' => 'did not work',
                    'message' =>  $result->message
                ];
            }
        }
    }





    public function call_account($accountId)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('API_URL') .  "/account/$accountId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "accountId=$accountId",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "x-api-key: " . env('X_API_KEY'),
                "x-api-secret: " . env('X_API_SECRET')
            ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);


        if ($err) {
            return json_encode([
                'message' => "cURL Error #:" . $err,
                'responseCode' => '404',
            ]);
        } else {

            $result = json_decode($response);
            // return $response;


            if (isset($result->code) != '000') {

                return [
                    'responseCode' =>  '000',
                    'message' =>  'Account Verified',
                    'data' => $result
                ];
            } else {
                // return $response;

                return [
                    'responseCode' =>  $result->code,
                    'message' => $result->message
                ];
            }
        }
    }






    public function call_other_bank_transfer($request_id, $request_type_check, $check_mandate, $comment, $comment_by, $debitAccountNumber, $creditAccountNumber, $bankCode, $amount, $narration, $documentRef, $postedBy, $approvedBy, $beneficiaryName, $beneficiaryAddress, $ex1, $ex2, $ex3)
    {

        $documentRef = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 2) . time();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('API_URL') . "/account/transfer/other-bank",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "debitAccountNumber=$debitAccountNumber&creditAccountNumber=$creditAccountNumber&bankCode=$bankCode&amount=$amount&narration=$narration&documentRef=$documentRef&postedBy=$postedBy&approvedBy=$approvedBy&beneficiaryName=$beneficiaryName&beneficiaryAddress=$beneficiaryAddress&ex1=$ex1&ex3=$ex2&ex3=$ex3",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "x-api-key: " . env('X_API_KEY'),
                "x-api-secret: " . env('X_API_SECRET')
            ),
        ));
        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);


        if ($err) {
            return json_encode([
                'message' => "cURL Error #:" . $err,
                'responseCode' => '404',
            ]);
        } else {

            // $response = json_decode($response);
            // return $response;

            $result = json_decode($response);

            $res_date = Carbon\Carbon::now();
            $res_date = $res_date->toDateTimeString();

            if ($result->responseCode == '000' || $result->responseCode == '200') {

                $approve_req = DB::table('tb_corp_bank_req')->where('request_id', $request_id)->update(['check_mandate' => $check_mandate, 'request_status' => 'A', 'waitinglist' => 'approved', 'comment_1' => $comment, 'DOCUMENTREF' => $documentRef, 'comment_1_by' => $comment_by, 'res_message' => $result->message, 'res_date' => $res_date]);

                $request = Auth::user()->user_alias . ' => ' . 'After approval received this response: => ' . $result->message;

                $this->request_logs($request, $request_type_check, $result->message);


                return [
                    'responseCode' =>  '000',
                    'status' => 'approved',
                    'message' =>  $result->message
                ];
            } else {
                return [
                    'responseCode' =>  '666',
                    'status' => 'did not work',
                    'message' =>  $result->message
                ];
            }
        }
    }



    public function call_Bulk_($request_id, $request_type_check, $check_mandate, $comment, $comment_by, $debitAccountNumber, $bankcode, $channelCode, $debitCurrency, $debitNarration, $postedBy, $approvedBy)
    {
        $batch_no = Session::get('batch_no');

        $creditAccounts = DB::table('tb_corp_bank_import_excel')->where(['account_no' => $debitAccountNumber,  'batch_no' => $batch_no])->get();

        $creditAccountData = array();
        $total_amt = 0;
        foreach ($creditAccounts as $creditAccount) {

            $total_amt = $total_amt + (float) $creditAccount->amount;

            $creditAccountData[] = [
                'creditAmount' => (float) $creditAccount->amount,
                'creditAccount' => $creditAccount->bban,
                'creditCurrency' => null,
                'creditNarration' => $creditAccount->trans_desc,
            ];
        }

        // return $total_amt;

        $debitAccountData = array();
        // $debitAccountData[] = [
        //     "debitAccount" => $debitAccountNumber,
        //     "debitAmount" =>  $total_amt,
        //     "debitCurrency" => $debitCurrency,
        //     "debitNarration" => $debitNarration,
        //     "department" => null,
        //     'referenceNo' => $batch_no,
        //     'transType' => "INTB",
        //     'postedBy' => $postedBy,
        //     'unit' => null

        // ];
        //  return $creditAccountData;

        $data = [
            'approvedBy' => $approvedBy,
            'channelCode' => $channelCode,
            'creditAccounts' => $creditAccountData,
            "debitAccount" => $debitAccountNumber,
            "bankType" => $bankcode,
            "debitAmount" =>  $total_amt,
            "debitCurrency" => $debitCurrency,
            "debitNarration" => $debitNarration,
            "department" => null,
            'referenceNo' => $batch_no,
            'transType' => "INTB",
            'postedBy' => $postedBy,
            'unit' => null
        ];
        // return $data;
        $data = json_encode($data);
        //    return $data;

        $documentRef = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 2) . time();
        // $creditAccountNumber =array();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('API_URL') . "/account/performBulkCredit",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "x-api-key: " . env('X_API_KEY'),
                "x-api-secret: " . env('X_API_SECRET'),
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        // return $response;


        if ($err) {
            return [
                'message' => "cURL Error #:" . $err,
                'responseCode' => '404',
            ];
        } else {

            // $response = json_decode($response);
            // return $response;

            $result = json_decode($response);

            $res_date = Carbon\Carbon::now();
            $res_date = $res_date->toDateTimeString();
            //  return $result->responseCode;

            if ($result->responseCode == '000' || $result->responseCode == '200') {

                $approve_req = DB::table('tb_corp_bank_req')->where('request_id', $request_id)->update(['check_mandate' => $check_mandate, 'request_status' => 'A', 'waitinglist' => 'approved', 'comment_1' => $comment, 'DOCUMENTREF' => $documentRef, 'comment_1_by' => $comment_by, 'res_message' => $result->message, 'res_date' => $res_date]);

                $request = Auth::user()->user_alias . ' => ' . 'After approval received this response: => ' . $result->message;

                $this->request_logs($request, $request_type_check, $result->message);

                $flag_status_A = DB::table('tb_corp_bank_import_excel')->where(['account_no' => $debitAccountNumber,  'batch_no' => $batch_no])->update(['status' => 'A']);

                return [
                    'responseCode' =>  '000',
                    'status' => 'approved',
                    'message' =>  $result->message
                ];
            } else {
                return [
                    'responseCode' =>  '666',
                    'status' => 'did not work',
                    'message' =>  $result->message
                ];
            }
        }
    }


    public function call_standing_orders($accountId)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL =>  env('API_URL') . "/request/$accountId/standing-orders",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "{\n\t\"accountId\": $accountId\n}",
            CURLOPT_HTTPHEADER => array(
                "x-api-key: " . env('X_API_KEY'),
                "x-api-secret: " . env('X_API_SECRET'),
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);



        if ($err) {
            return json_encode([
                'message' => "cURL Error #:" . $err,
                'responseCode' => '404',
            ]);
        } else {

            $result = json_decode($response);
            // return $response;


            if (isset($result->responseCode) == '000') {

                if (empty($result->data->orders)) {
                    return [
                        'responseCode' =>  '210',
                        'message' =>  'No orders for ' . $result->data->accountId,
                        'data' => $result->data->orders
                    ];
                }

                return [
                    'responseCode' =>  '000',
                    'message' =>  'Orders fetched successfullly',
                    'data' => $result->data->orders
                ];
            } else {
                // return $response;

                return [
                    'responseCode' =>  $result->code,
                    'message' => $result->message
                ];
            }
        }
    }






    public function call_cancel_standing_orders($request_id, $request_type_check, $check_mandate, $comment, $comment_by, $debitAccountNumber, $orderNumber, $postedBy)
    {
        // return [
        //     "data" => [
        //         $orderNumber,
        //         $postedBy
        //     ]
        // ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL =>  env('API_URL') . "/request/$orderNumber/standing-order/cancel",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "orderNumber=$orderNumber&postedBy=$postedBy",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "x-api-key: " . env('X_API_KEY'),
                "x-api-secret: " . env('X_API_SECRET'),
            ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);



        if ($err) {
            return json_encode([
                'message' => "cURL Error #:" . $err,
                'responseCode' => '404',
            ]);
        } else {

            $result = json_decode($response);
            // return $response;

            if (empty($result)) {
                return [
                    'responseCode' =>  '210',
                    'message' =>  'No response from API Server',
                    'data' => ''
                ];
            }


            if (isset($result->responseCode) == '000') {

                return [
                    'responseCode' =>  '000',
                    'message' =>  'Stop Standing Order successful',
                    'data' => $result->data
                ];
            } else {
                // return $response;

                return [
                    'responseCode' =>  '666',
                    'message' => 'Failed to Stop Standing Order'
                ];
            }
        }
    }





    public function request_logs($request, $type, $res_message)
    {
        $user_id =  Auth::user()->user_id;
        DB::table('tb_corp_bank_req_logs')->insert(
            ['user_id' => $user_id, 'request_type' => $type, 'request' => $request, 'res_message' => $res_message]
        );
    }
}
