<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblCMDDetails extends Model
{
    use HasFactory;
	protected $table = 'tbl_patient_command_details';
	protected $fillable = [
       
	  'cmd_id',
	  'invoice_num',
      'cmd_type',	  
	  'visit_num', 
	  'patient_num', 
	  'item_code', 
	  'item_name', 
	  'item_specs', 
	  'qty', 
	  'discount', 
	  'sel_price', 
	  'total', 
	  'tdiscount', 
	  'tax', 
	  'user_num', 
	  'active', 
	  'notes'
    ];    
}
