<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicSMSPack extends Model
{
    use HasFactory;
	protected $table = 'tbl_clinic_sms_pack';
	protected $fillable = [
        'clinic_num',
		'old_sms_pack',
		'current_sms_pack',
		'pay_pack',
		'user_num',		
		'active'
		
    ];    
}
