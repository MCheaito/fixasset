<?php
/*
* DEV APP
* Created date : 20-10-2022
*  
*
*/
namespace App\Http\Controllers\lab\extlabs;
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
use App\Models\TblBillGuarPayment;
use App\Models\ExtLab;
use Alert;
use DataTables;
use PDF;
use Image;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use UserHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\SettingMailAttach;

class GuarLabsController extends Controller
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
	   /// pay 
	   $myId=auth()->user()->id;	
	
	
	$FromFacility = Clinic::where('id', $idClinic)->where('active','O')->first();
    $idFacility =$FromFacility->id;
   	
if ($lang=='fr'){			 
	 $sqlPay = "select id,name_fr as name from tbl_bill_payment_mode where clinic_num='".$idFacility."'  and status='O' order by id desc";
}else{
	 $sqlPay = "select id,name_eng as name from tbl_bill_payment_mode where clinic_num='".$idFacility."'  and status='O' order by id desc";
	
}
	$methodepay= DB::select(DB::raw("$sqlPay"));	
   	  $currencys=TblBillCurrency::where('active','O')->get();

	   //endpay
	 	$doctors = Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
		//$ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
		$ext_labs=DB::table('tbl_referred_labs')->where('status','Y')->orderBy('full_name')->get();
		$guar_labs=DB::table('tbl_external_labs')->where('status','A')->orderBy('full_name')->get();

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
			
			
			 $filter_datefacture= "";
				
			if( ($request->filter_fromdatefacture!="" &&  $request->filter_fromdatefacture!=NULL)
				&& ($request->filter_todatefacture=="" || $request->filter_todatefacture==NULL) ){
			     $filter_datefacture= "and DATE(spec.ref_date_facture) >= '".$request->filter_fromdatefacture."' ";
				}
			
			if( ($request->filter_todatefacture!="" &&  $request->filter_todatefacture!=NULL)
				&& ($request->filter_fromdatefacture=="" || $request->filter_fromdatefacture==NULL) ){
			     $filter_datefacture= "and DATE(spec.ref_date_facture) <= '".$request->filter_todatefacture."' ";
				}	
				
			if( ($request->filter_fromdatefacture!="" &&  $request->filter_fromdatefacture!=NULL)
				&& ($request->filter_todatefacture!="" &&  $request->filter_todatefacture!=NULL) ){
			     $filter_datefacture = "and DATE(spec.ref_date_facture) BETWEEN '".$request->filter_fromdatefacture."' AND '".$request->filter_todatefacture."'";
				}	
			
			
			$filter_datepaid= "";
				
			if( ($request->filter_fromdatepaid!="" &&  $request->filter_fromdatepaid!=NULL)
				&& ($request->filter_todatepaid=="" || $request->filter_todatepaid==NULL) ){
			     $filter_datepaid= "and DATE(spec.ref_date_paid) >= '".$request->filter_fromdatepaid."' ";
				}
			
			if( ($request->filter_todatepaid!="" &&  $request->filter_todatepaid!=NULL)
				&& ($request->filter_fromdatepaid=="" || $request->filter_fromdatepaid==NULL) ){
			     $filter_datepaid= "and DATE(spec.ref_date_paid) <= '".$request->filter_todatepaid."' ";
				}	
				
			if( ($request->filter_fromdatepaid!="" &&  $request->filter_fromdatepaid!=NULL)
				&& ($request->filter_todatepaid!="" &&  $request->filter_todatepaid!=NULL) ){
			     $filter_datepaid = "and DATE(spec.ref_date_paid) BETWEEN '".$request->filter_fromdatepaid."' AND '".$request->filter_todatepaid."'";
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
			 $filter_status = "and spec.ref_paid='".$request->filter_status."'";  
		   }
		   
		     $filter_facture="";
           	if(isset($request->filter_facture)){
			 $filter_facture = "and spec.ref_facture='".$request->filter_facture."'";  
		   }
		   
		    $filter_sent="";
           	if(isset($request->filter_sent)){
			 $filter_sent = "and b.reference='".$request->filter_sent."'";  
		   }
		   $filter_ext_lab="";
           	
			if(isset($request->filter_g) && $request->filter_g!="0" ){
			 $filter_ext_lab = "and o.ext_lab='".$request->filter_g."' ";  
		     }
			 
			 $filter_doc="";
           	
			if(isset($request->filter_doc) && $request->filter_doc!="0" ){
			 $filter_doc = "and tbl_bill_head.doctor_num='".$request->filter_doc."' ";  
		     }
			 
			  $arr = array('from_date_bill'=>isset($request->filter_fromdate)?$request->filter_fromdate:'',
			             'to_date_bill'=>isset($request->filter_todate)?$request->filter_todate:'',
						 'from_date_facture'=>isset($request->filter_fromdatefacture)?$request->filter_fromdatefacture:'',
			             'to_date_facture'=>isset($request->filter_todatefacture)?$request->filter_todatefacture:'',
						 'from_date_paid'=>isset($request->filter_fromdatepaid)?$request->filter_fromdatepaid:'',
			             'to_date_paid'=>isset($request->filter_todatepaid)?$request->filter_todatepaid:'',
			             'status_bill'=>$request->filter_status,'patient_num_bill'=>$request->filter_patient,
						 'clinic_num_bill'=>$request->id_facility,'ext_lab'=>$request->id_pro);
						 
			//UserHelper::drop_session_keys($arr);	
			//UserHelper::generate_session_keys($arr);
		   
			$sql = "SELECT 
			spec.id AS id,
			GROUP_CONCAT(DISTINCT tbl_bill_head.clinic_bill_num ORDER BY tbl_bill_head.bill_datein) AS fac_bill,
			GROUP_CONCAT(DISTINCT sentFromclin.full_name) AS fromClinicName,
			GROUP_CONCAT(DISTINCT CONCAT(
				IFNULL(tbl_patients.first_name, ''),
				IF(tbl_patients.middle_name IS NOT NULL AND tbl_patients.middle_name <> '', CONCAT(' ', tbl_patients.middle_name), ''),
				' ', IFNULL(tbl_patients.last_name, ''),
				',', IFNULL(tbl_patients.birthdate, 'N/D'),
				',', IFNULL(tbl_patients.cell_phone, 'N/D')
			)) AS patDetail,
			GROUP_CONCAT(DISTINCT o.request_nb) AS request_nb,
			MAX(spec.bill_code) AS bill_code, 
			MAX(spec.reference) AS reference, 
			MAX(spec.bill_name) AS bill_name, 
			MAX(spec.ref_lab) AS ref_lab, 
			MAX(spec.ref_lbill_price) AS ref_lbill_price, 
			MAX(spec.ref_dolarprice) AS ref_dolarprice, 
			MAX(spec.ref_ebill_price) AS ref_ebill_price,
			MAX(spec.ref_paid) AS ref_paid, 
			MAX(spec.ref_date_paid) AS ref_date_paid,
			MAX(spec.bill_price) AS bill_price, 
			MAX(spec.ebill_price) AS ebill_price,
			MAX(tbl_bill_head.bill_total) AS bill_total, 
			MAX(spec.ref_paid) AS paid,
			MAX(spec.ref_facture) AS facture,
			MAX(spec.ref_date_facture) AS ref_date_facture,	
			GROUP_CONCAT(DISTINCT cust.sent_referredlab) AS sent_referredlab,
			IFNULL(MAX(l.full_name), '') AS ext_lab_name,
			IFNULL(MAX(CONCAT(doc.first_name, 
				IF(doc.middle_name IS NOT NULL AND doc.middle_name <> '', CONCAT(' ', doc.middle_name, ' '), ' '), 
				doc.last_name)), '') AS doctor_name
		FROM tbl_bill_head
		INNER JOIN tbl_bill_specifics AS spec ON tbl_bill_head.id = spec.bill_num
		INNER JOIN tbl_clinics AS sentFromclin ON tbl_bill_head.clinic_num = sentFromclin.id
		LEFT JOIN tbl_visits_orders AS o ON o.id = tbl_bill_head.order_id
		LEFT JOIN tbl_doctors AS doc ON tbl_bill_head.doctor_num = doc.id
		LEFT JOIN tbl_external_labs AS l ON l.id = o.ext_lab
		LEFT JOIN tbl_visits_order_custom_tests AS cust ON o.id = cust.order_id
		LEFT JOIN tbl_patients AS tbl_patients ON tbl_bill_head.patient_num = tbl_patients.id
		LEFT JOIN tbl_bill_payment as b ON b.bill_num=tbl_bill_head.id
		WHERE b.payment_type='P' and spec.status='O' AND o.active='Y' AND b.status='Y' 
			".$filter_status." 
			".$filter_doc." 
			".$filter_patient." 
			".$filter_facility." 
			".$filter_date." 
			".$filter_datefacture." 
			".$filter_datepaid." 
			".$filter_sent."  
			".$filter_facture."  
			".$filter_ext_lab."
		GROUP BY spec.id";

		  $bills= DB::select(DB::raw("$sql"));
           //dd($sql);
            return Datatables::of($bills)

                    ->addIndexColumn()
					->addColumn('Paid', function($row){
				            $checked = ($row->paid=='Y')?'checked':'';
							$disabled= ($row->paid=='N' )?'':'disabled';
							$state = ($row->paid=='Y')?'nopaid':'paid';
							$btn = '<label class="mt-2 slideon slideon-xs slideon-success">
										<input type="checkbox" class="row-paid" onclick="event.preventDefault(); Paid('.$row->id.', \''.$row->bill_code.'\', \''.$state.'\')" '.$checked.'>
										<span class="slideon-slider" title="'.__("Paid").'"></span>
									</label>';

						  return $btn;
                    })
                   
					->addColumn('GFacture', function($row){
				            $checked1 = ($row->facture=='Y')?'checked':'';
							$disabled1= ($row->facture=='N' )?'':'disabled';
							$state1 = ($row->facture=='Y')?'nofacture':'facture';
							$btn1 = '<label class="mt-2 slideon slideon-xs slideon-success">
										<input type="checkbox" class="row-facture" onclick="event.preventDefault(); GuarFacture('.$row->id.', \''.$row->bill_code.'\', \''.$state1.'\')" '.$checked1.'>
										<span class="slideon-slider" title="'.__("Facture").'"></span>
									</label>';

						  return $btn1;
                    })
                    ->rawColumns(['Paid','GFacture'])
					
                    ->make(true);

        }
	   
    $tdollar = DB::table('tbl_bill_specifics')->where('ref_lab', NULL)->where('status', 'O')->sum('bill_price');
    $teuro = DB::table('tbl_bill_specifics')->where('ref_lab', NULL)->where('status', 'O')->sum('ebill_price');
    $paydollar = DB::table('tbl_bill_specifics')->where('ref_paid', 'Y')->where('ref_lab', NULL)->where('status', 'O')->sum('ref_dolarprice');
    $payeuro = DB::table('tbl_bill_specifics')->where('ref_paid', 'Y')->where('ref_lab', NULL)->where('status', 'O')->sum('ref_ebill_price');
	$tdollar=number_format((float)$tdollar,2,'.',',');
	$teuro=number_format((float)$teuro,2,'.',',');
	$paydollar=number_format((float)$paydollar,2,'.',',');
	$payeuro=number_format((float)$payeuro,2,'.',',');
$ReqPay=TblBillGuarPayment::where('tbl_bill_guar_payment.status','Y')
			->where('guarantor',$request->bill_id)
            ->get();
$cptpCount= $ReqPay->count();		
	   return view('lab.extlabs.index')->with(['cptpCount'=>$cptpCount,'methodepay'=>$methodepay,'currencys'=>$currencys,'tdollar'=>$tdollar,'teuro'=>$teuro,'paydollar'=>$paydollar,'payeuro'=>$payeuro,'FromFacility'=>$clinics,'ext_labs'=>$ext_labs,'doctors'=>$doctors,
	                                           'Patients'=>$Patients,'filter_patients'=>$filter_patients,'guar_labs'=>$guar_labs]); 
	  
	    		
}

public function GuarPaidTest($lang,Request $request){
$id = $request->id;
	$type = $request->state;
	$code = $request->bill_code;
	$datein = $request->datein;

	$user_num = auth()->user()->id;
	switch($type){
		case 'paid': 
		//	$is_valid1 = $request->is_valid1;
			
		//	if($is_valid1 == 'Y'){
			   $ref_lab = DB::table('tbl_bill_specifics')->where('status', 'O')->where('id', $id)->first();
			   $guar_lab = DB::table('tbl_bill_head')->where('status', 'O')->where('id', $ref_lab->bill_num)->first();
			   $price_ref_lab = DB::table('tbl_external_labs_prices')->where('lab_id', $guar_lab->ext_lab)->where('test_id', $code)->first();
			   if (isset($price_ref_lab)){
			   TblBillSpecifics::where('id',$id)->where('bill_code',$code)->update(['ref_lbill_price'=>$price_ref_lab->totall,'ref_dolarprice'=>$price_ref_lab->totald,'ref_ebill_price'=>$price_ref_lab->totale,'ref_paid'=>'Y','user_num'=>$user_num, 'ref_date_paid' =>$datein]);
			   //only update if there is no report datetime 
			   
			   $msg = __('Test Paid');
			   }else{
				$msg = __('Test Code no have Price');  
			   }
		break;
		
		case 'nopaid': 
	
			   TblBillSpecifics::where('id',$id)->where('bill_code',$code)->update(['ref_lbill_price'=>'0.00','ref_dolarprice'=>'0.00','ref_ebill_price'=>'0.00','ref_paid'=>'N','user_num'=>$user_num, 'ref_date_paid' =>NULL]);
			   $msg = __('Test UnPaid');
			 //$location = route('lab.visit.edit',[$lang,$id]);
			
		break;
		}
	 return response()->json(['msg'=>$msg,'type'=>$type]);

}

public function FactureTest_old($lang,Request $request){
$id = $request->id;
	$type = $request->state1;
	$code = $request->bill_code;
	$datein = $request->datein;
	$user_num = auth()->user()->id;
	switch($type){
		case 'facture': 
		//	$is_valid1 = $request->is_valid1;
			
		//	if($is_valid1 == 'Y'){
			 //  $ref_lab = DB::table('tbl_bill_specifics')->where('status', 'O')->where('id', $id)->first();
			 //  $price_ref_lab = DB::table('tbl_external_labs_prices')->where('lab_id', $ref_lab->ext_lab)->where('test_id', $code)->first();
			 //  if (isset($price_ref_lab)){
			   TblBillSpecifics::where('id',$id)->where('bill_code',$code)->update(['ref_facture'=>'Y','user_num'=>$user_num, 'ref_date_facture' =>$datein = $datein]);
			   //only update if there is no report datetime 
			   
			   $msg = __('Test Facturation');
			//   }else{
			//	$msg = __('Test Code no have Price');  
			//   }
		break;
		
		case 'nofacture': 
	
			   TblBillSpecifics::where('id',$id)->where('bill_code',$code)->update(['ref_facture'=>'N','user_num'=>$user_num, 'ref_date_facture' => NULL]);
			   $msg = __('Test UnFacturation');
			 //$location = route('lab.visit.edit',[$lang,$id]);
			
		break;
		}
	 return response()->json(['msg'=>$msg,'type'=>$type]);

}

public function FactureTest($lang,Request $request){
   
	$datein = $request->datein;
	$datedue = $request->datedue;
	$user_num = auth()->user()->id;
	$filter_g = $request->filter_g;
	$selectedValues = $request->input('selectedValues', []); 
	//$selectedValuesN = $request->input('selectedValuesN', []); 
	$type = ($request->selectall=='true')?'Y':'N';
	//$type1 = ($request->unselectall=='true')?'Y':'N';
 $maxid = TblBillSpecifics::max('reference'); 
 $maxid = $maxid + 1;
	
	//if($type=='Y'){
		foreach ($selectedValues as $id) {
			$MaSpec= TblBillSpecifics::where('id',intval($id))->first();
				if ($MaSpec->ref_facture=='N'){
				   TblBillSpecifics::where('id',intval($id))->update([
				   'ref_facture'=>'Y',
				   'user_num'=>$user_num, 
				   'ref_date_facture' =>$datein,
				   'ref_datedue_facture' =>$datedue,
				   'reference'=>$maxid
				   ]);
				   
				}else{
				   TblBillSpecifics::where('id',intval($id))->update([
				   'ref_datedue_facture' =>$datedue,
				   ]);	
				}
		}
	$valamount = TblBillSpecifics::where('reference', $maxid)->sum('bill_price');
	$rates = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
    $rate=$rates->price;
	$dateObject = Carbon::parse($datein); // Convert to DateTime object
	$month = $dateObject->format('m'); // Extracts the month as a two-digit string
	$year = $dateObject->format('Y'); // Extracts the year as a four-digit string
	$notes = 'Lab Test ' . $month . '/' . $year;
	$idClinic = auth()->user()->clinic_num;
    $user_type= auth()->user()->type;

	 DB::table('tbl_bill_guar_payment')->insert([
	'datein'          => $datein,
	'bill_num'        => $maxid,
    'clinic_num'      => $idClinic,
    'payment_type'    => 'S',
    'currency'        => 'USD',
    'dolarprice'      => $rate,
    'payment_amount'  => $valamount,
    'user_type'       => $user_type,
    'guarantor'       => $filter_g,
    'user_num'        => $user_num,
	'notes'        => $notes,
    'status'          => 'Y',
]);
		
			 $msg = __('Test Facturation');
	//}
		
	//if($type1=='Y'){
	//foreach ($selectedValues as $id) {
	//		   TblBillSpecifics::where('id',intval($id))->update([
	//		   'ref_facture'=>'N',
	//		   'user_num'=>$user_num, 
	//		   'ref_date_facture' => NULL
	//		   ]);
	//}			  
		//	  $msg = __('Test UnFacturation');
			 //$location = route('lab.visit.edit',[$lang,$id]);
			
		
		//}
	 return response()->json(['msg'=>$msg,'type'=>$type]);

}



public function guargen_sumprice($lang,Request $request) 
    {
					$query = DB::table('tbl_bill_head')
				->join('tbl_bill_specifics as spec', 'tbl_bill_head.id', '=', 'spec.bill_num')
				->selectRaw('
					SUM(spec.bill_price) as tdollar,
					SUM(spec.ebill_price) as teuro,
					SUM(spec.ref_dolarprice) as paydollar,
					SUM(spec.ref_ebill_price) as payeuro
				')
				->where('spec.status', 'O')
				->where('spec.ref_paid', 'Y')
				->whereNotNull('tbl_bill_head.ext_lab');

			if ($request->filter_fromdate) {
				$query->whereDate('tbl_bill_head.bill_datein', '>=', $request->filter_fromdate);
			}

			if ($request->filter_todate) {
				$query->whereDate('tbl_bill_head.bill_datein', '<=', $request->filter_todate);
			}

			if ($request->filter_patient && $request->filter_patient != "0") {
				$query->where('tbl_bill_head.patient_num', $request->filter_patient);
			}

			if ($request->filter_status) {
				$query->where('spec.ref_paid', $request->filter_status);
			}
			if ($request->filter_facture) {
				$query->where('spec.ref_facture', $request->filter_facture);
			}


			if(isset($request->filter_g) && $request->filter_g!="0" ){
			 $filter_ext_lab = "and tbl_bill_head.ext_lab='".$request->filter_g."' ";  
		     }

			$SAmount = $query->get();
			$tdollar = $SAmount[0]->tdollar;
			$teuro = $SAmount[0]->teuro;
			$paydollar = $SAmount[0]->paydollar;
			$payeuro = $SAmount[0]->payeuro;
			$tdollar=number_format((float)$tdollar,2,'.',',');
			$teuro=number_format((float)$teuro,2,'.',',');
			$paydollar=number_format((float)$paydollar,2,'.',',');
			$payeuro=number_format((float)$payeuro,2,'.',',');
			
return response()->json(["tdollar" => $tdollar, "teuro" => $teuro, "paydollar" => $paydollar, "payeuro" => $payeuro]);


	}

public function GetPayFacture($lang,Request $request){
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
			
		$filter_sent="";
           	if(isset($request->filter_sent)){
			 $filter_sent = "and b.reference='".$request->filter_sent."'";  
		   }
	   $filter_status="";
           	if(isset($request->filter_status)){
			 $filter_status = "and spec.ref_paid='".$request->filter_status."'";  
		   }
		   
		     $filter_facture="";
           	if(isset($request->filter_facture)){
			 $filter_facture = "and spec.ref_facture='".$request->filter_facture."'";  
		   }
	  
	         $filter_datefacture= "";
				
			if( ($request->filter_fromdatefacture!="" &&  $request->filter_fromdatefacture!=NULL)
				&& ($request->filter_todatefacture=="" || $request->filter_todatefacture==NULL) ){
			     $filter_datefacture= "and DATE(spec.ref_date_facture) >= '".$request->filter_fromdatefacture."' ";
				}
			
			if( ($request->filter_todatefacture!="" &&  $request->filter_todatefacture!=NULL)
				&& ($request->filter_fromdatefacture=="" || $request->filter_fromdatefacture==NULL) ){
			     $filter_datefacture= "and DATE(spec.ref_date_facture) <= '".$request->filter_todatefacture."' ";
				}	
				
			if( ($request->filter_fromdatefacture!="" &&  $request->filter_fromdatefacture!=NULL)
				&& ($request->filter_todatefacture!="" &&  $request->filter_todatefacture!=NULL) ){
			     $filter_datefacture = "and DATE(spec.ref_date_facture) BETWEEN '".$request->filter_fromdatefacture."' AND '".$request->filter_todatefacture."'";
				}	
			
			
			$filter_datepaid= "";
				
			if( ($request->filter_fromdatepaid!="" &&  $request->filter_fromdatepaid!=NULL)
				&& ($request->filter_todatepaid=="" || $request->filter_todatepaid==NULL) ){
			     $filter_datepaid= "and DATE(spec.ref_date_paid) >= '".$request->filter_fromdatepaid."' ";
				}
			
			if( ($request->filter_todatepaid!="" &&  $request->filter_todatepaid!=NULL)
				&& ($request->filter_fromdatepaid=="" || $request->filter_fromdatepaid==NULL) ){
			     $filter_datepaid= "and DATE(spec.ref_date_paid) <= '".$request->filter_todatepaid."' ";
				}	
				
			if( ($request->filter_fromdatepaid!="" &&  $request->filter_fromdatepaid!=NULL)
				&& ($request->filter_todatepaid!="" &&  $request->filter_todatepaid!=NULL) ){
			     $filter_datepaid = "and DATE(spec.ref_date_paid) BETWEEN '".$request->filter_fromdatepaid."' AND '".$request->filter_todatepaid."'";
				}	
		 
		   $filter_ext_lab="";
           	
			if(isset($request->filter_g) && $request->filter_g!="0" ){
			 $filter_ext_lab = "and o.ext_lab='".$request->filter_g."' ";  
		     }     
			 $idFacility = auth()->user()->clinic_num; 
		     $clinic = Clinic::where('id',$idFacility)->where('active','O')->first();
			$ext_lab=ExtLab::where('status','A')->where('id',$request->filter_g)->first();
	       $logo=DB::table('tbl_bill_logo')->where('clinic_num',$idFacility)->where('status','O')->first();

			$sql = "SELECT 
			spec.id AS id,
			GROUP_CONCAT(DISTINCT tbl_bill_head.clinic_bill_num ORDER BY tbl_bill_head.bill_datein) AS fac_bill,
			GROUP_CONCAT(DISTINCT sentFromclin.full_name) AS fromClinicName,
			GROUP_CONCAT(DISTINCT CONCAT(
				IFNULL(tbl_patients.first_name, ''),
				IF(tbl_patients.middle_name IS NOT NULL AND tbl_patients.middle_name <> '', CONCAT(' ', tbl_patients.middle_name), ''),
				' ', IFNULL(tbl_patients.last_name, ''))) AS patDetail,
			GROUP_CONCAT(DISTINCT o.request_nb) AS request_nb,
			MAX(spec.bill_code) AS bill_code,
			MAX(spec.reference) AS reference, 		
			MAX(spec.bill_name) AS bill_name, 
			MAX(spec.ref_lab) AS ref_lab, 
			MAX(spec.ref_lbill_price) AS ref_lbill_price, 
			MAX(spec.ref_dolarprice) AS ref_dolarprice, 
			MAX(spec.ref_ebill_price) AS ref_ebill_price,
			MAX(spec.ref_paid) AS ref_paid, 
			MAX(spec.ref_date_paid) AS ref_date_paid,
			MAX(spec.ref_datedue_facture) AS facture_datedue,
			MAX(spec.bill_price) AS bill_price, 
			MAX(spec.ebill_price) AS ebill_price,
			MAX(tbl_bill_head.bill_total) AS bill_total, 
			MAX(spec.ref_paid) AS paid,
			MAX(spec.ref_facture) AS facture,
			MAX(spec.ref_date_facture) AS ref_date_facture,	
			GROUP_CONCAT(DISTINCT cust.sent_referredlab) AS sent_referredlab,
			GROUP_CONCAT(DISTINCT o.order_datetime) AS datein,
			IFNULL(MAX(l.full_name), '') AS ext_lab_name,
			IFNULL(MAX(CONCAT(doc.first_name, 
				IF(doc.middle_name IS NOT NULL AND doc.middle_name <> '', CONCAT(' ', doc.middle_name, ' '), ' '), 
				doc.last_name)), '') AS doctor_name
		FROM tbl_bill_head
		INNER JOIN tbl_bill_specifics AS spec ON tbl_bill_head.id = spec.bill_num
		INNER JOIN tbl_clinics AS sentFromclin ON tbl_bill_head.clinic_num = sentFromclin.id
		LEFT JOIN tbl_visits_orders AS o ON o.id = tbl_bill_head.order_id
		LEFT JOIN tbl_doctors AS doc ON tbl_bill_head.doctor_num = doc.id
		LEFT JOIN tbl_external_labs AS l ON l.id = o.ext_lab
		LEFT JOIN tbl_visits_order_custom_tests AS cust ON o.id = cust.order_id
		LEFT JOIN tbl_patients AS tbl_patients ON tbl_bill_head.patient_num = tbl_patients.id
		LEFT JOIN tbl_bill_payment as b ON b.bill_num=tbl_bill_head.id
		WHERE b.payment_type='P' and spec.status='O' AND o.active='Y' AND b.status='Y' 
			".$filter_ext_lab." 
			".$filter_datefacture." 
			".$filter_datepaid." 
			".$filter_facture." 
			".$filter_date."
			".$filter_sent."
			".$filter_status." 
		GROUP BY spec.id";
		  $bills= DB::select(DB::raw("$sql"));
			$uniquePatients = collect($bills)->pluck('patDetail')->unique()->count();		
            $reference = collect($bills)->pluck('reference')->unique()->first();
            $datedue = collect($bills)->pluck('facture_datedue')->unique()->first();		
            $datein = collect($bills)->pluck('ref_date_facture')->unique()->first();					
	        $totalBillPrice = collect($bills)->sum('bill_price');

return response()->json(['totalBillPrice' => $totalBillPrice,'bills' => $bills,'clinic' => $clinic,'ext_lab'=>$ext_lab,
								 'datein'=>$datein,'datedue'=>$datedue,'reference'=>$reference,'uniquePatients'=>$uniquePatients,'filter_fromdatefacture'=>$request->filter_fromdate,'filter_todatefacture'=>$request->filter_todate]);
	

}	

  public function downloadPDFFacture($lang,Request $request){
	  
	   
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
			
		$filter_sent="";
           	if(isset($request->filter_sent)){
			 $filter_sent = "and b.reference='".$request->filter_sent."'";  
		   }
	   $filter_status="";
           	if(isset($request->filter_status)){
			 $filter_status = "and spec.ref_paid='".$request->filter_status."'";  
		   }
		   
		     $filter_facture="";
           	if(isset($request->filter_facture)){
			 $filter_facture = "and spec.ref_facture='".$request->filter_facture."'";  
		   }
	  
	         $filter_datefacture= "";
				
			if( ($request->filter_fromdatefacture!="" &&  $request->filter_fromdatefacture!=NULL)
				&& ($request->filter_todatefacture=="" || $request->filter_todatefacture==NULL) ){
			     $filter_datefacture= "and DATE(spec.ref_date_facture) >= '".$request->filter_fromdatefacture."' ";
				}
			
			if( ($request->filter_todatefacture!="" &&  $request->filter_todatefacture!=NULL)
				&& ($request->filter_fromdatefacture=="" || $request->filter_fromdatefacture==NULL) ){
			     $filter_datefacture= "and DATE(spec.ref_date_facture) <= '".$request->filter_todatefacture."' ";
				}	
				
			if( ($request->filter_fromdatefacture!="" &&  $request->filter_fromdatefacture!=NULL)
				&& ($request->filter_todatefacture!="" &&  $request->filter_todatefacture!=NULL) ){
			     $filter_datefacture = "and DATE(spec.ref_date_facture) BETWEEN '".$request->filter_fromdatefacture."' AND '".$request->filter_todatefacture."'";
				}	
			
			
			$filter_datepaid= "";
				
			if( ($request->filter_fromdatepaid!="" &&  $request->filter_fromdatepaid!=NULL)
				&& ($request->filter_todatepaid=="" || $request->filter_todatepaid==NULL) ){
			     $filter_datepaid= "and DATE(spec.ref_date_paid) >= '".$request->filter_fromdatepaid."' ";
				}
			
			if( ($request->filter_todatepaid!="" &&  $request->filter_todatepaid!=NULL)
				&& ($request->filter_fromdatepaid=="" || $request->filter_fromdatepaid==NULL) ){
			     $filter_datepaid= "and DATE(spec.ref_date_paid) <= '".$request->filter_todatepaid."' ";
				}	
				
			if( ($request->filter_fromdatepaid!="" &&  $request->filter_fromdatepaid!=NULL)
				&& ($request->filter_todatepaid!="" &&  $request->filter_todatepaid!=NULL) ){
			     $filter_datepaid = "and DATE(spec.ref_date_paid) BETWEEN '".$request->filter_fromdatepaid."' AND '".$request->filter_todatepaid."'";
				}	
		 
		   $filter_ext_lab="";
           	
			if(isset($request->filter_g) && $request->filter_g!="0" ){
			 $filter_ext_lab = "and o.ext_lab='".$request->filter_g."' ";  
		     }     
			 $idFacility = auth()->user()->clinic_num; 
		     $clinic = Clinic::where('id',$idFacility)->where('active','O')->first();
			$ext_lab=ExtLab::where('status','A')->where('id',$request->filter_g)->first();
	       $logo=DB::table('tbl_bill_logo')->where('clinic_num',$idFacility)->where('status','O')->first();

			$sql = "SELECT 
			spec.id AS id,
			GROUP_CONCAT(DISTINCT tbl_bill_head.clinic_bill_num ORDER BY tbl_bill_head.bill_datein) AS fac_bill,
			GROUP_CONCAT(DISTINCT sentFromclin.full_name) AS fromClinicName,
			GROUP_CONCAT(DISTINCT CONCAT(
				IFNULL(tbl_patients.first_name, ''),
				IF(tbl_patients.middle_name IS NOT NULL AND tbl_patients.middle_name <> '', CONCAT(' ', tbl_patients.middle_name), ''),
				' ', IFNULL(tbl_patients.last_name, ''))) AS patDetail,
			GROUP_CONCAT(DISTINCT o.request_nb) AS request_nb,
			MAX(spec.bill_code) AS bill_code,
			MAX(spec.reference) AS reference, 		
			MAX(spec.bill_name) AS bill_name, 
			MAX(spec.ref_lab) AS ref_lab, 
			MAX(spec.ref_lbill_price) AS ref_lbill_price, 
			MAX(spec.ref_dolarprice) AS ref_dolarprice, 
			MAX(spec.ref_ebill_price) AS ref_ebill_price,
			MAX(spec.ref_paid) AS ref_paid, 
			MAX(spec.ref_date_paid) AS ref_date_paid,
			MAX(spec.ref_datedue_facture) AS facture_datedue,
			MAX(spec.bill_price) AS bill_price, 
			MAX(spec.ebill_price) AS ebill_price,
			MAX(tbl_bill_head.bill_total) AS bill_total, 
			MAX(spec.ref_paid) AS paid,
			MAX(spec.ref_facture) AS facture,
			MAX(spec.ref_date_facture) AS ref_date_facture,	
			GROUP_CONCAT(DISTINCT cust.sent_referredlab) AS sent_referredlab,
			GROUP_CONCAT(DISTINCT o.order_datetime) AS datein,
			IFNULL(MAX(l.full_name), '') AS ext_lab_name,
			IFNULL(MAX(CONCAT(doc.first_name, 
				IF(doc.middle_name IS NOT NULL AND doc.middle_name <> '', CONCAT(' ', doc.middle_name, ' '), ' '), 
				doc.last_name)), '') AS doctor_name
		FROM tbl_bill_head
		INNER JOIN tbl_bill_specifics AS spec ON tbl_bill_head.id = spec.bill_num
		INNER JOIN tbl_clinics AS sentFromclin ON tbl_bill_head.clinic_num = sentFromclin.id
		LEFT JOIN tbl_visits_orders AS o ON o.id = tbl_bill_head.order_id
		LEFT JOIN tbl_doctors AS doc ON tbl_bill_head.doctor_num = doc.id
		LEFT JOIN tbl_external_labs AS l ON l.id = o.ext_lab
		LEFT JOIN tbl_visits_order_custom_tests AS cust ON o.id = cust.order_id
		LEFT JOIN tbl_patients AS tbl_patients ON tbl_bill_head.patient_num = tbl_patients.id
		LEFT JOIN tbl_bill_payment as b ON b.bill_num=tbl_bill_head.id
		WHERE b.payment_type='P' and spec.status='O' AND o.active='Y' AND b.status='Y' 
			".$filter_ext_lab." 
			".$filter_datefacture." 
			".$filter_datepaid." 
			".$filter_facture." 
			".$filter_date."
			".$filter_sent."
			".$filter_status." 
		GROUP BY spec.id";
		    $bills= DB::select(DB::raw("$sql"));
			$uniquePatients = collect($bills)->pluck('patDetail')->unique()->count();		
            $reference = collect($bills)->pluck('reference')->unique()->first();
            $datedue = collect($bills)->pluck('facture_datedue')->unique()->first();		
            $datein = collect($bills)->pluck('ref_date_facture')->unique()->first();
			$payment=TblBillGuarPayment::where('bill_num', $reference)->where('guarantor',$ext_lab->id)->where('payment_type','D')->where('status','Y')->first();
			if(isset($payment)){
			$discount=$payment->payment_amount;
			}else{
			$discount=0.00;	
			}
						$data = ['discount'=>$discount,'title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'bills' => $bills,'clinic' => $clinic,'ext_lab'=>$ext_lab,
								 'datein'=>$datein,'datedue'=>$datedue,'reference'=>$reference,'uniquePatients'=>$uniquePatients,'filter_fromdatefacture'=>$request->filter_fromdate,'filter_todatefacture'=>$request->filter_todate]; 
			if($request->type=='D'){
						$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
										-> loadView('lab.extlabs.FactureDetailsPDF', $data);
			}else{
				$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
										-> loadView('lab.extlabs.FactureTotalPDF', $data);
	
			}
							$pdf->output();
							$dom_pdf = $pdf->getDomPDF();
							$canvas = $dom_pdf->get_canvas();
							$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
							
						
					
			
				

             return  $pdf->stream();				
					
   }		

function SavePayFacture($lang,Request $request){

$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
$filter_g=$request->filter_g;
$bill_id=$request->bill_id;

$valamount=$request->valamount;
$currency=$request->selectcurrencyp;
$selectmethod=$request->selectmethod;
switch($selectmethod) {
    case 'S':
        $notes = 'Schedule';
        break;
    case 'P':
        $notes = 'Cash/Payment';
        break;
    case 'D':
        $notes = 'Discount';
        break;
    case 'R':
        $notes = 'Refund';
        break;
    default:
        $notes = 'Unknown';
}

$datepay =$request->datepay;
$idClinic = auth()->user()->clinic_num;
$rates = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
$rate=$rates->price;
$order_id=TblBillGuarPayment::where('status','Y')->where('bill_num',$bill_id)->where('payment_type','D')->count();
if($order_id==1 and $selectmethod=='D'){
	TblBillGuarPayment::where('bill_num',$bill_id)->where('payment_type',$selectmethod)->update([
				                  'payment_amount'=>$valamount,'dpay_amount'=>$valamount
								  ]);
	$msg=__('Discount Updated Success');

}else{
	
DB::table('tbl_bill_guar_payment')->insert([
	'datein'          => now()->toDateString(),
	'bill_num'        => $bill_id,
    'clinic_num'      => $idClinic,
    'payment_type'    => $selectmethod,
    'currency'        => 'USD',
    'dolarprice'      => $rate,
    'dpay_amount'  => $valamount,
	'payment_amount'  => $valamount,
    'user_type'       => $user_type,
    'guarantor'       => $filter_g,
    'user_num'        => $user_id,
	'notes'        => $notes,
    'status'          => 'Y',
]);
$msg=__('Save Successfully');
}
return response()->json(['success'=>$msg]);	  
}			  

 public function downloadPDFStatment($lang,Request $request){
	  
	   
 $filter_date= "";
				
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $filter_date= "and DATE(tbl_bill_guar_payment.datein) >= '".$request->filter_fromdate."' ";
				}
			
			if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $filter_date= "and DATE(tbl_bill_guar_payment.datein) <= '".$request->filter_todate."' ";
				}	
				
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			     $filter_date = "and DATE(tbl_bill_guar_payment.datein) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				}	
			
		
		   $filter_ext_lab="";
           	
			if(isset($request->filter_g) && $request->filter_g!="0" ){
			 $filter_ext_lab = "and tbl_bill_guar_payment.guarantor='".$request->filter_g."' ";  
		     }     
			 $idFacility = auth()->user()->clinic_num; 
		     $clinic = Clinic::where('id',$idFacility)->where('active','O')->first();
			 $ext_lab=ExtLab::where('status','A')->where('id',$request->filter_g)->first();
	         $logo=DB::table('tbl_bill_logo')->where('clinic_num',$idFacility)->where('status','O')->first();

			$sql = "SELECT *
	 	FROM tbl_bill_guar_payment
		LEFT JOIN tbl_external_labs AS l ON l.id = tbl_bill_guar_payment.guarantor
		WHERE tbl_bill_guar_payment.status='Y' 
			".$filter_ext_lab." 
			".$filter_date;
		    $bills= DB::select(DB::raw("$sql"));
			$data = ['title' => __('Statment'),'date' => date('m/d/Y'),'logo'=>$logo,'bills' => $bills,'clinic' => $clinic,'ext_lab'=>$ext_lab,
								 'filter_fromdatefacture'=>$request->filter_fromdate,'filter_todatefacture'=>$request->filter_todate]; 
			
				$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
										-> loadView('lab.extlabs.FactureStatmentPDF', $data);
	
							$pdf->output();
							$dom_pdf = $pdf->getDomPDF();
							$canvas = $dom_pdf->get_canvas();
							$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
							
						
					
			
				

             return  $pdf->stream();				
					
   }		

public function GetPaymentFacture($lang,Request $request){
$type = $request->type;
$ReqPay=TblBillGuarPayment::where('tbl_bill_guar_payment.status','Y')
			->where('guarantor',$request->bill_id)
            ->get();
$cptpCount= $ReqPay->count();		
$cptp = 1;
$html1='<thead>
		<tr class="txt-bg text-white text-center">
		<th scope="col" style="font-size:16px;">'.__("#").'</th>
		<th scope="col" style="font-size:16px;">'.__("Date").'</th>
		<th scope="col" style="font-size:16px;">'.__("Type").'</th>
		<th scope="col" style="font-size:16px;">'.__("Currency").'</th>
		<th scope="col" style="font-size:16px;">'.__("Amount").'</th>
		<th scope="col" style="font-size:16px;">'.__("Rate").'</th>
		<th scope="col"></th>
		<th scope="col" style="display:none;"></th>
		</tr>
		</thead>
		<tbody>';
	foreach ($ReqPay as $sReqPays) {
			$type_name = ($lang=='fr')?$sReqPays->name_fr:$sReqPays->name_eng;
			$guarantor_name = DB::table('tbl_external_labs')->where('id',$sReqPays->guarantor)->value('full_name');
			$html1 .='<tr>
			<td>'.$cptp.'</td>
			<td>'.$sReqPays->datein.'</td>
			<td>'.$sReqPays->payment_type.'</td>
			<td>'.$sReqPays->currency.'</td>
			<td>'.$sReqPays->payment_amount.'</td>
			<td>'.$sReqPays->dolarprice.'</td>
			<td><input type="button" class="btn btn-delete" id="rowdeletepay'.$cptp.'" value="'.__("Delete").'" onclick="deleteRowPayFacture(this)"  /></td>
			<td style="display:none">'.$sReqPays->guarantor.'</td>
			</tr>';
	$cptp++;
	}
	$html1 .='</tbody>';

$sumpay=TblBillGuarPayment::where('guarantor','=',$request->bill_id)->where('status','Y')->where('payment_type','=','P')->sum('payment_amount');
$sumref=TblBillGuarPayment::where('guarantor','=',$request->bill_id)->where('status','Y')->where('payment_type','=','R')->sum('payment_amount');
$sumdis=TblBillGuarPayment::where('guarantor','=',$request->bill_id)->where('status','Y')->where('payment_type','=','D')->sum('payment_amount');

$Nsumpay=number_format((float)$sumpay, 2, '.', ',');
$sumpay=floatval(preg_replace('/[^\d.-]/', '', $Nsumpay));
$Nsumref=number_format((float)$sumref, 2, '.', ',');
$sumref=floatval(preg_replace('/[^\d.-]/', '', $Nsumref));
$Nsumdis=number_format((float)$sumdis, 2, '.', ',');
$sumdis=floatval(preg_replace('/[^\d.-]/', '', $Nsumdis));
$totald=0.00;
return response()->json(['html1' => $html1,'sumpay'=>$sumpay,'sumref'=>$sumref,'sumdis'=>$sumdis,
                         'totald'=>$totald]);
	
}

function SavePaymentFacture($lang,Request $request){

$someArray = [];
$someArray=json_decode($request->data,true); 		
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
$bill_id=$request->bill_id;
$clinic_id=auth()->user()->clinic_num;
$bill_num=$request->bill_num;



TblBillGuarPayment::where('guarantor',$request->bill_id)->update(['status'=>'N']); 

foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0){
   $code = $area["CODE"];
   $date = $area["DATE"];
   $type=trim($area["TYPE"]);
 
switch($type) {
    case 'S':
        $notes = 'Schedule';
        break;
    case 'P':
        $notes = 'Cash/Payment';
        break;
    case 'D':
        $notes = 'Discount';
        break;
    case 'R':
        $notes = 'Refund';
        break;
    default:
        $notes = 'Unknown';
}
if($type=='S'){
    $dateObject = Carbon::parse($date); // Convert to DateTime object
	$month = $dateObject->format('m'); // Extracts the month as a two-digit string
	$year = $dateObject->format('Y'); // Extracts the year as a four-digit string
	$notes = 'Lab Test ' . $month . '/' . $year;	
}
   $price = $area["PRICE"];
   $currency = $area["CURRENCY"];
   $rate = $area["RATE"];
   $guarantor = isset($area["GUARANTOR"]) && $area["GUARANTOR"]!=""?$area["GUARANTOR"]:0;
   $sqlInsertF = "insert into tbl_bill_guar_payment(datein,bill_num,clinic_num,payment_amount,dpay_amount,payment_type,currency,dolarprice,user_type,guarantor,user_num,notes,status) values('".
				 $date."','".
				 $bill_num."','".
				 $clinic_id."','".
				 $price."','".
				 $price."','".
				 $type."','".
				 $currency."','".
				 $rate."','".
				 $user_type."','".
				 $guarantor."','".
				  $user_id."','".
				 $notes."','Y')";
	DB::select(DB::raw("$sqlInsertF"));  
 }	
 }
$sumpay=TblBillGuarPayment::where('guarantor','=',$request->bill_id)->where('status','Y')->where('payment_type','=','P')->sum('payment_amount');
$sumref=TblBillGuarPayment::where('guarantor','=',$request->bill_id)->where('status','Y')->where('payment_type','=','R')->sum('payment_amount');
$sumdis=TblBillGuarPayment::where('guarantor','=',$request->bill_id)->where('status','Y')->where('payment_type','=','D')->sum('payment_amount');

$Nsumpay=number_format((float)$sumpay, 2, '.', ',');
$sumpay=floatval(preg_replace('/[^\d.-]/', '', $Nsumpay));
$Nsumref=number_format((float)$sumref, 2, '.', ',');
$sumref=floatval(preg_replace('/[^\d.-]/', '', $Nsumref));
$Nsumdis=number_format((float)$sumdis, 2, '.', ',');
$sumdis=floatval(preg_replace('/[^\d.-]/', '', $Nsumdis));

	  
$msg=__('Payment Saved Success');

return response()->json(['success'=>$msg,'sumpay'=>$sumpay,'sumref'=>$sumref,
                         'sumdis'=>$sumdis]);	  
}			  

}




