<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamsZones extends Model
{
    use HasFactory;
	protected $table = 'tbl_exams_zones';
	protected $fillable = [
        'english',
        'french',
		'display_order',
        'active'
		
    ];    
}
