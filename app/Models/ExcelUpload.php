<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelUpload extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bban', 'name', 'amount', 'trans_desc', 'user_id', 'account_no', 'batch_no', 'bank_code', 'ref_no', 'total_amount', 'value_date', 'status', 'message', 'bank_name'
        // 'bban', 'name', 'amount', 'trans_desc', 'user_id', 'account_no', 'batch_no',
    ];

    protected $table = 'tb_corp_bank_import_excel';
}