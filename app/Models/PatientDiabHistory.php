<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDiabHistory extends Model
{
    use HasFactory;
	protected $table = 'tbl_patient_diab_hist';

    protected $fillable = [
        'visit_num',
		'equip_type',
		'diab_conds',
		'diab_type',
		'diab_control',
		'notes',
		'clinic_num',
		'patient_num',
		'user_num',
		'active'	
    ];
    
   
}
