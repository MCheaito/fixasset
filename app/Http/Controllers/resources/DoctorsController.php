<?php
/*
* DEV APP
* Created date : 11-1-2023
*  
* update functions from time to time till today
*
*/
namespace App\Http\Controllers\resources;

use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\User;
use App\Models\RLDoctorClinic;
use App\Models\Permission;
use App\Models\TblBillRate;
use App\Models\TblBillCurrency;
use App\Models\TblDoctorsSpecia;

use Illuminate\Validation\Rule;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Alert;
use DB;
use Session;
use DataTables;

class DoctorsController extends Controller
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
        
		Session::forget('DOCTOR');
		Session::put('DOCTOR',true);
		Session::forget('DOCID');
		
		$user_type=auth()->user()->type;
				
		if(auth()->user()->admin_perm=='O'){
		 $admin_perm_doc = Doctor::where('doctor_user_num',auth()->user()->id)->first();
		}else{
		 $admin_perm_user = User::where('type',1)->where('admin_perm','O')->first();
		 $admin_perm_doc = Doctor::where('doctor_user_num',$admin_perm_user->id)->first();
		}
		 
		$specialities = DB::table('tbl_doctors_specia')->where('name_en','<>','')->orderBy('id')->get();
		
		if($request->ajax()){
			 $specia_name = ($lang=='fr')?'sp.name_fr':'sp.name_en';
				
			$doctors = DB::table('tbl_doctors')
		               ->select('tbl_doctors.id','tbl_doctors.code','tbl_doctors.active','tbl_doctors.priced','tbl_doctors.pricel',
					     'tbl_doctors.pricee','tbl_doctors.tel','tbl_doctors.tel2','tbl_doctors.tel3',
						 'tbl_doctors.email','tbl_doctors.fax','tbl_doctors.address','tbl_doctors.has_prices',
					     DB::raw("IF(tbl_doctors.middle_name IS NOT NULL and tbl_doctors.first_name<>'',concat(tbl_doctors.first_name,' ',tbl_doctors.middle_name,' ',tbl_doctors.last_name),concat(tbl_doctors.first_name,' ',tbl_doctors.last_name)) as doctor_name"),
					     DB::raw("IFNULL({$specia_name},'') as speciality_name"))
				       ->leftjoin('tbl_doctors_specia as sp','sp.id','tbl_doctors.specia')			
		               ->whereRaw('tbl_doctors.id<>?',$admin_perm_doc->id);
					   
            if(isset($request->filter_status) && $request->filter_status!=''){
				$doctors=$doctors->whereRaw('tbl_doctors.active=?',$request->filter_status);
			} 
			
			if(isset($request->filter_speciality) && $request->filter_speciality!=''){
				$doctors=$doctors->whereRaw('sp.id=?',$request->filter_speciality);
			} 
              					   
           $doctors=$doctors->orderBy('tbl_doctors.id','desc')->distinct();
		   return Datatables::of($doctors)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) use($lang){
						
						//$btn ='<a href="#showProfileModal" class="btn btn-sm btn-clean btn-icon "  title="'.__('Show').'" data-toggle="modal" data-target="#showProfileModal" onclick="showProfile('.$row->id.')"><i class="far fa-eye text-primary"></i></a>';	   
						$btn ='<a href="'.route("resources.edit",[app()->getLocale(),$row->id]).'"	title="'.__('Edit').'"	class="btn btn-sm btn-clean btn-icon"><i class="far fa-edit text-primary"></i></a>';
						if( auth()->user()->admin_perm=='O'  || (auth()->user()->permission=='S' && auth()->user()->type==2) ){ 
									       
							if($row->active=='O'){
								 	 
							   $checked =($row->active!='O')?'':'checked';
							   $btn.='<label class="mt-2 slideon slideon-xs  slideon-success"> <input type="checkbox" class="toggle-chk" data-id="'.$row->id.'" '.$checked.'/><span class="slideon-slider"></span> </label>';
							}
						  }
						return $btn;   
					})
					->filterColumn('speciality_name', function($query, $keyword) use($lang){
                       $cat_name = ($lang=='fr')?'sp.name_fr':'sp.name_en';
					   $sql = "{$cat_name} like ?";
                        $query->whereRaw($sql, ["%{$keyword}%"]);
                     })
					 ->filterColumn('doctor_name', function($query, $keyword) use($lang){
					   $sql = "IF(tbl_doctors.middle_name IS NOT NULL and tbl_doctors.first_name<>'',concat(tbl_doctors.first_name,' ',tbl_doctors.middle_name,' ',tbl_doctors.last_name),concat(tbl_doctors.first_name,' ',tbl_doctors.last_name)) like ?";
                        $query->whereRaw($sql, ["%{$keyword}%"]);
                     })
					->rawColumns(['action'])
                    ->make(true);
		 
		}
		
		
		
		return view('resources.index')->with('specialities',$specialities);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request,$lang)
    {
  
		$currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
		$currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
		$lbl_usd = isset($currUSD)? $currUSD->price:15000;
        $lbl_euro = isset($currEURO)? $currEURO->price:15000;
		 if ($lang=="fr"){
		$specias = TblDoctorsSpecia::select('id', 'name_fr as name')->where('active','O')->get();
		 }else{
		$specias = TblDoctorsSpecia::select('id', 'name_en as name')->where('active','O')->get();
		 }
		return view('resources.create')->with([
     
											 'lbl_usd'=>$lbl_usd,
											 'lbl_euro'=>$lbl_euro,
											 'specias'=>$specias
		                                       ]);
										
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($lang,Request $request)
    {
        /*'pricel'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/'],
		  'priced'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/'],
		  'pricee'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/']
		  'pricel.regex' => __('Enter price lbp in this format: 0.00'),
		  'priced.regex' => __('Enter price $ in this format: 0.00'),
		  'pricee.regex' => __('Enter price euro in this format: 0.00')
		  'pricel' => $request->pricel,
		  'priced' => $request->priced,
		  'pricee'=>$request->pricee,
		  */
        		
		$validated = $request->validate([
											'first_name' => ['required', 'string', 'max:255'],
											'code' => ['nullable','string','unique:tbl_doctors'],
											'email' => ['nullable','string', 'email', 'max:255','unique:tbl_doctors'],
											'tel' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
                                            'fax' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/']

                                        ],[
										
										  'code.unique' => __('Code already exists for another resource'),
										  'first_name.required' => __('Please fill your first name'),
										  'email.unique' => __('Email already exists'),
			                              'email.email' => __('Email is invalid'),
										  'tel.regex' =>__('Enter a phone number in this format: 00-000000'),
										  'fax.regex' => __('Enter fax number in this format: 00-0000000')
										  
										]); 
        
		if($validated){
		
		$id=Doctor::create([
							   'first_name' => strtoupper(trim($request->first_name)),
							   'middle_name' => strtoupper(trim($request->middle_name)),
							   'last_name' => strtoupper(trim($request->last_name)),  
							   'code' => $request->code,
							   'specia' => $request->specia,
							   'email' => $request->email,
							   'gender'=>$request->gender,
							   'tel' => str_replace("-","",$request->tel),
							   'tel2'=>str_replace("-","",$request->tel2),
							   'tel3'=>str_replace("-","",$request->tel3),
							   'address'=>trim($request->address),
							   'appt_nb'=>$request->appt_nb,
							   'city'=>$request->city,
							   'state'=>$request->state,
							   'zip_code'=>$request->zip_code,
							   'fax' => str_replace("-","",$request->fax),
							   'remarks' => trim($request->remarks),
							   'active' => 'O',
							   'user_num' => auth()->user()->id ] )->id;
		        		   
		           $doctor=Doctor::find($id);
			       $msg=__('Doctor added successfully');
				   Alert::toast($msg, 'success');
			      // redirect user
				  return redirect(route('resources.edit',[$lang,$doctor->id]));     
			  }
		  
       }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($lang,$id)
    {
		   $doctor = Doctor::find($id);
		   $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
		   $currEURO = TblBillCurrency::where('active','O')->where('abreviation','EUR')->first();
		   $lbl_usd = isset($currUSD)? $currUSD->price:15000;
           $lbl_euro = isset($currEURO)? $currEURO->price:15000;
		   if ($lang=="fr"){
		$specias = TblDoctorsSpecia::select('id', 'name_fr as name')->where('active','O')->get();
		 }else{
		$specias = TblDoctorsSpecia::select('id', 'name_en as name')->where('active','O')->get();
		 }
			return view('resources.create')->with([ 
													  'doctor'=>$doctor,
													  'lbl_usd'=>$lbl_usd,
													  'lbl_euro'=>$lbl_euro,
													  'specias'=>$specias		
													]);
			
		
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($lang,Doctor $id)
    {
		
		//
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($lang,Request $request,$id)
    {
    $msg=__('No update is done.');
	$doctor= Doctor::find($id);	 
	if(isset($request->update_professional)){
		 $validated = $request->validate([
											'first_name' => ['required', 'string', 'max:255'],
											'code' => ['nullable','string',Rule::unique('tbl_doctors')->ignore($doctor->id)],
											'email' => ['nullable','string', 'email', 'max:255',Rule::unique('tbl_doctors')->ignore($doctor->id)],
											'tel' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
                                            'fax' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/']

                                        ],[
										
										  'code.unique' => __('Code already exists for another resource'),
										  'first_name.required' => __('Please fill your first name'),
										  'email.unique' => __('Email already exists'),
			                              'email.email' => __('Email is invalid'),
										  'tel.regex' =>__('Enter a phone number in this format: 00-000000'),
										  'fax.regex' => __('Enter fax number in this format: 00-0000000')
										]); 
		 
		 
	
			$doctor->update([
							   'first_name' => strtoupper(trim($request->first_name)),
							   'middle_name' => strtoupper(trim($request->middle_name)),
							   'last_name' => strtoupper(trim($request->last_name)),  
							   'code' => $request->code,
							   'specia' => $request->specia,
							   'email' => $request->email,
							   'gender'=>$request->gender,
							   'tel' => str_replace("-","",$request->tel),
							   'tel2'=>str_replace("-","",$request->tel2),
							   'tel3'=>str_replace("-","",$request->tel3),
							   'address'=>trim($request->address),
							   'appt_nb'=>$request->appt_nb,
							   'city'=>$request->city,
							   'state'=>$request->state,
							   'zip_code'=>$request->zip_code,
							   'fax' => str_replace("-","",$request->fax),
							   'remarks' => trim($request->remarks),
							   'user_num' => auth()->user()->id
							]);
							
					
				  $msg=__('Doctor updated successfully');
		     
			  }
		      
	
		   Alert::toast($msg, 'success');
			return back();				
						
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($lang,Request $request,$id)
    {
	   $doctor=Doctor::find($id);
	  
		if($request->ajax()) 
		{ 

		     if($request->checked=='N'){
						if(auth()->user()->admin_perm=='O'){
							$res=Doctor::where('id',$id)->update(['active'=>'N','user_num' => auth()->user()->id]);
							//dd($res);
							if($res){
							 if(isset($doctor->doctor_user_num)){
								  $user=User::find($doctor->doctor_user_num);
								  User::where('id',$doctor->doctor_user_num)->update(['active'=>'N']);
								 }	
							// redirect user
							$msg=__('Doctor Deactivated Successfully.');

							Alert::toast($msg, 'success');
							$url = route('resources.index',$lang);
							return response()->json(["url"=>$url]);
							}
						   }else{
							   if(auth()->user()->type==2){
								   //only remove link for doctor from clinic
								   RLDoctorClinic::where('doctor_num',$doctor->id)
								                  ->where('clinic_num',auth()->user()->clinic_num)
												  ->update(['active'=>'N','user_num' => auth()->user()->id]);
									// redirect user
							    $msg=__('Doctor Deactivated Successfully.');
							    Alert::toast($msg, 'success');
							    $url = route('resources.index',$lang);
							return response()->json(["url"=>$url]);			  
							   }
						   }
					}
					if($request->checked=='O'){
						if(auth()->user()->admin_perm=='O'){
							$res=Doctor::where('id',$id)->update(['active'=>'O','user_num' => auth()->user()->id]);
							if($res){
							  if(isset($doctor->doctor_user_num)){
								  $user=User::find($doctor->doctor_user_num);
								 
								  User::where('id',$doctor->doctor_user_num)->update(['active'=>'O']);
								 }	
							// redirect user
							$msg=__('Doctor Activated Successfully.');

							Alert::toast($msg, 'success');
							$url = route('resources.index',$lang);
							return response()->json(["url"=>$url]);	
							 }
						}else{
							if(auth()->user()->type==2){
								   //only add link for doctor from clinic
								   RLDoctorClinic::where('doctor_num',$doctor->id)
								                  ->where('clinic_num',auth()->user()->clinic_num)
												  ->update(['active'=>'O','user_num' => auth()->user()->id]);
									// redirect user
								$msg=__('Doctor Activated Successfully.');

								Alert::toast($msg, 'success');
								$url = route('resources.index',$lang);
							return response()->json(["url"=>$url]);	
							   }
						}	 
				      
					  }
		
		}
	  	
    }
	


 
}
