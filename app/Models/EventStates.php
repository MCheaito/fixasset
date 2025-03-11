<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventStates extends Model
{
    use HasFactory;
	protected $table = 'tbl_events_states';
	protected $fillable = [
        'state_en',
        'state_fr',		
        'display_icon',
		'display_order',
        'active'
		
    ];    
}
