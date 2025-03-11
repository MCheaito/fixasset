<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtIns extends Model
{
    protected $table = 'tbl_referred_labs';
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
		'alternative_phone1',
		'alternative_phone2',
		'fax',
		'email',
		'remarks',
		'status',
		'pricel',
        'priced',
        'pricee',
		'prices',
		'has_prices',
		'rate',
		'user_num',
		'status'
        
  ];
}
