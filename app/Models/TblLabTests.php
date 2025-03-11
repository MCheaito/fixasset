<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblLabTests extends Model
{
    use HasFactory;
	protected $table = 'tbl_lab_tests';
	protected $fillable = [
		'clinic_num',	
		'user_num',	
		'test_code',	
		'descrip',	
		'group_num',	
		'normal_value',	
		'test_rq',	
		'unit',	
		'price',	
		'test_name',	
		'cnss',	
		'nbl',	
		'listcode',	
		'testord',
		'category_num ',
		'is_group',
		'active'
    ];    
}
