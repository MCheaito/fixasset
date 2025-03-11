<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalPrescription extends Model
{
 use HasFactory;
	protected $table = 'tbl_medical_prescriptions';
	protected $fillable = [
   	   'patient_num',
	   'date_presc',
	   'clinic_num',
	   'user_num',	
	   'active',
	   'visit_num',
	   'doctor_num',
	   'presc_email_pat',
	   'presc_sms_pat',
	   'presc_email_extbranch',
	   'presc_fax_extbranch',
	   'livrer',
	   'livrer_date',
	   'livrer_user'
	   
	   ];    	

}

