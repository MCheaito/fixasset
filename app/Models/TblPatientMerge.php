<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblPatientMerge extends Model
{
    use HasFactory;
	protected $table = 'tbl_patients_merge';

    protected $fillable = [
		'patient_num',
		'name',
		'merge_num',
		'user_id',
		'active'
    ];
    
   
}
