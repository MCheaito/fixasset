<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtLab extends Model
{
    protected $table = 'tbl_external_labs';
	protected $fillable = [
        'id',
		'code',
        'clinic_num',
		'percentage',
		'region_name',
		'full_name',
		'full_address',
		'appt_nb',
		'city',
		'state',
		'zip_code',
		'telephone',
		'alternate_phone1',
		'alternate_phone2',
		'fax',
		'email',
		'email2',
		'email3',
		'remarks',
		'status',
		'pricel',
        'priced',
        'pricee',
		'prices',
		'has_prices',
		'lab_user_num',
		'category',
		'rate',
		'user_num',
		'is_valid',
        'status'		
  ];
}
