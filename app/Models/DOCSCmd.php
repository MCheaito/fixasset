<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DOCSCmd extends Model
{
    use HasFactory;
	protected $table = 'tbl_docs_cmd';
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
