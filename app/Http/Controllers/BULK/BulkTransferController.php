<?php

namespace App\Http\Controllers\BULK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkTransferController extends Controller
{
    public function get_bulk_upload_list(Request $request)
    {

        $customerNumber = $request->query("customer_no");
        // return $customer_no;

        $files = DB::table('tb_corp_bank_import_excel')
            ->distinct()
            ->where('user_id', $customerNumber)
            ->where('status', 'P')
            ->orderBy('batch_no', 'desc')
            ->get(['batch_no', 'account_no', 'bank_code', 'status', 'ref_no', 'total_amount', 'value_date']);

        return response()->json([
            'responseCode' => '000',
            'message' => "Available Uploads",
            'data' => $files
        ], 200);
    }

    public function get_bulk_upload_detail_list_api(Request $request)
    {
        $batch_no = $request->query('batch_no');
        $account_no = $request->query('account_no');
        // $bank_type = $request->query('bank_type');

        $bulk_details = DB::table('tb_corp_bank_import_excel')->where('batch_no', $batch_no)->get();
        $bulk_info = DB::table('TB_CORP_BANK_BULK_REF')->where('batch_no', $batch_no)->first();


        return response()->json([
            'responseCode' => '000',
            'message' => "Detail of upload transfer",
            'data' => [
                'bulk_info' => $bulk_info,
                'bulk_details' => $bulk_details
            ]
        ], 200);
    }

    public function post_bulk_upload_detail_list_api(Request $request)
    {

        $bulk_data = $request->post();

        // return $bulk_data;

        // return response()->json([
        //     'responseCode' => '000',
        //     'message' => "API Test",
        //     'data' => $bulk_data
        // ], 200);

        $document_ref = (string) time();



        foreach ($bulk_data as $bulk) {
            $b = (object) $bulk;
            // return $b->BBAN;


            // $this->save_bulk($b->BBAN, $b->NAME, $b->TOTAL_AMOUNT, $b->AMOUNT, $b->TRANS_DESC, $b->USER_ID, $b->ACCOUNT_NO, $b->REF_NO, $b->BATCH_NO, $b->CUSTOMER_NO);

            DB::table('tb_corp_bank_import_excel')
                ->insert([
                    'bban' => $b->BBAN,
                    'name' => $b->NAME,
                    'total_amount' => $b->TOTAL_AMOUNT,
                    'amount' => $b->AMOUNT,
                    'trans_desc' => $b->TRANS_DESC,
                    'user_id' => $b->USER_ID,
                    'account_no' => $b->ACCOUNT_NO,
                    'batch_no' => $b->BATCH_NO,
                    'customer_no' => $b->CUSTOMER_NO,
                    'ref_no' => $b->REF_NO,
                    'status' => 'P',
                    'bank_code' => 'SAB',
                    'created_at' => NOW(),
                    'updated_at' => NOW(),

                ]);

            DB::commit();
        }

        $bolt = $bulk_data[0];
        $bulk_info = (object) $bolt;

        // return response()->json([
        //     'responseCode' => '000',
        //     'message' => "View data being inserted into toad",
        //     'data' => $bulk_info
        // ], 200);

        // $this->save_bulk_request($bulk_data, $bulk_info->USER_ID, $bulk_info->USER_ID, $bulk_info->CUSTOMER_NO, $bulk_info->ACCOUNT_MANDATE, $document_ref, $bulk_info->BATCH_NO, $bulk_info->ACCOUNT_NO, $bulk_info->REF_NO, $bulk_info->BATCH_NO, $bulk_info->TOTAL_AMOUNT, $bulk_info->CURRENCY);

        DB::table('tb_corp_bank_req')
            ->insert([
                'request_type' => 'BULK',
                'request_status' => 'P',
                'postedby' => $bulk_info->USER_ID,
                'user_id' => $bulk_info->USER_ID,
                // 'user_name' => $username,
                'customer_no' => $bulk_info->CUSTOMER_NO,
                'account_no' => $bulk_info->ACCOUNT_NO,
                'account_mandate' => $bulk_info->ACCOUNT_MANDATE,
                'documentref' => $document_ref,
                'batch' => $bulk_info->BATCH_NO,
                'ref_no' => $bulk_info->REF_NO,
                'total_amount' => $bulk_info->TOTAL_AMOUNT,
                'currency' => $bulk_info->CURRENCY,
                'narration' => 'Bulk transfer',
            ]);



        $bolt = $bulk_data[0];
        $bulk_info = (object) $bolt;

        DB::table('TB_CORP_BANK_BULK_REF')
            ->insert([
                'ref_no' => $bulk_info->REF_NO,
                'description' => $bulk_info->TRANS_DESC,
                'user_id' => $bulk_info->USER_ID,
                'total_amount' => $bulk_info->TOTAL_AMOUNT,
                'batch_no' => $bulk_info->BATCH_NO,
                'account_no' => $bulk_info->ACCOUNT_NO,
                'account_mandate' => $bulk_info->ACCOUNT_MANDATE,
                // 'customer_no' => $bulk_info->CUSTOMER_NO
            ]);


        DB::commit();


        return response()->json([
            'responseCode' => '000',
            'message' => "Detail of upload transfer",
            'data' => NULL
        ], 200);
    }

    // public function save_bulk($bban, $name, $total_amount, $amount, $trans_desc, $user_id, $account_no, $ref_no, $batch_no, $customer_no)
    // {


    //     DB::table('tb_corp_bank_import_excel')
    //         ->insert([
    //             'bban' => $bban,
    //             'name' => $name,
    //             'total_amount' => $total_amount,
    //             'amount' => $amount,
    //             'trans_desc' => $trans_desc,
    //             'user_id' => $user_id,
    //             'customer_no' => $customer_no,
    //             'account_no' => $account_no,
    //             'ref_no' => $ref_no,
    //             'batch_no' => $batch_no,
    //             'status' => 'P',
    //             'bank_code' => 'SAB',
    //             'created_at' => NOW(),
    //             'updated_at' => NOW(),
    //         ]);

    //     DB::commit();
    // }

    // public function save_bulk_request($bulk_data, $user_id, $username, $customer_no, $account_mandate, $document_ref, $batch_no, $account_no, $ref_no, $total_amount, $currency)
    // {


    //     DB::table('tb_corp_bank_req')
    //         ->insert([
    //             'request_type' => 'BULK',
    //             'request_status' => 'P',
    //             'postedby' => $user_id,
    //             'user_id' => $user_id,
    //             'user_name' => $username,
    //             'customer_no' => $customer_no,
    //             'account_no' => $account_no,
    //             'account_mandate' => $account_mandate,
    //             'documentref' => $document_ref,
    //             'batch' => $batch_no,
    //             'ref_no' => $ref_no,
    //             'total_amount' => $total_amount,
    //             'currency' => $currency,
    //             'narration' => 'Bulk transfer',
    //         ]);



    //     $bolt = $bulk_data[0];
    //     $bulk_info = (object) $bolt;

    //     DB::table('TB_CORP_BANK_BULK_REF')
    //         ->insert([
    //             'ref_no' => $bulk_info->REF_NO,
    //             'description' => $bulk_info->TRANS_DESC,
    //             'user_id' => $bulk_info->USER_ID,
    //             'total_amount' => $bulk_info->TOTAL_AMOUNT,
    //             'batch_no' => $bulk_info->BATCH_NO,
    //             'account_no' => $bulk_info->ACCOUNT_NO,
    //             'account_mandate' => $bulk_info->ACCOUNT_MANDATE,
    //         ]);


    //     DB::commit();
    //     return 'HI';


    //     return response()->json([
    //         'responseCode' => '000',
    //         'message' => "Detail of upload transfer",
    //         'data' => NULL
    //     ], 200);
    // }
}