<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\PatientInsurance;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\TblVisits;
use App\Models\ExtLab;
use App\Models\ExtIns;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Alert;
use DataTables;
use Session;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	
	/**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($lang,Request $request)
    {
		 
		
		 
		 $user_type = auth()->user()->type;
		 $myId=auth()->user()->clinic_num;
		 $user_num  = auth()->user()->id;
		 $patients_list =collect();
		 switch($user_type){
			 case 1: 
			   
			   $ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
			   
			   $doc_id = Doctor::where('doctor_user_num',$user_num)->value('id');
			   
			   $doctors = Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('doctor_user_num',$user_num)->get();
			   
			   $clinic=Clinic::find(auth()->user()->clinic_num);
			   
			   $patients_list = Patient::select('id','first_name','middle_name','last_name')
		                               ->where('doctor_num',$doc_id)
									   ->orderBy('id','desc')
							           ->get();			   
			 break;
			 case 3: 
			   
			   $ext_lab_id = ExtLab::where('lab_user_num',$user_num)->value('id');
			   
			   $ext_labs=ExtLab::select('id','full_name')->where('lab_user_num',$user_num)->get();
			   
			   $doctors = Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();

			   $clinic=Clinic::find(auth()->user()->clinic_num);
			   
			   $patients_list = Patient::select('id','first_name','middle_name','last_name')
		                               ->where('ext_lab',$ext_lab_id)
									   ->orderBy('id','desc')
							           ->get();			   
			 break;
			 case 2: 
			   
			   $clinic=Clinic::find(auth()->user()->clinic_num);
			   $ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
			   $doctors = Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
			   $patients_list = Patient::where('clinic_num',$myId)->orderBy('id','desc')->get(['id','first_name','middle_name','last_name']);			 
			 break;
		 }
        
		if ($request->ajax()) {
			$filter_status = "";
			if(isset($request->status) && $request->status!="0"){
				$filter_status = " and tbl_patients.status = '".$request->status."' ";
			}
			
			$filter_patient = '';
			if(isset($request->filter_patient) && $request->filter_patient!="0"){
				$filter_patient = " and tbl_patients.id = '".$request->filter_patient."' ";
			}
			
			$filter_extlab = '';
			if(isset($request->ext_lab) && $request->ext_lab !="0"){
				$filter_extlab = " and tbl_patients.ext_lab = '".$request->ext_lab."' ";
			}
			
			$filter_doc = '';
			if(isset($request->doctor_num) && $request->doctor_num !="0"){
				$filter_doc = " and tbl_patients.doctor_num = '".$request->doctor_num."' ";
			}
			
			$sql="select DISTINCT(tbl_patients.id),tbl_patients.clinic_num,tbl_patients.middle_name,tbl_patients.last_name,
				             tbl_patients.birthdate,tbl_patients.first_phone,tbl_patients.cell_phone,tbl_patients.sex,
						     tbl_patients.email,tbl_patients.status,clin.full_name as clinic_name,clin.id as lab_num,
							 IFNULL(CONCAT(d.first_name,IFNULL(CONCAT(' ',d.middle_name,' '),' '),d.last_name),'') as doctor_name,
							 IFNULL(lab.full_name,'') as ext_lab,
							 CONCAT(IFNULL(CONCAT(t.name,' '),''),tbl_patients.first_name) as pat_fname,
							 tbl_patients.file_nb
				      from tbl_patients
					  INNER JOIN tbl_clinics as clin ON clin.id=tbl_patients.clinic_num and clin.active='O'
					  LEFT JOIN tbl_patients_titles as t ON t.id=tbl_patients.title and t.status='Y' 
					  LEFT JOIN tbl_external_labs as lab ON lab.id=tbl_patients.ext_lab and lab.status='A'
					  LEFT JOIN tbl_doctors as d ON d.id=tbl_patients.doctor_num and d.active='O'
				      where tbl_patients.clinic_num='".$myId."'  ".$filter_status."  ".$filter_patient." ".$filter_extlab." ".$filter_doc."
					  Order By id desc";
				 
				
		 $patients = DB::select(DB::raw("$sql")); 
		 
		 return response()->json($patients);
		 
		  }
		
	  return view('patients_list.index')->with(['clinic'=>$clinic,'patients_list'=>$patients_list,'ext_labs'=>$ext_labs,'doctors'=>$doctors]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($lang)
    {
		$clinic=Clinic::find(auth()->user()->clinic_num);
		$user_type = auth()->user()->type;
		$user_num = auth()->user()->id;
        
		if($user_type==2)
		{
		
		$doctors=Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
		$ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
		}
		
		if($user_type==1){
		 $ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
		 $doctors=Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('doctor_user_num',$user_num)->get();	
		}
		
		if($user_type==3){
	     $doctors=Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
		 $ext_labs=ExtLab::select('lab_user_num as user_num','full_name')->where('lab_user_num',$user_num)->get();
		}
		
		 $titles = DB::table('tbl_patients_titles')->where('status','Y')->get();
		 $insurance=ExtIns::where('status','A')->orderBy('id','desc')->get();
		//dd($docs);
		return view('patients_list.create')->with(['clinic'=>$clinic,'titles'=>$titles,'insurance'=>$insurance,'ext_labs'=>$ext_labs,'doctors'=>$doctors]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
	
    public function store($lang,Request $request)
    {
	  
		 $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',			
            'last_name' => 'required|string|max:255',
			'gender'=>'required|string',
			'birthdate'=>'required',
            'clinic_num' => 'required'		
		    ]);
		  
       
		// Check if a patient with the same first, middle, last name, and DOB exists
         /*$existingPatientWithDOB = Patient::where(DB::raw('trim(upper(first_name))'), trim(strtoupper($request->first_name)))
            ->where(DB::raw('trim(upper(middle_name))'), trim(strtoupper($request->middle_name)))
            ->where(DB::raw('trim(upper(last_name))'), trim(strtoupper($request->last_name)))
            ->where('birthdate', $request->birthdate)
			->first();
		
		if($existingPatientWithDOB){
            return back()->withinput()->withErrors(['msg' => 'Patient with the same name and birthdate already exists']);
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
        
		   
		Alert::toast( __("New Patient Added Successfully."),"success");
        // redirect user
	    //return back();
		return redirect()->route('patientslist.edit',[$lang, $pat_id]);
      
       }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($lang,$id)
    {
		$patient=Patient::find($id);
		$clinic=Clinic::find(auth()->user()->clinic_num);
		$user_type = auth()->user()->type;
		$user_num = auth()->user()->id;
        if($user_type==2)
		{
		
		$doctors=Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
		$ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
		}
		
		if($user_type==1){
		 $ext_labs=ExtLab::select('id','full_name')->where('status','A')->orderBy('full_name')->get();
		 $doctors=Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('doctor_user_num',$user_num)->get();	
		}
		
		if($user_type==3){
	     $doctors=Doctor::select('id',DB::raw("CONCAT(first_name,IFNULL(CONCAT(' ',middle_name,' '),' '),last_name) as full_name"))->where('active','O')->orderBy('first_name')->orderBy('last_name')->get();
		 $ext_labs=ExtLab::select('lab_user_num as user_num','full_name')->where('lab_user_num',$user_num)->get();
		}
		
		 $insurance=ExtIns::where('status','A')->orderBy('id','desc')->get();
         $titles = DB::table('tbl_patients_titles')->where('status','Y')->get();
	 
		return view('patients_list.edit')->with('patient', $patient)->with(['titles'=>$titles,'clinic'=>$clinic,'insurance'=>$insurance,'ext_labs'=>$ext_labs,'doctors'=>$doctors]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($lang,$id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($lang,Request $request,$id)
    {
     
	 $patient=Patient::find($id); 
	
		 $validated = $request->validate([
            'first_name' => 'required|string|max:255',
			'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string',
			'birthdate' => 'required'
		    ]); 
		 
	    $myId=auth()->user()->id; 
       
		// Check if a patient with the same first, middle, last name, and DOB exists
         /*$existingPatientWithDOB = Patient::where(DB::raw('trim(upper(first_name))'), trim(strtoupper($request->first_name)))
            ->where(DB::raw('trim(upper(middle_name))'), trim(strtoupper($request->middle_name)))
            ->where(DB::raw('trim(upper(last_name))'), trim(strtoupper($request->last_name)))
            ->where('birthdate', $request->birthdate)
			->where('id','<>',$id)
			->first();*/
		
		//if($existingPatientWithDOB){
          //  return back()->withinput()->withErrors(['msg' => 'Patient with the same name and birthdate already exists']);
         //}  
	
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
		
		
		Alert::toast( __("Patient Updated Successfully."),"success");
        // redirect user
	    return back();
		
    
	}
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($lang,$id)
    {
   
	   
    }
	
	
	//inactivate patient
	public function inactivate_patient($lang,Request $request)
    {
     $myId=auth()->user()->id; 
     Patient::where('id',$request->id)->update(['status'=>'N','user_num'=>$myId]);
	 
	 return response()->json(["msg"=>__('Patient InActivated successfully.')]);
            
    }
	
	//activate patient
	public function activate_patient($lang,Request $request)
    {
     $myId=auth()->user()->id; 
     Patient::where('id',$request->id)->update(['status'=>'O','user_num'=>$myId]);
	 
	 return response()->json(["msg"=>__('Patient Activated successfully.')]);
            
    }


public function getPatResults($lang,Request $request){
	$id = $request->patient_id;
	//dd($id);
	$results = DB::table('tbl_visits_order_results as r')
		             ->select(
					          DB::raw("DATE_FORMAT(v.visit_date_time,'%Y-%m-%d %H:%i') as visit_data"),
		                      DB::raw("CONCAT(t.test_name,' ','(',' ',t.test_code,' ',')') as test_name"),
							  DB::raw("IFNULL(g.test_name,'No group') as group_name"),
							  DB::raw("IFNULL(t.unit,'') as unit"),
							  DB::raw("IFNULL(t.normal_value,'') as range_val"),
							  DB::raw("IFNULL(r.result,'') as result"),
							  'o.id as order_id','o.status as order_status')
							 ->join('tbl_lab_tests as t','t.id','r.test_id')
							 ->join('tbl_visits_orders as o','o.id','r.order_id')
							 ->join('tbl_visits as v','v.id','o.visit_num')
							 ->leftjoin('tbl_lab_tests as g',function($q){
				               $q->on('g.id','t.group_num');
							   $q->where('g.is_group','Y');
							   $q->where('g.active','Y');
				               })
							 ->where('o.active','Y')
							 ->where('v.active','O') 
							 ->where('r.patient_num',$id)
							 ->where('r.active','Y');
	if(auth()->user()->type==1 || auth()->user()->type==3){
		$results = $results->where('o.status','V');
	}						 
   
    $results = $results->orderBy('g.testord')->orderBy('t.testord')->orderBy('v.visit_date_time','desc')->get();
  
  return response()->json($results);

}

public function validatePatResults($lang,Request $request){
  $id = $request->order_id;
  DB::table('tbl_visits_orders')->where('id',$id)->update(['status'=>'V','user_num'=>auth()->user()->id]);
  return response()->json(['msg'=>'Validated successfully']);  
}



public function getPatVisits($lang,Request $request){
	$id = $request->id;
	$patient = Patient::find($id);
	$pat_visits = DB::table('tbl_visits_orders as v')
	              ->select('v.id','v.order_datetime',
				         DB::raw('DATE(v.order_datetime) as visit_date'),
				         DB::raw('DATE_FORMAT(v.order_datetime,"%H:%i") as visit_time'),
						 DB::raw('IF(v.status="P","'.__('Pending').'",
						          IF(v.status="F","'.__('Finished').'",
								  IF(v.status="V","'.__('Validated').'","'.__('Undefined').'"))) as order_status'),
						 'v.id as status')
				  ->where('v.active','Y')
				  ->where('v.clinic_num',$patient->clinic_num)
				  ->where('v.patient_num',$patient->id);
		$from_date = $request->filter_fromdate;		  
		$to_date = $request->filter_todate;
        if(isset($from_date) && $from_date!=''){
			$pat_visits=$pat_visits->where(DB::raw('DATE(v.order_datetime)'),'>=',$from_date);
		}
        if(isset($to_date) && $to_date!=''){
			$pat_visits=$pat_visits->where(DB::raw('DATE(v.order_datetime)'),'<=',$to_date);
		}			
				  
		$pat_visits=$pat_visits->orderBy('v.id','desc')->orderBy('v.order_datetime','desc')->distinct()->get();
			//dd($pat_visits);	  
	return response()->json($pat_visits);			  
    
}

public function new_visit($lang,Request $request){
	$patient_num = $request->patient_num;
	$clinic_num = $request->clinic_num;

		$date_order = Carbon::now()->format('Y-m-d H:i');
		
		if(auth()->user()->type==1){
		  $doctor = Doctor::where('doctor_user_num',auth()->user()->id)->first();
		  $doctor_num = $doctor->id;
		  $ext_lab = NULL;
		}
		
		if(auth()->user()->type==3){
		  $lab=ExtLab::where('lab_user_num',auth()->user()->id)->first();
	      $ext_lab = $lab->id;
		  $doctor_num = NULL;
		}
		
		if(auth()->user()->type==2){
		 $patient = Patient::find($patient_num);
		 $ext_lab = $patient->ext_lab;
		 $doctor_num = $patient->doctor_num;
	    }
	  Session::forget('order_patient_num');
	  Session::forget('order_clinic_num');
	  Session::forget('order_date_time');
	  Session::forget('order_ext_lab');
	  Session::forget('order_doctor_num');
	  
	  Session::put('order_patient_num',$patient_num);
	  Session::put('order_clinic_num',$clinic_num);
	  Session::put('order_date_time',$date_order);
	  Session::put('order_ext_lab',$ext_lab);
	  Session::put('order_doctor_num',$doctor_num);
	 
    return response()->json(['success'=>true]);	
}

public function createTitleTag($lang,Request $request){
 //dd("HI");
 $term = $request->term;	

}

public function loadPat($lang,Request $request){

	$search = $request->input('q');
    $user_type= auth()->user()->type;
	$clinic_num = auth()->user()->clinic_num;
	$status = $request->input('status');
	$user_num = auth()->user()->id;
	
	$patients = Patient::select('tbl_patients.id',
	                             DB::raw("CONCAT(IF(t.name IS NOT NULL and t.name<>'',CONCAT(t.name,' '),''),
								                       tbl_patients.first_name,
			                                           IF(tbl_patients.middle_name IS NOT NULL and tbl_patients.middle_name<>'',CONCAT(' ',tbl_patients.middle_name),''),' ',
													   tbl_patients.last_name,' ','[',' ','File Nb.',' : ',tbl_patients.file_nb,
			                                           IF(tbl_patients.ext_lab IS NOT NULL and tbl_patients.ext_lab<>'',CONCAT(' , ',g.full_name),''),
												       IF(tbl_patients.cell_phone IS NOT NULL and tbl_patients.cell_phone<>'',CONCAT(' , ','Cell#',' : ',tbl_patients.cell_phone),''),' ',']') as text"))
				->leftjoin('tbl_external_labs as g','g.id','tbl_patients.ext_lab')
			    ->leftjoin('tbl_patients_titles as t',function($q){
					$q->on('t.id','tbl_patients.title');
					$q->where('t.status','Y');
				   });
			
	$patients = $patients->where('tbl_patients.clinic_num',$clinic_num);
	
	if(isset($status)){
		$patients = $patients->where('tbl_patients.status','O');
	}
	
	if($user_type==1){
		$doc_id = Doctor::where('doctor_user_num',$user_num)->value('id');
		$patients = $patients->where('tbl_patients.doctor_num',$doc_id);
	}
	
	if($user_type==3){
		$lab_id = ExtLab::where('lab_user_num',$user_num)->value('id');
		$patients = $patients->where('tbl_patients.ext_lab',$lab_id);
	}
	
	
	 if($search !='' && $search!=NULL){				  
		     $patients=$patients->where(DB::raw("UPPER(CONCAT(IF(t.name IS NOT NULL and t.name<>'',CONCAT(t.name,' '),''),tbl_patients.first_name,
			                                                  IF(tbl_patients.middle_name IS NOT NULL and tbl_patients.middle_name<>'',CONCAT(' ',tbl_patients.middle_name),''),' ',tbl_patients.last_name,' ','[',' ','File Nb.',' : ',tbl_patients.file_nb,
			                                                  IF(tbl_patients.ext_lab IS NOT NULL and tbl_patients.ext_lab<>'',CONCAT(' , ',g.full_name),''),
												              IF(tbl_patients.cell_phone IS NOT NULL and tbl_patients.cell_phone<>'',CONCAT(' , ','Cell#',' : ',tbl_patients.cell_phone),''),' ',']'))"),'like','%'.strtoupper($search).'%');
		
			        }
		          
	$patients=$patients->orderBy('id','desc')->paginate(100);	
	
	
    return response()->json([
        'results' => $patients->items(),
        'pagination' => [
            'more' => $patients->hasMorePages(),
        ],
    ]);
	
}

public function loadDoctors($lang,Request $request){
	$search = $request->input('q');
    $status = $request->input('status');
	
	$doctors = Doctor::select('id',DB::raw("CONCAT(first_name,
	                                          IF(middle_name IS NOT NULL and middle_name<>'',CONCAT(' ',middle_name),''),' ',
											  last_name) as text"));

  if(isset($status)){ $doctors = $doctors->where('active',$status); }
  
  if($search !='' && $search!=NULL){
	 $doctors = $doctors->where(DB::raw("UPPER(CONCAT(first_name,
	                                    IF(middle_name IS NOT NULL and middle_name<>'',CONCAT(' ',middle_name),''),' ',
										last_name))"),'like','%'.strtoupper($search).'%');
   }
   
  $doctors=$doctors->orderBy('first_name')->orderBy('last_name')->paginate(100);	
  
  return response()->json([
        'results' => $doctors->items(),
        'pagination' => [
            'more' => $doctors->hasMorePages(),
        ],
    ]);

}


public function loadGuarantors($lang,Request $request){
	$search = $request->input('q');
    $status = $request->input('status');
	
	$grntrs = ExtLab::select('id','full_name as text');

  if(isset($status)){ $grntrs = $grntrs->where('status',$status); }
  
  if($search !='' && $search!=NULL){
	 $grntrs = $grntrs->where(DB::raw("UPPER(full_name)"),'like','%'.strtoupper($search).'%');
   }
   
  $grntrs=$grntrs->orderBy('full_name')->paginate(100);	
  
  return response()->json([
        'results' => $grntrs->items(),
        'pagination' => [
            'more' => $grntrs->hasMorePages(),
        ],
    ]);
	
	
}

 
	
}
