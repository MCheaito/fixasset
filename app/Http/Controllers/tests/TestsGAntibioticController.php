<?php

namespace App\Http\Controllers\tests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\LabTestsAntibiotic;
use App\Models\LabTestsSBacteria;
use App\Models\LabTestsGAntibiotic;
use DB;
use DataTables;
use Alert;


class TestsGAntibioticController extends Controller
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
  public function index($lang,Request $request){
		 $user_type = auth()->user()->type;
		   
			  $user_clinic_num=auth()->user()->clinic_num;
			  $my_labs = Clinic::where('id',$user_clinic_num)->orderBy('id','desc')->get();
		   
		   
		   $tests = DB::table('tbl_lab_antibiotic')->where('active','Y')->orderBy('id','desc')->get();
			
          $antibiotic = DB::table('tbl_lab_antibiotic_group')->orderBy('id','desc')->get();
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
			    
			   $data = DB::table('tbl_lab_antibiotic_group as p');
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
						$bacteria_antibiotic = json_decode($row->antibiotics,true);
						$tests = LabTestsAntibiotic::whereIn('id',$bacteria_antibiotic)->get();
						$names ='';
						if($tests->count()==1){
						  foreach($tests as $t){
							$names = $t->descrip;
						  }	
						}else{
						foreach($tests as $t){
							$names .= $t->descrip.',';
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
		  return view('tests.gantibiotic.index')->with(['my_labs'=>$my_labs,'tests'=>$tests,'antibiotic'=>$antibiotic]); 
	}
    /**
     * Get test lab info.
     *
     * @return \Illuminate\View\View
     */
   public function get_info($lang,Request $request)
    {
		
	    $id = $request->id;
		$data = DB::table('tbl_lab_antibiotic_group')->find($id);
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
	   $name = $request->group_name;
	   
	   if($id=='0'){
		   $c = DB::table('tbl_lab_antibiotic_group')->where('descrip',$request->group_name)->first();
		  if(isset($c) && isset($c->descrip) && $c->descrip!=''){
			  return response()->json(['error'=>'Name already exists for another antibiotic']);
		  }
		  
		  DB::table('tbl_lab_antibiotic_group')->insert([
		     'descrip'=>$request->group_name,
			 'user_num'=>$user_id,
			 'clinic_num'=>$user_clinic_num,
			 'antibiotics'=>json_encode($request->get('antibiotic_tests')),
			 'active'=>'Y'
		    ]); 
	   
	  
	      $msg=__('Saved successfully');
	   }else{
		   
		   $c = DB::table('tbl_lab_antibiotic_group')->where('id','<>',$id)->where('descrip',$request->group_name)->first();
		   if(isset($c) && isset($c->descrip) && $c->descrip!=''){
			  return response()->json(['error'=>'Name already exists for another antibiotic']);
		    }
		   
		     DB::table('tbl_lab_antibiotic_group')->where('id',$id)->update([
		     'descrip'=>$request->group_name,
			 'antibiotics'=>json_encode($request->get('antibiotic_tests')),
			 'user_num'=>$user_id
			   ]); 
	   	 $msg=__('Updated successfully');	

	   }


        $html ='<option value="">'.__("Choose a Group").'</option>';
	   $bacterias = DB::table('tbl_lab_antibiotic_group')->orderBy('id','desc')->get();
       foreach($bacterias as $p){
		  $html .='<option value="'.$p->id.'">'.$p->descrip.'</option>'; 
	   }	   	   
	return response()->json(['success'=>$msg,'html'=>$html]);	
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
		DB::table('tbl_lab_antibiotic_group')->where('id',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('InActivated successfully')]);	
		}
		
		if($checked=='O'){
		DB::table('tbl_lab_antibiotic_group')->where('id',$id)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('Activated successfully')]);		
		}
			
    }
}
