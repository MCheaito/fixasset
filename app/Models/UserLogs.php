<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLogs extends Model
{
    use HasFactory;
	 protected $table = 'user_logs';
	   protected $fillable = [
        'user_id',
		'ip_address',
		'countryName',
		'countryCode',
		'regionCode',
		'regionName',
		'cityName',
		'zipCode',
		'latitude',
		'longitude',
		'active'
	     ];
}
