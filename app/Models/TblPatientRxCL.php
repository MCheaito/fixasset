<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblPatientRxCL extends Model
{
    use HasFactory;
	protected $table = 'tbl_patient_rx_cl';

    protected $fillable = [
       	'user_num', 'visit_num', 'clinic_num', 'doctor_num', 'patient_num', 
		'datein', 'dateexp', 'reason', 'opticien', 'rx_type', 
		'odsph', 'odcyl', 'odaxe', 'odadd', 'ogsph', 'ogcyl', 'ogaxe', 'ogadd', 
		'oddom', 'odbc', 'oddia', 'ogdom', 'ogbc', 'ogdia', 'oddeloin', 'oddepres', 
		'ogdeloin', 'ogdepres', 'notes', 'rpower', 'rradius', 'raxe', 'rdom', 
		'lpower', 'lradius', 'laxe', 'ldom', 'rlens', 'ritem', 'llens', 'litem', 
		'oudeloin', 'oudepres', 'chirugie', 'glaucome', 'medication', 'blessure', 
		'diab', 'allergy', 'arterial', 'health_notes', 'rtaper', 'rmaterial', 
		'rscope', 'ltaper', 'lmaterial', 'lscope', 'solution', 'rconfort', 
		'rvision', 'rrotation', 'rcentration', 'rmovement', 'lconfort', 'lvision', 
		'lmovement', 'lcentration', 'lrotation', 'rlarmes', 'rsclere', 'rlimbe', 
		'rcornee', 'rpaupiere', 'llarmes', 'lsclere', 'llimbe', 'lcornee', 'lpaupiere', 
		'prop_notes', 'prop_image', 'active','prop_image_thumb','rremplacement','lremplacement','dmla',
		'rlatdisp','llatdisp','rmouvsec','lmouvsec','rpushup','lpushup','rdeglimb','deglimb','rhyd','lhyd'
    ];
    
   
}
