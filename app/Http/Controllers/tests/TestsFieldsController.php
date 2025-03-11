<?php

namespace App\Http\Controllers\tests;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\LabTestFields;
use App\Models\LabTests;
use App\Models\Doctor;
use App\Models\Clinic;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Alert;

class TestsFieldsController extends Controller
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
			  $my_labs = Clinic::where('id',$user_clinic_num)->get();
			  $tests = LabTests::where('clinic_num',$user_clinic_num)->where('is_group','<>','Y')->where('active','Y')->orderBy('id','desc')->get();
		  
	
	  if($request->ajax()){
		  $filter_status='';
       
		   if(isset($request->filter_status) && $request->filter_status!=''){
			   $filter_status = $request->filter_status;
		   } 

           $filter_lab='';
       
		   if(isset($request->filter_lab) && $request->filter_lab!=''){
			   $filter_lab = $request->filter_lab;
		   } 

           $filter_test='';
       
		   if(isset($request->filter_test) && $request->filter_test!=''){
			   $filter_test = $request->filter_test;
		   }  	    	   	   
	   
	    $fields = DB::table('tbl_lab_tests_fields as f')
		                ->select('f.id','f.gender','f.field_order',
						     DB::raw("IF(f.mytype='D' and f.tage=365,concat(f.fage,' ','D','-',1,' ','Y'),
							           IF((f.fage='' or f.fage='0' or f.fage IS NULL) and (f.tage!='' and f.tage!='0' and f.tage IS NOT NULL),concat('<',' ',f.tage,' ',f.mytype),
									      IF((f.fage!='' and f.fage!='0' and f.fage IS NOT NULL) and (f.tage='' or f.tage='0' or f.tage IS NULL),concat('>=',' ',f.fage,' ',f.mytype),concat(f.fage,' ',f.mytype,'-',f.tage,' ',f.mytype))
										  )
										   ) as age_range"),
		                      'f.rtype','f.active','f.descrip',
							  DB::raw("concat(f.panic_low_value,'-',f.panic_high_value) as panic_range"),
							  DB::raw("IF(f.sign_min IS NOT NULL and f.sign_min<>'',concat(f.normal_value1,'-',f.sign_min),f.normal_value1) as min"),
							  DB::raw("IF(f.sign_max IS NOT NULL and f.sign_max<>'',concat(f.normal_value2,'-',f.sign_max),f.normal_value2) as max"),
							  DB::raw("IF(t.test_code IS NULL or t.test_code='',t.test_name,concat(t.test_name,' ','(',' ',t.test_code,' ',')')) as test_name"),
							  'f.unit','f.remark','t.group_num')
						->join('tbl_lab_tests as t','t.id','f.test_id');
						
                       
						
                        
			if($filter_lab !=''){
				$fields = $fields->where('f.clinic_num',$filter_lab);
			}			
			if($filter_test !=''){			
                 $fields =  $fields->where('f.test_id',$filter_test);
			}
           if($filter_status =='Y'){
				$fields = $fields->where('f.active',$filter_status);
				             
			    }
			if($filter_status =='N'){
				$fields = $fields->where('f.active','N')->orWhereNULL('f.active');	
				}		            
						
		  
        
		return Datatables::of($fields)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                           $checked=($row->active=='Y')?'checked':'';
						   $disabled=($row->active=='Y')?'':'disabled';
						   $btn = '<button type="button"  class="btn btn-icon btn-md" onclick="editData('.$row->id.')" '.$disabled.'><i class="far fa-edit text-primary" title="'.__('Edit').'"></i></button>';
                           $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';
						   return $btn;
                    })
					->addColumn('group_name', function($row){
						$group= DB::table('tbl_lab_tests')->find($row->group_num);
						if(isset($group)){
							return $group->test_name;
						}else{
							return "";
						}
					})
					->filterColumn('gender',function($query,$keyword){
						$sql="IF(f.gender='M','Male',IF(f.gender='F','Female',IF(f.gender='B','Both',''))) like ?";
						$query->whereRaw($sql, ["%{$keyword}%"]);
					})
					->filterColumn('panic_range',function($query,$keyword){
						$sql="concat(f.panic_low_value,'-',f.panic_high_value) like ?";
						$query->whereRaw($sql, ["%{$keyword}%"]);
					})
					->filterColumn('min',function($query,$keyword){
						$sql="IF(f.sign_min IS NOT NULL and f.sign_min<>'',concat(f.normal_value1,'-',f.sign_min),f.normal_value1) like ?";
						$query->whereRaw($sql, ["%{$keyword}%"]);
					})
					->filterColumn('max',function($query,$keyword){
						$sql="IF(f.sign_max IS NOT NULL and f.sign_max<>'',concat(f.normal_value2,'-',f.sign_max),f.normal_value2) like ?";
						$query->whereRaw($sql, ["%{$keyword}%"]);
					})
					->filterColumn('test_name',function($query,$keyword){
						$sql="IF(t.test_code IS NULL,t.test_name,concat(t.test_name,' ','(',' ',t.test_code,' ',')')) like ?";
						$query->whereRaw($sql, ["%{$keyword}%"]);
					})
					->filterColumn('age_range',function($query,$keyword){
						$sql="concat(f.fage,'-',f.tage,' ',IF(f.mytype='D','Day',IF(f.mytype='W','Week',IF(f.mytype='M','Month','Year')))) like ?";
						$query->whereRaw($sql, ["%{$keyword}%"]);
					})
                    ->rawColumns(['action'])
                    ->make(true);
	     
		 }

        switch($user_type){
			case 1:
			return view('tests.fields.index')->with(['my_labs'=>$my_labs]);
			break;
			case 2:
			return view('tests.fields.index')->with(['my_labs'=>$my_labs,'tests'=>$tests]);
			break;
		}
    }

    
	public function filter_code($lang,Request $request){
		if($request->ajax()){
		   $filter_lab = $request->filter_lab;
		   $filter_test = $request->filter_test;
		   $fields = DB::table('tbl_lab_tests_fields as f')
		                ->select('f.id','f.field_order','f.test_id',
						         DB::raw("IF(f.mytype='D' and f.tage=365,concat(f.fage,' ','D','-',1,' ','Y'),
							           IF((f.fage='' or f.fage='0' or f.fage IS NULL) and (f.tage!='' and f.tage!='0' and f.tage IS NOT NULL),concat('<',' ',f.tage,' ',f.mytype),
									      IF((f.fage!='' and f.fage!='0' and f.fage IS NOT NULL) and (f.tage='' or f.tage='0' or f.tage IS NULL),concat('>=',' ',f.fage,' ',f.mytype),concat(f.fage,' ',f.mytype,'-',f.tage,' ',f.mytype))
										  )
										   ) as age_range"),
								 DB::raw("IF(f.is_comparison='Y','Yes','No') as is_comparison"),
								 DB::raw("IF(f.desirable_high='Y','Yes','No') as desirable_high"),
								 DB::raw("IF(f.desirable_low='Y','Yes','No') as desirable_low"),
								 DB::raw("IF(f.gender='M','Male',IF(f.gender='F','Female',IF(f.gender='B','Both',''))) as gender"),
							     DB::raw("IF(f.criteria='A','Age',IF(f.criteria='G','Gender',if(f.criteria='AG','Age and Gender','None'))) as criteria"),
		                         'f.rtype','f.active','f.descrip',
							     DB::raw("concat(f.panic_low_value,'-',f.panic_high_value) as panic_range"),
							     DB::raw("IF(f.sign_min IS NOT NULL and f.sign_min<>'',concat(f.normal_value1,'-',f.sign_min),f.normal_value1) as min"),
								 DB::raw("IF(f.sign_max IS NOT NULL and f.sign_max<>'',concat(f.normal_value2,'-',f.sign_max),f.normal_value2) as max"),
							     DB::raw("IF(t.test_code IS NULL or t.test_code='' ,t.test_name,concat(t.test_name,' ','(',' ',t.test_code,' ',')')) as test_name"),
							     'f.unit','f.remark')
						->join('tbl_lab_tests as t','t.id','f.test_id')
						->where('f.clinic_num',$filter_lab)
						->where('f.test_id',$filter_test)
						->where('f.active','Y')
						->orderBy('f.field_order')
						->get();
		      
			return response()->json($fields);
			
			
		}
	}
	
	/**
     * Get test field info.
     *
     * @return \Illuminate\View\View
     */
   public function get_info($lang,Request $request)
    {
		
	    $id = $request->id;
		$data = LabTestFields::find($id);
		return response()->json(['data'=>$data]);
	}
	
	/**
     * Get tests for lab in case doctor account.
     *
     * @return \Illuminate\View\View
     */
   public function get_tests_info($lang,Request $request)
    {
		
	    $lab_num = $request->lab_num;
		$tests = LabTests::where('clinic_num',$lab_num)->where('is_group','<>','Y')->where('active','Y')->orderBy('id','desc')->get();
		$html = '<option value="">'.__("Choose a code").'</option>';
		foreach($tests as $t){
			$name =$t->test_name;
			if(isset($t->test_code) && $t->test_code !=''){
				$name.=' ( '.$t->test_code.' )';
			}
			$html.='<option value="'.$t->id.'">'.$name.'</option>';
		}
		return response()->json(['html'=>$html]);
	}

    /**
     * Store a newly created resource in storage.
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
	   
	   $test = LabTests::find($request->test_id);
	   
	   $is_comparison = ($request->is_comparison=='on')?'Y':'N';
	   $desirable_high = ($request->desirable_high=='on')?'Y':'N';
	   $desirable_low = ($request->desirable_low=='on')?'Y':'N';
	   
	   
	   
	   if($id=='0'){
		  
		  if(isset($request->field_order) && $request->field_order!=''){
		  $order_exits = LabTestFields::where('active','Y')->where('test_id',$request->test_id)->where('field_order',$request->field_order)->get()->count();
		   if($order_exits){
			   return response()->json(['error'=>__('Order already exists for another field')]);
		   }
	      }
	   
		  LabTestFields::create([
		     'descrip'=>$request->descrip,
			 'fage'=>$request->fage,
			 'tage'=>$request->tage,
			 'gender'=>$request->gender,
			 'normal_value1'=>$request->normal_value1,
			 'normal_value2'=>$request->normal_value2,
			 'mytype'=>$request->mytype,
			 'test_id'=>$request->test_id,
			 'test_code'=>isset($test)?$test->test_code:NULL,
			 'user_num'=>$user_id,
			 'clinic_num'=>$user_clinic_num,
			 'panic_low_value'=>$request->panic_low_value,
			 'panic_high_value'=>$request->panic_high_value,
			 'unit'=>$request->unit,
			 'remark'=>$request->remark,
			 'is_comparison'=>$is_comparison,
			 'desirable_high'=>$desirable_high,
			 'desirable_low'=>$desirable_low,
			 'field_order'=>$request->field_order,
			 'sign_min'=>$request->sign_min,
			 'sign_max'=>$request->sign_max,
			 'sign'=>$request->sign,
			 'active'=>'Y'
		    ]); 
	   
	   LabTestFields::where('test_id',$request->test_id)->update(['criteria'=>$request->criteria]);
	   
	   return response()->json(['success'=>__('Saved successfully')]);
	   }else{
		    if(isset($request->field_order) && $request->field_order!=''){
			   $order_exits = LabTestFields::where('id','<>',$id)->where('active','Y')->where('test_id',$request->test_id)->where('field_order',$request->field_order)->get()->count();
			   if($order_exits){
				   return response()->json(['error'=>__('Order already exists for another field')]);
			   }
			}
		   LabTestFields::where('id',$id)->update([
		     'descrip'=>$request->descrip,
			 'fage'=>$request->fage,
			 'tage'=>$request->tage,
			 'gender'=>$request->gender,
			 'normal_value1'=>$request->normal_value1,
			 'normal_value2'=>$request->normal_value2,
			 'mytype'=>$request->mytype,
			 'panic_low_value'=>$request->panic_low_value,
			 'panic_high_value'=>$request->panic_high_value,
			 'unit'=>$request->unit,
			 'remark'=>$request->remark,
			 'is_comparison'=>$is_comparison,
			 'desirable_high'=>$desirable_high,
			 'desirable_low'=>$desirable_low,
			 'field_order'=>$request->field_order,
			 'sign_min'=>$request->sign_min,
			 'sign_max'=>$request->sign_max,
			 'sign'=>$request->sign,
			 'user_num'=>$user_id
			   ]);
		
		  LabTestFields::where('test_id',$request->test_id)->update(['criteria'=>$request->criteria]);
	   	 return response()->json(['success'=>__('Updated successfully')]);	

	   }		
		
	}
    

    

    public function chkFieldOrder($lang,Request $request){
		$id = $request->id;
		$field_order = $request->field_order;
		$test_id = $request->test_id;
		$db_field_order = LabTestFields::where('id',$id)->value('field_order'); 
		if($db_field_order!=$field_order){ 
		 if(isset($request->field_order) && $request->field_order!=''){
			$order_exits = LabTestFields::where('id','<>',$id)->where('active','Y')->where('test_id',$test_id)->where('field_order',$field_order)->where('field_order','<>','')->whereNotNull('field_order')->get()->count();
		    if($order_exits){
				   return response()->json(['error'=>__('Order already exists for another field')]);
			   } 
		 }
	     		
		$user_id = auth()->user()->id;
		LabTestFields::where('id',$id)->update(['field_order'=>$field_order,'user_num'=>$user_id]);
		return response()->json(['success'=>__('Field order updated successfully')]);
       }else{
		   return response()->json(['success'=>__('Nothing to update')]);
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
		
		LabTestFields::where('id',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('InActivated successfully')]);	
		}
		
		if($checked=='O'){
		LabTestFields::where('id',$id)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('Activated successfully')]);		
		}
			
    }
	
  public function inactiveFields($lang,Request $request){
	  $test_id = $request->test_id;
	  $type = $request->type;
	  switch($type){
	    case 'inactive':
		   LabTestFields::where('test_id',$test_id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
	       return response()->json(['success'=>__('All normal values are inactive')]);
		break;
		case 'inactive_field':
		 $field_id =$request->id;
		 LabTestFields::where('id',$field_id)->update(['user_num'=>auth()->user()->id,'active'=>'N']);
		 return response()->json(['success'=>__('This field is deleted successfully')]);
		break;
	   } 		
  }	
}
