<?php

namespace App\Http\Controllers\external_patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Clinic;
use App\Models\PatientInsurance;
use App\Models\Patient;

use App\Models\TblVisits;
use App\Models\Doctor;
use App\Models\DoctorSignature;

use App\Models\DOCSResults;
use App\Models\ExtIns;
use App\Models\ExtLab;
use App\Models\LabOrders;

use PDF;
use Image;
use DB;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class DashboardController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($lang)
    {
       $patient = Patient::where('patient_user_num',auth()->user()->id)->first();
	   $pid = $patient->id;
	   $clinic_num = $patient->clinic_num;
	   $clinic = Clinic::find($clinic_num);
	   $insurance=ExtIns::where('status','A')->orderBy('id','desc')->get();
	   
        $ext_labs=Doctor::select('doctor_user_num as user_num',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('doctor_user_num',$patient->ext_lab)->get();	
	    if($ext_labs->count()==0){
		 $ext_labs=ExtLab::select('lab_user_num as user_num','full_name')->where('lab_user_num',$patient->ext_lab)->get();
        }
	  
	  $sql="select DISTINCT(o.id) as id,DATE_FORMAT(o.order_datetime,'%Y-%m-%d %H:%i') as visit_date_time,
		          clin.full_name as ClinicName,
				  CONCAT(doc.first_name,IF(doc.middle_name=NULL,' ',CONCAT(doc.middle_name,' ')),doc.last_name) AS ProName,
				  extlab.full_name as ExtLabName,o.status as order_status,
		          CONCAT(patConsult.first_name,IF(patConsult.middle_name=NULL,' ',CONCAT(' ',patConsult.middle_name,' ')),patConsult.last_name) AS patDetail,
				  patConsult.first_phone as Tel,patConsult.cell_phone as Cell
				  from tbl_visits_orders as o
				  INNER JOIN tbl_clinics as clin on o.clinic_num=clin.id 
				  LEFT JOIN tbl_doctors as doc on o.ext_lab=doc.doctor_user_num 
				  LEFT JOIN tbl_external_labs as extlab on o.ext_lab=extlab.lab_user_num 
				  INNER JOIN tbl_patients as patConsult on tbl_visits.patient_num=patConsult.id
				  where o.active='Y' and o.status='V' and clin.id=".$clinic_num." and patConsult.id=".$pid." 
                  ORDER BY o.order_datetime desc		  
				  ";
	  
	$pat_visits= DB::select(DB::raw("$sql"));
	
	
	
	 return view('external_patient.dashboard.index')->with([
	                                                      'patient'=>$patient,
														  'visits'=>$pat_visits,
														  'clinic'=>$clinic,
														  'insurance'=>$insurance,
														  'ext_labs'=>$ext_labs
														  ]);

    
	}
	
	

public function chg_pass($lang,Request $request){
	
	$id = auth()->user()->id;
	
	if (!(Hash::check($request->old_pass, auth()->user()->password))) {
        // The passwords matches
        return response()->json(["error_match"=>__("Your current password does not match with the password you provided. Please try again.")]);
    }

    if(strcmp($request->old_pass, $request->new_pass) == 0){
        //Current password and new password are same
        return response()->json(["error_same1"=>__("New Password cannot be same as your current password. Please choose a different password.")]);
    }
	
	if(strcmp($request->new_pass, $request->cfrm_pass) != 0){
        //new password and confirm password not the same
        return response()->json(["error_same2"=>__("New Password and Password Confirmation do not match.")]);
    }

	
	User::where('id',$id)->update(['password' => Hash::make($request->new_pass)]);
	
	return response()->json(['msg'=>__("Password Updated Successfully.")]);
}


public function generatePDFOrder($lang,Request $request){
	$order_id = $request->id;
	$order = LabOrders::find($order_id);
	$patient = Patient::find($order->patient_num);
	$doctor = Doctor::where('doctor_user_num',$order->ext_lab)->first();
	$ext_lab = ExtLab::where('lab_user_num',$order->ext_lab)->first();
	//$lab = Clinic::find($order->clinic_num);
	$first_ins = ExtIns::find($patient->first_ins);
	$second_ins = ExtIns::find($patient->second_ins);
	$documents = DOCSResults::where('name','not like','%.pdf')->where('active','Y')->where('order_id',$order->id)->get();
	$results = DB::table('tbl_visits_order_results as r')
	               ->select('r.id','t.test_name','r.ref_range',
				            DB::raw("IFNULL(g.test_name,'Other') as group_name"),
							DB::raw("IFNULL(g.descrip,'') as group_instruction"),
							DB::raw("IFNULL(g.clinical_remark,'') as group_clinical_remark"),
							DB::raw("IFNULL(t.unit,'') as unit"),
							't.test_type','r.test_id',
							DB::raw("IFNULL(t.category_num,0) as category_num"),
							DB::raw("IFNULL(r.result,'') as result"),
							DB::raw("IFNULL(r.sign,'') as sign"),
							't.testord as position',
							DB::raw("IFNULL(prev.result,'') as prev_result_val"),
							DB::raw("IFNULL(prev.order_id,'') as prev_order_id"),
							DB::raw("IFNULL(r.field_num,'') as field_num"),
							DB::raw("IFNULL(t.clinical_remark,'') as clinical_remark"),
							DB::raw("IFNULL(t.descrip,'') as method_instruction"),
							DB::raw("IFNULL(t.test_rq,'') as code_remark"),
							't.is_printed','r.calc_result','r.calc_unit',
							'g.is_printed as group_printed'
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
				  ->where('r.active','Y')
                  ->where('r.order_id',$order_id)
	              ->orderBy('g.testord')
				  ->orderBy('t.testord')
				  ->get();	
				
				
				$order_tests = json_decode($order->tests,true);	
				
			    $categories = DB::table('tbl_lab_tests as t')
			                  ->select(DB::raw("IFNULL(cat.id,0) as id"),DB::raw("IFNULL(cat.descrip,'Other') as descrip"),'cat.testord')
							  ->leftjoin('tbl_lab_categories as cat','t.category_num','cat.id')
							  ->whereIn('t.id',$order_tests)
							  ->orderBy('cat.testord')
							  ->distinct()
							  ->get();
		     $phlebotomy = collect();
				  
	 $branch = Clinic::find($order->clinic_num);
	 $tel = isset($branch->telephone) && $branch->telephone!=''?'Tel: '.$branch->telephone.' , ':'';
	 $whatsapp = isset($branch->whatsapp) && $branch->whatsapp!=''?'Whatsapp: '.$branch->whatsapp.' , ':'';
	 $website = isset($branch->website) && $branch->website!=''?' '.$branch->website.' , ':'';
	 $address = isset($branch->full_address) && $branch->full_address!=''?'Address: '.$branch->full_address:'';
	 $branch_data = $tel.$whatsapp.$website.$address;               
	
	 $data = ['patient'=>$patient,'lab'=>$branch,'first_ins'=>$first_ins,'second_ins'=>$second_ins,
			  'doctor'=>$doctor,'ext_lab'=>$ext_lab,'order'=>$order,'results'=>$results,
			  'categories'=>$categories,'documents'=>$documents,
			  'branch_data'=>$branch_data,'phlebotomy'=>$phlebotomy]; 
	
	$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true,'isPHPEnabled'=>true])
                       -> loadView('lab.visit.ResultsPDF', $data);
	           
			$pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
            
			$pdf_docs = DOCSResults::where('active','Y')->where('order_id',$order->id)->where('name','like','%.pdf')->get();
			
			if($pdf_docs->count()>0){
				$pdf_merge = PDFMerger::init();
				$path = storage_path('app/private/tmp');
					if (!file_exists($path)) {
							mkdir($path, 0775, true);
					}
				$name=date("Y-m-d")."_".uniqid() . ".pdf";
				$pdf_file = $path . $name;
				file_put_contents($pdf_file, $pdf->output());
				$pdf_merge->addPDF( $pdf_file, 'all');
				foreach($pdf_docs as $d){
						$pdf_path = storage_path('app/private/'.$d->path);
						$pdf_merge->addPDF($pdf_path, 'all');
					}
				//delete old existing files
				 $files = glob($path.'*'); // get all file names
					foreach($files as $file){ // iterate files
					  if(is_file($file)) {
						unlink($file); // delete file
					  }
					}
				return $pdf_merge->stream();
				
			}else{
			   return $pdf->stream();
					
				
			}        
}



    
}
