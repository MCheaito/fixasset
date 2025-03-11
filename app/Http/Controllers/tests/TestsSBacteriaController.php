<?php

namespace App\Http\Controllers\tests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\LabTestsAntibiotic;
use App\Models\LabTestsSBacteria;
use DB;
use DataTables;
use Alert;


class TestsSBacteriaController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    	 public function index($lang,Request $request)
    {
         $user_type = auth()->user()->type;
		  
			  $user_clinic_num=auth()->user()->clinic_num;
			  $my_labs = Clinic::where('id',$user_clinic_num)->orderBy('id','desc')->get();
		  
		   
		   
		   
		  $antibiotic = DB::table('tbl_lab_sbacteria')->where('active','Y')->orderBy('testord')->orderBy('id','desc')->get();

	
	  if($request->ajax()){
		  $filter_status='';
       
		   if(isset($request->filter_status) && $request->filter_status!=''){
			   $filter_status = $request->filter_status;
		   } 
		   
		   

          
	         
			
               $data = DB::table('tbl_lab_sbacteria')
			        ->select('id','code','descrip','active',DB::raw("IFNULL(testord,0) as testord"));
					
			if($filter_status =='Y'){
				$data = $data->where('active',$filter_status);
				             
			    }
			if($filter_status =='N'){
				$data = $data->where('active','N')->orWhereNULL('active');	
				}			 
		
		   //$data = $data->orderBy('testord')->orderBy('id','desc');
		
		
		return Datatables::of($data)
                    ->addIndexColumn()
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

        return view('tests.sbacteria.index')->with(['my_labs'=>$my_labs]);
    }

    /**
     * Get test lab info.
     *
     * @return \Illuminate\View\View
     */
   public function get_info($lang,Request $request)
    {
		
	    $id = $request->id;
		$data = DB::table('tbl_lab_sbacteria')->find($id);
		return response()->json(['data'=>$data]);
	}
    /**
     * Store a newly created resource in storage or update an exiting one.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store($lang,Request $request)
    {
	   
	   $id = $request->id;
	   $user_id=auth()->user()->id;
	   $user_clinic_num=$request->lab_num; ; 
	   $code = $request->code;
	   if($id=='0'){
		  
		  $c = DB::table('tbl_lab_sbacteria')->where('code',$code)->first();
		  if(isset($c) && isset($c->code) && $c->code!=''){
			  return response()->json(['error'=>'Code already exists for another category']);
		  }
		  
		  DB::table('tbl_lab_sbacteria')->insert([
			 'code'=>$request->code,
			 'descrip'=>$request->descrip,
			 'testord'=>isset($request->testord)?$request->testord:0,
			 'user_num'=>$user_id,
			 'active'=>'Y'
		    ]); 
	   
	   return response()->json(['success'=>__('Saved successfully')]);
	   }else{
		  $c = DB::table('tbl_lab_sbacteria')->where('id','<>',$id)->where('code',$code)->first();
		  if(isset($c) && isset($c->code) && $c->code!=''){
			  return response()->json(['error'=>'Code already exists for another category']);
		  }
		   
		   
		   DB::table('tbl_lab_sbacteria')->where('id',$id)->update([
			 'code'=>$request->code,
			 'descrip'=>$request->descrip,
			 'testord'=>isset($request->testord)?$request->testord:0,
			 'user_num'=>$user_id
			   ]); 
	   	 return response()->json(['success'=>__('Updated successfully')]);	

	   }		
		
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($lang,Request $request)
    {
        $id = $request->id;
	    $checked = $request->checked;
		
		if($checked=='N'){
		DB::table('tbl_lab_sbacteria')->where('id',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
		DB::table('tbl_lab_tests')->where('category_num',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('InActivated successfully')]);	
		}
		
		if($checked=='O'){
	    DB::table('tbl_lab_sbacteria')->where('id',$id)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
		DB::table('tbl_lab_tests')->where('category_num',$id)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('Activated successfully')]);		
		}
			
    }
}
