<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RLDoctorClinic extends Model
{
    use HasFactory;
	protected $table = 'tbl_doctors_clinics';

    protected $fillable = [
        'user_num',
		'doctor_num',
        'clinic_num',
		'active'
		
    ];
    
   
}
