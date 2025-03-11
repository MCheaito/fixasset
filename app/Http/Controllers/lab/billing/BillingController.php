<?php
/*
* DEV APP
* Created date : 20-10-2022
*  
*
*/
namespace App\Http\Controllers\lab\billing;
use App\Http\Controllers\Controller;


use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use App\Models\LabTests;
use App\Models\TblBillAmounts;
use App\Models\TblBillRate;
use App\Models\TblBillPayment;
use App\Models\TblBillPaymentMode;
use App\Models\TblBillCurrency;
use App\Models\TblLabTests;
use App\Models\DoctorSignature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TblBillHead;
use App\Models\TblBillSpecifics;
use App\Models\ExtLab;
use Alert;
use DataTables;
use PDF;
use Image;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use UserHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\SettingMailAttach;

class BillingController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index($lang,Request $request) 
    {
	   $myId=auth()->user()->id;	
	   $idClinic = auth()->user()->clinic_num;
	   $type=auth()->user()->type;
	   
	   //does not exist
	   /*if($type==1){
        $ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
        $filter_patients = 	Patient::select('tbl_patients.*')
											 ->join('tbl_bill_head as c','c.patient_num','tbl_patients.id')
											 ->where('c.status','O')
											 ->where('c.clinic_num',$idClinic)
											 ->where('c.doctor_num',$myId)
											 ->where('tbl_patients.status','O')
											 ->orderBy('tbl_patients.id','desc')
											 ->distinct()
											 ->get();
	   $Patients = Patient::where('clinic_num',$idClinic)->where('ext_lab',$myId)->where('status','O')->orderBy('id','desc')->get();
							 
	   }*/
	   
	   //does not exist
	   /*if($type==3){
		$ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$myId)->get();
		$filter_patients = 	Patient::select('tbl_patients.*')
											 ->join('tbl_bill_head as c','c.patient_num','tbl_patients.id')
											 ->where('c.status','O')
											 ->where('c.clinic_num',$idClinic)
											 ->where('c.ext_lab',$myId)
											 ->where('tbl_patients.status','O')
											 ->orderBy('tbl_patients.id','desc')
											 ->distinct()
											 ->get();
        
	    $Patients = Patient::where('clinic_num',$idClinic)->where('ext_lab',$myId)->where('status','O')->orderBy('id','desc')->get();

	   }*/
	   
	 	$doctors = Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
		$ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
		$Patients = Patient::where('clinic_num',$idClinic)->where('status','O')->get();
		$filter_patients = 	Patient::select('tbl_patients.*')
											 ->join('tbl_bill_head as c','c.patient_num','tbl_patients.id')
											 ->where('c.status','O')->where('c.clinic_num',$idClinic)
											 ->where('tbl_patients.status','O')
											 ->orderBy('tbl_patients.id','desc')
											 ->distinct()
											 ->get();
	   
	   
	    $clinics = Clinic::find($idClinic);
		
		if ($request->ajax()) {
           
		    		 
			 $filter_date= "";
				
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $filter_date= "and DATE(tbl_bill_head.bill_datein) >= '".$request->filter_fromdate."' ";
				}
			
			if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $filter_date= "and DATE(tbl_bill_head.bill_datein) <= '".$request->filter_todate."' ";
				}	
				
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			     $filter_date = "and DATE(tbl_bill_head.bill_datein) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				}	
			
			$filter_patient="";
			
           	if(isset($request->filter_patient) && $request->filter_patient!="0" ){
			 $filter_patient = "and tbl_bill_head.patient_num=".$request->filter_patient; 
			    }
			
					   
		   $filter_facility="";
           	if(isset($request->id_facility) ){
			 $filter_facility = "and tbl_bill_head.clinic_num=".$request->id_facility;  
		   }
		   
		   $filter_status="";
           	if(isset($request->filter_status)){
			 $filter_status = "and tbl_bill_head.status='".$request->filter_status."'";  
		   }
		   
		   $filter_ext_lab="";
           	
			if(isset($request->id_pro) && $request->id_pro!="0" ){
			 $filter_ext_lab = "and tbl_bill_head.ext_lab='".$request->id_pro."' ";  
		     }
			 
			 $filter_doc="";
           	
			if(isset($request->filter_doc) && $request->filter_doc!="0" ){
			 $filter_doc = "and tbl_bill_head.doctor_num='".$request->filter_doc."' ";  
		     }
			 
			  $arr = array('from_date_bill'=>isset($request->filter_fromdate)?$request->filter_fromdate:'',
			             'to_date_bill'=>isset($request->filter_todate)?$request->filter_todate:'',
			             'status_bill'=>$request->filter_status,'patient_num_bill'=>$request->filter_patient,
						 'clinic_num_bill'=>$request->id_facility,'ext_lab'=>$request->id_pro);
						 
			UserHelper::drop_session_keys($arr);	
			UserHelper::generate_session_keys($arr);
		   
           $sql="select DISTINCT(tbl_bill_head.id) as id,
		          concat(tbl_bill_head.clinic_bill_num,',',DATE_FORMAT(tbl_bill_head.bill_datein,'%Y-%m-%d %H:%i')) as fac_bill,
		          
				  sentFromclin.full_name as fromClinicName,
		          CONCAT(IFNULL(tbl_patients.first_name,''),IF(tbl_patients.middle_name IS NOT NULL and tbl_patients.middle_name<>'',concat(' ',tbl_patients.middle_name),''),' ',IFNULL(tbl_patients.last_name,''),',',IFNULL(tbl_patients.birthdate,''),',',IFNULL(tbl_patients.cell_phone,'')) AS patDetail,
				  o.request_nb,
				  FORMAT(tbl_bill_head.lbill_total,0) as total,tbl_bill_head.ebill_total,tbl_bill_head.bill_total as totalUS,tbl_bill_head.status,
				  tbl_bill_head.tvq,tbl_bill_head.tvs,
				  FORMAT(tbl_bill_head.bill_balance,0) as sold,
				  (tbl_bill_head.tvq+tbl_bill_head.tvs+ tbl_bill_head.bill_total) as totaltva,
				  tbl_bill_head.bill_balance_us as soldUS,
				  IFNULL(l.full_name,'') as ext_lab_name,
				  IFNULL(CONCAT(doc.first_name,IF(doc.middle_name IS NOT NULL and doc.middle_name<>'',CONCAT(' ',doc.middle_name,' '),' '),doc.last_name),'') as doctor_name
				  from tbl_bill_head
				  INNER JOIN tbl_clinics as sentFromclin on tbl_bill_head.clinic_num=sentFromclin.id 
				  LEFT JOIN tbl_visits_orders as o on o.id=tbl_bill_head.order_id 
				  LEFT JOIN tbl_doctors as doc on tbl_bill_head.doctor_num=doc.id 
				  LEFT JOIN tbl_external_labs as l on l.id=tbl_bill_head.ext_lab				  
				  LEFT JOIN tbl_patients as tbl_patients on tbl_bill_head.patient_num=tbl_patients.id 
				  where 1=1 ".$filter_status." ".$filter_doc." ".$filter_patient." ".$filter_facility." ".$filter_date."  ".$filter_ext_lab."
				  ";
		  $bills= DB::select(DB::raw("$sql"));
           //dd($sql);
            return Datatables::of($bills)

                    ->addIndexColumn()

                    ->addColumn('action', function($row){
                           
						   $checked = ($row->status=='O')?'checked':'';
                           $disabled = ($row->status=='O')?'':'disabled';
						   $btn = '<a href="'.route('lab.billing.edit',[app()->getLocale(),$row->id]).'"  title="'.__("edit").'" class="btn btn-md btn-clean btn-icon  editVisit '.$disabled.'"><i class="far fa-edit text-primary"></i></a>';
						   $btn .= '<a href="javascript:void(0)"  title="'.__("PDF").'" class="btn btn-md btn-clean btn-icon   '.$disabled.'" onclick="getPDF('.$row->id.')"><i class="far fa-file-pdf text-primary"></i></a>';
                           $btn .= '<a href="javascript:void(0)"  title="'.__("Email").'" class="btn btn-md btn-clean btn-icon   '.$disabled.'" onclick="sendPDF('.$row->id.')"><i class="far fa-envelope text-primary"></i></a>';
						                         
						 if(UserHelper::can_access(auth()->user(),'delete_bill')){
						   $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';
						    
						  }
						   
						 
                            return $btn;

                    })

                    ->rawColumns(['action'])
					
                    ->make(true);

        }
	   
       
	   return view('lab.billing.index')->with(['FromFacility'=>$clinics,'ext_labs'=>$ext_labs,'doctors'=>$doctors,
	                                           'Patients'=>$Patients,'filter_patients'=>$filter_patients]); 
	  
	    		
}

public function edit($lang,$id)
    {
	$ReqDeails=TblBillSpecifics::where('bill_num',$id)->where('status','O')->get();	
	//$ReqPay=TblBillPayment::where('bill_num',$id)->where('payment_type','P')->where('status','Y')->get();	
	$ReqPay=TblBillPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_bill_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_bill_payment.status','Y')
			->where('bill_num',$id)
			->where('payment_type','P')
            ->get(['tbl_bill_payment.*', 'tbl_bill_payment_mode.name_fr']);
	
	
	$cptpCount= $ReqPay->count();
	$ReqRef=TblBillPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_bill_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_bill_payment.status','Y')
			->where('bill_num',$id)
			->where('payment_type','R')
            ->get(['tbl_bill_payment.*', 'tbl_bill_payment_mode.name_fr']);
	
	
	$cptrCount= $ReqRef->count();		
	$ReqPatient=TblBillHead::where('id',$id)->where('status','O')->first();	
	$patient=Patient::where('id',$ReqPatient->patient_num)->where('status','O')->first();	
	
	$user_type= auth()->user()->type;
	    
		if($user_type==1){
		//$profession=Doctor::select('doctor_user_num as user_num',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('doctor_user_num',$user_num)->get();
        $ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
       
	   }
		if($user_type==3){
	//	$profession=ExtLab::select('lab_user_num as user_num','full_name')->where('lab_user_num',$user_num)->get();
				$ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$user_num)->get();

		}
		if($user_type==2){
	//	$ext_labs1=Doctor::select('doctor_user_num as user_num',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->whereNOTNULL('doctor_user_num');
		//$profession=ExtLab::select('lab_user_num as user_num','full_name')->whereNOTNULL('lab_user_num')->union($ext_labs1)->get();
			$ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();

		}
	
	$doctors = Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get(); 
	$clinic=Clinic::where('id',$ReqPatient->clinic_num)->where('active','O')->first();	
	$cptCount= $ReqDeails->count();	
	
				$grps = DB::table('tbl_lab_groups as g')
		               ->select('g.id','g.descrip as test_name','active as group')
					   ->where('active','Y')->orderBy('id');
		
				$code= LabTests::select('id','test_name as name','group_num as group')
		         ->where('active','Y')->where('cnss','<>','')->where('clinic_num',$ReqPatient->clinic_num)->orderby('group_num')->orderBy('testord')
				 ->get();
		// ->union($grps)
	//$sqlPay = "select id,name_eng as name from tbl_bill_payment_mode where clinic_num='".$ReqPatient->clinic_num."'  and status='O' order by id desc";
	//$methodepay= DB::select(DB::raw("$sqlPay"));
	if ($lang=='fr'){			 
	 $sqlPay = "select id,name_fr as name from tbl_bill_payment_mode where clinic_num='".$ReqPatient->clinic_num."'  and status='O' order by id asc";
}else{
	 $sqlPay = "select id,name_eng as name from tbl_bill_payment_mode where clinic_num='".$ReqPatient->clinic_num."'  and status='O' order by id asc";
	
}
	$methodepay= DB::select(DB::raw("$sqlPay"));	
	
	$pay = DB::table('tbl_bill_payment')->where('bill_num', '=', $id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('lpay_amount');
	$refund = DB::table('tbl_bill_payment')->where('bill_num', '=', $id)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('lpay_amount');
	//	dd($ReqPatient);

	 // $taxqst=$ReqPatient->qst;
	  // $taxgst= $ReqPatient->gst;
     //   $qst=($ReqPatient->total*$taxqst)/100;
	  //  $gst=($ReqPatient->total*$taxgst)/100;
	//	$qst=number_format((float)$qst, 2, '.', ',');
	//	$gst=number_format((float)$gst, 2, '.', ',');
	  //  $balance=$ReqPatient->total+$qst+$gst- $pay+$refund;
	//$balance=number_format((float)$balance, 2, '.', ',');
	  $balance=$ReqPatient->bill_balance;
	  $balanced=$ReqPatient->bill_balance_us;
		//dd($balance);
	  $stotal=$ReqPatient->bill_total;
	  $totalf=$ReqPatient->lbill_total;
	  $etotal=$ReqPatient->ebill_total;
	  $totalf=number_format((float)$totalf, 2, '.', ',');
      $stotal=number_format((float)$stotal, 2, '.', ',');		
      $etotal=number_format((float)$etotal, 2, '.', ',');		
      //$balance=number_format((float)$balance, 2, '.', ',');			  
	  $rates = TblBillRate::where('status','O')->get();
	  $currencys=TblBillCurrency::where('active','O')->get();
	  $sumpay=DB::table('tbl_bill_payment')->where('bill_num','=',$id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
      $sumref=DB::table('tbl_bill_payment')->where('bill_num','=',$id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');
      $sumpayd=DB::table('tbl_bill_payment')->where('bill_num','=',$id)->where('status','Y')->where('payment_type','=','P')->sum('dpay_amount');
      $sumrefd=DB::table('tbl_bill_payment')->where('bill_num','=',$id)->where('status','Y')->where('payment_type','=','R')->sum('dpay_amount');
      
	  $currencyUSD=TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
      $lbl_usd = isset($currencyUSD)?$currencyUSD->price:15000;
	  
	  $ReqBill=TblBillHead::where('id',$id)->where('status','O')->first();
	  $tdiscount = isset($ReqBill->bill_discount) && $ReqBill->bill_discount!=''?$ReqBill->bill_discount:0;
      $tdiscountd = isset($ReqBill->bill_discount_us) && $ReqBill->bill_discount_us!=''?$ReqBill->bill_discount_us:0;
	  if($tdiscount!=0 && $tdiscountd==0){
		  $tdiscountd = floatval($tdiscount/$lbl_usd);
	  }
	  
	   $tbalance = $balance;
       $Nbalance=number_format((float)$tbalance, 2, '.', ',');
       $balance1=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));
	   $balance=number_format((float)$balance1, 2, '.', ',');		
	    
		$sumpayd =  number_format((float)$sumpayd,2,'.','');
		$sumpayl =  number_format((float)$sumpay,2,'.',',');

		$sumrefd =  number_format((float)$sumrefd,2,'.','');
		$sumrefl =  number_format((float)$sumref,2,'.',',');

		$balanced =  number_format((float)$balanced,2,'.','');
		$balancel=number_format((float)$balance, 2, '.', ',');

		$tdiscountd =  number_format((float)$tdiscountd,2,'.','');
		$tdiscountl =  number_format((float)$tdiscount,2,'.',',');
		
	return view('lab.billing.EditBillPatient')->with(['lbl_usd'=>$lbl_usd,'sumpayd'=>$sumpayd,'sumpayl'=>$sumpayl,'sumrefd'=>$sumrefd,'sumrefl'=>$sumrefl,'balanced'=>$balanced,'balancel'=>$balancel,'tdiscountd'=>$tdiscountd,'tdiscountl'=>$tdiscountl,'doctors'=>$doctors,
	'etotal'=>$etotal,'currencys'=>$currencys,'ReqPay'=>$ReqPay,'ReqRef'=>$ReqRef,'totalf'=>$totalf,'stotal'=>$stotal,'cptCount'=>$cptCount,'cptrCount'=>$cptrCount,'cptpCount'=>$cptpCount,'patient'=>$patient,'clinic'=>$clinic,'ReqPatient'=>$ReqPatient,'ext_labs'=>$ext_labs,'code'=>$code,'methodepay'=>$methodepay,'ReqDeails'=>$ReqDeails,'balance'=>$balance,'pay'=>$pay,'refund'=>$refund,'rates'=>$rates]);
		}
	
public function fillPatientBilllab($lang, Request $request ) {
    $type = $request->type;
    $clinic_id = $request->clinic_id;
    $html=$html1='';
	
	switch($type){
		case 'fill_all':
		 $Patients = Patient::where('clinic_num',$clinic_id)->where('status','O')->get();
		$filter_patients = 	Patient::select('tbl_patients.*')
											 ->join('tbl_bill_head as c','c.patient_num','tbl_patients.id')
											 ->where('c.status','O')->where('c.clinic_num',$clinic_id)
											 ->where('tbl_patients.status','O')
											 ->orderBy('tbl_patients.id','desc')
											 ->distinct()
											 ->get();
		
		if(count($Patients)!=1){
			$html='<option value="">'.__("Choose a patient").'</option>';
			}
		foreach($Patients as $pat){
			$id=$pat->id;
			$name=$pat->first_name.' '.$pat->last_name;
			$html.='<option value="'.$id.'">'.$name.'</option>';
		}
		
		
			$html1='<option value="0">'.__("Choose a patient").'</option>';
			
		
		foreach($filter_patients as $pat){
			$id=$pat->id;
			$name=$pat->first_name.' '.$pat->last_name;
			$html1.='<option value="'.$id.'">'.$name.'</option>';
			
		}
		//dd($html);
		return response()->json(["html"=>$html,"html1"=>$html1]);
		break;
		case 'fill_patients' :
		
		
		$Patients = Patient::where('clinic_num',$clinic_id)->where('status','O')->get();
		
		if(count($Patients)!=1){
			$html='<option value="">'.__("Choose a patient").'</option>';
			}
		foreach($Patients as $pat){
			$id=$pat->id;
			$name=$pat->first_name.' '.$pat->last_name;
			$html.='<option value="'.$id.'">'.$name.'</option>';
		}
		
		
		//dd($html);
		return response()->json(["html"=>$html]);
		break;
		case 'filter_patients':
		 $filter_patients = 	Patient::select('tbl_patients.*')
											 ->join('tbl_bill_head as c','c.patient_num','tbl_patients.id')
											 ->where('c.status','O')->where('c.clinic_num',$clinic_id)
											 ->where('tbl_patients.status','O')
											 ->orderBy('tbl_patients.id','desc')
											 ->distinct()
											 ->get();
	   
			$html='<option value="0">'.__("Choose a patient").'</option>';
				
		foreach($filter_patients as $pat){
			$id=$pat->id;
			$name=$pat->first_name.' '.$pat->middle_name.' '.$pat->last_name;
			$html.='<option value="'.$id.'">'.$name.'</option>';
			
		}								 
		return response()->json(["html"=>$html]);
		break;
		
	}	
  }  
     
public function create($lang,Request $request)
    {
		
		return view('lab.billing.NewBillPatient');
    }

public function fillPatientDataBilllab($lang, Request $request)
     {
    
        $id= $request->id ;
        
        $Patient_Data = Patient::where('id',$id)->first();
      
	  	$fac=Clinic::where('active','O')->where('id',auth()->user()->clinic_num)->first();
		
		
	  return response()->json(['patient'=>$Patient_Data]);

     }
	
public function NewBillPatient($lang,Request $request) 
    {
		
		$myId=auth()->user()->id;	
		$id_patient = $request->patient;
		$id_clinic = $request->clinic;
		$date_bill = $request->date_bill;
     	$patient = Patient::find($id_patient);
		
		$selectgrntr=(isset($patient) && isset($patient->ext_lab))?$patient->ext_lab:NULL;
		
		$clinic = Clinic::find($id_clinic);
		
		
		$code= LabTests::select('id','test_name as name','group_num as group')
		         ->where('active','Y')
				 ->where('cnss','<>','')
				 ->where('clinic_num',$id_clinic)
				 ->orderby('group_num')->orderBy('testord')
				 ->get();		
		    
		 $methodepay=DB::table('tbl_bill_payment_mode')
                     ->select('id','name_eng as name')
                     ->where('clinic_num',$id_clinic)
					 ->where('status','O')
					 ->orderBy('id')->get();
		
         $rates = TblBillRate::where('status','O')->get();
		 $currencys=TblBillCurrency::where('active','O')->orderBy('id','asc')->get();
         
		 $ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
		 
		 $doctors = Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get(); 
		
		
		return view('lab.billing.NewBillPatient')
		       ->with(['ext_labs'=>$ext_labs,'currencys'=>$currencys,'selectgrntr'=>$selectgrntr,
			           'patient'=>$patient,'clinic'=>$clinic,'date_bill'=>$date_bill,
					   'code'=>$code,'methodepay'=>$methodepay,'rates'=> $rates,'doctors'=>$doctors]);
	    
	 
	}

function fillTaxDel($lang,Request $request){
	    
   $descrip0=TblBillAmounts::where('am_code',$request->code)->first();
	$tax=TblBillRate::where('id',$request->selecttax)->first();
  if($descrip0->taxable=='Y'){
				$taxqst=$tax->tvq;
				$taxgst= $tax->tvs;
		 }else{
				$taxqst=0;
				$taxgst=0; 
		 }	
	     
		 $qst=number_format((float)$taxqst, 3, '.', ',');
		 $gst=number_format((float)$taxgst, 3, '.', ',');

	return response()->json(['gst'=>$gst,'qst'=>$qst]);
		  }   



function fillTax($lang,Request $request){
	    
$someArray = [];
$someArray=json_decode($request->data,true); 
$qst=0;
$gst=0;
foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0){
   $code = $area["CODE"];
   $total = $area["TOTAL"];
    $descrip0=TblBillAmounts::where('am_code',$code)->first();
	 $tax=TblBillRate::where('id',$request->selecttax)->first();
  if($descrip0->taxable=='Y'){
				$taxqst=$tax->tvq;
				$taxgst= $tax->tvs;
		 }else{
				$taxqst=0;
				$taxgst=0; 
		 }	
	     $qst=$qst+($total*$taxqst)/100;
	     $gst=$gst+($total*$taxgst)/100;
		
 }
}
		 $qst=number_format((float)$qst, 2, '.', ',');
		 $gst=number_format((float)$gst, 2, '.', ',');

	return response()->json(['gst'=>$gst,'qst'=>$qst]);
		  }   

function fillPrice($lang,Request $request){	
    	
		$txtCode=$request->selectcode;
		$pro=$request->selectpro;
		
		$patient = Patient::find($request->id_patient);
		//$doc_lab = Clinic::where('id',auth()->user()->clinic_num)->first();
	    $user_num = auth()->user()->id;
		
		//get according to user in order to get prices
	  			 
		$doc_lab = ExtLab::where('id',$pro)->first();
		$tbl_name = "tbl_external_labs_prices";	 
	
	   $priced = $pricel = $pricee =0;
	   $test_id = $txtCode;
	   
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
	   
     /*if($pricel=='0'){
	     $pricel0= TblBillCurrency::where('id','2')->first();
	     $pricel=$priced*$pricel0->price;
       }*/	
	   $NBL = TblLabTests::where('id',$txtCode)->where('clinic_num',$request->id_facility)->first();
       $total=$pricel;
	   $cnss=$NBL->cnss;
	   $nbl=$NBL->nbl;
	  
	   
	   $priced = number_format((float)$priced,2,'.','');
	   $pricel = number_format((float)$pricel,2,'.','');
	   $pricee = number_format((float)$pricee,2,'.','');
	   $total = number_format((float)$total,2,'.','');
		
	    return response()->json(['cnss'=>$cnss,'total'=>$total,'nbl'=>$nbl,'pricee'=>$pricee,'priced'=>$priced,'pricel'=>$pricel]);
		  }   

function fillCurrency($lang,Request $request){	
	    $txtCode=$request->selectcurrency;
		$pvdolar=TblBillCurrency::where('id',$txtCode)->first();
		$pdolar=$pvdolar->price;
	    return response()->json(['pdolar'=>$pdolar]);
		  }   

function addTaping($lang,Request $request){
  // alert($this->cpt);
    	 
	  
      if($request->selectcode=="0" || $request->selectcode==NULL){
		  $msg=__('Please select your Code!');
		  return response()->json(['warning' =>$msg]);	
		
			 }
	 
		if (is_numeric($request->valprice)!=1){
				$msg=__('Please enter numeric value in Price');
				return response()->json(['warning' =>$msg]);	
		
		}
//alert($proUid['type']);
//alert($idFacility['type']);
       $totalf=($request->totalf);
       $stotal=($request->stotal);
      $etotal=($request->etotal);	   
	//  $pricel=$request->valprice*$request->valnbl;
	 //  $priced=$request->valpriced*$request->valnbl;
	  //  $pricee=$request->valpricee*$request->valnbl;
		$pricel=$request->valprice;
	   $priced=$request->valpriced;
	    $pricee=$request->valpricee;
	  $nbl=$request->valnbl;
      $cpt=$request->cpt;
	  $descrip=$request->descrip;
	//  $total=($qty*$price)-$discount;
	 $tdiscount=0.00;
		$totalf=$totalf+(($pricel));
		 $stotal=$stotal+(($priced));
		  $etotal=$etotal+(($pricee));
	//  if ($totalf<0){
		//		$msg=__('You cannot Discount more than the total price');
		//		return response()->json(['warning' =>$msg]);	
	
		//}
   
	   $arrTaping = array("id"=>$cpt,
								"id_details"=>"",
                                "bill_id"=>$request->reqID,
                                "code"=>$request->selectcode,
								"code"=>$request->cnss,
                                "descrip"=>$descrip,
                                "nbl"=>$nbl,
								"pricee"=>$pricee,
								"pricel"=>$pricel,
								"priced"=>$priced
								);
								
	
	
		  $balance=$totalf;
		// $balance=$request->balance+(($price*$qty)-$discount)+$qst+$gst;
		//}
	  	 $balance=number_format((float)$balance, 2, '.', ',');
		
	     $totalf=number_format((float)$totalf, 2, '.', ',');
         $stotal=number_format((float)$stotal, 2, '.', ',');		 
		 $etotal=number_format((float)$etotal, 2, '.', ',');		 
			$msg=__('Added Success');
				return response()->json(['success' =>$msg,'arrTaping'=>$arrTaping,'etotal'=>$etotal,'stotal'=>$stotal,'tdiscount'=>$tdiscount,'totalf'=>$totalf,'balance'=>$balance]);	
  
    }
	
function SaveBill($lang,Request $request){
	
$someArray = [];
$someArray=json_decode($request->data,true); 
$result='';
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;

//update bill
if ($request->status=='M'){
 //delete old bill details	
 $last_id=$request->bill_id;
 TblBillSpecifics::where('bill_num',$last_id)->delete();
  
 TblBillHead::where('id',$last_id)
            ->update([
			       'ext_lab'=>isset($request->selectpro) && $request->selectpro!='0'?$request->selectpro:NULL,
				   'doctor_num'=>isset($request->selectdc) && $request->selectdc!='0'?$request->selectdc:NULL,
				   'doctor_bill_num'=>isset($request->selectpro) && $request->selectpro!='0'?$request->selectpro:NULL,
				   'clinic_num'=>$request->id_facility,
				   'patient_num'=>$request->id_patient,
				   'bill_discount'=>$request->tdiscount,
				   'bill_datein'=>$request->bill_date,
				   'user_num'=>$user_id,
				   'user_type'=>$user_type,
				   'notes'=>$request->notes,
				   'status'=>'O'
			       ]);  
 $FacBill = Clinic::where('id',$request->id_facility)->first();
 $reqID=$request->reqID;
		
}else{
	   //new bill save it
	   $last_id=TblBillHead::create([
	           'ext_lab'=>isset($request->selectpro) && $request->selectpro!='0'?$request->selectpro:NULL,
			   'doctor_num'=>isset($request->selectdc) && $request->selectdc!='0'?$request->selectdc:NULL,
			   'doctor_bill_num'=>isset($request->selectpro) && $request->selectpro!='0'?$request->selectpro:NULL,
			   'clinic_num'=>$request->id_facility,
			   'patient_num'=>$request->id_patient,
			   'bill_discount'=>$request->tdiscount,
			   'bill_datein'=>$request->bill_date,
			   'user_num'=>$user_id,
			   'user_type'=>$user_type,
			   'notes'=>$request->notes,
			   'status'=>'O'
	           ])->id;
	   
        $FacBill = Clinic::where('id',$request->id_facility)->first();
      	$SerieFacBill = $FacBill->bill_serial_code;
		$SeqFacBill = $FacBill-> bill_sequence_num ;
		$reqID=trim($SerieFacBill)."-".($SeqFacBill+1);
		Clinic::where('id',$request->id_facility)->update([
				                  'bill_sequence_num' => $SeqFacBill+1
								  ]);
		TblBillHead::where('id',$last_id)->update([
				                  'clinic_bill_num'=>$reqID	
								  ]);	
	}					  
//fac_bill_id to update reqID
$tbillpricel=0.00;
$tbillpriced=0.00;
$tbillpricee=0.00;
//save bill details in new bill or update bill
foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0){
   $code = $area["CODE"];
   $descrip = html_entity_decode(trim($area["DESCRIP"]));
   $nbl=isset($area["NBL"]) && $area["NBL"]!=''?$area["NBL"]:NULL;
   $cnss=$area["CNSS"];
   $pricel = str_replace(',', '', $area["PRICEL"]);
   $priced = $area["PRICED"];

//update 30-08-2024  refered labs prices

$currencyEUR=TblBillCurrency::where('active','O')->where('abreviation','EUR')->first();
$lbl_eur = isset($currencyEUR)?$currencyEUR->price:95000;

$pricee=isset($pricel)?number_format($pricel / $lbl_eur, 2) : NULL;


$orderid=TblBillHead::where('id',$last_id)->first();
$order_id= isset($orderid)?$orderid->order_id:NULL;
$reflab=DB::table('tbl_visits_order_custom_tests')->where('order_id', '=', $order_id)->where('test_id', '=', $code)->first();
$ref_lab=isset($reflab)?$reflab->referred_lab:NULL;
$ref_price=DB::table('tbl_referred_labs_prices')->where('lab_id', '=', $ref_lab)->where('test_id', '=', $code)->first();

$ref_lb_price=isset($ref_price)?$ref_price->totall:NULL;
$ref_dollar_price=isset($ref_price)?$ref_price->totald:NULL;
$ref_euro_price = isset($ref_price) ? number_format($ref_price->totall / $lbl_eur, 2) : NULL;


//end update 

   //no need for this
  // $result.= $code.",".$descrip.",".$quantity.",".$price.",".$discount.",".$total.PHP_EOL;
  TblBillSpecifics::create([
               'bill_num'=>$last_id,
			   'doctor_num'=>isset($request->selectdc) && $request->selectdc!='0'?$request->selectdc:NULL,
			   'ext_lab'=>isset($request->selectpro) && $request->selectpro!='0'?$request->selectpro:NULL,
			   'user_num'=>$user_id,
			   'user_type'=>$user_type,
			   'bill_code'=>$code,
			   'cnss'=>$cnss,
			   'bill_name'=>$descrip,
			   'bill_quantity'=>$nbl,
			   'lbill_price'=>$pricel,
			   'bill_price'=>$priced,
			   'ebill_price'=>$pricee,
			   'ref_lab'=>$ref_lab,
			   'ref_lbill_price'=>$ref_lb_price,
			   'ref_dolarprice'=>$ref_dollar_price,
			   'ref_ebill_price'=>$ref_euro_price,
               'status'=>'O'  
                ]);
  $tbillpricel=$tbillpricel+ $pricel;
  $tbillpriced=$tbillpriced+ $priced;
 }	

}
      
	  $currencyUSD=TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
      $lbl_usd = isset($currencyUSD)?$currencyUSD->price:90000;
	  $bill = TblBillHead::find($last_id);
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
			$tdiscountd = floatval($tdiscount/$lbl_usd);
		 }
	    }
	 
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
		$totalf=$tbillpricel;
		$stotal=$tbillpriced;
		$totalf=number_format((float)$totalf, 2, '.', ',');
		$stotal=number_format((float)$stotal, 2, '.', ',');
		$msg=__('Bill saved successfully');
		
if ($request->status=='M'){	
  return response()->json(['success'=>$msg,'totalf'=>$totalf,'stotal'=>$stotal,'reqID'=>$reqID,'last_id'=>$last_id,'sumpay'=>$sumpayl,'sumref'=>$sumrefl,'nbalance'=>$balancel,'tdiscount'=>$tdiscountl,
                         'tdiscountd'=>$tdiscountd,'sumpayd'=>$sumpayd,'sumrefd'=>$sumrefd,'balanced'=>$balanced]);	  	 

}else{
	$location = route('lab.billing.edit',[$lang,$last_id]);
	return response()->json(['success'=>$msg,'location'=>$location]);	
}
}   	
function SavePay($lang,Request $request){

$someArray = [];
$someArray=json_decode($request->data,true); 		
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
$order_id=TblBillHead::where('status','O')->where('id',$request->bill_id)->value('order_id');
if(!isset($order_id)){
$order_id=0; 	
}
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
   $guarantor = isset($area["GUARANTOR"]) && $area["GUARANTOR"]!=""?$area["GUARANTOR"]:0;
    if ($lang=='fr'){	
      $typepay = TblBillPaymentMode::where('name_fr',$type)->where('status','O')->first();
   }else{
	  $typepay = TblBillPaymentMode::where('name_eng',$type)->where('status','O')->first();   
   }
   $reference= $typepay->id;
   $assurance=$typepay->assurance;
   $sqlInsertF = "insert into tbl_bill_payment(datein,bill_num,clinic_num,payment_amount,payment_type,currency,dolarprice,lpay_amount,dpay_amount,reference,user_type,assurance,order_num,guarantor,user_num,status) values('".
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
	DB::select(DB::raw("$sqlInsertF"));
 
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
$order_id=TblBillHead::where('status','O')->where('id',$request->bill_id)->value('order_id');
if(!isset($order_id)){
$order_id=0; 	
}
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
   $guarantor = isset($area["GUARANTOR"]) && $area["GUARANTOR"]!=""?$area["GUARANTOR"]:0;
   $totald = number_format((float)$total/$lbl_usd,2,'.','');
  if ($lang=='fr'){	
  $typepay = TblBillPaymentMode::where('name_fr',$type)->where('status','O')->first();
   }else{
	$typepay = TblBillPaymentMode::where('name_eng',$type)->where('status','O')->first();   
   }
   $reference= $typepay->id;
   $assurance=$typepay->assurance;
   $sqlInsertF = "insert into tbl_bill_payment(datein,bill_num,clinic_num,payment_amount,payment_type,currency,dolarprice,lpay_amount,dpay_amount,reference,user_type,assurance,order_num,guarantor,user_num,status) values('".
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
	DB::select(DB::raw("$sqlInsertF"));
 
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

$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
$bill_id=$request->bill_id;
$clinic_id=$request->id_facility;

$billRate=TblBillHead::where('id',$bill_id)->value('exchange_rate');
$lbl_usd = isset($billRate)?$billRate:90000;
$currency1 = $request->currency;
$valamountdiscount= floatval($request->valamountdiscount);

$ReqBill=TblBillHead::find($bill_id);
$totall = $ReqBill->lbill_total;
$totald = $ReqBill->bill_total;


  if($currency1=='2'){
	//change USD discount to LBP
    $valamountdiscountd = $valamountdiscount;
	$valamountdiscount = $valamountdiscount*$lbl_usd;	
   }elseif($currency1=='1'){
	//currency is LBP
	$valamountdiscountd = number_format(floatval($valamountdiscount/$lbl_usd),2,'.','');
	$valamountdiscount = $valamountdiscount;	
	}
   

$sumpay=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
$sumref=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');


$Nbalance=number_format((float)$ReqBill->lbill_total-$valamountdiscount-$sumpay+$sumref+$ReqBill->tvq+$ReqBill->tps, 2, '.', ',');
$balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));

$sumpayd=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','P')->sum('dpay_amount');
$sumrefd=TblBillPayment::where('bill_num','=',$bill_id)->where('status','Y')->where('payment_type','=','R')->sum('dpay_amount');


$Nbalanced=number_format((float)$ReqBill->bill_total-$valamountdiscountd-$sumpayd+$sumrefd+$ReqBill->tvq+$ReqBill->tps, 2, '.', ',');
$balanced=floatval(preg_replace('/[^\d.-]/', '', $Nbalanced));



TblBillHead::where('id',$bill_id)->update(['bill_balance'=>$balance,'bill_balance_us'=>$balanced,'bill_discount'=>$valamountdiscount,'bill_discount_us'=>$valamountdiscountd]);	

//data to return to user
$sumpayd =  number_format((float)$sumpayd,2,'.','');
$sumpayl =  number_format((float)$sumpay,2,'.',',');

$sumrefd =  number_format((float)$sumrefd,2,'.','');
$sumrefl =  number_format((float)$sumref,2,'.',',');

$balanced =  number_format((float)$balanced,2,'.','');
$balancel=number_format((float)$balance, 2, '.', ',');

$tdiscountd =  number_format((float)$valamountdiscountd,2,'.','');
$tdiscountl =  number_format((float)$valamountdiscount,2,'.',',');
			  
$msg=__('Discount Saved Success');

return response()->json(['success'=>$msg,'sumpay'=>$sumpayl,'sumref'=>$sumrefl,'nbalance'=>$balancel,'tdiscount'=>$tdiscountl,
                         'tdiscountd'=>$tdiscountd,'sumpayd'=>$sumpayd,'sumrefd'=>$sumrefd,'balanced'=>$balanced]);	  

}		  		  		  		  
			  
	
function GetSumPayRef($lang,Request $request){
$bill_id=$request->bill_id;
$sumpay=TblBillPayment::where('bill_num','=',$bill_id)->where('payment_type','=','P')->sum('lpay_amount');
$sumref=TblBillPayment::where('bill_num','=',$bill_id)->where('payment_type','=','R')->sum('lpay_amount');

	return response()->json(['sumpay'=>$sumpay,'sumref'=>$sumref]);	
}



function deleteBilllab($lang,Request $request){
	
	$user_id = auth()->user()->id;
	$bill_id=$request->id;
	switch($request->type){
	case 'activate':
	TblBillHead::where('id',$bill_id)->update([
									  'status' => 'O',
									  'user_num' => auth()->user()->id
									]);

	$msg=__('Bill Activated Successfully');
	break;
	case 'inactivate':
	TblBillHead::where('id',$bill_id)->update([
									  'status' => 'N',
									  'user_num' => auth()->user()->id
									]);

	$msg=__('Bill Cancelled Successfully');
	break;
		
	}
	return response()->json(['success'=>$msg]);
}		  


public function downloadPDFBilling($lang,Request $request){
            
			$response=$this->generatePDFBilling($request->id,$request->tafqeetTotal);

            return $response;
        
        }
   public function generatePDFBilling($id,$tafqeet)
    
        {
            $Bill =TblBillHead::find($id);
            $clinic=Clinic::where('id',$Bill->clinic_num)->first();
            $pro=Doctor::where('id',$Bill->doctor_num)->first();
			$ext_lab=ExtLab::where('id',$Bill->ext_lab)->first();
            $patient=Patient::where('id',$Bill->patient_num)->first();
            $result=TblBillSpecifics::where('status','O')->where('bill_num',$Bill->id)->get();
			$logo=DB::table('tbl_bill_logo')->where('clinic_num',$Bill->clinic_num)->where('status','O')->first();
			$signature_path = NULL;
			$pay = DB::table('tbl_bill_payment')->where('bill_num', '=', $Bill->id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('lpay_amount');
	        $ref = DB::table('tbl_bill_payment')->where('bill_num', '=', $Bill->id)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('lpay_amount');
			$payd = DB::table('tbl_bill_payment')->where('bill_num', '=', $Bill->id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('dpay_amount');
	        $refd = DB::table('tbl_bill_payment')->where('bill_num', '=', $Bill->id)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('dpay_amount');
			$request_nb = DB::table('tbl_visits_orders')->where('id',$Bill->order_id)->value('request_nb');  
	          if(isset($pro)){
						 
						 if( (auth()->user()->type==1 && $pro->show_sign=='true')
							 || (auth()->user()->type==2 && $pro->show_sign=='true' && $pro->show_sign_for_clinic=='true')){
						   $user_signature = DoctorSignature::where('id',$pro->sign_num)->where('active','O')->first();
						   
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
											
												  $image
                                                  ->resize(200, 200, function ($constraint) {
												  $constraint->aspectRatio();

													// if you need to prevent upsizing, you can add:
												  $constraint->upsize();
												 
												  })->save($mainPath.$filename);

										}
						 
						 	$signature_path = $mainPath.$filename;				 
						   }//end sig chk
						   
					}	
			 
		 $qst=($Bill->totalf*$Bill->taxqst)/100;
	     $gst=($Bill->totalf*$Bill->taxgst)/100;
		 $qst=number_format((float)$qst, 2, '.', ',');
		 $gst=number_format((float)$gst, 2, '.', ',');
		
		  $balance=$Bill->bill_balance;
	  	// $balance=number_format((float)$balance, 2, '.', ',');
			 //dd($signature_path);
  
            $data = [
    
                'title' => __('Bill'),
                'date' => date('m/d/Y'),
				'logo'=>$logo,
                'signature_path' => $signature_path,
                'Bill' => $Bill,
                'clinic' => $clinic,
                'pro' =>$pro,
				'ext_lab'=>$ext_lab,
                'patient' => $patient,
                'result' => $result,
				'balance' =>$balance,
				'pay' =>$pay,
				'ref' =>$ref,
                'patient'=>$patient,
				'payd'=>$payd,
				'refd'=>$refd,
				'request_nb'=>$request_nb
                     ]; 
    
            $pdf = PDF::setOptions(['defaultFont' => 'helvetica','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
                        -> loadView('lab.billing.BillPDF', $data);
           
		   $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));         
			//$pdf_path = public_path('/custom/Bills/');
            //$uid=auth()->user()->id;
            
            //$main_path = $pdf_path . date("Y-m-d")."/{$id}"."/{$uid}/";
            //if (!file_exists($pdf_path)) {
              //      mkdir($pdf_path, 0775, true);
            //}
            
                        
             //delete old existing files
             //$files = glob($pdf_path.'*'); // get all file names
               // foreach($files as $file){ // iterate files
                 // if(is_file($file)) {
                   // unlink($file); // delete file
                  //}
                //}
             //create new pdf in directory
             //$name=date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
             //$bill_pdf = $pdf_path . $name;
           
			 //file_put_contents($bill_pdf, $pdf->output());
	          //  return response()->download( $bill_pdf);
                return $pdf->stream();
        }

function GetRef($lang,Request $request){
$html='';
if($request->type=='REF'){
	$ReqRef=TblBillPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_bill_payment.reference')
				->where('tbl_bill_payment_mode.status', 'O')
				->where('tbl_bill_payment.status','Y')
				->where('bill_num',$request->bill_id)
				->where('payment_type','R')
				->get(['tbl_bill_payment.*', 'tbl_bill_payment_mode.name_fr', 'tbl_bill_payment_mode.name_eng']);
	$cptrCount= $ReqRef->count();		
	$cptr = 1;
	$html='<thead>
			<tr class="txt-bg text-white text-center">
			<th scope="col" style="font-size:16px;">'.__("#").'</th>
			<th scope="col" style="font-size:16px;">'.__("Date").'</th>
			<th scope="col" style="font-size:16px;">'.__("Type").'</th>
			<th scope="col" style="font-size:16px;">'.__("Currency").'</th>
			<th scope="col" style="font-size:16px;">'.__("Amount").'</th>
			<th scope="col" style="font-size:16px;">'.__("Rate").'</th>
			<th scope="col" style="font-size:16px;">'.__("Amount LBP").'</th>
			<th scope="col"></th>
			</tr>
			</thead>
			<tbody>';
	foreach ($ReqRef as $sReqRefs) {
			$type_name = ($lang=='fr')?$sReqRefs->name_fr:$sReqRefs->name_eng;

			$html .='<tr>
			<td>'.$cptr.'</td>
			<td>'.$sReqRefs->datein.'</td>
			<td>'.$type_name.'</td>
			<td>'.$sReqRefs->currency.'</td>
			<td>'.$sReqRefs->payment_amount.'</td>
			<td>'.$sReqRefs->dolarprice.'</td>
			<td>'.$sReqRefs->lpay_amount.'</td>
			<td><input type="button" class="btn-btn-delete" id="rowdeleteref'.$cptr.'" value="'.__("Delete").'" onclick="deleteRowRef(this)"  /></td>
			</tr>';
	$cptr++;
	}
	$html .='</tbody>';
}
$sumpay=TblBillPayment::where('bill_num','=',$request->bill_id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
$sumref=TblBillPayment::where('bill_num','=',$request->bill_id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');
$Nsumpay=number_format((float)$sumpay, 2, '.', ',');
$sumpay=floatval(preg_replace('/[^\d.-]/', '', $Nsumpay));
$Nsumref=number_format((float)$sumref, 2, '.', ',');
$sumref=floatval(preg_replace('/[^\d.-]/', '', $Nsumref));
$ReqBill=TblBillHead::where('id',$request->bill_id)->where('status','O')->first();
$bill_discount=floatval(preg_replace('/[^\d.-]/', '', $ReqBill->bill_discount));

$Nbalance=number_format((float)$ReqBill->lbill_total-$bill_discount-$sumpay+$sumref+$ReqBill->tvq+$ReqBill->tps, 2, '.', ',');
$balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));
return response()->json(['html' => $html,'sumpay'=>$sumpay,'sumref'=>$sumref,'balance'=>$balance,'bill_discount'=>$bill_discount]);
	
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
}elseif($type=='DIS'){
	$html1='';
}
	


$sumpay=TblBillPayment::where('bill_num','=',$request->bill_id)->where('status','Y')->where('payment_type','=','P')->sum('lpay_amount');
$sumref=TblBillPayment::where('bill_num','=',$request->bill_id)->where('status','Y')->where('payment_type','=','R')->sum('lpay_amount');
$Nsumpay=number_format((float)$sumpay, 2, '.', ',');
$sumpay=floatval(preg_replace('/[^\d.-]/', '', $Nsumpay));
$Nsumref=number_format((float)$sumref, 2, '.', ',');
$sumref=floatval(preg_replace('/[^\d.-]/', '', $Nsumref));
$ReqBill=TblBillHead::where('id',$request->bill_id)->where('status','O')->first();
$bill_discount=floatval(preg_replace('/[^\d.-]/', '', $ReqBill->bill_discount));
$totall = $ReqBill->lbill_total;
$Nbalance=number_format((float)$totall-$bill_discount-$sumpay+$sumref+$ReqBill->tvq+$ReqBill->tps, 2, '.', ',');
$balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));


$sumpayd=TblBillPayment::where('bill_num','=',$request->bill_id)->where('status','Y')->where('payment_type','=','P')->sum('dpay_amount');
$sumrefd=TblBillPayment::where('bill_num','=',$request->bill_id)->where('status','Y')->where('payment_type','=','R')->sum('dpay_amount');
$Nsumpayd=number_format((float)$sumpayd, 2, '.', ',');
$sumpayd=floatval(preg_replace('/[^\d.-]/', '', $Nsumpayd));
$Nsumrefd=number_format((float)$sumrefd, 2, '.', ',');
$sumrefd=floatval(preg_replace('/[^\d.-]/', '', $Nsumrefd));
$ReqBill=TblBillHead::where('id',$request->bill_id)->where('status','O')->first();
$bill_discount_us=floatval(preg_replace('/[^\d.-]/', '', $ReqBill->bill_discount_us));
$totald = $ReqBill->bill_total;
$Nbalanced=number_format((float)$totald-$bill_discount_us-$sumpayd+$sumrefd+$ReqBill->tvq+$ReqBill->tps, 2, '.', ',');
$balanced=floatval(preg_replace('/[^\d.-]/', '', $Nbalanced));


return response()->json(['html1' => $html1,'sumpay'=>$sumpay,'sumref'=>$sumref,'balance'=>$balance,
                         'sumpayd'=>$sumpayd,'sumrefd'=>$sumrefd,'balanced'=>$balanced,
						 'totall'=>$totall,'totald'=>$totald,'discount'=>$bill_discount,'discountd'=>$bill_discount_us]);
	
}
public function send_patient($lang,Request $request){
  $bill_id = $request->bill_id; 
  //bill id
  $bill = TblBillHead::find($bill_id);
  
  $patient = Patient::find($bill->patient_num);
  $pat_email = $patient->email;
  $pat_name = $patient->first_name.' '.$patient->last_name;
  
  if($pat_email == NULL || $pat_email == ''){
	  $msg = __("No email is provided for the patient")." : ".$pat_name;
	  return response()->json(['error'=>$msg]);
  }
  
  if(!$patient->receive_mail){
	  $msg = __("This patient does not accept to be contacted by email");
	  return response()->json(['error'=>$msg]); 
  }
  
   
  
  $clinic = Clinic::where('id',$bill->clinic_num)->first();
  $branch_name = $clinic->full_name;
  $branch_email = isset($clinic->email)?$clinic->email:__("Undefined");
  $branch_address = isset($clinic->full_address)?$clinic->full_address:__("Undefined");
  $branch_tel = isset($clinic->telephone)?$clinic->telephone:__("Undefined");
  $branch_fax = isset($clinic->fax)?$clinic->fax:__("Undefined");
  //generate pdf to send by email or fax or both
  $pdf = $this->generatePDFBilling($bill->id,'send');
  
  $resp_email='';
 
  $title=__('Hello').' '.$pat_name.' , ';
  $msg_rem = NULL;
  $subject = __('Bill').'-'.$branch_name;
  $msg1=__('You will find attached your medical bill').' . ';
  $msg2=__('This medical bill is sent from branch').' : '.$branch_name.' . ';
  $from = $branch_name;
  $reply_to_name = __("No reply").','.$branch_name;
  $reply_to_address = isset($clinic->email)? $clinic->email:'noreply@email.com';
  $details = ['title'=>$title,'msg_rem'=>$msg_rem,'msg1'=>$msg1,'msg2'=>$msg2,
	             'branch_name'=>$branch_name,'branch_address'=>$branch_address,'branch_tel'=>$branch_tel,
				 'branch_fax'=>$branch_fax,'branch_email'=>$branch_email,
				 'from'=>$from,'reply_to_name'=>$reply_to_name,'reply_to_address'=>$reply_to_address,'subject'=>$subject];
	 $to  = $pat_email;
	 $pdf_name = __('BILL').'-'.Carbon::parse($bill->bill_datein)->format('Y-m-d H:i').'.pdf';
	 
	 Mail::to($to)->send(new SettingMailAttach($details,$pdf,$pdf_name));
	 
     if (Mail::failures()) {
				$msg= __("Email: fail");
				return response()->json(['error'=>$msg]);
			}else{
				$msg= __("Email : success");
				$email_pat = Carbon::now()->format('Y-m-d H:i');
				TblBillHead::where('id',$bill->id)->update(['email_pat'=>$email_pat]);	
				return response()->json(['success'=>$msg,'email_notice'=>$email_pat]);
			}	 
	
}	
}




