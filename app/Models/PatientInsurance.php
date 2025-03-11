<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Model;

class PatientInsurance extends Model
{
protected $table = 'tbl_patients_insurance';
											
    protected $fillable = [
          
          'patient_num', 
		  'primary_ins',
		  'primary_ins_state',
		  'secondary_ins', 
		  'secondary_ins_state', 
		  'other_ins_name', 
		  'other_ins_relation',
		  'other_ins_ssn',
		  'other_ins_dob',
		  'other_ins_address',
		  'other_ins_phone',
		  'user_num', 
		  'status'
    ];
   

}
