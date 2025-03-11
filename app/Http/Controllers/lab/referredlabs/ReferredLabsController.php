<?php
/*
* DEV APP
* Created date : 20-10-2022
*  
*
*/
namespace App\Http\Controllers\lab\referredlabs;
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

class ReferredLabsController extends Controller
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
	   
	 	$doctors = Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
		//$ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
		$ext_labs=DB::table('tbl_referred_labs')->where('status','Y')->orderBy('full_name')->get();

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
			 $filter_status = "and spec.ref_paid='".$request->filter_status."'";  
		   }
		   
		   $filter_ext_lab="";
           	
			if(isset($request->id_pro) && $request->id_pro!="0" ){
			 $filter_ext_lab = "and spec.ref_lab='".$request->id_pro."' ";  
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
		   
           $sql="SELECT 
					spec.id AS id,
					CONCAT(tbl_bill_head.clinic_bill_num, ',', DATE_FORMAT(tbl_bill_head.bill_datein, '%Y-%m-%d %H:%i')) AS fac_bill,
					sentFromclin.full_name AS fromClinicName,
					CONCAT(
						IFNULL(tbl_patients.first_name, ''),
						IF(tbl_patients.middle_name IS NOT NULL AND tbl_patients.middle_name <> '', CONCAT(' ', tbl_patients.middle_name), ''),
						' ', IFNULL(tbl_patients.last_name, ''),
						',', IFNULL(tbl_patients.birthdate, 'N/D'),
						',', IFNULL(tbl_patients.cell_phone, 'N/D')
					) AS patDetail,
					o.request_nb,
					spec.bill_code, spec.bill_name, spec.ref_lab, spec.ref_lbill_price, spec.ref_dolarprice, spec.ref_ebill_price,
					spec.ref_paid, spec.ref_date_paid,
					spec.bill_price, spec.ebill_price, tbl_bill_head.bill_total, spec.ref_paid as paid,
					IFNULL(l.full_name, '') AS ext_lab_name,
					IFNULL(
						CONCAT(doc.first_name,
						IF(doc.middle_name IS NOT NULL AND doc.middle_name <> '', CONCAT(' ', doc.middle_name, ' '), ' '),
						doc.last_name), ''
					) AS doctor_name
				FROM tbl_bill_head
				INNER JOIN tbl_bill_specifics AS spec ON tbl_bill_head.id = spec.bill_num
				INNER JOIN tbl_clinics AS sentFromclin ON tbl_bill_head.clinic_num = sentFromclin.id
				LEFT JOIN tbl_visits_orders AS o ON o.id = tbl_bill_head.order_id
				LEFT JOIN tbl_doctors AS doc ON tbl_bill_head.doctor_num = doc.id
				LEFT JOIN tbl_referred_labs AS l ON l.id = spec.ref_lab
				LEFT JOIN tbl_patients AS tbl_patients ON tbl_bill_head.patient_num = tbl_patients.id
				 where spec.status='O' and spec.ref_lab IS NOT NULL ".$filter_status." ".$filter_doc." ".$filter_patient." ".$filter_facility." ".$filter_date."  ".$filter_ext_lab."
				  ";
		  $bills= DB::select(DB::raw("$sql"));
           //dd($sql);
            return Datatables::of($bills)

                    ->addIndexColumn()
					->addColumn('Paid', function($row){
				            $checked = ($row->paid=='Y')?'checked':'';
							$disabled= ($row->paid=='N' )?'':'disabled';
							$state = ($row->paid=='Y')?'nopaid':'paid';
						    $btn=' <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" onclick="event.preventDefault();Paid('.$row->id.',\''.$state.'\')"  '.$checked.'><span class="slideon-slider" title="'.__("Paid").'"></span></label>';

						  return $btn;
                    })
                   

                    ->rawColumns(['Paid'])
					
                    ->make(true);

        }
	   
    $tdollar = DB::table('tbl_bill_specifics')->where('ref_lab','<>' ,'NULL')->where('status', 'O')->sum('bill_price');
    $teuro = DB::table('tbl_bill_specifics')->where('ref_lab','<>', 'NULL')->where('status', 'O')->sum('ebill_price');
    $paydollar = DB::table('tbl_bill_specifics')->where('status', 'O')->sum('ref_dolarprice');
    $payeuro = DB::table('tbl_bill_specifics')->where('status', 'O')->sum('ref_ebill_price');
	
		
	   return view('lab.referredlabs.index')->with(['tdollar'=>$tdollar,'teuro'=>$teuro,'paydollar'=>$paydollar,'payeuro'=>$payeuro,'FromFacility'=>$clinics,'ext_labs'=>$ext_labs,'doctors'=>$doctors,
	                                           'Patients'=>$Patients,'filter_patients'=>$filter_patients]); 
	  
	    		
}

public function PaidTest($lang,Request $request){
$id = $request->id;
	$type = $request->state;
	$user_num = auth()->user()->id;
	switch($type){
		case 'paid': 
		//	$is_valid1 = $request->is_valid1;
			
		//	if($is_valid1 == 'Y'){
			   
			   TblBillSpecifics::where('id',$id)->update(['ref_paid'=>'Y','user_num'=>$user_num, 'ref_date_paid' =>date('Y-m-d')]);
			   //only update if there is no report datetime 
			   
			   $msg = __('Test Paid');
		break;
		
		case 'nopaid': 
	
			   TblBillSpecifics::where('id',$id)->update(['ref_paid'=>'N','user_num'=>$user_num, 'ref_date_paid' => date('Y-m-d')]);
			   $msg = __('Test UnPaid');
			 //$location = route('lab.visit.edit',[$lang,$id]);
			
		break;
		}
	 return response()->json(['msg'=>$msg,'type'=>$type]);

}


public function refgen_sumprice($lang,Request $request) 
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
				->whereNotNull('spec.ref_lab');

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

			if ($request->filter_g && $request->filter_g != "0") {
				$query->where('spec.ref_lab', $request->filter_g);
			}

			$SAmount = $query->get();
			$tdollar = $SAmount[0]->tdollar;
			$teuro = $SAmount[0]->teuro;
			$paydollar = $SAmount[0]->paydollar;
			$payeuro = $SAmount[0]->payeuro;

return response()->json(["tdollar" => $tdollar, "teuro" => $teuro, "paydollar" => $paydollar, "payeuro" => $payeuro]);


	}


}




