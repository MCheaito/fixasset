<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTestsGAntibiotic extends Model
{
    use HasFactory;
	protected $table = 'tbl_lab_antibiotic_group';

    protected $fillable = [
        'id',
		'code',
		'descrip',
		'testord',
		'antibiotics',
		'clinic_num',
		'user_num',
		'active'	
    ];
    
   
}
