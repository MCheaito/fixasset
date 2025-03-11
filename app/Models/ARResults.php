<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ARResults extends Model
{
    use HasFactory;
	protected $table = 'tbl_auto_refractor';
	protected $fillable = [
        'visit_num',
		'equip_type',
		'right_sphere',
		'right_cylinder',
		'right_axis',
		'left_sphere',
		'left_cylinder',
        'left_axis',
		'binocularFar',
		'binocularNear',
		'right_ci',
		'left_ci',
		'right_se',
		'left_se',
		'remarks',
		'clinic_num',
		'patient_num',
		'user_num',
        'active',
		'right_sphere1',
		'right_cylinder1',
		'right_axis1',
		'left_sphere1',
		'left_cylinder1',
        'left_axis1',
		'right_size',
		'left_size',
		'right_lamp',
		'left_lamp'
		
    ];    
}
