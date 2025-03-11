<?php
/*
*
* DEV APP
* Created date : 14-12-2022
*
*/
namespace App\Http\Controllers\inventory\assetmaincategory;
use App\Http\Controllers\Controller;


use App\Models\TblInventoryItemsFormula;
use App\Models\TblInventoryItemsTypes;
use App\Models\TblInventoryItems;
use App\Models\TblInventoryItemsFournisseur;
use App\Models\TblInventoryCollectionFournisseur;
use App\Models\Clinic;
use App\Models\TblInventoryAssetMainCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Alert;
use DataTables;
use PDF;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use DomDocument;
use UserHelper;
use Session;

class AssetMainCategoryController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index($lang,Request $request) 
    {
	$myId=auth()->user()->id;	
	  if(auth()->user()->type==1){
		
		/*$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		$FromFacility =DB::select(DB::raw("$sqlFromFacility"));*/
		$FromFacility = Clinic::where('id',Session::get('inventory_branch_num'))->where('active','O')->first();

	   
	     }
	 if(auth()->user()->type==2){
	    $idFacility = auth()->user()->clinic_num; 
		
		$FromFacility = Clinic::where('id',$idFacility)->where('active','O')->first();
	 
	 }
	 
	   
	   
	   $all_asset = TblInventoryAssetMainCategory::where('clinic_num',$FromFacility->id)->orderBy('id','desc')->get();
	   
	  
	   
		if ($request->ajax()) {
		 
				 
		 $filter_status="";
           	if(isset($request->filter_status)){
			 $filter_status = "and f.active='".$request->filter_status."'";  
		   }
		 
		return Datatables::of($all_asset)

                    ->addIndexColumn()

                    ->addColumn('action', function($row){

							$checked = ($row->active=='O')?'checked':'';
							$disabled = ($row->active=='O')?'':'disabled';
                           //$btn = '<form class="text-center" method="post" action="'.route('inventory.clients.destroy',[app()->getLocale(),$row->id]).'">';
                           //$btn = $btn.'<input type="hidden" name="_token" value="'.csrf_token().'" />';
							   $btn ='<a href="'.route('inventory.assetmaincategory.edit',[app()->getLocale(),$row->id]).'"  title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems '.$disabled.' "  ><i class="far fa-edit text-primary"></i></a>';

                             $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';

						 //  if(UserHelper::can_access(auth()->user(),'delete_items')){
						   //$btn = $btn.'<button type="submit" title="'.__("delete").'" class="btn btn-sm btn-clean btn-icon btn-icon-md deleteItems" onclick="destroy_data('.$row->id.')"><i class="fa fa-trash-alt text-danger"></i></button>';
                         //  }
						   //$btn = $btn.'</form';
						   return $btn;

                         })

                    ->rawColumns(['action'])
					
                    ->make(true);

   	    }
		
			   
       return view('inventory.assetmaincategory.index')->with('all_asset',$all_asset)->with('FromFacility',$FromFacility); 
	    		
}

public function assetmaincategory($lang,$type,$id) 

    {
	
	$myId=auth()->user()->id;	
	 if(auth()->user()->type==1){
		
		/*$doc=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sql = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$doc->id." and a.active = 'O'";
        
		$clinic =DB::select(DB::raw("$sql"));*/
		$clinic = Clinic::where('id',Session::get('inventory_branch_num'))->where('active','O')->first();

	   
	     }	
		 if(auth()->user()->type==2){
	    $idClinic = auth()->user()->clinic_num; 
		
		$clinic = Clinic::where('id',$idClinic)->where('active','O')->first();
		 }			
	//$item=ListInventoryItems::where('id',$id)->where('active','O')->first();
	 $assets=TblInventoryAssetMainCategory::find($id);	
   

	
	return view('inventory.assetmaincategory.assetmaincategory')
	       ->with('assets',$assets)->with('clinic',$clinic)->with('id_assets',$id);	
	}	

public function store_assetmaincategory($lang,Request $request){
//get type of test
//$type=$request->type;
//get id
$id= $request->id_assets;

if($id=='0'){
$validator  = $request->validate([
		//   'code'=>'required',
		   'name'=>'required'
		   ],[
		//  'code.required' => __("Please enter the code"),
		  'name.required' => __("Please enter the  name")
		  ]);
}else{
	$validator  = $request->validate([
		   
		   'name'=>'required'
		   ],[
		  'name.required' => __("Please enter the  name")
		  ]);
}


$uid=auth()->user()->id;

 //$clientsCode=DB::table('tbl_inventory_items_fournisseur')->where('code',$request->code)->first();
//if (!empty($clientsCode->id) and ($id=='0')){
// $msg=__('Code clients Exists!');
//Alert::toast($msg,'Error');
//return back();
//}



 $signs = array();
   $assets=TblInventoryAssetMainCategory::find($request->id_assets);
   if($id=='0'){
	 $newitems=TblInventoryAssetMainCategory::create([
	    'name'=>$request->name,
	    'depreciation'=>$request->depreciation,
	    'accountnb'=>$request->accountnb,
	    'asset_life'=>$request->asset_life,
	    'asset_age'=>$request->asset_age,
	    'user_id'=>$uid,
		'clinic_num'=>$request->clinic_id,
		'active'=>'O'
	    ])->id; 
	   $msg=__('Saved successfully'); 
   }else{
	   $newitems=$id;
	   TblInventoryAssetMainCategory::where('id',$id)->update([
	  	'name'=>$request->name,
	    'depreciation'=>$request->depreciation,
	    'accountnb'=>$request->accountnb,
	    'asset_life'=>$request->asset_life,
	    'asset_age'=>$request->asset_age,
		 'user_id'=>$uid,
		'clinic_num'=>$request->clinic_id,
		'active'=>'O'
	    ]); 
	  $msg=__('Updated successfully'); 
     }
  
  		      
Alert::toast($msg,'success');
//return back()->with('lang',$lang)->with('selecttype','1')->with('lunette_id',$newitems->id);
$assets= TblInventoryAssetMainCategory::find($newitems);
//$collections= DB::table('tbl_inventory_collection')->where('active','O')->orderBy('id')->get();

return redirect()->route('inventory.assetmaincategory.assetmaincategory',[$lang,'1',$newitems])->with('assets',$assets);
	
}


					   
		      

public function edit($lang,$id) 
    {
	$assets= TblInventoryAssetMainCategory::find($id);	
	$clinic = Clinic::where('id',$assets->clinic_num)->where('active','O')->first();
	 
	 
	return view('inventory.assetmaincategory.assetmaincategory')->with('assets',$assets)->with('clinic',$clinic)->with('id_assets',$id);	
	
	}
	
public function destroy($lang,Request $request) 
    {
	$id=$request->id;
    $type=$request->type;
    switch($type){
		case 'activate':
		 	TblInventoryAssetMainCategory::where('id',$id)->update(['active'=>'O','user_id'=>auth()->user()->id]);
			$msg= __('Activated successfully');

		break;
		case 'inactivate':
		   	TblInventoryAssetMainCategory::where('id',$id)->update(['active'=>'N','user_id'=>auth()->user()->id]);
			$msg= __('InActivated successfully');
		break;
	}
    
	
	//Alert::toast($msg,'success');
	//return back();
	return response()->json(['msg'=>$msg]);
	}
}




