<?php
/*
* DEV APP
* Created date : 10-11-2022
*  
* update functions from time to time till today
*
*/
namespace App\Http\Controllers\reports;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\EventStates;
use App\Models\ExtLab;
use DB;
use Illuminate\Http\Request;
use Alert;
use DataTables;
use Session;
use UserHelper;
use PDF;

//Created date 10-5-2023
class ReportsController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index($lang) 
    {
	
	return view('reports.menu.index');	
	}
	



public function outreachRequests($lang,Request $request){
	
 $user_type=auth()->user()->type;
 $myId = auth()->user()->id;
 $idClinic = auth()->user()->clinic_num;
 $resource = Clinic::select('id','full_name')->where('id',$idClinic)->first(); 
 $reject_notes = DB::table('tbl_visits_orders')->whereNOTNULL('reject_note')->where('reject_note','<>','')->distinct()->pluck('reject_note');

 if($user_type==3){
		$ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$myId)->get();
	    $ext_lab_id = ExtLab::select('id')->where('lab_user_num',$myId)->value('id');
		$Patients = Patient::where('clinic_num',$idClinic)->where('ext_lab',$ext_lab_id)->where('status','O')->orderBy('id','desc')->get();
	   }else{
	    $ext_labs=ExtLab::select('id','full_name')->orderBy('full_name')->get();
		$Patients = Patient::where('clinic_num',$idClinic)->where('status','O')->orderBy('id','desc')->get();
		}

if($request->ajax()){	
	 
    $results= DB::table('tbl_visits_orders')
	          ->selectRaw('tbl_visits_orders.id as order_id,
			               DATE_FORMAT(tbl_visits_orders.order_datetime,"%Y-%m-%d %H:%i") as visit_date_time,
					       DATE_FORMAT(tbl_visits_orders.order_datetime,"%Y-%m-%d") as visit_date,
						   l.full_name as ext_lab_name,tbl_visits_orders.request_nb,
					       IFNULL(CONCAT(doc.first_name,IFNULL(CONCAT(" ",doc.middle_name," ")," "),doc.last_name),"") as doctor_name,
					       CONCAT(patConsult.first_name," ",IFNULL(patConsult.middle_name,"")," ",patConsult.last_name) AS patDetail,
					       IF(tbl_visits_orders.active="N","Rejected","In Progress") as order_status,tbl_visits_orders.reject_note,tbl_visits_orders.other_reject_note'
					       )
			  ->join('tbl_patients as patConsult','patConsult.id','tbl_visits_orders.patient_num')
              ->leftjoin('tbl_doctors as doc','doc.id','tbl_visits_orders.doctor_num')
              ->leftjoin('tbl_external_labs as l','l.id','tbl_visits_orders.ext_lab')
			  ->where('tbl_visits_orders.status','I')
			  ->where('tbl_visits_orders.clinic_num',$idClinic);
              			  
      if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $results=$results->where(DB::raw("DATE(tbl_visits_orders.order_datetime)"),'>=',$request->filter_fromdate);
				}
	  
	  if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $results=$results->where(DB::raw("DATE(tbl_visits_orders.order_datetime)"),'<=',$request->filter_todate);
				 
				}		
	  
	  if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			$results=$results->where(DB::raw("DATE(tbl_visits_orders.order_datetime)"),'>=',$request->filter_fromdate)
			                 ->where(DB::raw("DATE(tbl_visits_orders.order_datetime)"),'<=',$request->filter_todate);
				}
      
      if(isset($request->filter_patient) && $request->filter_patient!="" ){
			  $results=$results->where('tbl_visits_orders.patient_num',$request->filter_patient);		  		  
		    }
	  
	  
	  if(isset($request->filter_resource) && $request->filter_resource!="" ){
			 $results=$results->where('tbl_visits_orders.ext_lab',$request->filter_resource);		  
		    } 
			
      if(isset($request->filter_status) && $request->filter_status!="" ){
			 $results=$results->where('tbl_visits_orders.active',$request->filter_status);		  
		    } 
			
	   if(isset($request->filter_reject_notes) && $request->filter_reject_notes!=""){
		  $reject_note = trim($request->filter_reject_note);
		  $results=$results->where('reject_note','like','%'.$reject_note.'%');
	  }			
			
	  $results=$results->distinct()->orderBy('visit_date','desc')->get();
	  return response()->json($results);	
	   }

  
	  return view('reports.list.daily_outreach_requests')->with(['reject_notes'=>$reject_notes,'ext_labs'=>$ext_labs,'resource'=>$resource,'patients'=>$Patients]);	
	
}

public function results($lang,Request $request){
 $user_type=auth()->user()->type;
 $myId = auth()->user()->id;
 $idClinic = auth()->user()->clinic_num;
 $resource = Clinic::select('id','full_name')->where('id',$idClinic)->first(); 
 

 if($user_type==3){
		$ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$myId)->get();
	    $ext_lab_id = ExtLab::select('id')->where('lab_user_num',$myId)->value('id');
		$Patients = Patient::where('clinic_num',$idClinic)->where('ext_lab',$ext_lab_id)->where('status','O')->orderBy('id','desc')->get();
	   }else{
	    $ext_labs=ExtLab::select('id','full_name')->orderBy('full_name')->get();
		$Patients = Patient::where('clinic_num',$idClinic)->where('status','O')->orderBy('id','desc')->get();
		}

if($request->ajax()){	
	 
    $results= DB::table('tbl_visits_orders')
	          ->selectRaw('tbl_visits_orders.id as order_id,
			               DATE_FORMAT(tbl_visits_orders.order_datetime,"%Y-%m-%d %H:%i") as visit_date_time,
					       DATE_FORMAT(tbl_visits_orders.order_datetime,"%Y-%m-%d") as visit_date,
						   l.full_name as ext_lab_name,tbl_visits_orders.request_nb,
					       IFNULL(CONCAT(doc.first_name,IFNULL(CONCAT(" ",doc.middle_name," ")," "),doc.last_name),"") as doctor_name,
					       CONCAT(patConsult.first_name," ",IFNULL(patConsult.middle_name,"")," ",patConsult.last_name) AS patDetail,
					       IF(tbl_visits_orders.status="V","Validated",IF(tbl_visits_orders.status="F","Finished","Pending")) as order_status,tbl_visits_orders.reject_note,tbl_visits_orders.other_reject_note'
					   )
			  ->join('tbl_patients as patConsult','patConsult.id','tbl_visits_orders.patient_num')
              ->leftjoin('tbl_doctors as doc','doc.id','tbl_visits_orders.doctor_num')
              ->leftjoin('tbl_external_labs as l','l.id','tbl_visits_orders.ext_lab')
			  ->where('tbl_visits_orders.status','<>','I')
			  ->where('tbl_visits_orders.clinic_num',$idClinic)
			  ->where('tbl_visits_orders.active','Y');
              			  
      if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $results=$results->where(DB::raw("DATE(tbl_visits_orders.order_datetime)"),'>=',$request->filter_fromdate);
				}
	  
	  if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $results=$results->where(DB::raw("DATE(tbl_visits_orders.order_datetime)"),'<=',$request->filter_todate);
				 
				}		
	  
	  if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			$results=$results->where(DB::raw("DATE(tbl_visits_orders.order_datetime)"),'>=',$request->filter_fromdate)
			                 ->where(DB::raw("DATE(tbl_visits_orders.order_datetime)"),'<=',$request->filter_todate);
				}
      
      if(isset($request->filter_patient) && $request->filter_patient!="" ){
			  $results=$results->where('tbl_visits_orders.patient_num',$request->filter_patient);		  		  
		    }
	  
	  
	  if(isset($request->filter_resource) && $request->filter_resource!="" ){
			 $results=$results->where('tbl_visits_orders.ext_lab',$request->filter_resource);		  
		    } 
			
      if(isset($request->filter_status) && $request->filter_status!="" ){
			 $results=$results->where('tbl_visits_orders.status',$request->filter_status);		  
		    }

     
	  $results=$results->distinct()->orderBy('visit_date','desc')->get();
	  return response()->json($results);	
	   }

  
	  return view('reports.list.daily_requests')->with(['ext_labs'=>$ext_labs,'resource'=>$resource,'patients'=>$Patients]);	
	     
		   
} 

public function invoices($lang,Request $request){
 $user_type=auth()->user()->type;
 $myId = auth()->user()->id;
 $idClinic = auth()->user()->clinic_num;
 $resource = Clinic::select('id','full_name')->where('id',$idClinic)->first(); 
 

 if($user_type==3){
		$ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$myId)->get();
	    $ext_lab_id = ExtLab::select('id')->where('lab_user_num',$myId)->value('id');
		$Patients = Patient::where('clinic_num',$idClinic)->where('ext_lab',$ext_lab_id)->where('status','O')->orderBy('id','desc')->get();
	   }else{
	    $ext_labs=ExtLab::select('id','full_name')->orderBy('full_name')->get();
		$Patients = Patient::where('clinic_num',$idClinic)->where('status','O')->orderBy('id','desc')->get();
		}

if($request->ajax()){	
	 
	$results= DB::table('tbl_bill_head')
	          ->selectRaw('tbl_bill_head.id,
			               tbl_bill_head.clinic_bill_num as bill_num,
						   DATE_FORMAT(tbl_bill_head.bill_datein,"%Y-%m-%d %H:%i") as bill_date_time,
					       DATE_FORMAT(tbl_bill_head.bill_datein,"%Y-%m-%d") as bill_date,
						   l.full_name as ext_lab_name,
						   o.request_nb,
						   tbl_bill_head.lbill_total as totalLBP,
						   tbl_bill_head.bill_total as totalUS,
						   tbl_bill_head.bill_balance as soldLBP,
						   tbl_bill_head.bill_balance_us as soldUS,
					       IFNULL(CONCAT(doc.first_name,IFNULL(CONCAT(" ",doc.middle_name," ")," "),doc.last_name),"") as doctor_name,
					       CONCAT(patConsult.first_name," ",IFNULL(patConsult.middle_name,"")," ",patConsult.last_name) AS patDetail'
					     )
			  ->join('tbl_patients as patConsult','patConsult.id','tbl_bill_head.patient_num')
              ->leftjoin('tbl_visits_orders as o','o.id','tbl_bill_head.order_id')
			  ->leftjoin('tbl_doctors as doc','doc.id','tbl_bill_head.doctor_num')
              ->leftjoin('tbl_external_labs as l','l.id','tbl_bill_head.ext_lab')
			  ->where('tbl_bill_head.status','O')
			  ->where('tbl_bill_head.clinic_num',$idClinic);
              			  
      if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $results=$results->where(DB::raw("DATE(tbl_bill_head.bill_datein)"),'>=',$request->filter_fromdate);
				}
	  
	  if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $results=$results->where(DB::raw("DATE(tbl_bill_head.bill_datein)"),'<=',$request->filter_todate);
				 
				}		
	  
	  if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			$results=$results->where(DB::raw("DATE(tbl_bill_head.bill_datein)"),'>=',$request->filter_fromdate)
			                 ->where(DB::raw("DATE(tbl_bill_head.bill_datein)"),'<=',$request->filter_todate);
				}
      
      if(isset($request->filter_patient) && $request->filter_patient!="" ){
			  $results=$results->where('tbl_bill_head.patient_num',$request->filter_patient);		  		  
		    }
	  
	  
	  if(isset($request->filter_resource) && $request->filter_resource!="" ){
			 $results=$results->where('tbl_bill_head.ext_lab',$request->filter_resource);		  
		    } 
			
      if(isset($request->filter_request) && $request->filter_request!=""){
		  if($request->filter_request=="R"){
			 $results=$results->whereRaw('o.request_nb IS NOT NULL and o.request_nb<>""'); 
		  }else{
			 $results=$results->whereRaw('o.request_nb IS NULL or o.request_nb=""'); 
		  }
	  }
	  $results=$results->groupBy('tbl_bill_head.id')->orderBy('bill_date','desc')->get();
	  return response()->json($results);	
	   }

  
	  return view('reports.list.daily_invoices')->with(['ext_labs'=>$ext_labs,'resource'=>$resource,'patients'=>$Patients]);	
	     
		   
}

public function invoices_per_pay($lang,Request $request){
  $user_type=auth()->user()->type;
  $myId = auth()->user()->id;
  $idClinic = auth()->user()->clinic_num;
  $resource = Clinic::select('id','full_name')->where('id',$idClinic)->first();
  
  $payment_types =DB::table('tbl_bill_payment_mode')->where('status','O')->where('clinic_num',$idClinic)->get(['id','name_eng','name_fr']);
  
 if($user_type==3){
		$ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$myId)->get();
	    $ext_lab_id = ExtLab::select('id')->where('lab_user_num',$myId)->value('id');
		$Patients = Patient::where('clinic_num',$idClinic)->where('ext_lab',$ext_lab_id)->where('status','O')->orderBy('id','desc')->get();
	   }else{
	    $ext_labs=ExtLab::select('id','full_name')->orderBy('full_name')->get();
		$Patients = Patient::where('clinic_num',$idClinic)->where('status','O')->orderBy('id','desc')->get();
		} 
	if($request->ajax()){    
		if($request->filter_print_type=='USD'){
		$results = DB::table('tbl_bill_head as a')
		           ->selectRaw( 'a.id,
				                 DATE(b.datein) as payment_date,
								  if(b.payment_type="P",IFNULL(b.dpay_amount,0),IFNULL(-1*b.dpay_amount,0)) as pay,
								 a.clinic_bill_num as bill_num,DATE(a.bill_datein) as bill_date,
								 if(b.payment_type="P","Payment",if(b.payment_type="R","Donate",""))  as payment_type,
								 a.bill_total as total,
								 a.bill_balance_us as sold,
								 e.name_eng as payment_name,
								 l.full_name as ext_lab_name,
						         o.request_nb,
								 a.bill_discount_us as discount,
								 IFNULL(CONCAT(doc.first_name,IFNULL(CONCAT(" ",doc.middle_name," ")," "),doc.last_name),"") as doctor_name,
					             CONCAT(patConsult.first_name," ",IFNULL(patConsult.middle_name,"")," ",patConsult.last_name) AS patDetail')
					->join('tbl_patients as patConsult','patConsult.id','a.patient_num')
					->join('tbl_bill_payment as b','b.bill_num','a.id')
					->leftjoin('tbl_bill_payment_mode as e','e.id','b.reference')
					->leftjoin('tbl_visits_orders as o','o.id','a.order_id')
			        ->leftjoin('tbl_doctors as doc','doc.id','a.doctor_num')
                    ->leftjoin('tbl_external_labs as l','l.id','a.ext_lab')
					->where('a.status','O')
					->where('b.status','Y')
	                ->where('a.clinic_num',$idClinic);
		}else{
			$results = DB::table('tbl_bill_head as a')
		           ->selectRaw( 'a.id,
				                 DATE(b.datein) as payment_date,
				                 if(b.payment_type="P",IFNULL(b.lpay_amount,0),IFNULL(-1*b.lpay_amount,0)) as pay,
								 a.clinic_bill_num as bill_num,DATE(a.bill_datein) as bill_date,
								 if(b.payment_type="P","Payment",if(b.payment_type="R","Donate",""))  as payment_type,
								 a.lbill_total as total,
								 a.bill_balance as sold,
								 e.name_eng as payment_name,
								 l.full_name as ext_lab_name,
						         o.request_nb,
								 a.bill_discount as discount,
								 IFNULL(CONCAT(doc.first_name,IFNULL(CONCAT(" ",doc.middle_name," ")," "),doc.last_name),"") as doctor_name,
					             CONCAT(patConsult.first_name," ",IFNULL(patConsult.middle_name,"")," ",patConsult.last_name) AS patDetail')
					->join('tbl_patients as patConsult','patConsult.id','a.patient_num')
					->join('tbl_bill_payment as b','b.bill_num','a.id')
					->leftjoin('tbl_bill_payment_mode as e','e.id','b.reference')
					->leftjoin('tbl_visits_orders as o','o.id','a.order_id')
			        ->leftjoin('tbl_doctors as doc','doc.id','a.doctor_num')
                    ->leftjoin('tbl_external_labs as l','l.id','a.ext_lab')
					->where('a.status','O')
					->where('b.status','Y')
	                ->where('a.clinic_num',$idClinic);
		}
	
	 if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $results=$results->whereRaw('DATE(a.bill_datein)>=? or DATE(b.datein)>=?',[$request->filter_fromdate,$request->filter_fromdate]);
				}
	  
	  if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $results=$results->whereRaw('DATE(a.bill_datein)<=? or DATE(b.datein)<=?',[$request->filter_todate,$request->filter_todate]);
				 
				}		
	  
	  if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			    $results=$results->whereRaw('(DATE(a.bill_datein)>=? and DATE(a.bill_datein)<=?)or (DATE(b.datein)>=? and DATE(b.datein)<=?)',[$request->filter_fromdate,$request->filter_todate,$request->filter_fromdate,$request->filter_todate]);
				}
      
      if(isset($request->filter_patient) && $request->filter_patient!="" ){
			  $results=$results->where('a.patient_num',$request->filter_patient);		  		  
		    }
	  
	  
	  if(isset($request->filter_resource) && $request->filter_resource!="" ){
			 $results=$results->where('a.ext_lab',$request->filter_resource);		  
		    } 
	
	
	 if(isset($request->filter_payment) && $request->filter_payment!="0" ){
			if($request->filter_print_type=="USD"){
				$results=$results->where('a.bill_balance_us','>',0);	
			}else{
				$results=$results->where('a.bill_balance','>',0);	
			}
         }
			  
	 if(isset($request->filter_payment_type) && $request->filter_payment_type!="0" ){
			  $results=$results->where('e.id',$request->filter_payment_type);
		      }
	
	$results=$results->orderBy('bill_date','desc')->get();
	return response()->json($results);
	}	
		
  return view('reports.list.daily_invoices_per_payments')->with(['payment_types'=>$payment_types,'ext_labs'=>$ext_labs,'resource'=>$resource,'patients'=>$Patients]);	

}


public function invoices_per_test($lang,Request $request){
	 $user_type=auth()->user()->type;
     $myId = auth()->user()->id;
     $idClinic = auth()->user()->clinic_num;
     $resource = Clinic::select('id','full_name')->where('id',$idClinic)->first();
	 $items = DB::table('tbl_lab_tests')->select('id','test_name')->whereRaw('(group_num IS NULL or group_num="") and active="Y"')->orderBy('id','desc')->get();
	 if($user_type==3){
		$ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$myId)->get();
	    $ext_lab_id = ExtLab::select('id')->where('lab_user_num',$myId)->value('id');
		$Patients = Patient::where('clinic_num',$idClinic)->where('ext_lab',$ext_lab_id)->where('status','O')->orderBy('id','desc')->get();
	   }else{
	    $ext_labs=ExtLab::select('id','full_name')->orderBy('full_name')->get();
		$Patients = Patient::where('clinic_num',$idClinic)->where('status','O')->orderBy('id','desc')->get();
		} 
	if($request->ajax()){    
       $results = DB::table('tbl_bill_specifics as det')
		           ->selectRaw('det.id,
				                DATE_FORMAT(req.bill_datein,"%Y-%m-%d") as bill_date,
				                req.clinic_bill_num as bill_num,
								det.bill_name as test_name,
								det.cnss,
								det.lbill_price as price,
								det.bill_price as priceUS,
								l.full_name as ext_lab_name,
							    IFNULL(CONCAT(doc.first_name,IFNULL(CONCAT(" ",doc.middle_name," ")," "),doc.last_name),"") as doctor_name,
								CONCAT(patConsult.first_name," ",IFNULL(patConsult.middle_name,"")," ",patConsult.last_name) AS patDetail')
				   ->join('tbl_bill_head as req','req.id','det.bill_num')
	               ->join('tbl_patients as patConsult','patConsult.id','req.patient_num')
				   ->leftjoin('tbl_doctors as doc','doc.id','req.doctor_num')
                   ->leftjoin('tbl_external_labs as l','l.id','req.ext_lab')
				   ->where('det.status','O')
				   ->where('req.status','O')
	               ->where('req.clinic_num',$idClinic);
	
	 if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $results=$results->whereRaw('DATE(req.bill_datein)>=?',$request->filter_fromdate);
				}
	  
	  if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $results=$results->whereRaw('DATE(req.bill_datein)<=?',$request->filter_todate);
				 
				}		
	  
	  if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			    $results=$results->whereRaw('(DATE(req.bill_datein)>=? and DATE(req.bill_datein)<=?)',[$request->filter_fromdate,$request->filter_todate]);
				}
      
      if(isset($request->filter_patient) && $request->filter_patient!="" ){
			  $results=$results->where('req.patient_num',$request->filter_patient);		  		  
		    }
	  
	  
	  if(isset($request->filter_resource) && $request->filter_resource!="" ){
			 $results=$results->where('req.ext_lab',$request->filter_resource);		  
		    } 
	
	if(isset($request->filter_test) && $request->filter_test!="" ){
			 $ct_bills = DB::table('tbl_bill_head as h')
			             ->select('h.clinic_bill_num')
						 ->join('tbl_bill_specifics as det','det.bill_num','h.id')
						 ->where('det.bill_code',$request->filter_test)
						 ->where('det.status','O')
				         ->where('h.status','O')
	                     ->where('h.clinic_num',$idClinic)
						 ->distinct()->pluck('h.clinic_bill_num')->toArray();
			 
			 $results=$results->whereIn('req.clinic_bill_num',$ct_bills);		  
		    } 
	
	$results=$results->orderBy('bill_date','desc')->get();
	return response()->json($results);
	
	}
 return view('reports.list.daily_invoices_per_tests')->with(['items'=>$items,'ext_labs'=>$ext_labs,'resource'=>$resource,'patients'=>$Patients]);	

}


public function tests_per_request($lang,Request $request){
	 $user_type=auth()->user()->type;
     $myId = auth()->user()->id;
     $idClinic = auth()->user()->clinic_num;
     $resource = Clinic::select('id','full_name')->where('id',$idClinic)->first();
	 $items = DB::table('tbl_lab_tests')->select('id','test_name')->whereRaw('(group_num IS NULL or group_num="") and active="Y"')->orderBy('id','desc')->get();
	 if($user_type==3){
		$ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$myId)->get();
	    $ext_lab_id = ExtLab::select('id')->where('lab_user_num',$myId)->value('id');
		$Patients = Patient::where('clinic_num',$idClinic)->where('ext_lab',$ext_lab_id)->where('status','O')->orderBy('id','desc')->get();
	   }else{
	    $ext_labs=ExtLab::select('id','full_name')->orderBy('full_name')->get();
		$Patients = Patient::where('clinic_num',$idClinic)->where('status','O')->orderBy('id','desc')->get();
		} 
	if($request->ajax()){
		$results = DB::table('tbl_visits_order_custom_tests as ct')
                   ->selectRaw('ct.id as id,t.cnss as cnss,t.test_name as test_name,
                                t.preanalytical,sp.name as special_cons,specimen.name as specimen,				   
						       IF(t.is_group IS NOT NULL and t.is_group="Y","Yes","No") as is_group,
					           ct.collected_test_date,DATE(o.order_datetime) as visit_date,
						       o.order_datetime as visit_date_time,l.full_name as ext_lab_name,o.request_nb,
							   CONCAT(o.request_nb," ( ","Patient"," : ",CONCAT(patConsult.first_name," ",IFNULL(patConsult.middle_name,"")," ",patConsult.last_name),
							          IF(l.full_name IS NOT NULL and l.full_name<>"",CONCAT(" , ","Guarantor"," : ",l.full_name),""),
									  IFNULL(CONCAT(" , ","Doctor"," : ",doc.first_name,IFNULL(CONCAT(" ",doc.middle_name," ")," "),doc.last_name),"")," )") as request_data')
                   ->join('tbl_visits_orders as o','o.id','ct.order_id')
				   ->join('tbl_lab_tests as t','t.id','ct.test_id')
				   ->join('tbl_patients as patConsult','patConsult.id','o.patient_num')
				   ->leftjoin('tbl_doctors as doc','doc.id','o.doctor_num')
                   ->leftjoin('tbl_external_labs as l','l.id','o.ext_lab')
			       ->leftjoin('tbl_lab_special_considerations as sp','sp.id','t.special_considerations')
				   ->leftjoin('tbl_lab_specimen as specimen','specimen.id','t.specimen')
				   ->where('o.active','Y')
				   ->where('o.status','<>','I')
				   ->where('ct.active','Y')
				   ->where('ct.clinic_num',$idClinic);
		
		if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $results=$results->whereRaw('DATE(o.order_datetime)>=?',$request->filter_fromdate);
				}
	  
	    if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $results=$results->whereRaw('DATE(o.order_datetime)<=?',$request->filter_todate);
				 
				}		
	  
	    if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			    $results=$results->whereRaw('(DATE(o.order_datetime)>=? and DATE(o.order_datetime)<=?)',[$request->filter_fromdate,$request->filter_todate]);
				}
      
        
		if(isset($request->filter_status) && $request->filter_status!="" ){
			 $results=$results->where('o.status',$request->filter_status);		  
		    }
			
		if(isset($request->filter_patient) && $request->filter_patient!="" ){
			  $results=$results->where('o.patient_num',$request->filter_patient);		  		  
		    }
	  
	  
	   if(isset($request->filter_resource) && $request->filter_resource!="" ){
			 $results=$results->where('o.ext_lab',$request->filter_resource);		  
		    } 
	
	   if(isset($request->filter_test) && $request->filter_test!="" ){
			 $ct_reqs = DB::table('tbl_visits_orders as o')
			            ->select('o.request_nb')
			            ->join('tbl_visits_order_custom_tests as ct','ct.order_id','o.id')
						->where('ct.test_id',$request->filter_test)
						->where('o.active','Y')
				        ->where('o.status','<>','I')
				        ->where('ct.active','Y')
				        ->where('ct.clinic_num',$idClinic)
						->distinct()->pluck('o.request_nb')->toArray();
			 $results=$results->whereIn('o.request_nb',$ct_reqs);		  
		    } 	     
                 
	 $results=$results->orderBy('o.request_nb','desc')->orderBy('t.testord')->get();
	 return response()->json($results);
	}

 return view('reports.list.daily_requests_per_tests')->with(['items'=>$items,'ext_labs'=>$ext_labs,'resource'=>$resource,'patients'=>$Patients]);		
} 

//old ones
public function purchases($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
  

if($request->ajax()){	
	
		   $filter_supplier="";
		   if( $request->filter_supplier!="0" &&  $request->filter_supplier!=NULL){
			  $filter_supplier = " and tbl_inventory_invoices_request.fournisseur_id = '".$request->filter_supplier."' "; 
		   }
		   
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $filter_date= "and DATE(tbl_inventory_invoices_request.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
			
			if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $filter_date= "and DATE(tbl_inventory_invoices_request.date_invoice) <= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}		
				
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			     $filter_date = "and DATE(tbl_inventory_invoices_request.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}	
			 
		  
		   $filter_due_amount="";
		  if(isset($request->filter_due_amount) && $request->filter_due_amount!="0" ){
			 $filter_due_amount = "and tbl_inventory_invoices_request.inv_balance>0";  
		      } 
		  
		  $filter_inv_type="and (tbl_inventory_invoices_request.type='1' or tbl_inventory_invoices_request.type='4' )";
          
		  if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
			 $filter_inv_type = " and tbl_inventory_invoices_request.type = 4";
			
			}	
		  
	
	$undefined = __("Undefined");


    $data="select tbl_inventory_invoices_request.id as id,tbl_fournisseur.name,
		   CONCAT(IFNULL(tbl_inventory_invoices_request.clinic_inv_num,''),';',IF(tbl_inventory_invoices_request.cr_note='G','".__("Warranty")."',''),';',IF(tbl_inventory_invoices_request.reference IS NULL or tbl_inventory_invoices_request.type=1,'',tbl_inventory_invoices_request.reference)) as id_invoice, 
		   tbl_types.name as typename,tbl_inventory_invoices_request.cr_note, 
		   DATE_FORMAT(tbl_inventory_invoices_request.date_invoice,'%Y-%m-%d') as date_invoice,
		   IF(tbl_inventory_invoices_request.type=4,-1*tbl_inventory_invoices_request.total,tbl_inventory_invoices_request.total) as sub_total,
		   IF(tbl_inventory_invoices_request.type=4,-1*tbl_inventory_invoices_request.qst,tbl_inventory_invoices_request.qst) as qst,
		   IF(tbl_inventory_invoices_request.type=4,-1*tbl_inventory_invoices_request.gst,tbl_inventory_invoices_request.gst) as gst,
		   IF(tbl_inventory_invoices_request.type=4,-1*(tbl_inventory_invoices_request.total+tbl_inventory_invoices_request.qst+tbl_inventory_invoices_request.gst),tbl_inventory_invoices_request.total+tbl_inventory_invoices_request.qst+tbl_inventory_invoices_request.gst) as total,
		   tbl_inventory_invoices_request.active,clin.full_name as branch_name,
		   tbl_inventory_invoices_request.inv_balance as solde_du,ANY_VALUE(sum(if(b.payment_type='P',IFNULL(b.payment_amount,0.00),-1*IFNULL(b.payment_amount,0.00)))) as montant_payer
		   from tbl_inventory_invoices_request 
		   LEFT JOIN tbl_inventory_items_fournisseur as tbl_fournisseur on tbl_fournisseur.id=tbl_inventory_invoices_request.fournisseur_id 
		   LEFT JOIN tbl_inventory_types as tbl_types on tbl_types.id=tbl_inventory_invoices_request.type 
		   LEFT JOIN tbl_inventory_payment b ON  b.invoice_num=tbl_inventory_invoices_request.id and b.status='Y'
		   INNER JOIN tbl_clinics as clin ON clin.id=tbl_inventory_invoices_request.clinic_num
		   where tbl_inventory_invoices_request.active='O'  and tbl_inventory_invoices_request.clinic_num='".$clinic_num."' ".$filter_date." ".$filter_supplier." ".$filter_inv_type." ".$filter_due_amount." 		       
		   GROUP BY tbl_inventory_invoices_request.id";
		   
      $purchases = DB::select(DB::raw("$data"));
      
	  
      return Datatables::of($purchases)->addIndexColumn()->make(true);	
	 
	 }

  
	  return view('reports.list.inventory_purchases')->with(['resource'=>$resource,'suppliers'=>$suppliers]);	
	     	 
}

public function sales_per_item($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
	
	$patients = Patient::where('clinic_num',$clinic_num)->get();
	$suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();
    $items = DB::table('tbl_inventory_fournisseur_types')->where('active','O')->orderBy('id','desc')->get();
  		   
		if($request->ajax()){   
		   
		   $filter_supplier="";
		   if( $request->filter_supplier !="0" &&  $request->filter_supplier !=NULL){
			  $filter_supplier = " and supp.id = '".$request->filter_supplier."' "; 
		   }
		   
		   $filter_patient="";
           	if($request->filter_patient !=NULL && $request->filter_patient !="0" ){
			 $filter_patient = "and pat.id='".$request->filter_patient."' "; 
			  
		    }
		   
		   $filter_item="";
		   if( $request->filter_item !="0" &&  $request->filter_item !=NULL){
			  $filter_item = " and item_type.id = '".$request->filter_item."' "; 
		   }
		  
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate =="" || $request->filter_todate ==NULL) ){
			     $filter_date= "and DATE(req.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
				
			if( ($request->filter_fromdate =="" && $request->filter_fromdate ==NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date= "and DATE(req.date_invoice) >= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}	
				
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date = "and DATE(req.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}		
			$filter_inv_type="and (req.type='2' or req.type='3')";
			 if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
				 if($request->filter_inv_type=='RP'){
				 $filter_inv_type = " and req.type = 3 and (req.cr_note='N' or req.cr_note IS NULL)";
				 }
				 if($request->filter_inv_type=='CN'){
				 $filter_inv_type = " and req.type = 3 and req.cr_note='Y' ";
				 }
			  }					 
		   
       
	   $data = "select req.id, DATE_FORMAT(req.date_invoice,'%Y-%m-%d') as date_invoice,
	                   CONCAT(req.clinic_inv_num,';',IFNULL(req.reference,'')) as invoice_num,
	                   CASE WHEN item_type.name='Glass Frame' THEN '".__("Glass Frame")."'
    					   WHEN item_type.name='Contact Lens' THEN '".__("Contact Lens")."'
						   WHEN item_type.name='Products' THEN '".__("Products")."'
    					   WHEN item_type.name='Lens' THEN '".__("Lens")."'
					    END 	   
					    as item_type,
					    det.qty,det.price,det.discount,
						IF(req.type=3,-1*(det.total),det.total) as subtotal,
						IF(req.type=3,-1*IFNULL(det.qst,0),IFNULL(det.qst,0)) as qst,
						IF(req.type=3,-1*IFNULL(det.gst,0),IFNULL(det.gst,0)) as gst,
						IF(req.type=3,-1*(det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)),det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)) as total, 
					    CONCAT(pat.first_name,' ',pat.last_name) AS patName,supp.name as supplier_name,clin.full_name as branch_name,
					    item.description as item_name,IF(req.type=3,-1*item.cost_price*det.qty,item.cost_price*det.qty) as cost_price,
					    IF(req.type=3,-1*(det.total-(item.cost_price*det.qty)),det.total-(item.cost_price*det.qty)) as profit,
					    IF(item.cost_price=0.00,IF(req.type=3,-1*det.total,det.total),IF(type=3,-1*( (det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty)),(det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty))) as profit_percent
					    from tbl_inventory_invoices_request as req
					    INNER JOIN tbl_inventory_invoices_details as det ON det.status='O' and det.invoice_id=req.id
	                    INNER JOIN tbl_inventory_items as item ON item.id=det.item_code  and typecode=1 and item.active='O'
					    INNER JOIN tbl_inventory_fournisseur_types as item_type ON item_type.id=item.gen_types and item_type.active='O'
					    INNER JOIN tbl_patients as pat ON pat.id=req.patient_id 
					    INNER JOIN tbl_inventory_items_fournisseur as supp ON supp.id=item.fournisseur and supp.active='O'
					    INNER JOIN tbl_clinics as clin ON clin.id = req.clinic_num and clin.active='O'
	                    where req.active='O' and req.clinic_num='".$clinic_num."' ".$filter_inv_type." ".$filter_date." ".$filter_item." ".$filter_supplier." ".$filter_patient."  
	                    order by  item_type.name, req.date_invoice desc";
	   
	   $sales_per_item = DB::select(DB::raw("$data"));
	   
	   return response()->json($sales_per_item);
		}
	  return view('reports.list.inventory_sales_per_item')->with(['resource'=>$resource,'patients'=>$patients,'suppliers'=>$suppliers,'items'=>$items]);	
	    
} 

public function sales_per_supplier($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
	
	$patients = Patient::where('clinic_num',$clinic_num)->get();
	$suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();
    $items = DB::table('tbl_inventory_fournisseur_types')->where('active','O')->orderBy('id','desc')->get();
  	 
		  
		if($request->ajax()){   
		   
		   $filter_supplier="";
		   if( $request->filter_supplier !="0" &&  $request->filter_supplier !=NULL){
			  $filter_supplier = " and supp.id = '".$request->filter_supplier."' "; 
		   }
		   
		   $filter_patient="";
           	if($request->filter_patient !=NULL && $request->filter_patient !="0" ){
			 $filter_patient = "and pat.id='".$request->filter_patient."' "; 
			  
		    }
		   
		   $filter_item="";
		   if( $request->filter_item !="0" &&  $request->filter_item !=NULL){
			  $filter_item = " and item_type.id = '".$request->filter_item."' "; 
		   }
		  
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate =="" || $request->filter_todate ==NULL) ){
			     $filter_date= "and DATE(req.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
				
			if( ($request->filter_fromdate =="" && $request->filter_fromdate ==NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date= "and DATE(req.date_invoice) >= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}	
				
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date = "and DATE(req.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}		
			
            $filter_inv_type="and (req.type='2' or req.type='3')";
			 if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
				 if($request->filter_inv_type=='RP'){
				 $filter_inv_type = " and req.type = 3 and (req.cr_note='N' or req.cr_note IS NULL)";
				 }
				 if($request->filter_inv_type=='CN'){
				 $filter_inv_type = " and req.type = 3 and req.cr_note='Y' ";
				 }
			  }			
       
	   $data = "select req.id, DATE_FORMAT(req.date_invoice,'%Y-%m-%d') as date_invoice,
						CONCAT(req.clinic_inv_num,';',IFNULL(req.reference,'')) as invoice_num,	                    
						CASE WHEN item_type.name='Glass Frame' THEN '".__("Glass Frame")."'
    					   WHEN item_type.name='Contact Lens' THEN '".__("Contact Lens")."'
						   WHEN item_type.name='Products' THEN '".__("Products")."'
    					   WHEN item_type.name='Lens' THEN '".__("Lens")."'
					    END 	   
					    as item_type,
						det.qty,det.price,det.discount,
						IF(req.type=3,-1*(det.total),det.total) as subtotal,
						IF(req.type=3,-1*IFNULL(det.qst,0),IFNULL(det.qst,0)) as qst,
						IF(req.type=3,-1*IFNULL(det.gst,0),IFNULL(det.gst,0)) as gst,
						IF(req.type=3,-1*det.total+-1*IFNULL(det.qst,0)+-1*IFNULL(det.gst,0),det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)) as total,  
					    CONCAT(pat.first_name,' ',pat.last_name) AS patName,supp.name as supplier_name,clin.full_name as branch_name,
					    item.description as item_name,IF(req.type=3,-1*(item.cost_price*det.qty),item.cost_price*det.qty) as cost_price,
						IF(req.type=3,-1*(det.total-item.cost_price*det.qty),det.total-item.cost_price*det.qty) as profit,
					    IF(item.cost_price=0.00,IF(req.type=3,-1*det.total,det.total),IF(type=3,-1*( (det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty)),(det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty))) as profit_percent
					  from tbl_inventory_invoices_request as req
					  INNER JOIN tbl_inventory_invoices_details as det ON det.status='O' and det.invoice_id=req.id
	                  INNER JOIN tbl_inventory_items as item ON item.id=det.item_code  and typecode=1 and item.active='O'
					  INNER JOIN tbl_inventory_fournisseur_types as item_type ON item_type.id=item.gen_types and item_type.active='O'
					  INNER JOIN tbl_patients as pat ON pat.id=req.patient_id 
					  INNER JOIN tbl_inventory_items_fournisseur as supp ON supp.id=item.fournisseur and supp.active='O'
					  INNER JOIN tbl_clinics as clin ON clin.id = req.clinic_num and clin.active='O'
	            where req.active='O' and req.clinic_num='".$clinic_num."' ".$filter_inv_type." ".$filter_date." ".$filter_item." ".$filter_supplier." ".$filter_patient."  
	            order by  item_type.name, req.date_invoice desc";
	   
	   $sales_per_supplier = DB::select(DB::raw("$data"));
	   
	   return response()->json( $sales_per_supplier);
		}
	  return view('reports.list.inventory_sales_per_supplier')->with(['resource'=>$resource,'patients'=>$patients,'suppliers'=>$suppliers,'items'=>$items]);	
	    
} 

public function purchases_per_supplier($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
	
	$suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();
    $items = DB::table('tbl_inventory_fournisseur_types')->where('active','O')->orderBy('id','desc')->get();
  		   
		if($request->ajax()){   
		   
		   $filter_supplier="";
		   if( $request->filter_supplier !="0" &&  $request->filter_supplier !=NULL){
			  $filter_supplier = " and supp.id = '".$request->filter_supplier."' "; 
		   }
		   
		   
		   $filter_item="";
		   if( $request->filter_item !="0" &&  $request->filter_item !=NULL){
			  $filter_item = " and item_type.id = '".$request->filter_item."' "; 
		   }
		  
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate =="" || $request->filter_todate ==NULL) ){
			     $filter_date= "and DATE(req.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
				
			if( ($request->filter_fromdate =="" && $request->filter_fromdate ==NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date= "and DATE(req.date_invoice) >= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}	
				
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date = "and DATE(req.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}

             $filter_inv_type="and (req.type='1' or (req.type='4' and req.cr_note<>'G'))";
			 if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
				  $filter_inv_type = " and req.type = '4' and req.cr_note<>'G'";
				 
			  }							
					 
	    $data = "select req.id, DATE_FORMAT(req.date_invoice,'%Y-%m-%d') as date_invoice,
						CONCAT(req.clinic_inv_num,';',IF(req.reference IS NULL or req.type=1,'',req.reference)) as invoice_num,	                    
						CASE WHEN item_type.name='Glass Frame' THEN '".__("Glass Frame")."'
    					   WHEN item_type.name='Contact Lens' THEN '".__("Contact Lens")."'
						   WHEN item_type.name='Products' THEN '".__("Products")."'
    					   WHEN item_type.name='Lens' THEN '".__("Lens")."'
					    END 	   
					    as item_type,
						det.qty,det.price,det.discount,supp1.name as supplier_name_invoice,
						IF(req.type=4,-1*(det.total),det.total) as subtotal,
						IF(req.type=4,-1*IFNULL(det.qst,0),IFNULL(det.qst,0)) as qst,
						IF(req.type=4,-1*IFNULL(det.gst,0),IFNULL(det.gst,0)) as gst,
						IF(req.type=4,-1*(det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)),det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)) as total,  
					    supp.name as supplier_name,clin.full_name as branch_name,
					    item.description as item_name,
						IF(req.type=4,-1*(item.cost_price*det.qty),item.cost_price*det.qty) as cost_price,
						IF(req.type=4,-1*(det.total-item.cost_price*det.qty),det.total-item.cost_price*det.qty) as profit,
					    IF(item.cost_price=0.00,IF(req.type=4,-1*det.total,det.total),IF(type=4,-1*( (det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty)),(det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty))) as profit_percent
					  from tbl_inventory_invoices_request as req
                      INNER JOIN tbl_inventory_invoices_details as det ON det.status='O' and det.invoice_id=req.id                      
                      INNER JOIN tbl_inventory_items as item ON item.id=det.item_code  and typecode=1 and item.active='O'
					  INNER JOIN tbl_inventory_items_fournisseur as supp1 ON supp1.id=req.fournisseur_id and supp1.active='O'
					  INNER JOIN tbl_inventory_fournisseur_types as item_type ON item_type.id=item.gen_types and item_type.active='O'
					  INNER JOIN tbl_inventory_items_fournisseur as supp ON supp.id=item.fournisseur and supp.active='O'
					  INNER JOIN tbl_clinics as clin ON clin.id = req.clinic_num and clin.active='O'
	            where req.active='O' and req.clinic_num='".$clinic_num."' ".$filter_inv_type." ".$filter_date." ".$filter_item." ".$filter_supplier." 
	            order by  item_type.name, req.date_invoice desc";	 	
       
	  
	   
	   $sales_per_item = DB::select(DB::raw("$data"));
	   
	   return response()->json($sales_per_item);
		}
	  return view('reports.list.inventory_purchases_per_supplier')->with(['resource'=>$resource,'suppliers'=>$suppliers,'items'=>$items]);	
	    
}

public function purchases_per_item($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
	
	$suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();
    $items = DB::table('tbl_inventory_fournisseur_types')->where('active','O')->orderBy('id','desc')->get();
  		   
		if($request->ajax()){   
		   
		   $filter_supplier="";
		   if( $request->filter_supplier !="0" &&  $request->filter_supplier !=NULL){
			  $filter_supplier = " and supp.id = '".$request->filter_supplier."' "; 
		   }
		   
		   
		   $filter_item="";
		   if( $request->filter_item !="0" &&  $request->filter_item !=NULL){
			  $filter_item = " and item_type.id = '".$request->filter_item."' "; 
		   }
		  
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate =="" || $request->filter_todate ==NULL) ){
			     $filter_date= "and DATE(req.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
				
			if( ($request->filter_fromdate =="" && $request->filter_fromdate ==NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date= "and DATE(req.date_invoice) >= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}	
				
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date = "and DATE(req.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}		
					 
		  
      $filter_inv_type="and (req.type='1' or (req.type='4' and req.cr_note<>'G'))";
			 if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
				  $filter_inv_type = " and req.type = '4' and req.cr_note<>'G'";
				 
			  }							
					 
	    $data = "select req.id, DATE_FORMAT(req.date_invoice,'%Y-%m-%d') as date_invoice,
						CONCAT(req.clinic_inv_num,';',IF(req.reference IS NULL or req.type=1,'',req.reference)) as invoice_num,	                    
						CASE WHEN item_type.name='Glass Frame' THEN '".__("Glass Frame")."'
    					   WHEN item_type.name='Contact Lens' THEN '".__("Contact Lens")."'
						   WHEN item_type.name='Products' THEN '".__("Products")."'
    					   WHEN item_type.name='Lens' THEN '".__("Lens")."'
					    END 	   
					    as item_type,
						det.qty,det.price,det.discount,supp1.name as supplier_name_invoice,
						IF(req.type=4,-1*(det.total),det.total) as subtotal,
						IF(req.type=4,-1*IFNULL(det.qst,0),IFNULL(det.qst,0)) as qst,
						IF(req.type=4,-1*IFNULL(det.gst,0),IFNULL(det.gst,0)) as gst,
						IF(req.type=4,-1*(det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)),det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)) as total,  
					    supp.name as supplier_name,clin.full_name as branch_name,
					    item.description as item_name,
						IF(req.type=4,-1*(item.cost_price*det.qty),item.cost_price*det.qty) as cost_price,
						IF(req.type=4,-1*(det.total-item.cost_price*det.qty),det.total-item.cost_price*det.qty) as profit,
					    IF(item.cost_price=0.00,IF(req.type=4,-1*det.total,det.total),IF(type=4,-1*( (det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty)),(det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty))) as profit_percent
					  from tbl_inventory_invoices_request as req
                      INNER JOIN tbl_inventory_invoices_details as det ON det.status='O' and det.invoice_id=req.id                      
                      INNER JOIN tbl_inventory_items as item ON item.id=det.item_code  and typecode=1 and item.active='O'
					  INNER JOIN tbl_inventory_items_fournisseur as supp1 ON supp1.id=req.fournisseur_id and supp1.active='O'
					  INNER JOIN tbl_inventory_fournisseur_types as item_type ON item_type.id=item.gen_types and item_type.active='O'
					  INNER JOIN tbl_inventory_items_fournisseur as supp ON supp.id=item.fournisseur and supp.active='O'
					  INNER JOIN tbl_clinics as clin ON clin.id = req.clinic_num and clin.active='O'
	            where req.active='O' and req.clinic_num='".$clinic_num."' ".$filter_inv_type." ".$filter_date." ".$filter_item." ".$filter_supplier." 
	            order by  item_type.name, req.date_invoice desc";	 	
	   
	   $sales_per_item = DB::select(DB::raw("$data"));
	   
	   return response()->json($sales_per_item);
		}
	  return view('reports.list.inventory_purchases_per_item')->with(['resource'=>$resource,'suppliers'=>$suppliers,'items'=>$items]);	
	    
}

public function purchases_per_pay($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
    
	$suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();

	$payment_types =DB::table('tbl_bill_payment_mode')->where('status','O')->where('clinic_num',$clinic_num)->get(['id','name_eng','name_fr']);
	
	if($request->ajax()){
		   
		   $filter_supplier="";
           	if(isset($request->filter_supplier) && $request->filter_supplier!="0" ){
			 $filter_supplier = "and a.fournisseur_id=".$request->filter_supplier; 
			  
		    }
		   
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $filter_date= "and DATE(a.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
			
			if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $filter_date= "and DATE(a.date_invoice) <= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}		
				
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			     $filter_date = "and DATE(a.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}	
			 
		  		  
		  
		  $filter_payment="";
		  if(isset($request->filter_payment) && $request->filter_payment!="0" ){
			 $filter_payment = "and b.payment_type='".$request->filter_payment."' ";  
		      }
			  
	      $filter_payment_type="";
		  if(isset($request->filter_payment_type) && $request->filter_payment_type!="0" ){
			 $filter_payment_type = "and e.id='".$request->filter_payment_type."' ";  
		      }
			  
			  $filter_due_amount="";
		  if(isset($request->filter_due_amount) && $request->filter_due_amount!="0" ){
			 $filter_due_amount = "and a.inv_balance>0";  
		      } 
		  
		  $filter_inv_type="and (a.type='1' or a.type='4')";
          
		  if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
			 $filter_inv_type = " and a.type = 4";
			
			}	
			  
			 $payment_name=($lang=='en')?'e.name_eng':'e.name_fr'; 
								 
			  $data="SELECT DATE(b.datein) as payment_date, c.name as suppName,
					d.full_name as branch_name,if(b.payment_type='P',b.payment_amount,-1*b.payment_amount) as payment_amount,
					CONCAT(a.clinic_inv_num,';',IF(a.type=1 or a.reference IS NULL,'',a.reference)) as bill_nb,
					DATE(a.date_invoice) as bill_date,{$payment_name} as payment_name,
					if(b.payment_type='P','Payment',if(b.payment_type='R','Refund','ND'))  as payment_type,
					IF(a.type=4,-1*(a.total+a.gst+a.qst),a.total+a.gst+a.qst) as total_pay,
					a.inv_balance as solde_du,a.type
					FROM  tbl_inventory_invoices_request a
					INNER JOIN tbl_inventory_payment b ON   b.invoice_num=a.id and b.status='Y'
					INNER JOIN tbl_inventory_items_fournisseur c ON c.id = a.fournisseur_id AND c.active='O' 
					INNER JOIN tbl_clinics d ON d.id = a.clinic_num AND d.active='O'
					INNER JOIN tbl_bill_payment_mode e ON   e.id=b.reference and e.status='O'
					WHERE a.active='O' and a.clinic_num='".$clinic_num."' ".$filter_supplier." ".$filter_payment." ".$filter_date." ".$filter_payment_type." ".$filter_due_amount." ".$filter_inv_type."
					ORDER BY b.reference desc,a.date_invoice desc
			 	     ";
              
            $bills_payments= DB::select(DB::raw("$data"));
			
			
			
		    return response()->json($bills_payments);				 
			  
	   }
      
	  return view('reports.list.inventory_purchases_per_pay')->with(['resource'=>$resource,'payment_types'=>$payment_types,'suppliers'=>$suppliers]);	
	 
	 
  } 
  
  public function pay_refund($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
    
	$patients = Patient::select('id','first_name','last_name')->where('clinic_num',$clinic_num)->get();

	$payment_types =DB::table('tbl_bill_payment_mode')->where('status','O')->where('clinic_num',$clinic_num)->get(['id','name_eng','name_fr']);
	
	if($request->ajax()){
		   
		   $filter_patient="";
           	if(isset($request->filter_patient) && $request->filter_patient!="0" ){
			 $filter_patient = "and a.patient_id=".$request->filter_patient; 
			  
		    }
		   
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $filter_date= "and DATE(a.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
			
			if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $filter_date= "and DATE(a.date_invoice) <= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}		
				
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			     $filter_date = "and DATE(a.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}	
			 
		  		  
		  
		  /*$filter_payment="";
		  if(isset($request->filter_payment) && $request->filter_payment!="0" ){
			 $filter_payment = "and b.payment_type='".$request->filter_payment."' ";  
		      }*/
			  
	      $filter_payment_type="";
		  if(isset($request->filter_payment_type) && $request->filter_payment_type!="0" ){
			 $filter_payment_type = "and e.id='".$request->filter_payment_type."' ";  
		      }
		  
		  $filter_due_amount="";
		  if(isset($request->filter_due_amount) && $request->filter_due_amount!="0" ){
			 $filter_due_amount = "and a.inv_balance>0";  
		      }
        
           $filter_inv_type="and (a.type='2' or a.type='3')";
         if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
			 if($request->filter_inv_type=='RP'){
			 $filter_inv_type = " and a.type = 3 and (a.cr_note='N' or a.cr_note IS NULL)";
			 }
			 if($request->filter_inv_type=='CN'){
			 $filter_inv_type = " and a.type = 3 and a.cr_note='Y' ";
			 }
		  }			
			 
			 $payment_name=($lang=='en')?'e.name_eng':'e.name_fr'; 
			
			
			  $data="SELECT b.datein as payment_date, concat(c.first_name,' ',c.last_name) as patName,
					d.full_name as branch_name,if(b.payment_type='P',b.payment_amount,-1*b.payment_amount) as payment_amount,CONCAT(a.clinic_inv_num,';',IFNULL(a.reference,'')) as bill_nb,
					DATE(a.date_invoice) as bill_date,{$payment_name} as payment_name,
					if(b.payment_type='P','Payment',if(b.payment_type='R','Refund','ND'))  as payment_type,
					IF(a.type=3,-1*(a.total+a.gst+a.qst),a.total+a.gst+a.qst) as total_pay,
					a.inv_balance as solde_du,a.type
					FROM  tbl_inventory_invoices_request a
					INNER JOIN tbl_inventory_payment b ON   b.invoice_num=a.id and b.status='Y'
					INNER JOIN tbl_patients c ON c.id = a.patient_id 
					INNER JOIN tbl_clinics d ON d.id = a.clinic_num AND d.active='O'
					INNER JOIN tbl_bill_payment_mode e ON   e.id=b.reference and e.status='O'
					WHERE a.active='O' and a.clinic_num='".$clinic_num."'  ".$filter_inv_type." ".$filter_patient."  ".$filter_date." ".$filter_payment_type." ".$filter_due_amount."
					ORDER BY b.reference desc,a.date_invoice desc
			 	     ";
              
            $bills_payments= DB::select(DB::raw("$data"));
			
			
			
		    return response()->json($bills_payments);				 
			  
	   }
      
	  return view('reports.list.inventory_payments')->with(['resource'=>$resource,'payment_types'=>$payment_types,'patients'=>$patients]);	
	 
	 
  }

  
}	