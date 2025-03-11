<?php
/*
* DEV APP
* Created date : 27-4-2024
*  
* update functions from time to time till today
*
*/
namespace App\Http\Controllers\reports;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\ExtLab;
use DB;
use Illuminate\Http\Request;
use DataTables;
use UserHelper;
use Dompdf\Canvas;
use Dompdf\Options;
use Dompdf\Dompdf;


//Created date 27-4-2024
class CustomReportsController extends Controller
{
    
	public function __construct(){
        $this->middleware('auth');
    }
	
	public function index($lang,Request $request){
		  $user_type = auth()->user()->type;
		  $user_clinic_num=auth()->user()->clinic_num;
		  
		  $my_labs = Clinic::where('id',$user_clinic_num)->orderBy('id','desc')->get();
		  
		  $tests = DB::table('tbl_lab_tests')
		            ->whereRaw('is_group="Y" or ( (is_group<>"Y" or is_group IS NULL) and (group_num="" or group_num IS NULL) and cnss IS NOT NULL)')
					->orderBy('testord')->get();
		  $categories = DB::table('tbl_lab_categories')->where('active','Y')->orderBy('testord','desc')->get();
	
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
         
 		   $filter_cat='';
       	   if(isset($request->filter_cat) && $request->filter_cat!=''){
			   $filter_cat = $request->filter_cat;
		   }
		   
		   $data = DB::table('tbl_tests_custom_report as r')
		           ->select('r.id','r.report_name','t.test_name','cat.descrip as cat_name','r.active')
				   ->join('tbl_lab_tests as t','t.id','r.test_id')
				   ->leftjoin('tbl_lab_categories as cat','cat.id','r.category_num');
		 
		   if($filter_cat !=''){ $data = $data->where('r.category_num',$filter_cat); }
		   if($filter_test !=''){ $data = $data->where('r.test_id',$filter_test); }
		   if($filter_lab !=''){ $data = $data->where('r.clinic_num',$filter_lab); }
		    if($filter_status =='Y'){
				$data = $data->where('r.active',$filter_status);
			}else{
				$data = $data->where('r.active','N')->orWhereNULL('r.active');	
				}				 
		  $data = $data->orderBy('r.id','desc')->orderBy('cat.testord')->get();
		  
		  /*return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) use($lang){
                           $checked=($row->active=='Y')?'checked':'';
						   $disabled=($row->active=='Y')?'':'disabled';
						   $btn = '<button type="button"  class="btn btn-icon btn-md" onclick="editData('.$row->id.')" '.$disabled.'><i class="far fa-edit text-primary" title="'.__('Edit').'"></i></button>';
                           $btn.= '<button type="button"  class="btn btn-icon btn-md" onclick="printData('.$row->id.')" '.$disabled.'><i class="far fa-file-pdf text-primary" title="'.__('Print').'"></i></button>';
						   $btn.= '<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';
						   return $btn;
                    })
					->rawColumns(['action'])
                    ->make(true);*/
		 
		 
		 $result = $data->map(function($d){
			               $checked=($d->active=='Y')?'checked':'';
						   $disabled=($d->active=='Y')?'':'disabled';
						   $btn = '<button type="button"  class="btn btn-icon btn-md" onclick="editData('.$d->id.')" '.$disabled.'><i class="far fa-edit text-primary" title="'.__('Edit').'"></i></button>';
                           $btn.= '<button type="button"  class="btn btn-icon btn-md" onclick="printData('.$d->id.')" '.$disabled.'><i class="far fa-file-pdf text-primary" title="'.__('Print').'"></i></button>';
						   $btn.= '<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$d->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';
		                   return [
						      'id'=>$d->id,
						      'report_name'=>$d->report_name,
						      'test_name'=>$d->test_name,
						      'cat_name'=>$d->cat_name,
						      'action'=>$btn
						      ];
		                    });
		 
		 
		 return response()->json(['data' => $result]);
		 
		 }
	
	
	return view('reports.custom.index')->with(['my_labs'=>$my_labs,'tests'=>$tests,'categories'=>$categories]);;
	}

  public function getInfo($lang,Request $request){
		
	    $id = $request->id;
		$data = DB::table('tbl_tests_custom_report')->find($id);
		return response()->json(['data'=>$data]);
	}
  
  public function create($lang,Request $request){
	  $user_clinic_num = auth()->user()->clinic_num;
	  $user_num = auth()->user()->id;
	  $test = DB::table('tbl_lab_tests')->find($request->test_id);
	  $report_name = trim($request->report_name);
	  
	  $exist_test = DB::table('tbl_tests_custom_report')->where('active','Y')->where('test_id',$test->id)->get()->count();
	  
	  if($exist_test){
		$msg = __('A template report exists for the test').' : '.$test->test_name;
        return response()->json(['error'=>$msg]);		
	  }
	  
	  $exist = DB::table('tbl_tests_custom_report')->where('active','Y')->where('report_name',$report_name)->get()->count();
	  if($exist){
		$msg = __('The report name already exists for another test');
        return response()->json(['error'=>$msg]);		
	  }
	  
	  DB::table('tbl_tests_custom_report')->insert([
		     'test_id'=>$test->id,
			 'category_num'=>isset($test->category_num)?$test->category_num:NULL,
			 'description'=>$request->description,
			 'report_name'=>$report_name,
			 'user_num'=>$user_num,
			 'clinic_num'=>$user_clinic_num,
			 'active'=>'Y'
		    ]); 
	   
	  
	      $msg=__('Saved successfully');
	return response()->json(['success'=>$msg]);		  
	  
  }
  
  public function edit($lang,Request $request){
	  $user_num = auth()->user()->id;
	  $test = DB::table('tbl_lab_tests')->find($request->test_id);
	  $report_name = trim($request->report_name);
	  $id = $request->id;
	  
	  $exist_test = DB::table('tbl_tests_custom_report')->where('active','Y')->where('id','<>',$id)->where('test_id',$test->id)->get()->count();
	  
	  if($exist_test){
		$msg = __('A template report exists for the test').' : '.$test->test_name;
        return response()->json(['error'=>$msg]);		
	  }
	  
	  $exist = DB::table('tbl_tests_custom_report')->where('active','Y')->where('id','<>',$id)->where('report_name',$report_name)->get()->count();
	  
	  if($exist){
		$msg = __('The report name already exists for another test');
        return response()->json(['error'=>$msg]);		
	  }
	  
	  
	  
	  DB::table('tbl_tests_custom_report')->where('id',$id)->update([
		     'test_id'=>$test->id,
			 'category_num'=>isset($test->category_num)?$test->category_num:NULL,
			 'description'=>$request->description,
			 'report_name'=>$report_name,
			 'user_num'=>$user_num
		    ]); 
	   
	  
	 $msg=__('Updated successfully');
	return response()->json(['success'=>$msg]);		  
	  
  }
  
  public function delete($lang,Request $request){
        $id = $request->id;
	    $checked = $request->checked;
		$user_num = auth()->user()->id;
		
		if($checked=='N'){
		DB::table('tbl_tests_custom_report')->where('id',$id)->update(['active'=>'N','user_num'=>$user_num]);
		return response()->json(['success'=>__('InActivated successfully')]);	
		}
		
		if($checked=='O'){
		DB::table('tbl_tests_custom_report')->where('id',$id)->update(['active'=>'Y','user_num'=>$user_num]);
		return response()->json(['success'=>__('Activated successfully')]);		
		}
			
    }

  public function printPDF($lang,Request $request){
	  
	  $id = $request->id;
	  $template = DB::table('tbl_tests_custom_report as tr')
	              ->select('tr.id as template_id','t.test_name','tr.report_name','tr.description',DB::raw("IFNULL(cat.descrip,'') as cat_name"))
			      ->join('tbl_lab_tests as t','t.id','tr.test_id')
			      ->leftjoin('tbl_lab_categories as cat','cat.id','tr.category_num')
			      ->where('tr.id',$id)->first();
	 $branch = Clinic::find(auth()->user()->clinic_num);
	 $tel = isset($branch->telephone) && $branch->telephone!=''?'Tel: '.$branch->telephone.' , ':'';
	 $whatsapp = isset($branch->whatsapp) && $branch->whatsapp!=''?'Whatsapp: '.$branch->whatsapp.' , ':'';
	 $website = isset($branch->website) && $branch->website!=''?' '.$branch->website.' , ':'';
	 $address = isset($branch->full_address) && $branch->full_address!=''?'Address: '.$branch->full_address:'';
     $branch_data = $tel.$whatsapp.$website.$address;
     $descrip = $template->description;
	 $descrip = str_replace('&nbsp;',' ',$descrip);
	 
	 $data=['template'=>$template,'branch_data'=>$branch_data,'descrip'=>$descrip];	 
	 $htmlContent = view('reports.custom.templatePDF',$data)->render();
     $dompdf = new Dompdf();
     $dompdf->loadHtml($htmlContent);
     $options = new Options();
	 $options->set('defaultFont', 'sans-serif');
	 $options->set('isHtml5ParserEnabled', true);
	 $options->set('isRemoteEnabled', true);
	 $options->set('isJavascriptEnabled', true);
	 $dompdf->setOptions($options);
	 $dompdf->setPaper('A4', 'portrait');
     $dompdf->render();
     return $dompdf->stream('document.pdf');			
  }	
  


}	