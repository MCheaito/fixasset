<?php
/*
*
* DEV APP
* Created date : 14-12-2022
*
*/
namespace App\Http\Controllers\inventory\suppliers;
use App\Http\Controllers\Controller;


use App\Models\TblInventoryItemsFormula;
use App\Models\TblInventoryItemsTypes;
use App\Models\TblInventoryItems;
use App\Models\TblInventoryItemsFournisseur;
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

class SuppliersController extends Controller
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
	 
	   
	   
	   $all_suppliers = TblInventoryItemsFournisseur::where('clinic_num',$FromFacility->id)->orderBy('id','desc')->get();
	   
	  
	   
		if ($request->ajax()) {
		 
		 $filter_chart = "";
		 
		 if(isset($request->filter_chart) && $request->filter_chart!=""){ 
		 $filter_chart =" and f.fournisseur= '".$request->filter_chart."' ";
		 }
		 $filter_supplier = "";
		 
		 if(isset($request->filter_supplier) && $request->filter_supplier!=""){ 
		 $filter_supplier =" and f.id= '".$request->filter_supplier."' ";
		 }
		 
		 $filter_status="";
           	if(isset($request->filter_status)){
			 $filter_status = "and f.active='".$request->filter_status."'";  
		   }
		 $undefined = __('Undefined');
		 $name = ($lang=='en')?'c.name_eng':'c.name';
		 $sql="select f.id,f.name,f.num_compte,f.tel,f.adresse,group_concat({$name}) as collection,f.active
					 from tbl_inventory_items_fournisseur as f
					 LEFT JOIN tbl_inventory_fournisseur_collection as c ON c.active='O' and c.fournisseur_id=f.id and c.clinic_num=f.clinic_num
					 where  f.clinic_num='".$FromFacility->id."'  ".$filter_status." ".$filter_chart." ".$filter_supplier."
					 group by f.id";
		 
		 $suppliers = DB::select(DB::raw("$sql"));
		 
		return Datatables::of($suppliers)

                    ->addIndexColumn()

                    ->addColumn('action', function($row){

							$checked = ($row->active=='O')?'checked':'';
							$disabled = ($row->active=='O')?'':'disabled';
                           //$btn = '<form class="text-center" method="post" action="'.route('inventory.suppliers.destroy',[app()->getLocale(),$row->id]).'">';
                           //$btn = $btn.'<input type="hidden" name="_token" value="'.csrf_token().'" />';
						if(UserHelper::can_access(auth()->user(),'inv_setting_edit')){
                          
						  $btn ='<a href="'.route('inventory.suppliers.edit',[app()->getLocale(),$row->id]).'"  title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems '.$disabled.'"><i class="far fa-edit text-primary"></i></a>';
                        }else{
				  $btn ='<a href="'.route('inventory.suppliers.edit',[app()->getLocale(),$row->id]).'"  title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems '.$disabled.' disabled "><i class="far fa-edit text-primary"></i></a>';
   						
							
						}  
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
		
			   
       return view('inventory.suppliers.index')->with('all_suppliers',$all_suppliers)->with('FromFacility',$FromFacility); 
	    		
}

public function suppliers($lang,$type,$id) 

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
	 $suppliers=TblInventoryItemsFournisseur::find($id);	
     $types= DB::table('tbl_inventory_category_types')->where('active','O')->orderBy('id')->get();
     $suppclinicCollIds= DB::table('tbl_inventory_fournisseur_collection')->where('fournisseur_id',$id)->where('clinic_num',$clinic->id)->where('active','O')->orderBy('id')->pluck('id')->toArray();
	 
	 $supplierCollection=DB::table('tbl_inventory_fournisseur_collection')->where('fournisseur_id',$id)->where('active','O')->orderBy('id')->get();
	 
	return view('inventory.suppliers.suppliers')
	       ->with('suppliers',$suppliers)
		   ->with('id_suppliers',$id)->with('types',$types)
		   ->with('CollIds',$suppclinicCollIds)
		   ->with('supplierCollection',$supplierCollection)->with('clinic',$clinic);	
	}	

public function store_suppliers($lang,Request $request){
//get type of test
//$type=$request->type;
//get id
$id= $request->id_suppliers;

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

 //$suppliersCode=DB::table('tbl_inventory_items_fournisseur')->where('code',$request->code)->first();
//if (!empty($suppliersCode->id) and ($id=='0')){
// $msg=__('Code Supplier Exists!');
//Alert::toast($msg,'Error');
//return back();
//}



$suppliers_type = $request->get('suppliers_type');
 $signs = array();
	 if(!empty($request->get('suppliers_type')) ){ $signs = json_encode($request->get('suppliers_type')); }else{ $signs = json_encode($request->get(''));}
   $suppliers=TblInventoryItemsFournisseur::find($request->id_suppliers);
   if($id=='0'){
	 $newitems=TblInventoryItemsFournisseur::create([
	    'code'=>$request->code,
	    'name'=>$request->name,
	    'adresse'=>$request->adresse,
	    'tel'=>$request->tel,
	    'email'=>$request->email,
	    'ville'=>$request->ville,
	    'province'=>$request->province,	
	    'codepostal'=>$request->codepostal,
		'pays'=>$request->pays,
		'types'=>$signs,
	    'contact'=>$request->contact,	
	    'fax'=>$request->fax,
	    'num_compte'=>$request->num_compte,
	    'sequence'=>$request->sequence,
		'code_method'=>$request->code_method,
		'item_seq'=>$request->item_seq,
	    'notes'=>$request->notes,
	    'user_id'=>$uid,
		'clinic_num'=>$request->clinic_id,
		'fournisseur'=>$request->type_fournisseur,
		'active'=>'O'
	    ])->id; 
	   $msg=__('Saved successfully'); 
   }else{
	   $newitems=$id;
	   TblInventoryItemsFournisseur::where('id',$id)->update([
	  	 'code'=>$request->code,
	    'name'=>$request->name,
	    'adresse'=>$request->adresse,
	    'tel'=>$request->tel,
	    'email'=>$request->email,
	    'ville'=>$request->ville,
	    'province'=>$request->province,	
	    'codepostal'=>$request->codepostal,
		'pays'=>$request->pays,
		'types'=>$signs,
	    'contact'=>$request->contact,	
	    'fax'=>$request->fax,
	    'num_compte'=>$request->num_compte,
	    'sequence'=>$request->sequence,
		'code_method'=>$request->code_method,
		'item_seq'=>$request->item_seq,
	    'notes'=>$request->notes,
	    'user_id'=>$uid,
		'clinic_num'=>$request->clinic_id,
		'fournisseur'=>$request->type_fournisseur,
		'active'=>'O'
	    ]); 
	  $msg=__('Updated successfully'); 
     }
 // if(!empty($request->get('collection'))){
				 //delete old links      
		//		TblInventoryCollectionFournisseur::where('fournisseur_id',$newitems)->delete();
				
				//insert new  links for clinic	
		//		$collections= $request->get('collection');
		//		foreach($collections as $collection){
		//		$split_coll = explode(";",$collection); //collection: id;name
		//		TblInventoryCollectionFournisseur::create([
			//		'fournisseur_id'=> $newitems,
			//		'collection_id'=> $split_coll[0],
			//		'name'=>$split_coll[1],
			//		'name_eng'=>$split_coll[1],
			//		'clinic_num'=>$request->clinic_id,
			//		'user_id' => auth()->user()->id,
			//		'active' => 'O'
			//		 ]);
			//	}
			//   }else{
				 //delete old links      
			//	TblInventoryCollectionFournisseur::where('fournisseur_id',$newitems)->delete();
			//  }
  		      
Alert::toast($msg,'success');
//return back()->with('lang',$lang)->with('selecttype','1')->with('lunette_id',$newitems->id);
$suppliers= TblInventoryItemsFournisseur::find($newitems);
//$collections= DB::table('tbl_inventory_collection')->where('active','O')->orderBy('id')->get();
$supplierCollection=DB::table('tbl_inventory_fournisseur_collection')->where('fournisseur_id',$newitems)->where('active','O')->orderBy('id')->get();

return redirect()->route('inventory.suppliers.suppliers',[$lang,'1',$newitems])->with('suppliers',$suppliers)->with('supplierCollection',$supplierCollection);	
	
}


					   
		      

public function edit($lang,$id) 
    {
	$suppliers= TblInventoryItemsFournisseur::find($id);	
	$clinic = Clinic::where('id',$suppliers->clinic_num)->where('active','O')->first();
	 $types= DB::table('tbl_inventory_category_types')->where('active','O')->orderBy('id')->get();
     $suppclinicCollIds= DB::table('tbl_inventory_fournisseur_collection')->where('fournisseur_id',$id)->where('clinic_num',$clinic->id)->where('active','O')->orderBy('id')->pluck('id')->toArray();
	$supplierCollection=DB::table('tbl_inventory_fournisseur_collection')->where('fournisseur_id',$id)->where('active','O')->orderBy('id')->get();
	//dd($suppclinicCollIds);
	return view('inventory.suppliers.suppliers')->with('suppliers',$suppliers)->with('clinic',$clinic)->with('types',$types)->with('id_suppliers',$id)->with('CollIds',$suppclinicCollIds)->with('supplierCollection',$supplierCollection);	
	
	}
	
public function destroy($lang,Request $request) 
    {
	$id=$request->id;
    $type=$request->type;
    switch($type){
		case 'activate':
		 	TblInventoryItemsFournisseur::where('id',$id)->update(['active'=>'O','user_id'=>auth()->user()->id]);
			$msg= __('Activated successfully');

		break;
		case 'inactivate':
		   	TblInventoryItemsFournisseur::where('id',$id)->update(['active'=>'N','user_id'=>auth()->user()->id]);
			$msg= __('InActivated successfully');
		break;
	}
    
	
	//Alert::toast($msg,'success');
	//return back();
	return response()->json(['msg'=>$msg]);
	}
}




