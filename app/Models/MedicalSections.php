<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalSections extends Model
{
    use HasFactory;
	protected $table = 'tbl_medical_sections';
	protected $fillable = [
        'clinic_num',
        'user_num',
		'section_en',
		'section_fr',
        'status'
    ];    
}
