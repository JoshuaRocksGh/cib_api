<?php

namespace App\Http\Controllers\BULK;

use App\Http\Controllers\Controller;
use App\Imports\KorporUploadImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
                'responseCode' => '000',
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
}
