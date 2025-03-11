<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryTypes extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_types';
	protected $fillable = [
		'id',	
        'name',
		'sign',
		'type',		
		'user_id',
        'active'
    ];    
}
