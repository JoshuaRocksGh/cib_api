<?php

namespace App\Http\Controllers\BULK;

use App\Http\Controllers\Controller;
use App\Imports\KorporUploadImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BulkKorporController extends Controller
{
    public function bulk_korpor_upload(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'select_file' => 'required|mimes:xls,xlsx',
            'account_no' => 'required',
            'user_id' => 'required',
            'customer_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => '500',
                'message' => 'Error validation error',
                'error' => $validator->errors(),
                'data' => null
            ], 200);
        }

        // return $request;

        if ($request->file()) {

            $account_no = $request->account_no;
            $customer_no = $request->customer_no;
            $user_id = $request->user_id;


            $path = $request->file('select_file')->getRealPath();

            $file = $request->file('select_file');
            $ext = $file->getClientOriginalExtension();

            $post_date = Carbon::now();
            $post_date = $post_date->toDateTimeString();



            return Excel::import(new KorporUploadImport($account_no, $user_id, $customer_no, $file), $file);

        }else{
            return [
                'status' => 'failed',
                'message' => 'Upload file failed'
            ];
        }

    }

    public function get_bulk_korpor_upload_list(Request $request)
    {

        // return $request;
        $customerNumber = $request->query("customer_no");
        $status = $request->query("status");
        // return $customer_no;

        $files = DB::table('bankowner.paymentdb_cib_cobby')
            ->where('customer_no', $customerNumber)
            ->where('status', $status)
            ->orderBy('batch_no', 'desc')
            ->distinct()
            // ->get();status
            ->get(['batch_no', 'account_no', 'status', 'user_id', 'customer_no']);

        return response()->json([
            'responseCode' => '000',
            'message' => "Available Uploads",
            'data' => $files
        ], 200);
    }

    public function get_bulk_korpor_upload_detail_list(Request $request)
    {
        $customerNumber = $request->query("customer_no");
        $status = $request->query("status");
        $batch_no = $request->query('batch_no');

        $files = DB::table('bankowner.paymentdb_cib_cobby')
            ->where('customer_no', $customerNumber)
            ->where('status', $status)
            ->where('batch_no', $batch_no)
            ->get();

        return response()->json([
            'responseCode' => '000',
            'message' => "Available Uploads for Batch NO: #" . $batch_no,
            'data' => $files
        ], 200);
    }

    public function update_bulk_korpor_upload_detail_list(Request $request)
    {
        $id = $request->id;
        $customer_no = $request->customer_no;
        $name = $request->name;
        $phone = $request->phone;
        $amount = $request->amount;


        $update_query = DB::table('bankowner.paymentdb_cib_cobby')
            ->where('customer_no', $customer_no)
            ->where('id', $id)
            ->update([
                'name' => $name,
                'phone' => $phone,
                'amount' => $amount
            ]);

            if($update_query){
                return response()->json([
                    'responseCode' => '000',
                    'message' => "Update successful # " ,
                    'data' => null
                ], 200);
            }else{
                return response()->json([
                    'responseCode' => '550',
                    'message' => "Failed to update # " ,
                    'data' => null
                ], 200);
            }



    }

}
