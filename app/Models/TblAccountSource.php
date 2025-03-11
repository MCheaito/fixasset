<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblAccountSource extends Model
{
    use HasFactory;
	protected $table = 'tbl_account_source';
	protected $fillable = [
       'id',
	   'name_eng',
	   'name_fr',
	   'status',
	   'clinic_num',
	   'user_id'
	   ];    
}
