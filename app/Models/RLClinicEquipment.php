<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RLClinicEquipment extends Model
{
    use HasFactory;
	protected $table = 'tbl_equipments_branch';

    protected $fillable = [
        'user_num',
		'clinic_num',
		'equip_num',
		'equip_path',
		'equip_types',
        'equip_description'		
    ];
    
   
}
