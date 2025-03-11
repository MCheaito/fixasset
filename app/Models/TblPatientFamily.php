<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblPatientFamily extends Model
{
    use HasFactory;
	protected $table = 'tbl_patients_family';

    protected $fillable = [
		'patient_num',
		'name',
		'family_num',
		'family_rel',
		'user_id',
		'active'
    ];
    
   
}
