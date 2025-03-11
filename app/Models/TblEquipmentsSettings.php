<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblEquipmentsSettings extends Model
{
    use HasFactory;
	protected $table = 'tbl_equipments_settings';

    protected $fillable = [
        'equip_num',
		'equip_type',
		'equip_code',
		'main_elem',
		'first_elem',
		'first_elem_prop',
		'first_elem_value',
		'first_elem_prop1',
		'first_elem_value1',
		'second_elem',
		'second_elem_prop',
		'second_elem_value',
		'third_elem',
		'third_elem_prop',
		'third_elem_value',
		'active'	
    ];
    
   
}
