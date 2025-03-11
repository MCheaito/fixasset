<?php
/*
* DEV APP
* Created date : 13-6-2023
*  
*
*/

namespace App\Http\Controllers\inventory\formulas;
use App\Http\Controllers\Controller;
use App\Models\TblInventoryItemsFormula;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Doctor;
use App\Models\TblInventoryFormulaPrice;
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


class FormulasController extends Controller

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
	 
	   
	   
	   $all_formula = TblInventoryItemsFormula::where('clinic_num',$FromFacility->id)->orderBy('id','desc')->get();
	   
	  
	   
		if ($request->ajax()) {
		 $filter_formula = "";
		 
		 if(isset($request->filter_formula) && $request->filter_formula!=""){ 
		 $filter_formula =" and id= '".$request->filter_formula."' ";
		 }
		 
		 $filter_status="";
           	if(isset($request->filter_status)){
			 $filter_status = "and active='".$request->filter_status."'";  
		   }
		 $undefined = __('Undefined');
		 
		 $sql="select id,name,active
					 from tbl_inventory_items_formula 
					 where  clinic_num='".$FromFacility->id."'  ".$filter_status." ".$filter_formula."
					 group by id";
		 
		 $formula = DB::select(DB::raw("$sql"));
		 
		return Datatables::of($formula)

                    ->addIndexColumn()

                    ->addColumn('action', function($row){

							$checked = ($row->active=='O')?'checked':'';
							$disabled = ($row->active=='O')?'':'disabled';
							$state = ($row->active=='O')?'inactivate':'activate';
                           //$btn = '<form class="text-center" method="post" action="'.route('inventory.suppliers.destroy',[app()->getLocale(),$row->id]).'">';
                           //$btn = $btn.'<input type="hidden" name="_token" value="'.csrf_token().'" />';
                           $btn ='<a href="'.route('inventory.formulas.editformula',[app()->getLocale(),$row->id]).'"  title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems '.$disabled.'"><i class="far fa-edit text-primary"></i></a>';
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
		
			   
       return view('inventory.formulas.index')->with('all_formula',$all_formula)->with('FromFacility',$FromFacility); 
	}
public function NewFormula($lang,Request $request) 
    {
	$myId=auth()->user()->id;	
	 if(auth()->user()->type==1){
		
				$FromFacility = Clinic::where('id',Session::get('inventory_branch_num'))->where('active','O')->first();
                 $idFacility =$FromFacility->id;
	   
	     }	
		 if(auth()->user()->type==2){
	    $idFacility = auth()->user()->clinic_num; 
		
		$FromFacility = Clinic::where('id',$idFacility)->where('active','O')->first();
		 }		
	

  return view('inventory.formulas.NewFormula')->with(['FromFacility'=>$FromFacility]);
	}	

public function editformula($lang,$id)
    {
		
	$myId=auth()->user()->id;	
	
	
		
	if(auth()->user()->type==1){
		
		
	   		$FromFacility = Clinic::where('id',Session::get('inventory_branch_num'))->where('active','O')->first();
			$idFacility =$FromFacility->id;
	     }	
		 if(auth()->user()->type==2){
	    $idFacility = auth()->user()->clinic_num; 
		
		$FromFacility = Clinic::where('id',$idFacility)->where('active','O')->first();
		 }	
	
	$FormulaDeails=TblInventoryFormulaPrice::where('formula_id',$id)->where('active','O')->get();
	$cptCount= $FormulaDeails->count();	
	$ReqFormula=TblInventoryItemsFormula::where('id',$id)->where('active','O')->first();
	
	return view('inventory.formulas.EditFormula')->with(['ReqFormula'=>$ReqFormula,'FromFacility'=>$FromFacility,'cptCount'=>$cptCount,'FormulaDeails'=>$FormulaDeails]);
		}

	
function addInvoiceRow($lang,Request $request){
  
		
	//  if($request->selectpro=="" || $request->selectpro==NULL){
		//	 $msg=__('Please select your Professional!');
		//	 return response()->json(['warning' =>$msg]);	

			
	//		 }
		
		   $arrTaping = array("id"=>$cpt,
								"fprice"=>$request->fprice,
                                "tprice"=>$request->tprice,
                                 "multiple"=>$request->multiple,
								"divison"=>$request->divison,
								"plus"=>$request->plus,
								"minus"=>$request->minus,
								);
								
	
				  
			$msg=__('Added Success');
				return response()->json(['success' =>$msg,'arrTaping'=>$arrTaping]);	
  
    }	
function SaveFormula($lang,Request $request){

			

		
$someArray = [];
$someArray=json_decode($request->data,true); 
//$i=1;
//dd(json_decode($request->data,true));
$result='';
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;

if ($request->status=='M'){
$ReqRequest=TblInventoryItemsFormula::where('id',$request->formula_id)->where('active','O')->first();

		 $InvoiceDetails= TblInventoryFormulaPrice::where('formula_id',$request->formula_id)->where('active','O')->get();
		 $sqlReq = "delete from tbl_inventory_formula_price  where formula_id=".$request->formula_id;   			   
		 DB::select(DB::raw("$sqlReq"));
		 
				
		$last_id=$request->formula_id;
			
		$sqlRequpdate = "update tbl_inventory_items_formula set 
						  name='".$request->description."',
						  user_id='".$user_id."',
						  active='O' where id=". $last_id;
							   
				DB::select(DB::raw("$sqlRequpdate"));		
		$reqID=$last_id; 			
			

}else{
	
	   $sqlReq = "insert into tbl_inventory_items_formula(name,clinic_num,user_id,active)".
                  "values('".$request->description."','".
				           	$request->clinic_id."','".
							$user_id."','O')";
  	     
		DB::select(DB::raw("$sqlReq"));
        $last_id=DB::getPdo()->lastInsertId(); 
		$reqID=$last_id;
		

	}					  

foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0 ){
	
   $fprice = $area["FPRICE"];
   $tprice=$area["TPRICE"];
   $division = $area["DIVISION"];
   $multiple = $area["MULTIPLE"];
   $plus = $area["PLUS"];
   $minus = $area["MINUS"];
  
  $sqlInsertF = "insert into tbl_inventory_formula_price(formula_id,from_price,to_price,divise,multiple,plus,minus,user_id,active) values('".
                 $last_id."','".
				 $fprice."','".
				 $tprice."','".
				 $division."','".
				 $multiple."','".
				 $plus."','".
				 $minus."','".
				 $user_id."','O')";
	DB::select(DB::raw("$sqlInsertF"));
	$last_id_details=DB::getPdo()->lastInsertId(); 
 }

}
 $msg=__('Formula Saved Success');
	return response()->json(['success'=>$msg,'reqID'=>$reqID]);
		  
}  
public function destroy($lang,Request $request) 
    {
	$id=$request->id;
    $type=$request->type;
    switch($type){
		case 'activate':
		 	TblInventoryItemsFormula::where('id',$id)->update(['active'=>'O','user_id'=>auth()->user()->id]);
			$msg= __('Activated successfully');

		break;
		case 'inactivate':
		   	TblInventoryItemsFormula::where('id',$id)->update(['active'=>'N','user_id'=>auth()->user()->id]);
			$msg= __('InActivated successfully');
		break;
	}
    
	
	//Alert::toast($msg,'success');
	//return back();
	return response()->json(['msg'=>$msg]);
	}




}	