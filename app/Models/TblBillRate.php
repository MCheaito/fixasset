<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBillRate extends Model
{
    use HasFactory;
	protected $table = 'tbl_bill_rate';

    protected $fillable = [
      'name',
	  'tvq',
	  'tvs',
	  'tva',
	  'lbl_rate',
	  'user_num',
	  'status'
    ];
    
   
}
