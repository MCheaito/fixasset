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


class TestsFormulasController extends Controller
{
    
	public function __construct(){
        $this->middleware('auth');
    }
	
	public function index($lang,Request $request){
		 $user_type = auth()->user()->type;
		   
			  $user_clinic_num=auth()->user()->clinic_num;
			  $my_labs = Clinic::where('id',$user_clinic_num)->orderBy('id','desc')->get();
		   
		  
		  $tests = DB::table('tbl_lab_tests as t')->select('t.id',DB::raw("IF(g.test_name IS NOT NULL and g.test_name<>'',CONCAT(t.test_name,'-',g.test_name),t.test_name) as test_name"))
		           ->leftjoin('tbl_lab_tests as g',function($q){
					   $q->on('g.id','t.group_num');
					    $q->where('g.is_group','Y');
				   })
				   ->where('t.is_group','<>','Y')->where('t.active','Y')->orderBy('t.id','desc')
				   ->distinct()
				   ->get();
			
          $tests_formulas = DB::table('tbl_lab_tests')->where(function($q){
						   $q->where('test_type','F');
						   $q->orWhere('test_type','C');
					       })->where('active','Y')->orderBy('id','desc')->get();
		  
		  
		  //dd($tests_formulas);
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
			   
			   $data = DB::table('tbl_lab_tests_formulas as f')
			           ->select('f.id','t.test_name','f.formula','f.active',
					            'f.test1','f.test2','f.test3','f.test4',
								'f.factor1','f.factor2','f.factor3','f.factor4')
					   ->join('tbl_lab_tests as t','t.id','f.test_id');

			 if($filter_status =='Y'){
				$data = $data->where('f.active',$filter_status);
			    }
			  
			  if($filter_status =='N'){
				$data = $data->where('f.active','N')->orWhereNULL('f.active');	
				}
              
			  if($filter_lab !=''){
			   $data = $data->where('f.clinic_num',$filter_lab);
 			    }
              
			  if($filter_name !=''){
			   $data = $data->where('f.test_id',$filter_name);
 			    }
              
			  $data = $data->orderBy('f.id','desc');
             // dd($data);
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
		  return view('tests.formulas.formula')->with(['my_labs'=>$my_labs,'tests'=>$tests,'tests_formulas'=>$tests_formulas]); 
	}
	
public function get_info($lang,Request $request)
    {
		
	    $id = $request->id;
		$data = DB::table('tbl_lab_tests_formulas')->find($id);
		return response()->json(['data'=>$data]);
	}

 public function store($lang,Request $request)
    {
	   
	   $id = $request->id;
	   $user_id=auth()->user()->id;
	   $user_clinic_num=$request->lab_num; ; 
	   $formula = strtolower($request->formula);
	  
	   if($id=='0'){
		  
		  DB::table('tbl_lab_tests_formulas')->insert([
		     'test_id'=>$request->test_id,
			 'formula'=>$formula,
			 'unit'=>$request->unit,
			 'test1'=>$request->test1,
			 'test2'=>$request->test2,
			 'test3'=>$request->test3,
			 'test4'=>$request->test4,
			 'factor1'=>$request->factor1,
			 'factor2'=>$request->factor2,
			 'factor3'=>$request->factor3,
			 'factor4'=>$request->factor4,
			 'user_num'=>$user_id,
			 'clinic_num'=>$user_clinic_num,
			 'active'=>'Y'
		    ]); 
	   
	  
	      $msg=__('Saved successfully');
	   }else{
		  
		     DB::table('tbl_lab_tests_formulas')->where('id',$id)->update([
		     'test_id'=>$request->test_id,
			 'formula'=>$formula,
			 'unit'=>$request->unit,
			 'test1'=>$request->test1,
			 'test2'=>$request->test2,
			 'test3'=>$request->test3,
			 'test4'=>$request->test4,
			 'factor1'=>$request->factor1,
			 'factor2'=>$request->factor2,
			 'factor3'=>$request->factor3,
			 'factor4'=>$request->factor4,
			 'user_num'=>$user_id
			   ]); 
	   	 $msg=__('Updated successfully');	

	   }

        	   
	return response()->json(['success'=>$msg]);	
	}
	
 public function destroy($lang,Request $request)
    {
        $id = $request->id;
	    $checked = $request->checked;
		
		if($checked=='N'){
		DB::table('tbl_lab_tests_formulas')->where('id',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('InActivated successfully')]);	
		}
		
		if($checked=='O'){
		DB::table('tbl_lab_tests_formulas')->where('id',$id)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('Activated successfully')]);		
		}
			
    }	

	

}	
	