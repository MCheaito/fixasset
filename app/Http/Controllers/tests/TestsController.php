<?php

namespace App\Http\Controllers\tests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\LabTests;
use App\Models\LabTestFields;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\ExtLab;
use App\Models\ExtIns;

use DB;
use DataTables;
use Alert;
use UserHelper;


class TestsController extends Controller
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
		  $groups = DB::table('tbl_lab_tests')
		            ->where(function($q){
						$q->whereRaw('is_group=?','Y')
						  ->orWhereRaw('is_group<>? and cnss IS NOT NULL','Y');
						 })
					->orderBy('id','desc')->get();
		  $categories = DB::table('tbl_lab_categories')->where('active','Y')->orderBy('id','desc')->get();
          $ext_labs = ExtLab::where('status','A')->where('clinic_num',$user_clinic_num)->orderBy('id','desc')->get();
		 
	
	  if($request->ajax()){
		  $filter_status='';
       
		   if(isset($request->filter_status) && $request->filter_status!=''){
			   $filter_status = $request->filter_status;
		   } 

           $filter_lab='';
       
		   if(isset($request->filter_lab) && $request->filter_lab!=''){
			   $filter_lab = $request->filter_lab;
		   }

           $filter_group='';
       
		   if(isset($request->filter_group) && $request->filter_group!=''){
			   $filter_group = $request->filter_group;
		   } 
         
		 $filter_cat='';
       
		   if(isset($request->filter_cat) && $request->filter_cat!=''){
			   $filter_cat = $request->filter_cat;
		   }

        
	    $data = DB::table('tbl_lab_tests as t')
		            ->select('t.id','t.test_name','t.test_code','t.is_group','t.is_culture',
					         't.price','t.descrip','t.active','t.normal_value','t.test_rq','t.unit','t.cnss',
							 't.nbl','t.listcode','t.testord','t.test_type',
					         'cat.descrip as cat_name',
							 DB::raw("IFNULL(ext.full_name,'') as referred_lab")
							 )
					->leftjoin('tbl_referred_labs as ext','ext.id','t.referred_tests')
					->leftjoin('tbl_lab_categories as cat',function($q){
					            $q->On('cat.id','=','t.category_num');
							    $q->where('cat.active','=','Y');
					})->where(function($q){
						$q->whereRaw('t.is_group=?','Y')
						  ->orWhereRaw('t.is_group<>? and t.cnss IS NOT NULL','Y');
						});
              
			  if($filter_cat !=''){
				$data = $data->where('cat.id',$filter_cat);
  
			  }
			  
			 if(isset($request->filter_type) && $request->filter_type=='G'){
			   $data = $data->where('t.is_group','Y');
		       }
            
			if(isset($request->filter_type) && $request->filter_type=='NG'){
		 	   $data = $data->whereRaw('t.is_group<>? and t.cnss IS NOT NULL','Y');
		      }

			 if(isset($request->filter_type) && $request->filter_type=='CULT'){
		 	   $data = $data->whereRaw('t.is_culture=?','Y');
		      } 	 
           	
			 if($filter_group !=''){
				$data = $data->where('t.id',$filter_group);

			  }	 
             
			 if($filter_lab !=''){
			   $data = $data->where('t.clinic_num',$filter_lab);
 
			 }

            if($filter_status =='Y'){
				$data = $data->where('t.active',$filter_status);
			  }else{
			     if($filter_status =='N'){
				   $data = $data->where('t.active','N');	
				  }
				}				
		
		   //$data = $data->orderBy('t.id','desc')->orderBy('t.testord');
		
		
		return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) use($lang){
                           $checked=($row->active=='Y')?'checked':'';
						   $disabled=($row->active=='Y')?'':'disabled';
						   $edit_url = route('lab.tests.edit',[$lang,$row->id]);
						   $btn = '<a  href="'.$edit_url.'" class="btn btn-icon btn-md '.$disabled.'"><i class="far fa-edit text-primary" title="'.__('Edit').'"></i></a>';
                           $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';
						   return $btn;
                    })
					->editColumn('testord', function ($row) {
                      return '<input type="number" style="width:80px;max-width:100%;" class="order-input" value="' . $row->testord . '" data-id="' . $row->id . '" min="1" />';
                      })
					->filterColumn('testord', function($query, $keyword) {
                          $query->whereRaw('CAST("testord" AS CHAR) LIKE ?', ["%{$keyword}%"]);
                       })
                    ->editColumn('test_code', function ($row) {
                      return '<input type="text" style="width:140px;max-width:100%;" class="test-code-input" value="' . $row->test_code . '" data-id="' . $row->id . '" />';
                      })
					->filterColumn('test_coded', function($query, $keyword) {
                          $query->whereRaw('test_code LIKE ?', ["%{$keyword}%"]);
                       })  					   
					->filterColumn('is_group', function($query, $keyword) {
                       $sql = "IF(is_group='Y','".__('Group')."','".__('Not_Group')."')  LIKE ?";
                       $query->whereRaw($sql, ["%{$keyword}%"]);
                     })
                    ->rawColumns(['action','testord','test_code'])
                    ->make(true);
	     
		 }

        return view('tests.index')
		      ->with(['ext_labs'=>$ext_labs,'my_labs'=>$my_labs,'groups'=>$groups,
			          'categories'=>$categories]);
    }

    public function new($lang){
		$user_clinic_num=auth()->user()->clinic_num;
		$sub_groups=collect();
		$categories = DB::table('tbl_lab_categories')->where('active','Y')->orderBy('id','desc')->get();
        $ext_labs = ExtIns::where('status','Y')->where('clinic_num',$user_clinic_num)->orderBy('id','desc')->get();
		$specimen = DB::table('tbl_lab_specimen')->where('status','Y')->orderBy('specimen_order')->get();
		$spec_cons = DB::table('tbl_lab_special_considerations')->where('status','Y')->orderBy('id','desc')->get();
		$text_results = collect();
		$gram_stain_results = collect();
	    return view('tests.code')->with(['sub_groups'=>$sub_groups,'categories'=>$categories,'text_results'=>$text_results,
		                                 'ext_labs'=>$ext_labs,'specimen'=>$specimen,'spec_cons'=>$spec_cons,'gram_stain_results'=>$gram_stain_results]);
	}
	
	public function cancel($lang,Request $request){
		$id = $request->id;
		$sub_grps = LabTests::where('group_num',$id)->where('active','Y')->orderBy('testord')->get();
		$html = '';
		$cnt =0;
		foreach($sub_grps as $s){
			$cnt++;
			$formula_select='';
			if($s->test_type=="F"){
				$formula_select = 'selected';
			}
			$calc_select='';
			if($s->test_type=="C"){
				$calc_select = 'selected';
			}
			$printed = $s->is_printed=='Y'?'checked':'';
			$html.='<tr><td style="display:none;">'.$cnt.'</td>';
			$html.='<td><input type="number" min="0" class="form-control" value="'.$s->testord.'" onkeypress="return isNumberKey(event)" style="width:60px;"/></td>';
			$html.='<td><input type="text" class="form-control" value="'.$s->test_name.'" style="width:220px;"/></td>';
			$html.='<td><input type="text" class="form-control" value="'.$s->test_code.'" style="width:80px;"/></td>';
			$html.='<td><input type="text" class="form-control" value="'.$s->normal_value.'" style="width:130px;"/></td>';
			$html.='<td><input type="text" class="form-control" value="'.$s->listcode.'" style="width:150px;"/></td>';
			$subgroup_result = UserHelper::getSubgroupResults($s->id);
			$html.='<td class="select2-primary"><select  name="subgroup_result_text" class="subgroup_rslt  custom-select rounded-0" data-dropdown-css-class="select2-primary" data-placeholder="'.__('Choose a text result').'" multiple="multiple" style="min-width:250px;">';
			foreach($subgroup_result as $g){
			$html.='<option value="'.$g->id.'" selected>'.$g->name.'</option>';
			}
			$html.='</select></td>';
			$selected = $s->test_type=="F"?"selected":"";
			$html.='<td><select name="test_type" id="test_type" class="form-control" style="width:100px;"><option value="">'.__('Normal').'</option><option value="F" '.$selected.'>'.__('Formula').'</option></select></td>';
			$checked = $s->is_printed=='Y'?'checked':'';
			$html.='<td><label class="mt-2 slideon slideon-xs slideon-success"><input  type="checkbox" '.$checked.' style="width:50px;"/><span class="slideon-slider"></span></label></td>';
			$checked = $s->is_valid=='Y'?'checked':'';
			$html.='<td><label class="mt-2 slideon slideon-xs slideon-success"><input  type="checkbox" '.$checked.' style="width:50px;"/><span class="slideon-slider"></span></label></td>';
			$checked = $s->is_title=='Y'?'checked':'';
			$html.='<td><label class="mt-2 slideon slideon-xs slideon-success"><input  type="checkbox" '.$checked.' onchange="event.preventDefault();disableIns(this);" style="width:50px;"/><span class="slideon-slider"></span></label></td>';
			$disabled = $s->is_title=='Y'?'disabled':'';
			$html.='<td style="white-space:nowrap;"><button class="btn btn-action btn-xs" title="'.__('Insert Fields').'" '.$disabled.' onclick="event.preventDefault();insField(this,'.$s->id.');"><i class="fa fa-plus"></i></button><button class="ml-1 btn btn-delete btn-xs" title="'.__('Delete').'" onclick="event.preventDefault();deleteRow(this,'.$s->id.');"><i class="fa fa-trash"></i></button></td>';
			$html.='<td style="display:none;">'.$s->id.'</td></tr>';
		}
	
	  return response()->json(['html'=>$html]);
	}
	
	/**
     * Get test lab info.
     *
     * @return \Illuminate\View\View
     */
   public function get_info($lang,Request $request)
    {
		
	    $id = $request->id;
		$data = LabTests::find($id);
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
	   //dd("save");
	   $user_id=auth()->user()->id;
	   $user_clinic_num=auth()->user()->clinic_num;
	   $code = $request->test_code;
	   $cnss = $request->cnss;
	   $test_name = $request->test_name;
       //group_num=NULL means a group or a test not sub-group
       $name_exists = LabTests::where('active','Y')->whereNULL('group_num')->where(DB::raw('lower(trim(test_name))'),strtolower(trim($request->test_name)))->get()->count();
       if($name_exists){
		   return response()->json(['warning'=>__('Name already exists for another test')]);
	   }

       if(isset($request->testord) && $request->testord!=''){
		   $testord_exists = LabTests::where('active','Y')->whereNULL('group_num')->where('category_num',$request->category_num)->where('testord',$request->testord)->get()->count();
		   
		   if($testord_exists){
			   return response()->json(['warning'=>__('Order already exists for another test in the same category')]);
		   }
	   }	   
		  
		  $tst_id = LabTests::create([
		     'test_code'=>$request->test_code,
			 'test_name'=>trim($request->test_name),
			 'test_rq'=>$request->test_rq,
			 'category_num'=>$request->category_num,
			 'testord'=>$request->testord,
			 'cnss'=>$request->cnss,
			 'listcode'=>$request->listcode,
			 'nbl'=>$request->nbl,
			 'normal_value'=>$request->normal_value,
			 'price'=>$request->price,
			 'descrip'=>$request->description,
			 'user_num'=>$user_id,
			 'clinic_num'=>$user_clinic_num,
			 'test_type'=>$request->test_type,
			 'referred_tests'=>$request->referred_tests,
			 'preanalytical'=>$request->preanalytical,
			 'storage'=>$request->storage,
			 'transport'=>$request->transport,
			 'tat_hrs'=>$request->tat_hrs,
			 'is_group'=>$request->is_group,
			 'is_printed'=>$request->is_printed,
			 'custom_test'=>$request->custom_test,
			 'dec_pts'=>$request->dec_pts,
			 'clinical_remark'=>$request->clinical_remark,
			 'specimen'=>$request->specimen,
			 'special_considerations'=>$request->special_considerations,
			 'is_culture'=>$request->is_culture,
			 'is_valid'=>$request->is_valid,
			 'active'=>'Y'
		     ])->id; 
	   
	   
	   if(null !==$request->input('result_text')){
	    //delete all result texts for tests
        DB::table('tbl_lab_text_results')->where('test_id',$tst_id)->delete();
	    if(!empty($request->input('result_text'))){
		   foreach($request->input('result_text') as $d){
               //add non existing tests for this code
			   DB::table('tbl_lab_text_results')->insert(['user_num'=>auth()->user()->id,'status'=>'Y','name'=>$d,'name_fr'=>$d,'test_id'=>$tst_id]);
			  
		     }
	        }
	      }else{
			  //delete all result texts for tests
        DB::table('tbl_lab_text_results')->where('test_id',$tst_id)->delete();
		  } 
		 
		 if(null !==$request->input('gram_stain_results')){
		 //delete all gram staim results for tests 
         DB::table('tbl_lab_gram_stain_results')->where('test_id',$tst_id)->delete();
	     if(!empty($request->input('gram_stain_results'))){
		   foreach($request->input('gram_stain_results') as $d){
			   DB::table('tbl_lab_gram_stain_results')->insert(['user_num'=>auth()->user()->id,'status'=>'Y','name'=>$d,'name_fr'=>$d,'test_id'=>$tst_id]);
			   }
	        }
		  }else{
			//delete all gram staim results for tests 
            DB::table('tbl_lab_gram_stain_results')->where('test_id',$tst_id)->delete();  
		  }
	   
	   //No need to save it in code since result text has code it with value
	   /*if(!empty($result_ids)){
	     LabTests::where('id',$tst_id)->update(['result_text'=>$result_ids]);
	   }else{
		 LabTests::where('id',$tst_id)->update(['result_text'=>NULL]);  
	   }*/
	      
	   if($request->is_group=='Y' && isset($request->data)){
		  $grp = LabTests::find($tst_id);
		  $arr = json_decode($request->data,true);
		  foreach ( $arr as $key=>$val){
			  $test_name = trim($val["name"]);
			  $test_code = isset($val["code"]) && $val["code"]!=''?trim($val["code"]):NULL;
			  $normal_value=isset($val["normalvalue"]) && $val["normalvalue"]!=''?trim($val["normalvalue"]):NULL;
			  $listcode=isset($val["liscode"]) && $val["liscode"]!=''?trim($val["liscode"]):NULL;
			  $testord=isset($val["order"]) && $val["order"]!=''?trim($val["order"]):0;
			  $test_type = isset($val["type"]) && $val["type"]!=''?trim($val["type"]):'';
			  $is_printed = trim($val["isPrinted"]);
			  $is_valid = trim($val["isValid"]);
			  $group_num = $grp->id;
			  $category_num=$grp->category_num;
			  $referred_tests = isset($grp->referred_tests) && $grp->referred_tests!=""?$grp->referred_tests:NULL;
			  $is_title = trim($val["isTitle"]);
			  
			  $test_num = LabTests::create([
			  'testord'=>$testord,
			  'test_code'=>$test_code,
			  'test_name'=>$test_name,
			  'group_num'=>$group_num,
			  'category_num'=>$category_num,
			  'listcode'=>$listcode,
			  'normal_value'=>$normal_value,
			  'is_group'=>'N',
			  'is_printed'=>$is_printed,
			  'test_type'=>$test_type,
			  'referred_tests'=>$referred_tests,
			  'dec_pts'=>$grp->dec_pts,
			  'user_num'=>$user_id,
			  'clinic_num'=>$user_clinic_num,
			  'is_valid'=>$is_valid,
			  'is_title'=>$is_title,
			  'active'=>'Y'
			  ])->id;
		   
		      $rslts = $val["rslt"];
				//delete all subgroup result texts for tests
				DB::table('tbl_lab_text_results')->where('test_id',$test_num)->delete();
				if(!empty($rslts)){
				   foreach($rslts as $d){
					   //add non existing tests for this code
					   DB::table('tbl_lab_text_results')->insert(['user_num'=>auth()->user()->id,'status'=>'Y','name'=>$d,'name_fr'=>$d,'test_id'=>$test_num]);
					  
					 }
					}
		   
		   
		   }
	   }
	   
	   
	   $location = route('lab.tests.edit',[$lang,$tst_id]);
	   
	   return response()->json(['success'=>__('Saved successfully'),'location'=>$location]);
	   
		
	}
	
	public function edit($lang,$id){
		$test = LabTests::find($id);
		
		
		$sub_groups=collect();
		if($test->is_group=='Y'){
			$sub_groups = LabTests::where('group_num',$test->id)->where('active','Y')->orderBy('testord')->get();
			//dd($sub_groups);
		}
		$specimen = DB::table('tbl_lab_specimen')->where('status','Y')->orderBy('specimen_order')->get();
		$spec_cons = DB::table('tbl_lab_special_considerations')->where('status','Y')->orderBy('id','desc')->get();
		$user_clinic_num=auth()->user()->clinic_num;
		$categories = DB::table('tbl_lab_categories')->where('active','Y')->orderBy('id','desc')->get();
        $ext_labs = ExtIns::where('status','Y')->where('clinic_num',$user_clinic_num)->orderBy('id','desc')->get();
	    $text_results = DB::table('tbl_lab_text_results')->where('test_id',$id)->where('status','Y')->orderBy('id','desc')->get();
	    $gram_stain_results = DB::table('tbl_lab_gram_stain_results')->where('test_id',$id)->where('status','Y')->orderBy('id','desc')->get();

		return view('tests.code')->with(['test'=>$test,'sub_groups'=>$sub_groups,'categories'=>$categories,'text_results'=>$text_results,
		                                 'ext_labs'=>$ext_labs,'specimen'=>$specimen,'spec_cons'=>$spec_cons,
										 'gram_stain_results'=>$gram_stain_results]);
	}
	
	 public function update($lang,Request $request)
    {
	   //dd("HI");
	   
	   $user_id=auth()->user()->id;
	   $user_clinic_num=auth()->user()->clinic_num;
	   $code = $request->test_code;
	   $cnss = $request->cnss;
	   $test_name = $request->test_name;
	   $id = $request->id;
	   
	   $name_exists = LabTests::where('active','Y')->whereNULL('group_num')->where('id','<>',$id)->where(DB::raw('lower(trim(test_name))'),strtolower(trim($request->test_name)))->get()->count();
        if($name_exists){
		   return response()->json(['warning'=>__('Name already exists for another test')]);
	    }

        if(isset($request->testord) && $request->testord!=''){
		   $testord_exists = LabTests::where('active','Y')->whereNULL('group_num')->where('category_num',$request->category_num)->where('id','<>',$id)->where('testord',$request->testord)->get()->count();
		   //dd($testord_exists);
		   if($testord_exists){
			   return response()->json(['warning'=>__('Order already exists for another test in same category')]);
		   }
	     }	 
		
  
		  LabTests::where('id',$id)->update([
		     'is_group'=>$request->is_group,
			 'custom_test'=>$request->custom_test,
			 'test_code'=>$request->test_code,
			 'test_name'=>trim($request->test_name),
			 'test_rq'=>$request->test_rq,
			 'category_num'=>$request->category_num,
			 'testord'=>$request->testord,
			 'cnss'=>$request->cnss,
			 'listcode'=>$request->listcode,
			 'nbl'=>$request->nbl,
			 'normal_value'=>$request->normal_value,
			 'price'=>$request->price,
			 'descrip'=>$request->description,
			 'user_num'=>$user_id,
			 'clinic_num'=>$user_clinic_num,
			 'test_type'=>$request->test_type,
			 'referred_tests'=>$request->referred_tests,
			 'preanalytical'=>$request->preanalytical,
			 'storage'=>$request->storage,
			 'transport'=>$request->transport,
			 'tat_hrs'=>$request->tat_hrs,
			 'clinical_remark'=>$request->clinical_remark,
			 'specimen'=>$request->specimen,
			 'special_considerations'=>$request->special_considerations,
			 'dec_pts'=>$request->dec_pts,
			 'is_culture'=>$request->is_culture,
			 'is_valid'=>$request->is_valid,
			 'is_printed'=>$request->is_printed
			  ]);

        if(null !==$request->input('result_text')){
	     //delete all result texts for tests
         DB::table('tbl_lab_text_results')->where('test_id',$id)->delete();	
	     if(!empty($request->input('result_text'))){
		   foreach($request->input('result_text') as $d){
               //add non existing tests for this code
			   
			      DB::table('tbl_lab_text_results')->insert(['user_num'=>auth()->user()->id,'status'=>'Y','name'=>$d,'name_fr'=>$d,'test_id'=>$id]);
				
		     }
	        }
	     }else{
			//delete all result texts for tests
           DB::table('tbl_lab_text_results')->where('test_id',$id)->delete();	 
		 } 
		 
		 if(null !==$request->input('gram_stain_results')){
		        //delete all gram staim results for tests 
                DB::table('tbl_lab_gram_stain_results')->where('test_id',$id)->delete();
	       if(!empty($request->input('gram_stain_results'))){
		      foreach($request->input('gram_stain_results') as $d){
			   	  DB::table('tbl_lab_gram_stain_results')->insert(['user_num'=>auth()->user()->id,'status'=>'Y','name'=>$d,'name_fr'=>$d,'test_id'=>$id]);
				
			   }
	        }
		  }else{
			 //delete all gram staim results for tests 
                DB::table('tbl_lab_gram_stain_results')->where('test_id',$id)->delete(); 
		  }
	   
	   if($request->is_group=='Y' && isset($request->data)){
		   $grp = LabTests::find($id);
		  //delete old linked tests that are deleted in interface
		  $subgrps = LabTests::where('group_num',$grp->id)->pluck('id')->toArray();
          $sub_ids = isset($request->sub_ids)?json_decode($request->sub_ids):array();
	      $not_found= array();
	      if(count($subgrps)){
		   $not_found = array_diff($subgrps,$sub_ids);
		   LabTests::where('group_num',$grp->id)->whereIn('id',$not_found)->update(['active'=>'N','user_num'=>$user_id]);
	      }
	   		  
		  $arr = json_decode($request->data,true);
		  
		  foreach ( $arr as $key=>$val){
			  
			  $test_name = trim($val["name"]);
			  $test_code = isset($val["code"]) && $val["code"]!=''?trim($val["code"]):NULL;
			  $normal_value=isset($val["normalvalue"]) && $val["normalvalue"]!=''?trim($val["normalvalue"]):NULL;
			  $listcode=isset($val["liscode"]) && $val["liscode"]!=''?trim($val["liscode"]):NULL;
			  $testord=isset($val["order"]) && $val["order"]!=''?trim($val["order"]):0;
			  $test_type = isset($val["type"]) && $val["type"]!=''?trim($val["type"]):'';
			  $is_printed = trim($val["isPrinted"]);
			  $group_num = $grp->id;
			  $category_num=$grp->category_num;
			  $referred_tests = isset($grp->referred_tests) && $grp->referred_tests!=""?$grp->referred_tests:NULL;
			  $subgroup_id = intval($val["subgroupID"]);
			  $is_valid = trim($val["isValid"]);
			  $is_title = trim($val["isTitle"]);
			  
			  if(isset($subgroup_id) && $subgroup_id!=''){
				  $test_num = $subgroup_id;
				  LabTests::where('id',$subgroup_id)
				  ->update([
				  'testord'=>$testord,
				  'test_code'=>$test_code,
				  'test_name'=>$test_name,
				  'listcode'=>$listcode,
				  'normal_value'=>$normal_value,
				  'is_group'=>'N',
				  'is_printed'=>$is_printed,
				  'category_num'=>$category_num,
				  'test_type'=>$test_type,
				  'dec_pts'=>$grp->dec_pts,
				  'referred_tests'=>$referred_tests,
				  'is_valid'=>$is_valid,
				  'is_title'=>$is_title,
				  'user_num'=>$user_id
				   ]);

			  }else{
				  $test_num = LabTests::Create([
				  'testord'=>$testord,
				  'test_code'=>$test_code,
				  'test_name'=>$test_name,
				  'group_num'=>$group_num,
				  'category_num'=>$category_num,
				  'listcode'=>$listcode,
				  'normal_value'=>$normal_value,
				  'is_group'=>'N',
				  'is_printed'=>$is_printed,
				  'test_type'=>$test_type,
				  'referred_tests'=>$referred_tests,
				  'user_num'=>$user_id,
				  'dec_pts'=>$grp->dec_pts,
				  'clinic_num'=>$user_clinic_num,
				  'is_valid'=>$is_valid,
				  'is_title'=>$is_title,
				  'active'=>'Y'
				  ])->id;
			  }
		   
		        $rslts = $val["rslt"];
				
				 $rslts = $val["rslt"];
				//delete all subgroup result texts for tests
				DB::table('tbl_lab_text_results')->where('test_id',$test_num)->delete();
				if(!empty($rslts)){
				   foreach($rslts as $d){
					   //add non existing tests for this code
					   DB::table('tbl_lab_text_results')->insert(['user_num'=>auth()->user()->id,'status'=>'Y','name'=>$d,'name_fr'=>$d,'test_id'=>$test_num]);
					  
					 }
					}
	              
		   
		   
		   }
	   }
	   
	   
	   $location = route('lab.tests.edit',[$lang,$id]);
	   
	   return response()->json(['success'=>__('Updated successfully'),'location'=>$location]);
	   
		
	}
	
	public function remove($lang,Request $request)
    {
	$id = $request->id;
	$tst = LabTests::find($id);
	$is_group=$tst->is_group;
		
	if($is_group=='Y'){
	 $test_fields = LabTests::where('group_num',$id)->pluck('id')->toArray();
	 
	 LabTests::where('group_num',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
	 LabTestFields::whereIn('test_id',$test_fields)->update(['active'=>'N','user_num'=>auth()->user()->id]);
	}else{
	 LabTestFields::where('test_id',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
	}
	LabTests::where('id',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
	$location = route('lab.tests.index',$lang);
	return response()->json(['success'=>__('InActivated successfully'),'location'=>$location]);
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
		
		$tst = LabTests::find($id);
		$is_group=$tst->is_group;
		
		if($checked=='N'){
		
		if($is_group=='Y'){
			$test_fields = LabTests::where('group_num',$id)->pluck('id')->toArray(); 
	        LabTests::where('group_num',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
	        LabTestFields::whereIn('test_id',$test_fields)->update(['active'=>'N','user_num'=>auth()->user()->id]);
		}else{
		    LabTestFields::where('test_id',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
		}
		LabTests::where('id',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);
		return response()->json(['success'=>__('InActivated successfully')]);	
		}
		
		if($checked=='O'){
		LabTests::where('id',$id)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
		if($is_group=='Y'){
			$test_fields = LabTests::where('group_num',$id)->pluck('id')->toArray(); 
	        LabTests::where('group_num',$id)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
	        LabTestFields::whereIn('test_id',$test_fields)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
		}else{
		    LabTestFields::where('test_id',$id)->update(['active'=>'Y','user_num'=>auth()->user()->id]);
		}
		return response()->json(['success'=>__('Activated successfully')]);		
		}
			
    }
	
  public function deleteRow	($lang,Request $request){
	  $id = $request->id;
	  $fields = LabTestFields::where('test_id',$id)->get();
	  //dd($fields->count());
	  if($fields->count()>0){
		 //remove the fields of this test
         LabTestFields::where('test_id',$id)->update(['active'=>'N','user_num'=>auth()->user()->id]);		 
	  }
	  return response()->json(['success'=>true]);
  }
  
public function specConsideration($lang,Request $request){
	switch($request->type){ 
	 case 'table':
	 $special_considerations=DB::table('tbl_lab_special_considerations')->orderBy('id','desc')->get();
     return response()->json($special_considerations);
	 break;
	 case 'new':
	   $name = trim($request->name);
	   $cnt = DB::table('tbl_lab_special_considerations')->where(DB::raw('lower(name)'),strtolower($name))->get()->count();
	   if($cnt){
		   return response()->json(['error'=>__('Name already exists')]);
	   }else{
	       DB::table('tbl_lab_special_considerations')->insert(['name'=>$name,'name_fr'=>$name,'status'=>'Y']);
		   $keys = DB::table('tbl_lab_special_considerations')->where('status','Y')->orderBy('id','desc')->pluck('id')->toArray();
		   $values=DB::table('tbl_lab_special_considerations')->where('status','Y')->orderBy('id','desc')->pluck('name')->toArray();
		   return response()->json(['keys'=>$keys,'values'=>$values,'success'=>__('Inserted successfully')]);
	   }
	 break;
	 case 'update':
	   $name = trim($request->name);
	   $id = $request->id;
	   $cnt = DB::table('tbl_lab_special_considerations')->where('id','<>',$id)->where(DB::raw('lower(name)'),strtolower($name))->get()->count();
	   if($cnt){
		   return response()->json(['error'=>__('Name already exists')]);
	   }else{
	       DB::table('tbl_lab_special_considerations')->where('id',$id)->update(['name'=>$name,'name_fr'=>$name]);
		   $keys = DB::table('tbl_lab_special_considerations')->where('status','Y')->orderBy('id','desc')->pluck('id')->toArray();
		   $values=DB::table('tbl_lab_special_considerations')->where('status','Y')->orderBy('id','desc')->pluck('name')->toArray();
		   return response()->json(['keys'=>$keys,'values'=>$values,'success'=>__('Updated successfully')]);
	   }
	 break;
	 case 'delete':
	   $checked = $request->checked;
	   DB::table('tbl_lab_special_considerations')->where('id',$request->id)->update(['status'=>$checked]);
	   if($checked=='Y'){
		$keys = DB::table('tbl_lab_special_considerations')->where('status','Y')->orderBy('id','desc')->pluck('id')->toArray();
		$values=DB::table('tbl_lab_special_considerations')->where('status','Y')->orderBy('id','desc')->pluck('name')->toArray();
		return response()->json(['keys'=>$keys,'values'=>$values,'active'=>true,'success'=>__('Activated successfully')]);
	   }else{
		return response()->json(['inactive'=>true,'success'=>__('Deactivated successfully')]);   
	   }
	 break;
	}	 
  }

public function textResult($lang,Request $request){
	$user_num = auth()->user()->id;
	
	switch($request->type){ 
	 case 'table':
	 $data=DB::table('tbl_lab_text_results')->where('test_id',$request->test_id)->where('status','Y')->orderBy('id','desc')->get();
     return response()->json($data);
	 break;
	 case 'new':
	   $name = trim($request->name);
	   $cnt = DB::table('tbl_lab_text_results')->where('test_id',$request->test_id)->where(DB::raw('lower(name)'),strtolower($name))->get()->count();
	   if($cnt){
		   return response()->json(['error'=>__('Result text already exists for this test')]);
	   }else{
	       DB::table('tbl_lab_text_results')->insert(['test_id'=>$request->test_id,'name'=>$name,'name_fr'=>$name,'status'=>'Y','user_num'=>$user_num]);
		   $keys = DB::table('tbl_lab_text_results')->where('test_id',$request->test_id)->where('status','Y')->orderBy('id','desc')->pluck('id')->toArray();
		   $values=DB::table('tbl_lab_text_results')->where('test_id',$request->test_id)->where('status','Y')->orderBy('id','desc')->pluck('name')->toArray();
		   return response()->json(['keys'=>$keys,'values'=>$values,'success'=>__('Inserted successfully')]);
	   }
	 break;
	 
	 case 'delete':
	   $checked = $request->checked;
	   DB::table('tbl_lab_text_results')->where('id',$request->id)->update(['status'=>'N','user_num'=>$user_num]);
	   return response()->json(['success'=>__('Deleted successfully')]);   
	  
	 break;
	}	 
 }

public function get_specimens($lang,Request $request){
	switch($request->type){ 
	 case 'table':
	 $specimens=DB::table('tbl_lab_specimen')->orderBy('specimen_order')->get();
     return response()->json($specimens);
	 break;
	 case 'new':
	   $name = trim($request->name);
	   $order = $request->order;
	   $cnt = DB::table('tbl_lab_specimen')->where(DB::raw('lower(name)'),strtolower($name))->get()->count();
	   if($cnt){
		   return response()->json(['error'=>__('Name already exists')]);
	   }else{
	       $cnt = DB::table('tbl_lab_specimen')->where('specimen_order', $order)->get()->count();
		   if($cnt){
		   return response()->json(['error'=>__('Order already exists')]);
		   }else{
		   DB::table('tbl_lab_specimen')->insert(['specimen_order'=> $order,'name'=>$name,'name_fr'=>$name,'status'=>'Y']);
		   $keys = DB::table('tbl_lab_specimen')->where('status','Y')->orderBy('specimen_order')->pluck('id')->toArray();
		   $values=DB::table('tbl_lab_specimen')->where('status','Y')->orderBy('specimen_order')->pluck('name')->toArray();
		   return response()->json(['keys'=>$keys,'values'=>$values,'success'=>__('Inserted successfully')]);
		   }
	   }
	 break;
	 case 'update':
	   $name = trim($request->name);
	   $id = $request->id;
	   $order = $request->order;
	   $cnt = DB::table('tbl_lab_specimen')->where('id','<>',$id)->where(DB::raw('lower(name)'),strtolower($name))->get()->count();
	   if($cnt){
		   return response()->json(['error'=>__('Name already exists')]);
	   }else{
	       
		   DB::table('tbl_lab_specimen')->where('id',$id)->update(['name'=>$name,'name_fr'=>$name]);
		   $keys = DB::table('tbl_lab_specimen')->where('status','Y')->orderBy('specimen_order')->pluck('id')->toArray();
		   $values=DB::table('tbl_lab_specimen')->where('status','Y')->orderBy('specimen_order')->pluck('name')->toArray();
		   return response()->json(['keys'=>$keys,'values'=>$values,'success'=>__('Updated successfully')]);
		  
	   }
	 break;
	 case 'delete':
	   $checked = $request->checked;
	   DB::table('tbl_lab_specimen')->where('id',$request->id)->update(['status'=>$checked]);
	   if($checked=='Y'){
		$keys = DB::table('tbl_lab_specimen')->where('status','Y')->orderBy('specimen_order')->pluck('id')->toArray();
		$values=DB::table('tbl_lab_specimen')->where('status','Y')->orderBy('specimen_order')->pluck('name')->toArray();
		return response()->json(['keys'=>$keys,'values'=>$values,'active'=>true,'success'=>__('Activated successfully')]);
	   }else{
		return response()->json(['inactive'=>true,'success'=>__('Deactivated successfully')]);   
	   }
	 break;
	}	 
  }
  
  
public function updateOrder($lang,Request $request){
	$id = $request->id;
	$testord = $request->order;
	$test = LabTests::select('id','category_num','testord')->find($id);
	//dd($test->testord.'-'.$testord);
	$category_num = $test->category_num;
	$cat_name = DB::table('tbl_lab_categories')->where('id',$category_num)->value('descrip');
	$exist_order = LabTests::where('active','Y')->whereNULL('group_num')->where('id','<>',$id)->where('category_num',$category_num)->where('testord',$testord)->get()->count();
	if($exist_order){
		$msg = __('Order exists for anther test in category').' : '.trim($cat_name);
		return response()->json(['error'=>$msg]);
	}else{
		if($test->testord != $testord){
			$user_num = auth()->user()->id;
			LabTests::where('id',$id)->update(['user_num'=>$user_num,'testord'=>$testord]);
			return response()->json(['success'=>__('Order updated successfully')]);
		}else{
			return response()->json(['no_response'=>true]);
		}
	}
	
} 

public function updateTestCode($lang,Request $request){
	$id = $request->id;
	$test_code = $request->test_code;
	$test = LabTests::select('id','test_code')->find($id);
	$exist_code = LabTests::where('active','Y')->whereNULL('group_num')->where('id','<>',$id)->where(DB::raw('lower(trim(test_code))'),strtolower(trim($test_code)))->get()->count();
	
	if($exist_code){
		$msg = __('This code exists for another test');
		return response()->json(['error'=>$msg]);
	}else{
		if($test->test_code != $test_code){
			$user_num = auth()->user()->id;
			LabTests::where('id',$id)->update(['user_num'=>$user_num,'test_code'=>trim($test_code)]);
			return response()->json(['success'=>__('Code updated successfully')]);
		}else{
			return response()->json(['no_response'=>true]);
		}
	}
	
}   


}
