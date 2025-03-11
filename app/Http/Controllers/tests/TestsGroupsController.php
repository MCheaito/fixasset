<?php

namespace App\Http\Controllers\tests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\LabTests;
use App\Models\ExtLab;

use DB;
use DataTables;
use Alert;


class TestsGroupsController extends Controller
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
		   
		   
		  $categories = DB::table('tbl_lab_categories')->where('active','Y')->orderBy('id','desc')->get();
          $ext_labs = ExtLab::where('status','A')->where('clinic_num',$user_clinic_num)->orderBy('id','desc')->get();

	
	  if($request->ajax()){
		  $filter_status='';
       
		   if(isset($request->filter_status) && $request->filter_status!=''){
			   $filter_status = $request->filter_status;
		   } 
		   
		    $filter_cat='';
       
		   if(isset($request->filter_cat) && $request->filter_cat!=''){
			   $filter_cat = $request->filter_cat;
		   } 

          
	         
			 $data = DB::table('tbl_lab_tests as g')
			        ->select('g.id','g.test_code as code','g.test_name as descrip','g.cnss','g.nbl','g.price','g.active',
					  'g.unit','g.normal_value','g.test_rq','g.listcode','cat.descrip as cat_name','g.testord as order','g.descrip as instruction',
					  DB::raw("IFNULL(ext.full_name,'') as referred_lab"))
					->leftjoin('tbl_external_labs as ext','ext.id','g.referred_tests')  
					->join('tbl_lab_categories as cat','cat.id','g.category_num')
					->where('g.is_group','Y');
							   
			if($filter_status =='Y'){
				$data = $data->where('g.active',$filter_status);
				             
			    }
			if($filter_status =='N'){
				$data = $data->where('g.active','N')->orWhereNULL('g.active');	
				}			 
		
		    if($filter_cat !=''){
				$data = $data->where('g.category_num',$filter_cat);
				             
			    }
		   $data = $data->orderBy('g.testord')->orderBy('g.id','desc');
		
		
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

        return view('tests.groups.index')->with(['ext_labs'=>$ext_labs,'my_labs'=>$my_labs,'categories'=>$categories]);
    }

    /**
     * Get test lab info.
     *
     * @return \Illuminate\View\View
     */
   public function get_info($lang,Request $request)
    {
		
	    $id = $request->id;
		$data = DB::table('tbl_lab_tests')->find($id);
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
		  $c = DB::table('tbl_lab_tests')->where('category_num',$request->category_num)
		                                 ->where('is_group','Y')->where('test_code',$code)->first();
		  if(isset($c) && isset($c->test_code) && $c->test_code !=''){
			  return response()->json(['error'=>'Group code already exists for same category']);
		  }
		   
		  
		  DB::table('tbl_lab_tests')->insert([
		     'category_num'=>$request->category_num,
			 'clinic_num'=>$request->lab_num,
			 'test_code'=>$request->code,
			 'test_name'=>$request->descrip,
			 'cnss'=>$request->cnss,
			 'nbl'=>$request->nbl,
			 'price'=>$request->price,
			 'user_num'=>$user_id,
			 'testord'=>$request->testord,
			 'descrip'=>$request->description,
			 'is_group'=>'Y',
			 'test_rq'=>$request->test_rq,
			 'unit'=>$request->unit,
			 'normal_value'=>$request->normal_value,
			 'referred_tests'=>$request->referred_tests,
			 'preanalytical'=>$request->preanalytical,
			 'storage'=>$request->storage,
			 'transport'=>$request->transport,
			 'tat_hrs'=>$request->tat_hrs,
			 'active'=>'Y'
		    ]); 
	   
	   return response()->json(['success'=>__('Saved successfully')]);
	   }else{
		   $c = DB::table('tbl_lab_tests')->where('id','<>',$id)->where('category_num',$request->category_num)
		                                  ->where('is_group','Y')->where('test_code',$code)->first();
		  if(isset($c) && isset($c->test_code) && $c->test_code !=''){
			  return response()->json(['error'=>'Group code already exists for same category']);
		  }
		   
		   DB::table('tbl_lab_tests')->where('id',$id)->update([
		     'category_num'=>$request->category_num,
			 'test_code'=>$request->code,
			 'test_name'=>$request->descrip,
			 'cnss'=>$request->cnss,
			 'nbl'=>$request->nbl,
			 'price'=>$request->price,
			 'testord'=>$request->testord,
			 'descrip'=>$request->description,
			 'test_rq'=>$request->test_rq,
			 'unit'=>$request->unit,
			 'normal_value'=>$request->normal_value,
			 'referred_tests'=>$request->referred_tests,
			 'preanalytical'=>$request->preanalytical,
			 'storage'=>$request->storage,
			 'transport'=>$request->transport,
			 'tat_hrs'=>$request->tat_hrs,
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
		LabTests::where('group_num',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
		LabTests::where('id',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('InActivated successfully')]);	
		}
		
		if($checked=='O'){
	    LabTests::where('group_num',$id)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
		LabTests::where('id',$id)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('Activated successfully')]);		
		}
			
    }
}
