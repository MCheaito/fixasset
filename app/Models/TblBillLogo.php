<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBillLogo extends Model
{
    use HasFactory;
	protected $table = 'tbl_bill_logo';
					
    protected $fillable = [
   	   'clinic_num',
	   'clinic_name',
	   'logo_path',
	   'user_num',
	   'status'
    ];
    
   
}
