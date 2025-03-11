<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblDoctorsSpecia extends Model
{
    use HasFactory;
	protected $table = 'tbl_doctors_specia';

    protected $fillable = [
        'id',
		'name_fr',
		'name_en',
		'active'		
    ];
    
   
}
