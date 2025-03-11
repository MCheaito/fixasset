<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblCMD extends Model
{
    use HasFactory;
	protected $table = 'tbl_patient_command';
	protected $fillable = [
       'visit_num', 
	   'patient_num', 
	   'clinic_num', 
	   'doctor_num', 
	   'datecmd', 
	   'right_rx_data', 
	   'left_rx_data',
       'total',
       'discount',
       'qst',
       'gst',
       'cmd_balance',
       'invoice_num',
       'supplier_num',   	   
	   'user_num',
	   'remark',
	   'active'
    ];    
}
