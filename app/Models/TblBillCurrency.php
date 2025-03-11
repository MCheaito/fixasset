<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBillCurrency extends Model
{
    use HasFactory;
	protected $table = 'tbl_bill_currency';
						 						
    protected $fillable = [
		'descrip',
		'abreviation',	
		'price',	
		'active'	
    ];
    
   
}
