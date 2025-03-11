<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LMResults extends Model
{
    use HasFactory;
	protected $table = 'tbl_lensometer';
	protected $fillable = [
        'user_num',
		'visit_num',
		'clinic_num',
		'patient_num',
		'equip_type',
		'right_sphere',
		'right_cylinder',
		'right_axis', 
		'right_prism_h',
		'right_prism_v',
		'right_add1', 
		'right_add2',
		'left_sphere',
		'left_cylinder',
		'left_axis', 
		'left_prism_h', 
		'left_prism_v', 
		'left_add1', 
		'left_add2', 
		'binocular_distance',
        'right_se', 
		'left_se', 
		'right_ci', 
		'left_ci', 
		'right_uv', 
		'left_uv', 
		'right_prism', 
		'left_prism', 
		'right_prism_b', 
		'left_prism_b', 
		'right_near_sph', 
		'left_near_sph', 
		'right_near_sph2', 
		'left_near_sph2', 
		'right_near_pd', 
		'left_near_pd', 
		'both_near_pd', 
		'right_far_pd', 
		'left_far_pd',		
		'notes', 
		'active'
		
    ];    
}
