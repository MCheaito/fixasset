<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryPayment extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_payment';
						 						
    protected $fillable = [
       'datein',
	   'invoice_num',
	   'clinic_num',
	   'payment_amount',
	   'payment_type',
	   'reference',
	   'user_num',
	   'user_type',
	   'assur_nb',
	   'assurance',
	   'deposit',
	   'remark',
	   'status',
	   'crnote'		   
    ];
    
   
}
