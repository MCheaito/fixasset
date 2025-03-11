<?php

namespace App\Http\Controllers\registration;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\SettingMail;
//use App\Models\TblVisits;
use DB;
use Alert;

class RegistrationPatientController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('guest');
    }
	
	public function showRegistration()
    {
	   $clinic=Clinic::where('active','O')->get(['id','full_name']);
       return view('registration.inforegister')->with(['clinic'=>$clinic]);
    }
  
	public function confirmB($lang,Request $request)
    {
     $key = $request->key;
	 $data = DB::table('tbl_patients_hashKeys')->where('active','O')->where('secret_key',$key)->first();
	 if(isset($data)){
	   return view('registration.confirmation');
	 }else{
		 abort(403);
	 }
        
		
    }
   
public function registerB($lang,Request $request)
    {
       	   
	   $request->validate([
           'fname' => ['required', 'string', 'max:255'],
           'lname' => ['required', 'string', 'max:255'],  
           'tel' => ['required','regex:/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/'],
		   'email' => ['required','email'],
		   'password' => ['required', 'string', 'min:6', 'confirmed'],
		   'birthdate' => ['required']
		    ],[
			'fname.required' => __('Please fill your first name'),
			'lname.required' => __('Please fill your last name'),
			'tel.required' => __('Please fill your phone'),
			'tel.regex' => __('Your phone number must be of format 000-000-0000'),
			'email.required' => __('Please fill your email address'),
			'email.email' => __('Please fill a valid email address'),
			'password.required' => __('Please fill your password'),
			'password.confirmed' => __('Password confirmation does not match'),
			'password.min'=>__('The password must be at least 6 characters'),
			'birthdate.required'=>__('Please fill your birth date')
			
			]);
	   
  //conditions part one name or email in table users
  if($this->chk_user($request->fname,$request->lname,$request->email,$request->username)){
	return back()->withinput()->withErrors(['msg' => __("An account already exists , please use your current credentials to login")]);  
  }  

  
  //patient exists in patient table then create user for it
   $fname = trim(strtoupper($request->fname));
   $lname = trim(strtoupper($request->lname));
   $email = trim(strtoupper($request->email));
   $tel = $request->tel;
   $dob = $request->birthdate;
   $clinic_num = trim(strtoupper($request->clinic_num));
   
    //patient match by first_name and last_name
	$p1 = Patient::whereRaw('TRIM(UPPER(clinic_num)) = (?)', [$clinic_num])
	               ->whereRaw('TRIM(UPPER(first_name)) = (?)', [$fname])
				   ->whereRaw('TRIM(UPPER(last_name)) = (?)', [$lname])
				   ->pluck('id')->toArray();
    
	//patient match by email
	$p2 =  Patient::whereRaw('TRIM(UPPER(clinic_num)) = (?)', [$clinic_num])
					->whereRaw('TRIM(UPPER(email)) = (?)', [$email])
				    ->pluck('id')->toArray();
					
	//patient does not exist at all				
	if(count($p1)==0 && count($p2)==0){
	 	$patient_id = Patient::create([
            'clinic_num'=>$request->clinic_num,
            'first_name'=>strtoupper($request->fname),
            'last_name'=>strtoupper($request->lname),
            'birthdate'=>$request->birthdate,
            'cell_phone'=>$request->tel,
            'sex'=>$request->gender,
            'status' => 'O',
			'patient_kind'=>'external',
            'email'=>$request->email	
			])->id;
			
		$user_id = User::create([
            'fname' => strtoupper($request->fname),
			'lname' => strtoupper($request->lname),
            'email' => $request->email,
			'username' => $request->email,
			'clinic_num' => $request->clinic_num,
			'username' => $request->email,
			'type' =>'4',
			'password' => Hash::make($request->password),
			'patient_kind'=>'external',
			'active' => 'N'
            ])->id;
			
		Patient::where('id',$patient_id)->update(['patient_user_num'=>$user_id]);	
	}else{
		$pat_id = 0;
		$cnt_inactive =0;
		if(count($p1)>0){
		 foreach($p1 as $v){
			$patient = Patient::find($v);
          	if($this->chk_patient1($request,$patient) && $patient->status=='O'){ $pat_id = $patient->id; break; }
			if($this->chk_patient1($request,$patient) && $patient->status=='N'){ $cnt_inactive++; }
		 }
	    }
	  
	  //patient do not match by first_name and last_name then match by email
	  if(count($p2)>0 && $pat_id==0){
		 foreach($p2 as $v){
			$patient = Patient::find($v);
			if($this->chk_patient2($request,$patient) && $patient->status=='O'){$pat_id = $patient->id;	break;}
			if($this->chk_patient2($request,$patient) && $patient->status=='N'){ $cnt_inactive++; }
		 }
	    }
		
	   switch($pat_id){
	     case 0:
		   if($cnt_inactive>0){
			 $msg = __("Patient account is suspended, please call the lab for assistance");
  		   }else{
			 $msg = __("Patient exists but the coordinates do not match")." , ".__("please make sure you fill all the information.")." ".__("For  more assistance please call the lab.");
  		   }
			   
		   return back()->withinput()->withErrors(['msg' => $msg]);  
         break;
         default:
           	$user_id = User::create([
            'fname' => strtoupper($request->fname),
			'lname' => strtoupper($request->lname),
            'email' => $request->email,
			'username' => $request->email,
			'clinic_num' => $request->clinic_num,
			'username' => $request->email,
			'type' =>'4',
			'password' => Hash::make($request->password),
			'patient_kind'=>'external',
			'active' => 'N'
            ])->id;
          		  
		  //link patient to table users
	      Patient::where('id',$pat_id)->update(['patient_user_num'=>$user_id]);	 
		  } 	
		
	}
	
	  //send mail 	
	  $to=$request->email;
	  //prepare hash key
	  $unhashedKey = bin2hex ( openssl_random_pseudo_bytes ( 50 ) );
      $hashedKey = hash ( 'sha256', $unhashedKey );
      $hashedKey = str_replace("'","",$hashedKey); 
	  $pat = Patient::where('patient_user_num',$user_id)->first();

	  //create a hash key for patient
	  DB::table('tbl_patients_hashKeys')->insert([
	   'patient_id'=>$pat->id,
	   'patient_user_id'=>$user_id,
	   'secret_key'=>$hashedKey,
	   'active'=>'O'
	  ]);
	  
	  $clinic = Clinic::find($request->clinic_num);
	  $link = route('inforegister_confirm',['locale'=>$lang,'key'=>$hashedKey]);
	  $from = $clinic->full_name;
	  $reply_to_name = __("No reply").','.$clinic->full_name;
	  $reply_to_address = isset($clinic->email)? $clinic->email:'noreply@email.com';
	  $pat_name = $request->fname.' '.$request->lname;
	  $msg1 = 'Hello'.' '.$pat_name.' ,';
	  $msg2 =__('Please click on the link below to confirm your account').' . ';
	  
	  $details = [        'msg1' => $msg1,
	                      'msg2'=>$msg2,
	                      'link'=>$link,
			              'branch_name'=>$clinic->full_name,
						  'branch_address'=>$clinic->full_address,
						  'branch_tel'=>$clinic->telephone,
				          'branch_fax'=>$clinic->fax,
						  'branch_email'=>$clinic->email,
						  'from'=>$from,
						  'reply_to_name'=>$reply_to_name,
						  'reply_to_address'=>$reply_to_address
				];
	    
		$subject = __('Account Confirmation');  
		
		Mail::to($to)->send(new SettingMail($details,$subject,'patient'));		
		
        return redirect()->route('confirmB',['locale'=>$lang,'key'=>$hashedKey]);
   
}
	
 public function inforegister_confirm($lang,Request $request){
	 $key = $request->key;
	 $data = DB::table('tbl_patients_hashKeys')->where('active','O')->where('secret_key',$key)->first();
	 if(isset($data)){
	   User::where('id',$data->patient_user_id)->update(['active'=>'O']);
	   DB::table('tbl_patients_hashKeys')->where('id',$data->id)->delete();
	   return view('registration.confirm_account')->with(['state'=>'Y']);
	 }else{
		 return view('registration.confirm_account')->with(['state'=>'N']);
	 }
}

function chk_user($fname,$lname,$email,$username){
	 $fname = trim(strtoupper($fname));
	 $lname = trim(strtoupper($lname));
	 $email = trim(strtoupper($email));
	 $username = trim(strtoupper($username));
	 
	 $u1 = User::where('active','O')->whereRaw('TRIM(UPPER(fname)) = (?)', [$fname])
	       ->whereRaw('TRIM(UPPER(lname)) = (?)', [$lname])->get()->count();
	 $u2 = User::where('active','O')->whereRaw('TRIM(UPPER(email)) = (?)', [$email])->get()->count();			   
	 $u3 = User::where('active','O')->whereRaw('TRIM(UPPER(username)) = (?)', [$username])->get()->count();
	 
	 if($u1>0 || $u2>0 || $u3>0){
		 return true;
	 }else{
		 return false;
	 }
}

function chk_patient1($request,$patient){
	$fname = trim(strtoupper($request->fname));
    $lname = trim(strtoupper($request->lname));
    $email = trim(strtoupper($request->email));
    $tel = $request->tel;
    $dob = $request->birthdate;
    $clinic_num = trim(strtoupper($request->clinic_num));
	 
	if( $email==trim(strtoupper($patient->email)) && $tel==$patient->cell_phone
		&& $dob==$patient->birthdate 
		&& isset($patient->email) && isset($patient->cell_phone) && isset($patient->birthdate)
		){
			return true;
		}else{
		    return false;
			}

}

function chk_patient2($request,$patient){	  
	$fname = trim(strtoupper($request->fname));
    $lname = trim(strtoupper($request->lname));
    $email = trim(strtoupper($request->email));
    $tel = $request->tel;
    $dob = $request->birthdate;
    $clinic_num = trim(strtoupper($request->clinic_num)); 
		 
		 if( $fname==trim(strtoupper($patient->first_name)) && $lname==trim(strtoupper($patient->last_name)) 
				&& $tel==$patient->cell_phone && $dob==$patient->birthdate 
				&& isset($patient->first_name) && isset($patient->last_name) && isset($patient->cell_phone) 
				&& isset($patient->birthdate) ){
					return true;
				}else{
					return false;
				}
	
}


function chk_loginuser($ramq,$clinic_num){
	$ramq = trim(strtoupper($ramq));
	$clinic_num = trim(strtoupper($clinic_num));
	$p1=Patient::where('status','O')
	            ->whereRaw('TRIM(UPPER(clinic_num)) = (?)', [$clinic_num])
				->whereRaw('TRIM(UPPER(ramq)) = (?)', [$ramq])
				->where(function($q){
					$q->whereNotNULL('patient_user_num');
					$q->where('patient_user_num','<>',0);
				})
			    ->get()->count();
    if($p1>0){
		 return true;
	 }else{
		 return false;
	 }
}
 
}
