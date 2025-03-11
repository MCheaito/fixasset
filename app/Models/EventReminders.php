<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventReminders extends Model
{
    use HasFactory;
	protected $table = 'tbl_events_reminders';
	protected $fillable = [
        'type',
		'pat_remind_all',
		'start', 
        'end_before',
        'bill_exam_num',
        'clinic_num',
        'sms_body_en',
		'sms_body_fr',
		'email_body_en',
		'email_head_en',
		'email_body_fr',
		'email_head_fr',
        'remind_by_sms',
        'remind_by_email',
        'remind_before',
        'user_num',		
		'active'
		
    ];    
}
