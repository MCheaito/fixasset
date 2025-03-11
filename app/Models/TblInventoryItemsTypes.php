<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryItemsTypes extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_fournisseur_types';
	protected $fillable = [
		'id',	
        'name', 
		'user_id',
        'active'
    ];    
}
