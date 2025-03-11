<?php

namespace App\Http\Controllers\phlotobomy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LabTests;
use App\Models\LabOrders;
use DB;
use PDF;
use UserHelper;
use Carbon\Carbon;

class PhlotobomyController extends Controller{
    
public function __construct(){
    $this->middleware('auth');
}

public function index($lang,Request $request){
 $clinic_num = auth()->user()->clinic_num;
 $user_type = auth()->user()->type;
 $user_id = auth()->user()->id;
  //guarantor
 $guarantor_id = NULL;
 if($user_type==3){ $guarantor_id = DB::table('tbl_external_labs')->where('lab_user_num',$user_id)->value('id'); }
 
 $specimen = UserHelper::getSpecimenImg();
 $spec_cons = DB::table('tbl_lab_special_considerations')->orderBy('id','desc')->pluck('name','id')->toArray();
 
 $requestsGroupedByDate = DB::table('tbl_visits_orders as o')
                          ->selectRaw('DATE(o.order_datetime) as date,o.id as id')
                          ->where('o.active','Y');
 
						  

 if(isset($guarantor_id)){
	 $requestsGroupedByDate =$requestsGroupedByDate->where('ext_lab',$guarantor_id);
 }						  
 
 $requestsGroupedByDate =$requestsGroupedByDate->get()->groupBy('date');
	
 
 $serialNumbersByDate = [];
 
 foreach ($requestsGroupedByDate as $date => $requests) {
    $requestsForDate = [];
	$serial_nb = 0;
	foreach ($requests as $r) {
        $requestsCount = ++$serial_nb;
		$requestsForDate[] = ['id' => $r->id, 'serial'=>$requestsCount];
    }
    $serialNumbersByDate[$date] =  $requestsForDate;
  } 
  
  //dd($serialNumbersByDate);
 
 if($request->ajax()){
	 
	
	 $data = DB::table('tbl_visits_orders as o')
	         ->select('o.id as order_id',
			          DB::raw("group_concat(distinct t.specimen) as tests_specimens"),
					  DB::raw("group_concat(distinct t.special_considerations) as tests_specialcons"),
					  'o.coll_notes','o.chk_specimens','o.chk_specialcons',
			          DB::raw("IF(o.is_phlotobomy='Y',true,false) as choose"),
			          DB::raw("group_concat(t.test_name) as test_names"),
					  DB::raw("group_concat(t.id) as test_ids"),
					  DB::raw("group_concat(t.preanalytical) as preanalytical"),
			          DB::raw("CONCAT('Nb.',' ',':',' ',o.id,';','Date/Time',' ',':',' ',DATE_FORMAT(o.order_datetime,'%Y-%m-%d %H:%i')) as order_data"),
			          DB::raw("ANY_VALUE(IFNULL(DATE_FORMAT(o.collection_date,'%Y-%m-%d %H:%i'),'')) as collection_date"),
					  DB::raw("CONCAT(pat.first_name,IFNULL(CONCAT(' ',pat.middle_name),''),' ',pat.last_name) as patient_name"),
					  'pat.id as patient_num',
					  'pat.birthdate as patient_age',
					  DB::raw("ANY_VALUE(DATE(o.order_datetime)) as daily_serial_nb"),
					  DB::raw("ANY_VALUE(IF(bill.id IS NULL,'N',IF(bill.bill_balance=0,'Y','U'))) as billed"))
			->join('tbl_patients as pat','pat.id','o.patient_num')
			->join('tbl_visits_order_custom_tests as cust',function ($join) {
				$join->on('cust.order_id','o.id');
				$join->where('cust.active','Y');
			})
			->join('tbl_lab_tests as t','t.id','cust.test_id')
			->leftjoin('tbl_bill_head as bill','bill.order_id','o.id')
			->where('o.active','Y');
	
	if(isset($request->patient) && $request->patient!=""){
		$data = $data->where('o.patient_num',$request->patient);
	}
	
	if(isset($guarantor_id)){
		$data = $data->where('o.ext_lab',$guarantor_id);
	}
   
    if(isset($request->code) && $request->code!=""){
              $tst_id = intval($request->code);
			  $order_ids = DB::table('tbl_visits_order_custom_tests')->where('active','Y')->where('test_id',$tst_id)->distinct()->pluck('order_id')->toArray();
			  if(count($order_ids)){
			   $data = $data->whereIn('o.id',$order_ids);
			  }else{
			   $data = $data->whereNULL('o.id');
			  }
	}
    
	if(isset($request->from_date) && $request->from_date!=""){
		$data = $data->where(DB::raw("DATE(o.order_datetime)"),'>=',$request->from_date);
	}
	
	if(isset($request->to_date) && $request->to_date!=""){
		$data = $data->where(DB::raw("DATE(o.order_datetime)"),'<=',$request->to_date);
	}
    
	if(isset($request->is_phletobomy) && $request->is_phletobomy=="N"){
		$data = $data->where(function($q){
			     $q->whereNull('o.collection_date')->orWhere('o.collection_date', '=' , '');
		         });
		
	 }
    
    if(isset($request->is_phletobomy) && $request->is_phletobomy=="Y"){
		$data = $data->whereNotNULL('o.collection_date');
	 }		 	
		
	$data = $data->groupBy('o.id')->orderBy(DB::raw('DATE(o.order_datetime)'),'desc')->orderBy('o.id','asc')->get();
	
    return response()->json($data);
 
 }	
 return view('phlotobomy.index')->with(['clinic_num'=>$clinic_num,'specimen'=>$specimen,'spec_cons'=>$spec_cons,'serialNumbersByDate'=>json_encode($serialNumbersByDate)]);	
}


public function loadTests($lang,Request $request){
  
  $all_tests = DB::table('tbl_lab_tests')->select('id','test_name as text')
               ->whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and cnss IS NOT NULL))');
			   
  if($request->input('q')!=NULL && $request->input('q')!=''){
	  $search = $request->input('q');
	  $all_tests = $all_tests->where('test_name','like','%'.$search.'%');
  }
  
  $all_tests = $all_tests->orderBy('id','desc')->paginate(100);
  
  return response()->json([
        'results' => $all_tests->items(),
        'pagination' => [
            'more' => $all_tests->hasMorePages(),
        ],
    ]);
	
}

public function save_phlebotomy($lang,Request $request){
	
	$order_id = $request->order_id;
	$custom_test_id = $request->custom_test_id;
	$checkedSpecimens = isset($request->checkedSpecimens) && !empty($request->checkedSpecimens)? $request->checkedSpecimens:array();
	$checkedSpecCons = isset($request->checkedSpecCons) && !empty($request->checkedSpecCons)?$request->checkedSpecCons:array();
	$coll_notes = isset($request->coll_notes) && $request->coll_notes!=''?$request->coll_notes:NULL;	
	$coll =(count($checkedSpecimens)==0 && count($checkedSpecCons)==0)?'N':'Y';
	
	$user_num = auth()->user()->id;
	
	
	if($coll=='N'){
		//check if exists collection for order_id
		$order = LabOrders::find($request->order_id);
		//dd($order);
		if($order->is_phlotobomy=='Y'){
			LabOrders::where('id',$request->order_id)
	        ->update([
			      'collection_date'=>NULL,
		          'is_phlotobomy'=>'N',
				  'coll_notes'=>NULL,
				  'chk_specialcons'=>NULL,
				  'chk_specimens'=>NULL,
				  'user_num'=>$user_num]);
		
		
		   DB::table('tbl_visits_order_custom_tests')
		   ->where('order_id',$request->order_id)
		   ->update(['is_test_collected'=>'N','collected_test_date'=>NULL,'user_num'=>$user_num]);
		}
		 return response()->json("success");
	}else{
		$collection_datetime = Carbon::now()->format('Y-m-d H:i');
		$is_phlotobomy = 'Y';
		$chk_specimens = $chk_specialcons = $chk_tests = array();
		foreach($checkedSpecimens as $ct){
		   $tst = LabTests::find($ct);
           
		   $chk_specimens[intval($tst->id)]=$tst->specimen;

           if(!in_array(intval($tst->id),$chk_tests)){
			   array_push($chk_tests,intval($tst->id));
		   }   		   
		}

        foreach($checkedSpecCons as $ct){
		   $tst = LabTests::find($ct);
            $chk_specialcons[intval($tst->id)]=$tst->special_considerations;
		   
            if(!in_array(intval($tst->id),$chk_tests)){
			   array_push($chk_tests,intval($tst->id));
		   }   		   
		}

       	
		
		//activate collected for tests in checked tests
		DB::table('tbl_visits_order_custom_tests')
		->where('order_id',$request->order_id)
		->where('is_test_collected','N')
		->whereIn('test_id',$chk_tests)
		->update(['is_test_collected'=>'Y','collected_test_date'=>$collection_datetime,'user_num'=>$user_num]);
		
		//deactivate collected for tests not in checked tests
		DB::table('tbl_visits_order_custom_tests')
		->where('order_id',$request->order_id)
		->whereNotIn('test_id',$chk_tests)
		->update(['is_test_collected'=>'N','collected_test_date'=>NULL,'user_num'=>$user_num]);
				
		//update order collection date time and specimens and special_cons with notes 
		LabOrders::where('id',$request->order_id)
	    ->update(['collection_date'=>$collection_datetime,
		          'is_phlotobomy'=>$is_phlotobomy,
				  'coll_notes'=>$coll_notes,
				  'chk_specialcons'=>json_encode($chk_specialcons),
				  'chk_specimens'=>json_encode($chk_specimens),
				  'user_num'=>$user_num]);
				  
	  return response()->json(["success"=>true,'ok'=>$coll]);
	}

}

public function phlebotomy_codes($lang,Request $request){
	
	$coll_notes = LabOrders::where('id',$request->order_id)->value('coll_notes');
	
	$codes = DB::table('tbl_visits_order_custom_tests as ct')
             ->select('ct.id as id','t.testord as testord', 't.cnss as cnss', 't.test_name as test_name', 
			          't.is_group as is_group','t.specimen','t.special_considerations',
					  't.preanalytical','ct.test_id as test_id','t.test_code',
					  'ct.is_test_collected','ct.collected_test_date',
					  'o.chk_specialcons as chk_specialcons','o.chk_specimens as chk_specimens')
             ->join('tbl_lab_tests as t','t.id','ct.test_id')
			 ->join('tbl_visits_orders as o','o.id','ct.order_id')
			 ->where('ct.order_id',$request->order_id)
			 ->where('ct.active','Y')
             ->orderBy('t.testord')
             ->get();
       
    return response()->json(['table_data'=>$codes,'coll_notes'=>$coll_notes]);
}

public function phlebotomy_label($lang,Request $request){
	$order_id = $request->order_id;
	
	$specimens = DB::table('tbl_visits_order_custom_tests as ct')
                  ->selectRaw('spec.name as specimen_name, GROUP_CONCAT(t.test_code SEPARATOR ",") as codes')
				  ->join('tbl_lab_tests as t','t.id','ct.test_id')
			      ->join('tbl_lab_specimen as spec','spec.id','t.specimen')
			      ->where('ct.order_id',$order_id)
				  ->where('ct.active','Y')
				  ->whereNOTNULL('t.test_code')
				  ->where('t.test_code','<>','')
				  ->groupBy('specimen_name')
				  ->get(); 
				  
	    $test_codes = collect();
		foreach ($specimens as $item) {
			$codes = explode(',', $item->codes);
			$chunks = array_chunk($codes, 3);

			foreach ($chunks as $chunk) {
				$test_codes->push([
					'specimen_name' => $item->specimen_name,
					'codes' => $chunk,
				]);
			}
		}

      $test_codes = $test_codes->values();
		  
		//dd($grouped); 
	$label = DB::table('tbl_visits_orders as o')
	         ->select('o.id as order_id',
			          DB::raw("DATE_FORMAT(o.order_datetime,'%Y-%m-%d %H:%i') as order_datetime"),
					  DB::raw("CONCAT(pat.first_name,IFNULL(CONCAT(' ',pat.middle_name),''),' ',pat.last_name) as patient_name"),
					  'pat.id as patient_num',
					  'pat.file_nb as file_nb',
					  'pat.birthdate as patient_dob',
					  'pat.sex as patient_gender',
					  'o.ext_lab',
					  'o.doctor_num',
					  'o.request_nb',
					  DB::raw("DATE_FORMAT(o.collection_date,'%Y-%m-%d %H:%i') as coll_datetime"))
			->join('tbl_patients as pat','pat.id','o.patient_num')
			->where('o.id',$order_id)->first();
	$ext_lab = DB::table('tbl_external_labs')->find($label->ext_lab);
	$doctor = DB::table('tbl_doctors')->find($label->doctor_num);
	
	$data = ['title' => __('Label'),'date' => date('m/d/Y'),'label'=>$label,'ext_lab'=>$ext_lab,'doctor'=>$doctor,'test_codes'=>$test_codes];
	$customPaper = array(0, 0, 180, 90.141732283 );
   $pdf = PDF::setOptions(['orientation' => 'landscape','defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
                        -> loadView('phlotobomy.labelPDF', $data);
   $pdf->setPaper($customPaper);
   return $pdf->stream();
			  
}

public function phlebotomy_serial($lang,Request $request){
	$order_id = $request->order_id;
	$serial_nb = $request->serial_nb;
	$data = DB::table('tbl_visits_orders as o')
	         ->select('o.id as order_id',
			          DB::raw("DATE_FORMAT(o.order_datetime,'%Y-%m-%d %H:%i') as order_datetime"),
					  DB::raw("CONCAT(pat.first_name,IFNULL(CONCAT(' ',pat.middle_name),''),' ',pat.last_name) as patient_name"),
					  'pat.id as patient_num',
					  'pat.birthdate as patient_dob',
					  'pat.sex as patient_gender',
					  DB::raw("IF(doc.id IS NOT NULL,CONCAT(doc.first_name,IFNULL(CONCAT(' ',doc.middle_name),''),' ',doc.last_name),'') as doctor_name"))
			->join('tbl_patients as pat','pat.id','o.patient_num')
			->leftjoin('tbl_doctors as doc','doc.id','o.doctor_num')
			->where('o.id',$order_id)->first();
			
	$data = ['title' => __('Serial'),'date' => date('m/d/Y'),'data'=>$data,'serial_nb'=>$serial_nb];
	$customPaper = array(0, 0, 180, 90.141732283 );
   $pdf = PDF::setOptions(['orientation' => 'landscape','defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
                        -> loadView('phlotobomy.serialPDF', $data);
   $pdf->setPaper($customPaper);
   return $pdf->stream();
			  
}




}