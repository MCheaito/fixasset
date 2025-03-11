<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblLocation extends Model
{
    use HasFactory;
	protected $table = 'tbl_inventory_location';

    protected $fillable = [
		'code',
        'name',
		'name_eng',
		'types',
		'user_id',
		'clinic_num',
		'active'
		
    ];
    
   
}
