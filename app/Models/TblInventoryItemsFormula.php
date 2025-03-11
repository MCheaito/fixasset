<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryItemsFormula extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_items_formula';
	protected $fillable = [
        'name', 
        'formula',
		'user_id',
		'clinic_num',
        'active'
    ];    
}
