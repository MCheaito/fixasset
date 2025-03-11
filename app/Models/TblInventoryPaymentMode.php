<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryPaymentMode extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_payment_mode';
 						
    protected $fillable = [
       'name_eng',
	   'name_fr',
	   'clinic_num',
	   'assurance',
	   'user_num',
	   'status'
    ];
    
   
}
