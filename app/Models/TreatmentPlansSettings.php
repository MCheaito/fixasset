<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentPlansSettings extends Model
{
    use HasFactory;
	protected $table = 'tbl_treatment_plans_settings';
	protected $fillable = [
        'clinic_num',
        'user_num',
		'category_num',
		'title_en',
		'title_fr',
		'remark_en',
		'remark_fr',
        'status'
    ];    
}
