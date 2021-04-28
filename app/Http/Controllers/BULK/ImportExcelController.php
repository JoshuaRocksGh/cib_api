<?php

namespace App\Http\Controllers\BULK;

use App\Http\Controllers\Controller;
use App\Imports\ExcelUploadImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ImportExcelController extends Controller
{



    public function import(Request $request)
    {
        $documentRef = time();
        $account_no = $request->account_no;
        $bank_code = $request->bank_type;
        $trans_ref_no = $request->trans_ref_no;
        $total_amount = $request->total_amount;
        $value_date = $request->value_date;

        $validator = Validator::make($request->all(), [
            'select_file' => 'required|mimes:xls,xlsx',
            'account_no' => 'required',
            'bank_type' => 'required',
            'trans_ref_no' => 'required',
            'total_amount' => 'required',
            'value_date' => 'required',

            'user_id' => 'required',
            'user_name' => 'required',
            'customer_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 200);
        }
        if ($request->file()) {


            $path = $request->file('select_file')->getRealPath();

            $file = $request->file('select_file');
            $ext = $file->getClientOriginalExtension();
            $name = strtoupper($documentRef) . '~' . strtoupper($trans_ref_no) . '~' . strtoupper($total_amount) . '.' . $ext;

            $post_date = Carbon::now();
            $post_date = $post_date->toDateTimeString();

            if (Excel::import(new ExcelUploadImport($documentRef, $account_no, $bank_code, $trans_ref_no, $total_amount, $value_date), $file)) {
                // return response()->json( Excel::import(new ExcelUploadImport, $file));

                // return view();
                // return $batch_no;

                // GET ACCOUNT MANDATE
                $query_acc_mandate = DB::table('vw_ibank_mandate')->where('acct_link', $account_no)->value('mandate');
                $query_result = DB::table('tb_corp_bank_req')->insert(
                    [
                        'request_type' => 'BULK',
                        'request_status' => '',
                        'user_id' => "user_id",
                        'user_name' => "user_name",
                        'customer_no' => "customer_no",
                        'account_no' => $account_no,
                        'account_mandate' => $query_acc_mandate,
                        'batch' => $documentRef,
                        'waitinglist' => 'not approved',
                        'bankcode' => $bank_code,
                        // 'narration' => $narration,
                        'post_date' => $post_date,
                        'is_accept_excel' => 'NY',
                        'ref_no' => $trans_ref_no,
                        'total_amount' => $total_amount,
                        'value_date' => $value_date,
                    ]
                );

                // MOVE FILE TO THIS PATH
                $file->move(storage_path() . '/paymentupload/', $name);

                if ($query_result) {
                    $bank_type = $bank_code;
                    echo json_encode([
                        'status' => 'failed',
                        'message' => 'statement request failed'
                    ]);
                    die();
                } else {
                    echo json_encode([
                        'status' => 'failed',
                        'message' => 'statement request failed'
                    ]);
                    die();
                }
            }


            return [
                'name' => $name
            ];
        } else {
            return [
                'status' => 'failed',
                'message' => 'Upload file failed'
            ];
        }
    }
}