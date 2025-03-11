<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorProfilePhoto extends Model
{
    use HasFactory;
	protected $table = 'tbl_doctor_profile_photo';

    protected $fillable = [
        'path',
		'user_num',
		'active'
		
    ];
    
   
}
