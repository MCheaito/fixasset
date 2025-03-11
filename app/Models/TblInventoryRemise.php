<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryRemise extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_remise';
	protected $fillable = [
      'invoice_id',
	  'item_code',
	  'item_name',
	  'qty',
	  'patient_id',
	  'status',
	  'datein',
	   'user_id',
	   'active'
    ];    
}
