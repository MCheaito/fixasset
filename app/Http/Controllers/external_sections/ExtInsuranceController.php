<?php
namespace App\Http\Controllers\external_sections;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\ExtIns;
use App\Models\ExtLab;
use App\Models\TblBillCurrency;

use DB;
use DataTables;
use Alert;
use Session;

class ExtInsuranceController extends Controller
{
  public function __construct()
    {
        $this->middleware('auth');
    }
  
  public function index($lang,Request $request)
    {
	
	    $user_type = auth()->user()->type;
	    $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
		$currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
		$lbl_usd = isset($currUSD)? $currUSD->price:90000;
        $lbl_euro = isset($currEURO)? $currEURO->price:90000;
	    $user_clinic_num=auth()->user()->clinic_num;
	    $my_labs = Clinic::where('id',$user_clinic_num)->get();
        
		Session::forget('INS');
		Session::put('INS',true);
	   
	   
	if($request->ajax()){
	   
	   
	   $filter_status='';
       
	   if(isset($request->filter_status) && $request->filter_status!=''){
		   $filter_status = $request->filter_status;
	   } 

        $filter_lab='';
       
	   if(isset($request->filter_lab) && $request->filter_lab!=''){
		   $filter_lab = $request->filter_lab;
	   }  	   	   
	   
	   $data = ExtIns::select('id','code','percentage','full_name','telephone','fax','email',
	                        'alternative_phone1','alternative_phone2','full_address','status',
							'rate','pricel','priced','pricee','has_prices');
							
	
           if($filter_lab !=''){
			   $data = $data->where('clinic_num',$filter_lab);
    			 }

            if($filter_status !=''){
				$data = $data->where('status',$filter_status);
			}			 
		
		   $data = $data->orderBy('id','desc');	
	   
	   
	 return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) use($lang){
                           $checked=($row->status=='Y')?'checked':'';
						   $disabled=($row->status=='N')?'disabled':'';
						   $btn = '<button type="button"  class="btn btn-icon btn-md" onclick="editData('.$row->id.')" '.$disabled.'><i class="far fa-edit text-primary" title="'.__('Edit').'"></i></button>';
                           $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';
						   if($row->status=='Y'){
							  if($row->has_prices=='Y'){
								 $url = route('ins_prices.edit',[$lang,$row->id]);
								 $btn .= '<a href="'.$url.'" class="ml-2 btn btn-action btn-icon btn-xs" title="'.__('Edit prices').'">Edit<i class="ml-1 fas fa-dollar-sign"></i></a>';
 							  }else{
								$url = route('ins_prices.create',[$lang,$row->id]);
								$btn .= '<a href="'.$url.'"  class="ml-2 btn btn-action btn-xs" title="'.__('Create prices').'">Create<i class="ml-1 fas fa-dollar-sign"></i></a>';
 							  } 
						   }
						   return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
				
	}	
		
		
	return view('external_ins.index')->with(['my_labs'=>$my_labs,'lbl_usd'=>$lbl_usd,'lbl_euro'=>$lbl_euro]);	
	}
	
	public function get($lang,Request $request)
    {
		
	    $id = $request->id;
		$data = ExtIns::find($id);
		return response()->json(['data'=>$data]);
	}
	
	
	public function store($lang,Request $request)
    {
	   
	   $id = $request->id;
	   $user_id=auth()->user()->id;
	   $user_clinic_num=$request->lab_num; 
	   $code = $request->code;
	   if($id=='0'){
		  $ins = ExtIns::where('full_name',$request->full_name)->first();
		  if(isset($ins) && isset($ins->full_name) && $ins->full_name!=''){
			  return response()->json(['error'=>'Name already exists for another one']);
		  }
		  
		  $ins = ExtIns::where('code',$request->code)->first();
		  if(isset($ins) && isset($ins->code) && $ins->code!=''){
			  return response()->json(['error'=>'Code already exists for another one']);
		  }
		  
		  $ins = ExtIns::where('email',$request->email)->first();
		  if(isset($ins) && isset($ins->email) && $ins->email!=''){
			  return response()->json(['error'=>'Email already exists for another one']);
		  }
		  
		    /*
			 'region_name'=>$request->region_name,
			 'appt_nb'=>$request->appt_nb,
			 'city'=>$request->city,
			 'state'=>$request->state,
			 'zip_code'=>$request->zip_code,
			 */
		  
		  
		  ExtIns::create([
		     'full_name'=>strtoupper(trim($request->full_name)),
			 'code'=>$request->code,
			 'full_address'=>trim($request->full_address),
			 'remarks'=>$request->remarks,
			 'telephone'=>str_replace("-","",$request->telephone),
			 'alternative_phone1'=>str_replace("-","",$request->alternative_phone1),
			 'alternative_phone2'=>str_replace("-","",$request->alternative_phone2),
			 'fax'=>str_replace("-","",$request->fax),
			 'email'=>$request->email,
			 'rate'=>$request->rate,
			 'pricel' => $request->pricel,
			 'priced' => $request->priced,
			 'pricee'=>$request->pricee,
			 'user_num'=>$user_id,
			 'clinic_num'=>$user_clinic_num,
			 'status'=>'Y'
		    ]); 
	   
	   return response()->json(['success'=>__('Saved successfully')]);
	   }else{
		  $ins =  ExtIns::where('full_name',$request->full_name)->where('id','<>',$id)->first();
		  if(isset($ins) && isset($ins->full_name) && $ins->full_name!=''){
			  return response()->json(['error'=>'Name already exists for another one']);
		  }
		  
		  $ins =  ExtIns::where('code',$request->code)->where('id','<>',$id)->first();
		  if(isset($ins) && isset($ins->code) && $ins->code!=''){
			  return response()->json(['error'=>'Code already exists for another one']);
		  }
		  
		  $ins =  ExtIns::where('email',$request->email)->where('id','<>',$id)->first();
		  if(isset($ins) && isset($ins->email) && $ins->email!=''){
			  return response()->json(['error'=>'Email already exists for another one']);
		  }
		   
		    ExtIns::where('id',$id)->update([
		     'full_name'=>strtoupper(trim($request->full_name)),
			 'code'=>$request->code,
			 'full_address'=>trim($request->full_address),
			 'remarks'=>$request->remarks,
			 'telephone'=>str_replace("-","",$request->telephone),
			 'alternative_phone1'=>str_replace("-","",$request->alternative_phone1),
			 'alternative_phone2'=>str_replace("-","",$request->alternative_phone2),
			 'fax'=>str_replace("-","",$request->fax),
			 'email'=>$request->email,
			 'pricel' => $request->pricel,
			 'priced' => $request->priced,
			 'pricee'=>$request->pricee,
			 'rate'=>$request->rate,
			 'user_num'=>$user_id
			   ]); 
	   	 return response()->json(['success'=>__('Updated successfully')]);	

	   }		
		
	}
	
	public function delete($lang,Request $request)
    {
	$id = $request->id;
	$checked = $request->checked;
    if($checked=='N'){
     ExtIns::where('id',$id)->update(['status'=>'N','user_num'=>auth()->user()->id]);
    return response()->json(['success'=>__('InActivated successfully')]);	
	}
	if($checked=='O'){
	ExtIns::where('id',$id)->update(['status'=>'Y','user_num'=>auth()->user()->id]);
    return response()->json(['success'=>__('Activated successfully')]);		
	}
	
	}
}	