<?php

namespace App\Http\Controllers\stripe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Models\Patient;
use Exception;
use Session;
use Stripe;
use Crypt;
use DB;
use Carbon\Carbon;

class PaymentController extends Controller

{
	
	
	public function stripe($lang,$id)
    {
        //this is the encrypted event id for patient
		$event_id = Crypt::decrypt($id);
		//dd($event_id);
		$data_event = DB::table('tbl_calendar_events')->where('active','O')->where('id',$event_id)->first();
 	    $data_exam = DB::table('tbl_bill_amounts')->where('status','O')->where('id',$data_event->bill_exam_num)->first();
		if($lang=='fr'){
		$CodeExam=$data_exam->am_code.'-'.$data_exam->name_fr;
		}else{
		$CodeExam=$data_exam->am_code.'-'.$data_exam->name_eng;	
		}
		$ExamDate = Carbon::parse($data_event->start)->format('Y-m-d H:i');
		$PriceExam=$data_exam->sell_amount;
		$patient = Patient::find($data_event->patient_num);
       return view('stripe.payment')->with(["event_id"=>$event_id,
	                                        "ExamDate"=>$ExamDate,
											"patient"=>$patient,
											"CodeExam"=>$CodeExam,
											"PriceExam"=>$PriceExam]);
    }

    public function stripePost($lang,Request $request)
    {
		
	 $event_id = $request->input('event_id');
	 $data_event = DB::table('tbl_calendar_events')->where('active','O')->where('id',$event_id)->first();
 	 $data_exam = DB::table('tbl_bill_amounts')->where('status','O')->where('id',$data_event->bill_exam_num)->first();
     $user_num = auth()->user()->id;
  	 $data_patient = DB::table('tbl_patients')->where('status','O')->where('id',$data_event->patient_num)->first();
	 
	 // stripe customer payment token
     $stripe_token = $request->get('stripeToken');
	 //dd($stripe_token);
	 Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
     // charge customer with your amount
     Stripe\Charge::create(array(
                          "amount" =>$data_exam->sell_amount*100,
                          "currency" => "CAD",
                          "source" => $stripe_token,
                          "description" => 'Patient Name:'.$data_patient->first_name.' '.$data_patient->last_name,                                               
                    ));
	
	
		DB::table('tbl_stripe_payment')->insert([
		   'event_id'=>$event_id,
		   'user_num'=>$user_num,
		   'clinic_num'=>$data_event->clinic_num,
		   'patient_num'=>$data_event->patient_num,
		   'doctor_num'=>$data_event->doctor_num,
		   'exam_num'=>$data_event->bill_exam_num,
		   'price'=>$data_exam->sell_amount,
		   'pay_date'=>Carbon::now()->format('Y-m-d H:i'),
		   'active'=>'O'
		  ]);
        
		
        Session::flash('success', __('Payment Successful'));
	
	    return back();
    }
	
	
	
}
