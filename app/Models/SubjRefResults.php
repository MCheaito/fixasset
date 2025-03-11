<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjRefResults extends Model
{
    use HasFactory;
	protected $table = 'tbl_subj_refraction';
	protected $fillable = [
        'user_num',
		'visit_num',
		'subj_ref_num',
		'clinic_num',
		'patient_num',
		'equip_type',
		'expiry_date',
		'right_sphere',
		'right_cylinder',
		'right_axis',
		'right_prism_h',
		'right_prism_v',
		'right_prism',
		'right_prism_b',
		'right_near_va',
		'right_distant_va',
		'right_add1',
		'right_phva',
		'right_liva',
		'right_pd',
		'left_sphere',
		'left_cylinder',
		'left_axis',
		'left_prism_h',
		'left_prism_v',
		'left_prism',
		'left_prism_b',
		'left_near_va',
		'left_distant_va',
		'left_add1',
		'left_phva',
		'left_liva',
		'left_pd',
		'both_near_va',
		'both_distant_va',
		'both_phva',
		'both_liva',
		'both_pd',
		'notes', 
		'active'
	];    
}
