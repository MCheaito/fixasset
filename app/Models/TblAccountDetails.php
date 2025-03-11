<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblAccountDetails extends Model
{
    use HasFactory;
	protected $table = 'tbl_account_details';
	protected $fillable = [
      'account_id',
	  'serial',
	  'admi1',
	  'name1',
	  'amount1',
	  'curency1',
	  'prcurren1',
	  'admi2',
	  'name2',
	  'amount2',
	  'curency2',
	  'prcurren2',
	  'notes',
	  'status',
	  'user_id',
	  'active'
    ];    
}
