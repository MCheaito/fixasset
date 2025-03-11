<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamsNames extends Model
{
    use HasFactory;
	protected $table = 'tbl_exams_names';
	protected $fillable = [
        'code',
        'english',
        'french',
        'active',
		'zone_num'
    ];    
}
