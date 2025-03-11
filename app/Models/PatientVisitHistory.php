<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientVisitHistory extends Model
{
    use HasFactory;
	protected $table = 'tbl_patient_visit_hist';

    protected $fillable = [
        'visit_num',
		'equip_type',
		'root_cause',
		'second_cause',
		'exam_date',
		'visit_notes',
		'ocular_complaint',
		'ocular_part',
		'ocular_pain',
		'ocular_contexte',
		'ocular_signs',
		'ocular_conds',
		'ocular_notes',
		'med_conds',
		'med_allergies',
		'med_notes',
		'med_infos',
		'diab_type',
		'diab_control',
		'diab_notes',
		'clinic_num',
		'patient_num',
		'user_num',
		'active'	
    ];
    
   
}
