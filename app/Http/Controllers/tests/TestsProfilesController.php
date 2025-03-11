<?php

namespace App\Http\Controllers\tests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\LabTests;
use App\Models\Doctor;
use App\Models\Clinic;

use DB;
use DataTables;
use Alert;


class TestsProfilesController extends Controller
{
    
	public function __construct(){
        $this->middleware('auth');
    }
	
	public function index($lang,Request $request){
		 $user_type = auth()->user()->type;
		   
			  $user_clinic_num=auth()->user()->clinic_num;
			  $my_labs = Clinic::where('id',$user_clinic_num)->orderBy('id','desc')->get();
		   
		   
		   $tests = DB::table('tbl_lab_tests')->where(function($q){
						   $q->where('is_group','Y');
						   $q->orWhereNotNull('cnss');
					   })->where('active','Y')->orderBy('id','desc')->get();
			
          $profiles = DB::table('tbl_lab_tests_profiles')->orderBy('id','desc')->get();
			
		  if($request->ajax()){
			  $filter_name='';
			  if(isset($request->filter_name) && $request->filter_name!=''){
			   $filter_name = $request->filter_name;
		      } 
			  $filter_status='';
			  if(isset($request->filter_status) && $request->filter_status!=''){
			   $filter_status = $request->filter_status;
		      } 
			  $filter_lab='';
         	   if(isset($request->filter_lab) && $request->filter_lab!=''){
			     $filter_lab = $request->filter_lab;
		       }
			   
			   $data = DB::table('tbl_lab_tests_profiles as p');
			   if($filter_status =='Y'){
				$data = $data->where('p.active',$filter_status);
				             
			    }
			  if($filter_status =='N'){
				$data = $data->where('p.active','N')->orWhereNULL('p.active');	
				}
              if($filter_lab !=''){
			   $data = $data->where('p.clinic_num',$filter_lab);
 			    }
              if($filter_name !=''){
			   $data = $data->where('p.id',$filter_name);
 			    }
               $data = $data->orderBy('p.id','desc')->get();

               return Datatables::of($data)
                    ->addIndexColumn()
					->addColumn('tests_names', function($row){
						$profile_tests = json_decode($row->profile_tests,true);
						$tests = LabTests::whereIn('id',$profile_tests)->get();
						$names ='';
						if($tests->count()==1){
						  foreach($tests as $t){
							$names = $t->test_name;
						  }	
						}else{
						foreach($tests as $t){
							$names .= $t->test_name.',';
						}
						
						$names = substr($names,0,-1);
						}
						return $names;
					})
                   ->addColumn('action', function($row){
                           $checked=($row->active=='Y')?'checked':'';
						   $disabled=($row->active=='Y')?'':'disabled';
						   $btn = '<button type="button"  class="btn btn-icon btn-md" onclick="editData('.$row->id.')" '.$disabled.'><i class="far fa-edit text-primary" title="'.__('Edit').'"></i></button>';
                           $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';
						   return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);			   
			  
		    } 
		  return view('tests.profiles.index')->with(['my_labs'=>$my_labs,'tests'=>$tests,'profiles'=>$profiles]); 
	}
	
public function get_info($lang,Request $request)
    {
		
	    $id = $request->id;
		$data = DB::table('tbl_lab_tests_profiles')->find($id);
		return response()->json(['data'=>$data]);
	}

 public function store($lang,Request $request)
    {
	   
	   $id = $request->id;
	   $user_id=auth()->user()->id;
	   $user_clinic_num=$request->lab_num; ; 
	   $name = $request->profile_name;
	   
	   if($id=='0'){
		   $c = DB::table('tbl_lab_tests_profiles')->where('profile_name',$request->profile_name)->first();
		  if(isset($c) && isset($c->profile_name) && $c->profile_name!=''){
			  return response()->json(['error'=>'Name already exists for another profile']);
		  }
		  
		  DB::table('tbl_lab_tests_profiles')->insert([
		     'profile_name'=>$request->profile_name,
			 'user_num'=>$user_id,
			 'clinic_num'=>$user_clinic_num,
			 'profile_tests'=>json_encode($request->get('profile_tests')),
			 'active'=>'Y'
		    ]); 
	   
	  
	      $msg=__('Saved successfully');
	   }else{
		   
		   $c = DB::table('tbl_lab_tests_profiles')->where('id','<>',$id)->where('profile_name',$request->profile_name)->first();
		   if(isset($c) && isset($c->profile_name) && $c->profile_name!=''){
			  return response()->json(['error'=>'Name already exists for another profile']);
		    }
		   
		     DB::table('tbl_lab_tests_profiles')->where('id',$id)->update([
		     'profile_name'=>$request->profile_name,
			 'profile_tests'=>json_encode($request->get('profile_tests')),
			 'user_num'=>$user_id
			   ]); 
	   	 $msg=__('Updated successfully');	

	   }

        $html ='<option value="">'.__("Choose a profile").'</option>';
	   $profiles = DB::table('tbl_lab_tests_profiles')->orderBy('id','desc')->get();
       foreach($profiles as $p){
		  $html .='<option value="'.$p->id.'">'.$p->profile_name.'</option>'; 
	   }	   	   
	return response()->json(['success'=>$msg,'html'=>$html]);	
	}
	
 public function destroy($lang,Request $request)
    {
        $id = $request->id;
	    $checked = $request->checked;
		
		if($checked=='N'){
		DB::table('tbl_lab_tests_profiles')->where('id',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('InActivated successfully')]);	
		}
		
		if($checked=='O'){
		DB::table('tbl_lab_tests_profiles')->where('id',$id)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('Activated successfully')]);		
		}
			
    }	

	

}	
	