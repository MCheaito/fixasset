<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $table = 'tbl_clinics';
	protected $fillable = [
        'id',
        'clinic_user_num',
		'kind',
		'region_name',
		'province_code',
		'country_code',
		'full_name',
		'full_address',
		'appt_nb',
		'city_name',
		'state',
		'zip_code',
		'telephone',
		'alternate_phone1',
		'alternate_phone2',
		'fax',
		'email',
		'remarks',
		'open_days',
		'bill_serial_code',
		'bill_sequence_num',
		'bill_tax',
		'inv_serial_code',
		'inv_sequence_num',
		'active',
		'user_num',
		'LBL_Dollar',
		'pricel','priced','pricee',
		'has_prices','prices',
		'sms_body','email_head','email_body',
		'whatsapp','website'
        
  ];
}
