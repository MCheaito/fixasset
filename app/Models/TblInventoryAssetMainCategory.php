<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryAssetMainCategory extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_assetmaincategory';
	protected $fillable = [
		'id',
		'name',
		'accountnb',
		'depreciation',
		'asset_life',
		'asset_age',
		'user_id',
		'clinic_num',
		'active'		
    ];    
}
