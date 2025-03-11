<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationsList extends Model
{
    use HasFactory;
	protected $table = 'tbl_medications';
	protected $fillable = [
        'clinic_num',
        'user_num',
		'section_num',
		'title_en',
		'title_fr',
		'serial_num',
        'status'
    ];    
}
