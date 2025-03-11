<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblCategoriesTypes extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_category_types';

    protected $fillable = [
        'name',
		'name_eng',
		'types',
		'user_id',
		'clinic_num',
		'active'
		
    ];
    
   
}
