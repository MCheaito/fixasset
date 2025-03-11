<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblPaymentStripe extends Model
{
    use HasFactory;
	protected $table = 'tbl_stripe_payment';
	protected $fillable = [ 
			'user_num',
			'clinic_num',
			'patient_num',
			'doctor_num',
			'event_id',
			'exam_num',
			'price',
			'stripe_id',
			'bill_num',
			'active',
			'created_at',
			'pay_date'
   ];    
}
