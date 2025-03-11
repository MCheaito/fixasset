<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTestsSBacteria extends Model
{
    use HasFactory;
	protected $table = 'tbl_lab_sbacteria';

    protected $fillable = [
        'id',
		'code',
		'descrip',
		'testord',
		'clinic_num',
		'user_num',
		'active'	
    ];
    
   
}
