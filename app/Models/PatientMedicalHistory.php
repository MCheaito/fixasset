<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientMedicalHistory extends Model
{
    use HasFactory;
	protected $table = 'tbl_patient_med_hist';

    protected $fillable = [
        'visit_num',
		'equip_type',
		'med_conds',
		'other_med_conds',
		'med_allergies',
		'other_med_allergies',
		'med_notes',
		'med_infos',
		'clinic_num',
		'patient_num',
		'user_num',
		'active'	
    ];
    
   
}
