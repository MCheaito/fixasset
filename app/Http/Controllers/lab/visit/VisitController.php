<?php
namespace App\Http\Controllers\lab\visit;
use App\Http\Controllers\Controller;

use App\Models\Doctor;
use App\Models\DoctorSignature;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use App\Models\TblVisits;
use App\Models\ExtLab;
use App\Models\LabTests;
use App\Models\LabOrders;
use App\Models\LabResults;
use App\Models\DOCSResults;
use App\Models\TblBillHead;
use App\Models\TblBillSpecifics;
use App\Models\TblBillCurrency;
use App\Models\TblBillRate;
use App\Models\ExtIns;
use App\Models\LabTestFields;
use App\Models\ClinicSMSPack;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Alert;
use DataTables;
use DomDocument;
use UserHelper;

use PDF;
use Image;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Illuminate\Support\Facades\Mail;
use App\Mail\SettingMailAttach;
use Storage;
use App\Models\TblBillPayment;
use App\Models\TblBillPaymentMode;
use Session;
use Illuminate\Support\Facades\View;
//use ArPHP\I18N\Arabic;
use Dompdf\Canvas;
use Dompdf\Options;
use Dompdf\Dompdf;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;




class VisitController extends Controller
{
    
	public function __construct()
    {
        //$this->middleware('auth');
		$this->middleware('auth',['except'=>['sms_link']]);
    }
	
	public function index($lang,Request $request) 
    {
	   
	   $type=auth()->user()->type;	
	   
	   $myId = auth()->user()->id;
	   $idClinic = auth()->user()->clinic_num; 
	   
	   
	   if($type==3){
		$ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$myId)->get();
	    $lab_id = ExtLab::where('lab_user_num',$myId)->value('id');
		$gname = ExtLab::where('lab_user_num',$myId)->value('full_name');
		$doctors=Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
		$Patients = Patient::where('clinic_num',$idClinic)->where('ext_lab',$lab_id)->where('status','O')->orderBy('id','desc')->get();
	   }
	   
	   if($type==2){
		$doctors=Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
        $ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
		$Patients = Patient::where('clinic_num',$idClinic)->where('status','O')->orderBy('id','desc')->get();
			 
	   }
		
		$clinics = Clinic::find($idClinic);
		$lab_tests = LabTests::where('clinic_num',$idClinic)->where('active','Y')
					 ->orderBy('id','desc')->get();
		
		 $specimen = UserHelper::getSpecimenImg();
         $spec_cons = DB::table('tbl_lab_special_considerations')->orderBy('id','desc')->pluck('name','id')->toArray();
		
		switch($type){
			case 2:
			   if ($request->ajax()) {
           
						$filter_date= "";
							
						
						if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
							&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
							 $filter_date= "and DATE(tbl_visits_orders.order_datetime) >= '".$request->filter_fromdate."' ";
							}
						
						if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
							&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
							 $filter_date= "and DATE(tbl_visits_orders.order_datetime) <= '".$request->filter_todate."' ";
							}	
							
						if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
							&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
							 $filter_date = "and DATE(tbl_visits_orders.order_datetime) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
							}	
							
						
						$filter_patient="";
						if(isset($request->filter_patient) && $request->filter_patient!="0" && $request->filter_patient!=""){
						 $filter_patient = "and tbl_visits_orders.patient_num='".$request->filter_patient."' ";
						
						  }
						
								   
						$filter_test_codes="";
						if(isset($request->filter_test_codes) && $request->filter_test_codes!="0" ){
						 $tst_id = $request->filter_test_codes;
						 $order_ids = DB::table('tbl_visits_order_custom_tests')->where('active','Y')->where('test_id',$tst_id)->groupBy('order_id')->pluck('order_id')->toArray();
						 
						 if(count($order_ids )){
							$filter_test_codes = "and tbl_visits_orders.id IN (" . implode(',', $order_ids) . ") ";
							
						 }else{
							$filter_test_codes =" and tbl_visits_orders.id=0"; 
						 }
						 }
						 
					   $filter_doc="";
					   
						if(isset($request->doctor_num) && $request->doctor_num!="0" && $request->doctor_num!=""){
						 $filter_doc = "and tbl_visits_orders.doctor_num='".$request->doctor_num."' ";  
						 }
						 
						$filter_ext_lab="";
					   
						if(isset($request->ext_lab) && $request->ext_lab!="0" && $request->ext_lab!="" ){
						 $filter_ext_lab = "and tbl_visits_orders.ext_lab='".$request->ext_lab."' ";  
						 }
						 
						 $filter_status="";
						 if(isset($request->filter_status) && $request->filter_status!=''){
						   $filter_status = "and tbl_visits_orders.active='".$request->filter_status."' ";  
						 }
						 
						 $filter_order="";
						 
						 if(isset($request->filter_order) && $request->filter_order!='0'){
							 if($request->filter_order=='NF'){
							   $order_ids = LabResults::where('active','Y')->whereNULL('result')->whereNULL('result_txt')->groupBy('order_id')->pluck('order_id')->toArray();
							   $filter_order = "and tbl_visits_orders.id IN (" . implode(',', $order_ids) . ")";	 
							 }else{
							   if($request->filter_order=='NV'){
								 $order_ids = LabResults::where('active','Y')->where('need_validation','Y')->groupBy('order_id')->pluck('order_id')->toArray();
								 if(count($order_ids)==0){
									 //check if there is cultures need validation
								 	 $order_ids = DB::table('tbl_order_culture_results')->where('active','Y')->where('need_validation','Y')->groupBy('order_id')->pluck('order_id')->toArray();
                                     if(count($order_ids)==0){
									  $order_ids = DB::table('tbl_order_culture_results')->where('active','Y')->where('need_validation','Y')->groupBy('order_id')->pluck('order_id')->toArray();
	 								 }
								 }
								
								 if(count($order_ids)==0){
									$filter_order = "and tbl_visits_orders.id=0"; 
								 }else{
									$filter_order = "and tbl_visits_orders.id IN (" . implode(',', $order_ids) . ")"; 
								 }
								 
								
							   }else{
								 if($request->filter_order=='NVF'){
									$order_ids = DB::table('tbl_visits_order_results as r')->join('tbl_visits_orders as o','o.id','r.order_id')->where('r.active','Y')->where('o.status','F')->where('r.need_validation','Y')->groupBy('o.id')->pluck('o.id')->toArray();
									if(count($order_ids)==0){
									 //check if there is cultures need validation
								 	 $order_ids = DB::table('tbl_order_culture_results as r')->join('tbl_visits_orders as o','o.id','r.order_id')->where('r.active','Y')->where('o.status','F')->where('r.need_validation','Y')->groupBy('o.id')->pluck('o.id')->toArray();
                                      if(count($order_ids)==0){
									    $order_ids = DB::table('tbl_order_culture_results as r')->join('tbl_visits_orders as o','o.id','r.order_id')->where('r.active','Y')->where('o.status','F')->where('r.need_validation','Y')->groupBy('o.id')->pluck('o.id')->toArray();
	 								   }
								     }
									
									if(count($order_ids)==0){
									  $filter_order = "and tbl_visits_orders.id=0"; 
								    }else{
									  $filter_order = "and tbl_visits_orders.id IN (" . implode(',', $order_ids) . ")";
									}
								 }else{
									 
									 $filter_order = "and tbl_visits_orders.status='".$request->filter_order."' "; 
								 }
								 
								
							   }
							 }
						 }
						 
						 
						 $filter_patient_tel="";
						 if(isset($request->filter_patient_tel) && $request->filter_patient_tel!="" ){
						 $v1 = "%".str_replace("-","",$request->filter_patient_tel)."%";
						 $filter_patient_tel = "and ( patConsult.first_phone LIKE '".$v1."'  or patConsult.cell_phone LIKE '".$v1."') ";  
						 }
					   
						 
						
						$arr = array('from_date'=>isset($request->filter_fromdate)?$request->filter_fromdate:'',
									 'to_date'=>isset($request->filter_todate)?$request->filter_todate:'',
									 'status'=>$request->filter_status,'patient_num'=>$request->filter_patient,
									 'clinic_num'=>$request->clinic_num,'ext_lab'=>$request->ext_lab,'doctor_num'=>$request->doctor_num,
									 'filter_patient_tel'=>$request->filter_patient_tel,'lab_code'=>$request->filter_test_codes,
									 'filter_result_state'=>$request->filter_order);
						
						UserHelper::drop_session_keys($arr);	
						UserHelper::generate_session_keys($arr);			
					   $access_delete_order = UserHelper::can_access(auth()->user(),'delete_request');
					  
					
					   $sql="select DISTINCT(tbl_visits_orders.id) as order_id,
							  DATE_FORMAT(tbl_visits_orders.order_datetime,'%Y-%m-%d %H:%i') as visit_date_time,
							  clin.full_name as ClinicName,IFNULL(CONCAT(doc.first_name,' ',doc.last_name),'') AS ProName,
							  tbl_visits_orders.active,tbl_visits_orders.status,
							  CONCAT(patConsult.first_name,' ',IFNULL(patConsult.middle_name,''),' ',patConsult.last_name) AS patDetail,
							  IFNULL(l.full_name,'') as ext_lab_name,tbl_visits_orders.request_nb,
							  IFNULL(CONCAT(doc.first_name,IFNULL(CONCAT(' ',doc.middle_name,' '),' '),doc.last_name),'') as doctor_name,
							  tbl_visits_orders.status as order_status,tbl_visits_orders.click_pdf,tbl_visits_orders.reject_note
							  from tbl_visits_orders 
							  INNER JOIN tbl_patients as patConsult on patConsult.id=tbl_visits_orders.patient_num
							  INNER JOIN tbl_clinics as clin on clin.id=tbl_visits_orders.clinic_num
							  LEFT JOIN tbl_doctors as doc on doc.id=tbl_visits_orders.doctor_num
							  LEFT JOIN tbl_external_labs as l on l.id=tbl_visits_orders.ext_lab				  				  
							  where tbl_visits_orders.status<>'I' and tbl_visits_orders.clinic_num=".$idClinic." ".$filter_status." ".$filter_doc." ".$filter_patient."  ".$filter_date."  ".$filter_ext_lab." ".$filter_order." ".$filter_patient_tel." ".$filter_test_codes."
							  ";
							//dd($sql);  
						 $visits= DB::select(DB::raw("$sql"));
						 
					   //dd($visits);
						return Datatables::of($visits)

								->addIndexColumn()
								->addColumn('visit_date_time', function($row){
									 $date = Carbon::parse($row->visit_date_time)->format('d/m/Y H:i');
									return $date;
								})
								->addColumn('sent_result_details', function($row){
								   $email =$sms=$btn='';
								   $patient_name = $row->patDetail;
								   $doctor_name = $row->doctor_name;
								   $guarantor_name = $row->ext_lab_name;
								   $sent_email_hist = DB::table('tbl_visits_order_sent_email_history')->where('order_id',$row->order_id)->latest()->first();
								   $sent_sms_hist = DB::table('tbl_visits_order_sent_sms_history')->where('order_id',$row->order_id)->latest()->first();

								   if(isset($sent_email_hist)){
									 $sent_pat = $sent_email_hist->to_patient; 
									 $sent_doc = $sent_email_hist->to_doctor;
									 $sent_guarantor = $sent_email_hist->to_guarantor;
									 if( isset($sent_email_hist->email_date) && $sent_email_hist->email_date!=''){
										 $email.='<div>'.__('Last Email is sent on').' '.Carbon::parse($sent_email_hist->email_date)->format('d/m/Y H:i').' '.__('to').' : '.'</div>';
										 if($sent_pat=='Y'){
											 $email.='<div>'.__('Patient').' : '.$patient_name.'</div>';
										 }
										 if($sent_doc=='Y'){
											 $email.='<div>'.__('Doctor').' : '.$doctor_name.'</div>';
										 }
										 if($sent_guarantor=='Y'){
											 $email.='<div>'.__('Guarantor').' : '.$guarantor_name.'</div>';
										 }
									 }
								   }
								if(isset($sent_sms_hist)){	 
									 $sent_pat = $sent_sms_hist->to_patient; 
									 $sent_doc = $sent_sms_hist->to_doctor;
									 $sent_guarantor = $sent_sms_hist->to_guarantor;
									 if( isset($sent_sms_hist->sms_date) && $sent_sms_hist->sms_date!=''){
										 $sms.='<div>'.__('Last SMS is  sent on').' '.Carbon::parse($sent_sms_hist->sms_date)->format('d/m/Y H:i').' '.__('to').' : '.'</div>';
										 if($sent_pat=='Y'){
											 $sms.='<div>'.__('Patient').' : '.$patient_name.'</div>';
										 }
										 if($sent_doc=='Y'){
											 $sms.='<div>'.__('Doctor').' : '.$doctor_name.'</div>';
										 }
										 if($sent_guarantor=='Y'){
											 $sms.='<div>'.__('Guarantor').' : '.$guarantor_name.'</div>';
										 }
									 }
								   
								   }
								   
								   if($email!=''){
									 $btn .='<button type="button" class="p-0 pl-1 btn btn-icon" onclick="event.preventDefault();openSwalMSG(\''.$email.'\');"><i class="fas fa-envelope text-primary"></i></button>';
								   }
								   
								  if($sms!=''){
									 $btn .='<button type="button" class="p-0 pl-1 btn btn-icon" onclick="event.preventDefault();openSwalMSG(\''.$sms.'\');"><i class="fas fa-sms text-primary"></i></button>';
								   }
								  
								  
								   return $btn;
								})
								->addColumn('pat_details', function($row){
									 if($row->active=='Y'){
									 $btn = '<a href="'.route('lab.visit.edit',[app()->getLocale(),$row->order_id]).'">'.$row->patDetail.'</a>';                     
									 }else{
										$btn =$row->patDetail; 
									 }
								   return $btn;
								})
								->addColumn('color_status', function($row){
									switch($row->order_status){
										case 'P': $btn='<div class="form-control bg-gradient-danger">Pending</div>'; break;
										case 'I': $btn='<div class="form-control bg-gradient-info">In Progress</div>'; break;
										case 'F': $btn='<div class="form-control bg-gradient-yellow">Finished</div>'; break;
										case 'V': $btn='<div class="form-control bg-gradient-teal">Validated</div>'; break;
										default: $btn='<div>Undefined</div>';
									}
								  return $btn;
								})
								->addColumn('action', function($row) use($access_delete_order){
									  																			
										 $disabled = ($access_delete_order)?'':'disabled';
										 
										 if($row->active=='Y'){
										    $btn='<button type="button" class="p-1 btn btn-md btn-clean btn-icon" onclick="event.preventDefault();openCodesModal('.$row->order_id.');"><i title="'.__("Specimen Details").'" class="fas fa-plus text-primary"></i></button>';
											$btn .= '<a href="'.route('lab.visit.edit',[app()->getLocale(),$row->order_id]).'"  title="'.__("edit").'" class="btn btn-md btn-clean btn-icon  editVisit"><i class="far fa-edit text-primary"></i></a>';                     
 									        $btn .= '<a href="javascript::void(0)"  title="'.__("Download Result").'" class="btn btn-md btn-clean btn-icon" onclick="event.preventDefault();printPDF('.$row->order_id.')"><i class="far fa-file-pdf text-primary"></i></a>';                     
											if($row->order_status=='V'){
											 //do nothing do not delete	
											}else{
											$btn.='<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->order_id.'" class="toggle-chk" checked '.$disabled.'><span class="slideon-slider"></span></label>';
											}
										 }else{
										    $btn='<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->order_id.'" class="toggle-chk" '.$disabled.'><span class="slideon-slider"></span></label>';
 										 }
										
									    return $btn;
 
									 
									})
								 ->addColumn('test_names', function($row){
									 $chosen_codes = DB::table('tbl_visits_order_custom_tests')->where('order_id',$row->order_id)->where('active','Y')->pluck('test_id')->toArray();
									 $code = LabTests::whereIn('id',$chosen_codes)->pluck('test_name')->toArray();
									 $text = implode(",",$code);
									 if(strlen($text)>50){
										$res='<div class="content-container"><span class="truncated-content">'.rtrim(substr($text,0,50)).'</span><span class="load-more-btn" onclick="event.preventDefault();loadMore(this);"><i title="{{__("More")}}" class="fas fa-plus text-primary"></i></span><span class="full-content" style="display:none;">'.$text.'</span><span class="load-less-btn" onclick="event.preventDefault();loadLess(this);" style="display:none;"><i title="{{__("Less")}}" class="fas fa-minus text-primary"></i></span></div>';
										 return $res;
									 }else{
										 return $text;
									 }
									
								 })	 

								->rawColumns(['action','test_names','pat_details','color_status','sent_result_details'])
								
								->make(true);

					}
	   
     
	             return view('lab.visit.index')->with(['clinics'=>$clinics,'lab_tests'=>$lab_tests,'specimen'=>$specimen,'spec_cons'=>$spec_cons]); 
			
			break;
			case 3:
			 
			  $groups = LabTests::select('id','test_name','testord')
				  ->where('active','Y')
				  ->where('is_group','Y')
				  ->whereNOTNULL('test_name')
				  ->whereNOTNULL('test_name','<>','')
				  ->orderBy('testord')
				  ->get();
		
		     $tests= DB::table('tbl_lab_tests')
		         ->select('id','test_name','is_group','referred_tests','category_num','testord')
				 ->whereRaw('active="Y" and (is_group = "Y" or (is_group <> "Y" and group_num IS NULL))')
				 ->orderBy('testord')
				 ->get();
				 
			$categories = DB::table('tbl_lab_categories')->where('active','Y')->orderBy('testord')->get();
		    $profiles = DB::table('tbl_lab_tests_profiles')->where('clinic_num',$idClinic)->where('active','Y')->get();	 
		
			  
			  if ($request->ajax()) {
           
						$filter_date= "";
												
						if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
							&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
							 $filter_date= "and DATE(tbl_visits_orders.order_datetime) >= '".$request->filter_fromdate."' ";
							}
						
						if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
							&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
							 $filter_date= "and DATE(tbl_visits_orders.order_datetime) <= '".$request->filter_todate."' ";
							}	
							
						if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
							&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
							 $filter_date = "and DATE(tbl_visits_orders.order_datetime) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
							}	
							
						
						$filter_patient="";
						if(isset($request->filter_patient) && $request->filter_patient!="0" ){
						 $filter_patient = "and tbl_visits_orders.patient_num='".$request->filter_patient."' ";
						
						  }
						
								   
						$filter_test_codes="";
						if(isset($request->filter_test_codes) && $request->filter_test_codes!="0" ){
						 $tst_id = $request->filter_test_codes;
						 $order_ids = DB::table('tbl_visits_order_custom_tests')->where('active','Y')->where('test_id',$tst_id)->groupBy('order_id')->pluck('order_id')->toArray();
						 
						 if(count($order_ids )){
							$filter_test_codes = "and tbl_visits_orders.id IN (" . implode(',', $order_ids) . ") ";
							
						 }else{
							$filter_test_codes =" and tbl_visits_orders.id=0"; 
						 }
						 }
						 
					   $filter_doc="";
					   
						if(isset($request->doctor_num) && $request->doctor_num!="0"){
						 $filter_doc = "and tbl_visits_orders.doctor_num='".$request->doctor_num."' ";  
						 }
						 
												 
						 $filter_status="";
						 if(isset($request->filter_status) && $request->filter_status!=""){
						   if($request->filter_status=='R'){
							   $filter_status = "and tbl_visits_orders.status='I' and tbl_visits_orders.active='N' ";    
						   }else{
						     if($request->filter_status=='N'){
							    $filter_status = "and tbl_visits_orders.status<>'I' and tbl_visits_orders.active='".$request->filter_status."' ";  
						     }else{
								$filter_status = "and tbl_visits_orders.active='".$request->filter_status."' "; 
							 }
						   }
						 }
						 
						 $filter_order=""; 
						 
						 if(isset($request->filter_order) && $request->filter_order!='0'){
							 if($request->filter_order=='V'){
								$filter_order = "and tbl_visits_orders.status='".$request->filter_order."' ";  
							 }else{
								 $filter_order="and tbl_visits_orders.status <> 'V'";  
							 }
							 
							
							}
						 
						 
						 $filter_patient_tel="";
						 if(isset($request->filter_patient_tel) && $request->filter_patient_tel!="" ){
						 $v1 = "%".str_replace("-","",$request->filter_patient_tel)."%";
						 $filter_patient_tel = "and ( patConsult.first_phone LIKE '".$v1."'  or patConsult.cell_phone LIKE '".$v1."') ";  
						 }
					   
						 
						
						
					   $sql="select DISTINCT(tbl_visits_orders.id) as order_id,
							  DATE_FORMAT(tbl_visits_orders.order_datetime,'%Y-%m-%d %H:%i') as visit_date_time,
							  clin.full_name as ClinicName,IFNULL(CONCAT(doc.first_name,' ',doc.last_name),'') AS ProName,
							  tbl_visits_orders.active,tbl_visits_orders.status,tbl_visits_orders.other_reject_note,
							  CONCAT(patConsult.first_name,' ',IFNULL(patConsult.middle_name,''),' ',patConsult.last_name) AS patDetail,
							  IFNULL(l.full_name,'') as ext_lab_name,tbl_visits_orders.request_nb,tbl_visits_orders.click_pdf,tbl_visits_orders.reject_note,
							  IFNULL(CONCAT(doc.first_name,IFNULL(CONCAT(' ',doc.middle_name,' '),' '),doc.last_name),'') as doctor_name,
							  tbl_visits_orders.status as order_status
							  from tbl_visits_orders 
							  INNER JOIN tbl_patients as patConsult on patConsult.id=tbl_visits_orders.patient_num
							  INNER JOIN tbl_clinics as clin on clin.id=tbl_visits_orders.clinic_num
							  LEFT JOIN tbl_doctors as doc on doc.id=tbl_visits_orders.doctor_num
							  LEFT JOIN tbl_external_labs as l on l.id=tbl_visits_orders.ext_lab				  				  
							  where tbl_visits_orders.ext_lab='".$lab_id."' and tbl_visits_orders.clinic_num=".$idClinic." ".$filter_status." ".$filter_doc." ".$filter_patient."  ".$filter_date."   ".$filter_order." ".$filter_patient_tel." ".$filter_test_codes."
							  ";
							//dd($sql);  
						 $visits= DB::select(DB::raw("$sql"));
						 
					   //dd($visits);
						return Datatables::of($visits)

								->addIndexColumn()
								->addColumn('visit_date_time', function($row){
									 $date = Carbon::parse($row->visit_date_time)->format('d/m/Y H:i');
									return $date;
								})
								->addColumn('reject_note_list', function($row){
									$reject_note = $row->reject_note;
									if(isset($row->other_reject_note) && $row->other_reject_note!=''){
										$reject_note.='<div>'.$row->other_reject_note.'</div>';
									}
									return $reject_note;
								})
							    ->addColumn('color_status', function($row){
									switch($row->order_status){
										case 'P': case 'I': case 'F':
										 $btn='<div class="form-control bg-gradient-info">In Progress</div>'; 
										break;
										case 'V': $btn='<div class="form-control bg-gradient-teal">Completed</div>'; break;
										default: $btn='<div>Undefined</div>';
									}
								  return $btn;
								})
								->addColumn('action', function($row){
									   
									   $order_id = $row->order_id;
;
									   $disabled = ($row->active=='Y')?'':'disabled';
									   
									    $btn='<button type="button" class="p-1 btn btn-md btn-clean btn-icon" onclick="event.preventDefault();openCodesModal('.$order_id.');"><i title="'.__("Specimen Details").'" class="fas fa-plus text-primary"></i></button>';

									    if($row->active=='N'){
										    switch($row->order_status){ 
											 case 'I':
											   $btn.='<span style="border-radius:30px;" class="p-1 label-size badge bg-gradient-danger">'.__("Rejected").'</span>';
   									         break;
											 default:  
											   $btn.='<span style="border-radius:30px;" class="p-1 label-size badge bg-gradient-danger">'.__("Cancelled by Lab").'</span>';
											 
											}
										}else{
											switch($row->order_status){
												case 'I': 
												 $btn.='<span style="border-radius:30px;" class="p-1 label-size badge bg-gradient-secondary">'.__("Waiting Decision").'</span>';
												break;
												case 'P': case 'F': 
												  $btn.='<span style="background:#1bbc9b;linear-gradient(180deg,#41d1a7,#20c997) repeat-x !important;color:#fff;border-radius:30px;" class="p-1 label-size badge">'.__("Accepted").'</span>';
												break;
												case 'V': 
												  $pdf_icon = ($row->click_pdf=='Y')?'<i class="far fa-file-pdf text-primary"></i>':'<div style="position: relative;display: inline-block;"><span class="text-primary" style="position: absolute;top: 0;left: 0;font-size:20px;">*</span><i class="far fa-file-pdf text-primary"></i></div>';
											      $title =($row->click_pdf=='Y')?__("Download Result"):__("Download New Result");
											      $btn .= '<a href="javascript::void(0)"  title="'.$title.'" class="p-1 btn btn-lg btn-clean btn-icon   '.$disabled.'" onclick="event.preventDefault();printPDF('.$row->order_id.')">'.$pdf_icon.'</a>';                     
												break;
											} 
											
										 }
									  
									  
									  
									   return $btn;

									 })
								 
								 ->addColumn('sent_result_details', function($row){
								   $email =$sms=$btn='';
								   $patient_name = $row->patDetail;
								   $doctor_name = $row->doctor_name;
								   $guarantor_name = $row->ext_lab_name;
							   
								   $sent_email_hist = DB::table('tbl_visits_order_sent_email_history')->where('order_id',$row->order_id)->latest()->first();
								   $sent_sms_hist = DB::table('tbl_visits_order_sent_sms_history')->where('order_id',$row->order_id)->latest()->first();

								   if(isset($sent_email_hist)){
									 $sent_pat = $sent_email_hist->to_patient; 
									 $sent_doc = $sent_email_hist->to_doctor;
									 $sent_guarantor = $sent_email_hist->to_guarantor;
									 if( isset($sent_email_hist->email_date) && $sent_email_hist->email_date!=''){
										 $email.='<div>'.__('Last Email is sent on').' '.Carbon::parse($sent_email_hist->email_date)->format('d/m/Y H:i').' '.__('to').' : '.'</div>';
										 if($sent_pat=='Y'){
											 $email.='<div>'.__('Patient').' : '.$patient_name.'</div>';
										 }
										 if($sent_doc=='Y'){
											 $email.='<div>'.__('Doctor').' : '.$doctor_name.'</div>';
										 }
										 if($sent_guarantor=='Y'){
											 $email.='<div>'.__('Guarantor').' : '.$guarantor_name.'</div>';
										 }
									 }
								   }
								if(isset($sent_sms_hist)){	 
									 $sent_pat = $sent_sms_hist->to_patient; 
									 $sent_doc = $sent_sms_hist->to_doctor;
									 $sent_guarantor = $sent_sms_hist->to_guarantor;
									 if( isset($sent_sms_hist->sms_date) && $sent_sms_hist->sms_date!=''){
										 $sms.='<div>'.__('Last SMS is  sent on').' '.Carbon::parse($sent_sms_hist->sms_date)->format('d/m/Y H:i').' '.__('to').' : '.'</div>';
										 if($sent_pat=='Y'){
											 $sms.='<div>'.__('Patient').' : '.$patient_name.'</div>';
										 }
										 if($sent_doc=='Y'){
											 $sms.='<div>'.__('Doctor').' : '.$doctor_name.'</div>';
										 }
										 if($sent_guarantor=='Y'){
											 $sms.='<div>'.__('Guarantor').' : '.$guarantor_name.'</div>';
										 }
									 }
								   
								   }
								   
								   if($email!=''){
									 $btn .='<button type="button" class="p-0 pl-1 btn btn-icon" onclick="event.preventDefault();openSwalMSG(\''.$email.'\');"><i class="fas fa-envelope text-primary"></i></button>';
								   }
								   
								  if($sms!=''){
									 $btn .='<button type="button" class="p-0 pl-1 btn btn-icon" onclick="event.preventDefault();openSwalMSG(\''.$sms.'\');"><i class="fas fa-sms text-primary"></i></button>';
								   }
								  
								  
								   return $btn;
								})
								 ->addColumn('test_names', function($row){
									 $chosen_codes = DB::table('tbl_visits_order_custom_tests')->where('order_id',$row->order_id)->where('active','Y')->pluck('test_id')->toArray();
									 $code = LabTests::whereIn('id',$chosen_codes)->pluck('test_name')->toArray();
									 $text = implode(",",$code);
									 if(strlen($text)>30){
										$res='<div class="content-container"><span class="truncated-content">'.rtrim(substr($text,0,30)).'</span><span class="load-more-btn" onclick="event.preventDefault();loadMore(this);"><i title="{{__("More")}}" class="fas fa-plus text-primary"></i></span><span class="full-content" style="display:none;">'.$text.'</span><span class="load-less-btn" onclick="event.preventDefault();loadLess(this);" style="display:none;"><i title="{{__("Less")}}" class="fas fa-minus text-primary"></i></span></div>';
										 return $res;
									 }else{
										 return $text;
									 }
									
								 })	 

								->rawColumns(['action','test_names','color_status','sent_result_details','reject_note_list'])
								
								->make(true);

					}
	   
     
	         return view('lab.guarantor.index')->with(['clinics'=>$clinics,'ext_labs'=>$ext_labs,'specimen'=>$specimen,'spec_cons'=>$spec_cons,'profiles'=>$profiles,
	                                                   'lab_tests'=>$lab_tests,'gid'=>$lab_id,'gname'=>$gname,'tests'=>$tests,'groups'=>$groups,'categories'=>$categories]); 
			break;
		}
		
		
	  										
	    		
}

public function waiting_list($lang,Request $request){
       
	   $idClinic = auth()->user()->clinic_num; 	
	   $clinics = Clinic::find($idClinic);
	   $lab_tests = LabTests::where('clinic_num',$idClinic)->where('active','Y')
					 ->orderBy('id','desc')->get();
	   $specimen = UserHelper::getSpecimenImg();
       $spec_cons = DB::table('tbl_lab_special_considerations')->orderBy('id','desc')->pluck('name','id')->toArray();
	   $reject_notes = LabOrders::whereNOTNULL('reject_note')->where('reject_note','<>','')->distinct()->pluck('reject_note');
	if ($request->ajax()) {
       
	   $visits = DB::table('tbl_visits_orders as o')
		          ->select('o.id as order_id',
				           DB::raw("DATE_FORMAT(o.order_datetime,'%Y-%m-%d %H:%i') as visit_date_time"),
						   'clin.full_name as ClinicName',DB::raw(" IFNULL(CONCAT(doc.first_name,IFNULL(CONCAT(' ',doc.middle_name,' '),' '),doc.last_name),'') as doctor_name"),
	                       'o.active','o.status as order_status','o.request_nb','o.click_pdf','o.reject_note','o.other_reject_note',
						   DB::raw("CONCAT(patConsult.first_name,' ',IFNULL(patConsult.middle_name,''),' ',patConsult.last_name) AS patDetail"),
	                       'l.full_name as ext_lab_name')
                 ->join('tbl_patients as patConsult','patConsult.id','o.patient_num')
				 ->join('tbl_clinics as clin','clin.id','o.clinic_num')
				 ->leftjoin('tbl_doctors as doc','doc.id','o.doctor_num')
				 ->leftjoin('tbl_external_labs as l','l.id','o.ext_lab')
				 ->where('o.clinic_num',$idClinic)
				 ->where('o.status','I');
	   
	   
		if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
			&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
		     $visits=$visits->where(DB::raw("DATE(o.order_datetime)"),'>=',$request->filter_fromdate);
			}else{
				 if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
					&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL)){
						$visits=$visits->where(DB::raw("DATE(o.order_datetime)"),'<=',$request->filter_todate);	 
					}else{
						if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
							&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
							$visits=$visits->where(DB::raw("DATE(o.order_datetime)"),'>=',$request->filter_fromdate)
							               ->where(DB::raw("DATE(o.order_datetime)"),'<=',$request->filter_todate); 					 
							}	
					}	
				
			 }
						
		if(isset($request->filter_patient) && $request->filter_patient!="0" && $request->filter_patient!=""){
			$visits=$visits->where('o.patient_num',$request->filter_patient);		 	
						  }
						
		if(isset($request->filter_test_codes) && $request->filter_test_codes!="0" ){
		   $tst_id = $request->filter_test_codes;
		   $order_ids = DB::table('tbl_visits_order_custom_tests')->where('active','Y')->where('test_id',$tst_id)->groupBy('order_id')->pluck('order_id')->toArray();
		   if(count($order_ids )){
				$visits=$visits->whereIn('o.id',$order_ids);			
				 }else{
				$visits=$visits->whereIn('o.id',0);
					}
			}

		if(isset($request->doctor_num) && $request->doctor_num!="0" && $request->doctor_num!=""){
				$visits=$visits->where('o.doctor_num',$request->doctor_num);		 
						 }
						 
	    if(isset($request->ext_lab) && $request->ext_lab!="0" && $request->ext_lab!="" ){
				$visits=$visits->where('o.ext_lab',$request->ext_lab);		 
				 }
		
		if(isset($request->filter_status) && $request->filter_status!=''){
				$visits=$visits->where('o.active',$request->filter_status);			   
						 }
	    
		if(isset($request->filter_patient_tel) && $request->filter_patient_tel!="" ){
				$v1 = "%".str_replace("-","",$request->filter_patient_tel)."%";
			    $visits=$visits->whereRaw('patConsult.first_phone LIKE ? or patConsult.cell_phone LIKE ?',[$v1,$v1]);
				 }
		
		$arr = array('wl_from_date'=>isset($request->filter_fromdate)?$request->filter_fromdate:'',
					 'wl_to_date'=>isset($request->filter_todate)?$request->filter_todate:'',
					 'wl_status'=>$request->filter_status,
					 'wl_filter_patient_tel'=>$request->filter_patient_tel,
					 'wl_lab_code'=>$request->filter_test_codes);
		UserHelper::drop_session_keys($arr);	
		UserHelper::generate_session_keys($arr);			
		
		$visits=$visits->distinct()->get();
		
		return Datatables::of($visits)
					->addIndexColumn()
					->addColumn('reject_notes_list',function($row){
						$reject_note = $row->reject_note;
						if(isset($row->other_reject_note) && $row->other_reject_note!=''){
						 $reject_note.='<div>'.$row->other_reject_note.'</div>';	
						}
						return $reject_note;
					})
					->addColumn('visit_date_time', function($row){
									 $date = Carbon::parse($row->visit_date_time)->format('d/m/Y H:i');
									return $date;
								})
					
					->addColumn('color_status', function($row){
									switch($row->order_status){
										case 'P': $btn='<div class="form-control bg-gradient-danger">Pending</div>'; break;
										case 'I': $btn='<div class="form-control bg-gradient-info">In Progress</div>'; break;
										case 'F': $btn='<div class="form-control bg-gradient-yellow">Finished</div>'; break;
										case 'V': $btn='<div class="form-control bg-gradient-teal">Validated</div>'; break;
										default: $btn='<div>Undefined</div>';
									}
								  return $btn;
								})
					->addColumn('action', function($row){
						$btn='<button type="button" class="p-1 btn btn-md btn-clean btn-icon" onclick="event.preventDefault();openCodesModal('.$row->order_id.');"><i title="'.__("Specimen Details").'" class="fas fa-plus text-primary"></i></button>';

						if($row->active=='N'){
								 $btn.='<span style="border-radius:30px;" class="p-1 label-size badge bg-gradient-danger">'.__("Rejected").'</span>';
   							}else{
								if($row->order_status=='I'){
							      //In progress status accept or reject
							      $btn.='<button type="button" class="p-1 btn btn-md btn-clean btn-icon" onclick="event.preventDefault();acceptRequest('.$row->order_id.')" title="'.__("Accept").'"><i class="fa fa-check text-success"></i></button>';
							      $btn.='<button type="button" class="p-1 btn btn-md btn-clean btn-icon" onclick="event.preventDefault();rejectRequest('.$row->order_id.')" title="'.__("Reject").'"><i class="fa fa-times text-danger"></i></button>';
							    }
							}	
						
						
						return $btn;		   
						})
					->addColumn('test_names', function($row){
									 $chosen_codes = DB::table('tbl_visits_order_custom_tests')->where('order_id',$row->order_id)->where('active','Y')->pluck('test_id')->toArray();
									 $code = LabTests::whereIn('id',$chosen_codes)->pluck('test_name')->toArray();
									 $text = implode(",",$code);
									 if(strlen($text)>50){
										$res='<div class="content-container"><span class="truncated-content">'.rtrim(substr($text,0,50)).'</span><span class="load-more-btn" onclick="event.preventDefault();loadMore(this);"><i title="{{__("More")}}" class="fas fa-plus text-primary"></i></span><span class="full-content" style="display:none;">'.$text.'</span><span class="load-less-btn" onclick="event.preventDefault();loadLess(this);" style="display:none;"><i title="{{__("Less")}}" class="fas fa-minus text-primary"></i></span></div>';
										 return $res;
									 }else{
										 return $text;
									 }
									
								 })	 

					->rawColumns(['action','test_names','color_status','reject_notes_list'])
					->make(true);

					}

return view('lab.guarantor.waiting_list')->with(['clinics'=>$clinics,'reject_notes'=>$reject_notes,
                                      'lab_tests'=>$lab_tests,'specimen'=>$specimen,'spec_cons'=>$spec_cons]); 
	
}


public function create($lang,Request $request)
    {
		
		$patient_num = $request->patient;
		$date_visit = $request->date_visit;
		$clinic_num = $request->clinic;
		
		$patient = Patient::find($patient_num);
		$ext_lab = (isset($patient) && isset($patient->ext_lab))?$patient->ext_lab:NULL;
		$doctor_num = (isset($patient) && isset($patient->doctor_num))?$patient->doctor_num:NULL;
		
		Session::forget('order_patient_num');
		Session::forget('order_clinic_num');
		Session::forget('order_date_time');
		Session::forget('order_ext_lab');
		Session::forget('order_doctor_num');
		
		Session::put('order_patient_num',$patient_num);
		Session::put('order_clinic_num',$clinic_num);
		Session::put('order_date_time',$date_visit);
		Session::put('order_ext_lab',$ext_lab);
		Session::put('order_doctor_num',$doctor_num);
		
		
		$location= route('lab.visit.edit',$lang);
        
		return response()->json(["success"=>true,"location"=>$location]);
				
    }
	
public function edit($lang,$id=NULL)
    {
		
		
		$sms_pack = $this->get_sms_pack(auth()->user()->clinic_num);
		$view_bills = UserHelper::can_access(auth()->user(),'view_bills');
        
		$view_results = UserHelper::can_access(auth()->user(),'view_results');
		$validate_results = UserHelper::can_access(auth()->user(),'validate_results'); 
		$edit_results = UserHelper::can_access(auth()->user(),'edit_results'); 
        
		$view_culture = UserHelper::can_access(auth()->user(),'view_culture'); 
        $edit_culture = UserHelper::can_access(auth()->user(),'edit_culture');		
        
		$view_attachments = UserHelper::can_access(auth()->user(),'view_attachments'); 

        $view_templates = UserHelper::can_access(auth()->user(),'view_templates'); 
        $edit_templates = UserHelper::can_access(auth()->user(),'edit_templates');				
		
		$order = isset($id)?LabOrders::find($id):NULL;
		
		$patient_num = isset($order)?$order->patient_num:Session::get('order_patient_num');
	   	$patient = Patient::where('status','O')->where('id',$patient_num)->first();
		
		$patient_data =$patient->first_name;
		if(isset($patient->middle_name) && $patient->middle_name!=''){
			$patient_data.= ' '.$patient->middle_name;
		}
		
		$patient_data .= ' '.$patient->last_name.'   [   ';
		
		$patient_gender = $patient->sex;
		switch($patient_gender){
			case 'M':
			  $patient_data .='Gender'.' : '.'Male';
			break;
			case 'F':
			  $patient_data .='Gender'.' : '.'Female';
			break;
		}
		
			
		if(isset($patient->cell_phone) && $patient->cell_phone!=''){
			$patient_data.='   '.'Cellular'.' : '.$patient->cell_phone;
		}
		
		$patient_data .='   ]';
		
		
		$ageInyears = 0;
		$age_data = '';
		if(isset($patient->birthdate) && $patient->birthdate!=''){
		 //$age_data =UserHelper::getPatAge($patient->birthdate);
		 $age_data =UserHelper::getPatExactAge($patient->birthdate);
		 $age = Carbon::parse($patient->birthdate)->diffInYears(Carbon::now());
		 $ageInyears = $age;
		 if($age==0){
			$age=Carbon::parse($patient->birthdate)->diffInMonths(Carbon::now());
		    
			if($age==0){
		      //age in days
		      $age=Carbon::parse($patient->birthdate)->diffInDays(Carbon::now());
			  $ageInDays = $age;
			  //$year = Carbon::now()->year; 
              //$isLeapYear = Carbon::create($year, 1, 1, 0, 0, 0)->isLeapYear();
			  //$daysInYear = $isLeapYear ? 366 : 365;
			  $ageInYears = number_format((float)$ageInDays / 365.25,2);
			 
		    }else{
			  
			  $ageInyears = number_format((float)$age/12,2);
		     }
	       }
		}
		
		
	
		
		$id_clinic = isset($order)?$order->clinic_num:Session::get('order_clinic_num');
		$clinic = Clinic::where('id',$id_clinic)->first();
		
		
		$user_num = auth()->user()->id;
		$user_type= auth()->user()->type;
	    
		if($user_type==1){
		$doctors=Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('doctor_user_num',$user_num)->get();
        $ext_labs = ExtLab::where('status','A')->orderBy('full_name')->get(); 
		}
		if($user_type==3){
		$ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$user_num)->get();
		$doctors = Doctor::where('active','O')->orderBy('first_name')->orderBy('last_name')->get(); 
		}
		if($user_type==2){
		$doctors = Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get(); 
        $ext_labs = ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get(); 
		}
		
		
		$currencyUSD=TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
		$currencyEURO=TblBillCurrency::where('active','O')->where('abreviation','EUR')->first();
		$lbl_usd = isset($currencyUSD)?$currencyUSD->price:15000;
		$lbl_euro = isset($currencyEURO)?$currencyEURO->price:15000;
		$categories = DB::table('tbl_lab_categories')->where('active','Y')->orderBy('testord')->get();
		$profiles = DB::table('tbl_lab_tests_profiles')->where('clinic_num',$id_clinic)->where('active','Y')->get();
		
		
		$order_tests = array();
		$culture_test = $culture_test_det = collect();
		$documents = collect();
		$order_grps = array();
		$ReqPatient = NULL;
		$ReqPay = $ReqRef = $ReqDis = $test_textResults = $gram_staim_results = collect();
		$cptpCount= $ReqPay->count();
		$cptrCount= $ReqRef->count();
		$cptdCount= $ReqDis->count();
		$methodepay = $rates = $currencys =  collect();
		$profile_ids = array();
		$culture_tests_ids = array();
		$pay = $refund = $balance = $totalf = $stotal = $etotal = $payd = $refundd = $balanced=0.00;
		$results = collect();
		$savedBacterias = $savedBacteriaIDs= $savedAntibiotics = array();
		$result_textResults =$template_reports = collect();
		$valid_tests_cnt = 0;
		//$get_formulas = array();
		
		$num = -1;
		
		$groups = LabTests::select('id','test_name','testord')
				  ->where('active','Y')
				  ->where('is_group','Y')
				  ->whereNOTNULL('test_name')
				  ->whereNOTNULL('test_name','<>','')
				  ->orderBy('testord')
				  ->get();
		
		 $tests= DB::table('tbl_lab_tests')
		         ->select('id','test_name','is_group','referred_tests','category_num','testord')
				 ->whereRaw('active="Y" and (is_group = "Y" or (is_group <> "Y" and group_num IS NULL))')
				 ->orderBy('testord')
				 ->get();
		
			 
		
		$sbacteria = DB::table('tbl_lab_sbacteria')->where('active','Y')->orderBy('testord')->get();
	
		if(isset($order)){
		  
		  $template_reports = DB::table('tbl_visits_order_template_reports as tr')
		                      ->select('tr.id','tr.description','t.test_name','cat.descrip as cat_name')
							  ->join('tbl_lab_tests as t','t.id','tr.test_id')
							  ->leftjoin('tbl_lab_categories as cat','cat.id','t.category_num')
							  ->where('tr.order_id',$order->id)->where('tr.active','Y')->get();
		  
		  $order_tests = DB::table('tbl_visits_order_custom_tests')->where('order_id',$order->id)->where('active','Y')->pluck('test_id')->toArray();
		  
		  $documents = DOCSResults::where('order_id',$order->id)->where('active','Y')->get();
		  $ReqPatient = TblBillHead::where('order_id',$order->id)->where('status','O')->first();
		  
		  $culture_test = DB::table('tbl_order_culture_results as o')
		                  ->select('o.id','o.need_validation','o.order_id','o.test_id','o.gram_staim','o.culture_result','t.test_name')
						  ->join('tbl_lab_tests as t','t.id','o.test_id')
						  ->where('o.order_id',$order->id)->where('o.active','Y')->get();
		  
		  $culture_ids = DB::table('tbl_order_culture_results')->where('order_id',$order->id)->where('active','Y')->pluck('id')->toArray();
		  
		  $culture_test_ids = DB::table('tbl_order_culture_results')->where('order_id',$order->id)->where('active','Y')->pluck('test_id')->toArray();
		
		  
		  $test_textResults = DB::table('tbl_lab_text_results')->whereIn('test_id',$culture_test_ids)->where('status','Y')->orderBy('name')->get();
          $gram_staim_results = DB::table('tbl_lab_gram_stain_results')->whereIn('test_id',$culture_test_ids)->where('status','Y')->orderBy('name')->get(); 
           //dd($test_textResults);
		  $arr1 = $order_tests;
		  
		  //dd($profiles);
		  foreach($profiles as $p){
			$arr2 = json_decode($p->profile_tests,true);
			//dd($arr2);
			if(empty(array_diff($arr2,$arr1))){
				array_push($profile_ids,$p->id);
			}
		  }
		  
		  //$tests = isset($order->tests)?json_decode($order->tests,true):array();
		  
		  
		  $results = DB::table('tbl_visits_order_results as r')
	               ->select(
				            'r.id','r.prev_result_num','t.is_title',
				            DB::raw("IF(t.test_code IS NOT NULL and t.test_code <>'',CONCAT(t.test_name,' ','(',' ',t.test_code,' ',')'),t.test_name) as test_name"),
				            DB::raw("IFNULL(trim(g.test_name),'Other') as group_name"),
							DB::raw("IFNULL(t.unit,'') as unit"),
							't.test_type',
							'r.test_id',
							't.dec_pts',
							DB::raw("IFNULL(t.normal_value,'') as range_val"),
							DB::raw("IFNULL(r.result,'') as result"),
							DB::raw("IFNULL(r.result_txt,'') as result_txt"),
							DB::raw("IF(o.status='F','Finished',IF(o.status='P','Pending','Validated')) as status"),
							'r.result_status',
							DB::raw("IFNULL(prev.result,'') as prev_result"),
							DB::raw("IFNULL(r.field_num,'') as field_num"),
							DB::raw("IFNULL(r.sign,'') as result_sign"),
							't.testord as position',
							'r.ref_range',
							'r.one_ref_range',
							'r.calc_result',
							'r.calc_unit',
							't.is_printed',
							'r.need_validation',
							'g.testord as group_order',
							DB::raw("IFNULL(r.group_num,0) as group_num"),
							DB::raw("IFNULL(cat.testord,0) as cat_num")
							)
				   ->join('tbl_lab_tests as t',function($q){
					   $q->on('t.id','r.test_id');
					   $q->where('t.is_group','<>','Y');
				   })
				   ->join('tbl_visits_orders as o','o.id','r.order_id')
				   ->leftjoin('tbl_lab_tests as g',function($q){
				               $q->on('g.id','r.group_num');
							   $q->where('g.is_group','Y');
				          })
				  ->leftjoin('tbl_visits_order_results as prev','prev.id','r.prev_result_num')
				  ->join('tbl_lab_categories as cat','cat.id','t.category_num')
				  ->where('r.active','Y')
                  ->where('r.order_id',$order->id)
				  ->orderBy('cat_num')
				  ->orderBy('group_name')
		          ->orderByRaw('g.testord IS NULL,g.testord')
				  ->orderByRaw('t.testord IS NULL,t.testord')
				  
				  ->get();
		  
		  $results = $results->groupBy('group_name');
		  
		  $result_test_ids = LabResults::where('order_id',$order->id)->where('active','Y')->pluck('test_id')->toArray();
		  //validate tests is false
		  $valid_tests_cnt =0;
		  $exist_guarantor = ExtLab::select('id','is_valid')->where('id',$order->ext_lab)->first();
		  if(isset($exist_guarantor)){
			  //guarantor exists then validate tests is true
			  if($exist_guarantor->is_valid=='Y'){$valid_tests_cnt = 1;} 
		  }else{
			$exist_result = LabResults::where('need_validation','Y')->where('order_id',$order->id)->where('active','Y')->exists();  
		    $exist_cult = DB::table('tbl_order_culture_results')->where('need_validation','Y')->where('active','Y')->where('order_id',$order->id)->exists();
		    $exist_temp = DB::table('tbl_visits_order_template_reports')->where('need_validation','Y')->where('active','Y')->where('order_id',$order->id)->exists();
		    if($exist_result || $exist_cult || $exist_temp){
			 //at least one result needs validation or one culture needs validation or one template needs validation	
			 //validate tests is true	
				$valid_tests_cnt =1;
			}
		  }
		  
		 }
          
		  //billing data 
		 if(isset($order) && isset($ReqPatient)){ 
		   $ReqPay=DB::table('tbl_bill_payment')
		          ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_bill_payment.reference')
                  ->where('tbl_bill_payment_mode.status', 'O')
                  ->where('tbl_bill_payment.status','Y')
			      ->where('bill_num',$ReqPatient->id)
			      ->where('payment_type','P')
                  ->get(['tbl_bill_payment.*', 'tbl_bill_payment_mode.name_fr']);
		 
		   $cptpCount= $ReqPay->count();
	    
		   $ReqRef=DB::table('tbl_bill_payment')
		          ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_bill_payment.reference')
                  ->where('tbl_bill_payment_mode.status', 'O')
                  ->where('tbl_bill_payment.status','Y')
			      ->where('bill_num',$ReqPatient->id)
			      ->where('payment_type','R')
                  ->get(['tbl_bill_payment.*', 'tbl_bill_payment_mode.name_fr']);
			 $cptrCount = $ReqRef->count();
	 	  
		  $ReqDis=DB::table('tbl_bill_payment')
		          ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_bill_payment.reference')
                  ->where('tbl_bill_payment_mode.status', 'O')
                  ->where('tbl_bill_payment.status','Y')
			      ->where('bill_num',$ReqPatient->id)
			      ->where('payment_type','D')
                  ->get(['tbl_bill_payment.*', 'tbl_bill_payment_mode.name_fr']);
			 $cptdCount = $ReqDis->count();
			
			$methodepay = DB::table('tbl_bill_payment_mode')->select('id','name_eng as name')->where('clinic_num',$ReqPatient->clinic_num)->where('status','O')->orderBy('id','desc')->get();
			 $pay = DB::table('tbl_bill_payment')->where('bill_num', '=', $ReqPatient->id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('lpay_amount');
			 $refund = DB::table('tbl_bill_payment')->where('bill_num', '=', $ReqPatient->id)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('lpay_amount');
			 $balance=$ReqPatient->bill_balance;
			 
			 $balanced=$ReqPatient->bill_balance_us;
			 $payd = DB::table('tbl_bill_payment')->where('bill_num', '=', $ReqPatient->id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('dpay_amount');
			 $refundd = DB::table('tbl_bill_payment')->where('bill_num', '=', $ReqPatient->id)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('dpay_amount');
			 
			 
			 $stotal=$ReqPatient->bill_total;
			 $totalf=$ReqPatient->lbill_total;
			 $etotal=$ReqPatient->ebill_total;
			 $totalf=number_format((float)$totalf, 2, '.', ',');
			 $stotal=number_format((float)$stotal, 2, '.', ',');
			 $etotal=number_format((float)$etotal, 2, '.', ',');			 
			 $balance=number_format((float)$balance, 2, '.', ',');
             $balanced=number_format((float)$balanced, 2, '.', '');			 
			 $rates = TblBillRate::where('status','O')->get();
			 $currencys=TblBillCurrency::where('active','O')->get();
		 } 
		
		
		//dd($valid_tests_cnt);
		
		return view('lab.visit.Visits')->with(['patient'=>$patient,'clinic'=>$clinic,
											   'ext_labs'=>$ext_labs,'tests'=>$tests,
											   'order'=>$order,'order_tests'=>$order_tests,
											   'groups'=>$groups,'documents'=>$documents,
											   'lbl_usd'=>$lbl_usd,'lbl_euro'=>$lbl_euro,
											   'currencys'=>$currencys,'ReqPay'=>$ReqPay,'ReqRef'=>$ReqRef,
											   'totalf'=>$totalf,'stotal'=>$stotal,'etotal'=>$etotal,
											   'cptrCount'=>$cptrCount,'cptpCount'=>$cptpCount,'cptdCount'=>$cptdCount,
											   'ReqPatient'=>$ReqPatient,'methodepay'=>$methodepay,
											   'balance'=>$balance,'pay'=>$pay,'refund'=>$refund,'rates'=>$rates,
											   'balanced'=>$balanced,'payd'=>$payd,'refundd'=>$refundd,
											   'categories'=>$categories,'profiles'=>$profiles,
											   'profile_ids'=>$profile_ids,'culture_test'=>$culture_test,
											   'sbacteria'=>$sbacteria,'test_textResults'=>$test_textResults,
											   'results'=>$results,'culture_test_det'=>$culture_test_det,
											   'doctors'=>$doctors,'patient_data'=>$patient_data,
											   'result_textResults'=>$result_textResults,'gram_staim_results'=>$gram_staim_results,
											   'patient_age'=>$ageInyears,'patient_gender'=>$patient_gender,'age_data'=>$age_data,
											   'view_bills'=>$view_bills,'view_culture'=>$view_culture,'edit_culture'=>$edit_culture,'view_attachments'=>$view_attachments,
											   'view_results'=>$view_results,'edit_results'=>$edit_results,'validate_results'=>$validate_results,
											   'view_templates'=>$view_templates,'edit_templates'=>$edit_templates,'template_reports'=>$template_reports,
											   'valid_tests_cnt'=>$valid_tests_cnt,'sms_pack'=>$sms_pack
											   ]);
		
    }
	
public function getResult($lang,Request $request){
	$order_id = $request->filter_order;
	$results = collect();
	if($order_id != '0'){
	$order = LabOrders::find($order_id);
		
	$results = DB::table('tbl_visits_order_results as r')
	               ->select('r.id',
				            DB::raw("IF(t.test_code IS NOT NULL and t.test_code <>'',CONCAT(t.test_name,' ','(',' ',t.test_code,' ',')'),t.test_name) as test_name"),
				            DB::raw("IFNULL(g.test_name,'No Group') as group_name"),
							DB::raw("IFNULL(t.unit,'') as unit"),
							't.test_type','r.test_id','t.dec_pts',
							DB::raw("IFNULL(t.normal_value,'') as range_val"),
							DB::raw("IFNULL(r.result,'') as result_value"),
							DB::raw("IF(o.status='F','Finished',IF(o.status='P','Pending','Validated')) as status"),
							't.testord as position','r.result_status',
							DB::raw("IFNULL(prev.result,'') as prev_result"),
							DB::raw("IFNULL(r.field_num,'') as field_num"),
							DB::raw("IFNULL(r.sign,'') as sign"),
							'r.ref_range','r.calc_result','r.calc_unit','t.is_printed'
							)
				   ->join('tbl_lab_tests as t',function($q){
					   $q->on('t.id','r.test_id');
					   $q->where('t.is_group','<>','Y');
				   })
				   ->join('tbl_visits_orders as o','o.id','r.order_id')
				   ->leftjoin('tbl_lab_tests as g',function($q){
				               $q->on('g.id','t.group_num');
							   $q->where('g.is_group','Y');
							   $q->where('g.active','Y');
				          })
				  ->leftjoin('tbl_visits_order_results as prev','prev.id','r.prev_result_num')
				  ->where('r.active','Y')
                  ->where('r.order_id',$order_id)
	              ->orderBy('group_name')
				  ->orderBy('t.testord')
				  ->get();
	}
	return Datatables::of($results)
                   ->addIndexColumn()
                   ->addColumn('result_data', function($row){
                        $state = $row->result_status; 
			            $dec_pts = $row->dec_pts;
			            $result = $row->result_value;
						
					    switch($state){
						      case 'H':  $class = "bgred"; break;
							  case 'L':  $class = "bgblue"; break;
							  case 'PL': case 'PH':  $class = "bgorange"; break;
							  default:   $class = "bginfo"; 
					      }
				
				    if(isset($dec_pts) && $dec_pts !='' &&  is_numeric($dec_pts) && isset($result) && $result!='' &&  is_numeric($result)){
					    $result = number_format($result, $dec_pts, '.', '');
					  }
				     
					 $btn = '<input type="text" data-id="'.$row->id.'" data-decpts="'.$row->dec_pts.'" data-fieldnum="'.$row->field_num.'" data-testid="'.$row->test_id.'" data-type="'.$row->test_type.'" class="result_data form-control '.$class.'" value="'.$result.'" style="font-size:18px;"/>';
					 return $btn;
				    })
				   ->rawColumns(['result_data'])
				   ->make(true);
	
	//return response()->json($results);
}

public function getBill($lang,Request $request){
  $order_id = $request->filter_order;
  $bill_head = TblBillHead::where('order_id',$order_id)->where('status','O')->first();
  
  $bill = collect();
  if(isset($bill_head)){
	 $bill = DB::table('tbl_bill_head as h')
	         ->select(DB::raw('IFNULL(s.bill_name,"No Name") as bill_name'),'s.cnss',DB::raw("DATE(h.bill_datein) as bill_datein"),
			          's.bill_price','s.bill_quantity','s.lbill_price','s.ebill_price',
					  'h.clinic_bill_num','s.bill_num','s.bill_code',
					  DB::raw("s.bill_price/s.bill_quantity as test_cost")
					  )
	         ->join('tbl_bill_specifics as s',function($q){
				 $q->on('s.bill_num','h.id');
				 $q->where('s.status','O');
				 })
			 ->where('h.id',$bill_head->id)
			 ->distinct()
			 ->get();	  
	}
	//dd($bill);
 return response()->json($bill);	
}

public function checkResultVal($lang,Request $request){
	
	$state = 'U';
	$sign = '';
	$calc_result='';
	$user_num = auth()->user()->id;
	$type = $request->type;
	
   switch($type){
		//The result is text only update result text
		case 'T':
		 //new value then update in db
		 $id = $request->id;
	     $val = $request->result;
	     $res = LabResults::find($id); 
		 
		 if(trim($res->result_txt) != trim($val)){ 
		  LabResults::where('id',$id)->update([
				  'result'=>NULL,
				  'result_txt'=>$val,
		          'result_status'=>'U',
				  'sign'=>NULL,
				  'user_num'=>$user_num
				  ]);
		    }  
		break;
		//The result is number update sign , state and result
		case 'N':
		  $id = $request->id;
	      $val = $request->result;
		  $res = LabResults::find($id); 
	      if($val!=''){
		        $field_num = isset($res->field_num) && $res->field_num!=''?explode(',',$res->field_num):NULL;
				if(isset($field_num)){
				  $test_states = $this->getTestState($id,$val,$field_num);
				  if(count($test_states)){
					 $state = $test_states["state"];
					 $sign = $test_states["sign"];
					 }
				   }
		       }
			   
			  if($res->result == $val && $res->result_status==$state && $res->sign==$sign){
				 //do nothing no need to update
              }else{				 
			   LabResults::where('id',$id)->update([
					  'result_txt'=>NULL,
					  'result'=>$val,
					  'result_status'=>$state,
					  'sign'=>$sign!=''?$sign:NULL,
					  'user_num'=>$user_num
					  ]);
			  }				  
		break;
	    //The result is number with formula update sign and state for Both
		case 'F':
		  if(isset($request->formula_id)){
		      $form_res = LabResults::find($request->formula_id);
			   if($request->formula_result!=''){
			          $field_num = isset($form_res->field_num) && $form_res->field_num!=''?explode(',',$form_res->field_num):NULL;
				  	  if(isset($field_num)){
					  $test_states = $this->getTestState($request->formula_id,$request->formula_result,$field_num);
					  if(count($test_states)){
						$state = $test_states["state"];
						$sign = $test_states["sign"];
					   }
					  }
			      }
				 if($request->formula_type=='F'){
						 if($form_res->result == $request->formula_result && $form_res->result_status==$state && $form_res->sign==$sign){
				           //do nothing no need to update
                         }else{ 
						  LabResults::where('id',$request->formula_id)->update([
								  'result_txt'=>NULL,
								  'result'=>$request->formula_result,
								  'result_status'=>$state,
								  'sign'=>$sign!=''?$sign:NULL,
								  'user_num'=>$user_num
								  ]);
						 }
					}else{
						//calculate not formula
						if($request->formula_type=='C'){
							LabResults::where('id',$request->formula_id)->update([
								  'calc_result'=>$request->formula_result,
								  'calc_unit'=> $form_res->unit,
								  'result_status'=>$state,
								  'sign'=>$sign!=''?$sign:NULL,
								  'user_num'=>$user_num
								  ]);
						  
						}
					 }
			    
	         }			
	
	   break;
	
	}
	
  return response()->json(['state'=>$state,'sign'=>$sign]);
}

function getFieldRefRange($result_id){
	$result = LabResults::find($result_id);
	$test_id = $result->test_id;
	//if test id is print all normal values then get all fields
	//$test = LabTests::find($test_id);
	
	$order  = LabOrders::find($result->order_id);
	$patient = Patient::find($order->patient_num);
	$result_value = $result->result;
	$gender = $patient->sex;
	//translate always age in days
	$ageInDays=Carbon::parse($patient->birthdate)->diffInDays(Carbon::now());
	
	
	$field = DB::table('tbl_lab_tests_fields as f')
					 ->select('f.id')
					 ->where('f.active','Y')
					 ->whereNOTNULL('f.fage')
					 ->where(DB::raw('IF(f.mytype="Y",f.fage*365.25,IF(f.mytype="M",f.fage*30.44,IF(f.mytype="W",f.fage*7,f.fage)))'),'<=',$ageInDays)
					 ->where('f.fage','<>','')
					 ->where('f.fage','<>','0')
					 ->whereNOTNULL('f.tage')
					 ->where('f.tage','<>','')
					 ->where('f.tage','<>','0')
					 ->where(DB::raw('IF(f.mytype="Y",f.tage*365.25,IF(f.mytype="M",f.tage*30.44,IF(f.mytype="W",f.tage*7,f.tage)))'),'>',$ageInDays)
					 ->where('f.gender',$gender)
	  			     ->where('f.test_id',$test_id)
				     ->pluck('f.id')->toArray();

    //dd($ageInDays.'-'.implode(',',$field));					 
					 
	if(count($field)==0){
	  //age are both filled and gender is both
	  $field = DB::table('tbl_lab_tests_fields as f')
				     ->select('f.id')
					 ->where('f.active','Y')
					 ->whereNOTNULL('f.fage')
					 ->where('f.fage','<>','')
					 ->where('f.fage','<>','0')
					 ->where(DB::raw('IF(f.mytype="Y",f.fage*365.25,IF(f.mytype="M",f.fage*30.44,IF(f.mytype="W",f.fage*7,f.fage)))'),'<=',$ageInDays)
					 ->whereNOTNULL('f.tage')
					 ->where('f.tage','<>','')
					 ->where('f.tage','<>','0')
					 ->where(DB::raw('IF(f.mytype="Y",f.tage*365.25,IF(f.mytype="M",f.tage*30.44,IF(f.mytype="W",f.tage*7,f.tage)))'),'>',$ageInDays)
					 ->where('f.test_id',$test_id)
					 ->where(function($q){
						 $q->where('f.gender','=','B')
						   ->orWhereNULL('f.gender');
					     })
				     ->pluck('f.id')->toArray(); 
	     
	    if(count($field)==0){
			//min age is only filled and gender is male or female
			$field = DB::table('tbl_lab_tests_fields as f')
				      ->select('f.id')
					 ->where('f.active','Y')
					 ->whereNOTNULL('f.fage')
					 ->where('f.fage','<>','')
					 ->where('f.fage','<>','0')
					 ->where(DB::raw('IF(f.mytype="Y",f.fage*365.25,IF(f.mytype="M",f.fage*30.44,IF(f.mytype="W",f.fage*7,f.fage)))'),'<=',$ageInDays)
					 ->where('f.gender',$gender)
	  			     ->where('f.test_id',$test_id)
					 ->where(function($q){
						 $q->where('f.tage','=','')
						   ->orWhere('f.tage','=','0')
						   ->orWhereNULL('f.tage');
					 })
				     ->pluck('f.id')->toArray(); 
					 
			if(count($field)==0){
			   //min age is only filled and gender is Both
			   $field = DB::table('tbl_lab_tests_fields as f')
				     ->select('f.id')
					 ->where('f.active','Y')
					 ->whereNOTNULL('f.fage')
					 ->where('f.fage','<>','')
					 ->where('f.fage','<>','0')
					 ->where(DB::raw('IF(f.mytype="Y",f.fage*365.25,IF(f.mytype="M",f.fage*30.44,IF(f.mytype="W",f.fage*7,f.fage)))'),'<=',$ageInDays)
					 ->where('f.test_id',$test_id)
					 ->where(function($q){
						 $q->where('f.tage','=','')
						   ->orWhere('f.tage','=','0')
						   ->orWhereNULL('f.tage');
					        })
					->where(function($q){
						 $q->where('f.gender','=','B')
						   ->orWhereNULL('f.gender');
					     })		
				     ->pluck('f.id')->toArray(); 
			
			if(count($field)==0){
			         //max age is only filled and gender is Male or Female
			   $field = DB::table('tbl_lab_tests_fields as f')
				     ->select('f.id')
					 ->where('f.active','Y')
					 ->whereNOTNULL('f.tage')
					 ->where('f.tage','<>','')
					 ->where('f.tage','<>','0')
					 ->where(DB::raw('IF(f.mytype="Y",f.tage*365.25,IF(f.mytype="M",f.tage*30.44,IF(f.mytype="W",f.tage*7,f.tage)))'),'>',$ageInDays)
					 ->where('f.gender',$gender)
	  			     ->where('f.test_id',$test_id)
					 ->where(function($q){
						 $q->where('f.fage','=','')
						   ->orWhere('f.fage','<>','0')
						   ->orWhereNULL('f.fage');
					        })
				     ->pluck('f.id')->toArray(); 
			
			if(count($field)==0){
			         //max age is only filled and gender is Both
			   $field = DB::table('tbl_lab_tests_fields as f')
				      ->select('f.id')
					  ->where('f.active','Y')
					  ->whereNOTNULL('f.tage')
					  ->where('f.tage','<>','')
					  ->where('f.tage','<>','0')
					  ->where(DB::raw('IF(f.mytype="Y",f.tage*365.25,IF(f.mytype="M",f.tage*30.44,IF(f.mytype="W",f.tage*7,f.tage)))'),'>',$ageInDays)
	  			      ->where('f.test_id',$test_id)
					  ->where(function($q){
						 $q->where('f.fage','=','')
						   ->orWhere('f.fage','=','0')
						   ->orWhereNULL('f.fage');
					        })
					  ->where(function($q){
						 $q->where('f.gender','=','B')
						   ->orWhereNULL('f.gender');
					     })		
				      ->pluck('f.id')->toArray(); 
			
			if(count($field)==0){
			//no age is  filled and gender is male or female
			$field = DB::table('tbl_lab_tests_fields as f')
				     ->select('f.id')
					 ->whereRaw('f.active="Y" and f.gender=? and f.test_id=?
					             and (f.fage="0" or f.fage="" or f.fage IS NULL) and (f.tage="0" or f.tage="" or f.tage IS NULL)',[$gender,$test_id])
					 ->pluck('f.id')->toArray();  
		    
			if(count($field)==0){
				//no age is only filled and gender is both
				$field = DB::table('tbl_lab_tests_fields as f')
				     ->select('f.id')
					 ->whereRaw('f.active="Y" and f.gender="B" and f.test_id=?
					            and (f.fage="0" or f.fage="" or f.fage IS NULL) and (f.tage="0" or f.tage="" or f.tage IS NULL)',$test_id)
				    ->pluck('f.id')->toArray(); 
					 
			if(count($field)==0){
				//no age and no gender
				$field = DB::table('tbl_lab_tests_fields as f')
				     ->select('f.id')
					 ->whereRaw('f.active="Y" and  f.gender IS NULL and f.test_id=?
					            and (f.fage="0" or f.fage="" or f.fage IS NULL) and (f.tage="0" or f.tage="" or f.tage IS NULL)',$test_id)
				     ->pluck('f.id')->toArray();
					 
			 
			  
			  }		
			
			
			}
		  	  
		  }
		 }
		}
		}
	   }
	 }
	
	
	
return $field;
}

function getTestState($result_id,$result_val,$field_num){
	
	$result = LabResults::find($result_id);
	$test = LabTests::find($result->test_id);
	
	$field = NULL;
	$state= 'U';
	$sign='';
	$min=$max='';
	
	if(count($field_num)==1){
		
		$field = DB::table('tbl_lab_tests_fields as f')->select('f.*')->find($field_num[0]);
	   
		$min = isset($field->normal_value1)?$field->normal_value1:'';
		$max = isset($field->normal_value2)?$field->normal_value2:'';
	   //min is not empty and max is not empty
	   if($max!='' && $min!=''){
			 if($result_val>=floatVal($min) && $result_val<=floatVal($max)){
				 $state ='N';
				 $sign = isset($field->sign) && $field->sign!=''?$field->sign:'';
			 }else{
				 if($result_val>floatVal($max)){
					 $state='H';
					 $sign = isset($field->sign_max) && $field->sign_max!=''?$field->sign_max:__('High');
					 $high_panic = $field->panic_high_value;
					 if($high_panic !=NULL && $high_panic !='' && $result_val > floatVal($high_panic)){
					 $state='PH';
					 $sign = __('High High');
				      }
				 }else{
					if($result_val<floatVal($min)){
					  $low_panic = $field->panic_low_value;
					  $state='L';
					  $sign = isset($field->sign_min) && $field->sign_min!=''?$field->sign_min:__('Low');
					  if($low_panic !=NULL && $low_panic !='' && $result_val <floatVal($low_panic)){
						 $state='PL';
						 $sign = __('Low Low');
					  }
					  
					} 
				 }
			 }  
		  		  
		  }else{  
			 //min is not empty and max is empty
			 if($min!='' && $max==''){
				 if($result_val>=floatVal($min)){
					 $state='N';
					 $sign = isset($field->sign)&& $field->sign!=''?$field->sign:'';
				 }else{
					 $low_panic = $field->panic_low_value;
					 $state='L';
					 $sign = isset($field->sign_min)&& $field->sign_min!=''?$field->sign_min:__('Low');
					 if($low_panic !=NULL && $low_panic !='' && $result_val <floatVal($low_panic)){
						 $state='PL';
						 $sign = __('Low Low');
					 }
				 }
			 }else{
				  //min is empty and max is not empty
				 if($max!='' && $min==''){
					 if($result_val<=floatVal($max)){
						 $state='N';
						 $sign = isset($field->sign) && $field->sign!=''?$field->sign:'';
					 }else{
						 $high_panic = $field->panic_high_value;
						 $state='H';
						 $sign = isset($field->sign_max) && $field->sign_max!=''?$field->sign_max:__('High');
						 if($high_panic !=NULL && $high_panic !='' && $result_val > floatVal($high_panic)){
							 $state='PH';
							 $sign = __('High High');
						 }
					  }
				 }
				 
			 }
		  }
	 
	
	}else{
		$test = LabTests::find($result->test_id);
		$gender = Patient::where('id',$result->patient_num)->value('sex');
		
		$fields = DB::table('tbl_lab_tests_fields')->whereIn('id',$field_num)
		          ->orderBy('field_order')->get();  
         	 
		
		 //try to get field number that fits in result value
		 foreach($fields as $f){
			 $min = isset($f->normal_value1) && $f->normal_value1!=''?$f->normal_value1:'';
			 $max = isset($f->normal_value2) && $f->normal_value2!=''?$f->normal_value2:'';
			 $descrip = $f->descrip;
			 
			 if($min!='' && $max!='' && $result_val>=$min && $result_val<=$max){
				 $state='N';
				 $sign = isset($f->sign) && $f->sign!=''?$f->sign:'';
				 break;
			 }else{
				 if($min=='' && $max!='' && $result_val>=$max && strpos($descrip,'≥')!==false){
					   $state='H';
				       $sign = isset($f->sign_max) && $f->sign_max!=''?$f->sign_max:'High'; 
				 }else{
					 if($min=='' && $max!='' && $result_val>$max && strpos($descrip,'>')!==false){
						$state='H';
				        $sign = isset($f->sign_max) && $f->sign_max!=''?$f->sign_max:'High'; 
					 }else{
						if($min!='' && $max=='' && $result_val<$min && strpos($descrip,'<')!==false){
							 $state='L';
				             $sign = isset($f->sign_min) && $f->sign_min!=''?$f->sign_min:'Low';
						}else{
							if($min!='' && $max=='' && $result_val<=$min && strpos($descrip,'≤')!==false){
								 $state='L';
				                 $sign = isset($f->sign_min) && $f->sign_min!=''?$f->sign_min:'Low';
							}
						} 
					 }
				 }
			 }	 
	      }
	}
	  
	 
	
	
return array('state'=>$state,'sign'=>$sign);
}


function evaluateCalcFormula($formula_test_id,$all_tests){
		$formula_result = '';
		$calc_unit = '';
		$f = DB::table('tbl_lab_tests_formulas')->where('test_id',$formula_test_id)->where('active','Y')->first();
		
		//dd($f);
		if(isset($f)){
			$calc_unit = $f->unit;
			$code1 = isset($f->test1) && $f->test1!=''?$f->test1:'';
			$code2 = isset($f->test2) && $f->test2!=''?$f->test2:'';
			$code3 = isset($f->test3) && $f->test3!=''?$f->test3:'';
			$code4 = isset($f->test4) && $f->test4!=''?$f->test4:'';
			$factor1 = isset($f->factor1) && $f->factor1!=''?$f->factor1:'';
			$factor2 = isset($f->factor2) && $f->factor2!=''?$f->factor2:'';
			$factor3 = isset($f->factor3) && $f->factor3!=''?$f->factor3:'';
			$factor4 = isset($f->factor4) && $f->factor4!=''?$f->factor4:'';
			
			$formula = $f->formula;
			$formula = str_replace('factor1',$factor1,$formula);
			$formula = str_replace('factor2',$factor2,$formula);
			$formula = str_replace('factor3',$factor3,$formula);
			$formula = str_replace('factor4',$factor4,$formula);
			
			foreach($all_tests as $arr){
				if($code1!='' && $arr["test_id"]==$code1 && $arr["result"]!=''){
					$formula = str_replace('code1',$arr["result"],$formula);
				}
				if($code2!='' && $arr["test_id"]==$code2 && $arr["result"]!=''){
					$formula = str_replace('code2',$arr["result"],$formula);
				}
				if($code3!='' && $arr["test_id"]==$code3 && $arr["result"]!=''){
					$formula = str_replace('code3',$arr["result"],$formula);
				}
				if($code4!='' && $arr["test_id"]==$code4 && $arr["result"]!=''){
					$formula = str_replace('code3',$arr["result"],$formula);
				}
			  }
			
		    if ($this->isValidFormula($formula)) {
                 eval( '$formula_result = (' . $formula. ');' );
               }
		   
		   }
         //dd($formula_result);
		 if(is_numeric($formula_result)){
			 $formula_result = number_format((float)$formula_result,2,'.','');
			
		 }
return array('calc_result'=>$formula_result,'calc_unit'=>$calc_unit);
}	

function evaluateFormula($formula_test_id,$all_tests){
		$formula_result = '';
		$f = DB::table('tbl_lab_tests_formulas')->where('test_id',$formula_test_id)->where('active','Y')->first();
		
		//dd($f);
		if(isset($f)){
			$code1 = isset($f->test1) && $f->test1!=''?$f->test1:'';
			$code2 = isset($f->test2) && $f->test2!=''?$f->test2:'';
			$code3 = isset($f->test3) && $f->test3!=''?$f->test3:'';
			$code4 = isset($f->test4) && $f->test4!=''?$f->test4:'';
			$factor1 = isset($f->factor1) && $f->factor1!=''?$f->factor1:'';
			$factor2 = isset($f->factor2) && $f->factor2!=''?$f->factor2:'';
			$factor3 = isset($f->factor3) && $f->factor3!=''?$f->factor3:'';
			$factor4 = isset($f->factor4) && $f->factor4!=''?$f->factor4:'';
			
			$formula = $f->formula;
			$formula = str_replace('factor1',$factor1,$formula);
			$formula = str_replace('factor2',$factor2,$formula);
			$formula = str_replace('factor3',$factor3,$formula);
			$formula = str_replace('factor4',$factor4,$formula);
			
			foreach($all_tests as $arr){
				if($code1!='' && $arr["test_id"]==$code1 && $arr["result"]!=''){
					$formula = str_replace('code1',$arr["result"],$formula);
				}
				if($code2!='' && $arr["test_id"]==$code2 && $arr["result"]!=''){
					$formula = str_replace('code2',$arr["result"],$formula);
				}
				if($code3!='' && $arr["test_id"]==$code3 && $arr["result"]!=''){
					$formula = str_replace('code3',$arr["result"],$formula);
				}
				if($code4!='' && $arr["test_id"]==$code4 && $arr["result"]!=''){
					$formula = str_replace('code3',$arr["result"],$formula);
				}
			  }
			
		    if ($this->isValidFormula($formula)) {
                 eval( '$formula_result = (' . $formula. ');' );
               }
		   
		   }
         //dd($formula_result);
		 if(is_numeric($formula_result)){
			 $formula_result = number_format((float)$formula_result,2,'.','');
		 }
return $formula_result;
}	

function isValidFormula($formula) {
    // Define a regular expression pattern for a mathematical formula with decimals and parentheses
    $pat = '/^[\d\(\)\+\-\*\/\.]+$/';
    // Use preg_match to check if the formula matches the pattern
    return preg_match($pat, $formula);
}

public function UpdateLab($lang, Request $request)
    {
     $type = $request->type;
	 $user_num  = auth()->user()->id;
	 switch($type){
		 case 'doctor':
		  if($request->order_id=='0'){
		     Session::forget('order_doctor_num');
             Session::put('order_doctor_num',$request->doctor_num); 		
	       }else{ 
	         $order_id = $request->order_id;
			 $existing_doctor_num = LabOrders::where('id',$order_id)->value('doctor_num');
			 if(intval($existing_doctor_num) != intval($request->doctor_num)){
			   LabOrders::where('id',$request->order_id)->update(['doctor_num'=>$request->doctor_num,'user_num'=>$user_num]);
	           TblBillHead::where('order_id',$request->order_id)->where('status','O')->update(['doctor_num'=>$request->doctor_num,'user_num'=>$user_num]);
			  }
			}
		  return response()->json(['msg'=>__('Doctor changed successfully')]);
		 break;
		 case 'guarantor':
		  
		  if($request->order_id=='0'){
		     Session::forget('order_ext_lab');
             Session::put('order_ext_lab',$request->ext_lab); 
             return response()->json(['msg'=>__('Guarantor changed successfully')]);			 
	       }else{ 
	         $bill_id = $request->bill_id;
			 $order_id = $request->order_id;
			 $existing_ext_lab = LabOrders::where('id',$order_id)->value('ext_lab');
			 
			 if(intval($existing_ext_lab) != intval($request->ext_lab)){
			   LabOrders::where('id',$request->order_id)->update(['ext_lab'=>$request->ext_lab,'user_num'=>$user_num]);
	           
			  
			   if(isset($bill_id) && $bill_id!='0'){
			     TblBillHead::where('id', $bill_id)->update(['ext_lab'=>$request->ext_lab,'user_num'=>$user_num]);
			      $bill = TblBillHead::find($bill_id);
				 //get according to user in order to get prices
	             $doc_lab = Clinic::where('id',auth()->user()->clinic_num)->first();
	             $tbl_name = "tbl_clinics_prices";
	 	         if(isset($bill->ext_lab) && $bill->ext_lab!=''){
		            $doc_lab = ExtLab::where('id',$bill->ext_lab)->first();
	                $tbl_name = "tbl_external_labs_prices";
	               }
			   //recalculate totals for new ext_lab
			   $details = TblBillSpecifics::where('bill_num',$bill->id)->where('status','O')->get();
			   $tbillpriced=$tbillpricee=$tbillpricel=0;
			   foreach($details as $d){
				      $test_id = $d->bill_code;
					  $priced = $pricel = $pricee =0;
					  $pr = DB::table($tbl_name)->where('lab_id',$doc_lab->id)->where('test_id',$test_id)->first(); 
					   if(isset($pr)){
							if(isset($pr->totald) && $pr->totald!='' && $pr->totald!=0){
														$priced = $pr->totald;
												}
							 if(isset($pr->totall) && $pr->totall!='' && $pr->totall!=0){
														$pricel = $pr->totall; 
												}
							 if(isset($pr->totale) && $pr->totale!='' && $pr->totale!=0){
														$pricee = $pr->totale; 
													} 
										 }
					   $priced = number_format((float)$priced,2,'.','');
					   $pricel = number_format((float)$pricel,2,'.','');
					   $pricee = number_format((float)$pricee,2,'.','');
					  
					  TblBillSpecifics::where('id',$d->id)->update([
						'ext_lab'=>$bill->ext_lab,
						'user_num'=>$user_num,
						'user_type'=>auth()->user()->type,
						'lbill_price'=> $pricel,
						'bill_price'=> $priced,
						'ebill_price'=> $pricee,
						
						]);
					  
					  $tbillpricel=$tbillpricel+$pricel;
					  $tbillpriced=$tbillpriced+$priced;
					  $tbillpricee=$tbillpricee+$pricee;
			    }
			  //recalculate balance sumpay,sumref,discount and update them
	  	       //update totals
               $sumpay=DB::table('tbl_bill_payment')->where('bill_num','=',$bill->id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
               $sumref=DB::table('tbl_bill_payment')->where('bill_num','=',$bill->id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');
      	       $tdiscount = 0;
	           if(isset($bill->bill_discount) && $bill->bill_discount!=''){
		             $tdiscount = $bill->bill_discount;  
		               }
	           $tbalance = $tbillpricel-$tdiscount-$sumpay+$sumref+$bill->tvq+$bill->tps;
               $Nbalance=number_format((float)$tbalance, 2, '.', ',');
               $balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));  
               
			   $sumpayd=DB::table('tbl_bill_payment')->where('bill_num','=',$bill->id)->where('status','Y')->where('payment_type','=','P')->sum('dpay_amount');
               $sumrefd=DB::table('tbl_bill_payment')->where('bill_num','=',$bill->id)->where('status','Y')->where('payment_type','=','R')->sum('dpay_amount');
      	       $tdiscountd = 0;
	           if(isset($bill->bill_discount_us) && $bill->bill_discount_us!=''){
		             $tdiscountd = $bill->bill_discount_us;  
		               }
	           $tbalanced = $tbillpriced-$tdiscountd-$sumpayd+$sumrefd+$bill->tvq+$bill->tps;
               $Nbalanced=number_format((float)$tbalanced, 2, '.', ',');
               $balanced=floatval(preg_replace('/[^\d.-]/', '', $Nbalanced));  
             
			 
			 
			 TblBillHead::where('id',$bill->id)
			 ->update(['bill_discount'=>$tdiscount,'bill_balance'=>$balance,'bill_discount_us'=>$tdiscountd,
			           'bill_balance_us'=>$balanced,'lbill_total'=>$tbillpricel,'bill_total'=>$tbillpriced,
					   'ebill_total'=>$tbillpricee]);		
	  		 
			//data to return to user
		     $sumpayd =  number_format((float)$sumpayd,2,'.','');
		     $sumpayl =  number_format((float)$sumpay,2,'.',',');

		     $sumrefd =  number_format((float)$sumrefd,2,'.','');
		     $sumrefl =  number_format((float)$sumref,2,'.',',');

		     $balanced =  number_format((float)$balanced,2,'.','');
		     $balancel=number_format((float)$balance, 2, '.', ',');

		     $tdiscountd =  number_format((float)$tdiscountd,2,'.','');
		     $tdiscountl =  number_format((float)$tdiscount,2,'.',',');
	  
	        return response()->json(['change_bill'=>true,'msg'=>__('Guarantor changed successfully'),'sumpay'=>$sumpayl,'sumref'=>$sumrefl,'nbalance'=>$balancel,'tdiscount'=>$tdiscountl,
                         'tdiscountd'=>$tdiscountd,'sumpayd'=>$sumpayd,'sumrefd'=>$sumrefd,'balanced'=>$balanced]);	  	
			 }else{
				  return response()->json(['msg'=>__('Guarantor changed successfully')]);
			 }
	        
			 
			 }
			}
		    			
			
		 break;
	 }
	 
}

public function filterGroup($lang,Request $request){
	$type = $request->type;
	switch($type){
	 case 'cat':
	    $category_num = $request->category_num; 
	    $search = $request->search;
		
		$groups = DB::table('tbl_lab_tests');
		$div_grps = DB::table('tbl_lab_tests')->select('id','test_name','testord');
		
		if($category_num !='' && $category_num !='0' ){
		 if($category_num!='-1'){
		  $groups = $groups->where('category_num',$category_num);
		  $div_grps = $div_grps->where('tbl_lab_tests.category_num',$category_num);
		 }else{
		  $groups = $groups->where('custom_test','Y');
		  $div_grps = $div_grps->where('custom_test','Y'); 
		 }
		}
		
		if($search !=''){
		 $groups = $groups->where(DB::raw('trim(lower(test_name))'),'like','%'.$search.'%');
         }
		
				
		$groups = $groups->whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL)) and test_name<>"" and test_name IS NOT NULL')
					 ->orderBy('testord');
		
	    $div_grps = $div_grps->where('active','Y')
		             ->where(function($q){
						 $q->where('is_group','Y');
						 $q->whereNOTNULL('test_name');
						 $q->where('test_name','<>','');
						})
					 ->orderBy('testord')->get();
					 
		$testIds  = $groups->pluck('id')->toArray();
		
		
	    $html = '<option value="">'.__("Choose a code").'</option>';
		foreach($div_grps as $g){
			$name =$g->test_name;
			if(isset($g->test_code) && $g->test_code!=''){
			$name.=' ( '.$g->test_code.')';
			}
			$html.='<option value="'.$g->id.'">'.$name.'</option>';
		}
		
		$tests = LabTests::whereIn('id', $testIds)->orderBy('testord')->pluck('id')->toArray();

		$category_nums = LabTests::whereIn('id',$testIds)->distinct()->pluck('category_num')->toArray();
		
		return response()->json(['html'=>$html,'tests'=>$tests,'category_nums'=>$category_nums]);
     break;
     case 'grp':
	  $group_num = $request->group_num;
	  $category_num = $request->category_num;
	  $search = $request->search;
	  $groups = DB::table('tbl_lab_tests');
	  
	  if($category_num !='' && $category_num !='0'){
		if($category_num !='-1'){
		 $groups = $groups->where('category_num',$category_num);  
	    }else{
		 $groups = $groups->where('custom_test','Y');	
		}
	  }
	  
	  if($group_num !='' && $group_num !='0'){
		$groups = $groups->where('id',$group_num);  
	  }
	  
	  if($search !=''){
		 $groups = $groups->where(DB::raw('trim(lower(test_name))'),'like','%'.$search.'%');	
		}
	  
	  $groups = $groups->whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL)) and test_name<>"" and test_name IS NOT NULL')
				 ->orderBy('testord'); 
				 
	   $testIds  = $groups->pluck('id')->toArray();
	   
	   $tests = LabTests::whereIn('id', $testIds)->orderBy('testord')->pluck('id')->toArray();
	   $category_nums = LabTests::whereIn('id',$testIds)->distinct()->pluck('category_num')->toArray();

		return response()->json(['tests'=>$tests,'category_nums'=>$category_nums]);
     break;
     case 'search':
	   $group_num = $request->group_num;
	   $category_num = $request->category_num;
	   $search = $request->search;
	   
	   $groups = DB::table('tbl_lab_tests');
	  
	   if($category_num !='' && $category_num !='0'){
		 if($category_num !='-1'){
		    $groups = $groups->where('category_num',$category_num);
         }else{
			$groups = $groups->where('custom_test','Y');	 
		 }		  
	   }
	  
	   if($group_num !='' && $group_num !='0'){
		$groups = $groups->where('id',$group_num);  
	   }
	   
	   if($search!=''){
		$groups = $groups->where(DB::raw('trim(lower(test_name))'),'like','%'.$search.'%');    
	   }
	   
	   $groups = $groups->whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL)) and test_name<>"" and test_name IS NOT NULL')
				 ->orderBy('testord');
	 
	   $testIds  = $groups->pluck('id')->toArray();
	   $tests = LabTests::whereIn('id', $testIds)->orderBy('testord')->pluck('id')->toArray();
	   $category_nums = LabTests::whereIn('id',$testIds)->distinct()->pluck('category_num')->toArray();
	 
     return response()->json(['tests'=>$tests,'category_nums'=>$category_nums]);	 
     break;
     case 'datatable':
	 
	  $ref_labs = ExtIns::where('status','Y')->where('clinic_num',auth()->user()->clinic_num)->orderBy('id','desc')->get();
      
	  $order_status = DB::table('tbl_visits_orders')->where('id',$request->order_id)->value('status');
	   
	  $user_type = auth()->user()->type;
	  
	  $data = DB::table('tbl_visits_order_custom_tests as cust')
		        ->select('cust.id','cust.test_id','cust.referred_lab',
				         'cust.insert_date','cust.user_num','t.test_name','cat.descrip as category_name',
						 'cat.testord','t.testord')
				->join('tbl_lab_tests as t','t.id','cust.test_id')
				->join('tbl_lab_categories as cat','cat.id','t.category_num')
				->where('cust.order_id',$request->order_id)
				->where('cust.active','Y')
				->orderBy('cat.testord')
				->orderBy('t.testord')
				->distinct()->get();
		
	  return Datatables::of($data)
                   ->addIndexColumn()

                    ->addColumn('insert_date',function($row){
						$date = Carbon::parse($row->insert_date)->format('d/m/Y H:i');
						return $date;
					})
					->addColumn('referred_test', function($row) use($ref_labs,$order_status,$user_type){
					 
						 $disabled = ($order_status=='V' || $user_type!=2)?'disabled':'';
						 
						 $btn = '<select class="referred-test-select form-control" data-id="'.$row->id.'" '.$disabled.' name="referred_tests">';
						 $btn.='<option value="">Choose a referred lab</option>';
						 foreach($ref_labs as $i){
						  $selected='';
						  if(isset($row->referred_lab) && $row->referred_lab==$i->id){
							$selected = 'selected';  
						  }
						 $btn.='<option value="'.$i->id.'" '.$selected.'>'.$i->full_name.'</option>';
                         }
                         return $btn;
						 })
					                    
					->addColumn('user_name', function($row){
						$u = DB::table('users')->find($row->user_num);
						$name='';
						switch($u->type){
							case 1: 
							$fname = DB::table('tbl_doctors')->where('doctor_user_num',$u->id)->value('first_name');
							$lname = DB::table('tbl_doctors')->where('doctor_user_num',$u->id)->value('last_name');
							$name = $fname.' '.$lname;
							break;
							case 2: 
							$name = DB::table('tbl_clinics')->where('id',$u->clinic_num)->value('full_name');
							break;
							case 3: 
							$name = DB::table('tbl_external_labs')->where('lab_user_num',$u->id)->value('full_name');
							break;
						  }
						return $name;
					 })
					
                    ->rawColumns(['referred_test'])
				    ->make(true);
	   
     break;
     case 'Info':
	   $sent_data1 = DB::table('tbl_visits_order_sent_email_history')->where('order_id',$request->order_id)->whereNOTNULL('email_date')->orderBy('id','desc')->get();
	   $sent_data2 = DB::table('tbl_visits_order_sent_sms_history')->where('order_id',$request->order_id)->whereNOTNULL('sms_date')->orderBy('id','desc')->get();
	   $fail_data = DB::table('tbl_visits_order_sent_fail')->where('order_id',$request->order_id)->orderBy('id','desc')->get();
	   return response()->json(['table1'=>$sent_data1,'table3'=>$sent_data2,'table2'=>$fail_data]);
     break;
     case 'chgRefferedLAB':
	  //dd("HI");
	  $order_id = $request->order_id;
	  $referred_lab=$request->referred_lab;
	  $custom_id=$request->custom_id;
	  $old_referred_lab = DB::table('tbl_visits_order_custom_tests')->where('id',$custom_id)->value('referred_lab');
	  $test_id = DB::table('tbl_visits_order_custom_tests')->where('id',$custom_id)->value('test_id');
	  $test_name = LabTests::where('id',$test_id)->value('test_name');
	  if($referred_lab!= $old_referred_lab){
		  DB::table('tbl_visits_order_custom_tests')->where('id',$custom_id)->update(['referred_lab'=>$referred_lab]);
	      $bill = TblBillHead::where('order_id',$order_id)->where('status','O')->first();
		  if(isset($bill)){
			  $bill_id=$bill->id;
			  $currencyEUR=TblBillCurrency::where('active','O')->where('abreviation','EUR')->first();
              $lbl_eur = isset($currencyEUR)?$currencyEUR->price:90000;
			  $ref_price=DB::table('tbl_referred_labs_prices')->where('lab_id',$referred_lab)->where('test_id',$test_id)->first();
		      $ref_lb_price=isset($ref_price) && isset($ref_price->totall)?$ref_price->totall:NULL;
              $ref_dollar_price=isset($ref_price) && isset($ref_price->totald)?$ref_price->totald:NULL;
              $ref_euro_price = isset($ref_price) && isset($ref_price->totall)? number_format($ref_price->totall / $lbl_eur, 2) : NULL;
		      TblBillSpecifics::where('status','O')->where('bill_num',$bill_id)->where('bill_code',$test_id)->update(['ref_lab'=>$referred_lab,'ref_lbill_price'=>$ref_lb_price,'ref_dolarprice'=>$ref_dollar_price,'ref_ebill_price'=>$ref_euro_price]);
		  }
	      return response()->json(['success'=>true,'msg'=>__('The Referred Lab for ').$test_name.__(' is changed successfully')]);
	  }else{
		  return response()->json(['success'=>false]);
	  }
     break;	 
	}
	
}	

public function filterTests($lang,Request $request){
	$group_num = $request->group_num;
	//$category_num = $request->category_num;
	
	
	
	$grp_tests = array();
	
	if($group_num !='' && $group_num!=NULL){
	    $groups_tests = DB::table('tbl_lab_groups as g')
		               ->select('g.descrip as test_name')
					   ->where('id',$group_num)
					   ->where('active','Y')->orderBy('id');
		
		$tests= LabTests::select('test_name')
		         ->where('active','Y')
				 ->where('group_num',$group_num)
				 ->orderby('group_num')->orderBy('testord')
				 ->union($groups_tests)
				 ->get();

	}else{
   $groups_tests = DB::table('tbl_lab_groups as g')
		               ->select('g.descrip as test_name')
					   ->where('active','Y')->orderBy('id');
		
		$tests= LabTests::select('test_name')
		         ->where('active','Y')
				 ->orderby('group_num')->orderBy('testord')
				 ->union($groups_tests)
				 ->get();
	}

	
		
	
	foreach($tests as $t){
	  array_push($grp_tests,$t->test_name);	
	}
	return response()->json(['tests'=>$grp_tests]);
}	

public function saveOrder($lang,Request $request){
	//dd($request->input('tests'));
	//get requet data
	$clinic_num = $request->clinic_num;
	$patient_num = $request->patient_num;
	$ext_lab =  $request->ext_lab;
	$id = $request->order_id;
	$doctor_num = $request->doctor_num;
	$user_num = auth()->user()->id;
	//get order tests without sub groups and save them
	$tstData = json_decode($request->input('tests'), true);
	//dd($tstData);
	$patient = Patient::find($patient_num);
	
	$currencyUSD=TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
    $lbl_usd = isset($currencyUSD)?$currencyUSD->price:90000;
	
	$currencyEUR=TblBillCurrency::where('active','O')->where('abreviation','EUR')->first();
    $lbl_eur = isset($currencyEUR)?$currencyEUR->price:90000;
    
	//get prices
	 $doc_lab = Clinic::where('id',auth()->user()->clinic_num)->first();
	 $tbl_name = 'tbl_clinics_prices';
	 //get according to patient external , if none then choose clinic  
	 if(isset($ext_lab) && $ext_lab!=''){
		 $doc_lab = ExtLab::where('id',$ext_lab)->first();
		$tbl_name = 'tbl_external_labs_prices';
	 }else{
	 if(isset($patient->ext_lab) && $patient->ext_lab!=''){
		$doc_lab = ExtLab::where('id',$patient->ext_lab)->first();
		$tbl_name = 'tbl_external_labs_prices';
	  }
	 }
   	
	$is_trial = $request->is_trial;
	$guarantor_valid = ExtLab::where('id',$ext_lab)->value('is_valid');
	
	//save order at first
	if($id=='0'){
		
		//get file number
		$nextRequestNb = LabOrders::generateRequestNb();
		
		//create new order
		$order_id = LabOrders::create([
		  'clinic_num'=>$clinic_num,
		  'patient_num'=>$patient_num,
		  'ext_lab' =>$ext_lab,
		  'doctor_num'=>$doctor_num,
		  'user_num'=>$user_num,
		  'order_datetime'=>Carbon::now()->format('Y-m-d H:i'),
		  'status'=>'P',
		  'is_trial'=>$is_trial,
		  'active' => 'Y',
		  'request_nb'=>$nextRequestNb
		  ])->id;
	   $order = LabOrders::find($order_id);
	   
	    //create bill head for order
	   $bill_id = TblBillHead::create([
	     'order_id'=>$order->id,
		 'ext_lab'=>$order->ext_lab,
		 'clinic_num'=>$order->clinic_num,
		 'doctor_num'=>$order->doctor_num,
		 'patient_num'=>$order->patient_num,
		 'bill_datein'=>Carbon::now()->format('Y-m-d H:i'),
		 'user_num'=>$user_num,
		 'user_type'=>auth()->user()->type,
		 'status'=>'O',
		 'exchange_rate'=>$lbl_usd
	    ])->id;
		
		 //Add new serial for bill
	    $lab = Clinic::find($order->clinic_num);
      	$SerieFacBill = $lab->bill_serial_code;
		$SeqFacBill = $lab-> bill_sequence_num ;
		$reqID=trim($SerieFacBill)."-".($SeqFacBill+1);
		Clinic::where('id',$order->clinic_num)->update(['bill_sequence_num' => $SeqFacBill+1]);
		TblBillHead::where('id',$bill_id)->update(['clinic_bill_num'=>$reqID]);
        
		$tbillpricel=0.00;
        $tbillpriced=0.00;
		$tbillpricee=0.00;
        
		//this array is used to get tests for result and culture
	    $chk_tsts = array();
	   
	   //go test by test the codes without sub groups
	   foreach($tstData as $d){
		
		$tid = intval($d['test_id']);
		$test = LabTests::find($tid);
		$rtst = isset($d['referred_test']) && $d['referred_test']!=''?intval($d['referred_test']):(isset($test) && $test->referred_tests?$test->referred_tests:NULL);
		if(!in_array($tid,$chk_tsts)){
		  array_push($chk_tsts,$tid);
		}
		
		
		
		//custom tests section
		DB::table('tbl_visits_order_custom_tests')->insert([
		  'user_num'=>$user_num,
		  'order_id'=>$order->id,
		  'test_id'=>$test->id,
		  'clinic_num'=>$order->clinic_num,
		  'patient_num'=>$order->patient_num,
		  'referred_lab'=>$rtst,
		  'insert_date'=>Carbon::now()->format('Y-m-d H:i'),
		  'active'=>'Y'
		 ]);
		 //set referred labs for bill
		 $ref_price=DB::table('tbl_referred_labs_prices')->where('lab_id',$rtst)->where('test_id',$tid)->first();
         $ref_lb_price=$ref_dollar_price=$ref_euro_price=NULL;
		 if(isset($ref_price)){
			 $ref_lb_price= $ref_price->totall;
			 $ref_dollar_price= $ref_price->totald;
			 $ref_euro_price = number_format($ref_price->totall / $lbl_eur, 2);
		 }
		
		//bill section
		if($test->cnss !=NULL && $test->cnss !=''){
		   $priced = $pricel = $pricee =0;
		   $pr = DB::table($tbl_name)->where('lab_id',$doc_lab->id)->where('test_id',$test->id)->first(); 
				if(isset($pr)){
						   if(isset($pr->totald) && $pr->totald!='' && $pr->totald!=0){
										$priced = $pr->totald;
								}
							  if(isset($pr->totall) && $pr->totall!='' && $pr->totall!=0){
										$pricel = $pr->totall; 
								}
							if(isset($pr->totale) && $pr->totale!='' && $pr->totale!=0){
										$pricee = $pr->totale; 
									} 
						 }
					 
					 $priced = number_format((float)$priced,2,'.','');
		             $pricel = number_format((float)$pricel,2,'.','');
			         $pricee = number_format((float)$pricee,2,'.','');
					
					TblBillSpecifics::create([
					   'bill_num'=>$bill_id,
					   'user_num'=>$user_num,
					   'user_type'=>auth()->user()->type,
					   'bill_code'=>$test->id,
					   'cnss'=>$test->cnss,
					   'bill_name'=>$test->test_name,
					   'bill_quantity'=>$test->nbl,
					   'lbill_price'=>$pricel,
					   'bill_price'=>$priced,
					   'ebill_price'=>$pricee,
					   'ref_lab'=>$rtst,
					   'ref_lbill_price'=>$ref_lb_price,
			           'ref_dolarprice'=>$ref_dollar_price,
			           'ref_ebill_price'=>$ref_euro_price,
					   'status'=>'O'
					  ]);
					  
					  $tbillpricel=$tbillpricel+ $pricel;
					  $tbillpriced=$tbillpriced+ $priced;
					  $tbillpricee=$tbillpricee+ $pricee;
				    }
		
	
	
	            }

        //update bill totals
        $bill = TblBillHead::find($bill_id);
		$sumpay=DB::table('tbl_bill_payment')->where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
        $sumref=DB::table('tbl_bill_payment')->where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');
        $tbalance = $tbillpricel-$sumpay+$sumref+$bill->tvq+$bill->tps;
        $Nbalance=number_format((float)$tbalance, 2, '.', ',');
        $balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));
		
		$sumpayd = DB::table('tbl_bill_payment')->where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('dpay_amount');
		$sumrefd = DB::table('tbl_bill_payment')->where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('dpay_amount');
		$tbalanced = $tbillpriced-$sumpayd+$sumrefd+$bill->tvq+$bill->tps;
        $Nbalanced=number_format((float)$tbalanced, 2, '.', ',');
        $balanced=floatval(preg_replace('/[^\d.-]/', '', $Nbalanced));
		
		TblBillHead::where('id',$bill_id)->update(['bill_balance'=>$balance,'bill_balance_us'=>$balanced,'lbill_total'=>$tbillpricel,'bill_total'=>$tbillpriced,'ebill_total'=>$tbillpricee]);			
	   
	   $result_tests = LabTests::where(function($q) use($chk_tsts){
					     $q->where('is_group','<>','Y');
					     $q->whereIn('id',$chk_tsts);
					     })->orWhere(function($q) use($chk_tsts){
					          $q->where('is_group','<>','Y');
					          $q->whereIn('group_num',$chk_tsts);
				         })->where('active','Y')->pluck('id')->toArray();
	 
	 //create results for new order for each test
	   foreach($result_tests as $ord){
		    $test = LabTests::find($ord);
			$subgroup_order = $test->testord;
			$group_order = NULL;
			$group_num = NULL;
			if(isset($test->group_num) &&  $test->group_num!=''){
			 $group_order = LabTests::where('id',$test->group_num)->value('testord');
			 $group_num = $test->group_num;
			}
			
			$need_validation = ((isset($guarantor_valid) && $guarantor_valid=='Y') || ($test->is_valid=='Y' && $test->is_test!='Y'))?'Y':'N';
			
			//create tests section for template_report_id
			 $template = DB::table('tbl_tests_custom_report')->where('active','Y')->where('test_id',$test->id)->first();
			 if(isset($template)){
				 
				 DB::table('tbl_visits_order_template_reports')->insert([
					  'user_num'=>$user_num,
					  'order_id'=>$order->id,
					  'test_id'=>$test->id,
					  'clinic_num'=>$order->clinic_num,
					  'patient_num'=>$order->patient_num,
					  'description'=>$template->description,
					  'need_validation'=>$need_validation,
					  'active'=>'Y'
				  ]);
			    }

			if($test->is_culture=='Y'){
				//create culture data
                DB::table('tbl_order_culture_results')->insert([
				                 'order_id'=>$order->id,'test_id'=>$test->id,
	                             'user_num'=>$user_num,'clinic_num'=>$order->clinic_num,'active'=>'Y',
								 'patient_num'=>$order->patient_num,'ext_lab' =>$order->ext_lab,
								 'need_validation'=>$need_validation]);
				
			}else{
			    //check if there is a previous result id
				$prev_result_id = LabResults::join('tbl_visits_orders as o','o.id','tbl_visits_order_results.order_id')
							      ->where('o.clinic_num',$order->clinic_num)
							      ->where('o.patient_num',$order->patient_num)
							      ->where('o.id','<>',$order->id)
								  ->where('o.order_datetime','<',$order->order_datetime)
							      ->where('o.active','Y')
							      ->where('tbl_visits_order_results.active','Y')
							      ->where('tbl_visits_order_results.test_id',$test->id)
							      ->orderBy('tbl_visits_order_results.id','desc')
								  ->orderBy('tbl_visits_order_results.created_at','desc')
							       ->value('tbl_visits_order_results.id');
					   
				
				$result_id = LabResults::create([
				 'user_num'=>$user_num,
				 'clinic_num'=>$order->clinic_num,
				 'patient_num'=>$order->patient_num,
				 'order_id'=>$order->id,
				 'test_id'=>$test->id,
				 'prev_result_num'=>isset($prev_result_id)?$prev_result_id:NULL,
				 'active'=>'Y',
				 'group_order'=>$group_order,
				 'subgroup_order'=>$subgroup_order,
				 'group_num'=>$group_num,
				 'need_validation'=>$need_validation
				])->id;
				
				
					 $patient = Patient::find($order->patient_num);
					 $field_id = $this->getFieldRefRange($result_id);
					 $ref_data = $this->getRefUnit($patient->sex,$test->id,$test->is_printed,$field_id);
					 
					 LabResults::where('id',$result_id)->update(['ref_range'=>$ref_data['ref_range'],'one_ref_range'=>$ref_data['one_ref_range'],
					                                             'unit'=>$ref_data['unit'],'field_num'=>implode(',',$field_id)
																 ]);
					
				
			}
		 
		 
		 
		 }
		
	
	}else{
		//update order procedure
		$order_id = $id;
	    LabOrders::where('id',$id)->update([
		   'ext_lab' =>$ext_lab,
		   'doctor_num'=>$doctor_num,
		   'is_trial'=>$is_trial,
		   'user_num'=>$user_num
		]);
	    $order = LabOrders::find($order_id);
	    
		
		//get active bill data for this order
        $bill = TblBillHead::where('order_id',$order->id)->where('status','O')->first();
        if(isset($bill)){
	      $bill_id = $bill->id;
		  $exchange_rate=isset($bill->exchange_rate) && $bill->exchange_rate!=''?$bill->exchange_rate:$lbl_usd;
		  TblBillHead::where('id',$bill_id)->update([
		   'ext_lab'=>$order->ext_lab,
		   'clinic_num'=>$order->clinic_num,
		   'doctor_num'=>$order->doctor_num,
		   'patient_num'=>$order->patient_num,
		   'bill_datein'=>Carbon::now()->format('Y-m-d H:i'),
		   'user_num'=>$user_num,
		   'user_type'=>auth()->user()->type,
		   'exchange_rate'=>$exchange_rate
	       ]);
	   }else{
		   //create bill head for order
		   $bill_id = TblBillHead::create([
			 'order_id'=>$order->id,
			 'ext_lab'=>$order->ext_lab,
			 'clinic_num'=>$order->clinic_num,
			 'doctor_num'=>$order->doctor_num,
			 'patient_num'=>$order->patient_num,
			 'bill_datein'=>Carbon::now()->format('Y-m-d H:i'),
			 'user_num'=>$user_num,
			 'user_type'=>auth()->user()->type,
			 'status'=>'O',
			 'exchange_rate'=>$lbl_usd
		    ])->id;
			
		   $lab = Clinic::find($order->clinic_num);
      	   $SerieFacBill = $lab->bill_serial_code;
		   $SeqFacBill = $lab-> bill_sequence_num ;
		   $reqID=trim($SerieFacBill)."-".($SeqFacBill+1);
		   Clinic::where('id',$order->clinic_num)->update(['bill_sequence_num' => $SeqFacBill+1]);
		   TblBillHead::where('id',$bill_id)->update(['clinic_bill_num'=>$reqID]);	
	       }
		   
		   //delete all old bill details
		   TblBillSpecifics::where('bill_num',$bill_id)->delete();
		   $tbillpricel=0.00;
		   $tbillpriced=0.00;
		   $tbillpricee=0.00;
		   
		   //this array is used to get tests for result and culture
	       $chk_tsts = array();
		   
		   //go test by test the codes without sub groups
		   foreach($tstData as $d){
			  $tid = intval($d['test_id']);
			  $test = LabTests::find($tid);
			  $rtst = isset($d['referred_test']) && $d['referred_test']!=''?intval($d['referred_test']):(isset($test) && $test->referred_tests?$test->referred_tests:NULL);

				if(!in_array($tid,$chk_tsts)){
				  array_push($chk_tsts,$tid);
				}
				
			$custom_tst = DB::table('tbl_visits_order_custom_tests')->where('order_id',$order->id)->where('test_id',$tid)->first();
			
			if(isset($custom_tst)){
				//custom tests section
					DB::table('tbl_visits_order_custom_tests')->where('id',$custom_tst->id)->update([
					  'user_num'=>$user_num,
					  'referred_lab'=>$rtst
					 ]);
			}else{
				
				//custom tests section
				DB::table('tbl_visits_order_custom_tests')->insert([
				  'user_num'=>$user_num,
				  'order_id'=>$order->id,
				  'test_id'=>$test->id,
				  'clinic_num'=>$order->clinic_num,
				  'patient_num'=>$order->patient_num,
				  'referred_lab'=>$rtst,
				  'insert_date'=>Carbon::now()->format('Y-m-d H:i'),
				  'active'=>'Y'
				 ]);
				
			  }
	
			   //set referred lab for bill
			   $ref_price=DB::table('tbl_referred_labs_prices')->where('lab_id',$rtst)->where('test_id',$tid)->first();
               $ref_lb_price=$ref_dollar_price=$ref_euro_price=NULL;
			   if(isset($ref_price)){
					 $ref_lb_price= $ref_price->totall;
					 $ref_dollar_price= $ref_price->totald;
					 $ref_euro_price = number_format($ref_price->totall / $lbl_eur, 2);
				 }
			   
			   
			   //recalculate bill prices
			   if($test->cnss !=NULL && $test->cnss !=''){
					 $priced = $pricel = $pricee =0;
					 $pr = DB::table($tbl_name)->where('lab_id',$doc_lab->id)->where('test_id',$test->id)->first(); 
					   if(isset($pr)){
						   if(isset($pr->totald) && $pr->totald!='' && $pr->totald!=0){
										$priced = $pr->totald;
								}
							  if(isset($pr->totall) && $pr->totall!='' && $pr->totall!=0){
										$pricel = $pr->totall; 
								}
							if(isset($pr->totale) && $pr->totale!='' && $pr->totale!=0){
										$pricee = $pr->totale; 
									} 
						 }
					 
					 $priced = number_format((float)$priced,2,'.','');
		             $pricel = number_format((float)$pricel,2,'.','');
			         $pricee = number_format((float)$pricee,2,'.','');
		           
					TblBillSpecifics::create([
					   'bill_num'=>$bill_id,
					   'user_num'=>$user_num,
					   'user_type'=>auth()->user()->type,
					   'bill_code'=>$test->id,
					   'cnss'=>$test->cnss,
					   'bill_name'=>$test->test_name,
					   'bill_quantity'=>$test->nbl,
					   'lbill_price'=>$pricel,
					   'bill_price'=>$priced,
					   'ebill_price'=>$pricee,
					   'ref_lab'=>$rtst,
					   'ref_lbill_price'=>$ref_lb_price,
					   'ref_dolarprice'=>$ref_dollar_price,
					   'ref_ebill_price'=>$ref_euro_price,
					   'status'=>'O'
					   
					  ]);
					  
					  $tbillpricel=$tbillpricel+ $pricel;
					  $tbillpriced=$tbillpriced+ $priced;
					  $tbillpricee=$tbillpricee+ $pricee;
				    }
		   
		       }
		
		//update totals
        $get_bill = TblBillHead::find($bill_id);
		
		$sumpay=DB::table('tbl_bill_payment')->where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
        $sumref=DB::table('tbl_bill_payment')->where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');
        $tdiscount = isset($get_bill->bill_discount) && $get_bill->bill_discount!=''?$get_bill->bill_discount:0;
        $tbalance = $tbillpricel-$tdiscount-$sumpay+$sumref+$get_bill->tvq+$get_bill->tps;
        $Nbalance=number_format((float)$tbalance, 2, '.', ',');
        $balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));
		
        $sumpayd = DB::table('tbl_bill_payment')->where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('dpay_amount');
		$sumrefd = DB::table('tbl_bill_payment')->where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('dpay_amount');
		$tdiscountd = isset($get_bill->bill_discount_us) && $get_bill->bill_discount_us!=''?$get_bill->bill_discount_us:0;
		$tbalanced = $tbillpriced-$sumpayd-$tdiscountd+$sumrefd+$bill->tvq+$bill->tps;
        $Nbalanced=number_format((float)$tbalanced, 2, '.', ',');
        $balanced=floatval(preg_replace('/[^\d.-]/', '', $Nbalanced));
		
		//dd($doc_lab);
		TblBillHead::where('id',$bill_id)->update(['bill_balance'=>$balance,'bill_balance_us'=>$balanced,'lbill_total'=>$tbillpricel,'bill_total'=>$tbillpriced,'ebill_total'=>$tbillpricee]);		
		//delete custom tests not in checked tsts
        DB::table('tbl_visits_order_custom_tests')->where('order_id',$order->id)->whereNotIn('test_id',$chk_tsts)->delete();
		//delete template reports not in checked tsts
		DB::table('tbl_visits_order_template_reports')->where('order_id',$order->id)->whereNotIn('test_id',$chk_tsts)->delete();
		
		$result_tests = LabTests::where(function($q) use($chk_tsts){
					  $q->where('is_group','<>','Y');
					  $q->whereIn('id',$chk_tsts);
					 })->orWhere(function($q) use($chk_tsts){
					    $q->where('is_group','<>','Y');
					    $q->whereIn('group_num',$chk_tsts);
				      })->where('active','Y')->pluck('id')->toArray();
		
		//delete only results not in updated tests
	    LabResults::where('order_id',$order->id)->whereNotIn('test_id',$result_tests)->delete();
	   //delete only culture tests not in updated tests and its details
       $cult_ids = DB::table('tbl_order_culture_results')->where('order_id',$order->id)->whereNotIn('test_id',$result_tests)->pluck('id')->toArray();
	   DB::table('tbl_order_culture_results')->whereIn('id',$cult_ids)->delete();	 
	   DB::table('tbl_order_culture_results_detail')->whereIn('culture_id',$cult_ids)->delete();
		
		foreach($result_tests as $ord){
			$test = LabTests::find($ord);
			$subgroup_order = $test->testord;
			$group_order = NULL;
			$group_num = NULL;
			if(isset($test->group_num) &&  $test->group_num!=''){
			 $group_order = LabTests::where('id',$test->group_num)->value('testord');
			 $group_num = $test->group_num;
			}
			
			$need_validation = ((isset($guarantor_valid) && $guarantor_valid=='Y') || ($test->is_valid=='Y' && $test->is_test!='Y'))?'Y':'N';
			
			  //create tests section for template_report_id
		     $template = DB::table('tbl_visits_order_template_reports')->where('order_id',$order->id)->where('test_id',$ord)->first();
			 if(isset($template)){
				 //do nothing already exists
				 DB::table('tbl_visits_order_template_reports')->update([ 'need_validation'=>$need_validation]);
			  }else{
				  //create tests section for template_report_id
				 $template = DB::table('tbl_tests_custom_report')->where('active','Y')->where('test_id',$ord)->first();
				 if(isset($template)){
					 DB::table('tbl_visits_order_template_reports')->insert([
						  'user_num'=>$user_num,
						  'order_id'=>$order->id,
						  'test_id'=>$test->id,
						  'clinic_num'=>$order->clinic_num,
						  'patient_num'=>$order->patient_num,
						  'description'=>$template->description,
						  'need_validation'=>$need_validation,
						  'active'=>'Y'
					  ]);
				  }
			  }
			
			
			
			if($test->is_culture=='Y'){
				 $cult = DB::table('tbl_order_culture_results')->where('order_id',$order->id)->where('test_id',$ord)->first(); 
				 if(isset($cult)){
				    DB::table('tbl_order_culture_results')->update([ 'need_validation'=>$need_validation]);
				 }else{
					DB::table('tbl_order_culture_results')->insert(['order_id'=>$order->id,'test_id'=>$test->id,
	                             'user_num'=>$user_num,'clinic_num'=>$order->clinic_num,'active'=>'Y',
								 'patient_num'=>$order->patient_num,'need_validation'=>$need_validation]);
				    }	 
			}else{
				
				
				//check if test exists in result table 
				$result = LabResults::where('order_id',$id)->where('test_id',$ord)->first();
				
				//create only results not found in tests
				if(!isset($result)){ 
				   
				   $prev_result_id = LabResults::join('tbl_visits_orders as o','o.id','tbl_visits_order_results.order_id')
							      ->where('o.clinic_num',$order->clinic_num)
							      ->where('o.patient_num',$order->patient_num)
							      ->where('o.id','<>',$order->id)
								  ->where('o.order_datetime','<',$order->order_datetime)
							      ->where('o.active','Y')
							      ->where('tbl_visits_order_results.active','Y')
							      ->where('tbl_visits_order_results.test_id',$test->id)
							      ->orderBy('tbl_visits_order_results.id','desc')
								  ->orderBy('tbl_visits_order_results.created_at','desc')
							      ->value('tbl_visits_order_results.id');
		
				   $result_id = LabResults::create([
					 'user_num'=>$user_num,
					 'clinic_num'=>$order->clinic_num,
					 'patient_num'=>$order->patient_num,
					 'order_id'=>$order->id,
					 'test_id'=>$test->id,
					 'prev_result_num'=>isset($prev_result_id)? $prev_result_id:NULL,
					 'group_order'=>$group_order,
					 'subgroup_order'=>$subgroup_order,
					 'group_num'=>$group_num,
					 'need_validation'=>$need_validation,
					 'active'=>'Y'
					])->id;
				  
				 
					 $patient = Patient::find($order->patient_num);
					 $field_id = $this->getFieldRefRange($result_id);
					 $ref_data = $this->getRefUnit($patient->sex,$test->id,$test->is_printed,$field_id);
				    
					 LabResults::where('id',$result_id)->update(['ref_range'=>$ref_data['ref_range'],'one_ref_range'=>$ref_data['one_ref_range'],
					                                             'unit'=>$ref_data['unit'],'field_num'=>implode(',',$field_id)
																 ]);
					
				   
				  }else{
					  $prev_result_id = $result->prev_result_num;
					  if(!isset($prev_result_id)){
						   $prev_result_id = LabResults::join('tbl_visits_orders as o','o.id','tbl_visits_order_results.order_id')
							      ->where('o.clinic_num',$order->clinic_num)
							      ->where('o.patient_num',$order->patient_num)
							      ->where('o.id','<>',$order->id)
								  ->where('o.order_datetime','<',$order->order_datetime)
							      ->where('o.active','Y')
							      ->where('tbl_visits_order_results.active','Y')
							      ->where('tbl_visits_order_results.test_id',$test->id)
							      ->orderBy('tbl_visits_order_results.id','desc')
								  ->orderBy('tbl_visits_order_results.created_at','desc')
							      ->value('tbl_visits_order_results.id');
					  }
					  
					  
					  //test exists then update group and subgroup order with field_num and ref_range to ensure 
					  //if there is any updates in fields
                     	 $patient = Patient::find($order->patient_num);
						 $field_id = $this->getFieldRefRange($result->id);
						 //dd($field_id);
						 $ref_data = $this->getRefUnit($patient->sex,$test->id,$test->is_printed,$field_id);
						 
						 
						 LabResults::where('id',$result->id)->update(['group_num'=>$group_num,'group_order'=>$group_order,
						                                             'ref_range'=>$ref_data['ref_range'],
																	 'unit'=>$ref_data['unit'],'field_num'=>implode(',',$field_id),
																	 'one_ref_range'=>$ref_data['one_ref_range'],
																	 'subgroup_order'=>$subgroup_order,'user_num'=>$user_num,
																	 'prev_result_num'=>isset($prev_result_id)? $prev_result_id:NULL,
																	 'need_validation'=>$need_validation]);
						
						
								  
				  }
				 
				
			}	
		}
        		
				
		
	  
	  
	  
	  
 }//end update data

 //procedure to create text file if trial is N
	if($is_trial=='N'){
		$all_tests = LabResults::where('order_id',$order_id)->where('active','Y')->pluck('test_id')->toArray();
		$this->create_machine_files($order_id,$all_tests);
	}
 
 $location = route('lab.visit.edit',[$lang,$order_id]);	
 return response()->json(['msg'=>__('Saved succssfully'),'location'=>$location]);
}

public function saveResults($lang,Request $request){
	//dd($request->result_data);
	$arr = $request->result_data;
	$user_num = auth()->user()->id;
	$fixed_comment = isset($request->fixed_comment) && $request->fixed_comment!=''?$request->fixed_comment:NULL;
	
	LabOrders::where('id',$request->order_id)
		        ->update(['fixed_comment'=>$fixed_comment,'user_num'=>$user_num]);
				  
	foreach($arr as $k=>$v){
		$result = isset($v["result"])?$v["result"]:NULL;
		$result_status = isset($v["result_status"]) && $v["result_status"]!=''?$v["result_status"]:NULL;
		$id = $v["id"];
		$sign = $v["sign"];
		$calc_result = $v["calc_result"];
		$calc_unit = $v["calc_unit"];
		$test_type = $v["test_type"];
		
		if($test_type!='C'){
		  LabResults::where('id',$id)->update([
		          'result'=>$result,
		          'result_status'=>$result_status,
				  'sign'=>$sign,
				  'user_num'=>$user_num
				  ]);
		}else{
		  LabResults::where('id',$id)->update([
		          'result'=>$result,
		          'result_status'=>$result_status,
				  'sign'=>$sign,
				  'calc_result'=>$calc_result,
				  'calc_unit'=>$calc_unit,
				  'user_num'=>$user_num
				  ]);
	    }
		
		
		}
	
	
	
	return response()->json(["msg"=>__("Results are saved succssfully")]);
}

public function saveBill($lang,Request $request){
	$data = json_decode($request->data,true);
	$bill = TblBillHead::find($request->bill_id);
	$user_num = auth()->user()->id;
	$patient = Patient::find($bill->patient_num);
	//dd($data);
	//get according to user in order to get prices
	 $doc_lab = Clinic::where('id',auth()->user()->clinic_num)->first();
	 $tbl_name = "tbl_clinics_prices";
	 
	 if(isset($bill->ext_lab) && $bill->ext_lab!=''){
		$doc_lab = ExtLab::where('id',$bill->ext_lab)->first();
	    $tbl_name = "tbl_external_labs_prices";
	  }
	  
	$tbillpricel=0.00;
    $tbillpriced=0.00;
	$tbillpricee=0.00;
	
	$currencyEUR=TblBillCurrency::where('active','O')->where('abreviation','EUR')->first();
    $lbl_eur = isset($currencyEUR)?$currencyEUR->price:90000;
	
	 
	TblBillSpecifics::where('bill_num',$request->bill_id)->delete();
	foreach($data as $k=>$v){
	  $test_id = $v['bill_code'];
	  $priced = $pricel = $pricee =0;
	  $pr = DB::table($tbl_name)->where('lab_id',$doc_lab->id)->where('test_id',$test_id)->first(); 
	 //dd($pr);
	   if(isset($pr)){
			if(isset($pr->totald) && $pr->totald!='' && $pr->totald!=0){
										$priced = $pr->totald;
								}
		     if(isset($pr->totall) && $pr->totall!='' && $pr->totall!=0){
										$pricel = $pr->totall; 
								}
			 if(isset($pr->totale) && $pr->totale!='' && $pr->totale!=0){
										$pricee = $pr->totale; 
									} 
						 }
	   $priced = number_format((float)$priced,2,'.','');
	   $pricel = number_format((float)$pricel,2,'.','');
	   $pricee = number_format((float)$pricee,2,'.','');
	   
	   $ref_lab=DB::table('tbl_visits_order_custom_tests')->where('order_id',$bill->order_id)->where('test_id',$test_id)->value('referred_lab');
	   $ref_price=DB::table('tbl_referred_labs_prices')->where('lab_id',$ref_lab)->where('test_id',$test_id)->first();
	   $ref_lb_price=$ref_dollar_price=$ref_euro_price =NULL;
	   if(isset($ref_price)){
		  $ref_lb_price= $ref_price->totall;
		  $ref_dollar_price=$ref_price->totald;
          $ref_euro_price =	number_format($ref_price->totall / $lbl_eur, 2);	  
	   }
	  TblBillSpecifics::create([
		'bill_num'=>$bill->id,
		'doctor_num'=>$bill->doctor_num,
		'ext_lab'=>$bill->ext_lab,
	    'user_num'=>$user_num,
		'user_type'=>auth()->user()->type,
		'bill_code'=>$test_id,
		'cnss'=>$v['cnss'],
		'bill_name'=>$v['bill_name'],
		'bill_quantity'=>$v['bill_quantity'],
		'lbill_price'=> $pricel,
		'bill_price'=> $priced,
		'ebill_price'=> $pricee,
		'ref_lab'=>$ref_lab,
		'ref_lbill_price'=>$ref_lb_price,
		'ref_dolarprice'=>$ref_dollar_price,
		'ref_ebill_price'=>$ref_euro_price,
		'status'=>'O'
		]);
	  
	  $tbillpricel=$tbillpricel+$pricel;
      $tbillpriced=$tbillpriced+$priced;
	  $tbillpricee=$tbillpricee+$pricee;
	  
	  }
	  
	 
	  
	  //update totals
      $sumpay=DB::table('tbl_bill_payment')->where('bill_num','=',$bill->id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
      $sumref=DB::table('tbl_bill_payment')->where('bill_num','=',$bill->id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');
      $sumpayd=DB::table('tbl_bill_payment')->where('bill_num','=',$bill->id)->where('status','Y')->where('payment_type','=','P')->sum('dpay_amount');
      $sumrefd=DB::table('tbl_bill_payment')->where('bill_num','=',$bill->id)->where('status','Y')->where('payment_type','=','R')->sum('dpay_amount');
      
	  
	  $tdiscount = 0;
	  if(isset($bill->bill_discount) && $bill->bill_discount!='' && $bill->bill_discount!=0){
		 $tdiscount = $bill->bill_discount;  
		
	  }
	  
	  $tdiscountd = 0;
	  if(isset($bill->bill_discount_us) && $bill->bill_discount_us!='' && $bill->bill_discount_us!=0){
		 $tdiscountd = $bill->bill_discount_us;  
		 
	  }else{
		  if($tdiscount!=0){
			 $currencyUSD=TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
             $lbl_usd = isset($currencyUSD)?$currencyUSD->price:90000;
			 $tdiscountd = floatval($tdiscount/$lbl_usd);
		 }
	  }
	 
	 //dd($tdiscount.'-'.$tdiscountd);
	  $tbalance = $tbillpricel-$tdiscount-$sumpay+$sumref+$bill->tvq+$bill->tps;
      $Nbalance=number_format((float)$tbalance, 2, '.', ',');
      $balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));

      $tbalanced = $tbillpriced-$tdiscountd-$sumpayd+$sumrefd+$bill->tvq+$bill->tps;
      $Nbalanced=number_format((float)$tbalanced, 2, '.', ',');
      $balanced=floatval(preg_replace('/[^\d.-]/', '', $Nbalanced));  	  
      
	  
	  TblBillHead::where('id',$bill->id)->update(['bill_datein'=>Carbon::now()->format('Y-m-d H:i'),'bill_discount'=>$tdiscount,'bill_discount_us'=>$tdiscountd,'bill_balance'=>$balance,'bill_balance_us'=>$balanced,'lbill_total'=>$tbillpricel,'bill_total'=>$tbillpriced,'ebill_total'=>$tbillpricee]);		
	  
	     //data to return to user
		$sumpayd =  number_format((float)$sumpayd,2,'.','');
		$sumpayl =  number_format((float)$sumpay,2,'.',',');

		$sumrefd =  number_format((float)$sumrefd,2,'.','');
		$sumrefl =  number_format((float)$sumref,2,'.',',');

		$balanced =  number_format((float)$balanced,2,'.','');
		$balancel=number_format((float)$balance, 2, '.', ',');

		$tdiscountd =  number_format((float)$tdiscountd,2,'.','');
		$tdiscountl =  number_format((float)$tdiscount,2,'.',',');
	  
	  return response()->json(['msg'=>__('Bill saved successfully'),'sumpay'=>$sumpayl,'sumref'=>$sumrefl,'nbalance'=>$balancel,'tdiscount'=>$tdiscountl,
                         'tdiscountd'=>$tdiscountd,'sumpayd'=>$sumpayd,'sumrefd'=>$sumrefd,'balanced'=>$balanced]);	  	 
}

public function validateResults($lang,Request $request){
	//dd("HI");
	$id = $request->order_id;
	$type = $request->type;
	$user_num = auth()->user()->id;
	switch($type){
		case 'finishProcess':
		   $is_valid = $request->is_valid;
		   switch($is_valid){
			  //results finish
			  case 'F':
				   $report_datetime = LabOrders::where('id',$id)->value('report_datetime');
				   if(isset($report_datetime) && $report_datetime!=''){
					 LabOrders::where('id',$id)->update(['pdf_path'=>NULL,'status'=>'F','user_num'=>$user_num]);
  				   }else{
				     LabOrders::where('id',$id)->update(['pdf_path'=>NULL,'report_datetime'=>Carbon::now()->format('Y-m-d H:i'),'status'=>'F','user_num'=>$user_num]);
				    }
				   $msg = __('Results are done succssfully');
				   return response()->json(['return_status'=>'F','msg'=>$msg]); 
			  break;
			  //invalidate results to Finished
			  case 'IF':
				   //update back results that need validation from N:Done to Y:Yes
				   //get ids to update to return need validation to Y
				   $exists_reults = LabResults::where('order_id',$id)->where('active','Y')->where('need_validation','<>','Y')->exists();
				   if($exists_reults){
				     LabResults::where('order_id',$id)->where('active','Y')->where('need_validation','<>','Y')->update(['need_validation'=>'Y','user_num'=>$user_num]);
				   }
				   
				   $cult_exists = DB::table('tbl_order_culture_results')->where('order_id',$id)->where('active','Y')->exists();
				   if($cult_exists){
					  DB::table('tbl_order_culture_results')->where('order_id',$id)->where('active','Y')->where('need_validation','<>','Y')->update(['need_validation'=>'Y','user_num'=>$user_num]);
					 }
				   
				   $temp_exists = DB::table('tbl_visits_order_template_reports')->where('order_id',$id)->where('active','Y')->exists();
				   if($temp_exists){
					  DB::table('tbl_visits_order_template_reports')->where('order_id',$id)->where('active','Y')->where('need_validation','<>','Y')->update(['need_validation'=>'Y','user_num'=>$user_num]);
						 }
				   
				   $report_datetime = LabOrders::where('id',$id)->value('report_datetime');
				   if(isset($report_datetime) && $report_datetime!=''){
					 LabOrders::where('id',$id)->update(['pdf_path'=>NULL,'status'=>'F','user_num'=>$user_num]);
  				   }else{
				     LabOrders::where('id',$id)->update(['pdf_path'=>NULL,'report_datetime'=>Carbon::now()->format('Y-m-d H:i'),'status'=>'F','user_num'=>$user_num]);
				    }
				   $msg = __('Results are invalidated succssfully');
				   return response()->json(['return_status'=>'F','msg'=>$msg]); 
			  break;
			  //invalidate results to Pending
			  case 'P': case 'IP':
				   if($is_valid=='IP'){
					   //do nothing the results do not need validation
				   }else{
				  
				   
				   //update back results that need validation from N:Done to Y:Yes
				   //get ids to update to return need validation to Y
				   $exists_reults = LabResults::where('order_id',$id)->where('active','Y')->where('need_validation','<>','Y')->exists();
				   if($exists_reults){
				     LabResults::where('order_id',$id)->where('active','Y')->where('need_validation','<>','Y')->update(['need_validation'=>'Y','user_num'=>$user_num]);
				   }
				   
				   $cult_exists = DB::table('tbl_order_culture_results')->where('order_id',$id)->where('active','Y')->exists();
				   if($cult_exists){
					  DB::table('tbl_order_culture_results')->where('order_id',$id)->where('active','Y')->where('need_validation','<>','Y')->update(['need_validation'=>'Y','user_num'=>$user_num]);
					 }
				   
				   $temp_exists = DB::table('tbl_visits_order_template_reports')->where('order_id',$id)->where('active','Y')->exists();
				   if($temp_exists){
						 DB::table('tbl_visits_order_template_reports')->where('order_id',$id)->where('active','Y')->where('need_validation','<>','Y')->update(['need_validation'=>'Y','user_num'=>$user_num]);
						 }
				   }
				   LabOrders::where('id',$id)->update(['pdf_path'=>NULL,'report_datetime'=>NULL,'status'=>'P','user_num'=>$user_num]);
				   $msg = __('Results are invalidated succssfully');
				   return response()->json(['return_status'=>'P','msg'=>$msg]); 
				  
			  break;
              case 'V': 
			    
			    $user_num  = auth()->user()->id;
			    //begin auto sending email and sms
			    $order = LabOrders::find($id);   
		        $lab = Clinic::find($order->clinic_num);
	            $patient = Patient::find($order->patient_num);
		        $ext_lab = ExtLab::find($order->ext_lab);
	            $doc = Doctor::find($order->doctor_num);
		  	    $pat_name = $patient->first_name;
				if(isset($patient->middle_name) && $patient->middle_name!=''){
				  $pat_name.=' '.$patient->middle_name;
				 }
				$pat_name.=' '.$patient->last_name;
	   
				$doc_name = '';
				if(isset($doc)){
				 $doc_name = $doc->first_name;
				 if(isset($doc->middle_name) && $doc->middle_name!=''){
				   $doc_name.=' '.$doc->middle_name;
				  }
				 $doc_name.=' '.$doc->last_name;
				}
				
		       $sms_body=$email_body=$email_subject='';
			   //get clinic SMS (body) and Email (subject , body)
		 	   $sms_body = $this->GenerateSMS($lang,$lab,$order,$pat_name,$doc_name);
			   $email_body = $this->GenerateEMAIL($lab,$order,$pat_name,$doc_name,'body');
			   $email_subject = $this->GenerateEMAIL($lab,$order,$pat_name,$doc_name,'subject');
			   
		       $lab_name = $lab->full_name;
	           $lab_email = $lab->email;
	           //prepare email parts
	           $pat_email = isset($patient->email)?$patient->email:'';
			   $pat_tel = isset($patient->cell_phone)?$patient->cell_phone:'';
			   $doc_email = isset($doc) && isset($doc->email)?$doc->email:'';
			   $doc_tel = isset($doc) && isset($doc->tel)?$doc->tel:'';
			   $guarantor_email = isset($ext_lab) && isset($ext_lab->email)?$ext_lab->email:'';
			   $guarantor_tel = isset($ext_lab) && isset($ext_lab->telephone)?$ext_lab->telephone:'';
	           $email_success = $error=$sms_success=$pdf='';
	
		       //prepare email body
	           $sms_body  = $this->escape_newline($sms_body);
	           $sms_body = str_replace("*ResultsPDF*","*DownloadLink*",$sms_body);
	           $sms_body=str_replace('*BR*',PHP_EOL,$sms_body);
		
			   $emails = array();
	           $to_patient = $to_doctor = $to_guarantor = 'N';
			   $status_id = '';
	
				if(isset($pat_email) && $pat_email!=''){
					array_push($emails,$pat_email);
					$to_patient = 'Y';
					
				}
		
				if(isset($doc_email) && $doc_email!=''){
					array_push($emails,$doc_email);
					$to_doctor = 'Y';
					
				}
		
				if(isset($guarantor_email) && $guarantor_email!=''){
					array_push($emails,$guarantor_email);
					$to_guarantor = 'Y';
					
				}
				
				//check if pdf exists then get it else generate it	
		  	   if(isset($order->pdf_path) && $order->pdf_path!=''){ 
			      $path = storage_path('app/7mJ~33/'.$order->pdf_path);
				  $pdf = file_get_contents($path);
			   }else{
				  $path = $this->getPDFPath($lang,$order->id);
				  $pdf = file_get_contents($path);  
			   }
	         $error='';
			 $cnt_email_errors=0;
			 $cnt_sms_errors=0;
		  	//only send emails if there is emails	
			if(count($emails)>0){	
			  
			    $result = $this->sendResultsByEmail($id,$lab_name,$lab_email,$emails,$email_subject,$email_body,$pdf);
				if($result){
					
					$email_success.='<div>'.__("Emails sent succssfully to")." : ".implode(',',$emails).'</div>';
					$now_time = Carbon::now()->format("Y-m-d H:i");
					DB::table('tbl_visits_order_sent_email_history')->insert([
									  'order_id'=>$id,
									  'to_patient'=>$to_patient,
									  'to_doctor'=>$to_doctor,
									  'to_guarantor'=>$to_guarantor,
									  'email_date'=>$now_time,
									  'user_num'=>$user_num
									]);
								
				}else{
				  $now_time = Carbon::now()->format("d/m/Y H:i");
				  $error.='<div>'.__("Emails failed to be sent succssfully on").' : '.$now_time.'</div>';
				  $cnt_email_errors++;
				  }	
				}else{
				  $error.='<div>'.__("No emails are provided for either Patient/Guarantor/Doctor").'</div>';
				  $cnt_email_errors++;
				}
			
			if($cnt_email_errors==0){
			
			  //check sms pack to send sms
	          $chk_pack = $this->check_sms_pack($order->clinic_num);
			  //reset send data
		      $to_patient = $to_doctor = $to_guarantor = 'N';
				
				if($chk_pack){
				    //pass tels to send SMS text messages for them
					$tels = array();
					$types = array();
					if(isset($pat_tel) && $pat_tel!=''){
					   $pat_tel=str_replace("-","",$pat_tel);
					   $pat_tel=str_replace(" ","",$pat_tel);
					   array_push($tels,$pat_tel);
					   $to_patient='Y';
					  }
					if(isset($doc_tel) && $doc_tel!=''){
					   $doc_tel=str_replace("-","",$doc_tel);
					   $doc_tel=str_replace(" ","",$doc_tel);
					   array_push($tels,$doc_tel);
					   $to_doctor='Y';
					  }
					if(isset($guarantor_tel) && $guarantor_tel!=''){
					   $guarantor_tel=str_replace("-","",$guarantor_tel);
					   $guarantor_tel=str_replace(" ","",$guarantor_tel);
					   array_push($tels,$guarantor_tel);
					   $to_guarantor='Y';
					  }   
				    
						//only send sms if there is tels
                    if(count($tels)>0){	
					  $result = $this->sendResultsBySMS($lang,$id,$tels,$sms_body,$types);
					  if($result){
							$sms_success.='<div>'.__("SMS sent succssfully to")." : ".implode(',',$tels).'</div>';
							$now_time = Carbon::now()->format("Y-m-d H:i");
							DB::table('tbl_visits_order_sent_sms_history')->insert([
								  'order_id'=>$id,
								  'to_patient'=>$to_patient,
								  'to_doctor'=>$to_doctor,
								  'to_guarantor'=>$to_guarantor,
								  'sms_date'=>$now_time,
								  'user_num'=>$user_num
								]);
							
						}else{
							$now_time = Carbon::now()->format("d/m/Y H:i");
		                    $error.='<div>'.__("SMS failed to be sent succssfully on").' : '.$now_time.'</div>';
							$cnt_sms_errors++;
						}
				    }else{
						$error.='<div>'.__("No telephone is provided for either Patient/Guarantor/Doctor").'</div>';
						$cnt_sms_errors++;
					}
				
				
				}else{
					$error.='<div>'.__("No SMS units are left to send text messages!").'</div>';
					$cnt_sms_errors++;
				}
			  }
	         
	          if($error!=''){
				  DB::table('tbl_visits_order_sent_fail')->insert([
				  'order_id'=>$id,
				  'user_num'=>$user_num,
				  'error_msg'=>$error
				  ]);
			  }
	          
			  if($cnt_sms_errors!=0 && $cnt_email_errors!=0){
				  $msg =__('Error!,No data is sent by either Email or SMS');
				  return response()->json(['return_status'=>'NV','msg'=>$msg]); 
			  }else{
				//update status of messages
	            LabOrders::where('id',$id)->update(['status'=>'V','user_num'=>$user_num]);
	            $report_datetime = $order->report_datetime;
				//only update if there is no report datetime 
			    if($report_datetime==NULL || $report_datetime==''){
				    LabOrders::where('id',$id)->update(['report_datetime'=>Carbon::now()->format('Y-m-d H:i')]);
			    }
	          
			    $msg ='<div>'.__('Results are validated succssfully').'</div>';
			    $msg.=$email_success.$sms_success;
			    $sms_pack = $this->get_sms_pack($order->clinic_num); 
				return response()->json(['return_status'=>'V','msg'=>$msg,'sms_pack'=>$sms_pack]); 
			  }
			  
			  
			 
		      break;			  
		   
		   
		   
		   }
		break;
		
		case 'openValidateModal':
		$rows = array();	                
	    $tests = DB::table('tbl_visits_order_results as r')
		           ->select('o.status','r.need_validation','r.id as result_id','r.test_id','t.testord','t.test_name','t.group_num',DB::raw("IF(r.result IS NOT NULL and r.result<>'',r.result,IF(r.result_txt IS NOT NULL and r.result_txt<>'',r.result_txt,'')) as rslt"))
				   ->join('tbl_lab_tests as t','t.id','r.test_id')
				   ->join('tbl_visits_orders as o','o.id','r.order_id')
				   ->where('r.order_id',$request->order_id)
				   ->where('r.active','Y')
				   ->where('t.is_title','<>','Y')
				   ->orderBy('t.group_num')
				   ->orderBy('t.testord')
				   ->get();
				   
		 
		$html = '';
        $cnt = $tests->count();
		if($cnt){
		   foreach($tests as $t){		
				
				$checked = $t->need_validation<>'Y'?'checked':'';
				$group_name =LabTests::where('id',$t->group_num)->value('test_name');
				$test_name = $t->test_name;
				$test_rslt = strlen($t->rslt)>50?substr($t->rslt,0,50).'...':$t->rslt;
				array_push($rows,array(
				'action'=>'<input type="checkbox" data-id="'.$t->result_id.'" '.$checked.'  class="validate_one_tst"/>',
				'type'=>'',
				'group'=>$group_name,
				'test'=>$test_name,
				'result'=>$test_rslt
				));
			} 		
		}
		
		$cult_tests = DB::table('tbl_order_culture_results as r')
		              ->select('o.status','r.need_validation','r.id as result_id','r.test_id','t.testord','t.test_name','t.group_num','culture_result as rslt')
					  ->join('tbl_lab_tests as t','t.id','r.test_id')
				      ->join('tbl_visits_orders as o','o.id','r.order_id')
                      ->where('r.order_id',$request->order_id)
				      ->where('r.active','Y')
					  ->orderBy('t.group_num')
				      ->orderBy('t.testord')
				      ->get();					  
		$cnt1 = $cult_tests->count();
		
		if($cnt1){
		  foreach($cult_tests as $t){
				$type='Culture';
				$checked = $t->need_validation<>'Y'?'checked':'';
				$group_name =LabTests::where('id',$t->group_num)->value('test_name');
				$test_name = $t->test_name;
				$test_rslt = strlen($t->rslt)>50?substr($t->rslt,0,50).'...':$t->rslt;
				array_push($rows,array(
				'action'=>'<input type="checkbox" data-id="'.$t->result_id.'" '.$checked.' class="validate_one_tst"/>',
				'type'=>$type,
				'group'=>$group_name,
				'test'=>$test_name,
				'result'=>$test_rslt
				));
			} 		
		}
		
		$temp_tests = DB::table('tbl_visits_order_template_reports as r')
		              ->select('o.status','r.need_validation','r.test_id','r.id as result_id','t.testord','t.test_name','t.group_num')
					  ->join('tbl_lab_tests as t','t.id','r.test_id')
				      ->join('tbl_visits_orders as o','o.id','r.order_id')
                      ->where('r.order_id',$request->order_id)
				      ->where('r.active','Y')
				      ->orderBy('t.group_num')
				      ->orderBy('t.testord')
					  ->get();					  
		
		$cnt2 = $temp_tests->count();
		if($cnt2){
		  foreach($temp_tests as $t){
				$type='Template';
				$checked = $t->need_validation<>'Y'?'checked':'';
				$group_name =LabTests::where('id',$t->group_num)->value('test_name');
				$test_name = $t->test_name;
				$test_rslt = '-';
				array_push($rows,array(
				'action'=>'<input type="checkbox" data-id="'.$t->result_id.'" '.$checked.' class="validate_one_tst"/>',
				'type'=>$type,
				'group'=>$group_name,
				'test'=>$test_name,
				'result'=>$test_rslt
				));
			} 		
		}
		
		
		return response()->json(['rows'=>$rows]);
		break;
		case 'endValidateModal':
		  //dd("HI");
		  $result_ids = isset($request->resultIds)?$request->resultIds:array();
		  $culture_ids = isset($request->cultureIDs)?$request->cultureIDs:array();
		  $template_ids = isset($request->templateIds)?$request->templateIds:array();
		  
		  $user_num = auth()->user()->id;
		  $order_id = $request->order_id;
		  $msg = '';
		  //reset all to unvalidated 
		  $exist1 = LabResults::where('order_id',$order_id)->where('active','Y')->exists();
		  if($exist1){
		   LabResults::where('order_id',$order_id)->where('active','Y')->where('need_validation','<>','Y')->update(['need_validation'=>'Y','user_num'=>$user_num]);
		  }
		  
		  $exist2 = DB::table('tbl_order_culture_results')->where('order_id',$order_id)->where('active','Y')->exists();
		  if($exist2){
				 DB::table('tbl_order_culture_results')->where('order_id',$order_id)->where('active','Y')->where('need_validation','<>','Y')->update(['need_validation'=>'Y','user_num'=>$user_num]);
                 }
		  $exist3 = DB::table('tbl_visits_order_template_reports')->where('order_id',$order_id)->where('active','Y')->exists();
		  if($exist3){
				 DB::table('tbl_visits_order_template_reports')->where('order_id',$order_id)->where('active','Y')->where('need_validation','<>','Y')->update(['need_validation'=>'Y','user_num'=>$user_num]);
                 }
		  
		  if(count($result_ids)>0 || count($culture_ids)>0 || count($template_ids)>0 ){
		    $msg = __('Choosen Tests are validated successfully');
		    //update need validation to done
		    $cnt_valid_rslts=$cnt_valid_cultures=$cnt_valid_templates=0;
			if(count($result_ids)>0){
			LabResults::where('order_id',$order_id)->whereIn('id',$result_ids)->update(['need_validation'=>'N','user_num'=>$user_num]);
			$cnt_valid_rslts = LabResults::where('order_id',$order_id)->where('active','Y')->where('need_validation','Y')->count();			
			}
			
			if(count($culture_ids)>0){
			DB::table('tbl_order_culture_results')->where('order_id',$order_id)->whereIn('id',$culture_ids)->update(['need_validation'=>'N','user_num'=>$user_num]);
		    $cnt_valid_cultures = DB::table('tbl_order_culture_results')->where('order_id',$order_id)->where('active','Y')->where('need_validation','Y')->count();			
			}
			
			if(count($template_ids)>0){
			DB::table('tbl_visits_order_template_reports')->where('order_id',$order_id)->whereIn('id',$template_ids)->update(['need_validation'=>'N','user_num'=>$user_num]);
		    $cnt_valid_templates = DB::table('tbl_visits_order_template_reports')->where('order_id',$order_id)->where('active','Y')->where('need_validation','Y')->count();			
			}
			//check if all test are validated then send email and sms
		    $cnt_need_validation = $cnt_valid_rslts+$cnt_valid_templates+$cnt_valid_cultures;

			if($cnt_need_validation==0){
				 //send email and sms
			    //begin auto sending email and sms
			    $order = LabOrders::find($order_id);   
		        $lab = Clinic::find($order->clinic_num);
	            $patient = Patient::find($order->patient_num);
		        $ext_lab = ExtLab::find($order->ext_lab);
	            $doc = Doctor::find($order->doctor_num);
				$report_datetime = LabOrders::where('id',$order_id)->value('report_datetime');
		  	    $pat_name = $patient->first_name;
				if(isset($patient->middle_name) && $patient->middle_name!=''){$pat_name.=' '.$patient->middle_name;}
				$pat_name.=' '.$patient->last_name;
	   			$doc_name = '';
				if(isset($doc)){
				 $doc_name = $doc->first_name;
				 if(isset($doc->middle_name) && $doc->middle_name!=''){$doc_name.=' '.$doc->middle_name;}
				 $doc_name.=' '.$doc->last_name;
				}
			    $sms_body=$email_body=$email_subject='';
			    //get clinic SMS (body) and Email (subject , body)
		 	    $sms_body = $this->GenerateSMS($lang,$lab,$order,$pat_name,$doc_name);
			    $email_body = $this->GenerateEMAIL($lab,$order,$pat_name,$doc_name,'body');
			    $email_subject = $this->GenerateEMAIL($lab,$order,$pat_name,$doc_name,'subject');
			    $lab_name = $lab->full_name;
	            $lab_email = $lab->email;
	            //prepare email parts
	            $pat_email = isset($patient->email)?$patient->email:'';
			    $pat_tel = isset($patient->cell_phone)?$patient->cell_phone:'';
			    $doc_email = isset($doc) && isset($doc->email)?$doc->email:'';
			    $doc_tel = isset($doc) && isset($doc->tel)?$doc->tel:'';
			    $guarantor_email = isset($ext_lab) && isset($ext_lab->email)?$ext_lab->email:'';
			    $guarantor_tel = isset($ext_lab) && isset($ext_lab->telephone)?$ext_lab->telephone:'';
	            $email_success = $error=$sms_success=$pdf='';
		       //prepare email body
	            $sms_body  = $this->escape_newline($sms_body);
	            $sms_body = str_replace("*ResultsPDF*","*DownloadLink*",$sms_body);
	            $sms_body=str_replace('*BR*',PHP_EOL,$sms_body);
			   
			    $emails = array();
	            $to_patient = $to_doctor = $to_guarantor = 'N';
			    $status_id = '';
				
				if(isset($pat_email) && $pat_email!=''){
				  array_push($emails,$pat_email);
				  $to_patient = 'Y';
				}
			    
				if(isset($doc_email) && $doc_email!=''){
				 array_push($emails,$doc_email);
				 $to_doctor = 'Y';
				}
	
			   if(isset($guarantor_email) && $guarantor_email!=''){
				 array_push($emails,$guarantor_email);
				 $to_guarantor = 'Y';
				}
	            
			   //check if pdf exists then get it else generate it	
		  	   if(isset($order->pdf_path) && $order->pdf_path!=''){ 
			      $path = storage_path('app/7mJ~33/'.$order->pdf_path);
				  $pdf = file_get_contents($path); 
			   }else{
				  $path = storage_path('app/7mJ~33/'.$order->pdf_path);
				  $pdf = file_get_contents($path);  
			   }
			   
			   //only send emails if there is emails	
               if(count($emails)>0){	
		  		  
	              $result = $this->sendResultsByEmail($order_id,$lab_name,$lab_email,$emails,$email_subject,$email_body,$pdf);
			      if($result){
				     $email_success.='<div>'.__("Emails sent succssfully to")." : ".implode(',',$emails).'</div>';
				     $now_time = Carbon::now()->format("Y-m-d H:i");
					 
					 DB::table('tbl_visits_order_sent_email_history')->insert([
								  'order_id'=>$order_id,
								  'to_patient'=>$to_patient,
								  'to_doctor'=>$to_doctor,
								  'to_guarantor'=>$to_guarantor,
								  'email_date'=>$now_time,
								  'user_num'=>$user_num
								]);
							
			      }else{
                       $now_time = Carbon::now()->format("d/m/Y H:i");
		               $error.='<div>'.__("Emails failed to be sent succssfully on").' : '.$now_time.'</div>';			
			        }	
		        }
				//check sms pack to send sms
				  $chk_pack = $this->check_sms_pack($order->clinic_num);
				  //reset send data
		         $to_patient = $to_doctor = $to_guarantor = $to_others = 'N';
					if($chk_pack){
						//pass tels to send SMS text messages for them
						$tels = array();
						$types = array();
						if(isset($pat_tel) && $pat_tel!=''){
						   $pat_tel=str_replace("-","",$pat_tel);
						   $pat_tel=str_replace(" ","",$pat_tel);
						   array_push($tels,$pat_tel);
						   $to_patient='Y';
						  }
						if(isset($doc_tel) && $doc_tel!=''){
						   $doc_tel=str_replace("-","",$doc_tel);
						   $doc_tel=str_replace(" ","",$doc_tel);
						   array_push($tels,$doc_tel);
						   $to_doctor='Y';
						  }
						if(isset($guarantor_tel) && $guarantor_tel!=''){
						   $guarantor_tel=str_replace("-","",$guarantor_tel);
						   $guarantor_tel=str_replace(" ","",$guarantor_tel);
						   array_push($tels,$guarantor_tel);
						   $to_guarantor='Y';
						  }   
						
							//only send sms if there is tels
						if(count($tels)>0){	
						  $result = $this->sendResultsBySMS($lang,$order_id,$tels,$sms_body,$types);
						  if($result){
								$sms_success.='<div>'.__("SMS sent succssfully to")." : ".implode(',',$tels).'</div>';
								$now_time = Carbon::now()->format("Y-m-d H:i");
								DB::table('tbl_visits_order_sent_sms_history')->insert([
									  'order_id'=>$order_id,
									  'to_patient'=>$to_patient,
									  'to_doctor'=>$to_doctor,
									  'to_guarantor'=>$to_guarantor,
									  'sms_date'=>$now_time,
									  'user_num'=>$user_num
									]);
								
							}else{
								$now_time = Carbon::now()->format("d/m/Y H:i");
								$error.='<div>'.__("SMS failed to be sent succssfully on").' : '.$now_time.'</div>';	
							}
						}
					
					
					}else{
						$error.='<div>'.__("No SMS units are left to send text messages!").'</div>';
					}
	
	         
				  if($error!=''){
					  DB::table('tbl_visits_order_sent_fail')->insert([
					  'order_id'=>$id,
					  'user_num'=>$user_num,
					  'error_msg'=>$error
					  ]);
				  }
	             //update status of messages
	             LabOrders::where('id',$order_id)->update(['status'=>'V','user_num'=>$user_num]);
	             //only update if there is no report datetime 
			      if($report_datetime==NULL || $report_datetime==''){
				    LabOrders::where('id',$order_id)->update(['report_datetime'=>Carbon::now()->format('Y-m-d H:i')]);
			       }
	          
			     $msg ='<div>'.__('ALL Tests are validated succssfully').'</div>';
			     $msg.=$email_success.$sms_success;
			     $sms_pack = $this->get_sms_pack($order->clinic_num);
				 $all_valid = 'Y';
			     return response()->json(['all_valid'=>$all_valid,'msg'=>$msg,'sms_pack'=>$sms_pack]);
			   }else{
				 $all_valid = 'N';
				 //just to change icon in results table
				 $done_resultids = LabResults::where('order_id',$order_id)->where('active','Y')->where('need_validation','<>','Y')->pluck('id')->toArray();
				 $undone_resultids = LabResults::where('order_id',$order_id)->where('active','Y')->where('need_validation','Y')->pluck('id')->toArray();
				 $exist_cult = DB::table('tbl_order_culture_results')->where('order_id',$order_id)->where('active','Y')->exists();
				 $done_cultids = [];
				 $undone_cultids = [];
				 if($exist_cult){
				  $done_cultids = DB::table('tbl_order_culture_results')->where('order_id',$order_id)->where('active','Y')->where('need_validation','<>','Y')->pluck('id')->toArray();
				  $undone_cultids = DB::table('tbl_order_culture_results')->where('order_id',$order_id)->where('active','Y')->where('need_validation','Y')->pluck('id')->toArray();
 				 }
				 return response()->json(['all_valid'=>$all_valid,'msg'=>$msg,
				                          'done_resultids'=>$done_resultids,'done_cultids'=>$done_cultids,
										  'undone_resultids'=>$undone_resultids,'undone_cultids'=>$undone_cultids]); 
			   }
		        
			  }else{
				 $all_valid = 'A';
				 //just to change icon in results table
				 $done_resultids = LabResults::where('order_id',$order_id)->where('active','Y')->where('need_validation','Y')->pluck('id')->toArray();
				 $exist_cult = DB::table('tbl_order_culture_results')->where('order_id',$order_id)->where('active','Y')->exists();
				 $done_cultids = [];
				 if($exist_cult){
				  $done_cultids = DB::table('tbl_order_culture_results')->where('order_id',$order_id)->where('active','Y')->where('need_validation','Y')->pluck('id')->toArray();
				 }
				 $msg = __('ALL Tests are unvalidated succssfully');
				 return response()->json(['all_valid'=>$all_valid,'msg'=>$msg,'done_resultids'=>$done_resultids,'done_cultids'=>$done_cultids]); 
			  }
		  
		break;
		
	}
	
}

//added
public function newValidation($lang,Request $request){
	//dd("HI");
	$req_type = $request->req_type;
	$user_num = auth()->user()->id;
	switch($req_type){
		case 'OneTST':
			  $id = $request->id;
			  $type = $request->type;
			  $need_validation = $request->need_validation;
			  switch($type){
				  case 'result':
				   $done_upd = LabResults::where('id',$id)->where('need_validation','<>',$need_validation)->update(['need_validation'=>$need_validation,'user_num'=>$user_num]);
				  break;
				  case 'culture':
				   $done_upd = DB::table('tbl_order_culture_results')->where('id',$id)->where('need_validation','<>',$need_validation)->update(['need_validation'=>$need_validation,'user_num'=>$user_num]);
				  break;
				  case 'template':
				  $done_upd = DB::table('tbl_visits_order_template_reports')->where('id',$id)->where('need_validation','<>',$need_validation)->update(['need_validation'=>$need_validation,'user_num'=>$user_num]);
				  break;
			  }
			 
			 if($done_upd >0){
			 $msg = $need_validation=='Y'?__('Test invalidated successfully'):__('Test validated successfully');
			   return response()->json(['success'=>true,'msg'=>$msg]);
			 }else{
				$msg = __('An error has occured during validation!');
				return response()->json(['success'=>false,'msg'=>$msg]); 
			 }
		break;
		case 'AllTSTs':
		        
			    $order_id = $request->order_id;
			  
			  //begin auto sending email and sms
			    $order = LabOrders::find($order_id);   
		        $lab = Clinic::find($order->clinic_num);
	            $patient = Patient::find($order->patient_num);
		        $ext_lab = ExtLab::find($order->ext_lab);
	            $doc = Doctor::find($order->doctor_num);
				$pat_name = $patient->first_name;
				if(isset($patient->middle_name) && $patient->middle_name!=''){$pat_name.=' '.$patient->middle_name;}
				$pat_name.=' '.$patient->last_name;
	   			$doc_name = '';
				if(isset($doc)){
				 $doc_name = $doc->first_name;
				 if(isset($doc->middle_name) && $doc->middle_name!=''){$doc_name.=' '.$doc->middle_name;}
				 $doc_name.=' '.$doc->last_name;
				}
			    $sms_body=$email_body=$email_subject='';
			    //get clinic SMS (body) and Email (subject , body)
		 	    $sms_body = $this->GenerateSMS($lang,$lab,$order,$pat_name,$doc_name);
			    $email_body = $this->GenerateEMAIL($lab,$order,$pat_name,$doc_name,'body');
			    $email_subject = $this->GenerateEMAIL($lab,$order,$pat_name,$doc_name,'subject');
			    $lab_name = $lab->full_name;
	            $lab_email = $lab->email;
	            //prepare email parts
	            $pat_email = isset($patient->email)?$patient->email:'';
			    $pat_tel = isset($patient->cell_phone)?$patient->cell_phone:'';
			    $doc_email = isset($doc) && isset($doc->email)?$doc->email:'';
			    $doc_tel = isset($doc) && isset($doc->tel)?$doc->tel:'';
			    $guarantor_email = isset($ext_lab) && isset($ext_lab->email)?$ext_lab->email:'';
			    $guarantor_tel = isset($ext_lab) && isset($ext_lab->telephone)?$ext_lab->telephone:'';
	            $email_success = $error=$sms_success=$pdf='';
		        //prepare email body
	            $sms_body  = $this->escape_newline($sms_body);
	            $sms_body = str_replace("*ResultsPDF*","*DownloadLink*",$sms_body);
	            $sms_body=str_replace('*BR*',PHP_EOL,$sms_body);
			   
			    $emails = array();
	            $to_patient = $to_doctor = $to_guarantor = 'N';
			    
				if(isset($pat_email) && $pat_email!=''){
				  array_push($emails,$pat_email);
				  $to_patient = 'Y';
				}
			    
				if(isset($doc_email) && $doc_email!=''){
				 array_push($emails,$doc_email);
				 $to_doctor = 'Y';
				}
	
			   if(isset($guarantor_email) && $guarantor_email!=''){
				 array_push($emails,$guarantor_email);
				 $to_guarantor = 'Y';
				}
	            
			   
			   
			   //only send emails if there is emails
			   $error = ''; 
			   $cnt_email_errors=0;
			   $cnt_sms_errors=0;
               if(count($emails)>0){	
		  		   if($request->has('id')){
							$id = $request->id;
							$type = $request->type;
							
							switch($type){
							  case 'result':
							   LabResults::where('id',$id)->update(['need_validation'=>'N','user_num'=>$user_num]);
							  break;
							  case 'culture':
							   DB::table('tbl_order_culture_results')->where('id',$id)->update(['need_validation'=>'N','user_num'=>$user_num]);
							  break;
							  case 'template':
							  DB::table('tbl_visits_order_template_reports')->where('id',$id)->update(['need_validation'=>'N','user_num'=>$user_num]);
							  break;
							}
					   }else{
							 //check ALL to validated i.e. no need for validation
							 LabResults::where('active','Y')->where('order_id',$order_id)->where('need_validation','Y')->update(['need_validation'=>'N','user_num'=>$user_num]);
							 $exists_culture = DB::table('tbl_order_culture_results')->where('active','Y')->where('order_id',$order_id)->exists();
							 if($exists_culture){
								DB::table('tbl_order_culture_results')->where('active','Y')->where('order_id',$order_id)->where('need_validation','Y')->update(['need_validation'=>'N','user_num'=>$user_num]); 
							 }
							 $exists_tmplate = DB::table('tbl_visits_order_template_reports')->where('active','Y')->where('order_id',$order_id)->exists();
							 if($exists_tmplate){
								DB::table('tbl_visits_order_template_reports')->where('active','Y')->where('order_id',$order_id)->where('need_validation','Y')->update(['need_validation'=>'N','user_num'=>$user_num]); 
							 }
						 }
				  //check if pdf exists then get it else generate it	
				   if(isset($order->pdf_path) && $order->pdf_path!=''){ 
					  $path = storage_path('app/7mJ~33/'.$order->pdf_path);
					  $pdf = file_get_contents($path); 
				   }else{
					  $path = $this->getPDFPath($lang,$order_id);
					  $pdf = file_get_contents($path);  
				   }
				  $result = $this->sendResultsByEmail($order_id,$lab_name,$lab_email,$emails,$email_subject,$email_body,$pdf);
			      if($result){
				     $email_success.='<div>'.__("Emails sent succssfully to")." : ".implode(',',$emails).'</div>';
				     $now_time = Carbon::now()->format("Y-m-d H:i");
					 
					 DB::table('tbl_visits_order_sent_email_history')->insert([
								  'order_id'=>$order_id,
								  'to_patient'=>$to_patient,
								  'to_doctor'=>$to_doctor,
								  'to_guarantor'=>$to_guarantor,
								  'email_date'=>$now_time,
								  'user_num'=>$user_num
								]);
							
			      }else{
                       $now_time = Carbon::now()->format("d/m/Y H:i");
		               $error.='<div>'.__("Emails failed to be sent succssfully on").' : '.$now_time.'</div>';
					   $cnt_email_errors++;
					  }	
		        }else{
					   $error.='<div>'.__("No email is provided for either Patient/Guarantor/Doctor").'</div>';
					   $cnt_email_errors++;
				}
				
				//if error occured in email then do not send it in sms
				if($cnt_email_errors==0){
					//check sms pack to send sms
					  $chk_pack = $this->check_sms_pack($order->clinic_num);
					  //reset send data
		              $to_patient = $to_doctor = $to_guarantor = $to_others = 'N';
					  if($chk_pack){
						//pass tels to send SMS text messages for them
						$tels = array();
						$types = array();
						if(isset($pat_tel) && $pat_tel!=''){
						   $pat_tel=str_replace("-","",$pat_tel);
						   $pat_tel=str_replace(" ","",$pat_tel);
						   array_push($tels,$pat_tel);
						   $to_patient='Y';
						  }
						if(isset($doc_tel) && $doc_tel!=''){
						   $doc_tel=str_replace("-","",$doc_tel);
						   $doc_tel=str_replace(" ","",$doc_tel);
						   array_push($tels,$doc_tel);
						   $to_doctor='Y';
						  }
						if(isset($guarantor_tel) && $guarantor_tel!=''){
						   $guarantor_tel=str_replace("-","",$guarantor_tel);
						   $guarantor_tel=str_replace(" ","",$guarantor_tel);
						   array_push($tels,$guarantor_tel);
						   $to_guarantor='Y';
						  }   
						
							//only send sms if there is tels
						if(count($tels)>0){	
						  $result = $this->sendResultsBySMS($lang,$order_id,$tels,$sms_body,$types);
						  if($result){
								$sms_success.='<div>'.__("SMS sent succssfully to")." : ".implode(',',$tels).'</div>';
								$now_time = Carbon::now()->format("Y-m-d H:i");
								DB::table('tbl_visits_order_sent_sms_history')->insert([
									  'order_id'=>$order_id,
									  'to_patient'=>$to_patient,
									  'to_doctor'=>$to_doctor,
									  'to_guarantor'=>$to_guarantor,
									  'sms_date'=>$now_time,
									  'user_num'=>$user_num
									]);
								
							}else{
								$now_time = Carbon::now()->format("d/m/Y H:i");
								$error.='<div>'.__("SMS failed to be sent succssfully on").' : '.$now_time.'</div>';
								$cnt_sms_errors++;
							}
						}else{
							$error.='<div>'.__("No telephone is provided for either Patient/Guarantor/Doctor").'</div>';
					        $cnt_sms_errors++;
						}
					
					
					}else{
						$error.='<div>'.__("No SMS units are left to send text messages!").'</div>';
						$cnt_sms_errors++;
					}
				   }
	         
				  if($error!=''){
					  
					  DB::table('tbl_visits_order_sent_fail')->insert([
					  'order_id'=>$order_id,
					  'user_num'=>$user_num,
					  'error_msg'=>$error
					  ]);
					  
				  }
				  
				  if($cnt_email_errors!=0 && $cnt_sms_errors!=0){
				   //if error occurs in sending both email and sms then do not change state keep it as finished
				    //check ALL to invalidated i.e.  need for validation
					LabResults::where('active','Y')->where('order_id',$order_id)->where('need_validation','N')->update(['need_validation'=>'Y','user_num'=>$user_num]);
					$exists_culture = DB::table('tbl_order_culture_results')->where('active','Y')->where('order_id',$order_id)->exists();
					if($exists_culture){
						DB::table('tbl_order_culture_results')->where('active','Y')->where('order_id',$order_id)->where('need_validation','N')->update(['need_validation'=>'Y','user_num'=>$user_num]); 
					 }
					$exists_tmplate = DB::table('tbl_visits_order_template_reports')->where('active','Y')->where('order_id',$order_id)->exists();
					if($exists_tmplate){
					    DB::table('tbl_visits_order_template_reports')->where('active','Y')->where('order_id',$order_id)->where('need_validation','N')->update(['need_validation'=>'Y','user_num'=>$user_num]); 
					 }
				   //return result ids that are not validated 
				   $msg =__('Error!,No data is sent by either Email or SMS');
				   return response()->json(['success'=>false,'msg'=>$msg]);
				  }else{
					    
				        //update status of messages
	                    LabOrders::where('id',$order_id)->update(['status'=>'V','user_num'=>$user_num]);
	                    //only update if there is no report datetime 
			            $report_datetime = LabOrders::where('id',$order_id)->value('report_datetime');
					    if($report_datetime==NULL || $report_datetime==''){
				         LabOrders::where('id',$order_id)->update(['report_datetime'=>Carbon::now()->format('Y-m-d H:i')]);
			            }
	                   $msg ='<div>'.__('ALL Tests are validated succssfully').'</div>';
			           $msg.=$email_success.$sms_success;
			           $sms_pack = $this->get_sms_pack($order->clinic_num);
				       return response()->json(['success'=>true,'msg'=>$msg,'sms_pack'=>$sms_pack]);
				     
				  }
	            
		break;
	}
}


public function destroy($lang,Request $request)
    {
	  $id = $request->id;
	  $user_num = auth()->user()->id;
	 Switch($request->type){
	   case 'inactivate':
		//destroy order with all its results
		LabOrders::where('id',$id)->update(['active'=>'N','user_num'=>$user_num]);
		TblBillHead::where('order_id',$id)->update(['status'=>'N','user_num'=>$user_num]);
		$msg= __('Cancelled Successfully');
		break;
	 case 'activate':
	  	 //activate order with all its results
		 LabOrders::where('id',$id)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
		 TblBillHead::where('order_id',$id)->update(['status'=>'O','user_num'=>$user_num]);
		 $msg= __('Activated Successfully');
	    break;
	 }
	return response()->json(["msg"=>$msg]);

	}	
	
public function fillPatientDatalab($lang, Request $request)
     {
    
        $id= $request->id ;
        
        $Patient_Data = Patient::where('id',$id)->first();
      
	  	  
	  return response()->json(['patient'=>$Patient_Data]);

     }

public function printOrder($lang,Request $request){
	$order = LabOrders::find($request->order_id);
	$patient = Patient::find($order->patient_num);
	$doctor = Doctor::where('doctor_user_num',$order->ext_lab)->first();
	$ext_lab = ExtLab::where('id',$order->ext_lab)->first();
    
	$groups = DB::table('tbl_visits_order_custom_tests')->where('active','Y')->where('order_id',$order->id)->pluck('test_id')->toArray();

		
	$arr = array();
	//add group with tests
	foreach($groups as $tst_id){
		$grp = LabTests::find($tst_id);
		$category = DB::table('tbl_lab_categories')->where('id',$grp->category_num)->value('descrip');
		if($grp->is_group=='Y'){
			$tests = LabTests::where('group_num',$grp->id)->pluck('test_name')->toArray();
			$arr[$category][$grp->test_name]=$tests;
		}else{
			$arr[$category][$grp->test_name]='NG';
		}
	}			  
    
	$data = ['patient'=>$patient,'doctor'=>$doctor,'ext_lab'=>$ext_lab,'order'=>$order,'arr'=>$arr];
	$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
                       -> loadView('lab.visit.orders.OrderPDF', $data);
    $pdf->output();
    $dom_pdf = $pdf->getDomPDF();
	$canvas = $dom_pdf->get_canvas();
            
			//$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
            $canvas->page_text(50, 820, "Page {PAGE_NUM}".__('of')." {PAGE_COUNT}", null, 8, array(0, 0, 0)); // Bottom-left page number
            $canvas->page_text(500, 820, Carbon::now()->format('d/m/Y H:i'), null, 8, array(0, 0, 0)); // Bottom-right current datetime
			  
//dd($arr);
return $pdf->stream();
}
	
private function getPDFPath($lang,$order_id){
	$type= 'pdf';
	$pdf = $this->getPDF($lang,$order_id,$type);
    //save PDF as file
    $request_nb=LabOrders::where('id',$order_id)->value('request_nb');
	$result_path = storage_path('app/7mJ~33/ResultPDF/'.$request_nb);
	if(!file_exists($result_path)){mkdir($result_path, 0775, true);}
	$files = glob($result_path.'/*'); 
	foreach($files as $file){ if(is_file($file)) unlink($file);} 
	$result_file= uniqid().'.pdf';
	$result_file_path = $result_path.'/'.$result_file;
	file_put_contents($result_file_path,$pdf);
	LabOrders::where('id',$order_id)->update(['pdf_path'=>'ResultPDF/'.$request_nb.'/'.$result_file]);
	$new_path = LabOrders::where('id',$order_id)->value('pdf_path');
	$pdfContent = storage_path('app/7mJ~33/'.$new_path);
    return $pdfContent;  	
}
public function printResults($lang,Request $request){
	$order_id = $request->order_id;
	$user_num = auth()->user()->id;
	$type= 'pdf';
	$order = LabOrders::find($request->order_id);
	
	if($order->status=='V' && isset($order->pdf_path) && $order->pdf_path!=''){
	    $pdfContent = storage_path('app/7mJ~33/'.$order->pdf_path); 
	}else{
		$pdf = $this->getPDF($lang,$order_id,$type);			
		//save PDF as file
		$result_path = storage_path('app/7mJ~33/ResultPDF/'.$order->request_nb);
		if(!file_exists($result_path)){mkdir($result_path, 0775, true);}
		$files = glob($result_path.'/*'); 
		foreach($files as $file){ if(is_file($file)) unlink($file);} 
		$result_file= uniqid().'.pdf';
		$result_file_path = $result_path.'/'.$result_file;
		file_put_contents($result_file_path,$pdf);
		LabOrders::where('id',$order->id)->update(['pdf_path'=>'ResultPDF/'.$order->request_nb.'/'.$result_file]);
		$new_path = LabOrders::where('id',$request->order_id)->value('pdf_path');
		$pdfContent = storage_path('app/7mJ~33/'.$new_path);  
	} 
	
	
	//do this only for guarantor users
	$user_type = auth()->user()->type;
	//guarantor User
	if($user_type==3){
	   $click_pdf = LabOrders::where('id',$order_id)->value('click_pdf');
	   if(isset($click_pdf) && $click_pdf!='Y'){
	    LabOrders::where('id',$order_id)->update(['click_pdf'=>'Y','user_num'=>$user_num]);
	    }
	 }
	
	
	$fileContent = file_get_contents($pdfContent);
	$mimeType =  mime_content_type($pdfContent);
	
	
	//return $pdfContent;
	    return response($fileContent)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . basename($pdfContent) . '"');
	
}

function getPDF($lang,$order_id,$type){
	        // Set maximum execution time to 300 seconds (5 minutes)
            set_time_limit(300);
			$order = LabOrders::find($order_id);
			$patient = Patient::find($order->patient_num);
			$doctor = Doctor::where('id',$order->doctor_num)->first();
			$ext_lab = ExtLab::where('id',$order->ext_lab)->first();
			$documents = DOCSResults::where('name','not like','%.pdf')->where('active','Y')->where('order_id',$order->id)->get();
			$logo=DB::table('tbl_bill_logo')->where('clinic_num',$order->clinic_num)->where('status','O')->first();
	        $general_sig = DB::table('tbl_clinics_signatures')->where('clinic_num',$order->clinic_num)->where(DB::raw('trim(type)'),'results')->where('active','Y')->first();
	       
		   $results = DB::table('tbl_visits_order_results as r')
	               ->select('r.id','t.test_name','r.ref_range','t.is_title',
				            DB::raw("IFNULL(g.test_name,'Other') as group_name"),
							DB::raw("IFNULL(g.descrip,'') as group_instruction"),
							DB::raw("IFNULL(g.clinical_remark,'') as group_clinical_remark"),
							DB::raw("IFNULL(r.unit,'') as unit"),
							DB::raw("IFNULL(r.ref_range,'') as ref_range"),
							't.test_type','r.test_id','t.is_valid',
							DB::raw("IFNULL(t.category_num,0) as category_num"),
							DB::raw("IFNULL(r.result,'') as result"),
							DB::raw("IFNULL(r.result_txt,'') as result_txt"),
							DB::raw("IFNULL(r.sign,'') as sign"),
							't.testord as position',
							DB::raw("IFNULL(prev.result,'') as prev_result_val"),
							DB::raw("IFNULL(prev.order_id,'') as prev_order_id"),
							DB::raw("IFNULL(r.field_num,'') as field_num"),
							DB::raw("IFNULL(t.clinical_remark,'') as clinical_remark"),
							DB::raw("IFNULL(t.descrip,'') as method_instruction"),
							DB::raw("IFNULL(t.test_rq,'') as code_remark"),
							't.is_printed','r.calc_result','r.calc_unit',
							'g.is_printed as group_printed',
							'r.need_validation',
							DB::raw("IF(g.is_group='Y',g.testord,t.testord)  as group_order"),
							DB::raw("IFNULL(r.group_num,0) as group_num"),
							DB::raw("IFNULL(cat.testord,0) as cat_num"),
							'r.result_status'
							)
				   ->join('tbl_lab_tests as t',function($q){
					   $q->on('t.id','r.test_id');
					   $q->where('t.is_group','<>','Y');
					   $q->orWhereNULL('t.is_group');
				   })
				   ->join('tbl_visits_orders as o','o.id','r.order_id')
				   ->leftjoin('tbl_lab_tests as g',function($q){
				               $q->on('g.id','t.group_num');
							   $q->where('g.is_group','Y');
				          })
				  ->leftjoin('tbl_visits_order_results as prev',function($q){
				               $q->on('prev.id','r.prev_result_num');
							   $q->where('prev.active','Y');
				          })
			      ->join('tbl_lab_categories as cat','cat.id','t.category_num')
				  ->where('r.active','Y')
                  ->where('r.order_id',$order_id)
	              ->get();	
				
				
				$order_tests = DB::table('tbl_visits_order_custom_tests')->where('active','Y')->where('order_id',$order->id)->pluck('test_id')->toArray();	
				
			    $data_tests = implode(',', array_fill(0, count($order_tests), '?'));
				$query = "t.id IN ($data_tests) AND (t.is_culture <> 'Y' OR t.is_culture IS NULL)";
				
				$categories = DB::table('tbl_lab_tests as t')
			                  ->select(DB::raw("IFNULL(t.category_num,0) as id"),DB::raw("IFNULL(cat.descrip,'Other') as descrip"),'cat.testord')
							  ->leftjoin('tbl_lab_categories as cat','cat.id','t.category_num')
							  ->whereRaw($query, $order_tests)
							  ->orderBy('cat.testord')
							  ->groupBy('t.category_num')
							  ->get();
					//dd($categories);		  
			    
				   //$phlebotomy = collect();
				
				$custom_reports_tests  = array();
				$custom_reports = DB::table('tbl_visits_order_template_reports as r')
				                  ->select('r.test_id','r.need_validation','t.test_name','r.id','r.description','cat.descrip as cat_name')
								  ->join('tbl_lab_tests as t','t.id','r.test_id')
								  ->leftjoin('tbl_lab_categories as cat','cat.id','t.category_num')						 
								  ->where('r.active','Y')->where('r.order_id',$order_id)->orderBy('t.testord')->get();   
				
				$culture_data = DB::table('tbl_order_culture_results as c')
	           ->select('c.id as culture_id','c.need_validation','t.test_name','c.clinic_num','c.order_id','c.patient_num','c.gram_staim','c.culture_result')
			   ->join('tbl_lab_tests as t','t.id','c.test_id')
			   ->where('c.order_id',$order_id)->where('c.active','Y')->get();
	
				$culture_ids = DB::table('tbl_order_culture_results')->where('order_id',$order_id)->where('active','Y')->pluck('id')->toArray();
				
				$culture_details = DB::table('tbl_order_culture_results_detail as det')
						   ->select('det.bacteria_id','det.culture_id','bact.descrip as bacteria_name','ant.descrip as antibiotic_name','det.result')
						   ->join('tbl_lab_sbacteria as bact','bact.id','det.bacteria_id')
						   ->join('tbl_lab_antibiotic as ant','ant.id','det.antibiotic_id')
						   ->where('det.active','Y')
						   ->whereIn('det.culture_id',$culture_ids)
						   ->get();
				
				$bacteria_ids = array();
				foreach($culture_details as $d){
					if(!in_array($d->bacteria_id,$bacteria_ids)){
						array_push($bacteria_ids,$d->bacteria_id);
					}
				}
	
	               $bacteria = DB::table('tbl_lab_sbacteria')->whereIn('id',$bacteria_ids)->get(); 
				  
				    $branch = Clinic::find($order->clinic_num);
					$tel = isset($branch->telephone) && $branch->telephone!=''?'Tel: '.$branch->telephone.' , ':'';
					$whatsapp = isset($branch->whatsapp) && $branch->whatsapp!=''?'Whatsapp: '.$branch->whatsapp.' , ':'';
					$website = isset($branch->website) && $branch->website!=''?' '.$branch->website.' , ':'';
			        $address = isset($branch->full_address) && $branch->full_address!=''?'Address: '.$branch->full_address:'';
			        $branch_data = $tel.$whatsapp.$website.$address;               
			
            if($custom_reports->count()>0){
				$custom_reports_tests = $custom_reports->pluck('test_id')->toArray(); 
			}
						
			$cnt_covid_pcr = LabTests::whereIn('id',$order_tests)->where(DB::raw('trim(lower(test_name))'),'LIKE','covid%')->get()->count();		
			//dd($cnt_covid_pcr);
			if($cnt_covid_pcr>0){
			$pdf_data_qrcode = [
			             'patient'=>$patient,'lab'=>$branch,
			             'doctor'=>$doctor,'ext_lab'=>$ext_lab,'order'=>$order,
					     'results'=>$results,'categories'=>$categories,
					     'documents'=>$documents,'branch_data'=>$branch_data,
					     'culture_data'=>$culture_data,'culture_details'=>$culture_details,
					     'bacteria'=>$bacteria,'custom_reports_tests'=>$custom_reports_tests,
					     'logo'=>$logo,'general_sig'=>$general_sig
					   ]; 
           
			$pdf_qrcode = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true,'isPHPEnabled'=>true])
                      -> loadView('lab.visit.ResultsPDF', $pdf_data_qrcode);
			$pdfContent = $pdf_qrcode->output();		  
            $tmp_dir = storage_path('app/7mJ~33/fqrcde');
			$folderName = Str::random(10);
			$tmp_path = $tmp_dir.'/'.$folderName;
			if(!file_exists($tmp_path)){mkdir($tmp_path,0775,true); }
			$filename = uniqid() . '.pdf';
			$filepath = $tmp_path.'/'.$filename;
			file_put_contents($filepath,$pdfContent);
			$send_path = 'fqrcde/'.$folderName.'/'.$filename;
			
			$encryptedPath = Crypt::encrypt($send_path);
			
			$tempUrl = URL::temporarySignedRoute(
                        'qrCode.getFile', 
                        now()->addHours(96), 
                        ['encryptedPath' => $encryptedPath] 
                        ); 
			
			
			$qrCode = QrCode::size(100)->generate($tempUrl, storage_path('app/qr-codes/temporary_url.png'));
			$qrCodeImage =  str_replace(config('app.src_url'),config('app.url'),storage_path('app/qr-codes/temporary_url.png'));
			}else{
				$qrCodeImage=NULL;
			}
			$pdf_data = [
			             'patient'=>$patient,'lab'=>$branch,
			             'doctor'=>$doctor,'ext_lab'=>$ext_lab,'order'=>$order,
					     'results'=>$results,'categories'=>$categories,
					     'documents'=>$documents,'branch_data'=>$branch_data,
					     'culture_data'=>$culture_data,'culture_details'=>$culture_details,
					     'bacteria'=>$bacteria,'custom_reports_tests'=>$custom_reports_tests,
					     'logo'=>$logo,'general_sig'=>$general_sig,'qrCodeImage'=>$qrCodeImage
					   ]; 
           
			$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true,'isPHPEnabled'=>true])
                      -> loadView('lab.visit.ResultsPDF', $pdf_data);
			
			
			$pdf_docs = DOCSResults::where('active','Y')->where('order_id',$order->id)->where('name','like','%.pdf')->get();
						
			if($pdf_docs->count()>0 || $custom_reports->count()>0){
				$pdf_merge = PDFMerger::init();
				
				$path = storage_path('app/7mJ~33/resultdocs/tmp');
					if (!file_exists($path)) {
							mkdir($path, 0775, true);
					}
				$name=date("Y-m-d")."_".uniqid() . ".pdf";
				$pdf_file = $path.'/'.$name;
				file_put_contents($pdf_file, $pdf->output());
				$pdf_merge->addPDF( $pdf_file, 'all');
				
				
				foreach($custom_reports as $cr){
				 if($cr->need_validation<>'Y'){
				  $pdf_name = 'template_'.uniqid().'.pdf';
				  $pdfFilePath = $path.'/'.$pdf_name;
                  $descrip = $cr->description;
	              $descrip = str_replace('&nbsp;',' ',$descrip);
				  $pdf_data1 = ['patient'=>$patient,'lab'=>$branch,'doctor'=>$doctor,'ext_lab'=>$ext_lab,'order'=>$order,'branch_data'=>$branch_data,'template'=>$cr,'logo'=>$logo,'descrip'=>$descrip];
                  $htmlContent = view('lab.visit.templatePDF',$pdf_data1)->render();
                  $dompdf1 = new Dompdf();
                  $dompdf1->loadHtml($htmlContent);
                  $options = new Options();
				  $options->set('defaultFont', 'sans-serif');
				  $options->set('isHtml5ParserEnabled', true);
				  $options->set('isRemoteEnabled', true);
				  $options->set('isJavascriptEnabled', true);
				  $options->set('isPhpEnabled', true);
				  $dompdf1->setOptions($options);
				  $dompdf1->setPaper('A4', 'portrait');
				  $dompdf1->render();
				  $pdfData = $dompdf1->output();
				  file_put_contents($pdfFilePath,$pdfData);
                  $pdf_merge->addPDF($pdfFilePath, 'all');
				 }
				}
				
				foreach($pdf_docs as $d){
						$pdf_path = storage_path('app/7mJ~33/'.$d->path);
						$pdf_merge->addPDF($pdf_path, 'all');
					}
				
				$pdf_merge->merge();
				//delete tmp existing files
				 $files = glob($path.'*'); // get all file names
					foreach($files as $file){ // iterate files
					  if(is_file($file)) {
						unlink($file); // delete file
					  }
					}
				
				return $pdf_merge->output();
				/*switch($type){
					case 'pdf':
					return $pdf_merge->stream();
					break;
					case 'email': case 'sms': case 'whatsapp':
					return $pdf_merge->output();
					break;
				}*/
			}else{
				return $pdf->output(); 
				 /*switch($type){
					case 'pdf':
					return $pdf->stream();
					break;
					case 'email': case 'sms': case 'whatsapp':
					return $pdf->output();
					break;
				}*/
			}
}


public function sendResults($lang,Request $request){
	
	$order_id = $request->order_id;
	$lab_num = LabOrders::where('id',$order_id)->value('clinic_num');
	$lab_name = Clinic::where('id',$lab_num)->value('full_name');
	$lab_email = Clinic::where('id',$lab_num)->value('email');
	//prepare email parts
	$pat_email = $request->pat_email;
	$doc_email = $request->doc_email;
	$guarantor_email = $request->guarantor_email;
	$other_emails = $request->other_emails;
	
	$pat_tel = $request->pat_tel;
	$doc_tel = $request->doc_tel;
	$guarantor_tel = $request->guarantor_tel;
	$other_phones = $request->other_phones;
	
	$email_subject = $request->email_subject;
	$email_body = $request->email_body;
	
	
	$email_success = $sms_success=$error_msg=$pdf='';
	
	
	$sms_body = $request->sms_body;
	//prepare email body
	$sms_body  = $this->escape_newline($sms_body);
	$sms_body=str_replace('*BR*',PHP_EOL,$sms_body);
	
    $emails = array();
	
	$user_num = auth()->user()->id;
	
	$status_id = '';
	$to_patient = $to_doctor = $to_guarantor = 'N';
	
	if(isset($pat_email) && $pat_email!=''){
		array_push($emails,$pat_email);
		$to_patient ='Y';
		
	}
	
	if(isset($doc_email) && $doc_email!=''){
		array_push($emails,$doc_email);
		$to_doctor='Y';
		
	}
	
	if(isset($guarantor_email) && $guarantor_email!=''){
		array_push($emails,$guarantor_email);
		$to_guarantor = 'Y';
		
	}
	
	if(isset($other_emails) && count($other_emails)>0){
		foreach($other_emails as $email){
		 array_push($emails,$email);	
		}
		
	}
	
	//check if pdf exists then get it else generate it	
	$pdfPATH = LabOrders::where('id',$order_id)->value('pdf_path');	  	  
			  if(isset($pdfPATH) && $pdfPATH!=''){ 
			      $path = storage_path('app/7mJ~33/'.$pdfPATH);
				  $pdf = file_get_contents($path);
			   }else{
				  $path = $this->getPDFPath($lang,$order_id);
				  $pdf = file_get_contents($path);
			   }
	
	//only send emails if there is emails	
	 if(count($emails)>0){	
		
		   $result = $this->sendResultsByEmail($order_id,$lab_name,$lab_email,$emails,$email_subject,$email_body,$pdf);
		if($result){
			$email_success.='<div>'.__("Emails sent succssfully to")." : ".implode(',',$emails).'</div>';
			$now_time = Carbon::now()->format("Y-m-d H:i");	
			DB::table('tbl_visits_order_sent_email_history')->insert([
				  'order_id'=>$order_id,
				  'to_patient'=>$to_patient,
				  'to_doctor'=>$to_doctor,
				  'to_guarantor'=>$to_guarantor,
				  'email_date'=>$now_time,
				  'user_num'=>$user_num
				]);
			
		}else{
			$now_time = Carbon::now()->format("d/m/Y H:i");
			$error_msg.='<div>'.__("Emails failed to be sent succssfully on").' : '.$now_time.'</div>';

		}
	 }	
	
	$order = LabOrders::find($request->order_id);
	//check sms pack to send sms
	$chk_pack = $this->check_sms_pack($order->clinic_num);
	//reset send data
	$to_patient = $to_doctor = $to_guarantor = $to_others = 'N';
	if($chk_pack){
	      
		 //pass tels to send SMS text messages for them
					$tels = array();
					$types = array();
					if(isset($pat_tel) && $pat_tel!=''){
					   $pat_tel=str_replace("-","",$pat_tel);
					   $pat_tel=str_replace(" ","",$pat_tel);
					   array_push($tels,$pat_tel);
					   $to_patient = 'Y';
					  }
					if(isset($doc_tel) && $doc_tel!=''){
					   $doc_tel=str_replace("-","",$doc_tel);
					   $doc_tel=str_replace(" ","",$doc_tel);
					   array_push($tels,$doc_tel);
					   $to_doctor = 'Y';
					  }
					if(isset($guarantor_tel) && $guarantor_tel!=''){
					   $guarantor_tel=str_replace("-","",$guarantor_tel);
					   $guarantor_tel=str_replace(" ","",$guarantor_tel);
					   array_push($tels,$guarantor_tel);
					   $to_guarantor = 'Y';
					  }
                    if(isset($other_phones) && count($other_phones)>0){
		  
			          foreach($other_phones as $phone){						  
				        $phone=str_replace("-","",$phone);
					    $phone=str_replace(" ","",$phone);
						array_push($tels,$phone);
					  }
					} 
					
				//only send sms if there is telephone numbers	
				if(count($tels)>0){	
					$result = $this->sendResultsBySMS($lang,$order_id,$tels,$sms_body);
					if($result){
							$sms_success.='<div>'.__("SMS sent succssfully to")." : ".implode(',',$tels).'</div>';
							$now_time = Carbon::now()->format("Y-m-d H:i");
							DB::table('tbl_visits_order_sent_sms_history')->insert([
								  'order_id'=>$order_id,
								  'to_patient'=>$to_patient,
								  'to_doctor'=>$to_doctor,
								  'to_guarantor'=>$to_guarantor,
								  'sms_date'=>$now_time,
								  'user_num'=>$user_num
								]);
							
						}else{
							$now_time = Carbon::now()->format("d/m/Y H:i");
		                    $error_msg.='<div>'.__("SMS failed to be sent succssfully on").' : '.$now_time.'</div>';
						}
				}	
	}else{
		$error_msg.='<div>'.__("No SMS units are left to send text messages!").'</div>';
	}
	
	
	 $user_num  = auth()->user()->id;
	 if($error_msg!=''){
		 DB::table('tbl_visits_order_sent_fail')->insert([
		 'order_id'=>$order_id,
		 'error_msg'=>$error_msg,
		 'user_num'=>$user_num
		 ]);
	 }
	 
	 
	$success = $email_success.$sms_success;
	$sms_pack = $this->get_sms_pack($order->clinic_num);
	
	return response()->json(['success'=>$success,'sms_pack'=>$sms_pack]);
	
}

private function get_sms_pack($lab_id){
	$clin_sms = ClinicSMSPack::where('active','O')->where('clinic_num',$lab_id)->first();
	if(isset($clin_sms)){
	  $fixed_pack = isset($clin_sms->current_sms_pack)?$clin_sms->current_sms_pack:0;
	  $pay_pack = isset($clin_sms->pay_pack)?$clin_sms->pay_pack:0;
		if($fixed_pack >0){
			$package = $fixed_pack;
		}else{
			if($pay_pack >0){
				$package = $pay_pack;
			}else{
				$package = 0;
			}
		}
	}else{
		$package = 0;
	}
	
	return $package;
}


private function check_sms_pack($lab_id){
	$clin_sms = ClinicSMSPack::where('active','O')->where('clinic_num',$lab_id)->first();
	if(isset($clin_sms)){
	  $fixed_pack = isset($clin_sms->current_sms_pack)?$clin_sms->current_sms_pack:0;
	  $pay_pack = isset($clin_sms->pay_pack)?$clin_sms->pay_pack:0;
		if($fixed_pack >0){
			$package = $fixed_pack;
		}else{
			if($pay_pack >0){
				$package = $pay_pack;
			}else{
				$package = 0;
			}
		}
	}else{
		$package = 0;
	}
	
if($package<=0){
	return false;
}else{
	return true;
}	
	
}

public function SMS_EMAIL($lang,Request $request){
	    $order = LabOrders::find($request->order_id);   
		$lab = Clinic::find($order->clinic_num);
	    $patient = Patient::find($order->patient_num);
		$ext_lab = ExtLab::find($request->ext_lab);
	    $doc = Doctor::find($request->doctor_num);
		
		$pat_name = $patient->first_name;
	    if(isset($patient->middle_name) && $patient->middle_name!=''){
		  $pat_name.=' '.$patient->middle_name;
	     }
	    $pat_name.=' '.$patient->last_name;
	   
	    $doc_name = '';
	    if(isset($doc)){
	     $doc_name = $doc->first_name;
	     if(isset($doc->middle_name) && $doc->middle_name!=''){
		   $doc_name.=' '.$doc->middle_name;
	      }
	     $doc_name.=' '.$doc->last_name;
	    }
		
		$sms_body=$email_body=$email_subject='';
		//get clinic SMS (body) and Email (subject , body)
		$sms_body = $this->GenerateSMS($lang,$lab,$order,$pat_name,$doc_name);
		$email_body = $this->GenerateEMAIL($lab,$order,$pat_name,$doc_name,'body');
		$email_subject = $this->GenerateEMAIL($lab,$order,$pat_name,$doc_name,'subject');
		$pat_email = $patient->email;
		$pat_tel = $patient->cell_phone;
		$doc_email = isset($doc) && isset($doc->email)?$doc->email:'';
		$doc_tel = isset($doc) && isset($doc->tel)?$doc->tel:'';
		$guarantor_email = isset($ext_lab) && isset($ext_lab->email)?$ext_lab->email:'';
		$guarantor_tel = isset($ext_lab) && isset($ext_lab->telephone)?$ext_lab->telephone:'';
		
return response()->json(['pat_tel'=>$pat_tel,'pat_email'=>$pat_email,'doc_email'=>$doc_email,'doc_tel'=>$doc_tel,
                         'guarantor_email'=>$guarantor_email,'guarantor_tel'=>$guarantor_tel,
						 'sms_body'=>$sms_body,'email_body'=>$email_body,'email_subject'=>$email_subject]);
}

private function GenerateSMS($lang,$lab,$order,$pat_name,$doc_name){
	
	
	$sms_body = $lab->sms_body;
	//$sms_body  = $this->escape_newline($sms_body);
	$sms_body = str_replace("*ResultsPDF*","*DownloadLink*",$sms_body);
	$sms_body = str_replace("*PatientName*",$pat_name,$sms_body);
	$sms_body = str_replace("*DoctorName*",$doc_name,$sms_body);
	$sms_body = str_replace("*LabName*",$lab->full_name,$sms_body);
	$sms_body = str_replace("*LabAddress*",$lab->full_address,$sms_body);
	$sms_body = str_replace("*LabPhone*",$lab->telephone,$sms_body);
	$sms_body = str_replace("*LabEmail*",$lab->email,$sms_body);
	$sms_body = str_replace("*LabWhatsapp*",$lab->whatsapp,$sms_body);
	$sms_body = str_replace("*LabSite*",$lab->website,$sms_body);
	$sms_body = str_replace("*RequestDate*",Carbon::parse($order->order_datetime)->format('d/m/Y'),$sms_body);
	$sms_body = str_replace("*RequestTime*",Carbon::parse($order->order_datetime)->format('H:i'),$sms_body);
	
	return $sms_body;
}

private function GenerateEMAIL($lab,$order,$pat_name,$doc_name,$type){
	
	switch($type){
		case 'subject':
			  $email_head = $lab->email_head;
			  //prepare email head 
			 $email_head = str_replace("*PatientName*",$pat_name,$email_head);
			 $email_head = str_replace("*DoctorName*",$doc_name,$email_head);
			 $email_head = str_replace("*LabName*",$lab->full_name,$email_head);
			 $email_head = str_replace("*LabAddress*",$lab->full_address,$email_head);
			 $email_head = str_replace("*LabPhone*",$lab->telephone,$email_head);
			 $email_head = str_replace("*LabEmail*",$lab->email,$email_head);
			 $email_head = str_replace("*LabWhatsapp*",$lab->whatsapp,$email_head);
			 $email_head = str_replace("*LabSite*",$lab->website,$email_head);
			 $email_head = str_replace("*RequestDate*",Carbon::parse($order->order_datetime)->format('d/m/Y'),$email_head);
			 $email_head = str_replace("*RequestTime*",Carbon::parse($order->order_datetime)->format('H:i'),$email_head);
		     return $email_head;
		break;
		case 'body':
		      $email_body = $lab->email_body;
    		 //prepare email body
			 $email_body  = $this->escape_newline($email_body);
			 $email_body = str_replace("*PatientName*",$pat_name,$email_body);
			 $email_body = str_replace("*DoctorName*",$doc_name,$email_body);
			 $email_body = str_replace("*LabName*",$lab->full_name,$email_body);
			 $email_body = str_replace("*LabAddress*",$lab->full_address,$email_body);
			 $email_body = str_replace("*LabPhone*",$lab->telephone,$email_body);
			 $email_body = str_replace("*LabEmail*",$lab->email,$email_body);
			 $email_body = str_replace("*LabWhatsapp*",$lab->whatsapp,$email_body);
			 $email_body = str_replace("*LabSite*",$lab->website,$email_body);
			 $email_body = str_replace("*RequestDate*",Carbon::parse($order->order_datetime)->format('d/m/Y'),$email_body);
			 $email_body = str_replace("*RequestTime*",Carbon::parse($order->order_datetime)->format('H:i'),$email_body);
			 $email_body=str_replace('*BR*','<br/>',$email_body);
			 return $email_body;
		break;
	}
	
	
	
	
}

//send results to patient/doctor/guarantor by sms
private function sendResultsBySMS($lang,$order_id,$tels,$sms){
	$user_num  = auth()->user()->id;	
	$order = LabOrders::find($order_id);
	$clin_sms = ClinicSMSPack::where('active','O')->where('clinic_num',$order->clinic_num)->first();
	  $tels = implode(';',$tels);
	  $unhashedValidator = bin2hex ( openssl_random_pseudo_bytes ( 50 ) );
	  $hashedValidator = hash ( 'sha256', $unhashedValidator );
	  $hashedValidator = str_replace("'","",$hashedValidator);
	  $hashedValidator = substr($hashedValidator,0,16);
	  $sms_link = route('open_sms_link',['locale'=>$lang,'key'=> $hashedValidator]);
	  $sms = str_replace("*DownloadLink*",$sms_link,$sms);
	  //dd($sms);
	  $sms_response= UserHelper::sendSMS($tels,$sms,$clin_sms->id);
	  	  
	  if($sms_response){
		DB::table('tbl_order_reminders_key')->where('order_id',$order_id)->update(['active'=>'N','user_num'=>$user_num]); 
        DB::table('tbl_order_reminders_key')->insert(['order_id'=>$order_id,'secret_key'=>$hashedValidator,'remind_by'=>'sms','active'=>'Y','user_num'=>$user_num]);
		//decrease sms messages and update database for new package
		$msg_size = strlen($sms);
		$fixed_pack = isset($clin_sms->current_sms_pack)?$clin_sms->current_sms_pack:0;
		$pay_pack = isset($clin_sms->pay_pack)?$clin_sms->pay_pack:0;
		if($fixed_pack >0){
			$package = $fixed_pack;
			//$count_msg = ceil($msg_size/160);
			$count_msg = isset($clin_sms->nb_spent_units)?intval($clin_sms->nb_spent_units):0;
			$package=$package-$count_msg;
			ClinicSMSPack::where('id',$clin_sms->id)->update(['old_sms_pack'=>$fixed_pack,'current_sms_pack'=>$package,'user_num'=>$user_num]);
			return true;
			}else{
				if($pay_pack >0){
					$package = $pay_pack;
				    //$count_msg = ceil($msg_size/160);
				    $count_msg = isset($clin_sms->nb_spent_units)?intval($clin_sms->nb_spent_units):0;
					$package=$package-$count_msg;
					ClinicSMSPack::where('id',$clin_sms->id)->update(['old_sms_pack'=>$pay_pack,'pay_pack'=>$package,'user_num'=>$user_num]);
					return true;
					}else{
						return false;
					}
			}
			
		
	    	
		}else{
			return false;
		}
	
	
}

//send results to patient/doctor by email 
private function sendResultsByEmail($order_id,$lab_name,$lab_email,$emails,$email_subject,$email_body,$pdf){
  	$user_num  = auth()->user()->id;
	$msg_rem=$email_body;
    $from = $lab_name;
	$reply_to_name = __("No reply").','.$lab_name;
	$reply_to_address = isset($lab_email) && $lab_email!=''? $lab_email:'noreply@email.com';
	$subject = $email_subject;
    $details = ['msg_rem'=>$msg_rem,'from'=>$from,'reply_to_name'=>$reply_to_name,'reply_to_address'=>$reply_to_address,'subject'=>$subject];	  
	$to  =  $emails;
	$visit_date = Carbon::now()->format('d/m/Y H:i');
	$pdf_name = 'Result-'.$visit_date.'.pdf';
	Mail::to($to)->send(new SettingMailAttach($details,$pdf,$pdf_name));
	if(Mail::failures()){
	  return false;
	}else{
		
	return true;
	}
	
	
}

public function uploadAttach($lang,Request $request)

    {
        
		$this->validate($request, [

				'files' => 'required',
				'files.*'=>'mimes:jpg,jpeg,png,gif,bmp,tiff,svg,webp,pdf|max:3072'
			  ],[
				 'files.required' => __('Please choose a document of type image/pdf'),
				 'files.*.mimes' => __('Please insert a document of type image/pdf'),
				 'files.*.max'   => __('Document should be less than 3 MB')
			
			]);
       
		$uid = auth()->user()->id;
		$order_id = $request->doc_order_id;
		
		$image_path = storage_path('app/7mJ~33/Documents');
		if (!file_exists($image_path)){
				mkdir($image_path, 0775, true);
			}
		
		if($request->hasfile('files')) {
			
			  foreach($request->file('files') as $file){
			  
			  $image_name =  date('Ymd').'_'.uniqid().'.'.$file->getClientOriginalExtension();
              $file->move($image_path, $image_name);
			  $notes = $request->description;
			  $path = 'Documents';
			  
				DOCSResults::create([
					'order_id' => $order_id,
					'notes' => $notes,
					'name' => $file->getClientOriginalName(),
					'path' => $path.'/'.$image_name,
					'user_num' => $uid,
					'active' => 'Y'
				]);
			  }
           
			return back()->with('success',__('Documents Uploaded successfully'));
			
			 }
    
	}

 public function destroyAttach($lang,Request $request)

    {
    	$doc = DOCSResults::find($request->image_id);
        Storage::disk('private')->delete($doc->path);
        $doc->delete();		
    	return back()->with('success','Document removed successfully.');	

    }


public function getProfileTests($lang,Request $request){
	$prof_id = $request->id;
	$profile = DB::table('tbl_lab_tests_profiles')->find($prof_id);
	return response()->json(['tests'=>json_decode($profile->profile_tests,true)]);
}

function SavePay($lang,Request $request){

$someArray = [];
$someArray=json_decode($request->data,true); 		
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
$order_id= $request->order_id;
$bill_id=$request->bill_id;
$clinic_id=$request->id_facility;
$balance=$request->balance;

$billRate=TblBillHead::where('id',$bill_id)->value('exchange_rate');
$lbl_usd = isset($billRate)?$billRate:90000;

$ReqBill=TblBillHead::where('id',$bill_id)->where('status','O')->first();



$total_pay=0;
foreach ($someArray as $key=>$area){
  if($key!=0){
	$total_pay += $area["TOTAL"];  
  }	
}
$total_ref=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');
$bill_discount = isset($ReqBill->bill_discount) && $ReqBill->bill_discount!=''?$ReqBill->bill_discount:0;
$bill_balance = $ReqBill->lbill_total-$bill_discount-$total_pay+$total_ref+$ReqBill->tvq+$ReqBill->tps; 

if($bill_balance<0){
	$msg = __('Attention! The remaining is').' '.$bill_balance.' LBP '.__('must not be below zero.');
	return response()->json(['warning'=>$msg]);
}

TblBillPayment::where('payment_type','P')->where('bill_num',$request->bill_id)->update(['status'=>'N']); 

foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0){
   $code = $area["CODE"];
   $date = $area["DATE"];
   $type=trim($area["TYPE"]);
   $price = $area["PRICE"];
   $currency = $area["CURRENCY"];
   $rate = $area["RATE"];
   $total = $area["TOTAL"];
   $totald = number_format((float)$total/$lbl_usd,2,'.','');
   $guarantor = isset($area["GUARANTOR"]) && $area["GUARANTOR"]!=""?$area["GUARANTOR"]:NULL;
  
    if ($lang=='fr'){	
      $typepay = TblBillPaymentMode::where('name_fr',$type)->where('status','O')->first();
   }else{
	  $typepay = TblBillPaymentMode::where('name_eng',$type)->where('status','O')->first();   
   }
   $reference= $typepay->id;
   $assurance=$typepay->assurance;
   
   DB::table('tbl_bill_payment')->insert([
   'datein'=>$date,'bill_num'=>$bill_id,'clinic_num'=>$clinic_id,
   'payment_amount'=>$price,'payment_type'=>'P','currency'=>$currency,
   'dolarprice'=>$rate,'lpay_amount'=>$total,'dpay_amount'=>$totald,
   'reference'=>$reference,'user_type'=>$user_type,'assurance'=>$assurance,
   'order_num'=>$order_id,'guarantor'=>$guarantor,'user_num'=>$user_id,
   'status'=>'Y'
   ]);
   /*$sqlInsertF = "insert into tbl_bill_payment(datein,bill_num,clinic_num,payment_amount,payment_type,currency,dolarprice,lpay_amount,dpay_amount,reference,user_type,assurance,order_num,guarantor,user_num,status) values('".
				 $date."','".
				 $bill_id."','".
				 $clinic_id."','".
				 $price."','P','".
				 $currency."','".
				 $rate."','".
				 $total."','".
				 $totald."','".
				 $reference."','".
				 $user_type."','".
				 $assurance."','".
				 $order_id."','".
				 $guarantor."','".
				 $user_id."','Y')";
	DB::select(DB::raw("$sqlInsertF"));*/
 
 }	
 }


$sumpay=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
$sumref=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');
$tdiscount = isset($ReqBill->bill_discount) && $ReqBill->bill_discount!=''?$ReqBill->bill_discount:0;
$tbalance = $ReqBill->lbill_total-$tdiscount-$sumpay+$sumref+$ReqBill->tvq+$ReqBill->tps;


$Nbalance=number_format((float)$tbalance, 2, '.', ',');
$balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));

$sumpayd=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('dpay_amount');
$sumrefd=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('dpay_amount');
$tdiscountd = isset($ReqBill->bill_discount_us) && $ReqBill->bill_discount_us!=''?$ReqBill->bill_discount_us:0;
$tbalanced = $ReqBill->bill_total-$tdiscountd-$sumpayd+$sumrefd+$ReqBill->tvq+$ReqBill->tps;
$Nbalanced=number_format((float)$tbalanced, 2, '.', ',');
$balanced=floatval(preg_replace('/[^\d.-]/', '', $Nbalanced));

TblBillHead::where('id',$bill_id)->update([
				                  'bill_balance'=>$balance,
								  'bill_balance_us'=>$balanced
								  ]);

//data to return to user
$sumpayd =  number_format((float)$sumpayd,2,'.','');
$sumpayl =  number_format((float)$sumpay,2,'.',',');

$sumrefd =  number_format((float)$sumrefd,2,'.','');
$sumrefl =  number_format((float)$sumref,2,'.',',');

$balanced =  number_format((float)$balanced,2,'.','');
$balancel=number_format((float)$balance, 2, '.', ',');

$tdiscountd =  number_format((float)$tdiscountd,2,'.','');
$tdiscountl =  number_format((float)$tdiscount,2,'.',',');
			  
$msg=__('Payment Saved Success');

return response()->json(['success'=>$msg,'sumpay'=>$sumpayl,'sumref'=>$sumrefl,'nbalance'=>$balancel,'tdiscount'=>$tdiscountl,
                         'tdiscountd'=>$tdiscountd,'sumpayd'=>$sumpayd,'sumrefd'=>$sumrefd,'balanced'=>$balanced]);	  
}	
	  
function SaveRefund($lang,Request $request){

$someArray = [];
$someArray=json_decode($request->data,true); 		
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
$bill_id=$request->bill_id;
$clinic_id=$request->id_facility;
$order_id=$request->order_id;
$balance=$request->balance;

$billRate=TblBillHead::where('id',$bill_id)->value('exchange_rate');
$lbl_usd = isset($billRate)?$billRate:90000;

TblBillPayment::where('payment_type','R')->where('bill_num',$request->bill_id)->update(['status'=>'N']); 
 
foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0){
   $code = $area["CODE"];
   $date = $area["DATE"];
   $type=trim($area["TYPE"]);
    $currency = $area["CURRENCY"];
	 $rate = $area["RATE"];
   $total = $area["TOTAL"];
   $price = $area["PRICE"];
   $guarantor = isset($area["GUARANTOR"]) && $area["GUARANTOR"]!=""?$area["GUARANTOR"]:NULL;
   $totald = number_format((float)$total/$lbl_usd,2,'.','');
  if ($lang=='fr'){	
  $typepay = TblBillPaymentMode::where('name_fr',$type)->where('status','O')->first();
   }else{
	$typepay = TblBillPaymentMode::where('name_eng',$type)->where('status','O')->first();   
   }
   $reference= $typepay->id;
   $assurance=$typepay->assurance;
   
   DB::table('tbl_bill_payment')->insert([
   'datein'=>$date,'bill_num'=>$bill_id,'clinic_num'=>$clinic_id,
   'payment_amount'=>$price,'payment_type'=>'R','currency'=>$currency,
   'dolarprice'=>$rate,'lpay_amount'=>$total,'dpay_amount'=>$totald,
   'reference'=>$reference,'user_type'=>$user_type,'assurance'=>$assurance,
   'order_num'=>$order_id,'guarantor'=>$guarantor,'user_num'=>$user_id,
   'status'=>'Y'
   ]);
   /*$sqlInsertF = "insert into tbl_bill_payment(datein,bill_num,clinic_num,payment_amount,payment_type,currency,dolarprice,lpay_amount,dpay_amount,reference,user_type,assurance,order_num,guarantor,user_num,status) values('".
				 $date."','".
				 $bill_id."','".
				 $clinic_id."','".
				 $price."','R','".
				 $currency."','".
				 $rate."','".
				 $total."','".
				 $totald."','".
				 $reference."','".
				 $user_type."','".
				 $assurance."','".
				 $order_id."','".
				 $guarantor."','".
				 $user_id."','Y')";
	DB::select(DB::raw("$sqlInsertF"));*/
 
 }	
 }

$ReqBill=TblBillHead::where('id',$bill_id)->where('status','O')->first();
$sumpay=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
$sumref=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');
$tdiscount = isset($ReqBill->bill_discount) && $ReqBill->bill_discount!=''?$ReqBill->bill_discount:0;
$tbalance = $ReqBill->lbill_total-$tdiscount-$sumpay+$sumref+$ReqBill->tvq+$ReqBill->tps;
$Nbalance=number_format((float)$tbalance, 2, '.', ',');
$balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));

$sumpayd=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('dpay_amount');
$sumrefd=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('dpay_amount');
$tdiscountd = isset($ReqBill->bill_discount_us) && $ReqBill->bill_discount_us!=''?$ReqBill->bill_discount_us:0;
$tbalanced = $ReqBill->bill_total-$tdiscountd-$sumpayd+$sumrefd+$ReqBill->tvq+$ReqBill->tps;
$Nbalanced=number_format((float)$tbalanced, 2, '.', ',');
$balanced=floatval(preg_replace('/[^\d.-]/', '', $Nbalanced));

TblBillHead::where('id',$bill_id)->update([
				                  'bill_balance'=>$balance,
								  'bill_balance_us'=>$balanced
								  ]);

//data to return to user

$sumpayd =  number_format((float)$sumpayd,2,'.','');
$sumpayl =  number_format((float)$sumpay,2,'.',',');

$sumrefd =  number_format((float)$sumrefd,2,'.','');
$sumrefl =  number_format((float)$sumref,2,'.',',');

$balanced =  number_format((float)$balanced,2,'.','');
$balancel=number_format((float)$balance, 2, '.', ',');

$tdiscountd =  number_format((float)$tdiscountd,2,'.','');
$tdiscountl =  number_format((float)$tdiscount,2,'.',',');
			  
$msg=__('Donate Saved Success');

return response()->json(['success'=>$msg,'sumpay'=>$sumpayl,'sumref'=>$sumrefl,'nbalance'=>$balancel,'tdiscount'=>$tdiscountl,
                         'tdiscountd'=>$tdiscountd,'sumpayd'=>$sumpayd,'sumrefd'=>$sumrefd,'balanced'=>$balanced]);	  



}


function SaveDiscount($lang,Request $request){

$someArray = [];
$someArray=json_decode($request->data,true); 		
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
$bill_id=$request->bill_id;
$clinic_id=$request->id_facility;
$order_id=$request->order_id;
$balance=$request->balance;
$billRate=TblBillHead::where('id',$bill_id)->value('exchange_rate');
$lbl_usd = isset($billRate)?$billRate:90000;

TblBillPayment::where('payment_type','D')->where('bill_num',$request->bill_id)->update(['status'=>'N']); 
 
foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0){
   $code = $area["CODE"];
   $date = $area["DATE"];
   $type=trim($area["TYPE"]);
    $currency = $area["CURRENCY"];
	 $rate = $area["RATE"];
   $total = $area["TOTAL"];
   $price = $area["PRICE"];
   $guarantor = isset($area["GUARANTOR"]) && $area["GUARANTOR"]!=""?$area["GUARANTOR"]:NULL;
   $totald = number_format((float)$total/$lbl_usd,2,'.','');
  if ($lang=='fr'){	
  $typepay = TblBillPaymentMode::where('name_fr',$type)->where('status','O')->first();
   }else{
	$typepay = TblBillPaymentMode::where('name_eng',$type)->where('status','O')->first();   
   }
   $reference= $typepay->id;
   $assurance=$typepay->assurance;
   
   DB::table('tbl_bill_payment')->insert([
   'datein'=>$date,'bill_num'=>$bill_id,'clinic_num'=>$clinic_id,
   'payment_amount'=>$price,'payment_type'=>'D','currency'=>$currency,
   'dolarprice'=>$rate,'lpay_amount'=>$total,'dpay_amount'=>$totald,
   'reference'=>$reference,'user_type'=>$user_type,'assurance'=>$assurance,
   'order_num'=>$order_id,'guarantor'=>$guarantor,'user_num'=>$user_id,
   'status'=>'Y'
   ]);
 
 }	
 }
$ReqBill=TblBillHead::where('id',$bill_id)->where('status','O')->first();

     

$sumpay=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
$sumref=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');
$sumdis=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','D')->sum('lpay_amount');


$Nbalance=number_format((float)$ReqBill->lbill_total-$sumdis-$sumpay+$sumref+$ReqBill->tvq+$ReqBill->tps, 2, '.', ',');
$balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));

$sumpayd=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('dpay_amount');
$sumrefd=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('dpay_amount');
$sumdisd=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','D')->sum('dpay_amount');


$Nbalanced=number_format((float)$ReqBill->bill_total-$sumdisd-$sumpayd+$sumrefd+$ReqBill->tvq+$ReqBill->tps, 2, '.', ',');
$balanced=floatval(preg_replace('/[^\d.-]/', '', $Nbalanced));



TblBillHead::where('id',$bill_id)->update(['bill_balance'=>$balance,'bill_balance_us'=>$balanced,'bill_discount'=>$sumdis,'bill_discount_us'=>$sumdisd]);	

//data to return to user
$sumpayd =  number_format((float)$sumpayd,2,'.','');
$sumpayl =  number_format((float)$sumpay,2,'.',',');

$sumrefd =  number_format((float)$sumrefd,2,'.','');
$sumrefl =  number_format((float)$sumref,2,'.',',');

$sumdisd =  number_format((float)$sumdisd,2,'.','');
$sumdisl =  number_format((float)$sumdis,2,'.',',');

$balanced =  number_format((float)$balanced,2,'.','');
$balancel=number_format((float)$balance, 2, '.', ',');

$tdiscountd =  number_format((float)$sumdisd,2,'.','');
$tdiscountl =  number_format((float)$sumdisl,2,'.',',');
			  
$msg=__('Discount Saved Success');

return response()->json(['success'=>$msg,'sumpay'=>$sumpayl,'sumref'=>$sumrefl,'nbalance'=>$balancel,'tdiscount'=>$tdiscountl,
                         'tdiscountd'=>$tdiscountd,'sumpayd'=>$sumpayd,'sumrefd'=>$sumrefd,'balanced'=>$balanced]);	  

}		  		  		  		  
	

public function get_referredtests($lang,Request $request){
    $id_clinic = auth()->user()->clinic_num;
	$options = ExtLab::select(DB::raw('concat(id,"-",full_name) as value'), 'full_name as label')->where('status','A')->where('clinic_num',$id_clinic)
					 ->orderBy('id','desc')->get();
	// Add the "Choose" option with an empty ID
    $chooseOption = ['label' => __('Choose'), 'value' => ''];
    $options->prepend($chooseOption);
	
	return response()->json($options);			 
			
}

public function getBacteriaAntibiotics($lang,Request $request){
  //dd("Hi");
  $order_id = $request->order_id;
  $test_id = $request->test_id;
  //get from db or from modal hidden
  $arr_bacteria = !empty($request->input('bacteria'))?$request->input('bacteria'):array();
  //get antibiotic with result from db or from modal hidden
  $antibiotics_data = !empty($request->input('antibiotics_data'))?json_decode($request->input('antibiotics_data'),true):array();
  //dd($antibiotics_data);
  $collection = collect($antibiotics_data);
  
  $culture = DB::table('tbl_order_culture_results')->find($request->culture_id);
  
 
  $result = NULL;
  
  
  $html = $selectedS = $selectedR = $selectedI ='';
  if(!empty($arr_bacteria)){
	  
	  foreach($arr_bacteria as $b){
		  $html.='<div class="mb-2 table-responsive col-md-4"><table  class="table-bordered" style="width:100%;"><thead><tr><th>'.__('Germ').'</th><th>'.__('Pathogen').'</th><th>'.__('Antibiogram').'</th><th style="display:none;">'.__('BactID').'</th> <th style="display:none;">'.__('AntbID').'</th></tr></thead><tbody>';
		  
		  $bacteria_name = DB::table('tbl_lab_sbacteria')->where('id',$b)->value('descrip');
		  //get group antibiotics for each bacteria
		  $antibiotics = DB::table('tbl_lab_bacteria')->whereJsonContains('sbacteria',$b)->get();
		  foreach($antibiotics as $ant){
			//get all antibiotics for bacteria
			$antibiotic = DB::table('tbl_lab_antibiotic')->whereIn('id',json_decode($ant->bacteria_antibiotic,true))->distinct()->get(); 
			
			foreach($antibiotic as $a){
				 $val1 = $b;
				 $val2= $a->id;
				 $result = "";
				 $filtered = $collection->filter(function ($innerArray) use ($val1, $val2) {
                    return intval($innerArray[0]) == $val1 && intval($innerArray[1]) == $val2;
                  });
				 
				 if (!$filtered->isEmpty()) {
					 $result = $filtered->first()[2];
				 }
			     
				 $selectedS = ($result=="" || (isset($result) && $result=='S'))?"selected":"";
			     $selectedR = (isset($result) && $result=='R')?"selected":"";
			     $selectedI = (isset($result) && $result=='I')?"selected":"";
				 
				 $html.='<tr><td>'.$bacteria_name.'</td><td>'.$a->descrip.'</td><td><select class="cult_result form-control"><option value="S" '.$selectedS.'>S</option><option value="R" '.$selectedR.'>R</option><option value="I" '.$selectedI.'>I</option></select></td><td style="display:none">'.$b.'</td><td style="display:none;">'.$a->id.'</td></tr>';
			}
		  } 
	      $html.='</tbody></table></div>';
	  
	  }
   } 
  return response()->json(['html'=>$html]);
}



public function saveCultureData($lang,Request $request){
	$type= $request->type;
	$user_num = auth()->user()->id;
	
	switch($type){
		case 'culture_result':
		 $culture_id = $request->culture_id;
		 $cult = DB::table('tbl_order_culture_results')->find($culture_id);
		 //if culture result is  not the same then update it
		 if($cult->culture_result != $request->culture_result){
		  DB::table('tbl_order_culture_results')->where('id',$culture_id)->update(['culture_result'=>$request->culture_result,'user_num'=>$user_num]);	
		 }
		return response()->json(['msg'=>__('Culture result updated successfully')]);
		break;
		case 'gram_stain':
		 $culture_id = $request->culture_id;
		 $cult = DB::table('tbl_order_culture_results')->find($culture_id);
		 //if culture result is  not the same then update it
		 if($cult->gram_staim != $request->gram_staim){
		  DB::table('tbl_order_culture_results')->where('id',$culture_id)->update(['gram_staim'=>$request->gram_staim,'user_num'=>$user_num]);	
		 }
		return response()->json(['msg'=>__('Gram stain updated successfully')]);
		break;
		case 'antibiotic':
		   $culture_id = $request->culture_id;
		  //update all to inactive N
		  DB::table('tbl_order_culture_results_detail')->where('culture_id',$culture_id)->where('active','Y')->update(['active'=>'N','user_num'=>$user_num]);		
		  $details = json_decode($request->antibiotic_data,true);
		    foreach($details as $d){
			        $bacteria_id = $d[0];
					$antibiotic_id = $d[1];
					$result = $d[2];
					$exist = DB::table('tbl_order_culture_results_detail')->where('culture_id',$culture_id)->where('bacteria_id',$bacteria_id)->where('antibiotic_id',$antibiotic_id)->first();
					if($exist){
						DB::table('tbl_order_culture_results_detail')
						   ->where('id',$exist->id)->update([
						   'result'=>$result,
						   'user_num'=>$user_num,
						   'active'=>'Y'
							]);  
					}else{
						DB::table('tbl_order_culture_results_detail')->insert([
						   'culture_id'=>$culture_id,
						   'bacteria_id'=>$bacteria_id,
						   'antibiotic_id'=>$antibiotic_id,
						   'result'=>$result,
						   'user_num'=>$user_num,
						   'active'=>'Y'
						  ]);  
					} 
		   
		        }		
		return response()->json(['msg'=>__('Antibiotics updated successfully')]);
		break;
		case 'all':
		    $culture_data = json_decode($request->input('culture_data'),true);
						
			foreach($culture_data as $k=>$v){
				$culture_id = intval($v["culture_id"]);
				$test_id = intval($v["test_id"]);
				$order_id = $v["order_id"];
				$gram_staim = trim($v["gram_staim"]);
				$culture_result = trim($v["culture_result"]);
				$details = json_decode($v["antibiotic_data"],true);
				$order = LabOrders::find($order_id);
						
				DB::table('tbl_order_culture_results')->where('id',$culture_id)
					->update(['gram_staim'=>$gram_staim,'culture_result'=>$culture_result,'user_num'=>$user_num]);
				
				//update all to inactive N
				DB::table('tbl_order_culture_results_detail')->where('culture_id',$culture_id)->where('active','Y')->update(['active'=>'N','user_num'=>$user_num]);		
				
				foreach($details as $d){
					$bacteria_id = $d[0];
					$antibiotic_id = $d[1];
					$result = $d[2];
					$exist = DB::table('tbl_order_culture_results_detail')->where('culture_id',$culture_id)->where('bacteria_id',$bacteria_id)->where('antibiotic_id',$antibiotic_id)->first();
					if($exist){
						DB::table('tbl_order_culture_results_detail')
						   ->where('id',$exist->id)->update([
						   'result'=>$result,
						   'user_num'=>$user_num,
						   'active'=>'Y'
							]);  
					}else{
						DB::table('tbl_order_culture_results_detail')->insert([
						   'culture_id'=>$culture_id,
						   'bacteria_id'=>$bacteria_id,
						   'antibiotic_id'=>$antibiotic_id,
						   'result'=>$result,
						   'user_num'=>$user_num,
						   'active'=>'Y'
						  ]);  
					}
				
				}
			}
		return response()->json(['msg'=>__('Culture data updated successfully')]);
		break;
	}
	
	

  
	
  

}

public function printCultureData($lang,Request $request){
	$culture_data = DB::table('tbl_order_culture_results as c')
	           ->select('c.id as culture_id','c.need_validation','t.test_name','c.clinic_num','c.order_id','c.patient_num','c.gram_staim','c.culture_result')
			   ->join('tbl_lab_tests as t','t.id','c.test_id')
			   ->where('c.order_id',$request->order_id)->where('c.active','Y')->get();
	
	$culture_ids = DB::table('tbl_order_culture_results')->where('order_id',$request->order_id)->where('active','Y')->pluck('id')->toArray();
	
	$culture_details = DB::table('tbl_order_culture_results_detail as det')
	           ->select('det.bacteria_id','det.culture_id','bact.descrip as bacteria_name','ant.descrip as antibiotic_name','det.result')
			   ->join('tbl_lab_sbacteria as bact','bact.id','det.bacteria_id')
			   ->join('tbl_lab_antibiotic as ant','ant.id','det.antibiotic_id')
			   ->where('det.active','Y')
			   ->whereIn('det.culture_id',$culture_ids)
			   ->get();
	
	$bacteria_ids = array();
	foreach($culture_details as $d){
		if(!in_array($d->bacteria_id,$bacteria_ids)){
			array_push($bacteria_ids,$d->bacteria_id);
		}
	}
	
	$bacteria = DB::table('tbl_lab_sbacteria')->whereIn('id',$bacteria_ids)->get();
	
	$order = LabOrders::find($request->order_id);
	$patient = Patient::find($order->patient_num);
	$doctor = Doctor::where('doctor_user_num',$order->ext_lab)->first();
	$ext_lab = ExtLab::where('id',$order->ext_lab)->first();
	$logo = DB::table('tbl_bill_logo')->where('clinic_num',$order->clinic_num)->where('status','O')->first();
	$general_sig = DB::table('tbl_clinics_signatures')->where('clinic_num',$order->clinic_num)->where(DB::raw('trim(type)'),'results')->where('active','Y')->first();

	$branch = Clinic::find($order->clinic_num);
	$tel = isset($branch->telephone) && $branch->telephone!=''?'Tel: '.$branch->telephone.' , ':'';
	$whatsapp = isset($branch->whatsapp) && $branch->whatsapp!=''?'Whatsapp: '.$branch->whatsapp.' , ':'';
	$website = isset($branch->website) && $branch->website!=''?' '.$branch->website.' , ':'';
	$address = isset($branch->full_address) && $branch->full_address!=''?'Address: '.$branch->full_address:'';
    $branch_data = $tel.$whatsapp.$website.$address;               
    $data = ['patient'=>$patient,'lab'=>$branch,
			'doctor'=>$doctor,'ext_lab'=>$ext_lab,'order'=>$order,'branch_data'=>$branch_data,
			'culture_data'=>$culture_data,'culture_details'=>$culture_details,'bacteria'=>$bacteria,'logo'=>$logo,
			'general_sig'=>$general_sig]; 
           
		    
			$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true,'isPHPEnabled'=>true])
                       -> loadView('lab.visit.culture.culturePDF', $data);
            $dom_pdf = $pdf->getDomPDF();
	        $canvas = $dom_pdf->get_canvas();
			$canvas->page_text(250, $canvas->get_height() - 65, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 8, [0, 0, 0]);	

    return $pdf->stream();

}

public function patData($lang,Request $request){
	
	$req_type = $request->req_type;
	$user_type= auth()->user()->type;
	$user_num = auth()->user()->id;
	switch($req_type){
	  case 'newData':
	    $patient_id = $request->patient;
		$clinic = Clinic::find($request->clinic);
		
		   if($user_type==1){
			$doctors=Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('doctor_user_num',$user_num)->get();
			$ext_labs = ExtLab::where('status','A')->orderBy('full_name')->get();
			}
			if($user_type==3){
			$ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$user_num)->get();
			$doctors=Doctor::select('doctor_user_num as user_num',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
			}
			if($user_type==2){
			$doctors=Doctor::select('doctor_user_num as user_num',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
			$ext_labs = ExtLab::where('status','A')->orderBy('full_name')->get();
			}
		    $titles = DB::table('tbl_patients_titles')->where('status','Y')->get();
			$html = view('patients_list.patient_modal_view')->with(['clinic'=>$clinic,'ext_labs'=>$ext_labs,'doctors'=>$doctors,'new'=>true,'titles'=>$titles])->render();
            return response()->json(['html'=>$html]);
	  break;
	  case 'editData':	
		   
		   $patient_id = $request->patient;
		   $clinic = Clinic::find($request->clinic);
		   
			 if($user_type==1){
			$doctors=Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('doctor_user_num',$user_num)->get();
			$ext_labs = ExtLab::where('status','A')->orderBy('full_name')->get();
			}
			if($user_type==3){
			$ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$user_num)->get();
			$doctors=Doctor::select('doctor_user_num as user_num',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
			}
			if($user_type==2){
			$doctors=Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
			$ext_labs = ExtLab::where('status','A')->orderBy('full_name')->get();
			}
		    $titles = DB::table('tbl_patients_titles')->where('status','Y')->get();
			$patient = Patient::find($patient_id);
			$html = view('patients_list.patient_modal_view')->with(['patient'=>$patient,'clinic'=>$clinic,'ext_labs'=>$ext_labs,'doctors'=>$doctors,'titles'=>$titles])->render();
			return response()->json(['html'=>$html]);
	  break;
	  case 'new':
           
			// Check if a patient with the same first, middle, last name, and DOB exists
			 /*$existingPatientWithDOB = Patient::where(DB::raw('trim(upper(first_name))'), trim(strtoupper($request->first_name)))
				->where(DB::raw('trim(upper(middle_name))'), trim(strtoupper($request->middle_name)))
				->where(DB::raw('trim(upper(last_name))'), trim(strtoupper($request->last_name)))
				->where('birthdate', $request->birthdate)
				->first();
		
			if($existingPatientWithDOB){
				return response()->json(['error' => 'A patient with the same name and birthdate already exists']);
			 }*/  
		$myId=auth()->user()->id;
        $title_id = $request->title;
		$title = DB::table('tbl_patients_titles')->find($title_id);
		
		if(!isset($title)){
			$title_id = DB::table('tbl_patients_titles')->insertGetId(['name'=>$request->title,'name_fr'=>$request->title,'status'=>'Y','user_num'=>$myId]);
		} 			
       			
        //get file number
		$nextFileNb = Patient::generateFileNb();
		
		$pat_id = Patient::create([
            'clinic_num'=>$request->clinic_num,
			'doctor_num'=>$request->doctor_num,
            'first_name'=>strtoupper(trim($request->first_name)),
			'middle_name'=>isset($request->middle_name) && $request->middle_name!=''?strtoupper(trim($request->middle_name)):NULL,
            'last_name'=>strtoupper(trim($request->last_name)),
			'husband_name'=>trim($request->husband_name),
			'passport_nb'=>$request->passport_nb,
            'birthdate'=>$request->birthdate,
            'title'=>$title_id,
			'ext_lab'=>$request->ext_lab,
            'addresse'=>trim($request->address),
            'first_phone'=>str_replace("-","",$request->home_phone),
            'cell_phone'=>str_replace("-","",$request->cell_phone),
            'sex'=>$request->gender,
            'status' => 'O',
            'user_num'=>$myId,
            'email'=>$request->email,
			'file_nb' => $nextFileNb
			])->id;
            
		   $msg = __("New Patient Added Successfully.");
		   $patient = Patient::find($pat_id);
		   $title = DB::table('tbl_patients_titles')->where('id',$patient->title)->value('name');
		   
		   $patient_data ='';
			if(isset($title) && $title!=''){
			  $patient_data = $title.' ';	
			}
		   
		   if(isset($patient->middle_name) && $patient->middle_name!=''){
		      $patient_data .= $patient->first_name.' '.$patient->middle_name.' '.$patient->last_name;
		   }else{
			  $patient_data .= $patient->first_name.' '.$patient->last_name;  
		   }
		   
		   $patient_data .=' [ '.'File Nb.'.' : '.$patient->file_nb;
		   
		   if(isset($patient->ext_lab) && $patient->ext_lab!=''){
		     $gname=ExtLab::where('id',$patient->ext_lab)->value('full_name');
			 if(isset($gname) && $gname!=''){
			  $patient_data .=' , '.$gname;
			 }
		   }
		   
		   if(isset($patient->cell_phone) && $patient->cell_phone!=''){
		     $patient_data .=' , '.'Cell#'.' : '.$patient->cell_phone;
		   }
		   $patient_data .=' ]';
		   
		   return response()->json(['success'=>$msg,'patient_id'=>$pat_id,'patient_data'=>$patient_data]);
        break;
	  case 'update':
	     
			$id = $request->id_patient;
			// Check if a patient with the same first, middle, last name, and DOB exists
			 /*$existingPatientWithDOB = Patient::where(DB::raw('trim(upper(first_name))'), trim(strtoupper($request->first_name)))
				->where(DB::raw('trim(upper(middle_name))'), trim(strtoupper($request->middle_name)))
				->where(DB::raw('trim(upper(last_name))'), trim(strtoupper($request->last_name)))
				->where('birthdate', $request->birthdate)
				->where('id','<>',$id)
				->first();
		
			if($existingPatientWithDOB){
				return response()->json(['error' => 'A patient with the same name and birthdate already exists']);
			 } */ 
	
			$myId=auth()->user()->id;
			$title_id = $request->title;
		    $title = DB::table('tbl_patients_titles')->find($title_id);
		
			if(!isset($title)){
				$title_id = DB::table('tbl_patients_titles')->insertGetId(['name'=>$request->title,'name_fr'=>$request->title,'status'=>'Y','user_num'=>$myId]);
			} 	
			
		    Patient::where('id',$id)->update([
			'doctor_num'=>$request->doctor_num,
            'first_name'=>strtoupper(trim($request->first_name)),
			'middle_name'=>isset($request->middle_name) && $request->middle_name!=''?strtoupper(trim($request->middle_name)):NULL,
            'last_name'=>strtoupper(trim($request->last_name)),
			'husband_name'=>trim($request->husband_name),
			'passport_nb'=>$request->passport_nb,
            'birthdate'=>$request->birthdate,
            'title'=>$title_id,
			'ext_lab'=>$request->ext_lab,
            'addresse'=>trim($request->address),
            'first_phone'=>str_replace("-","",$request->home_phone),
            'cell_phone'=>str_replace("-","",$request->cell_phone),
            'sex'=>$request->gender,
            'user_num'=>$myId,
            'email'=>$request->email
             ]);
				
		    $msg = __("Patient Updated Successfully.");
			
			$patient=Patient::find($id);
			$title = DB::table('tbl_patients_titles')->where('id',$patient->title)->value('name');
			
			$patient_data ='';
			if(isset($title) && $title!=''){
			  $patient_data = $title.' ';	
			}
		   
		   if(isset($patient->middle_name) && $patient->middle_name!=''){
		      $patient_data .= $patient->first_name.' '.$patient->middle_name.' '.$patient->last_name;
		   }else{
			  $patient_data .= $patient->first_name.' '.$patient->last_name;  
		   }
		   
		   $patient_data .=' [ '.'File Nb.'.' : '.$patient->file_nb;
		   
		   if(isset($patient->ext_lab) && $patient->ext_lab!=''){
		     $gname=ExtLab::where('id',$patient->ext_lab)->value('full_name');
			 if(isset($gname) && $gname!=''){
			  $patient_data .=' , '.$gname;
			 }
		   }
		   
		   if(isset($patient->cell_phone) && $patient->cell_phone!=''){
		     $patient_data .=' , '.'Cell#'.' : '.$patient->cell_phone;
		   }
		   $patient_data .=' ]';
		   
		   return response()->json(['success'=>$msg,'patient_id'=>$patient->id,'patient_data'=>$patient_data]);
            
	  break;
	}
  	
}

public function addResultTag($lang,Request $request){
	$type = $request->type;
	$test_id = intval($request->test_id);
	$user_num = auth()->user()->id;
	switch($type){
		case 'culture':
		 $name = trim($request->tagName);
		 $tag_id =DB::table('tbl_lab_text_results')->insertGetId(['status'=>'Y','user_num'=>$user_num,'test_id'=>$test_id,'name'=>$name,'name_fr'=>$name]);
		return response()->json(['tag_id'=>$tag_id]);
		break;
		case 'result': 
		 $name = trim($request->tagName);
		 $tag_id =DB::table('tbl_lab_text_results')->insertGetId(['status'=>'Y','user_num'=>$user_num,'test_id'=>$test_id,'name'=>$name,'name_fr'=>$name]);
		return response()->json(['tag_id'=>$tag_id]);
		break;
	}
}

public function GetPDFBill($lang,Request $request){
	        
			$Bill =TblBillHead::find($request->id);
			$order = LabOrders::find($Bill->order_id);
			$patient = Patient::find($order->patient_num);
			$doctor = Doctor::where('id',$order->doctor_num)->first();
			$ext_lab = ExtLab::where('id',$order->ext_lab)->first();
			$result=TblBillSpecifics::where('status','O')->where('bill_num',$Bill->id)->get();
		    $logo=DB::table('tbl_bill_logo')->where('clinic_num',$order->clinic_num)->where('status','O')->first();

			$branch = Clinic::find($order->clinic_num);
			$tel = isset($branch->telephone) && $branch->telephone!=''?'Tel: '.$branch->telephone.' , ':'';
			$whatsapp = isset($branch->whatsapp) && $branch->whatsapp!=''?'Whatsapp: '.$branch->whatsapp.' , ':'';
			$website = isset($branch->website) && $branch->website!=''?' '.$branch->website.' , ':'';
			$address = isset($branch->full_address) && $branch->full_address!=''?'Address: '.$branch->full_address:'';
			$branch_data = $tel.$whatsapp.$website.$address;               
			
            $pay = DB::table('tbl_bill_payment')->where('bill_num', '=', $Bill->id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('lpay_amount');
	        $ref = DB::table('tbl_bill_payment')->where('bill_num', '=', $Bill->id)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('lpay_amount');
            $Nbalance=number_format((float)$Bill->bill_balance, 2, '.', ',');
            $balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));
			
			$payd = DB::table('tbl_bill_payment')->where('bill_num', '=', $Bill->id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('dpay_amount');
	        $refd = DB::table('tbl_bill_payment')->where('bill_num', '=', $Bill->id)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('dpay_amount');
			$Nbalanced=number_format((float)$Bill->bill_balance_us, 2, '.', ',');
            $balanced=floatval(preg_replace('/[^\d.-]/', '', $Nbalanced));
			
			$discount =$Bill->bill_discount;
			
			$discountd =$Bill->bill_discount_us;
			
			$currencyUSD=TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
		    $lbl_usd = isset($currencyUSD)?$currencyUSD->price:90000;
			
			$sumpayd =  number_format((float)$payd,2,'.','');
            $sumpayl =  number_format((float)$pay,2,'.',',');

            $sumrefd =  number_format((float)$refd,2,'.','');
            $sumrefl =  number_format((float)$ref,2,'.',',');

            $balanced =  number_format((float)$balanced,2,'.','');
            $balancel=number_format((float)$balance, 2, '.', ',');

            $tdiscountd =  number_format((float)$discountd,2,'.','');
            $tdiscountl =  number_format((float)$discount,2,'.',',');
           	$general_sig = DB::table('tbl_clinics_signatures')->where('clinic_num',$order->clinic_num)->where(DB::raw('trim(type)'),'results')->where('active','Y')->first();
		
				
			$data = ['Bill' => $Bill,'patient'=>$patient,'lab'=>$branch,
			         'doctor'=>$doctor,'ext_lab'=>$ext_lab,'order'=>$order,
					 'branch_data'=>$branch_data,'result' => $result,
				     'sumpayd'=>$sumpayd,'sumpayl'=>$sumpayl,'sumrefd'=>$sumrefd,'sumrefl'=>$sumrefl,
					 'balanced'=>$balanced,'balancel'=>$balancel,'tdiscountd'=>$tdiscountd,
					 'tdiscountl'=>$tdiscountl,'logo'=>$logo,'general_sig'=>$general_sig]; 
			
            $pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
                        -> loadView('lab.visit.billing.BillPDF', $data);
     
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));        
            return $pdf->stream();
}



private function getRefUnit($gender,$test_id,$is_printed,$field_num){
     $ref_range = NULL;
     $one_ref_range = NULL;
     $unit = NULL;
     $remark='';
     $range = '';
    
	if($is_printed=='Y'){
		$all_fields = LabTestFields::whereRaw('active="Y" and test_id=? and (gender=? or gender="B" or gender="" or gender IS NULL)',[$test_id,$gender])
								   ->orderBy('field_order')
								   ->orderByRaw("CASE 
										WHEN gender = ? THEN 1 
										WHEN gender = 'B' THEN 2 
										WHEN gender = '' THEN 3 
										WHEN gender IS NULL THEN 4 
									  END", [$gender])
		                           ->get();
		
		$one_field = LabTestFields::whereIn('id',$field_num)
		                            ->orderBy('field_order')
									->orderByRaw("CASE 
										WHEN gender = ? THEN 1 
										WHEN gender = 'B' THEN 2 
										WHEN gender = '' THEN 3 
										WHEN gender IS NULL THEN 4 
									  END", [$gender])
									->get(); 
	    
		
		if($all_fields->count()){
			$ref_range = '';
			
			foreach($all_fields as $f){
			 //get only one unit
			 if($f->unit!='' && isset($f->unit) && $unit==''){	$unit =  $f->unit;	  }
			 //gender
				$gender = $f->gender;
				switch($gender){
				 case 'F': $gender=__('Female'); break;
				 case 'M': $gender=__('Male'); break;
				 case 'B': $gender=__('Male or Female'); break;
				 default: $gender='';
				}
			  
			 		 
			 $fage = isset($f->fage) && $f->fage!='' && $f->fage!='0'?$f->fage:'';
			 $tage = isset($f->tage) && $f->tage!='' && $f->tage!='0'?$f->tage:'';
			 
			 $age_type = __('yrs');
			 if($f->mytype=='D'){
				$age_type = __('days'); 
			 }
			 if($f->mytype=='M'){
				$age_type = __('months'); 
			 }
			 if($f->mytype=='W'){
				$age_type = __('weeks'); 
			 }
			 
			 $age_range ='';
			 if($fage !='' && $tage !=''){
				 $age_range = $fage.'-'.$tage.' '.$age_type;
				 if($f->mytype=='D' && $tage==365){
					 $age_range = $fage.' '.__('days').'-1'.' '.__('year'); 
				 }
			 }else{
				if($fage =='' && $tage !=''){
					$age_range = '<'.$tage.' '.$age_type;
				}else{
				  if($fage !='' && $tage ==''){
					$age_range = '≥'.$fage.' '.$age_type;
				  }
				}
			 }
			 
			
			 //normal ranges
			 $min = isset($f->normal_value1) && $f->normal_value1!=''?$f->normal_value1:'';
			 $max = isset($f->normal_value2) && $f->normal_value2!=''?$f->normal_value2:'';
			 $range='';
			 if($min !='' && $max !=''){
				
				$range=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$min.'-'.$max:$min.'-'.$max;
				}else{
					if($min=='' && $max!=''){
						$range=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$max:"<".$max;
					}else{
					  if($max=='' && $min!=''){
					    $range=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$min:"≥".$min;
					  }else{
					   if($min=='' && $max==''){
						   
						   if(isset($f->descrip)&& $f->descrip!=''){
							  $descrip = $f->descrip;
							  //$range = $descrip;
							  $range='<div style="white-space:pre-line;">'.$descrip.'</div>'; 
						   }
						 }
					  }
				   } 	
				
				}
				
				
				if($gender!=''){
					if($age_range!=''){
						 
						 
						 if(strpos($ref_range, $gender) !== false){
						   $ref_range .= '<div>'.$age_range.' : '.$range.'</div>';	 
						 }else{
						   $ref_range .= '<div>'.$gender.' ( '.$age_range.' ) '.': '.$range.'</div>';
						 }
					}else{
						 
						 
						 if(strpos($ref_range, $gender) !== false){
							$ref_range .= '<div>'.$range.'</div>'; 
						 }else{
						    $ref_range .= '<div>'.$gender.' : '.$range.'</div>';
						 }
					}
				  }else{
					if($age_range!=''){
					  $ref_range .= '<div>'.$age_range.' : '.$range.'</div>';
					  
						
					}else{
					  $ref_range .= '<div>'.$range.'</div>';
					   
						   
					 }	
				 }
				 
			
						
				if(isset($f->remark) && $f->remark!=''){
					$remark = $f->remark;
					$ref_range.= '<div style="white-space:pre-line;">'.$remark.'</div>';
					
				}
				
				
			  }
	        }//end all field
	
	      if($one_field->count()){
            $one_ref_range = '';
			
			foreach($one_field as $f){
			  //gender
				$gender = $f->gender;
				switch($gender){
				 case 'F': $gender=__('Female'); break;
				 case 'M': $gender=__('Male'); break;
				 case 'B': $gender=__('Male or Female'); break;
				 default: $gender='';
				}
			  
			 			 
			 $fage = isset($f->fage) && $f->fage!='' && $f->fage!='0'?$f->fage:'';
			 $tage = isset($f->tage) && $f->tage!='' && $f->tage!='0'?$f->tage:'';
			 
			 $age_type = __('yrs');
			 if($f->mytype=='D'){
				$age_type = __('days'); 
			 }
			 if($f->mytype=='M'){
				$age_type = __('months'); 
			 }
			 if($f->mytype=='W'){
				$age_type = __('weeks'); 
			 }
			 
			 $age_range ='';
			 if($fage !='' && $tage !=''){
				 $age_range = $fage.'-'.$tage.' '.$age_type;
				 if($f->mytype=='D' && $tage==365){
					 $age_range = $fage.' '.__('days').'-1'.' '.__('year'); 
				 }
			 }else{
				if($fage =='' && $tage !=''){
					$age_range = '<'.$tage.' '.$age_type;
				}else{
				  if($fage !='' && $tage ==''){
					$age_range = '≥'.$fage.' '.$age_type;
				  }
				}
			 }
			 
			
			 //normal ranges
			 $min = isset($f->normal_value1) && $f->normal_value1!=''?$f->normal_value1:'';
			 $max = isset($f->normal_value2) && $f->normal_value2!=''?$f->normal_value2:'';
			 $range='';
			 if($min !='' && $max !=''){
				
				$range=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$min.'-'.$max:$min.'-'.$max;
				}else{
					if($min=='' && $max!=''){
						$range=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$max:"<".$max;
					}else{
					  if($max=='' && $min!=''){
					    $range=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$min:"≥".$min;
					  }else{
					   if($min=='' && $max==''){
						   
						   if(isset($f->descrip)&& $f->descrip!=''){
							  $descrip = $f->descrip;
							  //$range = $descrip;
							  $range='<div style="white-space:pre-line;">'.$descrip.'</div>'; 
						   }
						 }
					  }
				   } 	
				
				}
				
				
				if($gender!=''){
					if($age_range!=''){
						 
						  $one_ref_range .= '<div>'.$age_range.' : '.$range.'</div>';	
						 
						
					}else{
						 
						   $one_ref_range .= '<div>'.$range.'</div>';	
						 
						
					}
				  }else{
					if($age_range!=''){
					  
					      $one_ref_range .= '<div>'.$age_range.' : '.$range.'</div>';	
						
					}else{
					   
					       $one_ref_range .= '<div>'.$range.'</div>';
						   
					 }	
				 }
							
			  }
	
	        }//end one field

		  }else{
		  //not print all
		  $field = LabTestFields::whereIn('id',$field_num)
		           ->orderBy('field_order')
				   ->orderByRaw("CASE 
                    WHEN gender = ? THEN 1 
                    WHEN gender = 'B' THEN 2 
                    WHEN gender = '' THEN 3 
                    WHEN gender IS NULL THEN 4 
                    END", [$gender])
				   ->get();  
			$field_cnt = $field->count();
			
			if($field_cnt){
					$ref_range = '';
					$one_ref_range = '';
					
					foreach($field as $f){
					 
					
					  //get only one unit
					 if($f->unit!='' && isset($f->unit) && $unit==''){
						$unit =  $f->unit;
					  }
					 //gender
						
						$gender = $f->gender;
						switch($gender){
						 case 'F': $gender=__('Female'); break;
						 case 'M': $gender=__('Male'); break;
						 case 'B': $gender=__('Male or Female'); break;
						 default: $gender='';
						}
					  
					 
					 
					 $fage = isset($f->fage) && $f->fage!='' && $f->fage!='0'?$f->fage:'';
					 $tage = isset($f->tage) && $f->tage!='' && $f->tage!='0'?$f->tage:'';
					 
					 $age_type = __('yrs');
					 if($f->mytype=='D'){
						$age_type = __('days'); 
					 }
					 if($f->mytype=='M'){
						$age_type = __('months'); 
					 }
					 if($f->mytype=='W'){
						$age_type = __('weeks'); 
					 }
					 
					 $age_range ='';
					 if($fage !='' && $tage !=''){
						 $age_range = $fage.'-'.$tage.' '.$age_type;
						 if($f->mytype=='D' && $tage==365){
							 $age_range = $fage.' '.__('days').'-1'.' '.__('year'); 
						 }
					 }else{
						if($fage =='' && $tage !=''){
							$age_range = '<'.$tage.' '.$age_type;
						}else{
						  if($fage !='' && $tage ==''){
							$age_range = '≥'.$fage.' '.$age_type;
						  }
						}
					 }
					 
					
					 //normal ranges
					 $min = isset($f->normal_value1) && $f->normal_value1!=''?$f->normal_value1:'';
					 $max = isset($f->normal_value2) && $f->normal_value2!=''?$f->normal_value2:'';
					 $range='';
					 if($min !='' && $max !=''){
						
						$range=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$min.'-'.$max:$min.'-'.$max;
						}else{
							if($min=='' && $max!=''){
								$range=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$max:"<".$max;
							}else{
							  if($max=='' && $min!=''){
								$range=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$min:"≥".$min;
							  }else{
							   if($min=='' && $max==''){
								   
								   if(isset($f->descrip)&& $f->descrip!=''){
									  $descrip = $f->descrip;
									  //$range = $descrip;
									  $range='<div style="white-space:pre-line;">'.$descrip.'</div>'; 
								   }
								 }
							  }
						   } 	
						
						}
						
						
						if($gender!=''){
							if($age_range!=''){
								 
								  $one_ref_range .= '<div>'.$age_range.' : '.$range.'</div>';	
								 
								 if(strpos($ref_range, $gender) !== false){
								   $ref_range .= '<div>'.$age_range.' : '.$range.'</div>';	 
								 }else{
								   $ref_range .= '<div>'.$gender.' ( '.$age_range.' ) '.': '.$range.'</div>';
								 }
							}else{
								 
								   $one_ref_range .= '<div>'.$range.'</div>';	
								 
								 if(strpos($ref_range, $gender) !== false){
									$ref_range .= '<div>'.$range.'</div>'; 
								 }else{
									$ref_range .= '<div>'.$gender.' : '.$range.'</div>';
								 }
							}
						  }else{
							if($age_range!=''){
							  $ref_range .= '<div>'.$age_range.' : '.$range.'</div>';
							  
								  $one_ref_range .= '<div>'.$age_range.' : '.$range.'</div>';	
								
							}else{
							  $ref_range .= '<div>'.$range.'</div>';
							   
								   $one_ref_range .= '<div>'.$range.'</div>';
								   
							 }	
						 }
						 
					
								
						if(isset($f->remark) && $f->remark!=''){
							$remark = $f->remark;
							$ref_range.= '<div style="white-space:pre-line;">'.$remark.'</div>';
							
						}
						
						
					  }
			
					 }
	            }
	
  $unit = self::replaceGreekSymbols($unit);
  return array('ref_range'=>$ref_range,'unit'=>$unit,'one_ref_range' => $one_ref_range);
}

private function replaceGreekSymbols($unit){
	$greekSymbols = array(
        'α' => '&alpha;',
        'β' => '&beta;',
        'γ' => '&gamma;',
        'δ' => '&delta;',
        'ε' => '&epsilon;',
        'ζ' => '&zeta;',
        'η' => '&eta;',
        'θ' => '&theta;',
        'ι' => '&iota;',
        'κ' => '&kappa;',
        'λ' => '&lambda;',
        'μ' => '&micro;',
        'ν' => '&nu;',
        'ξ' => '&xi;',
        'ο' => '&omicron;',
        'π' => '&pi;',
        'ρ' => '&rho;',
        'σ' => '&sigma;',
        'τ' => '&tau;',
        'υ' => '&upsilon;',
        'φ' => '&phi;',
        'χ' => '&chi;',
        'ψ' => '&psi;',
        'ω' => '&omega;',
        'Α' => '&Alpha;',
        'Β' => '&Beta;',
        'Γ' => '&Gamma;',
        'Δ' => '&Delta;',
        'Ε' => '&Epsilon;',
        'Ζ' => '&Zeta;',
        'Η' => '&Eta;',
        'Θ' => '&Theta;',
        'Ι' => '&Iota;',
        'Κ' => '&Kappa;',
        'Λ' => '&Lambda;',
        'Μ' => '&Mu;',
        'Ν' => '&Nu;',
        'Ξ' => '&Xi;',
        'Ο' => '&Omicron;',
        'Π' => '&Pi;',
        'Ρ' => '&Rho;',
        'Σ' => '&Sigma;',
        'Τ' => '&Tau;',
        'Υ' => '&Upsilon;',
        'Φ' => '&Phi;',
        'Χ' => '&Chi;',
        'Ψ' => '&Psi;',
        'Ω' => '&Omega;'
        
    );

    return str_replace(array_keys($greekSymbols), array_values($greekSymbols), $unit);

}

public function opTemplateData($lang,Request $request){
	$id = $request->id;
	$user_num =  auth()->user()->id;
	switch($request->type){
	case 'update':
	  DB::table('tbl_visits_order_template_reports')->where('id',$id)->update(['description'=>$request->description,'user_num'=>$user_num]);
      return response()->json(['msg'=>__('Updated Successfully')]);
	break;
    case 'cancel':
	  $description = DB::table('tbl_visits_order_template_reports')->where('id',$id)->value('description');
      return response()->json(['description'=>$description]);
	break;
   case 'print':
     $template = DB::table('tbl_visits_order_template_reports as tr')
	           ->select('tr.id as template_id','t.test_name','tr.clinic_num','tr.order_id','tr.patient_num','tr.description',DB::raw("IFNULL(cat.descrip,'') as cat_name"))
			   ->join('tbl_lab_tests as t','t.id','tr.test_id')
			   ->leftjoin('tbl_lab_categories as cat','cat.id','t.category_num')
			   ->where('tr.id',$id)->first();
	
	//dd($template);
	$order = LabOrders::find($template->order_id);
	$patient = Patient::find($order->patient_num);
	$doctor = Doctor::where('doctor_user_num',$order->ext_lab)->first();
	$ext_lab = ExtLab::where('id',$order->ext_lab)->first();
	$branch = Clinic::find($order->clinic_num);
	$tel = isset($branch->telephone) && $branch->telephone!=''?'Tel: '.$branch->telephone.' , ':'';
	$whatsapp = isset($branch->whatsapp) && $branch->whatsapp!=''?'Whatsapp: '.$branch->whatsapp.' , ':'';
	$website = isset($branch->website) && $branch->website!=''?' '.$branch->website.' , ':'';
	$address = isset($branch->full_address) && $branch->full_address!=''?'Address: '.$branch->full_address:'';
    $branch_data = $tel.$whatsapp.$website.$address;
    //$general_sig = DB::table('tbl_clinics_signatures')->where('clinic_num',$order->clinic_num)->where(DB::raw('trim(type)'),'results')->where('active','Y')->first();

    $logo = DB::table('tbl_bill_logo')->where('status','O')->where('clinic_num',$order->clinic_num)->first();	
    
	$descrip = $template->description;
	$descrip = str_replace('&nbsp;',' ',$descrip);
	
	$data = ['patient'=>$patient,'lab'=>$branch,'doctor'=>$doctor,'ext_lab'=>$ext_lab,'order'=>$order,'branch_data'=>$branch_data,
			'template'=>$template,'logo'=>$logo,'descrip'=>$descrip];
    $htmlContent = view('lab.visit.templatePDF',$data)->render();
    $dompdf = new Dompdf();
    $dompdf->loadHtml($htmlContent);
    $options = new Options();
	$options->set('defaultFont', 'sans-serif');
	$options->set('isHtml5ParserEnabled', true);
	$options->set('isRemoteEnabled', true);
	$options->set('isJavascriptEnabled', true);
	$dompdf->setOptions($options);
	$dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
	//$canvas = $dompdf->getCanvas(); 
	//$canvas->page_text(250, $canvas->get_height() - 30, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 8, [0, 0, 0]);
    return $dompdf->stream('document.pdf');			
    //$htmlContent = view('lab.visit.templatePDF', $data)->render();      
	/*$pdfPath = storage_path('app/7mJ~33/templatedocs/tmp');
    if (!file_exists($pdfPath)){ mkdir($pdfPath, 0775, true);}
    $pdf_name = "template_".uniqid().".pdf";
	$pdfFilePath = $pdfPath.'/'.$pdf_name;
    $pdfData = shell_exec("echo '$htmlContent' | wkhtmltopdf - -");
    file_put_contents($pdfFilePath,$pdfData);
    //delete tmp existing files
				 $files = glob($pdfPath.'*'); // get all file names
					foreach($files as $file){ // iterate files
					  if(is_file($file)) {
						unlink($file); // delete file
					  }
					}*/    
	/*$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true,'isPHPEnabled'=>true])
                       ->loadView('lab.visit.templatePDF', ['template'=>$template]);
    $dom_pdf = $pdf->getDomPDF();
	$canvas = $dom_pdf->get_canvas();
	$canvas->page_text(250, $canvas->get_height() - 65, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 8, [0, 0, 0]);	
    return $pdf->stream();*/
    /*return response($pdfData)
            ->header('Content-Type', 'application/pdf');*/
   break;   
	}
}



private function escape_newline($str)
	{
		
		$str = str_replace("\r\n", "*BR*", $str);
		return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
	}



public function GetPay($lang,Request $request){
$type = $request->type;
$ReqPay=TblBillPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_bill_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_bill_payment.status','Y')
			->where('bill_num',$request->bill_id)
			->where('payment_type',$type)
            ->get(['tbl_bill_payment.*', 'tbl_bill_payment_mode.name_fr', 'tbl_bill_payment_mode.name_eng']);
$cptpCount= $ReqPay->count();		
$cptp = 1;
$html1='<thead>
		<tr class="txt-bg text-white text-center">
		<th scope="col" style="font-size:16px;">'.__("#").'</th>
		<th scope="col" style="font-size:16px;">'.__("Date").'</th>
		<th scope="col" style="font-size:16px;">'.__("Guarantor").'</th>
		<th scope="col" style="font-size:16px;">'.__("Type").'</th>
		<th scope="col" style="font-size:16px;">'.__("Currency").'</th>
		<th scope="col" style="font-size:16px;">'.__("Amount").'</th>
		<th scope="col" style="font-size:16px;">'.__("Rate").'</th>
		<th scope="col" style="font-size:16px;">'.__("Amount LBP").'</th>
		<th scope="col"></th>
		<th scope="col" style="display:none;"></th>
		</tr>
		</thead>
		<tbody>';
if($type=='P'){
	foreach ($ReqPay as $sReqPays) {
			$type_name = ($lang=='fr')?$sReqPays->name_fr:$sReqPays->name_eng;
			$guarantor_name = DB::table('tbl_external_labs')->where('id',$sReqPays->guarantor)->value('full_name');
			$html1 .='<tr>
			<td>'.$cptp.'</td>
			<td>'.$sReqPays->datein.'</td>
			<td>'.$guarantor_name.'</td>
			<td>'.$type_name.'</td>
			<td>'.$sReqPays->currency.'</td>
			<td>'.$sReqPays->payment_amount.'</td>
			<td>'.$sReqPays->dolarprice.'</td>
			<td>'.$sReqPays->lpay_amount.'</td>
			<td><input type="button" class="btn btn-delete" id="rowdeletepay'.$cptp.'" value="'.__("Delete").'" onclick="deleteRowPay(this)"  /></td>
			<td style="display:none">'.$sReqPays->guarantor.'</td>
			</tr>';
	$cptp++;
	}
	$html1 .='</tbody>';
}elseif($type=='R'){
		foreach ($ReqPay as $sReqRefs) {
			$type_name = ($lang=='fr')?$sReqRefs->name_fr:$sReqRefs->name_eng;
            $guarantor_name = DB::table('tbl_external_labs')->where('id',$sReqRefs->guarantor)->value('full_name');
			$html1 .='<tr>
			<td>'.$cptp.'</td>
			<td>'.$sReqRefs->datein.'</td>
			<td>'.$guarantor_name.'</td>
			<td>'.$type_name.'</td>
			<td>'.$sReqRefs->currency.'</td>
			<td>'.$sReqRefs->payment_amount.'</td>
			<td>'.$sReqRefs->dolarprice.'</td>
			<td>'.$sReqRefs->lpay_amount.'</td>
			<td><input type="button" class="btn-btn-delete" id="rowdeleteref'.$cptp.'" value="'.__("Delete").'" onclick="deleteRowRef(this)"  /></td>
			<td style="display:none">'.$sReqRefs->guarantor.'</td>
			</tr>';
	    $cptp++;
	  }
	  $html1 .='</tbody>';
}elseif($type=='D'){
	foreach ($ReqPay as $sReqDis) {
			$type_name = ($lang=='fr')?$sReqDis->name_fr:$sReqDis->name_eng;
            $guarantor_name = DB::table('tbl_external_labs')->where('id',$sReqDis->guarantor)->value('full_name');
			$html1 .='<tr>
			<td>'.$cptp.'</td>
			<td>'.$sReqDis->datein.'</td>
			<td>'.$guarantor_name.'</td>
			<td>'.$type_name.'</td>
			<td>'.$sReqDis->currency.'</td>
			<td>'.$sReqDis->payment_amount.'</td>
			<td>'.$sReqDis->dolarprice.'</td>
			<td>'.$sReqDis->lpay_amount.'</td>
			<td><input type="button" class="btn-btn-delete" id="rowdeletedis'.$cptp.'" value="'.__("Delete").'" onclick="deleteRowDis(this)"  /></td>
			<td style="display:none">'.$sReqDis->guarantor.'</td>
			</tr>';
	    $cptp++;
	  }
}
	


$sumpay=TblBillPayment::where('bill_num','=',$request->bill_id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
$sumref=TblBillPayment::where('bill_num','=',$request->bill_id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');

$sumdis=TblBillPayment::where('bill_num','=',$request->bill_id)->where('status','Y')->where('payment_type','=','D')->sum('lpay_amount');

$Nsumpay=number_format((float)$sumpay, 2, '.', ',');
$sumpay=floatval(preg_replace('/[^\d.-]/', '', $Nsumpay));
$Nsumref=number_format((float)$sumref, 2, '.', ',');
$sumref=floatval(preg_replace('/[^\d.-]/', '', $Nsumref));

$Nsumdis=number_format((float)$sumdis, 2, '.', ',');
$sumdis=floatval(preg_replace('/[^\d.-]/', '', $Nsumdis));

$ReqBill=TblBillHead::where('id',$request->bill_id)->where('status','O')->first();
$bill_discount=floatval(preg_replace('/[^\d.-]/', '', $ReqBill->bill_discount));
$totall = $ReqBill->lbill_total;

$Nbalance=number_format((float)$totall-$sumdis-$sumpay+$sumref+$ReqBill->tvq+$ReqBill->tps, 2, '.', ',');
$balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));


$sumpayd=TblBillPayment::where('bill_num','=',$request->bill_id)->where('status','Y')->where('payment_type','=','P')->sum('dpay_amount');
$sumrefd=TblBillPayment::where('bill_num','=',$request->bill_id)->where('status','Y')->where('payment_type','=','R')->sum('dpay_amount');

$sumdisd=TblBillPayment::where('bill_num','=',$request->bill_id)->where('status','Y')->where('payment_type','=','D')->sum('dpay_amount');

$Nsumpayd=number_format((float)$sumpayd, 2, '.', ',');
$sumpayd=floatval(preg_replace('/[^\d.-]/', '', $Nsumpayd));
$Nsumrefd=number_format((float)$sumrefd, 2, '.', ',');
$sumrefd=floatval(preg_replace('/[^\d.-]/', '', $Nsumrefd));

$Nsumdisd=number_format((float)$sumdisd, 2, '.', ',');
$sumdisd=floatval(preg_replace('/[^\d.-]/', '', $Nsumdisd));

$ReqBill=TblBillHead::where('id',$request->bill_id)->where('status','O')->first();
$bill_discount_us=floatval(preg_replace('/[^\d.-]/', '', $ReqBill->bill_discount_us));
$totald = $ReqBill->bill_total;
$Nbalanced=number_format((float)$totald-$sumdisd-$sumpayd+$sumrefd+$ReqBill->tvq+$ReqBill->tps, 2, '.', ',');
$balanced=floatval(preg_replace('/[^\d.-]/', '', $Nbalanced));


return response()->json(['html1' => $html1,'sumpay'=>$sumpay,'sumref'=>$sumref,'balance'=>$balance,
                         'sumpayd'=>$sumpayd,'sumrefd'=>$sumrefd,'balanced'=>$balanced,'sumdisd'=>$sumdisd,'sumdis'=>$sumdis,
						 'totall'=>$totall,'totald'=>$totald,'discount'=>$bill_discount,'discountd'=>$bill_discount_us]);
	
}

public function sms_link($lang,Request $request){
	$hashedValidator = $request->key;
	$auth=DB::table('tbl_order_reminders_key')->select('id','order_id','secret_key','active','remind_by','created_at')
                                              ->where('active','Y')->where('secret_key',$hashedValidator)->first();
										  
	if(isset($auth)){
		if ($auth->active === 'N') {
            abort(403, 'The requested link has expired.');
        }
		
				
		$currentDate = Carbon::now();
		$createdAt = Carbon::parse($auth->created_at);
			
		$differenceInHours = $createdAt->diffInHours($currentDate);
		//4days = 96 hours
		if ($differenceInHours>96) {
           DB::table('tbl_order_reminders_key')->where('id',$auth->id)->update(['active'=>'N']);
           abort(403, 'The requested link has expired and was valid for only 4 days, and that time has passed.');
        }
		
		$pdf_path = LabOrders::where('id',$auth->order_id)->value('pdf_path');
		$pdf_path = storage_path('app/7mJ~33/'.$pdf_path);
		
		if(!file($pdf_path)){
			 abort(403, 'The requested file does not exist.');
		}
				
		$pdfContent = file_get_contents($pdf_path);
		$mimeType = mime_content_type($pdf_path);
		
		return response($pdfContent)
            ->header('Content-Type', $mimeType)
             ->header('Content-Disposition', 'inline; filename="' . basename($pdf_path) . '"');
		
	}else{
		abort(403, 'The requested link has expired.');
        
	}										  



}


private function create_machine_files($order_id,$tests){
  $user_num = auth()->user()->id;
  
  if(count($tests)>0){	
	 $order = LabOrders::find($order_id);
	 $patient = Patient::find($order->patient_num);
     $dob = explode('-',$patient->birthdate);
     $dob=$dob[2].$dob[1].$dob[0];	
	 $pat_name = strtoupper(trim($patient->first_name));
	 if(isset($patient->middle_name) && $patient->middle_name!=''){
		$pat_name.=' ';
		$pat_name.=strtoupper(trim($patient->middle_name));
	   }
	 $pat_name.=' '.strtoupper(trim($patient->last_name));
	 $gender = trim($patient->sex);
	 $lis_codes = [];
	 	 
	 foreach($tests as $d){
		$tid = intval($d);
		$lisCode = LabTests::where('id',$tid)->value('listcode');
		$test_code = LabTests::where('id',$tid)->value('test_code');
		//create a file and directory for each lisCode uncreated
		if(isset($lisCode) && $lisCode!=''){
			$arr = explode('_',$lisCode);
			// Find the position of the first underscore
            $pos = strpos($lisCode, '_');
			if ($pos !== false) {
				$code = substr($lisCode, 0, $pos);
				$name = substr($lisCode, $pos + 1);
			}
									
			if (!isset($lis_codes[$name])) {
             $lis_codes[$name] = [];
            }
			$lis_codes[$name][] = $code;
	      }
      }
 
   
	 
	//now lis_codes contains all codes where machine name as array
	 if(count($lis_codes)){
		 $request_nb = $order->request_nb;
		 $fileName = 'Request_'.$request_nb.'.txt'; 
		 foreach ($lis_codes as $key => $codes) {
			$folderPath = storage_path('app/7mJ~33/Machines/Sent').'/'.$key;
			if (!is_dir($folderPath)) {
				mkdir($folderPath, 0777, true);
			}
			$filePath = $folderPath.'/'.$fileName;
			$data="REQUEST ID, NAME,SEX,CODE,DOB\n";
			foreach($codes as $code){
				$data.=$request_nb.",".$pat_name.",".$gender.",".$code.",".$dob.",\n";
			}
			file_put_contents($filePath, $data);
		 }
	 }

 }
}

public function import_machine_results($lang,Request $request){
	$order_id = $request->order_id;
	$order = LabOrders::find($order_id);
	$user_num = auth()->user()->id;
    $state = 'U';
	$sign = '';
	$import_date_time ='';
	$request_nb = $order->request_nb;
	$folderPath = storage_path('app/7mJ~33/Machines/Receive');
	if (!is_dir($folderPath)) { mkdir($folderPath, 0777, true); }
	$filename = 'Request_'.$request_nb;
	
	$files = glob($folderPath . '/' . $filename . '.{txt,TXT,csv,CSV}', GLOB_BRACE);
	
		if (!empty($files)) {
			usort($files, function($a, $b) {return filemtime($a) < filemtime($b);});

			$latestFile = $files[0];
			
			if (file_exists($latestFile)) {
			 $results = [];
			 $file = fopen($latestFile, 'r');
			 fgets($file);
			
			 while (!feof($file)) {
				$line = fgets($file);
				if (!empty($line)) {
					 $fields = explode(',', $line);
					 
					 
					 $results[] = [
								'ID' => $fields[0],
								'DATE' => $fields[1],
								'TIME' => $fields[2],
								'CODE' => trim($fields[3]),
								'RESULT' => trim($fields[4])
                                ];
				     
				    
				}
			}
			//dd($results);
			// Close the file
            fclose($file);
			$user_num = auth()->user()->id;
			$state = 'U';
	        $sign = '';
			 $import_date_time='';
			foreach($results as $data){
				if($data["ID"]==$request_nb || $data["ID"]==$order_id){
					$code = trim($data["CODE"]);
					$result = $data["RESULT"];
					$test_id = LabTests::where('listcode','like',$code.'_'.'%')->value('id');
					if($import_date_time==''){
					 $date = explode('/',$data["DATE"]);
					 $time = $data["TIME"];
					 $import_date_time = Carbon::parse($date[2].'-'.$date[1].'-'.$date[0].' '.$time)->format('Y-m-d H:i');
					}
					if(isset($test_id)){
					  
					     //get result id and field number
				         $res = LabResults::where('active','Y')->where('order_id',$order_id)->where('test_id',$test_id)->first(); 
	      	             
						 $field_num = isset($res) && isset($res->field_num) && $res->field_num!=''?explode(',',$res->field_num):NULL;
				         
						 if(isset($res) && isset($field_num)){
				             $test_states = $this->getTestState($res->id,$result,$field_num);
				             if(count($test_states)){
					           $state = $test_states["state"];
					           $sign = $test_states["sign"];
					          }
				           }
					     
						if(isset($res)){
						  if(is_numeric($result)){ 
						   LabResults::where('id',$res->id)->update(['result_txt'=>NULL,'result'=>$result,'sign'=>$sign,'result_status'=>$state,'user_num'=>$user_num]);
					        }else{
						  //result is a text
						  LabResults::where('id',$res->id)->update(['result_txt'=>$result,'result'=>NULL,'sign'=>$sign,'result_status'=>$state,'user_num'=>$user_num]);
					       }
						}		   
				    }
				}
			}
			
			if($import_date_time!=''){
				LabOrders::where('id',$order_id)->update(['user_num'=>$user_num,'import_date_time'=>$import_date_time]);
			}
			
			//delete last file found
			unlink($latestFile);
			
			$location = route('lab.visit.edit',[$lang,$order_id]);
			return response()->json(['success'=>__('Results are imported succssfully'),'location'=>$location]);
			}else{
			return response()->json(['error'=>__('File not found in order to import results from it!')]);	
			}
		}else{
			return response()->json(['error'=>__('File not found in order to import results from it!')]);
		}
		
	
}


public function request_codes($lang,Request $request){
	
	$coll_notes = LabOrders::where('id',$request->order_id)->value('coll_notes');
	$guarantor_id = LabOrders::where('id',$request->order_id)->value('ext_lab');
	
	$codes = DB::table('tbl_visits_order_custom_tests as ct')
             ->select('ct.id as id','t.testord as testord', 't.cnss as cnss', 't.test_name as test_name', 
			          't.is_group as is_group','t.specimen','t.special_considerations',
					  't.preanalytical','ct.test_id as test_id','t.test_code',
					  'ct.is_test_collected','ct.collected_test_date',
					  'o.chk_specialcons as chk_specialcons','o.chk_specimens as chk_specimens',
					  DB::raw("IFNULL(p.totald,'') as price_usd"),DB::raw("IFNULL(p.totall,'') as price_lbl"))
             ->join('tbl_lab_tests as t','t.id','ct.test_id')
			 ->join('tbl_visits_orders as o','o.id','ct.order_id')
			 ->leftjoin('tbl_external_labs_prices as p',function($q) use($guarantor_id){
				 $q->on('p.test_id','ct.test_id');
				 $q->where('p.lab_id',$guarantor_id);
			 })
			 ->where('ct.order_id',$request->order_id)
			 ->orderBy('t.testord')
             ->get();
       
    return response()->json(['table_data'=>$codes,'coll_notes'=>$coll_notes]);
}

public function saveGuarantorOrder($lang,Request $request){
	$clinic_num = $request->clinic_num;
	$patient_num = $request->patient_num;
	$ext_lab = $request->ext_lab;
	$user_num = auth()->user()->id;
	//get order tests without sub groups and save them
	$tstData = json_decode($request->input('tests'), true);
	$patient = Patient::find($patient_num);
	$doctor_num = isset($patient->doctor_num) && $patient->doctor_num!=''?$patient->doctor_num:NULL;
	//process to save order with status in progress
	//get file number, active:P means in progress the order
	$nextRequestNb = LabOrders::generateRequestNb();
	//create new order
		$order_id = LabOrders::create([
		  'clinic_num'=>$clinic_num,
		  'patient_num'=>$patient_num,
		  'ext_lab' =>$ext_lab,
		  'doctor_num'=>$doctor_num,
		  'user_num'=>$user_num,
		  'order_datetime'=>Carbon::now()->format('Y-m-d H:i'),
		  'status'=>'I',
		  'is_trial'=>'N',
		  'active' => 'Y',
		  'request_nb'=>$nextRequestNb
		  ])->id;
	   $order = LabOrders::find($order_id);
	   
	   //go test by test the codes without sub groups
	   foreach($tstData as $d){
		$tid = intval($d['test_id']);
		$test = LabTests::find($tid);
		$rtst = isset($d['referred_test']) && $d['referred_test']!=''?intval($d['referred_test']):(isset($test) && $test->referred_tests?$test->referred_tests:NULL);
		//custom tests section
		DB::table('tbl_visits_order_custom_tests')->insert([
		  'user_num'=>$user_num,
		  'order_id'=>$order_id,
		  'test_id'=>$test->id,
		  'clinic_num'=>$order->clinic_num,
		  'patient_num'=>$order->patient_num,
		  'referred_lab'=>$rtst,
		  'insert_date'=>Carbon::now()->format('Y-m-d H:i'),
		  'active'=>'Y'
		 ]);
		}
  return response()->json(['msg'=>__('Request saved successfully')]);
	
}

public function acceptRequest($lang,Request $request){
	$id = $request->id;
	$user_num = auth()->user()->id;
	$currencyUSD=TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
    $lbl_usd = isset($currencyUSD)?$currencyUSD->price:90000;
	//update status from I: In Progress to P:pending and active from P:Pending to Y:Accepted
	LabOrders::where('id',$id)->update(['status'=>'P','user_num'=>$user_num]);
	//if accepted continue creating all needed data to see Request
	$order = LabOrders::find($id);
	//get saved tests by guarantor
	$tstData = DB::table('tbl_visits_order_custom_tests')->where('order_id',$id)->where('active','Y')->pluck('test_id')->toArray();
	  //create bill head for order
    $bill_id = TblBillHead::create([
	     'order_id'=>$order->id,
		 'ext_lab'=>$order->ext_lab,
		 'clinic_num'=>$order->clinic_num,
		 'doctor_num'=>$order->doctor_num,
		 'patient_num'=>$order->patient_num,
		 'bill_datein'=>Carbon::now()->format('Y-m-d H:i'),
		 'user_num'=>$user_num,
		 'user_type'=>auth()->user()->type,
		 'status'=>'O',
		 'exchange_rate'=>$lbl_usd
	    ])->id;
		
		 //Add new serial for bill
	    $lab = Clinic::find($order->clinic_num);
      	$SerieFacBill = $lab->bill_serial_code;
		$SeqFacBill = $lab-> bill_sequence_num ;
		$reqID=trim($SerieFacBill)."-".($SeqFacBill+1);
		Clinic::where('id',$order->clinic_num)->update(['bill_sequence_num' => $SeqFacBill+1]);
		TblBillHead::where('id',$bill_id)->update(['clinic_bill_num'=>$reqID]);
        
		$tbillpricel=0.00;
        $tbillpriced=0.00;
		$tbillpricee=0.00;
        $guarantor_valid = ExtLab::where('id',$order->ext_lab)->value('is_valid');
		
	   //go test by test the codes without sub groups
	   foreach($tstData as $tid){
		
		$test = LabTests::find($tid);
		
		//bill section
		if($test->cnss !=NULL && $test->cnss !=''){
		   $priced = $pricel = $pricee =0;
		   $pr = DB::table('tbl_external_labs_prices')->where('lab_id',$order->ext_lab)->where('test_id',$test->id)->first(); 
				if(isset($pr)){
						   if(isset($pr->totald) && $pr->totald!='' && $pr->totald!=0){
										$priced = $pr->totald;
								}
							  if(isset($pr->totall) && $pr->totall!='' && $pr->totall!=0){
										$pricel = $pr->totall; 
								}
							if(isset($pr->totale) && $pr->totale!='' && $pr->totale!=0){
										$pricee = $pr->totale; 
									} 
						 }
					 
					 $priced = number_format((float)$priced,2,'.','');
		             $pricel = number_format((float)$pricel,2,'.','');
			         $pricee = number_format((float)$pricee,2,'.','');
					
					TblBillSpecifics::create([
					   'bill_num'=>$bill_id,
					   'user_num'=>$user_num,
					   'user_type'=>auth()->user()->type,
					   'bill_code'=>$test->id,
					   'cnss'=>$test->cnss,
					   'bill_name'=>$test->test_name,
					   'bill_quantity'=>$test->nbl,
					   'lbill_price'=>$pricel,
					   'bill_price'=>$priced,
					   'ebill_price'=>$pricee,
					   'status'=>'O'
					  ]);
					  
					  $tbillpricel=$tbillpricel+ $pricel;
					  $tbillpriced=$tbillpriced+ $priced;
					  $tbillpricee=$tbillpricee+ $pricee;
				    }
		
	
	
	            }

        //update bill totals
        $bill = TblBillHead::find($bill_id);
		$sumpay=DB::table('tbl_bill_payment')->where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
        $sumref=DB::table('tbl_bill_payment')->where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');
        $tbalance = $tbillpricel-$sumpay+$sumref+$bill->tvq+$bill->tps;
        $Nbalance=number_format((float)$tbalance, 2, '.', ',');
        $balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));
		
		$sumpayd = DB::table('tbl_bill_payment')->where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('dpay_amount');
		$sumrefd = DB::table('tbl_bill_payment')->where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('dpay_amount');
		$tbalanced = $tbillpriced-$sumpayd+$sumrefd+$bill->tvq+$bill->tps;
        $Nbalanced=number_format((float)$tbalanced, 2, '.', ',');
        $balanced=floatval(preg_replace('/[^\d.-]/', '', $Nbalanced));
		
		TblBillHead::where('id',$bill_id)->update(['bill_balance'=>$balance,'bill_balance_us'=>$balanced,'lbill_total'=>$tbillpricel,'bill_total'=>$tbillpriced,'ebill_total'=>$tbillpricee]);			
	   
	   $result_tests = LabTests::where(function($q) use($tstData){
					     $q->where('is_group','<>','Y');
					     $q->whereIn('id',$tstData);
					     })->orWhere(function($q) use($tstData){
					          $q->where('is_group','<>','Y');
					          $q->whereIn('group_num',$tstData);
				         })->where('active','Y')->pluck('id')->toArray();
	 
	 //create results for new order for each test
	   foreach($result_tests as $ord){
		    $test = LabTests::find($ord);
			$subgroup_order = $test->testord;
			$group_order = NULL;
			$group_num = NULL;
			if(isset($test->group_num) &&  $test->group_num!=''){
			 $group_order = LabTests::where('id',$test->group_num)->value('testord');
			 $group_num = $test->group_num;
			}
			if($test->is_culture=='Y'){
				//create culture data
                DB::table('tbl_order_culture_results')->insert(['order_id'=>$order->id,'test_id'=>$test->id,
	                             'user_num'=>$user_num,'clinic_num'=>$order->clinic_num,'active'=>'Y',
								 'patient_num'=>$order->patient_num,'ext_lab' =>$order->ext_lab]);
				
			}else{
			    //check if there is a previous result id
				$prev_result_id = LabResults::join('tbl_visits_orders as o','o.id','tbl_visits_order_results.order_id')
							      ->where('o.clinic_num',$order->clinic_num)
							      ->where('o.patient_num',$order->patient_num)
							      ->where('o.id','<>',$order->id)
								  ->where('o.order_datetime','<',$order->order_datetime)
							      ->where('o.active','Y')
							      ->where('tbl_visits_order_results.active','Y')
							      ->where('tbl_visits_order_results.test_id',$test->id)
							      ->orderBy('tbl_visits_order_results.id','desc')
								  ->orderBy('tbl_visits_order_results.created_at','desc')
							       ->value('tbl_visits_order_results.id');
					   
				$need_validation = ($guarantor_valid=='Y' || $test->is_valid=='Y')?'Y':'N';
				$result_id = LabResults::create([
				 'user_num'=>$user_num,
				 'clinic_num'=>$order->clinic_num,
				 'patient_num'=>$order->patient_num,
				 'order_id'=>$order->id,
				 'test_id'=>$test->id,
				 'prev_result_num'=>isset($prev_result_id)?$prev_result_id:NULL,
				 'active'=>'Y',
				 'group_order'=>$group_order,
				 'subgroup_order'=>$subgroup_order,
				 'group_num'=>$group_num,
				 'need_validation'=>$need_validation
				])->id;
				
				
					 $patient = Patient::find($order->patient_num);
					 $field_id = $this->getFieldRefRange($result_id);
					 $ref_data = $this->getRefUnit($patient->sex,$test->id,$test->is_printed,$field_id);
					 
					 LabResults::where('id',$result_id)->update(['ref_range'=>$ref_data['ref_range'],'one_ref_range'=>$ref_data['one_ref_range'],
					                                             'unit'=>$ref_data['unit'],'field_num'=>implode(',',$field_id)
																 ]);
					
				
			}
		 }
		
	//all results are saved,bill is saved , status='P' and active='Y'
	return response()->json(['msg'=>__('Accepted Successfully')]);
}

public function rejectRequest($lang,Request $request){
	$id = $request->id;
	$user_num = auth()->user()->id;
	$note = isset($request->note) && $request->note!=''?trim($request->note):NULL;
	$other_note = isset($request->other_note) && $request->other_note!=''?trim($request->other_note):NULL;
	//update active from P:Pending to N:Cancelled
    //update reject note
	LabOrders::where('id',$id)->update(['reject_note'=>$note,'other_reject_note'=>$other_note,'active'=>'N','user_num'=>$user_num]);
	DB::table('tbl_visits_order_custom_tests')->where('order_id',$id)->update(['active'=>'N','user_num'=>$user_num]);
	//TblBillHead::where('order_id',$id)->update(['status'=>'N','user_num'=>$user_num]);
	$msg= __('Cancelled Successfully');
	return response()->json(['msg'=>$msg]);
}

public function chkReceiveFile($lang,Request $request){
	$id = $request->id;
	$request_nb=LabOrders::where('id',$id)->value('request_nb');
	$folderPath = storage_path('app/7mJ~33/Machines/Receive');
	$fileExists=false;
	$filename = 'Request_'.$request_nb;
	$files = glob($folderPath . '/' . $filename . '.{txt,TXT,csv,CSV}', GLOB_BRACE);
	if(!empty($files)){ $fileExists=true;}
	return response()->json(['fileExists'=>$fileExists]);
}

}

