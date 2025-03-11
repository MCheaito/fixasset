<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSignature extends Model
{
    use HasFactory;
	protected $table = 'tbl_doctor_signature';

    protected $fillable = [
        'path',
		'user_num',
		'active'
		
    ];
    
   
}
