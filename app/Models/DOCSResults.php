<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DOCSResults extends Model
{
    use HasFactory;
	protected $table = 'tbl_docs_data';
	protected $fillable = [
        'order_id',
		'title',
		'notes',
		'name',
		'path',
		'user_num',
        'active'
		
    ];    
}
