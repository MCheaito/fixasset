<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBillSpecifics extends Model
{
    use HasFactory;
	protected $table = 'tbl_bill_specifics';

    protected $fillable = [
       'bill_num',
	   'bill_code',
	   'bill_name',
	   'doctor_num',
	   'bill_quantity',
	   'bill_price',
	   'ebill_price',
	   'bill_discount',
	   'currency',
	   'dolarprice',
	   'lbill_price',
	   'cnss',
	   'status',
	   'user_num',
	   'user_type',
       'ext_lab',
	   'ref_lab',
	   'ref_lbill_price',
	   'ref_dolarprice',
	   'ref_ebill_price',
	   'ref_paid',
	   'ref_date_paid' 	
	 
    ];
    
   
}
