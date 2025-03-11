<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtBranch extends Model
{
    protected $table = 'tbl_external_labs';
	protected $fillable = [
        'id',
        'clinic_num',
		'kind',
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
		'remarks',
		'status',
		'user_num'
        
  ];
}
