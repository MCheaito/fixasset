<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RLClinicsExams extends Model
{
    use HasFactory;
	protected $table = 'tbl_clinics_exams';

    protected $fillable = [
        'user_num',
		'clinic_num',
        'exam_num'
		
    ];
    
   
}
