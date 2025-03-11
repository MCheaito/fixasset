<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBillPaymentMode extends Model
{
    use HasFactory;
	protected $table = 'tbl_bill_payment_mode';
 						
    protected $fillable = [
       'name_eng',
	   'name_fr',
	   'clinic_num',
	   'assurance',
	   'user_num',
	   'status'
    ];
    
   
}
