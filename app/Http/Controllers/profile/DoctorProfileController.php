<?php
/*
* DEV APP
* Created date : 4-10-2022
*  
*
*/
namespace App\Http\Controllers\profile;
use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Region;
use App\Models\DoctorProfilePhoto;
use App\Models\DoctorSignature;
use App\Models\RLDoctorExams;
use App\Models\ExamsNames;
use App\Models\TblBillCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Alert;
use DB;
use Image;
use UserHelper;
use Illuminate\Validation\Rule;


class DoctorProfileController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index($lang) 
    {
     $userID = auth()->user()->id;
	 $docUser=Doctor::join('users','users.id','=','tbl_doctors.doctor_user_num')
					 ->where('tbl_doctors.doctor_user_num',$userID)->first([      
								'tbl_doctors.*',
								'users.email as user_email',
								'users.username as username',
								'users.password as password',
								'users.fname as user_fname',
								'users.lname as user_lname']);
	 
	 $imgProfile = DoctorProfilePhoto::where('id',$docUser->profile_photo_num)->first();
	 
	 $docSignature = DoctorSignature::where('id',$docUser->sign_num)->where('active','O')->first();
	 $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
		$currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
		$lbl_usd = isset($currUSD)? $currUSD->price:15000;
        $lbl_euro = isset($currEURO)? $currEURO->price:15000;
       
	 
	return view('profile.doctor.index',compact('docUser'))->with([ 
																	'imgProfile' => $imgProfile,
																	'docSignature' =>$docSignature,
																	'lbl_usd'=>$lbl_rate,
											                        'lbl_euro'=>$lbl_euro
                     																					
																  ]);	
	
	}


public function show_profile($lang,Request $request) 
    {
	$id = $request->doctor_num;
    
	 $doctor=Doctor::where('tbl_doctors.id',$id)->first(['tbl_doctors.*']);
    if(isset($doctor->profile_photo_num))
	{ 
    $imgProfile=DoctorProfilePhoto::where('id',$doctor->profile_photo_num)->first(); 
    $path= url('furl/'.$imgProfile->path);
	}else{  	
	$path= url('furl/default_profile_photo/noimage.png'); 
	}
    
	$profession = (isset($doctor->specia) && $doctor->specia!='')?' , '.$doctor->specia:'';
	$mname = (isset($doctor->middle_name) && $doctor->middle_name!='')?$doctor->middle_name:'';
	if($mname==''){
	$name = $doctor->first_name.' '.$doctor->last_name.$profession;
	}else{
	$name = $doctor->first_name.' '.$mname.' '.$doctor->last_name.$profession;	
	}
	
	$code = $doctor->code;
	$pricel = isset($doctor->pricel) && $doctor->pricel!=''?$doctor->pricel:'0';
	$priced = isset($doctor->priced) && $doctor->priced!=''?$doctor->priced:'0';
	$pricee = isset($doctor->pricee) && $doctor->pricee!=''?$doctor->pricee:'0';
	$tel= isset($doctor->tel) && $doctor->tel!=''?$doctor->tel:NULL;
	$tel2= isset($doctor->tel2) && $doctor->tel2!=''?$doctor->tel2:NULL;
	$tel3= isset($doctor->tel3) && $doctor->tel3!=''?$doctor->tel3:NULL;
	$address = isset($doctor->address) && $doctor->address!=''? $doctor->address:NULL;
	$fax = isset($doctor->fax) && $doctor->fax!=''?$doctor->fax:NULL;
	$email = isset($doctor->email) && $doctor->email!='' ?$doctor->email:NULL;
	$remark = isset($doctor->remarks) && $doctor->remarks !=''?$doctor->remarks:NULL;
	
	$html_persos='<div class="text-center"><img class="profile-user-img img-fluid img-circle elevation-2" src="'.$path.'" alt="User profile picture"></div>';
	$html_persos.='<table class="table table-bordered table-hover" style="width:100%;">';
	$html_persos.='<tr><th>'.__("Code").'</th><td>'.$code.'</td></tr>';
	$html_persos.='<tr><th>'.__("Complete Name").'</th><td>'.$name.'</td></tr>';
	$html_persos.='<tr><th>'.__("PriceLBP").'</th><td>'.$pricel.'</td></tr>';
	$html_persos.='<tr><th>'.__("Price$").'</th><td>'.$priced.'</td></tr>';
	$html_persos.='<tr><th>'.__("Price€").'</th><td>'.$pricee.'</td></tr>';
	if(isset($tel)) { $html_persos.='<tr><th>'.__("Phone").'</th><td>'.$tel.'</td></tr>'; }
	if(isset($tel2)) { $html_persos.='<tr><th>'.__("Phone2").'</th><td>'.$tel2.'</td></tr>'; }
	if(isset($tel3)) { $html_persos.='<tr><th>'.__("Phone3").'</th><td>'.$tel3.'</td></tr>'; }
    if(isset($address)) { $html_persos.='<tr><th>'.__("Address").'</th><td>'.$address.'</td></tr>'; }
	if(isset($fax)) { $html_persos.='<tr><th>'.__("Fax Nb").'</th><td>'.$fax.'</td></tr>'; }
	if(isset($email)) { $html_persos.='<tr><th>'.__("Email").'</th><td>'.$email.'</td></tr>'; }
	if(isset($remark)) { $html_persos.='<th>'.__("Remarks").'</th><td>'.$remark.'</td></tr>';  }
	$html_persos.='</table>';
	
	
	
	return response()->json(['html_persos'=>$html_persos]);	
		
	}
	
       /**

     * Upload user profile signature

     *

     * @return response()

     */

    public function upload_signature($lang,Request $request)

    {
		
		$user_num = auth()->user()->id;
		$doc=Doctor::where('doctor_user_num',$user_num)->first();
		
		//New signature exists then add it 		
		if($request->signed !="" && $request->signed !=NULL){
		
		$folderPath = public_path('/custom/profile_signatures/');
        $image_parts = explode(";base64,", $request->signed);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $signature = uniqid() .'_'. date('YmdHi').'.'.$image_type; 
        $file = $folderPath . $signature;
        file_put_contents($file, $image_base64);       
		$public_path='/public/custom/profile_signatures/'.$signature;
		DoctorSignature::where('id',$doc->sign_num)->update(['active'=>'N','user_num'=>$user_num]);
		
		$id_sig=DoctorSignature::create([
							   'path' => $public_path,
							   'user_num'=>auth()->user()->id,
							   'active' => 'O'])->id;
		
		Doctor::where('id',$doc->id)->update(['sign_num'=>$id_sig,'user_num'=>$user_num]);
		}
		 
				
		 $show_sign = ($request->chkSignature=='on')?'true':'false';
	     $show_sign_for_clinic = ($request->chkClinAut=='on')?'true':'false';
		 
		Doctor::where('id',$doc->id)->update(['user_num'=>$user_num,'show_sign'=>$show_sign,'show_sign_for_clinic'=>$show_sign_for_clinic]);
        
        

									   		 
		
		$msg= __('Updated successfully');
		Alert::toast($msg, 'success');
    	return back();
    }
    
	   
 
public function update_info($lang,Request $request,$id) {
	  
	 
	 
	 if( $request->has('update_persos') ){	 
			            $request->validate([
											'first_name' => ['required', 'string', 'max:255'],
											'last_name' => ['required', 'string', 'max:255'],
											'code' => ['required','string','unique:tbl_doctors'],
											'email' => ['nullable','string', 'email', 'max:255','unique:tbl_doctors'],
											'tel' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
											'tel2' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
											'tel3' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
                                            'fax' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
											'pricel'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/'],
											'priced'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/'],
											'pricee'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/']
                                        ],[
										
										  'code.required' => __('Please fill your code'),
										  'code.unique' => __('Code already exists for another resource'),
										  'first_name.required' => __('Please fill your first name'),
										  'last_name.required' => __('Please fill your last name'),
										  'email.unique' => __('Email already exists'),
			                              'email.email' => __('Email is invalid'),
										  'tel.regex' =>__('Enter a phone number in this format: 00-000000'),
										  'tel2.regex' =>__('Enter a phone number in this format: 00-000000'),
										  'tel3.regex' =>__('Enter a phone number in this format: 00-000000'),
										  'fax.regex' => __('Enter fax number in this format: 00-0000000'),
										  'pricel.regex' => __('Enter price LBP in this format: 0.00'),
										  'priced.regex' => __('Enter price $ in this format: 0.00'),
										  'pricee.regex' => __('Enter price euro in this format: 0.00'),
										]); 
			 
				
				 $gender = $request->gender;
				 Doctor::where('id',$id)->update([
									   'first_name' => strtoupper($request->first_name),
									   'middle_name' => strtoupper($request->middle_name),
									   'last_name' => strtoupper($request->last_name),  
									   'code' => $request->code,
									   'type' => $request->type,
									   'specia' => strtoupper($request->specia),
									   'email' => $request->email,
									   'gender'=>$request->gender,
									   'tel' => str_replace("-","",$request->tel),
							           'tel2'=>str_replace("-","",$request->tel2),
							           'tel3'=>str_replace("-","",$request->tel3),
									   'address'=>$request->address,
									   'appt_nb'=>$request->appt_nb,
									   'city'=>$request->city,
									   'state'=>$request->state,
									   'zip_code'=>$request->zip_code,
									   'fax' => str_replace("-","",$request->fax),
									   'pricel' => $request->pricel,
									   'priced' => $request->priced,
									   'pricee' => $request->pricee,
									   'remarks' => $request->remarks,
									   'user_num' => auth()->user()->id
										]);
				 
				 //update pro user data
				 
				 $email = NULL;
				 $doc = Doctor::where('id',$id)->first();
				$request->validate( [
							'email' => ['nullable','string', 'email', 'max:255',Rule::unique('users')->ignore($doc->doctor_user_num)]
							],[
							
							'email.unique' => __('Email already exists'),
							'email.email' => __('Email is invalid!')
							
							]);			
				 if(isset($request->email))
					$email = $request->email;
				 
					User::where('id',auth()->user()->id)->update([
													   'fname' => $request->first_name,
													   'lname' => $request->last_name,
													   'email' => $email
													   ]);
					Doctor::where('id',$id)->update(['email' => $email ]);
				
				 
	 }
     
	   
	  // redirect user
	  $msg=__('Info Updated Successfully.');  	
	   Alert::toast($msg, 'success');
	   return back();
	 
   }
   
   
   public function update_password($lang,Request $request) {
	  $user_num = auth()->user()->id; 
	
	if($request->file('image')){
				
			      $request->validate([ 
                   'image' => 'required|image|nullable|mimes:jpeg,png,jpg,gif|max:2048',
				   'password' => 'nullable|string|min:6|confirmed',
				   'user_fname' =>'required|string',
				   'user_lname' =>'required|string',
				   'profileUser'=>'required|string'
                    ],[
					'image.required' => __('Please choose an image'),
			        'image.mimes' => __('Please insert image only'),
			        'image.max'   => __('Image should be less than 2 MB'),
					'password.min' => __('Password must be at least 6 characters'),
		            'password.confirmed' => __('Password confirmation does not match'),
					'user_fname.required' => __('Please fill your first name'),
					'user_lname.required' => __('Please fill your last name'),
					'profileUser.required' => __('Please fill your username')
					]);
	}else{					
	   
	$request->validate([
		'password' =>  'nullable|string|min:6|confirmed',
		'user_fname' =>'required|string',
		'user_lname' =>'required|string',
		'profileUser'=>'required|string'
		],
		[
		'password.min' => __('Password must be at least 6 characters'),
		'password.confirmed' => __('Password confirmation does not match'),
		'user_fname.required' => __('Please fill your first name'),
		'user_lname.required' => __('Please fill your last name'),
		'profileUser.required' => __('Please fill your username')
		]			
		);
	   }
		
		//dd($validator->errors()->all());
		
	
		 if($request->file('image')){	
					$filenameWithExt = $request->file('image')->getClientOriginalName ();
					$filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); // Get Filename
					$extension = $request->file('image')->getClientOriginalExtension();// Get just Extension
					$fileNameToStore = $filename. '_'. date('YmdHi').'.'.$extension;// Filename To store 
					$destinationPath = public_path('/custom/profile_images/');
					$path = $request->file('image');
					$path->move($destinationPath,  $fileNameToStore); // Upload Image
					$target_file=$destinationPath.$fileNameToStore;
					$public_path='/public/custom/profile_images/'.$fileNameToStore;	 
		   			$image = Image::make($target_file)->orientate();
					if(($image->width() <= 100) and ($image->height() <= 100))  {
							
						}else{
                        
						$image->resize(100, 100, function ($constraint) {
                                  $constraint->aspectRatio();

									// if you need to prevent upsizing, you can add:
								  $constraint->upsize();
                                  })->save();

						}
				$doc=Doctor::where('doctor_user_num',$user_num)->first();
		 	    DoctorProfilePhoto::where('id',$doc->profile_photo_num)->update(['active'=>'N','user_num'=>auth()->user()->id]);
		  	    $photoID=DoctorProfilePhoto::create([
								'path'=>$public_path,
								'user_num'=>$user_num,
								'active'=>'O'
							])->id;
				
		        Doctor::where('id',$doc->id)->update(['profile_photo_num'=>$photoID,'user_num'=>$user_num]);							
		        }	
			
			  if($request->password != NULL && $request->password != ""){
				  User::where('id',$user_num)->update(['password' => Hash::make($request->password)]);
				  }
			  
			  
				  User::where('id',$user_num)->update(['fname'=>$request->user_fname,'lname'=>$request->user_lname,'username' => $request->profileUser]);
				  	  
	   
			 $msg=__('Updated successfully');  	
			 Alert::toast($msg, 'success');
			 return back();
	      
		
   }

 
}


