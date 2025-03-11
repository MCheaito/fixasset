<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentPlansCategories extends Model
{
    use HasFactory;
	protected $table = 'tbl_treatment_plans_categories';
	protected $fillable = [
        'clinic_num',
        'user_num',
		'category_en',
		'category_fr',
		'status'
    ];    
}
