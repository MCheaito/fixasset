<?php
/*
* DEV APP
* Created date : 27-9-2022
*  
*
*/
namespace App\Http\Controllers\profile;
use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\User;
use App\Models\RLDoctorClinic;
use App\Models\Doctor;
use App\Models\Region;
use Illuminate\Http\Request;
use Alert;
use Validator;
use UserHelper;
use Illuminate\Validation\Rule;
use App\Models\RLClinicsExams;
use App\Models\ExamsNames;
use App\Models\ClinicSMSPack;
use App\Models\TblBillPaymentMode;
use App\Models\TblBillLogo;
use App\Models\TblBillRate;
use App\Models\TblBillCurrency;
use App\Models\ExtLab;
use Image;
use DB;
use Session;
use Storage;
use Illuminate\Support\Facades\Crypt;

class BranchProfileController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	
	
	public function index($lang) 
    {
    
	$user=User::where('id',auth()->user()->id)->first();
	$type = $user->type;
	//default sms and email settings
	$default_email_body = "Hello ,\r\n\r\nPlease find attached the lab results that are sent from *LabName* on *RequestDate* at *RequestTime*.";
    $default_sms_body = "Hello ,\r\nPlease click on the link below to download the lab results that are sent from *LabName* on *RequestDate* at *RequestTime*.\r\n*ResultsPDF*";				
    $default_email_head = "*PatientName*-LAB Results";
	
	
	switch($type){
		//main lab
		case 2:
		     $clinUser=Clinic::find($user->clinic_num);
			
			 $sms_body = isset($clinUser->sms_body) && $clinUser->sms_body!=''?$clinUser->sms_body:$default_sms_body;
			 $email_body = isset($clinUser->email_body) && $clinUser->email_body!=''?$clinUser->email_body:$default_email_body;
			 $email_head = isset($clinUser->email_head) && $clinUser->email_head!=''?$clinUser->email_head:$default_email_head;

			//billing part
			$sms_package = ClinicSMSPack::where('active','O')->where('clinic_num',$clinUser->id)->first();
			$pay_methods = TblBillPaymentMode::where('status','O')->where('clinic_num',$clinUser->id)->get();
			$bill_logo = TblBillLogo::where('status','O')->where('clinic_num',$clinUser->id)->first();
			
			
			$clinic_pay_methods = TblBillPaymentMode::where('status','O')->where('clinic_num',$clinUser->id)->pluck('name_eng')->toArray();
			
			
			$bill_rates = TblBillRate::where('status','O')->get();
			
			$currUSD = DB::table('tbl_bill_currency')->where('abreviation','USD')->first();
			$currEUR = DB::table('tbl_bill_currency')->where('abreviation','EUR')->first();
			$lbl_usd = isset($currUSD)?$currUSD->price:90000;
			$lbl_euro = isset($currEUR)?$currEUR->price:90000;
			//dd($bill_currency);
			return view('profile.branch.index',compact('clinUser'))->with([ 
																			'user_email' => $user->email,
																			'sms_package'=>$sms_package,
																			'pay_methods'=>$pay_methods,
																			'clinic_pay_methods'=>$clinic_pay_methods,
																			'bill_logo'=>$bill_logo,
																			'bill_rates'=> $bill_rates,
																			'lbl_euro'=>$lbl_euro,
																			'lbl_usd'=>$lbl_usd,
																			'edit_profile' =>true,
																			'sms_body'=>$sms_body,
																			'email_body'=>$email_body,
																			'email_head'=>$email_head
																			
																			]);	
		break;
		//external lab
		case 3:
		$clinUser=ExtLab::where('lab_user_num',$user->id)->first();
		$cats = DB::table('tbl_external_labs_categories')->orderBy('id')->get();
		$currUSD = DB::table('tbl_bill_currency')->where('abreviation','USD')->first();
		$currEUR = DB::table('tbl_bill_currency')->where('abreviation','EUR')->first();
		$lbl_usd = isset($currUSD)?$currUSD->price:90000;
		$lbl_euro = isset($currEUR)?$currEUR->price:90000;
		return view('profile.external_lab.index',compact('clinUser'))
		       ->with(['cats'=>$cats,'lbl_euro'=>$lbl_euro,'lbl_usd'=>$lbl_usd]);
		break;
	}
	
    }
	
	
   public function show($lang,$id) 
    {
     $clinUser=Clinic::where('id',$id)->first();
	 
	 $docsClinic =  Doctor::join('tbl_doctors_clinics as doc_clinic','doc_clinic.doctor_num','=','tbl_doctors.id')
						 ->where('doc_clinic.active','O')
                         ->where('doc_clinic.clinic_num',$clinUser->id)
						 ->where('tbl_doctors.active','O')
						 ->get(['tbl_doctors.*']);
	
	 
	  if(auth()->user()->type==2){
		 
		return view('profile.branch.show',compact('clinUser'))->with([ 'docsClinic'=>$docsClinic
																		
																		]);
	                                    }
	 
	 if(auth()->user()->type==1){ 
	  if(auth()->user()->admin_perm=='O'){
		$user=User::where('active','O')->where('clinic_num',$clinUser->id)->first();
		$user_email = isset($user) && isset($user->email)? $user->email:'';
		$admin_perm_user = User::where('admin_perm','O')->first();
	    $admin_perm_doc = Doctor::where('doctor_user_num',$admin_perm_user->id)->first();
	
	    $doctors=Doctor::where('active','O')->orderBy('id','DESC')->get();
		
		//billing part
			$sms_package = ClinicSMSPack::where('active','O')->where('clinic_num',$clinUser->id)->first();
			$pay_methods = TblBillPaymentMode::where('status','O')->where('clinic_num',$clinUser->id)->get();
			$bill_logo = TblBillLogo::where('status','O')->where('clinic_num',$clinUser->id)->first();
			
			
			$clinic_pay_methods = TblBillPaymentMode::where('status','O')->where('clinic_num',$clinUser->id)->pluck('name_eng')->toArray();
			
			$bill_rates = TblBillRate::where('status','O')->get();
			$clinic_bill_rate = TblBillRate::where('id',$clinUser->bill_tax)->where('status','O')->first();
		return view('profile.branch.index',compact('clinUser'))->with([
		                                                            'docsClinic'=>$docsClinic,
																	'doctors' => $doctors,
																	'user_email' => $user_email,
																	'sms_package'=>$sms_package,
																	'pay_methods'=>$pay_methods,
																	'clinic_pay_methods'=>$clinic_pay_methods,
																	'bill_logo'=>$bill_logo,
																	'bill_rates'=> $bill_rates,
																	'clinic_bill_rate'=>$clinic_bill_rate,
																	'show_profile'=>true]);	
	  }else{
	  return view('profile.branch.show',compact('clinUser'))->with([ 
	                                                                'docsClinic'=>$docsClinic
																	]);
												}
	 }																	
   
	}
   public function update_info($lang,Request $request,$id) {
	  $user_type = auth()->user()->type;  	
	//check if clinic has a user id then update email in table users
	if($user_type==2){  
	   
		$validator=Validator::make($request->all(),[
											'full_name' => ['required', 'string', 'max:255'],
											'telephone' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
											'email' => ['nullable','email',Rule::unique('tbl_clinics')->ignore($id)],
											'fax' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
											'whatsapp' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
											'pricel'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/'],
											'priced'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/'],
											'pricee'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/']
                                        ],[
										'full_name.required' => __('Lab name is required'),
										'telephone.regex' => __('Enter a phone number in this format: 00-000000'),
										'whatsapp.regex' => __('Enter a phone number in this format: 00-000000'),
										'fax.regex' => __('Enter fax number in this format: 00-000000'),
										'email.unique' => __('Email already exists for another lab profile'),
										'email.email' => __('Email is invalid'),
										'pricel.regex' => __('Enter price LBP in this format: 0.00'),
										 'priced.regex' => __('Enter price $ in this format: 0.00'),
										  'pricee.regex' => __('Enter price euro in this format: 0.00'),
										
										]);
        if ($validator->fails()){
            return response()->json(['errors'=>$validator->errors()->all()]);
           }										
		   
	    if($validator->passes()){
		 
			  $clinic = Clinic::find($id);
			  if(isset($clinic->clinic_user_num)){
				$count_existing_email = User::where('id','<>',$clinic->clinic_user_num)->where('email',trim($request->email))->get()->count();
				if($count_existing_email){
				  return response()->json(['error_useremail'=>__('Email already exists for another user')]);	
				}
			   User::where('id',$clinic->clinic_user_num)->update(['email'=>$request->email]);
			}
				
			Clinic::where('id',$id)->update([
								   'full_name' => trim($request->full_name),
								   'full_address' => trim($request->full_address),
								   'city_name' => trim($request->city_name),
								   'zip_code' => $request->zip_code,
								   'province_code' => $request->province_code,
								   'country_code' => $request->country_code,
								   'region_name' => $request->region_name,
								   'telephone' =>str_replace("-","",$request->telephone),
								   'fax' => str_replace("-","",$request->fax),
								   'email' => $request->email,
								   'appt_nb'=>$request->appt_nb,
							       'state'=>$request->state,
							       'alternate_phone1'=>str_replace("-","",$request->alternate_phone1),
							       'alternate_phone2'=>str_replace("-","",$request->alternate_phone2),
								   'whatsapp'=>str_replace("-","",$request->whatsapp),
								   'website'=>$request->website,
								   'remarks' => $request->remarks,
								   'pricel' => $request->pricel,
								   'priced' => $request->priced,
								   'pricee' => $request->pricee,
								   'user_num' => auth()->user()->id
								  
								]);
			
			             
		 
			}
	}else{
	
	if($user_type==3){
			  
			  $validator=Validator::make($request->all(),[
											'full_name' => ['required', 'string', 'max:255'],
											'telephone' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
											'email' => ['nullable','email',Rule::unique('tbl_clinics')->ignore($id)],
											'fax' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
											'pricel'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/'],
											'priced'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/'],
											'pricee'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/']
                                        ],[
										 'full_name.required' => __('Lab name is required'),
										 'telephone.regex' => __('Enter a phone number in this format: 00-000000'),
										 'fax.regex' => __('Enter fax number in this format: 00-000000'),
										 'email.unique' => __('Email already exists for another lab profile'),
										 'email.email' => __('Email is invalid'),
										 'pricel.regex' => __('Enter price LBP in this format: 0.00'),
										 'priced.regex' => __('Enter price $ in this format: 0.00'),
										 'pricee.regex' => __('Enter price euro in this format: 0.00'),
										
										]);
										
			if ($validator->fails()){
				return response()->json(['errors'=>$validator->errors()->all()]);
			   }										
			  
			  
			 if($validator->passes()){ 
			   $clinic = ExtLab::find($id);
			   if(isset($clinic->lab_user_num)){
				$count_existing_email = User::where('id','<>',$clinic->lab_user_num)->where('email',trim($request->email))->get()->count();
				if($count_existing_email){
				  return response()->json(['error_useremail'=>__('Email already exists for another user')]);	
				}
			   User::where('id',$clinic->clinic_user_num)->update(['email'=>$request->email]);
			  }
				
			ExtLab::where('id',$id)->update([
								   'full_name' => strtoupper(trim($request->full_name)),
								   'full_address' => trim($request->full_address),
								   'telephone' => $request->telephone,
								   'fax' => $request->fax,
								   'email' => $request->email,
								   'alternate_phone1'=>$request->alternate_phone1,
							       'alternate_phone2'=>$request->alternate_phone2,
								   'remarks' => $request->remarks,
								   'pricel' => $request->pricel,
								   'priced' => $request->priced,
								   'pricee' => $request->pricee,
								   'rate'=>$request->rate,
								   'user_num' => auth()->user()->id
								  
								]);
			
			   }
		 
			}
			
	     }
			
		 $msg=__('Updated Successfully.');
		  
		 return response()->json(['success'=>$msg]);
		 
   }
   
   public function update_schedule($lang,Request $request) {
	   $id = intval(Crypt::decryptString($request->clinic_num));
	   $type = $request->action_type;
	  //dd($id);
	   if($type=='delete_schedule'){
		  Clinic::where('id',$id)->update(['open_hours' => NULL,'user_num' => auth()->user()->id]);
          $msg=__('Deleted successfully');
		  Alert::toast($msg, 'success');
          return response()->json(['success'=>true]);		  
	   }
	  
	  if($type=='save_schedule'){
		   
				$arr=array();
				
				$monday = $tuesday = $wednesday = $thursday = $friday = $saturday = $sunday = null;   
				
				if(null!==$request->start_time0 && null!==$request->end_time0){
					$monday=$request->start_time0."-".$request->end_time0;
				}
				if(null!==$request->start_time1 && null!==$request->end_time1){
					$tuesday=$request->start_time1."-".$request->end_time1;
				}
				if(null!==$request->start_time2 && null!==$request->end_time2){
					$wednesday=$request->start_time2."-".$request->end_time2;
				}
				if(null!==$request->start_time3 && null!==$request->end_time3){
					$thursday=$request->start_time3."-".$request->end_time3;
				}
				if(null!==$request->start_time4 && null!==$request->end_time4){
					$friday=$request->start_time4."-".$request->end_time4;
				}
				if(null!==$request->start_time5 && null!==$request->end_time5){
					$saturday=$request->start_time5."-".$request->end_time5;
				}
				if(null!==$request->start_time6 && null!==$request->end_time6){
					$sunday=$request->start_time6."-".$request->end_time6;
				}
				
				if(null!==$monday or null!==$tuesday or null!==$wednesday 
				   or null!==$thursday or null!==$friday or null!==$saturday or null!==$sunday){   
				
				$arr= array("Monday"=>$monday,
							"Tuesday" => $tuesday,
							"Wednesday" => $wednesday,
							"Thursday" => $thursday,
							"Friday" => $friday,
							"Saturday" => $saturday,
							"Sunday" => $sunday);
				   }  
				 
				 Clinic::where('id',$id)->update(['open_hours' => json_encode($arr),'user_num' => auth()->user()->id]);
				 $msg=__('Schedule updated successfully');
				 Alert::toast($msg, 'success');
				 return response()->json(['success'=>true]);
				 //Alert::toast($msg, 'success');
				 // redirect user
				//return back();
				
	          }
   
   }

   
	
	public function update_exams($lang,Request $request) {
	   $id = $request->clinic_id;
	   
	   if(!empty($request->get('exam'))){
		     //delete old links      
			RLClinicsExams::where('clinic_num',$id)->delete();
			
			//insert new Exams links for clinic
            
            $exams = $request->get('exam');
		    
			
			foreach($exams as $exam){
			
			RLClinicsExams::create([
				'clinic_num'=> $id,
				'exam_num'=> $exam,
				'user_num' => auth()->user()->id
				 ]);
			}
		  }else{
			  //delete old links      
			RLClinicsExams::where('clinic_num',$id)->delete(); 
		  }
		  $msg=__('Exams Updated Successfully.');  	
		 
         Alert::toast($msg, 'success');
		  
		 // redirect user
		 return back();
        }	
		
 

   public function update_professionals($lang,Request $request,$id) {
	   if(!empty($request->get('doctor'))){
		     //delete old links      
			RLDoctorClinic::where('clinic_num',$id)->delete();
			
			//insert new doctors links for clinic	
			$doctors= $request->get('doctor');
			//dd($doctors);
			foreach($doctors as $doctor){
			
			RLDoctorClinic::create([
				'clinic_num'=> $id,
				'doctor_num'=> $doctor,
				'active'=>'O',
				'user_num' => auth()->user()->id
				 ]);
			}
	      }else{
			    //delete old links      
			RLDoctorClinic::where('clinic_num',$id)->delete();
		  }
		  $msg=__('Resource updated successfully');  	
		  
         Alert::toast($msg, 'success');
		  
		 // redirect user
		 return back();
        }
		

public function changeUserClinic_language(Request $request){
	    
		$locale=$request->lang;
		app()->setlocale($locale);
		User::where('id',$request->user_id)->update(['user_language'=>$locale]);
        $url=route('profiles.clinic',app()->getLocale());		
		return response()->json(["success"=>true,"url"=>$url]);
	}   
   
 //billing part
public function save_bill($lang,Request $request){
	$type= $request->type;
	$clinic_id = $request->clinic_id;
	switch($type){
		
		case 'new_sms_balance':
		$sms = ClinicSMSPack::where('clinic_num',$clinic_id)->where('active','O')->first();
		//sms pack defined for clinic
		if(isset($sms)){
		$old_pay = isset($sms->pay_pack) && $sms->pay_pack>0?$sms->pay_pack:0;
		$fixed_pack = isset($sms->current_sms_pack) && $sms->current_sms_pack>0?$sms->current_sms_pack:0;
		$pay_pack = $old_pay+$request->new_pay;
		//$sms_package = $fixed_pack+$pay_pack;
		$pack_id = $sms->id;
		ClinicSMSPack::where('clinic_num',$clinic_id)->where('active','O')->update(['old_sms_pack'=>$old_pay,'pay_pack'=>$pay_pack,'user_num'=>auth()->user()->id]);
		}else{
			
			//create new sms pack for clinic if sms pack not zero
			$pay_pack = $request->new_pay;
			if($pay_pack>=0){
				//$sms_package = 140+$pay_pack;
				$pack_id=ClinicSMSPack::create(['clinic_num'=>$clinic_id,'old_sms_pack'=>0,'current_sms_pack'=>140,'pay_pack'=>$pay_pack,'active'=>'O','user_num'=>auth()->user()->id])->id;
			}
		}
		
		$pack = ClinicSMSPack::find($pack_id);
		$sms_package = $pack->current_sms_pack+$pack->pay_pack;
		$paid = $pack->pay_pack;
		
		return response()->json(["success"=>__("SMS balance saved successfully"),"sms_package"=>$sms_package,"paid"=>$paid]);
		break;
		
		
		case 'new_pay_method':
		$name_eng = $request->name_eng;
		$name_fr = $request->name_fr;
		$insurance = $request->insurance;
		TblBillPaymentMode::create(['name_eng'=>$name_eng,'name_fr'=>$name_fr,'clinic_num'=>$clinic_id,'assurance'=>$insurance,'status'=>'O','user_num'=>auth()->user()->id]);
		$pay_methods = TblBillPaymentMode::where('status','O')->where('clinic_num',$clinic_id)->get();
		$html='';	
		foreach($pay_methods as $m){
		   $selected = ($clinic_id==$m->clinic_num)?'selected':'';
		   $name = ($lang=='en')?$m->name_eng:$m->name_fr;
		   $html.= '<option value="'.$m->id.'" '.$selected.'>'.$name.'</option>';
			  }
		return response()->json(["success"=>__("Pay method saved successfully"),"html"=>$html]);
	  	break;
		case 'new_bill_logo' :
		 $file=$request->file("logoImage");
		 $name = $file->getClientOriginalName();
		 $ext = $file->getClientOriginalExtension();
		 $size= $file->getSize();
		 if(empty($name)){
			 return response()->json(["error"=>__("An error has occured while uploading the file")]);
		 }
		 
		 if($ext !="jpg" && $ext!="jpeg" && $ext!="png" && $ext!="tiff" && $ext!="svg" && $ext!="bmp"){
			 return response()->json(["error"=>__("Please choose an image")]);
		 }
		 if($size>2097152){
            return response()->json(["error"=>__("Image size must be less than 2MB")]);
 		 }
		
		$uid = auth()->user()->id;
		$dir =storage_path('app/7mJ~33/bill_logos');
        $current_file = $clinic_id.'_'.date('Ymd').'_'.uniqid().'.'.$ext;
		 if($file->move($dir, $current_file)){
			 $image = Image::make($dir.'/'.$current_file)->orientate();
				if(($image->width() <= 200) and ($image->height() <= 200))  {
							
						}else{
                        
						$image->resize(200, 200, function ($constraint) {
                                  $constraint->aspectRatio();

									// if you need to prevent upsizing, you can add:
								  $constraint->upsize();
                                  })->save();

						}
		 }
		 
		 $image_path='bill_logos/'.$current_file;
         TblBillLogo::where('clinic_num',$clinic_id)->update(['status'=>'N','user_num'=>auth()->user()->id]);
		 $attachID=TblBillLogo::create([
		   'clinic_num'=>$clinic_id,
		   'clinic_name'=>$name,
		   'logo_path'=> $image_path,
		   'status'=>'O',
		   'user_num'=>auth()->user()->id
		   ])->id;
		 
		 $attach=TblBillLogo::where('id',$attachID)->where('status','O')->first();
         
		 $html =  '<div  id="attach-'.$attach->id.'" class="mb-1" style="border-radius: 4px;border:solid gray 1px;padding:2px 7px;margin:5px 8px 0 0;display:inline-block;">
                                <input type="hidden" name="attachID" id="attachID" value="'.$attach->id.'"/>
								<a  data-fancybox="gallery" data-type="image"  data-caption="'.$attach->clinic_name.'"  data-src="'.url('furl/'.$attach->logo_path).'" href="javascript:;">
								  <img  class="img-fluid mb-2" alt="" src="'.url('furl/'.$attach->logo_path).'"/>
								</a>
                                <i  onclick="getAttach('.$attach->id.')" class="fa fa-times" style="color:rgb(220,53,69); margin-left:2px;cursor:pointer;"></i>
                          </div>';
         
		 return response()->json(["success"=>__("Logo uploaded successfully"),"html"=>$html]);						
		break;
		case 'delete_bill_logo' :
		$attach=TblBillLogo::where('id',$request->attach_id)->where('status','O')->first();
		TblBillLogo::where('id',$request->attach_id)->update(['status'=>'N','user_num'=>auth()->user()->id]);
		Storage::disk('private')->delete($attach->logo_path);
		return response()->json(["success"=>__("Logo deleted successfully")]);	
		break;
		
		case 'update_bill':
		parse_str($request->data, $data);
		if(!empty($data['pay_methods'])){
		 $pay_methods = $data['pay_methods'];
		
		 //delete old pay methods for bill
		 TblBillPaymentMode::whereNotIn('id',$pay_methods)->update(['status'=>'N','user_num'=>auth()->user()->id]);

		}else{
		 //delete all pay methods
		 TblBillPaymentMode::where('status','O')->where('clinic_num',$clinic_id)->update(['status'=>'N','user_num'=>auth()->user()->id]);	

		}
		
        //update choosen bill rate data with new id to keep old data
		$lbl_usd = intval($data['lbl_usd']);
		//$lbl_euro = intval($data['lbl_euro']);
		$currLBP = DB::table('tbl_bill_currency')->where('abreviation','USD')->value('price');
		//$currEURO = DB::table('tbl_bill_currency')->where('abreviation','USD')->value('price');
		if($currLBP!=$lbl_usd){
		 DB::table('tbl_bill_currency')->where('abreviation','USD')->update(['price'=>$lbl_usd]);
		 //update LBP prices for rate is USD
		   $reflabs_ids = DB::table('tbl_referred_labs')->where('rate','USD')->where('status','Y')->where('has_prices','Y')->pluck('id')->toArray();
		   $guarantors_ids = DB::table('tbl_external_labs')->where('rate','USD')->where('status','A')->where('has_prices','Y')->pluck('id')->toArray();
           //dd(count($guarantors_ids));
		   if(count($reflabs_ids)>0){
			  DB::statement('CALL update_referredlabs_lbp(?)', array($lbl_usd));
			  
		   }
		   if(count($guarantors_ids)>0){
			    DB::statement('CALL update_guarantors_lbp(?)', array($lbl_usd));
		   }
		 //update USD prices for rate is LBP
		   $reflabs_ids = DB::table('tbl_referred_labs')->where('rate','LBP')->where('status','Y')->where('has_prices','Y')->pluck('id')->toArray();
		   $guarantors_ids = DB::table('tbl_external_labs')->where('rate','LBP')->where('status','A')->where('has_prices','Y')->pluck('id')->toArray();
           if(count($reflabs_ids)>0){
			 DB::statement('CALL update_referredlabs_dollar(?)', array($lbl_usd));
		   }
		   if(count($guarantors_ids)>0){
			 DB::statement('CALL update_guarantors_dollar(?)', array($lbl_usd));
		   }
		}
		
		//if($currEURO != $lbl_euro){
		  //DB::table('tbl_bill_currency')->where('abreviation','EUR')->update(['price'=>$lbl_euro]);
		//}
		//Doctor::where('type','L')->update(['user_num'=>auth()->user()->id,'pricel'=>1,'priced'=>$lbl_usd,'pricee'=>$lbl_euro]);
		
		//update clinic with new bill rate, bill serial code and bill sequence number
		Clinic::where('id',$clinic_id)->update(['bill_serial_code'=>$data['prefix'],'bill_sequence_num'=>$data['seq'],'user_num'=>auth()->user()->id]);
         		
		Alert::toast(__("Bill updated successfully"), 'success');
		return response()->json(["success"=>__("Bill updated successfully")]);
		break;
	}
} 

//new version of profile
public function show_profile($lang,Request $request){
$id = $request->clinic_num;
$clinic = Clinic::find($id);
$full_name = $clinic->full_name;
$full_address = isset($clinic->full_address)?$clinic->full_address:__('Undefined');
$full_tel = isset($clinic->telephone)?$clinic->telephone:__('Undefined');
$alt_tel1 = isset($clinic->alternate_phone1)?$clinic->alternate_phone1:NULL;
$alt_tel2 = isset($clinic->alternate_phone2)?$clinic->alternate_phone2:NULL;
$full_fax = isset($clinic->fax)?$clinic->fax:NULL;
$full_email = isset($clinic->email)?$clinic->email:NULL;
$full_remarks = isset($clinic->remarks)?$clinic->remarks:NULL;
$whatsapp = isset($clinic->whatsapp)?$clinic->whatsapp:NULL;
$website = isset($clinic->website)?$clinic->website:NULL;

$html_persos='<table class="table table-bordered table-hover" style="width:100%;"><tr><th>'.__("Lab Name").'</th><td>'.$full_name.'</td></tr>';
$html_persos.='<tr><th>'.__("Address").'</th><td>'.$full_address.'</td></tr>';
$html_persos.='<tr><th>'.__("Contact Phone").'</th><td>'.$full_tel.'</td></tr>';
if(isset($alt_tel1) && $alt_tel1!='') { $html_persos.='<tr><th>'.__("Alternate Phone1").'</th><td>'.$alt_tel1.'</td></tr>'; }
if(isset($alt_tel2) && $alt_tel2!='') { $html_persos.='<tr><th>'.__("Alternate Phone2").'</th><td>'.$alt_tel2.'</td></tr>'; }
if(isset($full_fax) && $full_fax!='') { $html_persos.='<tr><th>'.__("Fax Nb").'</th><td>'.$full_fax.'</td></tr>'; }
if(isset($full_email) && $full_email!='') { $html_persos.='<tr><th>'.__("Email").'</th><td>'.$full_email.'</td></tr>'; }
if(isset($whatsapp) && $whatsapp!='') { $html_persos.='<tr><th>'.__("Whatsapp").'</th><td>'.$whatsapp.'</td></tr>'; }
if(isset($website) && $website!='') { $html_persos.='<tr><th>'.__("Website").'</th><td>'.$website.'</td></tr>'; }
if(isset($full_remarks) && $full_remarks!='') { $html_persos.='</tr><tr><th>'.__("Remarks").'</th><td>'.$full_remarks.'</td></tr>';}
$html_persos.='</table>';	

$open_hours = json_decode($clinic->open_hours,true);
$html_schedule='<table class="table table-bordered table-hover" style="width:100%;">';
if(isset($open_hours) && count($open_hours)>0){
foreach($open_hours as $d=>$h)
{
$value = isset($open_hours[$d])?$open_hours[$d]:__('');
$html_schedule.='<tr><th>'.__($d).'</th><td>'.$value.'</td></tr>';
}
}else{
$week_days=array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
foreach($week_days as $day){
   $html_schedule.='<tr><th>'.__($day).'</th><td>'.__("").'</td></tr>';
   }   
}
$html_schedule.='</table>';	


	
return response()->json(['html_persos'=>$html_persos,'html_schedule'=>$html_schedule]);	
}

public function sms_email_setting($lang,Request $request){
	$id = intval(Crypt::decryptString($request->profile_id));
	$user_num = auth()->user()->id;
	
	Clinic::where('id',$id)->update([
	'email_head'=>trim($request->email_head),
	'email_body'=>trim($request->email_body),
	'sms_body'=>trim($request->sms_body),
	'user_num'=>$user_num
	]);
   return response()->json(['success'=>__('Saved Successfully')]);
}
 
}


