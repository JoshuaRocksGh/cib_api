<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApiBaseResponse extends Model
{
    
    public static function api_response($response)
    {

       

        if ($response->ok()) {    // API response status code is 200

            $result = json_decode($response->body());
            // return $result->responseCode;


            if ($result->responseCode == '000') {

                return response()->json([
                    'responseCode' => $result->responseCode,
                    'message' => $result->message,
                    'data' => $result->data
                ], 200);

            } else {   // API responseCode is not 000

                return response()->json([
                    'responseCode' => $result->responseCode,
                    'message' => $result->message,
                    'data' => $result->data
                ], 200);

            }
        } else { // API response status code not 200

            //return $response->body();
            DB::table('tb_error_logs')->insert([
                'platform' => 'ONLINE_INTERNET_BANKING',
                'user_id' => 'AUTH',
                'code' => $response->status(),
                'message' => $response->body()
            ]);

            return response()->json([
                'responseCode' => '500',
                'message' => 'API SERVER ERROR',
                'data' => NULL
            ], 200);

        }
    }
}
