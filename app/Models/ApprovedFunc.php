<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApprovedFunc extends Model
{

    public function approved_req($request_type_check, $request_id, $up, $acm, $chm, $comment, $comment_by, $user_id, $approvers)
    {



        // return [
        //     'request_type_check' => $request_type_check,
        //     'request_id' => $request_id,
        //     'up' => $up,
        //     'acm' => $acm,
        //     'chm' => $chm,
        //     'comment' => $comment,
        //     'comment_by' => $comment_by
        // ];



        if(is_null($approvers)){
            $approvers = $user_id;
        }else{
            $approvers = $approvers . ',' . $user_id;
        }





        // // THIS HAS DATA IN IT LIKE
        // // $chm = "2A AND 0B";
        if ($chm != null) {
            $chm = explode(' ', $chm);
        }


        $_acm = explode(' ', $acm);

        $json_array_format = array();

        $find_user = [];

        for ($index = 0; $index < count($_acm); $index++) {

            // if ( ($index % 2 != 0) AND ($_acm[$index][1] == $up) ) {
            if (($_acm[$index][1] != $up)) {
            } else {
                $find_user[] = 'user found';
            }
        }

        if (empty($find_user)) {
            return [
                'responseCode' => '400',
                'message' => 'User does not has the right'
            ];
        }

        if ($chm == null) {
            $chm = array();

            for ($index = 0; $index < count($_acm); $index++) {
                $chm[$index] = $_acm[$index];
            }
            for ($index = 0; $index < count($chm); $index++) {
                if (($chm[$index] == 'OR' or $chm[$index] == 'AND')) {
                    # code...
                } else {
                    $chm[$index][0] = 0;
                }
            }

            for ($index = 0; $index < count($chm); $index++) {

                if (($chm[$index] == 'OR' or $chm[$index] == 'AND')) {

                    if (($chm[$index] == 'OR') and ((isset($chm[$index - 1])) or (isset($chm[$index + 1]))) and (($chm[$index - 1] == $_acm[$index - 1]) or ($chm[$index + 1] == $_acm[$index + 1]))) {

                        $imp_acm = implode(" ", $_acm);
                        $imp_chm = implode(" ", $chm);

                        return [
                            'responseCode' => '000',
                            'check_mandate' => $imp_chm,
                            'message' => 'Do the check api calls but check the request type check string -> 1'
                        ];

                        // return $this->check_request($request_type_check, $request_id, $imp_chm, $comment, $comment_by,  $account_no, $start_date, $end_date, $type, $user_alias, $leaflet, $branch, $start_cheque, $end_cheque, $date_issued, $beneficiary_name, $amount);


                    }
                } else {
                    // $chm[$index][0] = 0;
                    if (($up == $chm[$index][1])) {
                        // CONVERT TO INTEGER
                        $_computer = intval($chm[$index][0]) + 1;
                        $chm[$index] = $_computer . $chm[$index][1];


                        if ((isset($chm[$index - 1]) and  $chm[$index - 1] == 'OR') and  (isset($chm[$index - 1])  and ($chm[$index - 1] == $_acm[$index - 1]))) {
                            if (intval($chm[$index][0]) == intval($_acm[$index][0])) {

                                $imp_acm = implode(" ", $_acm);
                                $imp_chm = implode(" ", $chm);

                                return [
                                    'responseCode' => '000',
                                    'check_mandate' => $imp_chm,
                                    'message' => 'Do the check api calls but check the request type check string'
                                ];

                                // return $this->check_request($request_type_check, $request_id, $imp_chm, );



                            }
                        }

                        if ((isset($chm[$index + 1]) and $chm[$index + 1] == 'OR') and  (isset($chm[$index + 1])  and ($chm[$index + 1] == $_acm[$index + 1]))) {

                            if (intval($chm[$index][0]) == intval($_acm[$index][0])) {

                                $imp_acm = implode(" ", $_acm);
                                $imp_chm = implode(" ", $chm);


                                return [
                                    'responseCode' => '000',
                                    'check_mandate' => $imp_chm,
                                    'message' => 'Do the check api calls but check the request type check string -> 2'
                                ];

                                // return $this->check_request($request_type_check, $request_id, $imp_chm, $comment, $comment_by,  $account_no, $start_date, $end_date, $type, $user_alias, $leaflet, $branch, $start_cheque, $end_cheque, $date_issued, $beneficiary_name, $amount);




                            }
                        }



                        if ((intval($chm[$index][0]) == intval($_acm[$index][0]))  and  isset($chm[$index]) and  $chm[$index] == 'OR') {
                            return [
                                'responseCode' => '400',
                                'message' => 'Please error occured'
                            ];
                        }

                        if (intval($chm[$index][0]) > intval($_acm[$index][0])) {
                            return [
                                'responseCode' => '400',
                                'message' => 'PANEL [' . $up . '] HAS REACHED ITS LIMITS '
                            ];
                        }
                    }
                }


                // echo $chm[$index];

            }
            $imp_acm = implode(" ", $_acm);
            $imp_chm = implode(" ", $chm);

            return $this->compare($request_id, $imp_acm, $imp_chm, $comment, $comment_by, $approvers);
        } else {

            for ($index = 0; $index < count($chm); $index++) {

                if (($chm[$index] == 'OR' or $chm[$index] == 'AND')) {
                    # code...
                } else {

                    if (($up == $chm[$index][1])) {
                        // CONVERT TO INTEGER
                        $_computer = intval($chm[$index][0]) + 1;
                        $chm[$index] = $_computer . $chm[$index][1];

                        if (intval($chm[$index][0]) > intval($_acm[$index][0])) {

                            return [
                                'responseCode' => '400',
                                'message' => 'PANEL [' . $up . '] HAS REACHED ITS LIMITS '
                            ];
                        }
                    }
                }

                // echo $chm[$index];

            }

            $imp_acm = implode(" ", $_acm);
            $imp_chm = implode(" ", $chm);

            for ($index = 0; $index < count($_acm); $index++) {

                // if ( ($chm[$index] == 'OR') AND (isset($chm[$index-1]) AND isset($_acm[$index-1]) ) AND ( $chm[$index-1] == $_acm[$index-1])  ) {
                if (($chm[$index] == 'OR') and (isset($chm[$index - 1]) and isset($_acm[$index - 1])) and (($chm[$index - 1] == $_acm[$index - 1]) or ($chm[$index + 1] == $_acm[$index + 1]))) {


                    return [
                        'responseCode' => '000',
                        'check_mandate' => $imp_chm,
                        'message' => 'Do the check api calls but check the request type check string -> 3'
                    ];

                    // return $this->check_request($request_type_check, $request_id, $imp_chm, $comment, $comment_by,  $account_no, $start_date, $end_date, $type, $user_alias, $leaflet, $branch, $start_cheque, $_cheque, $date_issued, $beneficiary_name, $amount);

                }
            }

            return $this->compare($request_id, $imp_acm, $imp_chm, $comment, $comment_by, $approvers);
        }
    }



    public function compare($request_id, $_account_mandater, $_check_mandate, $comment, $comment_by, $approvers)
    {
        if ($_account_mandater == $_check_mandate) {


            return [
                'responseCode' => '000',
                'check_mandate' => $_check_mandate,
                'message' => 'Do the check api calls but check the request type check string -> 4'
            ];

            // check_request($request_type_check, $request_id, $imp_chm, $comment, $comment_by,  $account_no, $start_date, $end_date, $type, $user_alias, $leaflet, $branch, $start_cheque, $end_cheque, $issue_date, $beneficiary_name, $amount);

            // return $this->call_statement_req($request_id, $request_type_check, $_check_mandate, $comment, $comment_by,  $account_no, $start_date, $end_date, $type, $user_alias);


        } else {
            $waiting = DB::table('tb_corp_bank_req')->where('request_id', $request_id)->update(['check_mandate' => $_check_mandate, 'waitinglist' => 'waiting', 'comment_1' => $comment, 'comment_1_by' => $comment_by, 'approvers' => $approvers]);

            if ($waiting == true) {
                return [
                    'responseCode' => '200',
                    'status' => 'waiting',
                    'message' => 'Waiting for others to approve'
                ];
            }
        }
    }
}
