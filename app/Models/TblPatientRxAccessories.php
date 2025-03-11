<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblPatientRxAccessories extends Model
{
    use HasFactory;
	protected $table = 'tbl_patient_rx_accessories';

    protected $fillable = [
       	'visit_num',
		'datein',
		'dateexp',
		'reason',
		'opticien',
		'doctor_num',
		'rx_type',
		'odsph',
		'odcyl',
		'odaxe',
		'oddva',
		'ogsph',
		'ogcyl',
		'ogaxe',
		'ogdva',
		'odprism',
		'odbase',
		'odprism2',
		'odbase2',
		'ogprism',
		'ogbase',
		'ogprism2',
		'ogbase2',
		'odav',
		'odvertex',
		'oddeloin',
		'oddepres',
		'ogav',
		'ogvertex',
		'ogdeloin',
		'ogdepres',
		'odbalance',
		'ogbalance',
		'notes',
		'clinic_num',
		'patient_num',
		'user_num',
		'active'
    ];
    
   
}
