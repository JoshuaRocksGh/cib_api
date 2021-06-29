<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KorporUploadImport implements WithHeadingRow, ToCollection
{
    private $customer_no;
    private $account_no;
    private $user_id;

    public function __construct($account_no, $user_id, $customer_no)
    {
        $this->customer_no = $customer_no;
        $this->user_id = $user_id;
        $this->account_no = $account_no;

    }

    public function headingRow(): int
    {
        return 1;
    }


    public function collection(Collection $rows)
    {
        header('Content-type: application/json');

        $batch_no = (string) time();
        $account_no = $this->account_no ;
        $customer_no = $this->customer_no ;
        $user_id = $this->user_id ;

        foreach ($rows as $row) {
            if(is_null($row)){

            }else{
                DB::table('paymentdb_cib_cobby')->insert([
                    'name' => trim($row['name']),
                    'phone' => trim($row['phone']),
                    'amount' => trim($row['amount']),
                    'account_no' => $account_no,
                    'customer_no' => $customer_no,
                    'user_id' => $user_id,
                    'batch_no' => $batch_no,
                    'reference_no' => (String) time(),
                    'status' => 'P',
                ]);
            }
        }

        DB::commit();

       $data =  DB::table('paymentdb_cib_cobby')->where('customer_no', $customer_no)->where('batch_no', $batch_no)->get();

        exit(json_encode([
            'responseCode' => '000',
            'message' => 'Uploaded korpor transaction',
            'data' => $data
        ]));

    }

}
