<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table = 'tbl_doctors';
	protected $fillable = [
        'user_num', 
		'doctor_user_num', 
		'profile_photo_num', 
		'sign_num', 
		'code', 
		'type', 
		'specia', 
		'first_name', 
		'middle_name', 
		'last_name', 
		'license_num', 
		'gender', 
		'address', 
		'appt_nb', 
		'city', 
		'state', 
		'zip_code', 
		'email', 
		'tel', 
		'tel2', 
		'tel3', 
		'fax', 
		'show_sign', 
		'show_sign_for_clinic', 
		'remarks', 
		'date', 
		'pricel',
        'priced',
        'pricee',
        'has_prices',
        'prices', 		
		'active'
		
  ];
}
