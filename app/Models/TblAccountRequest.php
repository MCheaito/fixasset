<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblAccountRequest extends Model
{
    use HasFactory;
	protected $table = 'tbl_account_head';
	protected $fillable = [
       'serial',
	   'datein',
	   'refer',
	   'rq',
	   'type',
	   'etamount1',
	   'etamount2',
	   'ltamount1',
	   'ltamount2',
	   'dtamount1',
	   'dtamount2',
	   'dateout',
	   'clinic_num',
	   'user_id',
	   'active'	
	   ];    
}
