<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TMResults extends Model
{
    use HasFactory;
	protected $table = 'tbl_tonometer';
	protected $fillable = [
         'user_num',
		 'visit_num',
		 'clinic_num',
		 'patient_num',
		 'equip_type', 
		 'right_iop_pa',
		 'right_iop_mmhg',
		 'left_iop_pa',
		 'left_iop_mmhg',
		 'notes', 
		 'active',
		 'is_goldmann',
		 'right_correction',
		 'left_correction'
		
    ];    
}
