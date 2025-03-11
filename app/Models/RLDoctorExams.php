<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RLDoctorExams extends Model
{
    use HasFactory;
	protected $table = 'tbl_doctors_exams';

    protected $fillable = [
        'exam_num',
		'doctor_num',
        'user_num'
		
    ];
    
   
}
