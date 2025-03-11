<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBillAssurance extends Model
{
    use HasFactory;
	protected $table = 'tbl_bill_assurance';
									  				 	 
    protected $fillable = [
      'assur_date',
	  'first_name',
	  'middle_name',
	  'last_name',
	  'birthdate',
	  'gender',
	  'assur_id_mode',
	  'assur_name',
	  'assur_relation',
	  'assur_nb',
	  'member_nb',
	  'assur_price',
	  'assur_status',
	  'user_num',
	  'user_type'
    ];
    
   
}
