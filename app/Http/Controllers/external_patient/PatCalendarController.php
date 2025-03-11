<?php
/*
 *  DEV APP
 *  Created date : 28-7-2022
 *
*/
namespace App\Http\Controllers\external_patient;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\CalendarEvents;
use App\Models\CalendarResources;
use App\Models\EventStates;
use App\Models\EventReminders;
use App\Models\EventRemindersKey;
use App\Models\ClinicSMSPack;
use App\Models\TblBillAmounts;
use App\Models\TblVisits;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\PatientInsurance;

use App\Models\ARResults;
use App\Models\LMResults;
use App\Models\TMResults;
use App\Models\KMResults;
use App\Models\SubjRefResults;
use App\Models\MedImagesResults;
use App\Models\PatientVisitHistory;
use App\Models\DOCSResults;
use App\Models\TblPatientRxAccessories;
use App\Models\PatientTreatmentPlans;

use Illuminate\Support\Facades\Mail;
use App\Mail\SettingMail;

use Carbon\Carbon;
use DB;
use UserHelper;
use Alert;
use Twilio\Rest\Client;
use DataTables;
use Crypt;


class PatCalendarController extends Controller
{
    
public function __construct(){
        $this->middleware('auth');
    }
	
/**
*Get calendar view with events
*/	
public function index($lang,Request $request){
		$patient = Patient::select('id','clinic_num',DB::raw("CONCAT(first_name,' ',last_name) as patName"),'ramq','first_phone','cell_phone','email')->where('loginuser',auth()->user()->id)->first();
     	$status = EventStates::where('id',2)->where('active','O')->orderBy('display_order')->get();
		$clinic = Clinic::find($patient->clinic_num);
		$docID=NULL;
		$clinicID = $patient->clinic_num;
		$doctors = Doctor::select('tbl_doctors.id','tbl_doctors.first_name','tbl_doctors.last_name')
		                  ->join('tbl_doctors_clinics as pro_clinic','pro_clinic.doctor_num','tbl_doctors.id')
		                   ->where('pro_clinic.active','O')
						   ->where('pro_clinic.clinic_num',$clinicID)
						   ->where('tbl_doctors.active','O')->get();
	   
	   //dd($clinic);
	   $exams = TblBillAmounts::where('for_patient','Y')->where('status','O')->where('scheduling','O')->where('clinic_num',$clinicID)->get();
			   
		if($request->ajax()){
			$data =  Patient::select('id',DB::raw("CONCAT(first_name,' ',last_name) as patName"),'ramq','first_phone','cell_phone','email')->where('loginuser',auth()->user()->id)->get();	  
				  
				  return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                           $icon_label=__('Add to calendar');
		                   $icon2_label=__('View patient card');
						   $btn='<span class="fc-event fc-event-main"><input type="hidden" id="dragged_patient_id" value="'.$row->id.'"/>';
		                   $btn.='<button class="btn btn-icon btn-sm btn-action rounded-circle" style="font-size:0.7rem;" title="'.$icon_label.'"><i class="fas fa-plus"></i></button></span>';
		                   $btn.='<button class="ml-1 btn btn-icon btn-sm btn-action" style="font-size:0.7rem;" title="'.$icon2_label.'" data-toggle="modal" data-target="#patientCardModal" onclick="openPatientCardModal('.$row->id.')"><i class="far fa-address-card"></i></button>';
		                   return $btn;

                         })
                    ->rawColumns(['action'])
				    ->make(true);
				
						
		}
		
		return view('external_patient.calendar.index')
		        ->with('docID',$docID)
		        ->with('clinicID',$clinicID)
				->with('doctors',$doctors)
				->with('status',$status)
				->with('exams',$exams)
				->with('patients',$patient);
		 
		
	}
	
/**
*get calendar events
*/
public function calendarEvents($lang,Request $request){
	$type = $request->type;
	$events = [];
	$patient = Patient::where('loginuser',auth()->user()->id)->first();
		   $recurrent_events =collect();
		   
		   if(isset($request->clinicID) && isset($request->docID) ){
    	           $pro_id = $request->docID;
				   $recurrent_events = CalendarResources::select('id','clinic_num','doctor_num','start_time','end_time',
				                                                 'start','end_before','repeat_type','repeat_interval',
																  'week_days','month_day_num','off_days',
																  'descrip_en','descrip_fr')
				                                ->where('clinic_num',$patient->clinic_num)
	                                            ->where(function($q) use($pro_id){
													$q->where('doctor_num',$pro_id);
													$q->orWhereNULL('doctor_num');
													})
												->where('active','O')
												->where('for_patient','Y')
												->get();
		            }
			
		   if(isset($request->clinicID) && !isset($request->docID) ){
    	           $recurrent_events = CalendarResources::select('id','clinic_num','doctor_num','start_time','end_time',
				                                                 'start','end_before','repeat_type','repeat_interval',
																  'week_days','month_day_num','off_days',
																  'descrip_en','descrip_fr')
				                                        ->where('clinic_num',$patient->clinic_num)
	                                           		    ->where('active','O')
														->where('for_patient','Y')
														->get();
		            }
           $count =$recurrent_events->count();
	      //dd($recurrent_events);
	if($count >0 ){
	 foreach($recurrent_events as $evt)
	 {
	 $dteStart = new Carbon($evt->start_time);
     $dteEnd   = new Carbon($evt->end_time);
	 
	 $minsDiff  =  intval(($dteEnd->diffInMinutes($dteStart))/15);
	 //dd($minsDiff);
	 $dteDiff  =  $dteEnd->diff($dteStart); 
     
	 $interval= $dteDiff->format("%H:%I:%S"); 
	 
	 $dtstart= Carbon::parse($evt->start." ".$evt->start_time)->format('Y-m-d H:i:s');
	 $dtuntil=Carbon::parse($evt->end_before)->format('Y-m-d');
	 $resourceId = $evt->doctor_num; 
	 $intv = '00:15:00';
     
	 for($i=0;$i<$minsDiff;$i++){	 
		if($i>0){
			$dtstart = Carbon::parse($dtstart)->addMinutes(15)->format('Y-m-d H:i:s');
		} 	 
     if($evt->repeat_type=='daily' ||$evt->repeat_type=='weekly'){ 
			 if($evt->off_days=='Y'){
					 if($resourceId==NULL){
						$doctor_ids = DB::table('tbl_doctors_clinics')->where('active','O')->where('clinic_num',$evt->clinic_num)->pluck('doctor_num')->toArray();
					   array_push($events, array(
					   'id'  => $evt->id,
					   'format'=>'json',
					   'backgroundColor'=>'rgb(255,128,128)',
					   'title'=>($lang=='en')?$evt->descrip_en:$evt->descrip_fr,
					   'resourceIds' => $doctor_ids,
					   'rrule'=> array('freq'=>$evt->repeat_type,'interval'=>intval($evt->repeat_interval),'dtstart'=>$dtstart ,'until'=>$dtuntil,'byweekday'=>explode(",",$evt->week_days)),
					   'duration'=>$intv,
					   'recurrent'=>3,
					   'editable'=>false,
					   'overlap'=>false
					   
						));
					 }else{  
				  
					  array_push($events, array(
					  'id'  => $evt->id,
					   'format'=>'json',
					   'backgroundColor'=>'rgb(255,128,128)',
					   'title'=>($lang=='en')?$evt->descrip_en:$evt->descrip_fr,
					   'resourceId' => $resourceId,
					   'rrule'=> array('freq'=>$evt->repeat_type,'interval'=>intval($evt->repeat_interval),'dtstart'=>$dtstart ,'until'=>$dtuntil,'byweekday'=>explode(",",$evt->week_days)),
					   'duration'=>$intv,
					   'recurrent'=>3,
					   'editable'=>false,
					   'overlap'=>false
					   
						));
					 }
			 }else{
			 
			 array_push($events, array(
			   'id'  => $evt->id,
			   'format'=>'json',
			   'groupId' =>'availableSchedule',
			   'className'=>'placeholder-event',
			   'display'=> 'background',
			   'resourceId' => $resourceId,
			   'rrule'=> array('freq'=>$evt->repeat_type,'interval'=>intval($evt->repeat_interval),'dtstart'=>$dtstart ,'until'=>$dtuntil,'byweekday'=>explode(",",$evt->week_days)),
			   'duration'=>$intv,
			   'recurrent'=>0
			   
				));
			   }
			 }else{
			 if($evt->repeat_type=='monthly'){
				 array_push($events, array(
				   'id'  => $evt->id,
				   'format'=>'json',
				   'groupId' =>'availableSchedule',
				   'className'=> 'placeholder-event', // Add a custom CSS class
				   'display'=> 'background',
				   'resourceId' => $resourceId,
				   'rrule'=> array('freq'=>$evt->repeat_type,'interval'=>intval($evt->repeat_interval),'dtstart'=>$dtstart ,'until'=>$dtuntil,'bymonthday'=> array_map('intval',explode(",",$evt->month_day_num))),
				   'duration'=>$intv,
				   'recurrent'=>0
				   
					));
			     }else{
				array_push($events, array(
				   'id'  => $evt->id,
				   'format'=>'json',
				   'groupId' =>'availableSchedule',
				   'className'=> 'placeholder-event', // Add a custom CSS class
				   'display'=> 'background',
				   'resourceId' => $resourceId,
				   'rrule'=> array('freq'=>$evt->repeat_type,'interval'=>intval($evt->repeat_interval),'dtstart'=>$dtstart ,'until'=>$dtuntil,'bymonth'=> array_map('intval',explode(",",$evt->month_day_num)),'bymonthday'=> array_map('intval',explode(",",$evt->month_day_num))),
				   'duration'=>$intv,
				   'recurrent'=>0
				   
					)); 
				 }		
			 }
	    }//end minutes diff
	       
		   }
	  
	    }

		 //dd($events);
		 $start_date = Carbon::parse($request->start)->format('Y-m-d');
		 $end_date = Carbon::parse($request->end)->format('Y-m-d');
         $filter_date = " and DATE(evts.start) >='".$start_date."' ";
		 $filter_date .= " and DATE(evts.start) <='".$end_date."' ";
		 $filter_resource ='';  
		   if(isset($request->docID)){
			    $filter_resource=" and evts.doctor_num='".$request->docID."' ";
		   }
		  

		   $sql="select evts.id,evts.clinic_num,evts.doctor_num,evts.patient_num,evts.state,evts.bill_exam_num,evts.start,evts.end,
		                evts.title,evts.remark,evts.sent_by_email,evts.sent_by_sms,evts.is_exceptional,res.doctor_num as res_doc,
		                exam.am_code as exam_id,exam.taxable,exam.name_fr,exam.name_eng,exam.color,exam.duration,CONCAT(pat.first_name,' ',pat.last_name) as patName,pat.birthdate,
						pat.sex as gender,pat.first_phone,pat.email,pat.ramq,pat.cell_phone,pat.id as pat_id,
						clin.full_name as clinic_name,CONCAT(doc.first_name,' ',doc.last_name) as doc_name,
						evts.onlineform_sent_sms,evts.onlineform_sent_email
	       from tbl_calendar_events as evts
		   INNER JOIN tbl_patients as pat ON pat.id=evts.patient_num and pat.status='O'
		   INNER JOIN tbl_doctors as doc ON doc.id=evts.doctor_num and doc.active='O'
		   INNER JOIN tbl_clinics as clin ON clin.id=evts.clinic_num and clin.active='O'
		   INNER JOIN tbl_bill_amounts as exam ON exam.id=evts.bill_exam_num and exam.scheduling='O' and exam.status='O'
		   LEFT JOIN tbl_calendar_resources as res ON res.doctor_num=evts.doctor_num and res.active='O' and res.for_patient='Y' 
		   where evts.active='O' and TIME(evts.start)>=res.start_time and TIME(evts.start)<res.end_time and evts.clinic_num=".$patient->clinic_num."  ".$filter_resource." ".$filter_date."
           GROUP BY evts.id
           ORDER BY evts.id";    		   
	       $normal_events = DB::select(DB::raw("$sql"));
		   //dd($sql);
		   $icon='';
	     if( count($normal_events)>0){
	
			 foreach($normal_events as $evt)
			 {
			  
			  $resourceId = $evt->doctor_num; 
			  
			  $evt_status = EventStates::where('id',$evt->state)->first();
			  $status_name = ($lang=='en')? $evt_status->state_en : $evt_status->state_fr;
			  $icon = $evt_status->display_icon;
			  
			  $exam_color = "#".$evt->color;
			  $exam_name = ($lang=='en')?$evt->name_eng:$evt->name_fr;
			  //$exam_name = ($evt->taxable=='Y')? $exam_desc." ( ".$evt->exam_id.",".__("Taxable")." )" : $exam_desc." ( ".$evt->exam_id." )";
			  
			  $age = Carbon::parse($evt->birthdate)->age;
			  
			  $clinic_name = $evt->clinic_name;
			  $doc_name = $evt->doc_name;
			  $pat_name = $evt->patName;
			  
			  $title = ($evt->title!='')?"<div><b>".__("Title")." : </b>".$evt->title."</div>":'';
			  $sent_by ='';
			  if($evt->sent_by_sms=='O' && $evt->sent_by_email!='O'){
				  $sent_by = "<div><b>".__("Remind")." : </b>".__("SMS")."</div>";
				  }
			  if($evt->sent_by_sms!='O' && $evt->sent_by_email=='O'){
				  $sent_by = "<div><b>".__("Remind")." : </b>".__("Email")."</div>";
			  }
			  if($evt->sent_by_sms=='O' && $evt->sent_by_email=='O'){
				  $sent_by = "<div><b>".__("Remind")." : </b>".__("SMS").",".__("Email")."</div>"; 		  
			  }
			  //created date 11-7-2023
			  $sent_onlineform ='';
			 
			 if($evt->state==1){
				  if($evt->onlineform_sent_sms=='O' && $evt->onlineform_sent_email!='O'){
					  $sent_onlineform = "<div><b>".__("Patient history")." : </b>".__("SMS")."</div>";
					  }
				  if($evt->onlineform_sent_sms!='O' && $evt->onlineform_sent_email=='O'){
					  $sent_onlineform = "<div><b>".__("Patient history")." : </b>".__("Email")."</div>";
				  }
				  if($evt->onlineform_sent_sms=='O' && $evt->onlineform_sent_email=='O'){
					  $sent_onlineform = "<div><b>".__("Patient history")." : </b>".__("SMS").",".__("Email")."</div>";
				  }
			    }
			   //end	
			  $descrip_note = ($evt->remark!='' && $evt->remark!=NULL)?"<div><b>Note : </b>".$evt->remark."</div>":'';
			  
			  $pat_gender=($evt->gender=='M')?__("Male"):(($evt->gender=='F')?__("Female"):__("Undefined"));
			  $pat_email =($evt->email != '' && $evt->email!=NULL)?'<b>'.__("Email").' : </b>'.$evt->email:'';
			  $pat_ramq = ($evt->ramq != '' && $evt->ramq != NULL)?'<b>'.__("RAMQ").' : </b>'.$evt->ramq:'';
			  $pat_tel = ($evt->first_phone != '' && $evt->first_phone != NULL)?'<b>'.__("Landline Phone").' : </b>'.$evt->first_phone:'';
			  $pat_tel1 = ($evt->cell_phone != '' && $evt->cell_phone != NULL)?'<b>'.__("Cell Phone").' : </b>'.$evt->cell_phone:'';

			  $description=$sent_by.$sent_onlineform.$title."<div><b>".__("Status")." : </b>".$status_name."</div><div><b>".__("Exam")." : </b>".$exam_name."</div><div><b>".__("Patient details")." : </b> ".$pat_name."<div><b>".__("AGE")." : </b>".$age."<span class='ml-4'><b>".__("GENDER")." : </b>".$pat_gender."</span></div><div>".$pat_ramq."</div><div>".$pat_tel."</div><div>".$pat_tel1."</div><div>".$pat_email."</div>".$descrip_note;	  
			 
			 
			  $evtTitle=($evt->pat_id==$patient->id)? $exam_name." - ".$status_name: $exam_name; 
			  //event associated with a resource
			  if(null!==$evt->res_doc){
			  
			  array_push($events, array(
			   'id'  => $evt->id,
			   'title'  =>($evt->pat_id==$patient->id)?$evtTitle:__('Reserved'),
			   'format'=>'json',
			   'icon' => $icon,
			   'color' => ($evt->pat_id==$patient->id)?$exam_color:'gray',
			   'start'=>$evt->start,
			   'display'=> 'inline',
			   'end'=>$evt->end,
			   'id_status'=>$evt->state,
			   'status' => $status_name,
			   'constraint'=> 'availableSchedule', 
			   'description' => $description,
			   'id_patient' => $evt->patient_num,
			   'id_clinic' => $evt->clinic_num,
			   'clinic' => $clinic_name,
			   'pro' => $doc_name,
			   'exam_name' => $exam_name,
			   'exam_id' => $evt->bill_exam_num,
			   'event_title' => $evt->title,
			   'event_note'=>$evt->remark,
			   'is_exceptional'=>'N',
			   'event_sms_status'=>$evt->sent_by_sms,
			   'event_mail_status'=>$evt->sent_by_email,
			   'onlineform_email_status'=>$evt->onlineform_sent_email,
			   'onlineform_sms_status'=>$evt->onlineform_sent_sms,
			   'resourceId' => $resourceId,
			   'editable'=>($evt->pat_id==$patient->id)? true:false,
			   'overlap'=>($evt->pat_id==$patient->id)? true:false,
			   'recurrent'=>1
			   
				));
			  }else{
				  array_push($events, array(
							   'id'  => $evt->id,
							   'title'  =>$evtTitle,
							   'format'=>'json',
							   'icon' => $icon,
							   'color' => $exam_color,
							   'start'=>$evt->start,
							   'display'=> 'inline',
							   'end'=>$evt->end,
							   'id_status'=>$evt->state,
							   'status' => $status_name,
							   'description' => $description,
							   'id_patient' => $evt->patient_num,
							   'id_clinic' => $evt->clinic_num,
							   'clinic' => $clinic_name,
			                   'pro' => $doc_name,
							   'exam_name' => $exam_name,
							   'exam_id' => $evt->bill_exam_num,
							   'event_title' => $evt->title,
							   'event_note'=>$evt->remark,
							   'is_exceptional'=>'N',
							   'event_sms_status'=>$evt->sent_by_sms,
							   'event_mail_status'=>$evt->sent_by_email,
							   'resourceId' => $resourceId,
							   'recurrent'=>2
							));
				  
			  }
			 }
	       }
		   

return response()->json($events);
}


public function getEventData($lang,$id){
	 $patient = Patient::where('loginuser',auth()->user()->id)->first();
	 
	 $sql="select evts.id,evts.clinic_num,evts.doctor_num,evts.patient_num,evts.state,evts.bill_exam_num,evts.start,evts.end,
		          evts.title,evts.remark,evts.sent_by_email,evts.sent_by_sms,evts.is_exceptional,res.doctor_num as res_doc,
		          exam.am_code as exam_id,exam.taxable,exam.name_fr,exam.name_eng,exam.color,exam.duration,CONCAT(pat.first_name,' ',pat.last_name) as patName,pat.birthdate,
				  pat.sex as gender,pat.first_phone,pat.email,pat.ramq,pat.cell_phone,pat.id as pat_id,
				  clin.full_name as clinic_name,CONCAT(doc.first_name,' ',doc.last_name) as doc_name,
				  evts.onlineform_sent_email,evts.onlineform_sent_sms
	       from tbl_calendar_events as evts
		   INNER JOIN tbl_patients as pat ON pat.id=evts.patient_num and pat.status='O'
		   INNER JOIN tbl_doctors as doc ON doc.id=evts.doctor_num and doc.active='O'
		   INNER JOIN tbl_clinics as clin ON clin.id=evts.clinic_num and clin.active='O'
		   INNER JOIN tbl_bill_amounts as exam ON exam.id=evts.bill_exam_num and exam.scheduling='O' and exam.status='O'
		   LEFT JOIN tbl_calendar_resources as res ON res.doctor_num=evts.doctor_num and res.active='O'
		   where evts.patient_num=".$patient->id." and evts.id='".$id." '
           "; 

     $normal_events = DB::select(DB::raw("$sql"));
     $icon='';
	 $event_data=array();
	     if( count($normal_events)>0){
	
			 foreach($normal_events as $evt)
			 {
			  
			  $resourceId = $resourceId = $evt->doctor_num; 
			  $evt_status = EventStates::where('id',$evt->state)->first();
			  $status_name = ($lang=='en')? $evt_status->state_en : $evt_status->state_fr;
			  $icon = $evt_status->display_icon;
			  $exam_color = "#".$evt->color;
			  $exam_desc = ($lang=='en')?$evt->name_eng:$evt->name_fr;
			  $exam_name = ($evt->taxable=='Y')?$exam_desc." ( ".$evt->exam_id.",".__("Taxable")." )":$exam_desc." ( ".$evt->exam_id." )";
			  $age = Carbon::parse($evt->birthdate)->age;
			  $clinic_name = $evt->clinic_name;
			  $doc_name = $evt->doc_name;
			  $pat_name = $evt->patName;
			  $title = ($evt->title!='')?"<div><b>".__("Title")." : </b>".$evt->title."</div>":'';
			  $sent_by ='';
			  if($evt->sent_by_sms=='O' && $evt->sent_by_email!='O'){
				  $sent_by = "<div><b>".__("Remind")." : </b>".__("SMS")."</div>";
				  }
			  if($evt->sent_by_sms!='O' && $evt->sent_by_email=='O'){
				  $sent_by = "<div><b>".__("Remind")." : </b>".__("Email")."</div>";
			  }
			  if($evt->sent_by_sms=='O' && $evt->sent_by_email=='O'){
				  $sent_by = "<div><b>".__("Remind")." : </b>".__("SMS").",".__("Email")."</div>"; 		  
			  }
			  
			  //created date 11-7-2023
			  $sent_onlineform ='';
			 
			  if($evt->state==1){
				  if($evt->onlineform_sent_sms=='O' && $evt->onlineform_sent_email!='O'){
					  $sent_onlineform = "<div><b>".__("Patient history")." : </b>".__("SMS")."</div>";
					  }
				  if($evt->onlineform_sent_sms!='O' && $evt->onlineform_sent_email=='O'){
					  $sent_onlineform = "<div><b>".__("Patient history")." : </b>".__("Email")."</div>";
				  }
				  if($evt->onlineform_sent_sms=='O' && $evt->onlineform_sent_email=='O'){
					  $sent_onlineform = "<div><b>".__("Patient history")." : </b>".__("SMS").",".__("Email")."</div>";
				  }
			    }
			   //end	
			  $descrip_note = ($evt->remark!='' && $evt->remark!=NULL)?"<div><b>Note : </b>".$evt->remark."</div>":'';
			  
			  $pat_gender=($evt->gender=='M')?__("Male"):(($evt->gender=='F')?__("Female"):__("Undefined"));
			  $pat_email =($evt->email != '' && $evt->email!=NULL)?'<b>'.__("Email").' : </b>'.$evt->email:'';
			  $pat_ramq = ($evt->ramq != '' && $evt->ramq != NULL)?'<b>'.__("RAMQ").' : </b>'.$evt->ramq:'';
			  $pat_tel = ($evt->first_phone != '' && $evt->first_phone != NULL)?'<b>'.__("Landline Phone").' : </b>'.$evt->first_phone:'';
			  $pat_tel1 = ($evt->cell_phone != '' && $evt->cell_phone != NULL)?'<b>'.__("Cell Phone").' : </b>'.$evt->cell_phone:'';

			  $description=$sent_by.$sent_onlineform.$title."<div><b>".__("Status")." : </b>".$status_name."</div><div><b>".__("Exam")." : </b>".$exam_name."</div><div><b>".__("Patient details")." : </b> ".$pat_name."<div><b>".__("AGE")." : </b>".$age."<span class='ml-4'><b>".__("GENDER")." : </b>".$pat_gender."</span></div><div>".$pat_ramq."</div><div>".$pat_tel."</div><div>".$pat_tel1."</div><div>".$pat_email."</div>".$descrip_note;	  
			
			  $evtTitle=$exam_name." - ".$status_name; 
			  
			  //event associated with a resource
			  if(null!==$evt->res_doc){
			  
			  array_push($event_data, array(
			   'id'  => $evt->id,
			   'title'  =>$evtTitle,
			   'format'=>'json',
			   'icon' => $icon,
			   'color' => $exam_color,
			   'start'=>$evt->start,
			   'display'=> 'inline',
			   'end'=>$evt->end,
			   'id_status'=>$evt->state,
			   'status' => $status_name,
			   'constraint'=> 'availableSchedule', 
			   'description' => $description,
			   'id_patient' => $evt->patient_num,
			   'id_clinic' => $evt->clinic_num,
			   'clinic' => $clinic_name,
			   'pro' => $doc_name,
			   'exam_name' => $exam_name,
			   'exam_id' => $evt->bill_exam_num,
			   'event_title' => $evt->title,
			   'event_note'=>$evt->remark,
			   'is_exceptional'=>'N',
			   'event_sms_status'=>$evt->sent_by_sms,
			   'event_mail_status'=>$evt->sent_by_email,
			   'onlineform_email_status'=>$evt->onlineform_sent_email,
			   'onlineform_sms_status'=>$evt->onlineform_sent_sms,
			   'resourceId' => $resourceId,
			   'editable'=>true,
			   'overlap'=> true,
			   'recurrent'=>1
			   
				));
			  }else{
				  array_push($event_data, array(
							   'id'  => $evt->id,
							   'title'  =>$evtTitle,
							   'format'=>'json',
							   'icon' => $icon,
							   'color' => $exam_color,
							   'start'=>$evt->start,
							   'display'=> 'inline',
							   'end'=>$evt->end,
							   'id_status'=>$evt->state,
							   'status' => $status_name,
							   'description' => $description,
							   'id_patient' => $evt->patient_num,
							   'id_clinic' => $evt->clinic_num,
							   'clinic' => $clinic_name,
			                   'pro' => $doc_name,
							   'exam_name' => $exam_name,
							   'exam_id' => $evt->bill_exam_num,
							   'event_title' => $evt->title,
							   'event_note'=>$evt->remark,
							   'is_exceptional'=>'N',
							   'event_sms_status'=>$evt->sent_by_sms,
							   'event_mail_status'=>$evt->sent_by_email,
							   'resourceId' => $resourceId,
							   'recurrent'=>2
							));
				  
			  }
			 }
	       }
		   
return $event_data;	
}

/**
*Calendar events operations
*/	
  			
public function calendarCRUDEvents($lang,Request $request){
 
        switch ($request->type) {

           case 'add':
                $start_date=Carbon::create($request->start)->format('Y-m-d H:i');
		
						
				$end_date=Carbon::create($request->end)->format('Y-m-d H:i');
				
							
				
				//dd($st.';'.$et.';'.$sd);
				$sql1 = "select id,start,end from tbl_calendar_events 
				         where  active='O' and doctor_num='".$request->pro."' and clinic_num='".$request->clinic."'
						 and '".$start_date."'>start and '".$start_date."'<end ";  
				
				$sql2 = "select id,start,end from tbl_calendar_events 
				         where  active='O' and doctor_num='".$request->pro."' and clinic_num='".$request->clinic."'
						 and  '".$end_date."'>start and '".$end_date."'<end ";
				
				$sql3 = "select id,start,end from tbl_calendar_events 
				         where  active='O' and doctor_num='".$request->pro."' and clinic_num='".$request->clinic."'
						 and  '".$end_date."'= end ";
				
				$r1 = DB::select(DB::raw("$sql1"));
				$r2 = DB::select(DB::raw("$sql2"));
				$r3 = DB::select(DB::raw("$sql3"));
				
				//dd(count($r1).';'.count($r2).';'.count($r3));
				
				if(count($r1)>0 || count($r2)>0){
					return response()->json(['error'=>__("Date is overlapping with another schedule")]);
				}
				
				if(count($r1)==0 && count($r3)>0){
					return response()->json(['error'=>__("Date is overlapping with another schedule")]);
				}
				
				
                $event_id = CalendarEvents::create([
                    'title' => $request->title,
                    'state'=>$request->status,
					'patient_num'=>$request->patient,
					'bill_exam_num'=>$request->exam,
					'doctor_num' => $request->pro,
					'clinic_num' => $request->clinic,
                    'start' => $start_date,
                    'end' => $end_date,
					'remark' => $request->note,
					'user_num' =>auth()->user()->id,
					'active' => 'O'
                ])->id;
               				
				 $event = CalendarEvents::find($event_id);
                //create new visitation for new event
                 $visit_id = TblVisits::create([
		                                 'event_num'=>$event->id,
										 'status'=>$event->state,
										 'bill_exam_num'=>$event->bill_exam_num,
										 'patient_num'=>$event->patient_num,
		                                 'clinic_num'=>$event->clinic_num,
										 'doctor_num'=>isset($event->doctor_num)?$event->doctor_num:NULL ,
										 'user_num'=>auth()->user()->id,
										 'visit_date_time'=>$event->start,
										 'active'=>'O'])->id;				
                
				//update event with created visit id
				CalendarEvents::where('id', $event_id)->update(['visit_num'=>$visit_id]); 
                $event_data = $this->getEventData($lang,$event_id);
				return response()->json($event_data);

            break;

  

            case 'update':
                 $start_date=Carbon::create($request->start)->format('Y-m-d H:i');
			     $end_date=Carbon::create($request->end)->format('Y-m-d H:i');
				 
				 $event = CalendarEvents::where('id', $request->id)->first();
				 
				 $sql1 = "select id,start,end from tbl_calendar_events 
				         where  active='O' and doctor_num='".$event->doctor_num."' and clinic_num='".$event->clinic_num."'
						 and id <> '".$event->id."'
						 and '".$start_date."'>start and '".$start_date."'<end ";  
				
				$sql2 = "select id,start,end from tbl_calendar_events 
				         where  active='O' and doctor_num='".$event->doctor_num."' and clinic_num='".$event->clinic_num."'
						 and id <> '".$event->id."'
						 and  '".$end_date."'>start and '".$end_date."'<end ";
				
				$sql3 = "select id,start,end from tbl_calendar_events 
				         where  active='O' and doctor_num='".$event->doctor_num."' and clinic_num='".$event->clinic_num."'
						 and id <> '".$event->id."'
						 and  '".$end_date."'= end ";
				
				$r1 = DB::select(DB::raw("$sql1"));
				$r2 = DB::select(DB::raw("$sql2"));
				$r3 = DB::select(DB::raw("$sql3"));
				
				//dd(count($r1).';'.count($r2).';'.count($r3));
				
				if(count($r1)>0 || count($r2)>0){
					return response()->json(['error'=>__("Date is overlapping with another schedule")]);
				}
				
				if(count($r1)==0 && count($r3)>0){
					return response()->json(['error'=>__("Date is overlapping with another schedule")]);
				}
               
			   
			   
			   CalendarEvents::find($request->id)->update([
                    'title' => $request->title,
                    'state'=>$request->status,
					'bill_exam_num'=>$request->exam,
                    'start' => $start_date,
                    'end' => $end_date,
					'remark' => $request->note,
					'user_num' =>auth()->user()->id
                ]);
                
				
                //get active visitation for this event
                $visit=TblVisits::where('id',$event->visit_num)->where('event_num',$event->id)->where('active','O')->first();
				//update only if status not equal to  arrived=3
				if(isset($visit) && $visit->status !=3){
					TblVisits::find($visit->id)->update([
					  'patient_num'=>$event->patient_num,
					  'status'=>$event->state,
					  'bill_exam_num'=>$event->bill_exam_num,
					  'visit_date_time'=>$event->start,
					  'user_num' =>auth()->user()->id
					]);
                }

                //$event_data = $this->getEventData($lang,$event->id);
				return response()->json(["success"=>true]);


            break;
            

            case 'delete':
                //get event to be deleted
				$event = CalendarEvents::find($request->id);
				
                //delete active visit for this event
                $visit=TblVisits::where('id',$event->visit_num)->where('event_num',$event->id)->where('active','O')->first();
				
				if(!isset($visit)){
					//update event to non active
				    CalendarEvents::find($request->id)->update(['user_num' =>auth()->user()->id,'active'=>'N']);
					return response()->json(["success"=>true]);
				 }
                
				if(isset($visit) && $visit->status !=3){
					 //update event to non active
				     CalendarEvents::find($request->id)->update(['user_num' =>auth()->user()->id,'active'=>'N']);
					 //destroy visit with all its tests only if visit is not arrived
					 TblVisits::where('id',$visit->id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
					
					 ARResults::where('visit_num',$visit->id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
					 LMResults::where('visit_num',$visit->id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
					 TMResults::where('visit_num',$visit->id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
					 KMResults::where('visit_num',$visit->id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
					 SubjRefResults::where('visit_num',$visit->id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
					 PatientVisitHistory::where('visit_num',$visit->id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
                     TblPatientRxAccessories::where('visit_num',$visit->id)->update(['active'=>'N','user_num'=>auth()->user()->id]);				 
					 MedImagesResults::where('visit_num',$visit->id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
					 DOCSResults::where('visit_num',$visit->id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
                     PatientTreatmentPlans::where('visit_num',$visit->id)->update(['status'=>'N','user_num'=>auth()->user()->id]);		
					 return response()->json(["success"=>true]);					 
                }
				
				 if(isset($visit) && $visit->status ==3){
					$msg_error = __("This schedule is attached to an active visit with arrived status");
                    return response()->json(["error"=>$msg_error]);					
				 }

                

            break;

           
			case 'event_details':
             $id = $request->id;
			  $evt = CalendarEvents::where('id',$id)->first();
			  $startD=Carbon::create($evt->start)->format('Y-m-d');
			  $startT=Carbon::create($evt->start)->format('H:i');
			  $endT=Carbon::create($evt->end)->format('H:i');
			  $clinic = Clinic::select('full_name')->find($evt->clinic_num);
			  $doctor = Doctor::select('first_name','last_name')->find($evt->doctor_num);
			  $doctor_name=$doctor->first_name.' '.$doctor->last_name;
			 return response()->json(["event_id"=>$evt->id,"clinic_name"=>$clinic->full_name,"doctor_name"=>$doctor_name,
			                          "event_title"=>$evt->title,"id_status"=>$evt->state,
			                          "startD"=>$startD,"startT"=>$startT,"endT"=>$endT,
			                          "exam_id"=>$evt->bill_exam_num,"id_patient"=>$evt->patient_num,"note"=>$evt->remark,
									  "is_exceptional"=>$evt->is_exceptional]);
			
			
			break;
			//stripe payment right click
			case 'payment':
			 $id = $request->id;
			 $event = CalendarEvents::find($id);
			 $exam = TblBillAmounts::find($event->bill_exam_num);
			 if(intval($exam->sell_amount)==0){
				$price = __("Price").' : '.$exam->sell_amount.' '.'CAD';
				$msg = '<div><lable class="badge bg-gradient-danger" style="font-size:1em;">'.$price.'</label></div>';
				$msg.='<div>'.__("Please call your clinic to adjust the exam price").'</div>';
				return response()->json(["error"=>true,"msg"=>$msg]); 
			 }else{
			   $url = route('stripe.payment.index',[$lang,Crypt::encrypt($id)]);
			   return response()->json(["success"=>true,"url"=>$url]);
			 }
			break;
			
            default:

            # code...

            break;

        }
    }
	
	
/**
*Get calendar resources
*/	
public function calendarResources($lang,Request $request){
	 $output=[];
	 $resources='';
	 $bhrs=array();
	 //branch and doctor are choosen 
    if(isset($request->clinicID) && isset($request->docID) ){	
	 
	 $resources = CalendarResources::select('tbl_calendar_resources.id','tbl_calendar_resources.doctor_num',
	                                     'tbl_calendar_resources.start_time','tbl_calendar_resources.end_time',
	                                     'clinic.full_name','clinic.open_hours')
	                                   ->join('tbl_clinics as clinic','clinic.id','tbl_calendar_resources.clinic_num')
									   ->where('tbl_calendar_resources.clinic_num',$request->clinicID)
									   ->where('tbl_calendar_resources.doctor_num',$request->docID)
									   ->where('tbl_calendar_resources.active','O')
									   ->whereNULL('tbl_calendar_resources.off_days')
									   ->where('clinic.active','O')
									   ->get();
		
   if($resources->count()==0){
	 $doc=Doctor::where('id',$request->docID)->first();
	 $branch=Clinic::where('id',$request->clinicID)->first();
	 $doc_name = $doc->first_name.' '.$doc->last_name;
	 $branch_name=(isset($branch))?$branch->full_name:__('Undefined');		
	  $title=__("Branch")." : ".$branch_name." ; ".__("Doctor")." : ".$doc_name; 	
     $bhrs = isset($branch->open_hours)?$this->getOpeningHours($branch->open_hours):array();	 
	 $id = $request->docID;
				
	 if(!empty($bhrs)){
					   $output[]=array('id'=>$id,'title'  =>$title,'businessHours'=>$bhrs);
					}else{
					
					   $output[]= array('id'=>$id,'title'  => $title);   
					 }
	 
	      			  
	 }
	
		
	if($resources->count()>0) {
			
	foreach($resources as $resource){
				$start_time=Carbon::create($resource->start_time)->format('H:i');
				$end_time=Carbon::create($resource->end_time)->format('H:i');
				$pro = Doctor::find($resource->doctor_num);
				$pro_name=$pro->first_name.' '.$pro->last_name;
				$fac_name=$resource->full_name;
				if(strlen($fac_name)>60){
						$fac_name=substr($fac_name,0,60).'...';
					  } 
				
				$title=__("Branch")." : ".$fac_name." ; ".__("Doctor")." : ".$pro_name; 		
					
				
				$bhrs = isset($resource->open_hours)?$this->getOpeningHours($resource->open_hours):array();
				$id = $request->docID;
				
				if(!empty($bhrs)){
					   $output[]=array('id'=>$id,'title'  =>$title,'businessHours'=>$bhrs,'start_time'=>$start_time,'end_time'=>$end_time );
					}else{
					
					   $output[]= array('id'=>$id,'title'  => $title,'start_time'=>$start_time,'end_time'=>$end_time);   
					 }
				
			
			}
	
	    }
	
	}
   
   //branch account and no doctor is choosen 
   if(isset($request->clinicID) && !isset($request->docID) ){	
	 $doctors_resources_ids = DB::table('tbl_calendar_resources')
	                		->where('active','O')
					        ->where('clinic_num',$request->clinicID)
					        ->groupBy('doctor_num')
					        ->pluck('doctor_num')->toArray();
	
	$no_schedules = Clinic::select('tbl_clinics.full_name','tbl_clinics.open_hours','pro_clinic.doctor_num')
	              ->join('tbl_doctors_clinics as pro_clinic','pro_clinic.clinic_num','tbl_clinics.id')
			      ->where('pro_clinic.active','O')
				  ->where('tbl_clinics.active','O')
				  ->where('tbl_clinics.id',$request->clinicID)
				  ->whereNotIn('pro_clinic.doctor_num',$doctors_resources_ids)
				  ->get();				
	 
	 $resources = CalendarResources::select('tbl_calendar_resources.id','tbl_calendar_resources.doctor_name',
	                                      'tbl_calendar_resources.doctor_num',
	                                     'tbl_calendar_resources.start_time','tbl_calendar_resources.end_time',
	                                     'clinic.full_name','clinic.open_hours')
	                                   ->join('tbl_clinics as clinic','clinic.id','tbl_calendar_resources.clinic_num')
									   ->where('tbl_calendar_resources.clinic_num',$request->clinicID)
									   ->where('tbl_calendar_resources.active','O')
									   ->whereNULL('tbl_calendar_resources.off_days')
									   ->where('clinic.active','O')
									   ->get();
	
	
	//dd($resources);
	
	//dd($doctors_resources_ids);
	if($no_schedules->count()>0){
		foreach($no_schedules as $res){
		 $doc=Doctor::where('id',$res->doctor_num)->first();
		 $doc_name = $doc->first_name.' '.$doc->last_name;
		 $branch_name=$res->full_name;	
		  $title=__("Branch")." : ".$branch_name." ; ".__("Doctor")." : ".$doc_name; 	
		$bhrs = isset($res->open_hours)?$this->getOpeningHours($res->open_hours):array(); 
		 $id = $res->doctor_num;
		 if(!empty($bhrs)){
						   $output[]=array('id'=>$id,'title'=>$title,'businessHours'=>$bhrs);
						}else{
						
						   $output[]= array('id'=>$id,'title'  => $title);   
						 }
		 
		}
	}	
	if($resources->count()>0){
	foreach($resources as $resource){
				$start_time=Carbon::create($resource->start_time)->format('H:i');
				$end_time=Carbon::create($resource->end_time)->format('H:i');
					 $pro = Doctor::find($resource->doctor_num);
				     $pro_name=$pro->first_name.' '.$pro->last_name;
					 $fac_name=$resource->full_name;
					 if(strlen($fac_name)>60){
						$fac_name=substr($fac_name,0,60).'...';
					  } 
					  $title=__("Branch")." : ".$fac_name." ; ".__("Doctor")." : ".$pro_name; 			
					
				
				$bhrs = isset($resource->open_hours)?$this->getOpeningHours($resource->open_hours):array();
				
				$id = $resource->doctor_num;
		       // $id = $request->clinicID;
				if(!empty($bhrs)){
					   $output[]=array('id'=>$id,'title'=>$title,'businessHours'=>$bhrs,'start_time'=>$start_time,'end_time'=>$end_time );
					}else{
					
					   $output[]= array('id'=>$id,'title'=> $title,'start_time'=>$start_time,'end_time'=>$end_time);   
					 }
				
			
			}
	    }	

	 
	}
	
		
	
  
    if(empty($output)){
		 $output[]=array('title'  =>__("Please create a schedule to use the calendar !"));  
	 }
	 
 return response()->json($output);		
		
	}


/**
*get exam durations 
*/
public function get_exam_duration($lang,Request $request){
	$id_exam = $request->id_exam;
	$duration = TblBillAmounts::where('id',$id_exam)->pluck('duration')[0];
	return response()->json(['duration' => $duration]);	
}

/**
*Back to calendar
*/

public function session_calendar($lang,Request $request){
    $id = $request->id;
    
	switch($request->type){   
	     case 'calendar' :
	       $url = route("external_patient.calendar.index",$lang);
		  break;			  
			
	
	}
	
	return response()->json(['url'=>$url]);
}

/**
*get facility business hours
*/	

function getOpeningHours($hours){
	$monday = $tuesday = $wednesday = $thursday = $friday = $saturday = $sunday = "";
	$open_hours=json_decode($hours,true);
	$result=array();
	
	if(isset($open_hours['Monday'])){
		$monday = explode("-",$open_hours['Monday']);
		$day=1;
		array_push($result,array('startTime'=>trim($monday[0]),'endTime'=>trim($monday[1]),'daysOfWeek'=>array($day)));
		
	}
	
	if(isset($open_hours['Tuesday'])){
		$tuesday = explode("-",$open_hours['Tuesday']);
		$day=2;
		array_push($result,array('startTime'=>trim($tuesday[0]),'endTime'=>trim($tuesday[1]),'daysOfWeek'=>array($day)));
		
	}
	
	if(isset($open_hours['Wednesday'])){
		$wednesday = explode("-",$open_hours['Wednesday']);
		$day=3;
		array_push($result,array('startTime'=>trim($wednesday[0]),'endTime'=>trim($wednesday[1]),'daysOfWeek'=>array($day)));
		
	}
	
	if(isset($open_hours['Thursday'])){
		$thursday = explode("-",$open_hours['Thursday']);
		$day=4;
		array_push($result,array('startTime'=>trim($thursday[0]),'endTime'=>trim($thursday[1]),'daysOfWeek'=>array($day)));
		
	}
	
	if(isset($open_hours['Friday'])){
		$friday = explode("-",$open_hours['Friday']);
		$day=5;
		array_push($result,array('startTime'=>trim($friday[0]),'endTime'=>trim($friday[1]),'daysOfWeek'=>array($day)));
		
	}
	
	if(isset($open_hours['Saturday'])){
		$saturday = explode("-",$open_hours['Saturday']);
		$day=6;
		array_push($result,array('startTime'=>trim($saturday[0]),'endTime'=>trim($saturday[1]),'daysOfWeek'=>array($day)));
		
	}
	
	if(isset($open_hours['Sunday'])){
		$sunday = explode("-",$open_hours['Sunday']);
		$day=0;
		array_push($result,array('startTime'=>trim($sunday[0]),'endTime'=>trim($sunday[1]),'daysOfWeek'=>array($day)));
		
	}
	
		
return $result;	
	
}



}


