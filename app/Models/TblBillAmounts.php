<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBillAmounts extends Model
{
    use HasFactory;
	protected $table = 'tbl_bill_amounts';
															 		 					
	protected $fillable = [
        'clinic_num', 
        'am_code',
        'name_fr',
        'name_eng',
        'color',
		'duration',
		'assurance',
		'category',
		'scheduling',
		'taxable',
        'amount',
		'cost_amount',
		'sell_amount',
		'formula_id',
        'discount',
		'comments',
		'typecode',
        'user_num',
  		'status'
		
    ];    
}
