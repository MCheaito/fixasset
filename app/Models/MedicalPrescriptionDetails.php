<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalPrescriptionDetails extends Model
{
 use HasFactory;
	protected $table = 'tbl_medical_prescriptions_details';
	protected $fillable = [
   	  'presc_id',
	  'eye_type',
	  'section_num',
	  'medicine_num',
	  'voie_num', 
	  'dosage', 
	  'dosage_type', 
	  'dosage_period', 
	  'freq', 
	  'duration', 
	  'duration_unit',
	  'expiry_date',
	  'remarks',
	  'renew',
	  'user_num',
      'active'
	   ];    	

}