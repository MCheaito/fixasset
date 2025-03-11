<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KMResults extends Model
{
    use HasFactory;
	protected $table = 'tbl_keratometer';
	protected $fillable = [
        'visit_num',
		'equip_type',
		'right_power',
		'right_radius',
		'right_axis',
		'left_power',
		'left_radius',
		'left_axis',
		'right_power2',
		'right_radius2',
		'right_axis2',
		'left_power2',
		'left_radius2',
		'left_axis2',
		'right_power3',
		'right_radius3',
		'left_power3',
		'left_radius3',
		'right_size',
		'left_size',
		'notes',
		'clinic_num',
		'patient_num',
		'user_num',
        'active'
		
    ];    
}
