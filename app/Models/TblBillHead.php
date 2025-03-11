<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBillHead extends Model
{
    use HasFactory;
	protected $table = 'tbl_bill_head';
	     			 				 	 	       	   						
    protected $fillable = [
	'clinic_num',
	'clinic_bill_num',
	'visit_num',
	'doctor_num',
	'doctor_bill_num',
	'patient_num',
	'user_num',
	'user_type',
	'bill_discount',
	'bill_total',
	'lbill_total',
	'ebill_total',
	'bill_tax',
	'tvq',
	'tvs',
	'bill_balance',
	'bill_datein',
	'bill_path',
	'notes',
	'ext_lab',
	'order_id',
	'status',
	'is_percentage',
	'discount_percent',
	'bill_discount_us',
	'bill_balance_us',
	'exchange_rate'
    ];
    
   
}
