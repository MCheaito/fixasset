<?php
/*
* DEV APP
* Created date : 23-2-2023
*  
*
*/

namespace App\Http\Controllers\lab\visit;
use App\Http\Controllers\Controller;
use App\Models\TblVisits;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\TreatmentPlansSettings;
use App\Models\PatientTreatmentPlans;
use App\Models\DoctorSignature;
use App\Models\ExtBranch;

use Illuminate\Support\Facades\Mail;
use App\Mail\SettingMailAttach;

use Illuminate\Http\Request;
use Alert;
use PDF;
use Image;
use DB;
use RingCentral\SDK\SDK;

class TreatmentPlansController extends Controller

{
    public function __construct()
    {
        $this->middleware('auth');
    }



public function index($lang,$id)

    {
		 $visit=TblVisits::find($id);
		 
		 $patient_plans = PatientTreatmentPlans::where('visit_num',$visit->id)->where('status','O')->get();
		 
		 $plans= TreatmentPlansSettings::select('tbl_treatment_plans_settings.id','tbl_treatment_plans_settings.clinic_num',
		                                        'tbl_treatment_plans_settings.title_en','tbl_treatment_plans_settings.title_fr',
												'tbl_treatment_plans_settings.remark_en','tbl_treatment_plans_settings.remark_fr',
												'cat.category_en','cat.category_fr')
		                               ->join('tbl_treatment_plans_categories as cat',function($join){
			                             $join->on('cat.id','tbl_treatment_plans_settings.category_num');
										 $join->on('cat.clinic_num','tbl_treatment_plans_settings.clinic_num');
										 })
		                               ->where('tbl_treatment_plans_settings.clinic_num',$visit->clinic_num)
									   ->where('tbl_treatment_plans_settings.status','O')
									   ->where('cat.status','O')->get(); 
		 
		
		 
		 return view('emr.visit.treatment_plans.index')->with(['visit_id'=>$id,'plans'=>$plans,'patient_plans'=>$patient_plans]);
	}
	
	
public function store($lang,Request $request){
	$visit=TblVisits::find($request->visit_num);
	$descriptions = $request->input('description');
	if(!empty($descriptions)){
	  //delete old treatment plans for this visit
	  //PatientTreatmentPlans::where('visit_num',$visit->id)->delete();
	  foreach($descriptions as $desc){	
			PatientTreatmentPlans::create([
			 'visit_num'=>$visit->id,
			 'patient_num' => $visit->patient_num,
			 'clinic_num' => $visit->clinic_num,
			 'doctor_num' => $visit->doctor_num,
			 'status'=>'O',
			 'description'=>$desc,
			 'equip_type'=>'PLANS',
			 'user_num'=>auth()->user()->id
			 ]);
	      }
	  
	}else{
     //delete old treatment plans for this visit
	  //PatientTreatmentPlans::where('visit_num',$visit->id)->delete();
	}	
    $msg=__('Saved successfully');	
	Alert::toast($msg,'success');
    return back();
}

public function update($lang,Request $request){
	$visit=TblVisits::find($request->visit_num);
	$descriptions = $request->input('description');
	if(!empty($descriptions)){
	  //delete old treatment plans for this visit
	  PatientTreatmentPlans::where('visit_num',$visit->id)->delete();
	  foreach($descriptions as $desc){	
			PatientTreatmentPlans::create([
			 'visit_num'=>$visit->id,
			 'patient_num' => $visit->patient_num,
			 'clinic_num' => $visit->clinic_num,
			 'doctor_num' => $visit->doctor_num,
			 'status'=>'O',
			 'description'=>$desc,
			 'equip_type'=>'PLANS',
			 'user_num'=>auth()->user()->id
			 ]);
	      }
	 
	}else{
     //delete old treatment plans for this visit
	  PatientTreatmentPlans::where('visit_num',$visit->id)->delete();
	}
     $msg=__('Saved successfully');	
    return back();
}

//generate treatment plan pdf document
public function generateTreatmentPlanPDF($lang,Request $request)
{
  $id = $request->visit_num;
  $pdf = $this->getPDF($id,'generate');			
  
  //$pdf_path = public_path('/custom/treatment_plans/');
  //$uid=auth()->user()->id;
            
  //$path = $pdf_path;
           // if (!file_exists($path)) {
             //       mkdir($path, 0775, true);
            //}
            
                        
             //delete old existing files
             //$files = glob($path.'*'); // get all file names
               // foreach($files as $file){ // iterate files
                 // if(is_file($file)) {
                  //  unlink($file); // delete file
                  //}
                //}
             //create new pdf in directory
             //$name=date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
             //$pdf_file = $path . $name;
      
			 //file_put_contents($pdf_file, $pdf);
		     //return response()->download( $pdf_file);
       return $pdf;
    }
	
	
//get patient treatment plans list
public function get_treatment_plans($lang,Request $request){
	$visit_num = $request->visit_num;
	//dd($visit_num);
	$visit = TblVisits::find($visit_num);
	$patient_num = $visit->patient_num;
	$clinic_num = $visit->clinic_num;
	
	$sql="select  p.visit_num,ANY_VALUE(DATE_FORMAT(visit.visit_date_time,'%Y-%m-%d %H:%i')) as visit_date_time,
		          ANY_VALUE(clin.full_name) as ClinicName,ANY_VALUE(CONCAT(doc.first_name,' ',doc.last_name)) AS ProName,
		          ANY_VALUE(CONCAT(patConsult.first_name,' ',patConsult.last_name)) AS patDetail,ANY_VALUE(patConsult.first_phone) as Tel
				  from tbl_patient_visit_treatment_plan as p
				  INNER JOIN tbl_visits as visit on visit.id=p.visit_num and  visit.active='O'
				  INNER JOIN tbl_clinics as clin on p.clinic_num=clin.id and  clin.active='O'
				  LEFT JOIN tbl_doctors as doc on p.doctor_num=doc.id and doc.active='O'
				  INNER JOIN tbl_patients as patConsult on p.patient_num=patConsult.id and   patConsult.status='O'
				  where p.status='O' and p.visit_num<>".$visit_num." and p.patient_num=".$patient_num." and p.clinic_num=".$clinic_num." 
                  GROUP BY p.visit_num
				  ORDER BY visit_date_time desc		  
				  ";
	$treatment_plan = DB::select(DB::raw("$sql"));
	$html_treatment_plans= view('emr.visit.treatment_plans.patient_treatment_plan_list')->with(['treatment_plan'=>$treatment_plan])->render();
	return response()->json(['html_treatment_plans'=>$html_treatment_plans]);
	
}	

//import patient treatment plans
public function import_treatment_plans($lang,Request $request){
	$import_visit_num = $request->id;
	$current_visit_num= $request->current_visit_num;
	$plans = PatientTreatmentPlans::where('visit_num',$import_visit_num)->where('status','O')->get();
	if($plans->count()>0){
		//delete plans for current visit 
		PatientTreatmentPlans::where('visit_num',$current_visit_num)->delete();
		//create plans for current visit as in imported visit
		foreach($plans as $p){
			 PatientTreatmentPlans::create([
				 'visit_num'=>$current_visit_num,
				 'patient_num' => $p->patient_num,
				 'clinic_num' => $p->clinic_num,
				 'doctor_num' => $p->doctor_num,
				 'status'=>'O',
				 'description'=>$p->description,
				 'equip_type'=>'PLANS',
				 'user_num'=>auth()->user()->id
				 ]);
		    }
	  $msg = __('Imported successfully');
	  
	
	}else{
		 $msg = __('No data to import');
	}
	
	Alert::toast($msg,"success");
    $location = route('emr.visit.treatment_plans.index',[app()->getLocale(),$current_visit_num]);
    
	return response()->json(['location'=>$location]);
}

//get external branches list created date : 24-3-2023
public function pat_externalBranches_list($lang,Request $request){
 $visit_num = $request->visit_num;
 $clinic_num = TblVisits::where('id',$visit_num)->pluck('clinic_num')[0];
 $patient_num= TblVisits::where('id',$visit_num)->pluck('patient_num')[0];
 $external_branches = ExtBranch::where('clinic_num',$clinic_num)->where('status','A')->get();
 $patient=Patient::select('id','first_name','last_name','fax','email','receive_mail')->where('id',$patient_num)->first();
 $html_ext_branch= view('external_branches.send_by.ext_branches_list')->with(['external_branches'=>$external_branches])->render();
 $html_patient= view('patients_list.send_by.patient_data')->with(['patient'=>$patient])->render();

 return response()->json(['html_external_branches'=>$html_ext_branch,'html_patient'=>$html_patient]);	
}

//send treatment plan by email , fax created date : 25-3-2023
public function send_email_fax($lang,Request $request){
 
 switch($request->type){
 case 'patient': 
	   $id = $request->patient_num;
	   $patient = Patient::find($id);
	   $email = $patient->email;
	   $fax = $patient->fax;
	   $name = $patient->first_name.' '.$patient->last_name;
 break;
 case 'ext_branch':
	 //external branch id
	 $id = $request->ext_branch_id;
	 //get external branch details
	 $external_branch = ExtBranch::find($id);
	 $email = $external_branch->email;
	 $fax = $external_branch->fax;
	 $name = $external_branch->full_name;
   break;
 }
 //visit num
 $visit_id  = $request->visit_num;
 //generate pdf to send by email or fax or both
 $pdf = $this->getPDF($visit_id,'send');
  //is checked email
 $is_checked_email = $request->is_checked_email; 
//dd($is_checked_email);
 $resp_email='';
 if($is_checked_email=="true"){
	 //dd("hello mail");
	 $data = TblVisits::select(DB::raw("DATE(tbl_visits.visit_date_time) as date_visit"),
	                       DB::raw("CONCAT(pat.first_name,' ',pat.last_name) as pat_name"),
						   'clin.full_name as branch_name','clin.telephone as branch_tel','clin.fax as branch_fax',
						 'clin.email as branch_email','clin.full_address as branch_address')
	                    ->join('tbl_patients as pat','pat.id','tbl_visits.patient_num')
	                    ->join('tbl_clinics as clin','clin.id','tbl_visits.clinic_num')
						->where('tbl_visits.id',$visit_id)->first();
	
	 $logo=public_path("storage/images/logo-120-120.jpg");
	 $logo = str_replace(config('app.src_url'),config("app.url"),$logo);
	 $title=__('Hello').' '.$name.' , ';
	 if($request->type=='ext_branch'){
	 $msg1=__('You will find attached the treatment plan report for the patient').' : '.$data->pat_name.' . ';
	 }
	 if($request->type=='patient'){
	 $msg1=__('You will find attached your treatment plan report').' . ';
	 }
	 $msg2=__('This report is sent from branch').' : '.$data->branch_name.' . ';
	 
	 $from = $data->branch_name;
	 $reply_to_name = __("No reply").','.$data->branch_name;
	 $reply_to_address = isset($data->branch_email)? $data->branch_email:'noreply@email.com';
	 $subject = __('Treatment plan report');

	 $details = ['title'=>$title,'msg1'=>$msg1,'msg2'=>$msg2,
	             'branch_name'=>$data->branch_name,'branch_address'=>$data->branch_address,'branch_tel'=>$data->branch_tel,
				 'branch_fax'=>$data->branch_fax,'branch_email'=>$data->branch_email,
				 'from'=>$from,'reply_to_name'=>$reply_to_name,'reply_to_address'=>$reply_to_address,'subject'=>$subject];
	 
	 $to  = $email;
	 $pdf_name = 'treatment-plan-'.$data->date_visit.'.pdf';
	 
	 Mail::to($to)->send(new SettingMailAttach($details,$pdf,$pdf_name));
	
     if (Mail::failures()) {
				$resp_email.= __("Email: fail");
			}else{
				$resp_email.= __("Email : success");
			}	 
	}
 
  //is checked fax
  $is_checked_fax = $request->is_checked_fax;
  //send by fax procedure
  $resp_fax='';
    if($is_checked_fax=="true"){
	//dd("hello fax");
	//send through ring central
	$fax = str_replace("-","",$fax);
    $fax = str_replace("(","",$fax);
    $fax = str_replace(")","",$fax);
    $faxNo = str_replace(" ","",$fax);
    if(strlen($fax)==10){
      $fax = "+1".$fax;
      }
	  //dd($fax);
	$path = public_path('/custom/fax/treatment_plans/tmp/');  
	if (!file_exists($path)) {
                    mkdir($path, 0775, true);
            }
	
	$name = $visit_id.'_'.uniqid().'.pdf';
	$pdf_file=$path.$name;
	file_put_contents($pdf_file,$pdf);	
			
	$client_id = config('app.ringcentral_clientid');
    $secrect_code = config('app.ringcentral_clientsecret');
    $server = config('app.ringcentral_server');
    $user_name = config('app.ringcentral_username');
    $pass = config('app.ringcentral_pass');
    $ext = config('app.ringcentral_ext');
	
    require base_path("vendor/autoload.php");
    $rcsdk = new SDK($client_id, $secrect_code, $server);

    $platform = $rcsdk->platform();
    $platform->login($user_name, $ext, $pass);

	$request = $rcsdk->createMultipartBuilder()
	->setBody(array(
	  'to' => array(array('phoneNumber' => $fax)),
	  'faxResolution' => 'High',
	  'coverIndex' =>0
	))
	->add(fopen($pdf_file, 'r'))
	->request('/account/~/extension/~/fax');

       $response = $platform->sendRequest($request);
      //dd($response->json()->id);
	   if(isset($response->json()->id)){
		   $resp_fax.= __("Fax: success");
	   }else{
		   $resp_fax.= __("Fax: fail");

	   }
    //delete all files after sending fax success or fail
        $files = glob($path.'*'); // get all file names
           foreach($files as $file){ // iterate files
                 if(is_file($file)) {
                   unlink($file); // delete file
                 }
                }
  
  
  }
  
  if($resp_fax=='' && $resp_email!=''){
	  $msg = $resp_email;
  }
  
  if($resp_email=='' && $resp_fax!=''){
	  $msg = $resp_fax;
  }
  
  
  if($resp_email!='' && $resp_fax!=''){
    $msg = $resp_email." , ".$resp_fax;
  }
  
 
  return response()->json(["success"=>$msg,'modal_name'=>'sendTreatmentPlanModal']);
  
	
}


//generate treatment plan pdf document for print,email and fax
function getPDF($id,$type)
{
  
  $patient_plan = PatientTreatmentPlans::where('status','O')->where('visit_num',$id)->get();
  $visit = TblVisits::where('id',$id)->where('active','O')->first();
  $clinic=Clinic::where('active','O')->where('id',$visit->clinic_num)->first();
  $doctor=Doctor::where('active','O')->where('id',$visit->doctor_num)->first();
  $patient=Patient::where('status','O')->where('id',$visit->patient_num)->first();
  $signature_path = NULL;
			  //doctor account check if signature is allowed
			  //clinical account check both doctor signature and authorize clinic are allowed 
			 
	          if(isset($doctor)){
					 if( (auth()->user()->type==1 && $doctor->show_sign=='true')
						  || (auth()->user()->type==2 && $doctor->show_sign=='true' && $doctor->show_sign_for_clinic=='true')){
						   $user_signature = DoctorSignature::where('id',$doctor->sign_num)->where('active','O')->first();
						   
						   $mainPath = public_path('/custom/profile_signatures/tmp/');
						   $uid=auth()->user()->id;
													
							 if (!file_exists($mainPath)) {
									mkdir($mainPath, 0775, true);
							  }
							 $image = Image::make(file_get_contents(public_path(substr($user_signature->path,7))))->orientate();
							 
							 //delete old existing files
							 $files = glob($mainPath.'*'); // get all file names
								foreach($files as $file){ // iterate files
								  if(is_file($file)) {
									unlink($file); // delete file
								  }
								}
							 $filename= date('Y').date('m').date('d').'_'.$uid.'_'.uniqid().'.png';
							             
										if(($image->width() <= 200) and ($image->height() <= 200)) {
										}else{
											
												  $image->resize(200, 200, function ($constraint) {
												  $constraint->aspectRatio();

													// if you need to prevent upsizing, you can add:
												  $constraint->upsize();
												 
												  })->save($mainPath.$filename);

										}
						 
						 	$signature_path = $mainPath.$filename;				 
						   }//end sig chk
						   
					}	
			 
		
            $data = [
    
                'title' => __('Treatment Plan'),
                'date' => date('m/d/Y'),
				'signature_path' => $signature_path,
                'patient_plan' => $patient_plan,
				'visit'=>$visit,
                'clinic' => $clinic,
                'doctor' =>$doctor,
                'patient' => $patient,
                 ]; 
    
            $pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
                        -> loadView('emr.visit.treatment_plans.TreatmentPlanPDF', $data);
           
		    $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));         
			if($type=='send')
			{ return $pdf->output(); }
           if($type=='generate')
		   { return $pdf->stream();	}
    }	
	
}	