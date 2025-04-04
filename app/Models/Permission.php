<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
	protected $table = 'users_permissions';

    protected $fillable = [
        'uid',
		'profile_permissions',
		'user_num',
		'clinic_num',
		'clients',
		'active'
    ];
}
