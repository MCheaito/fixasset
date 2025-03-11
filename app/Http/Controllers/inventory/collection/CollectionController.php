<?php
/*
*
* DEV APP
* Created date : 8-3-2023
*
*/
namespace App\Http\Controllers\inventory\collection;
use App\Http\Controllers\Controller;


use App\Models\TblInventoryItemsFormula;
use App\Models\TblInventoryItemsTypes;
use App\Models\TblInventoryItems;
use App\Models\TblInventoryAssetMainCategory;
use App\Models\TblInventoryCollectionFournisseur;
use App\Models\Clinic;

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

class CollectionController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index($lang,Request $request) 
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
	 
	 $Fournisseur=TblInventoryAssetMainCategory::where('clinic_num',$clinic->id)->where('active','O')->orderBy('id')->get();
	 
	 $filter_supplier="";
	 if(isset($request->selectfournisseur) && $request->selectfournisseur!=""){
	  $filter_supplier=" and a.fournisseur_id = '".$request->selectfournisseur."' ";	 
	 }
	 
	 $filter_status="";
           	if(isset($request->filter_status)){
			 $filter_status = "and a.active='".$request->filter_status."'";  
		   }
	 
	 $name = ($lang=='en')?'a.name_eng':'a.name';
	 $sql="select a.id,{$name} as col_name,c.id as fid,c.name as fname,a.active,a.name_eng,a.name  
	       from tbl_inventory_fournisseur_collection a 
           INNER JOIN tbl_inventory_assetmaincategory c ON a.fournisseur_id=c.id and a.clinic_num=c.clinic_num and c.active='O'		   
		   where  a.clinic_num='".$clinic->id."'  ".$filter_status." ".$filter_supplier." order by a.id";
	 $collection=DB::select(DB::raw("$sql"));
		
		if ($request->ajax()) {
		return Datatables::of($collection)

                    ->addIndexColumn()

                    ->addColumn('action', function($row){

							$checked = ($row->active=='O')?'checked':'';
							$disabled = ($row->active=='O')?'':'disabled';

                           //$btn = '<form class="text-cnter" method="post" action="'.route('inventory.collection.destroy',[app()->getLocale(),$row->id]).'">';
                           //$btn = $btn.'<input type="hidden" name="_token" value="'.csrf_token().'" />';
                           //$btn = $btn.'<a href="'.route('inventory.collection.edit',[app()->getLocale(),$row->id]).'"  title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems"><i class="far fa-edit text-primary"></i></a>';
							$btn = '<a href="javascript:void(0)" onclick="editcollection('.$row->id.',\''.$row->name.'\','.$row->fid.',\''.$row->fname.'\',\''.$row->name_eng.'\')" title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems '.$disabled.'"><i class="far fa-edit text-primary"></i></a>';
						   $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';

						 //  if(UserHelper::can_access(auth()->user(),'delete_items')){
						   //$btn = $btn.'<button  title="'.__("delete").'" onclick="delCollection('.$row->id.')" class="btn btn-sm btn-clean btn-icon btn-icon-md deleteItems"><i class="fa fa-trash-alt text-danger"></i></button>';
                         //  }
						   //$btn = $btn.'</form';
						   return $btn;

                         })

                    ->rawColumns(['action'])
					
                    ->make(true);

   	    }
		
			   
       return view('inventory.collection.index')->with('collection',$collection)->with('Fournisseur',$Fournisseur)->with('clinic',$clinic); 
	    		
}

public function SaveCollection($lang,Request $request){
$fournisseur_id= $request->fournisseur_id;
$collection_name=$request->collection_name;
$collection_name_eng=$request->collection_name_eng;
$max = TblInventoryCollectionFournisseur::selectRaw('MAX(CAST(collection_id AS UNSIGNED)) as max_collection_id')
    ->value('max_collection_id');
$clinic_id=auth()->user()->clinic_num; 
$uid=auth()->user()->id;
//dd($request->id);
   if($request->id=='0'){
	 $newcollection=TblInventoryCollectionFournisseur::create([
	    'fournisseur_id'=>$fournisseur_id,
		'name'=>$collection_name,
		'name_eng'=>$collection_name_eng,
	    'user_id'=>$uid,
		'clinic_num'=>$clinic_id,
		'collection_id'=>$max+1,
		'active'=>'O'
	    ])->id; 
	   $msg=__('Saved successfully'); 
   }else{
	   $newcollection=$request->id;
	   TblInventoryCollectionFournisseur::where('id',$request->id)->update([
	  	'name'=>$collection_name,
		'name_eng'=>$collection_name_eng,
	    'user_id'=>$uid,
    	
	    ]); 
	  $msg=__('Updated successfully'); 
     }
  
//Alert::toast($msg,'success');
//return back()->with('lang',$lang)->with('selecttype','1')->with('lunette_id',$newitems->id);
//$collection= TblInventoryCollectionFournisseur::find($newcollection);

//return back()->with('collection',$collection);	
return response()->json(["success"=>$msg]);	
}


					   
		      

public function edit($lang,$id) 
    {
	$suppliers= TblInventoryAssetMainCategory::find($id);	
	 $types= DB::table('tbl_inventory_fournisseur_types')->where('active','O')->orderBy('id')->get();
    $collections= DB::table('tbl_inventory_collection')->where('active','O')->orderBy('id')->get();
	$supplierCollection=DB::table('tbl_inventory_fournisseur_collection')->where('fournisseur_id',$id)->where('active','O')->orderBy('id')->get();
	return view('inventory.suppliers.suppliers')->with('suppliers',$suppliers)->with('types',$types)->with('id_suppliers',$id)->with('collections',$collections)->with('supplierCollection',$supplierCollection);	
	
	}
	
public function destroy($lang,Request $request) 
    {
    $id = $request->id;
	$type=$request->type;
	
	switch($request->type){
	 case 'activate':
	   	 TblInventoryCollectionFournisseur::where('id',$id)->update(['active'=>'O','user_id'=>auth()->user()->id]);
         $msg= __('Activated successfully');
	 break; 	 
	 case 'inactivate':	 
	   TblInventoryCollectionFournisseur::where('id',$id)->update(['active'=>'N','user_id'=>auth()->user()->id]);
       $msg= __('InActivated successfully');
	  break;
	}	  
	//Alert::toast($msg,'success');
	return response()->json(['msg'=>$msg]);
	}
}




