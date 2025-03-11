<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRemindersKey extends Model
{
    use HasFactory;
	protected $table = 'tbl_events_reminders_key';
	protected $fillable = [
        'event_num', 
        'secret_key',
        'remind_by',
        'user_num',		
		'active'
		
    ];    
}
