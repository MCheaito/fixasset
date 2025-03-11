<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblEquipments extends Model
{
    use HasFactory;
	protected $table = 'tbl_equipments';

    protected $fillable = [
        'equip_name',
		'equip_version',
		'active'	
    ];
    
   
}
