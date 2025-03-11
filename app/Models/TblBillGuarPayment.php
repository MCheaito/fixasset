<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBillGuarPayment extends Model
{
    use HasFactory;
	protected $table = 'tbl_bill_guar_payment';
						 						
    protected $fillable = [
       'datein',
	   'bill_num',
	   'clinic_num',
	   'visit_num',
	   'payment_amount',
	   'dolarprice',
	   'lpay_amount',
	   'dpay_amount',
	   'currency',
	   'payment_type',
	   'reference',
	   'user_num',
	   'user_type',
	   'assur_nb',
	   'assurance',
	   'status',
       'guarantor'	   
    ];
    
   
}
