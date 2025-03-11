<?php
/*
*
* DEV APP
* Created date : 14-12-2022
*
*/
namespace App\Http\Controllers\inventory\Clients;
use App\Http\Controllers\Controller;


use App\Models\TblInventoryItemsFormula;
use App\Models\TblInventoryItemsTypes;
use App\Models\TblInventoryItems;
use App\Models\TblInventoryItemsFournisseur;
use App\Models\TblInventoryCollectionFournisseur;
use App\Models\Clinic;
use App\Models\TblInventoryClients;
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

class ClientsController extends Controller
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
	 
	   
	   
	   $all_clients = TblInventoryClients::where('clinic_num',$FromFacility->id)->orderBy('id','desc')->get();
	   
	  
	   
		if ($request->ajax()) {
		 
		 $filter_chart = "";
		 
		 if(isset($request->filter_chart) && $request->filter_chart!=""){ 
		 $filter_chart =" and f.fournisseur= '".$request->filter_chart."' ";
		 }
		 $filter_clients = "";
		 
		 if(isset($request->filter_clients) && $request->filter_clients!=""){ 
		 $filter_clients =" and f.id= '".$request->filter_clients."' ";
		 }
		 
		 $filter_status="";
           	if(isset($request->filter_status)){
			 $filter_status = "and f.active='".$request->filter_status."'";  
		   }
		 $undefined = __('Undefined');
		 $name = ($lang=='en')?'c.name_eng':'c.name';
		 $sql="select f.id,f.name,f.num_compte,f.tel,f.adresse,group_concat({$name}) as collection,f.active
					 from tbl_inventory_clients as f
					 LEFT JOIN tbl_inventory_fournisseur_collection as c ON c.active='O' and c.fournisseur_id=f.id and c.clinic_num=f.clinic_num
					 where  f.clinic_num='".$FromFacility->id."'  ".$filter_status." ".$filter_chart." ".$filter_clients."
					 group by f.id";
		 
		 $clients = DB::select(DB::raw("$sql"));
			$inv_setting_edit = UserHelper::can_access(auth()->user(),'inv_setting_edit');
 
		return Datatables::of($clients)

                    ->addIndexColumn()

                    ->addColumn('action', function($row){

							$checked = ($row->active=='O')?'checked':'';
							$disabled = ($row->active=='O')?'':'disabled';
                           //$btn = '<form class="text-center" method="post" action="'.route('inventory.clients.destroy',[app()->getLocale(),$row->id]).'">';
                           //$btn = $btn.'<input type="hidden" name="_token" value="'.csrf_token().'" />';
							   if(UserHelper::can_access(auth()->user(),'inv_setting_edit')){
								$btn ='<a href="'.route('inventory.clients.edit',[app()->getLocale(),$row->id]).'"  title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems '.$disabled.'"><i class="far fa-edit text-primary"></i></a>';
							   
						   }else{
							        $btn ='<a href="'.route('inventory.clients.edit',[app()->getLocale(),$row->id]).'"  title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems '.$disabled.' disabled "  ><i class="far fa-edit text-primary"></i></a>';

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
		
			   
       return view('inventory.clients.index')->with('all_clients',$all_clients)->with('FromFacility',$FromFacility); 
	    		
}

public function clients($lang,$type,$id) 

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
	 $clients=TblInventoryClients::find($id);	
     $types= DB::table('tbl_inventory_category_types')->where('active','O')->orderBy('id')->get();
	 $analyzer = DB::table('tbl_inventory_fournisseur_collection')
    ->select('collection_id as id', 'name')
    ->where('active', 'O')
    ->orderBy('id', 'desc')
    ->orderBy('name')
    ->get();
	$analyzer_code =[];
	$delivery_code =[];
	if (isset($clients)){
 $analyzer_code = json_decode($clients->analyzer,true);
	}
 $delivery = DB::table('tbl_inventory_week')
    ->select('id', 'name')
    ->where('active', 'O')
    ->orderBy('id', 'desc')
    ->orderBy('name')
    ->get();
	if (isset($clients)){
 $delivery_code = json_decode($clients->delivery,true);	 
	}
$usersp = DB::table('users_permissions as up')
    ->join('users as u', 'up.uid', '=', 'u.id')  // Correct join syntax
    ->select('up.uid', 'up.clients', DB::raw('CONCAT(u.fname, " ", u.lname) as name')) // Concatenate first and last name
    ->where('up.active', 'O')
    ->where('up.clients', 'like', '%"' . $id . ';%') // Match the ID within the string, with quotation marks
    ->get();

$userp_code = [];
if (!$usersp->isEmpty()) {
    $userp_code = $usersp->toArray(); // Convert collection to array if not empty
}

	
	return view('inventory.clients.clients')
	       ->with('clients',$clients)->with('delivery',$delivery)->with('delivery_code',$delivery_code)
		   ->with('id_clients',$id)->with('types',$types)->with('analyzer',$analyzer)->with('analyzer_code',$analyzer_code)
		  ->with('clinic',$clinic)->with('usersp',$usersp)->with('userp_code',$userp_code);	
	}	

public function store_clients($lang,Request $request){
//get type of test
//$type=$request->type;
//get id
$id= $request->id_clients;
$analyzer=json_encode($request->get('analyzer_code'));
$delivery=json_encode($request->get('delivery_code'));

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



$clients_type = $request->get('clients_type');
 $signs = array();
	 if(!empty($request->get('clients_type')) ){ $signs = json_encode($request->get('clients_type')); }else{ $signs = json_encode($request->get(''));}
   $clients=TblInventoryClients::find($request->id_clients);
   if($id=='0'){
	 $newitems=TblInventoryClients::create([
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
		'analyzer'=>$analyzer,
		'delivery'=>$delivery,
		'active'=>'O'
	    ])->id; 
	   $msg=__('Saved successfully'); 
   }else{
	   $newitems=$id;
	   TblInventoryClients::where('id',$id)->update([
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
		'analyzer'=>$analyzer,
		'delivery'=>$delivery,
		'active'=>'O'
	    ]); 
	  $msg=__('Updated successfully'); 
     }
  
  		      
Alert::toast($msg,'success');
//return back()->with('lang',$lang)->with('selecttype','1')->with('lunette_id',$newitems->id);
$clients= TblInventoryClients::find($newitems);
//$collections= DB::table('tbl_inventory_collection')->where('active','O')->orderBy('id')->get();

return redirect()->route('inventory.clients.clients',[$lang,'1',$newitems])->with('clients',$clients);
	
}


					   
		      

public function edit($lang,$id) 
    {
	$clients= TblInventoryClients::find($id);	
	$clinic = Clinic::where('id',$clients->clinic_num)->where('active','O')->first();
	 $types= DB::table('tbl_inventory_category_types')->where('active','O')->orderBy('id')->get();
	//dd($suppclinicCollIds);
	    $analyzer_code = json_decode($clients->analyzer,true);
		$analyzer=DB::table('tbl_inventory_fournisseur_collection')->select('collection_id as id','name')->where('active','O')->orderBy('id')->get();
		 $delivery_code = json_decode($clients->delivery,true);
		 $delivery=DB::table('tbl_inventory_week')->where('active','O')->orderBy('id')->get();



$usersp = DB::table('users_permissions as up')
    ->join('users as u', 'up.uid', '=', 'u.id')  // Correct join syntax
    ->select('up.uid', 'up.clients', DB::raw('CONCAT(u.fname, " ", u.lname) as name')) // Concatenate first and last name
    ->where('up.active', 'O')
    ->where('up.clients', 'like', '%"' . $id . ';%') // Match the ID within the string, with quotation marks
    ->get();

$userp_code = [];
if (!$usersp->isEmpty()) {
    $userp_code = $usersp->toArray(); // Convert collection to array if not empty
}


		 
		 
	return view('inventory.clients.clients')->with('userp_code',$userp_code)->with('usersp',$usersp)->with('delivery_code',$delivery_code)->with('delivery',$delivery)->with('analyzer_code',$analyzer_code)->with('analyzer',$analyzer)->with('clients',$clients)->with('clinic',$clinic)->with('types',$types)->with('id_clients',$id);	
	
	}
	
public function destroy($lang,Request $request) 
    {
	$id=$request->id;
    $type=$request->type;
    switch($type){
		case 'activate':
		 	TblInventoryClients::where('id',$id)->update(['active'=>'O','user_id'=>auth()->user()->id]);
			$msg= __('Activated successfully');

		break;
		case 'inactivate':
		   	TblInventoryClients::where('id',$id)->update(['active'=>'N','user_id'=>auth()->user()->id]);
			$msg= __('InActivated successfully');
		break;
	}
    
	
	//Alert::toast($msg,'success');
	//return back();
	return response()->json(['msg'=>$msg]);
	}
}




