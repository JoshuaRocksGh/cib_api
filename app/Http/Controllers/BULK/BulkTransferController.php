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
        // return $request;

        return response()->json([
            'responseCode' => '000',
            'message' => "Detail of upload transfer",
            'data' => $request
        ], 200);

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



}
