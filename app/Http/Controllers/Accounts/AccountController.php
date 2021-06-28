<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
     public function get_accounts(Request $request)
     {
         $customer_no = $request->customer_no;

         $accounts_query = " SELECT a.cust_no as customer_number, cus_ac_no as account_number, NVL (ACCOUNT_DESCRP, customer_name) as account_desc, cleared_bal as available_balance, book_bal as ledger_balance, get_curriso (get_acctcurrcode (cus_ac_no)) as currency, acc_type as account_type, get_localeqv_avbal (cus_ac_no)  as local_equivalent_available_balance, a.account_mandate  FROM   TBLCUSTOMERACCINFO_VV a LEFT OUTER JOIN mbank_acct_desc b  ON a.cus_ac_no = B.ACCT_LINK  WHERE A.CUST_NO = '$customer_no' AND info_type IN ('1', '2')";

         $accounts = DB::select(DB::raw($accounts_query));

         return response()->json([
             'responseCode' => '000',
             'message' => 'Customer Account',
             'data' => $accounts
         ], 200);
     }
}
