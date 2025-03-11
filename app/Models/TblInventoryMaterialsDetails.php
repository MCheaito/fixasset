<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryMaterialsDetails extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_materials_details';
	protected $fillable = [
      'invoice_id',
	  'item_code',
	  'item_name',
	  'item_specs',
	  'qty',
	  'price',
	  'total',	
	  'qst',	
	  'gst',
	  'notes',
	  'tax',
	  'status',
	  'user_id',
	  'rqty',
	  'dqty',
      'active'
    ];    
}
