<?php
/*
*
* DEV APP
* Created date : 4-12-2022
* Created date : 30-3-2023 (generate normal pdf)
*
*/
namespace App\Http\Controllers\inventory\items;
use App\Http\Controllers\Controller;


use App\Models\TblInventoryItemsFormula;
use App\Models\TblInventoryItemsTypes;
use App\Models\TblInventoryItems;
use App\Models\TblInventoryItemsFournisseur;
use App\Models\TblInventoryFormulaPrice;
use App\Models\TblInventoryInvSerials;
use App\Models\TblInventoryInvoicesRequest;
use App\Models\TblInventoryAssetMainCategory;
use App\Models\TblInventoryCollectionFournisseur;
use App\Models\TblLocation;
use App\Models\TblInventoryInvoicesDetails;
use App\Models\TblInventoryTypes;
use App\Models\TblCategoriesTypes;
use App\Models\Clinic;
use App\Models\Doctor;
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

class ItemsController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index($lang,Request $request) 
    {
	 $myId=auth()->user()->id;	
	  if(auth()->user()->type==1){
		
		//old no need
		//$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        //$sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_name from 
                            //tbl_doctors_clinics a, tbl_clinics b where 
                            //a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		//$FromFacility =DB::select(DB::raw("$sqlFromFacility"));
         $FromFacility = Clinic::where('id',Session::get('inventory_branch_num'))->where('active','O')->first();
	     }
	 if(auth()->user()->type==2){
	    $idFacility = auth()->user()->clinic_num; 
		
		$FromFacility = Clinic::where('id',$idFacility)->where('active','O')->first();

	 }
	 if ($lang=="fr"){
		   $iCategory = DB::table('tbl_inventory_category_types')->select('id', 'name')->where('clinic_num',$FromFacility->id)->where('active','O')->get();

	}else{
		   $iCategory = DB::table('tbl_inventory_category_types')->select('id', 'name_eng as name')->where('clinic_num',$FromFacility->id)->where('active','O')->get();

	}
	   $iSupplier =TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->get();

		   $Analyzer = DB::table('tbl_inventory_fournisseur_collection')->select('collection_id as id', 'name_eng as name')->where('clinic_num',$FromFacility->id)->where('active','O')->get();

		
		$Type=TblCategoriesTypes::where('active','O')->get();
	  
	   $Items = DB::table('tbl_inventory_items')
		           ->select('tbl_inventory_items.id','tbl_fournisseur.name as fournisseur',
				            'tbl_inventory_items.sel_price','tbl_inventory_items.cost_price','tbl_inventory_items.qty',
							'tbl_inventory_items.sku','tbl_inventory_items.description','cat.name as cat_name',
							'tbl_inventory_items.active','tbl_inventory_items.nbtest','tbl_collection.name as analyzer')
				   ->leftjoin('tbl_inventory_items_fournisseur as tbl_fournisseur','tbl_fournisseur.id','tbl_inventory_items.fournisseur')			
				   ->leftjoin('tbl_inventory_fournisseur_collection as tbl_collection','tbl_inventory_items.brand','tbl_collection.collection_id')			
		           ->leftjoin('tbl_inventory_category_types as cat',function($q){
					   $q->on('cat.id','tbl_inventory_items.category')->where('cat.active','O');
				     })->where('tbl_inventory_items.clinic_num',$FromFacility->id);
					 
		  if(isset($request->selecttype) && $request->selecttype!=""){
			  $Items=$Items->where('tbl_inventory_items.materiel',$request->selecttype);
		   }
		  if(isset($request->filter_fournisseur) && $request->filter_fournisseur!=""){
			 $Items=$Items->where('tbl_inventory_items.fournisseur',$request->filter_fournisseur);
		   }
          if(isset($request->filter_category) && $request->filter_category!=""){
			 $Items=$Items->where('tbl_inventory_items.category',$request->filter_category);
		   }
         if(isset($request->filter_status)){
			 $Items=$Items->where('tbl_inventory_items.active',$request->filter_status);
		   }
		if(isset($request->filter_used)){
			 $Items=$Items->where('tbl_inventory_items.brand',$request->filter_used);
		   }		   		   
		 $Items=$Items->distinct();
		 $Items_Data = $Items->get();
		
		if ($request->ajax()) {
		return Datatables::of($Items)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
							$checked = ($row->active=='O')?'checked':'';
                            $disabled = ($row->active=='O')?'':'disabled';
						if(UserHelper::can_access(auth()->user(),'inv_setting_edit')){
                            $btn = '<a href="'.route('inventory.items.edit',[app()->getLocale(),$row->id]).'"  title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems '.$disabled.'"><i class="far fa-edit text-primary"></i></a>';
						}else{
				          $btn = '<a href="'.route('inventory.items.edit',[app()->getLocale(),$row->id]).'"  title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems '.$disabled.' disabled "><i class="far fa-edit text-primary"></i></a>';
							
							
						}
                            $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';
						   return $btn;
                         })
                    ->rawColumns(['action'])
				    ->make(true);
   	    }
		
	$tsq = DB::table('tbl_inventory_items')->where('active', 'O')->where('typecode', '1')->where('qty','>', '0')->sum('qty');
	
	$tamount = DB::table('tbl_inventory_items')
    ->where('active', 'O')->where('typecode', '1')->where('qty','>', '0')
    ->sum(DB::raw('qty * cost_price'));	 
	 
       return view('inventory.items.index')->with(['Analyzer'=>$Analyzer,'iSupplier'=>$iSupplier,'tsq'=>$tsq,'tamount'=>$tamount])->with('iCategory',$iCategory)->with('Type',$Type)->with('Items',$Items)->with('FromFacility',$FromFacility); 
	    		
}
public function produits($lang,$type,$id) 

    {
		
	//$item=ListInventoryItems::where('id',$id)->where('active','O')->first();
	$item= TblInventoryItems::find($id);	
	$Fournisseur=TblInventoryItemsFournisseur::where('active','O')->get();
	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->where('fournisseur_type',$type)->get();
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->where('fournisseur_type','3')->orderBy('ord')->get();
  
	return view('inventory.items.produits')->with('item',$item)->with('Fournisseur',$Fournisseur)->with('iType',$iType)->with('Formula',$Formula)->with('lunette_specs',$lunette_specs)->with('lunette_id',$id)->with('type',$type);	
	}	
public function lunette($lang,$type,$id) 

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
		//$item=ListInventoryItems::where('id',$id)->where('active','O')->first();
	$item= TblInventoryItems::find($id);	
	if($lang=="fr"){
		$sql="select a.collection_id as id,a.name from tbl_inventory_fournisseur_collection a where a.clinic_num = ".$FromFacility->id." and a.active='O' order by a.id";
		}else{
		$sql="select a.collection_id as id,a.name_eng as name from tbl_inventory_fournisseur_collection a where a.clinic_num = ".$FromFacility->id." and a.active='O' order by a.id";
		}
	$collection=DB::select(DB::raw("$sql"));	 
	$location=TblLocation::where('active','O')->get();
	$assetm=TblInventoryAssetMainCategory::where('active','O')->get();
	$subcategry=TblInventoryCollectionFournisseur::where('active','O')->get();
	$Fournisseur=TblInventoryItemsFournisseur::where('clinic_num',$FromFacility->id)->where('active','O')->where('fournisseur','2')->get();
	$General=TblInventoryItemsFournisseur::where('clinic_num',$FromFacility->id)->where('active','O')->where('fournisseur','1')->get();
	$Depreciation=TblInventoryItemsFournisseur::where('clinic_num',$FromFacility->id)->where('active','O')->where('fournisseur','3')->get();
	$Fixed=TblInventoryItemsFournisseur::where('clinic_num',$FromFacility->id)->where('active','O')->where('fournisseur','4')->get();
	$DepreciationType=TblCategoriesTypes::where('active','O')->get();

	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblCategoriesTypes::where('active','O')->get();
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->where('fournisseur_type',$type)->orderBy('ord')->get();
    if($lang=="fr"){
	$iCategory= DB::table('tbl_inventory_category_types')->select('id', 'name')->where('active','O')->orderBy('id')->get();
	}else{
	$iCategory= DB::table('tbl_inventory_category_types')->select('id', 'name_eng as name')->where('active','O')->orderBy('id')->get();
	}
	return view('inventory.items.lunette')->with('collection',$collection)->with('item',$item)->with('Fournisseur',$Fournisseur)->with('iType',$iType)->with('Formula',$Formula)->with('lunette_specs',$lunette_specs)->with('lunette_id',$id)->with('type',$type)->with('FromFacility',$FromFacility)->with('iCategory',$iCategory)->with('location',$location)->with('assetm',$assetm)->with('subcategry',$subcategry)->with('General',$General)->with('Depreciation',$Depreciation)->with('Fixed',$Fixed)->with('DepreciationType',$DepreciationType);	
	}	
 public function specstype($lang,Request $Request) {
	 	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->where('fournisseur_type',$Request->id)->orderBy('ord')->get();
        $html = '';
      	$count=0;
		
 foreach($lunette_specs as $t) {
			$name = ($lang=='en')?$t->english:$t->french;
			
				$html .= '<div class="mb-1 col-md-2 col-5">
				 <label class="label-size">'.$name.'</label>
				 <input type="text" id="'.$t->english.'" name="lunette_type[]" class="form-control" value=""/>
				 </div>'; 	
			 
			 $count++; 
						}
if($lang=='fr'){
$lunette_category= DB::table('tbl_inventory_category_types')->select('id','name')->where('active','O')->where('types',$Request->id)->orderBy('id')->get();
}else{
$lunette_category= DB::table('tbl_inventory_category_types')->select('id','name_eng as name')->where('active','O')->where('types',$Request->id)->orderBy('id')->get();
	
}      
		$html1 = '';
 $html1='';
	foreach ($lunette_category as $slunette_category) {
		// $selected=($slunette_category->id==$item->category)?'selected':'';
       $html1 .= '<option value="' . $slunette_category->id.'" '.'>'.$slunette_category->name .'</option>';
           }			
						
            return response()->json(['html' => $html,'html1' => $html1]);
         }      
public function store_lunette($lang,Request $request){
//get type of test
//$type=$request->type;
//get id
	
 if ($request->typesave!='M'){
 $validator  = $request->validate([
	   'fournisseur'=>'required',
	   'description'=>'required'
	 	],[
	   'fournisseur.required' => __("Please choose your supplier"),
	   'description.required' => __("Please Enter the description")
			]);
          }else{
	
if($request->fournisseur==""){
    $msg=__('Please choose your supplier');
	return response()->json(['warning'=>$msg]);
}		  
if($request->description==""){
    $msg=__('Please Enter the description');
	return response()->json(['warning'=>$msg]);
}			  
		  }
$id= $request->id_lunette;
$uid=auth()->user()->id;
//array with selected checkboxes
 //$lunette_type = $request->has('lunette_type')?$request->get('lunette_type'):NULL;
 //dd($request->sel_price1);
 //  $item=ListInventoryItems::find($request->lunette_id);
	if($request->has('lunette_type')){ 
		 $specs_lunette = array();
		 if(!empty($request->get('lunette_type')) ){ 
			  $specs_lunette = json_encode($request->get('lunette_type')); 
			  }
			  //else{ 
			  //$specs_lunette = json_encode($request->get(''));
			  //}
	       }else{
			 $specs_lunette = NULL;  
		   } 
		   
		   //dd($specs_lunette);
   //new data
//   dd($specs_lunette);
 //  $cost_price=number_format((float)$request->cost_price, 2, '.','');
 //  $sel_price=number_format((float)$request->sel_price, 2, '.','');

   
  // dd($request->num_invoice);
		$Fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('id',$request->fournisseur)->first();

  if ($Fournisseur->code_method=='2')
		{
			//$sequence=substr($Fournisseur->name,0,3);
			$sequence=$Fournisseur->id."-";
			$seq=$Fournisseur->item_seq;
			$seq=(int) $seq+1;
			//if ($seq<10)
			//{	
			//$sequence .="00";
			//$sequence .=$seq;
			//}
			//if ($seq>=10 and $seq<100)
			//{	
			//$sequence .="0";
			//$sequence .=$seq;
			//}
			//if ($seq>=100 and $seq<1000)
			//{	
			$sequence .=$seq;
			//}
		}
		if ($Fournisseur->code_method=='1'){
		//$seq=$Fournisseur->item_seq;
		$seq= TblInventoryItems::max('id');
		$seq=(int) $seq+1;
		$sequence=$seq;	
		}
  if(isset($request->barcode)){
	  $sequence= $request->barcode;
  }else{
	  $sequence=$sequence;
  }
  
   if($id=='0'){
	 $newitems=TblInventoryItems::create([
	    'sku'=>$sequence,
	    'num_invoice'=>$request->num_invoice,
	    'items_specs'=>$specs_lunette,
	    'brand'=>$request->brand,
	    'line'=>$request->line,
	    'fournisseur'=>$request->fournisseur,
	    'description'=>$request->description,
	    'gen_types'=>'2',	
	    'cost_price'=>number_format((float)$request->cost_price, 2, '.', ''),
	    'sel_price'=>number_format((float)$request->sel_price, 2, '.', ''),
		'initprice'=>number_format((float)$request->initprice, 2, '.', ''),
	    'formula_id'=>$request->formula_id,	
		'nbtest'=>$request->nbtest,	
		'frac'=>$request->frac,	
		'currency'=>$request->currency,	
		'used'=>$request->used,	
	    'qty'=>'0',
		'gqty'=>'0',
	    'barcode'=>$sequence,
	    'notes'=>$request->notes,
	    'date_reception'=>$request->date_reception,
	    'num_facture'=>$request->num_facture,
	    'category'=>$request->category,
	    'taxable'=>($request->taxable=='on')?'Y':'N',
	    'min'=>$request->min,
	    'max'=>$request->max,
	    'garanty'=>$request->garanty,
	   	'clinic_num'=>$request->clinic_id,
		'materiel'=>$request->materiel,
		'typecode'=>$request->typecode,
		'offre'=>$request->offre,
		'discount'=>$request->discount,
		'nblot'=>$request->nblot,
		'dexpiry'=>$request->dexpiry,
		'location_id'=>$request->location_id,
		'main_id'=>$request->main_id,
		'sub_id'=>$request->sub_id,
		'detailsasset'=>$request->detailsasset,
		'manufacturer'=>$request->manufacturer,
		'modele'=>$request->modele,
		'serial'=>$request->serial,
		'madeof'=>$request->madeof,
		'dcolor'=>$request->dcolor,
		'invdate'=>$request->invdate,
		'capdate'=>$request->capdate,
		'specdetails'=>$request->specdetails,
		'pricelbp'=>$request->pricelbp,
		'priceusd'=>$request->priceusd,
		'deplbp'=>$request->deplbp,
		'rate'=>$request->rate,
		'grosslbp'=>$request->grosslbp,
		'netlbp'=>$request->netlbp,
		'acclbp'=>$request->acclbp,
		'lastdatedep'=>$request->lastdatedep,	
		'descriptionacct'=>$request->descriptionacct,	
		'accumlateacct'=>$request->accumlateacct,	
		'descriptiontype'=>$request->descriptiontype,
		'status'=>$request->status1,			
		'remark'=>$request->remark,			
		'insurance'=>$request->insurance,			
		'lastmaintenance'=>$request->lastmaintenance,			
		'inexpdate'=>$request->inexpdate,			
		'softlicence'=>$request->softlicence,			
		'username'=>$request->username1,
		'mainacct'=>$request->mainacct,
		'deprate'=>$request->deprate,
		'assetlifey'=>$request->assetlifey,
		'assetlifea'=>$request->assetlifea,
		'locationd'=>$request->locationd,		
		'user_id'=>$uid,
		'active'=>'O'
	    ])->id; 
	 //   DB::table("tbl_inventory_items_fournisseur")->where('id', $request->fournisseur)->increment('item_seq');
	  //  $Fournisseur=ListInventoryItemsFournisseur::where('active','O')->where('id',$request->fournisseur)->first();
	//   if ($Fournisseur->code_method=='1')
	//	{
	//	ListInventoryItems::where('id',$newitems)->update([
	 // 	'sku'=>$Fournisseur->item_seq,
	//'num_invoice'=>$Fournisseur->item_seq,
	  //  'barcode'=>$Fournisseur->item_seq
	//	  ]); 
	//	}
	   $msg=__('Saved successfully'); 
   }else{
	   $newitems=$request->id_lunette;
	   TblInventoryItems::where('id',$newitems)->update([
	  	 'sku'=>$request->sku,
	    'num_invoice'=>$request->num_invoice,
	    'items_specs'=>$specs_lunette,
	    'brand'=>$request->brand,
	    'line'=>$request->line,
	    'fournisseur'=>$request->fournisseur,
	    'description'=>$request->description,
	    'gen_types'=>'2',	
	    'cost_price'=>number_format((float)$request->cost_price, 2, '.', ''),
	    'sel_price'=>number_format((float)$request->sel_price, 2, '.', ''),
		'initprice'=>number_format((float)$request->initprice, 2, '.', ''),
	    'formula_id'=>$request->formula_id,
		'nbtest'=>$request->nbtest,	
		'frac'=>$request->frac,	
		'currency'=>$request->currency,
		'used'=>$request->used,			
	    'qty'=>$request->qty,
		'gqty'=>$request->gqty,
	    'barcode'=>$sequence,
	    'notes'=>$request->notes,
	    'date_reception'=>$request->date_reception,
	    'num_facture'=>$request->num_facture,
	    'category'=>$request->category,
	    'taxable'=>($request->taxable=='on')?'Y':'N',
	    'min'=>$request->min,
	    'max'=>$request->max,
	    'garanty'=>$request->garanty,
		'clinic_num'=>$request->clinic_id,
		'materiel'=>$request->materiel,
		'typecode'=>$request->typecode,
		'offre'=>$request->offre,
		'discount'=>$request->discount,
		'nblot'=>$request->nblot,
		'dexpiry'=>$request->dexpiry,
		'location_id'=>$request->location_id,
		'main_id'=>$request->main_id,
		'sub_id'=>$request->sub_id,
		'detailsasset'=>$request->detailsasset,
		'manufacturer'=>$request->manufacturer,
		'modele'=>$request->modele,
		'serial'=>$request->serial,
		'madeof'=>$request->madeof,
		'dcolor'=>$request->dcolor,
		'invdate'=>$request->invdate,
		'capdate'=>$request->capdate,
		'specdetails'=>$request->specdetails,
		'pricelbp'=>$request->pricelbp,
		'priceusd'=>$request->priceusd,
		'deplbp'=>$request->deplbp,
		'rate'=>$request->rate,
		'grosslbp'=>$request->grosslbp,
		'netlbp'=>$request->netlbp,
		'acclbp'=>$request->acclbp,
		'lastdatedep'=>$request->lastdatedep,
		'descriptionacct'=>$request->descriptionacct,	
		'accumlateacct'=>$request->accumlateacct,	
		'descriptiontype'=>$request->descriptiontype,			
		'status'=>$request->status1,			
		'remark'=>$request->remark,			
		'insurance'=>$request->insurance,			
		'lastmaintenance'=>$request->lastmaintenance,			
		'inexpdate'=>$request->inexpdate,			
		'softlicence'=>$request->softlicence,			
		'username'=>$request->username1,
		'mainacct'=>$request->mainacct,
		'deprate'=>$request->deprate,
		'assetlifey'=>$request->assetlifey,
		'assetlifea'=>$request->assetlifea,
		'locationd'=>$request->locationd,		
	   	'user_id'=>$uid,
		'active'=>'O'
	    ]); 
		 $msg=__('Updated successfully'); 
     }
if(isset($request->barcode)){
	
  }else{
 	    DB::table("tbl_inventory_items_fournisseur")->where('id', $request->fournisseur)->increment('item_seq');

  }

//return back()->with('lang',$lang)->with('selecttype','1')->with('lunette_id',$newitems->id);
if ($request->typesave!='M'){
	 
	 $item= TblInventoryItems::find($newitems);
	 if(isset($item)){ 
	 if($lang=="fr"){
	 $sql="select a.collection_id as id,a.name from tbl_inventory_fournisseur_collection a where a.clinic_num=".$item->clinic_num." and a.fournisseur_id=".$item->fournisseur." and a.active='O' order by a.id";
	 }else{
	 $sql="select a.collection_id as id,a.name_eng as name from tbl_inventory_fournisseur_collection a where a.clinic_num=".$item->clinic_num." and a.fournisseur_id=".$item->fournisseur." and a.active='O' order by a.id";

	 }
	 $collection=DB::select(DB::raw("$sql"));
	 Alert::toast($msg,'success');
	 return redirect()->route('inventory.items.lunette',[$lang,$request->gen_type,$newitems])->with('item',$item)->with('collection',$collection);	
	 }else{
	 return back();
	 }
}else{
 $msg=__('Item Saved Success');
	return response()->json(['success'=>$msg]);
}	
}
public function collection_code($lang,Request $request) 
{
	
		$html='<option value="">'.__("Undefined").'</option>';;
		$item= TblInventoryItems::find($request->id);
		if($lang=="fr"){
		$sql="select a.collection_id as id,a.name from tbl_inventory_fournisseur_collection a where a.clinic_num = ".$item->clinic_num." and a.active='O' order by a.id";
		}else{
		$sql="select a.collection_id as id,a.name_eng as name from tbl_inventory_fournisseur_collection a where a.clinic_num = ".$item->clinic_num." and a.active='O' order by a.id";
		}
	$collection=DB::select(DB::raw("$sql"));
     foreach ($collection as $scollection) {
		 $selected=($scollection->id==$item->brand)?'selected':'';
       $html .= '<option value="' . $scollection->id.'" '.$selected.'>'.$scollection->name .'</option>';
           }
			return response()->json(["html"=>$html]);   
	}
public function generation_code($lang,Request $request) 
    {
		 
	if(	$request->type=='main'){
	if ($request->id!='0' && $request->id!=''){
		$main=TblInventoryAssetMainCategory::where('active','O')->where('id',$request->id)->first();
	
		
	$main_accctnb=$main->accountnb;
	$main_depreciation=$main->depreciation;
	$main_assetlife=$main->asset_life;
	$main_assetage=$main->asset_age;
	}
		return response()->json(["main_accctnb"=>$main_accctnb,"main_depreciation"=>$main_depreciation,"main_assetlife"=>$main_assetlife,"main_assetage"=>$main_assetage]);

	}
if(	$request->type=='location'){
	if ($request->id!='0' && $request->id!=''){
		$location=TblLocation::where('active','O')->where('id',$request->id)->first();
	
		
	$namelocation=$location->name;
	
	}
		return response()->json(["namelocation"=>$namelocation]);

	}
	}
	
public function generation_price($lang,Request $request) 
    {
		switch($request->id){
		case 0:
			$sel_price="0.00";
			break;
	//	case 1:
	//	if ($request->cost_price!=0)
	//	{
	//		$Fournisseur=TblInventoryFormulaPrice::where('active','O')->where('formula_id',$request->id)->first();
	//		$divise=$Fournisseur->divise;
	//		$multiple=$Fournisseur->multiple;
	//		$minus=$Fournisseur->minus;
	//		$plus=$Fournisseur->plus;
	//		$sel_price=(($request->cost_price*$multiple)/$divise)+$plus-$minus;
	//	}else{
	//	$sel_price="0.00";
	//	}
	//	break;
		default:
		if ($request->cost_price!=0)
		{
			$Fournisseur=TblInventoryFormulaPrice::where('active','O')->where('formula_id',$request->id)->where('from_price','<=',$request->cost_price)->where('to_price','>=',$request->cost_price)->first();
			//dd($Fournisseur);
			if (isset($Fournisseur)){
				$divise=$Fournisseur->divise;
				if($divise==0){
					$divise=1;
						}
				$multiple=$Fournisseur->multiple;
				$plus=$Fournisseur->plus;
				$minus=$Fournisseur->minus;
				$sel_price=(($request->cost_price*$multiple)/$divise)+$plus-$minus;
				$sel_price=number_format((float)$sel_price, 2, '.', '');
			}else{
				$divise=1;	
				$multiple=0;
				$plus=0;
				$minus=0;
				$sel_price=(($request->cost_price*$multiple)/$divise)+$plus-$minus;
				$sel_price=number_format((float)$sel_price, 2, '.', '');
			}
		}else{
		$sel_price="0.00";
		}
		break;
		
		}
		
 	return response()->json(["sel_price"=>$sel_price]);
	}
		
public function lenses($lang,Request $request) 

    {
	$item=TblInventoryItems::where('active','O')->first();	
	return view('inventory.items.lenses')->with('item',$item);	
	}		
	
	
public function edit($lang,$id) 
    {
	$item= TblInventoryItems::find($id);	
	$Fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('clinic_num',$item->clinic_num)->where('fournisseur','2')->get();
	$General=TblInventoryItemsFournisseur::where('clinic_num',$item->clinic_num)->where('active','O')->where('fournisseur','1')->get();
	$Depreciation=TblInventoryItemsFournisseur::where('clinic_num',$item->clinic_num)->where('active','O')->where('fournisseur','3')->get();
	$Fixed=TblInventoryItemsFournisseur::where('clinic_num',$item->clinic_num)->where('active','O')->where('fournisseur','4')->get();
	$DepreciationType=TblCategoriesTypes::where('active','O')->get();


	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$FromFacility = Clinic::where('id',$item->clinic_num)->where('active','O')->first();
	$iType=TblInventoryItemsTypes::where('id',$item->gen_types)->where('active','O')->get();
	$location=TblLocation::where('active','O')->get();
	$assetm=TblInventoryAssetMainCategory::where('active','O')->get();
	$subcategry=TblInventoryCollectionFournisseur::where('active','O')->get();

	$type=$item->gen_types;
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('fournisseur_type',$item->gen_types)->where('active','O')->orderBy('ord')->get();
    if ($lang=="fr"){
	$collection= DB::table('tbl_inventory_fournisseur_collection')->select('collection_id as id', 'name')->where('fournisseur_id',$item->fournisseur_id)->where('clinic_num',$item->clinic_num)->where('active','O')->orderBy('id')->get();
    $iCategory= DB::table('tbl_inventory_category_types')->select('id', 'name')->where('active','O')->orderBy('id')->get();
	}else{
	$collection= DB::table('tbl_inventory_fournisseur_collection')->select('collection_id as id', 'name_eng as name')->where('fournisseur_id',$item->fournisseur_id)->where('clinic_num',$item->clinic_num)->where('active','O')->orderBy('id')->get();
    $iCategory= DB::table('tbl_inventory_category_types')->select('id', 'name_eng as name')->where('active','O')->orderBy('id')->get();
	
	}
	return view('inventory.items.lunette')->with('item',$item)->with('iCategory',$iCategory)->with('FromFacility',$FromFacility)->with('Fournisseur',$Fournisseur)->with('iType',$iType)->with('Formula',$Formula)->with('lunette_specs',$lunette_specs)->with('lunette_id',$id)->with('type',$type)->with('collection',$collection)->with('location',$location)->with('assetm',$assetm)->with('subcategry',$subcategry)->with('General',$General)->with('Depreciation',$Depreciation)->with('Fixed',$Fixed)->with('DepreciationType',$DepreciationType);	
	
	}
	
public function destroy($lang,Request $request) 
    {
	$id=$request->id;
    $type=$request->type;
    switch($type){
		case 'activate':
		  	TblInventoryItems::where('id',$id)->update(['active'=>'O','user_id'=>auth()->user()->id]);
			$msg= __('Activated successfully');
		break;
		case 'inactivate':
		  	TblInventoryItems::where('id',$id)->update(['active'=>'N','user_id'=>auth()->user()->id]);
			$msg= __('InActivated successfully');
		break;
	}	
    
    
	//	Alert::toast($msg,'success');
	return response()->json(["msg"=>$msg]);
	}
	
//generate pdf for items ,create date: 30-3-2023
public function generate_pdf($lang,Request $request){
  $item = TblInventoryItems::select('tbl_inventory_items.id','f.name as fournisseur','c.name as brand',
                                    'tbl_inventory_items.sel_price','tbl_inventory_items.cost_price',
									'tbl_inventory_items.qty','tbl_inventory_items.sku','tbl_inventory_items.description',
									'tbl_inventory_items.barcode','tbl_inventory_items.clinic_num',
									'tbl_inventory_items.date_reception','tbl_inventory_items.description',
									'tbl_inventory_items.num_facture','cat.name as cat_name',
									'ft.name as ft_name','tbl_inventory_items.qty','tbl_inventory_items.min',
									'tbl_inventory_items.max','tbl_inventory_items.taxable','tbl_inventory_items.garanty',
									'formula.name as formula_name','tbl_inventory_items.materiel',
									'tbl_inventory_items.notes')
           ->leftjoin('tbl_inventory_items_fournisseur as f','f.id','tbl_inventory_items.fournisseur')
           ->leftjoin('tbl_inventory_fournisseur_collection as c','c.id','tbl_inventory_items.brand')
		   ->leftjoin('tbl_inventory_category_types as cat','cat.id','tbl_inventory_items.category')
		   ->leftjoin('tbl_inventory_fournisseur_types as ft','ft.id','tbl_inventory_items.gen_types')
		   ->leftjoin('tbl_inventory_items_formula as formula','formula.id','tbl_inventory_items.formula_id')
           ->where('tbl_inventory_items.id',$request->id)->first();
  
  //dd($item);
  $clinic=Clinic::where('active','O')->where('id',$item->clinic_num)->first();
  $data = ['title' => __('Item'),'date' => date('m/d/Y'),'item'=>$item,'clinic' => $clinic ]; 
   
			 //$pdf_path = public_path('/custom/inventory/items');
			 $pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
                        -> loadView('inventory.items.itemPDF', $data);
			 
		     $pdf->output();
             $dom_pdf = $pdf->getDomPDF();
             $canvas = $dom_pdf->get_canvas();
            $canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0)); 
           
            /*$uid=auth()->user()->id;
           
            if (!file_exists( $pdf_path)) {
                    mkdir($pdf_path, 0775, true);
            }*/
            return $pdf->stream();
                        
             //delete old existing files
             //$files = glob($pdf_path.'*'); // get all file names
               // foreach($files as $file){ // iterate files
                 // if(is_file($file)) {
                   // unlink($file); // delete file
                 // }
                //}
             //create new pdf in directory
             //$name=date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
             //$pdf_file = $pdf_path . $name;
      
			 //file_put_contents($pdf_file, $pdf->output());
		     //return response()->download( $pdf_file);			
}

//generate pdf for items ,create date: 3-4-2023
public function generate_item_label($lang,Request $request){
   $item = TblInventoryItems::select('tbl_inventory_items.gen_types','tbl_inventory_items.typecode',
                                    'tbl_inventory_items.sel_price','tbl_inventory_items.description',
									'tbl_inventory_items.barcode','tbl_inventory_items.items_specs',
									'tbl_inventory_items.sku','four.name as supplier_name')         
                            ->join('tbl_inventory_items_fournisseur as four','four.id','tbl_inventory_items.fournisseur')
							->where('tbl_inventory_items.typecode',1)
							->where('tbl_inventory_items.id',$request->id)->first();
  
   $data = ['title' => __('Item label'),'date' => date('m/d/Y'),'item'=>$item]; 
   $customPaper = array(0, 0, 180, 90.141732283 );
   $pdf = PDF::setOptions(['orientation' => 'landscape','defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
                        -> loadView('inventory.items.LabelItemPDF', $data);
   $pdf->setPaper($customPaper);
   return $pdf->stream();
    }	
	
//function to get quantity inventory history for item
public function item_qty_history($lang,Request $request){
	$id = $request->id;
	
	$inventory_qty=0;
	
	
	$sql = "SELECT sum(det.qty) as total_qty,req.clinic_inv_num,ANY_VALUE(req.date_invoice) as date_invoice,
	               ANY_VALUE(CONCAT(pat.first_name,' ',pat.last_name)) as pat_name,
				   ANY_VALUE(supp.name) as supplier_name,ANY_VALUE(req.type) as type,
				   ANY_VALUE(req.typeadjacement) as adj,ANY_VALUE(req.id),ANY_VALUE(req.updated_at) as date_updated 
			FROM tbl_inventory_invoices_details as det
			INNER JOIN tbl_inventory_invoices_request as req ON req.id=det.invoice_id and req.active='O' 
			LEFT JOIN tbl_patients as pat ON pat.id=req.patient_id and pat.status='O'
			LEFT JOIN tbl_inventory_items_fournisseur as supp ON supp.id=req.fournisseur_id and supp.active='O'
			where det.item_code='".$request->id."' and det.status='O' and req.type<>99 
			      and (req.is_warranty<>'Y'or req.is_warranty IS NULL) 
			GROUP BY req.clinic_inv_num,req.date_invoice
			HAVING total_qty <>0
			ORDER BY req.date_invoice desc,req.clinic_inv_num 
			";
	
	$data = DB::select(DB::raw("$sql"));
 	
    $sql ="SELECT sum(det.qty) as total_qty,IF(req.cmd_cl='Y','".__("Cl. Order")."','".__("GF. Order")."') as clinic_inv_num,ANY_VALUE(req.date_cmd) as date_invoice,
	               ANY_VALUE(CONCAT(pat.first_name,' ',pat.last_name)) as pat_name,ANY_VALUE(req.id),ANY_VALUE(req.updated_at) as date_updated
			FROM tbl_inventory_invoices_details as det
			INNER JOIN tbl_inventory_invoices_request as req ON req.id=det.cmd_id and req.active='O' 
			INNER JOIN tbl_patients as pat ON pat.id=req.patient_id and pat.status='O'
			where det.item_code='".$request->id."' and det.status='O' and req.type IS NULL and req.clinic_inv_num IS NULL 
			      and (req.is_warranty<>'Y'or req.is_warranty IS NULL) and (req.is_estimation<>'Y'or req.is_estimation IS NULL) 
			GROUP BY req.id,req.date_cmd
			HAVING total_qty <>0
			ORDER BY req.id desc,req.date_cmd desc";
			
	$data_cmd = DB::select(DB::raw("$sql"));
	
	$sql ="SELECT sum(det.dqty) as total_qty,ANY_VALUE(req.date_approve) as date_invoice,ANY_VALUE(req.id),ANY_VALUE(req.updated_at) as date_updated
			FROM  tbl_inventory_materials_details as det
			INNER JOIN tbl_inventory_materials_request as req ON req.id=det.invoice_id and req.approve='Y' and req.active='O'
			where det.item_id='".$request->id."' 
			GROUP BY req.id,req.date_approve
			HAVING total_qty <>0
			ORDER BY req.id desc,req.date_approve desc
			";	
	$data_audit = DB::select(DB::raw("$sql"));
	
	$item = TblInventoryItems::find($id);
    $item_qty = isset($item)?$item->qty:0;
	

   
   $html='<table id="qty_history_table" class="table table-bordered table-sm nowrap" style="width:100%;"><thead><th>#</th><th>'.__('Qty').'</th><th>'.__('Details').'</th></thead><tbody>';
   $count=0;
   foreach($data as $d){
	   $count++;
	   
	   if($d->type==1){
		   $html.='<tr><td>'.$count.'</td><td>+'.$d->total_qty.'</td><td>'.__("Purchase").' : '.$d->clinic_inv_num.'<br/>'.__("Supplier").' : '.$d->supplier_name.'<br/>Date : '.Carbon::parse($d->date_invoice)->format("Y-m-d H:i").'<br/>Last Updated : '.Carbon::parse($d->date_updated)->format("Y-m-d H:i").'</td></tr>'; 
           $inventory_qty+=$d->total_qty;  
	    }
		
		if($d->type==2){
		   $html.='<tr><td>'.$count.'</td><td>-'.$d->total_qty.'</td><td>'.__("Sale").' : '.$d->clinic_inv_num.'<br/>'.__("Patient").' : '.$d->pat_name.'<br/>Date : '.Carbon::parse($d->date_invoice)->format("Y-m-d H:i").'<br/>Last Updated : '.Carbon::parse($d->date_updated)->format("Y-m-d H:i").'</td></tr>'; 
           $inventory_qty-=$d->total_qty;  
	    }
		
		if($d->type==3){
		   $html.='<tr><td>'.$count.'</td><td>+'.$d->total_qty.'</td><td>'.__("Return Patient").' : '.$d->clinic_inv_num.'<br/>'.__("Patient").' : '.$d->pat_name.'<br/>Date : '.Carbon::parse($d->date_invoice)->format("Y-m-d H:i").'<br/>Last Updated : '.Carbon::parse($d->date_updated)->format("Y-m-d H:i").'</td></tr>'; 
           $inventory_qty+=$d->total_qty;  
	    }
		
		if($d->type==4){
		   $html.='<tr><td>'.$count.'</td><td>-'.$d->total_qty.'</td><td>'.__("Return Supplier").' : '.$d->clinic_inv_num.'<br/>'.__("Supplier").' : '.$d->supplier_name.'<br/>Date : '.Carbon::parse($d->date_invoice)->format("Y-m-d H:i").'<br/>Last Updated : '.Carbon::parse($d->date_updated)->format("Y-m-d H:i").'</td></tr>'; 
           $inventory_qty-=$d->total_qty;  
	    }
		
		if($d->type==5){           
		   //minus
		   switch($d->adj){
		   case 2:
		   $html.='<tr><td>'.$count.'</td><td>-'.$d->total_qty.'</td><td>'.__("Adjustment").' : '.$d->clinic_inv_num.'<br/>Date : '.Carbon::parse($d->date_invoice)->format("Y-m-d H:i").'<br/>Last Updated : '.Carbon::parse($d->date_updated)->format("Y-m-d H:i").'</td></tr>'; 
		   $inventory_qty-=$d->total_qty;
           break;
		   //Add
		   case 1:
		   $html.='<tr><td>'.$count.'</td><td>+'.$d->total_qty.'</td><td>'.__("Adjustment").' : '.$d->clinic_inv_num.'<br/>Date : '.Carbon::parse($d->date_invoice)->format("Y-m-d H:i").'<br/>Last Updated : '.Carbon::parse($d->date_updated)->format("Y-m-d H:i").'</td></tr>'; 
		   $inventory_qty+=$d->total_qty;
           break;  		   
		   }		   
	    }
   
   }
   
  foreach($data_cmd as $d){
	  $count++;
	   //commands
		   $html.='<tr><td>'.$count.'</td><td>-'.$d->total_qty.'</td><td>'.$d->clinic_inv_num.'<br/>'.__("Patient").' : '.$d->pat_name.'<br/>Date : '.Carbon::parse($d->date_invoice)->format("Y-m-d H:i").'<br/>Last Updated : '.Carbon::parse($d->date_updated)->format("Y-m-d H:i").'</td></tr>'; 
           $inventory_qty-=$d->total_qty;   
	  
  }
   
   foreach($data_audit as $d){
	   $count++;
	   $html.='<tr><td>'.$count.'</td><td>'.$d->total_qty.'</td><td>'.__("Inventory audit").'<br/>Date : '.Carbon::parse($d->date_invoice)->format("Y-m-d H:i").'<br/>Last Updated : '.Carbon::parse($d->date_updated)->format("Y-m-d H:i").'</td></tr>'; 
       $inventory_qty+=$d->total_qty;   
   }
   
   $html.='</tbody><tfoot><th>'.__("Total qty from invoices").' : </th><th>'.$inventory_qty.'</th><th></th></tfoot></table>';
   
   $html1='<span class="ml-2"><b>'.__("Current Qty in inventory").' : </b></span><span class="ml-2">'.$item_qty.'</span>';
   
  return response()->json(["html"=>$html,"html1"=>$html1]);

}

function SaveAdjacement($lang,Request $request){
$price=0.00;
$stotal=0.00;
$totalf=0.00;
$tdiscount=0.00;
$Nbalance=0.00;
$gst=0;
$qst=0;
$selectpro='0';
$selectpatient='0';
$ref='0';
$invoice_rq='';
$user_id = auth()->user()->id;
$clinic_id = auth()->user()->clinic_num; 
$sqlReq = "insert into tbl_inventory_invoices_request(fournisseur_id,patient_id,type,date_invoice,".
	              "date_due,reference,qst,gst,notes,total,discount,inv_balance,typeadjacement,clinic_num,gstock,user_id,active)".
                  "values('".$selectpro."','".
				            $selectpatient."','".
							$request->selecttype."','".
							$request->adjacement_date_val."','".
							$request->adjacement_date_val."','".
							$ref."','".
							$qst."','".
							$gst."','".
							$invoice_rq."','".
							$totalf."','".
							$tdiscount."','".
							$Nbalance."','".
							$request->selectadj."','".
							$clinic_id."','".
							$request->gstock."','".
							$user_id."','O')";
  	     
		DB::select(DB::raw("$sqlReq"));
        $last_id=DB::getPdo()->lastInsertId(); 	
		$FacInventory = TblInventoryInvSerials::where('clinic_num',$clinic_id)->first();
		
    //switch($request->selecttype){			  
		//case "1":
		    $SerieFacInventory = $FacInventory->adj_serial_code;
			$SeqFacInventory = $FacInventory-> adj_sequence_num ;
			$reqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
		    TblInventoryInvSerials::where('clinic_num',$clinic_id)->update([
				                  'adj_sequence_num' => $SeqFacInventory+1
								  ]);
		//	break;	
	//	case "2":
	//	    $SerieFacInventory = $FacInventory->adj_serial_code;
	//		$SeqFacInventory = $FacInventory-> adj_sequence_num ;	
	//		$reqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
	//	    TblInventoryInvSerials::where('clinic_num',$clinic_id)->update([
	//			                  'adj_sequence_num' => $SeqFacInventory+1
	//							  ]);
	//	break;
			
	//	}							
	
	TblInventoryInvoicesRequest::where('id',$last_id)->update([
				                  'clinic_inv_num'=>$reqID	
								  ]);			
   $STypes = TblInventoryTypes::where('id',$request->selecttype)->first();
   $SignTypes = $STypes->sign ;
   $item_id = TblInventoryItems::where('id',$request->id)->where('active','O')->first();
   $code = $item_id->id;
   $descrip = $request->descrip;
   $quantity=$request->qty;
   $gquantity=$request->gqty;
	if($quantity<=0){
    $quantity=$gquantity;
	}
   $price ="0.00";
   $discount = "0.00";
   $total ="0.00";
   $date_exp = $request->adjacement_date_val;
   $taxable="N";
   $typediscount="0";
   $formulaid="1";  
   $sel_price="0.00";
   $initprice="0.00";

 
   $sqlInsertF = "insert into tbl_inventory_invoices_details(invoice_id,ref_invoice,item_code,item_name,qty,price,discount,total,date_exp,tdiscount,tax,formula_id,initprice,sel_price,notes,status,user_id,active) values('".
                 $last_id."','".
				 $ref."','".
				 $code."','".
				 $descrip."','".
				 $quantity."','".
				 $price."','".
				 $discount."','".
				 $total."','".
				 $date_exp."','".
				 $typediscount."','".
				 $taxable."','".
				 $formulaid."','".
				 $initprice."','".
				 $sel_price."','".				 
				 $request->invoice_rq."','O','".
				 $user_id."','O')";
	DB::select(DB::raw("$sqlInsertF"));
	
 

$SQty = TblInventoryItems::where('id',$code)->first();
if($gquantity<=0){
$QtyItem = $SQty->qty;	
}else{
$QtyItem = $SQty->gqty;		
}

if ($request->selectadj=='1'){
   $qty0=$QtyItem+$quantity;
}else{
  $qty0=$QtyItem-$quantity;
}
$qty=strval($qty0);

if($gquantity<=0){
 $UpdateQty="update tbl_inventory_items set qty=".$qty." where id='".$code."'";
 $genstock='N';
}else{
 $UpdateQty="update tbl_inventory_items set gqty=".$qty." where id='".$code."'";
 $genstock='Y';

}
 DB::select(DB::raw("$UpdateQty"));	


 $msg=__('Adjustment Saved Successfully');
	return response()->json(['success'=>$msg,'genstock'=>$genstock,'qty'=>$qty,'gqty'=>$qty]);
		  
}  

public function generation_sumqtyprice_old($lang,Request $request) 
    {
    if(isset($request->filter_type) && $request->filter_type!=""){
	$tsq =DB::table('tbl_inventory_items')->where('qty','>', '0')->where('active', 'O')->where('typecode', '1')->where('gen_types', $request->filter_type)->sum('qty');
	
	$tamount = DB::table('tbl_inventory_items')
    ->where('active', 'O')->where('typecode', '1')->where('qty','>', '0')->where('gen_types', $request->filter_type)
    ->sum(DB::raw('qty * cost_price'));	 
	}else{
	$tsq =DB::table('tbl_inventory_items')->where('qty','>', '0')->where('active', 'O')->where('typecode', '1')->sum('qty');
	
	$tamount = DB::table('tbl_inventory_items')
    ->where('active', 'O')->where('typecode', '1')->where('qty','>', '0')
    ->sum(DB::raw('qty * cost_price'));	 
		   }
	 	return response()->json(["tamount"=>$tamount,"tsq"=>$tsq]);

	}

public function generation_sumqtyprice($lang,Request $request) 
    {
   			   
		   $filter_category="";
           	if(isset($request->filter_category) && $request->filter_category!=""){
			 $filter_category = "and tbl_inventory_items.category='".$request->filter_category."'";  
		   }
		   
		    $filter_type="";
           	if(isset($request->filter_type) && $request->filter_type!=""){
			 $filter_type = "and tbl_inventory_items.gen_types='".$request->filter_type."'";  
		   }
		   
		 $filter_fournisseur="";
           	if(isset($request->filter_fournisseur) && $request->filter_fournisseur!=""){
			 $filter_fournisseur = "and tbl_inventory_items.fournisseur='".$request->filter_fournisseur."'";  
		   }
		   
		 $sqlQty="select sum(tbl_inventory_items.qty) as sqty
				  from tbl_inventory_items
				  where 1=1 and tbl_inventory_items.qty>0 and tbl_inventory_items.active='O' and tbl_inventory_items.typecode='1'  
				   ". $filter_type." ".$filter_fournisseur." ".$filter_category."
				  ";
		  $SQty= DB::select(DB::raw("$sqlQty")); 
		 $sqlAmount="select sum(tbl_inventory_items.qty*tbl_inventory_items.cost_price) as sumprice
				  from tbl_inventory_items
				  where 1=1 and tbl_inventory_items.qty>0 and tbl_inventory_items.active='O' and tbl_inventory_items.typecode='1'  
				   ". $filter_type." ".$filter_fournisseur." ".$filter_category."
				  ";
		  $SAmount= DB::select(DB::raw("$sqlAmount")); 
		 $tamount = $SAmount[0]->sumprice;
		 $tsq = $SQty[0]->sqty;
	 	return response()->json(["tamount"=>$tamount,"tsq"=>$tsq]);

	}
	
public function loadOtherItems($lang,Request $request){
			
	$search = $request->input('q');
	$code = DB::table('tbl_inventory_items as a')
	        ->select('a.id as id',DB::raw("CONCAT(a.description,'(Ref:',a.sku,'),Lot Nb:',a.nblot,',Expiray Date:',a.dexpiry) as text"))
			->where('a.active','O');

   if(isset($request->supplier) && $request->supplier !='0'){
		$supplier= $request->supplier;
		$code = $code->where('a.fournisseur',$supplier);
	   }
	
	if($search !='' && $search!=NULL){
	     $code = $code->where('a.description', 'like', '%'.$search.'%');
                               
			 }
    
	$code = $code->orderBy('a.id','desc')->paginate(100);
	
    return response()->json([
        'results' => $code->items(),
        'pagination' => [
            'more' => $code->hasMorePages(),
        ],
    ]);
	
}	
	
public function loadItems($lang,Request $request){
			
	$search = $request->input('q');
    
	$lbl_supp=__('Supplier');
	
	$code = DB::table('tbl_inventory_items as a')
	        ->select('a.id as id',    DB::raw("CONCAT(a.description, ', Lot Nb: ', IFNULL(a.nblot, ''), ', Expiry Date: ', IFNULL(a.dexpiry, 'N/A')) as text"))
    ->where('a.active', 'O');
		
	if($request->item_type !='all_item'){
		if(isset($request->type)){
		$type= $request->type;
		$code = $code->where('a.gen_types',$type);
	    }
		
		switch($request->item_type){
			case 'discount': 
			      $code = $code->where('a.typecode',2); break;
			case 'rlens': 
			case 'llens':
			case 'blens':
			case 'rclens': 
			case 'lclens':
			case 'bclens':
			      $code = $code->where('a.typecode','<>',2); break;
		}
		
	   }else{
		if(isset($request->supplier) && $request->supplier !='0'){
		$supplier= $request->supplier;
		$code = $code->where('a.fournisseur',$supplier);
	   }
	}
	
	if($search !='' && $search!=NULL){
	      $code = $code->where(function($q) use($search){
							 $q->where('a.description', 'like', '%'.$search.'%')
                               ->orWhere('b.name', 'like', '%'.$search.'%');							 
						  });
	}
    
	$code = $code->orderBy('a.id','desc')->paginate(100);
	
    return response()->json([
        'results' => $code->items(),
        'pagination' => [
            'more' => $code->hasMorePages(),
        ],
    ]);
	
}		

}




