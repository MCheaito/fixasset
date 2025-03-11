<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvents extends Model
{
    use HasFactory;
	protected $table = 'tbl_calendar_events';
	protected $fillable = [
        'title', 
        'start',
        'end',
        'doctor_num',
        'clinic_num',
		'patient_num',
		'bill_exam_num',
		'state',
		'sent_by_sms',
		'sent_by_email',
		'remark',
		'is_exceptional',
        'user_num',
        'visit_num',		
		'active',
		'onlineform_sent_email',
		'onlineform_sent_sms'
		
    ];    
}
