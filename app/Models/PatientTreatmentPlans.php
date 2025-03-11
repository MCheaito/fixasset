<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientTreatmentPlans extends Model
{
    use HasFactory;
	protected $table = 'tbl_patient_visit_treatment_plan';
	protected $fillable = [
        
		'visit_num',
		'patient_num',
		'clinic_num',
		'doctor_num',
        'user_num',
		'equip_type',
		'description',
		'status'
    ];    
}
