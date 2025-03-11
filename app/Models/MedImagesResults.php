<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedImagesResults extends Model
{
    use HasFactory;
	protected $table = 'tbl_medical_images_data';
	protected $fillable = [
        'visit_num',
		'equip_type',
		'remarks',
		'image_name',
		'image_path',
		'thumb_image_path',
		'clinic_num',
		'patient_num',
		'user_num',
        'active'
		
    ];    
}
