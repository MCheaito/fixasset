<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarResources extends Model
{
    use HasFactory;
	protected $table = 'tbl_calendar_resources';
	protected $fillable = [
        'clinic_num',
		'doctor_num',
		'doctor_name', 
        'start_time',
        'end_time',
		'week_days',
		'month_day_num',
		'month_num',
        'start',
		'end_before',
		'repeat_type',
		'repeat_interval',
        'user_num',
		'for_patient',
		'off_days',
		'descrip_en',
		'descrip_fr',
		'active'
    ];    
}
