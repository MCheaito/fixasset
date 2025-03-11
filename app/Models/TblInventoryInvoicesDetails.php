<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblInventoryInvoicesDetails extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_invoices_details';
	protected $fillable = [
      'invoice_id',
	  'ref_invoice',
	  'cmd_id',
	  'cmd_type',
	  'item_code',
	  'item_name',
	  'item_specs',
	  'qty',
	  'price',
	  'discount',	
	  'total',	
	  'qst',	
	  'gst',
	  'date_exp',
	  'notes',
	  'tdiscount',
	  'tax',
	  'formula_id',
	  'initprice',
	  'sel_price',
	  'status',
	  'user_id',
	  'rqty',
      'rlid', 
	  'rprice', 
	  'cfacture', 
	  'cpay',
	  'tpackage',
		'nblot',	  
	   'active'
    ];    
}
