<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryMaterialsRequest extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_materials_request';
	protected $fillable = [
	'id',	
	'type',	
	'date_invoice',
	'total',
	'remark',	
	'clinic_num',	
	'approve',	
	'adj',	
	'date_approve',	
	'user_id',	
	'active'		
	   ];    
}
