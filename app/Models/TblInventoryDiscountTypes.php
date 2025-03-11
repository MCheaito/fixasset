<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryDiscountTypes extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_discount_types';
	protected $fillable = [
		'id',	
        'name',
		'sign',
		'type',		
		'user_id',
        'active'
    ];    
}
