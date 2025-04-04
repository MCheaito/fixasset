<?php
/*
*
* DEV APP
* Created date : 12-3-2023
*
*/
namespace App\Http\Controllers\inventory\category;
use App\Http\Controllers\Controller;


use App\Models\TblInventoryItemsFormula;
use App\Models\TblInventoryItemsTypes;
use App\Models\TblInventoryItems;
use App\Models\TblInventoryItemsFournisseur;
use App\Models\TblCategoriesTypes;
use App\Models\Clinic;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Alert;
use DataTables;
use PDF;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use DomDocument;
use UserHelper;
use Session;

class CategoryController extends Controller
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
	 $Type=TblInventoryItemsTypes::where('active','O')->orderBy('id')->get();
	 $filter_types="";
	 if(isset($request->selecttypes)&& $request->selecttypes!=""){
		 $filter_types=" and a.types='".$request->selecttypes."' ";
	 }
	 
	 $filter_status="";
           	if(isset($request->filter_status)){
			 $filter_status = "and a.active='".$request->filter_status."'";  
		   }
	 
	  $name = ($lang=='en')?'a.name_eng':'a.name';
	 $sql="select a.id,{$name} as cat_name,{$name}	as fname,
				 a.active,a.name_eng,a.name
	      from tbl_inventory_category_types a
		  where a.clinic_num='".$FromFacility->id."' ".$filter_status." order by a.id";
	 $category=DB::select(DB::raw("$sql"));
		
		if ($request->ajax()) {
		return Datatables::of($category)

                    ->addIndexColumn()

                    ->addColumn('action', function($row){

							$checked = ($row->active=='O')?'checked':'';
							$disabled = ($row->active=='O')?'':'disabled';

                           //$btn = '<form class="text-cnter" method="post" action="'.route('inventory.category.destroy',[app()->getLocale(),$row->id]).'">';
                           //$btn = $btn.'<input type="hidden" name="_token" value="'.csrf_token().'" />';
                           //$btn = $btn.'<a href="'.route('inventory.category.edit',[app()->getLocale(),$row->id]).'"  title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems"><i class="far fa-edit text-primary"></i></a>';
                           $btn = ' <a href="javascript:void(0)" onclick="editcategory('.$row->id.',\''.$row->name.'\',\''.$row->fname.'\',\''.$row->name_eng.'\')" title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems '.$disabled.'"><i class="far fa-edit text-primary"></i></a>';
						   $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';

						 //  if(UserHelper::can_access(auth()->user(),'delete_items')){
						   //$btn = $btn.'<button type="submit" title="'.__("delete").'" onclick="delCategory('.$row->id.')" class="btn btn-sm btn-clean btn-icon btn-icon-md deleteItems"><i class="fa fa-trash-alt text-danger"></i></button>';
                         //  }
						   //$btn = $btn.'</form';
						   return $btn;

                         })

                    ->rawColumns(['action'])
					
                    ->make(true);

   	    }
		
			   
       return view('inventory.category.index')->with('category',$category)->with('Type',$Type)->with('FromFacility',$FromFacility); 
	    		
}

public function SaveCategory($lang,Request $request){
$category_name=$request->category_name;
$category_name_eng=$request->category_name_eng;
$clinic_id=$request->clinic_id;

$uid=auth()->user()->id;
   if($request->id=='0'){
	 $newcategory=TblCategoriesTypes::create([
	    'types'=>'1',
	    'name'=>$category_name,
		 'name_eng'=>$category_name_eng,
	    'user_id'=>$uid,
		'clinic_num'=>$clinic_id,
		'active'=>'O'
	    ])->id; 
	   $msg=__('Saved successfully'); 
   }else{
	   $newcategory=$request->id;
	   TblCategoriesTypes::where('id',$request->id)->update([
	    'name'=>$category_name,
        'name_eng'=>$category_name_eng,
		'clinic_num'=>$clinic_id,
	    'user_id'=>$uid,
		'active'=>'O'
	    ]); 
	  $msg=__('Updated successfully'); 
     }
  
//Alert::toast($msg,'success');
//return back()->with('lang',$lang)->with('selecttype','1')->with('lunette_id',$newitems->id);
//$category= TblCategoriesTypes::find($newcategory);

//return back()->with('category',$category);	
return response()->json(["success"=>$msg]);	
}


					   
		      

public function edit($lang,$id) 
    {
	$suppliers= TblInventoryItemsFournisseur::find($id);	
	 $types= DB::table('tbl_inventory_fournisseur_types')->where('active','O')->orderBy('id')->get();
  $categorys= DB::table('tbl_inventory_category')->where('active','O')->orderBy('id')->get();
	$suppliercategory=DB::table('tbl_inventory_fournisseur_category')->where('fournisseur_id',$id)->where('active','O')->orderBy('id')->get();
	return view('inventory.suppliers.suppliers')->with('suppliers',$suppliers)->with('types',$types)->with('id_suppliers',$id)->with('categorys',$categorys)->with('suppliercategory',$suppliercategory);	
	
	}
	
public function destroy($lang,Request $request) 
    {
	$id = $request->id;
	$type=$request->type;
	
	switch($request->type){
	 case 'activate':
      TblCategoriesTypes::where('id',$id)->update(['active'=>'O','user_id'=>auth()->user()->id]);
	  $msg= __('Activated successfully');
     break;
     case 'inactivate':	 
       TblCategoriesTypes::where('id',$id)->update(['active'=>'N','user_id'=>auth()->user()->id]);
	   $msg= __('InActivated successfully');
     break;
	}
	//Alert::toast($msg,'success');
	//return back();
	return response()->json(["msg"=>$msg]);
	}
}




