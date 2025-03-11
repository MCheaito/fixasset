<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientOcularHistory extends Model
{
    use HasFactory;
	protected $table = 'tbl_patient_ocular_hist';

    protected $fillable = [
        'visit_num',
		'equip_type',
		'ocular_conds',
		'other_ocular_conds',
		'exam_date',
		'ocular_notes',
		'clinic_num',
		'patient_num',
		'user_num',
		'active'	
    ];
    
   
}
