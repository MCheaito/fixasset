<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryFormulaPrice extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_formula_price';
	protected $fillable = [
		'id',	
        'formula_id',
		'from_price',
		'to_price',
		'divise',
		'multiple',
		'minus',
		'plus',
		'user_id',
        'active'
    ];    
}
