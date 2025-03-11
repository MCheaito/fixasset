<?php
/*
* DEV APP
* Created date : 23-6-2023
*  
*
*/

namespace App\Http\Controllers\inventory\materials;
use App\Http\Controllers\Controller;
use App\Models\TblInventoryItemsFormula;
use App\Models\TblInventoryFormulaPrice;
use App\Models\TblInventoryMaterialsDetails;
use App\Models\TblInventoryMaterialsRequest;
use App\Models\TblBillRate;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Doctor;
use App\Models\TblInventoryItems;

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


class MaterialsController extends Controller

{
    public function __construct()
    {
        $this->middleware('auth');
    }



public function index($lang,Request $request){
	
	$myId=auth()->user()->id;	
	  if(auth()->user()->type==1){
	    $branch = Clinic::where('id',Session::get('inventory_branch_num'))->where('active','O')->first();
      }
	 if(auth()->user()->type==2){
	    $idclinic = auth()->user()->clinic_num; 
	    $branch = Clinic::where('id',$idclinic)->where('active','O')->first();
	 }
	 
	 $all_materials = TblInventoryMaterialsRequest::where('clinic_num',$branch->id)->orderBy('id','desc')->get();

		if ($request->ajax()) {
		  
		 $undefined = __('Undefined');
		 
		 $data = TblInventoryMaterialsRequest::where('clinic_num',$branch->id);
			if(isset($request->filter_status)){
					$data = $data->where('active',$request->filter_status);
				}
		 
		 	 if(isset($request->filter_approve)){
			    $data = $data->where('approve',$request->filter_approve);
			 }
			 
			 if(isset($request->filter_materials) && $request->filter_materials!=""){ 
			  $data = $data->where('id',$request->filter_materials);
			 }
		 
		    $data = $data->groupBy('id')->orderBy('id','desc');
				 
		return Datatables::of($data)

                    ->addIndexColumn()

                    ->addColumn('action', function($row){

							$checked = ($row->active=='O')?'checked':'';
							$disabled = ($row->active=='O')?'':'disabled';
							$state = ($row->active=='O')?'inactivate':'activate';
					        $btn ='<a href="'.route('inventory.materials.editmaterials',[app()->getLocale(),$row->id]).'"  title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editItems '.$disabled.'"><i class="far fa-edit text-primary"></i></a>';
                            $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';
						    return $btn;

                         })

                    ->rawColumns(['action'])
	                ->make(true);

   	    }
		
			   
 return view('inventory.materials.index')->with('all_materials',$all_materials)->with('branch',$branch);
}

public function NewMaterials($lang,Request $request) 
{
	$myId=auth()->user()->id;	
	    if(auth()->user()->type==1){
		
				 $branch = Clinic::where('id',Session::get('inventory_branch_num'))->where('active','O')->first();
                 $idclinic =$branch->id;
	   
	     }	
		 
		 if(auth()->user()->type==2){
	      $idclinic = auth()->user()->clinic_num; 
		
		  $branch = Clinic::where('id',$idclinic)->where('active','O')->first();
	 
		 }		
	 
	 $sqlCode = "select a.id,a.description as name,b.name as namefournisseur from tbl_inventory_items a,tbl_inventory_items_fournisseur b where a.fournisseur=b.id and a.active='O' order by a.id desc";
     $code= DB::select(DB::raw("$sqlCode"));
	
	 $rates=TblBillRate::where('status','O')->get();
	 
	 if($request->ajax()){
		 	 
		 $data= collect();
		 if(isset($request->invoice_id) && $request->invoice_id !='0'){
		 		 $id = $request->invoice_id;
				 $data=TblInventoryMaterialsDetails::where('active','O')->where('invoice_id',$id)->get();
				 //dd($data);
	     
		 }
		
		return Datatables::of($data)
		       ->addIndexColumn()
			   ->make(true);
		 
	    }

  return view('inventory.materials.NewMaterials')->with(['branch'=>$branch,'code'=>$code,'rates'=>$rates]);
	}	

public function editmaterials($lang,$id,$edit=NULL,Request $request)
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
	$MaterialsRequest=TblInventoryMaterialsRequest::where('id',$id)->where('active','O')->first();
	$MaterialsDeails=TblInventoryMaterialsDetails::where('invoice_id',$id)->where('active','O')->get();
	$cpt= $MaterialsDeails->count();
    $tsq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$id)->sum('qty');
	$tpq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$id)->sum('rqty');
	$tdq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$id)->sum('dqty');
	$tamount = DB::table('tbl_inventory_materials_details')
    ->where('active', 'O')
    ->where('invoice_id', $id)
    ->sum(DB::raw('rqty * price'));
	$ReqMaterials=TblInventoryItemsFormula::where('id',$id)->where('active','O')->first();
	//$sqlCode = "select a.id,a.description as name,b.name as namefournisseur from tbl_inventory_items a,tbl_inventory_items_fournisseur b where a.fournisseur=b.id and a.active='O' order by a.id desc";
    //$code= DB::select(DB::raw("$sqlCode"));
	if($request->ajax()){
		 	 
		 $data= collect();
		 if(isset($request->invoice_id) && $request->invoice_id !='0'){
		 		 $id = $request->invoice_id;
				 $data=TblInventoryMaterialsDetails::where('active','O')->where('invoice_id',$id);
				 
	     
		 }
		
		return Datatables::of($data)
		       ->addIndexColumn()
			   ->make(true);
		 
	    }
	return view('inventory.materials.EditMaterials')->with(['edit'=>$edit,'tpq'=>$tpq,'tsq'=>$tsq,'tdq'=>$tdq,'tamount'=>$tamount,'MaterialsRequest'=>$MaterialsRequest,'ReqMaterials'=>$ReqMaterials,'FromFacility'=>$FromFacility,'cpt'=>$cpt,'MaterialsDeails'=>$MaterialsDeails]);
		}

public function destroy($lang,Request $request) 
    {
	$id=$request->id;
    $type=$request->type;
    switch($type){
		case 'activate':
		 	TblInventoryMaterialsRequest::where('id',$id)->update(['active'=>'O','user_id'=>auth()->user()->id]);
			$msg= __('Activated successfully');

		break;
		case 'inactivate':
		   	TblInventoryMaterialsRequest::where('id',$id)->update(['active'=>'N','user_id'=>auth()->user()->id]);
			$msg= __('InActivated successfully');
		break;
	}
    
	
	//Alert::toast($msg,'success');
	//return back();
	return response()->json(['msg'=>$msg]);
	}
	
	
	
function GetItemsDetails($lang,Request $request){
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
$sqlReq = "insert into tbl_inventory_materials_request(date_invoice,remark,clinic_num,user_id,active)".
          "values('".$request->invoice_date_val."','".
                    $request->comment."','".
                    $request->clinic_id."','".
                    $user_id."','O')";

DB::select(DB::raw("$sqlReq"));

$last_id = DB::getPdo()->lastInsertId();

$dataToInsert = DB::table('tbl_inventory_items')
    ->select('barcode', 'description', 'qty','cost_price','id')
    ->where('active', 'O')
	->where('typecode', '1')	
    ->get()
    ->toArray();

$dataToInsert = array_map(function ($item) use ($last_id) {
    $item->invoice_id = $last_id;
    return (array)$item;
}, $dataToInsert);

foreach ($dataToInsert as $data) {
    DB::table('tbl_inventory_materials_details')->insert([
        'invoice_id' => $data['invoice_id'],
        'item_code' => $data['barcode'],
        'item_name' => $data['description'],
        'qty' => $data['qty'],
		'price' => $data['cost_price'],
		'item_id' => $data['id'],
		'dqty' => -1*$data['qty'],
		'active' =>'O'
    ]);
}	
   
   $ReqInvDetails=TblInventoryMaterialsDetails::where('active','O')->where('invoice_id',$last_id)->get();
   $cpt= $ReqInvDetails->count();
   $tsq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$last_id)->sum('qty');
   $tpq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$last_id)->sum('rqty');
   $tdq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$last_id)->sum('dqty');
   $tamount = DB::table('tbl_inventory_materials_details')
              ->where('active', 'O')
              ->where('invoice_id', $last_id)
              ->sum(DB::raw('dqty * price'));	
 
$cpt = 1;
/*$html1='<thead>
		<tr class="txt-bg text-white text-center">
		<th scope="col" style="font-size:16px;">'.__("#").'</th>
		<th scope="col" style="font-size:16px;">'.__("BarCode").'</th>
		<th scope="col" style="font-size:16px;">'.__("Item name").'</th>
		<th scope="col" style="font-size:16px;">'.__("Stock Qty").'</th>
		<th scope="col" style="font-size:16px;">'.__("Phys Qty").'</th>
		<th scope="col" style="font-size:16px;">'.__("Diff Qty").'</th>
		<th scope="col"></th>
		<th scope="col" style="font-size:16px;display:none;">'.__("item id").'</th>
		</tr>
		</thead>
		<tbody>';
foreach ($ReqInvDetails as $sReqInvDetails) {
	
		$DiffQty=$sReqInvDetails->dqty;
		$id_jqty= ' onchange=ChangePhysQty(this,"jqty'.$cpt.'") oninput=ChangePhysQty(this,"jqty'.$cpt.'")';
		$html1 .='<tr>
		<td>'.$cpt.'</td>
		<td>'.$sReqInvDetails->item_code.'</td>
		<td>'.$sReqInvDetails->item_name.'</td>
		<td>'.$sReqInvDetails->qty.'</td>
		<td><input type="text" class="form-control" size="3"  id="jqty'.$cpt.'" value="0"'.$id_jqty.' ></td>
		<td><input type="text" class="form-control" size="3"  id="dqty'.$cpt.'" value="'.$DiffQty.'" disabled ></td>
	    <td><input type="button" class="btn btn-danger" data-type="minus" id="MinusItem'.$cpt.'" value=" - " style="border-radius:50%;" onclick="MinusOne(this)"  />
		<input type="button" class="btn btn-success" data-type="plus" id="AddItem'.$cpt.'" value=" + " style="border-radius:50%;" onclick="AddOne(this)"  /></td>
		<td style="display:none;">'.$sReqInvDetails->item_id.'</td>
		</tr>';
$cpt++;
}

  $html1 .='</tbody>';*/
 $loc = route('inventory.materials.editmaterials',[app()->getLocale(),$last_id,'in']);
return response()->json(['loc'=>$loc]);
	
}	

function fillStockNb($lang,Request $request){
	    $txtCode=$request->selectcode;
	    $Price = TblInventoryItems::where('id',$txtCode)->first();
		$NbItemStock=$Price->qty;
		$barcode=$Price->barcode;
        $ReqInvDetails=TblInventoryMaterialsDetails::where('active','O')->where('invoice_id',$request->invoice_id)->where('item_id',$txtCode)->first();
		$rqty=isset($ReqInvDetails->rqty)?$ReqInvDetails->rqty:0;
return response()->json(['NbItemStock'=>$NbItemStock,'rqty'=>$rqty]);
		  }   
		  
function getDatatableMaterials($lang,Request $request){
	
	    
	  //$MaterialsDetails=TblInventoryMaterialsDetails::where('invoice_id',$request->invoice_id)->where('active','O')->get();
	  //$cpt= $MaterialsDetails->count();
	  //$tsq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$request->invoice_id)->sum('qty');
	//$tpq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$request->invoice_id)->sum('rqty');
	//$tdq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$request->invoice_id)->sum('dqty');
	//$tamount = DB::table('tbl_inventory_materials_details')
    //->where('active', 'O')
    //->where('invoice_id', $request->invoice_id)
    //->sum(DB::raw('rqty * price'));
//$cpt = 1;
/*$html1='<thead>
		<tr class="txt-bg text-white text-center">
		<th scope="col" style="font-size:16px;">'.__("#").'</th>
		<th scope="col" style="font-size:16px;">'.__("BarCode").'</th>
		<th scope="col" style="font-size:16px;">'.__("Item name").'</th>
		<th scope="col" style="font-size:16px;">'.__("Stock Qty").'</th>
		<th scope="col" style="font-size:16px;">'.__("Phys Qty").'</th>
		<th scope="col" style="font-size:16px;">'.__("Diff Qty").'</th>
		<th scope="col"></th>
		<th scope="col" style="font-size:16px;display:none;">'.__("item id").'</th>
		</tr>
		</thead>
		<tbody>';
foreach ($MaterialsDetails as $sMaterialsDeails) {
$id_jqty= ' onchange=ChangePhysQty(this,"jqty'.$cpt.'") oninput=ChangePhysQty(this,"jqty'.$cpt.'")';
		$html1 .='<tr>
		<td>'.$cpt.'</td>
		<td>'.$sMaterialsDeails->item_code.'</td>
		<td>'.$sMaterialsDeails->item_name.'</td>
		<td>'.$sMaterialsDeails->qty.'</td>
		<td><input type="text" class="form-control" size="3"  id="jqty'.$cpt.'" value="'.$sMaterialsDeails->rqty.'"'.$id_jqty.' disabled ></td>
		<td><input type="text" class="form-control" size="3"  id="dqty'.$cpt.'" value="'.$sMaterialsDeails->dqty.'" disabled ></td>
		 <td><input type="button" class="btn btn-danger" data-type="minus" id="MinusItem'.$cpt.'" value=" - " style="border-radius:50%;" onclick="MinusOne(this)"  />
		<input type="button" class="btn btn-success" data-type="plus" id="AddItem'.$cpt.'" value=" + " style="border-radius:50%;" onclick="AddOne(this)"  /></td>
		<td style="display:none;">'.$sMaterialsDeails->item_id.'</td>
		</tr>';
$cpt++;
}

  $html1 .='</tbody>';*/
//return response()->json(['tpq'=>$tpq,'tsq'=>$tsq,'tdq'=>$tdq,'tamount'=>$tamount]);
		  }
		  
function updateQtyRow($lang,Request $request){
	$txtCode=$request->code;
	$quantity=$request->jqty;
	if ($request->barcode=="Y"){
    $Price = TblInventoryItems::where('barcode',$txtCode)->first();
	}else{
	$Price = TblInventoryItems::where('id',$txtCode)->first();	
	}
	if(!isset($Price)){
		return response()->json(["warning"=>__("Please choose a valid bar code")]);
	}
	
	$sqlRqtyUpdate="update tbl_inventory_materials_details set rqty=rqty+".$quantity." where invoice_id='".$request->invoice_id."' and item_id='".$Price->id."'";
    DB::select(DB::raw("$sqlRqtyUpdate"));
	//if($Price->qty>=0){
	$sqlRqtyUpdateD="update tbl_inventory_materials_details set dqty=rqty-qty where invoice_id='".$request->invoice_id."' and item_id='".$Price->id."'";
	//}else{
	//$sqlRqtyUpdateD="update tbl_inventory_materials_details set dqty=rqty+qty where invoice_id='".$request->invoice_id."' and item_id='".$Price->id."'";
	//}
	
    DB::select(DB::raw("$sqlRqtyUpdateD"));
 $msgContent=__("Last Item Updated success")." : ".$Price->description." - ".__("Qty")." : ".$request->jqty; 
$ReqInvDetails=TblInventoryMaterialsDetails::where('active','O')->where('invoice_id',$request->invoice_id)
            ->get();
$cpt= $ReqInvDetails->count();	
    $tsq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$request->invoice_id)->sum('qty');
	$tpq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$request->invoice_id)->sum('rqty');
	$tdq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$request->invoice_id)->sum('dqty');
	$tamount = DB::table('tbl_inventory_materials_details')
    ->where('active', 'O')
    ->where('invoice_id', $request->invoice_id)
    ->sum(DB::raw('rqty * price'));	
$cpt = 1;
/*$html1='<thead>
		<tr class="txt-bg text-white text-center">
		<th scope="col" style="font-size:16px;">'.__("#").'</th>
		<th scope="col" style="font-size:16px;">'.__("BarCode").'</th>
		<th scope="col" style="font-size:16px;">'.__("Item name").'</th>
		<th scope="col" style="font-size:16px;">'.__("Stock Qty").'</th>
		<th scope="col" style="font-size:16px;">'.__("Phys Qty").'</th>
		<th scope="col" style="font-size:16px;">'.__("Diff Qty").'</th>
		<th scope="col"></th>
		<th scope="col" style="font-size:16px;display:none;">'.__("item id").'</th>
		</tr>
		</thead>
		<tbody>';
foreach ($ReqInvDetails as $sReqInvDetails) {

		$html1 .='<tr>
		<td>'.$cpt.'</td>
		<td>'.$sReqInvDetails->item_code.'</td>
		<td>'.$sReqInvDetails->item_name.'</td>
		<td>'.$sReqInvDetails->qty.'</td>
		<td><input type="text" class="form-control" size="3"  id="jqty'.$cpt.'" value="'.$sReqInvDetails->rqty.'" disabled ></td>
		<td><input type="text" class="form-control" size="3"  id="dqty'.$cpt.'" value="'.$sReqInvDetails->dqty.'" disabled ></td>
		 <td><input type="button" class="btn btn-danger" data-type="minus" id="MinusItem'.$cpt.'" value=" - " style="border-radius:50%;" onclick="MinusOne(this)"  />
		<input type="button" class="btn btn-success" data-type="plus" id="AddItem'.$cpt.'" value=" + " style="border-radius:50%;" onclick="AddOne(this)"  /></td>
		<td style="display:none;">'.$sReqInvDetails->item_id.'</td>
		</tr>';
$cpt++;
}

  $html1 .='</tbody>';*/
  
 
 $msg= $msgContent;
	return response()->json(['success'=>$msg,'tpq'=>$tpq,'tsq'=>$tsq,'tdq'=>$tdq,'tamount'=>$tamount]);
		  }   

function updateQtyByPlusMinus($lang,Request $request){
	$txtCode=$request->code;
	$rqty=$request->jqty;
	$dqty=$request->dqty;
    $sqlRqtyUpdate="update tbl_inventory_materials_details set rqty=".$rqty.",dqty=".$dqty." where invoice_id='".$request->invoice_id."' and item_id='".$request->code."'";
    DB::select(DB::raw("$sqlRqtyUpdate"));
	if ($request->type=='+1'){
		$msgContent=__("Last Item Increment success")." : ".$request->description." - ".__("Qty")." : 1"; 
	}
	if ($request->type=='-1'){
	$msgContent=__("Last Item Decrement success")." : ".$request->description." - ".__("Qty")." : 1"; 
	}else{
		$msgContent=__("Last Item Modify success")." : ".$request->description." - ".__("Qty")." : ".$rqty; 
	}
	 $tsq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$request->invoice_id)->sum('qty');
	$tpq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$request->invoice_id)->sum('rqty');
	$tdq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$request->invoice_id)->sum('dqty');
	$tamount = DB::table('tbl_inventory_materials_details')
    ->where('active', 'O')
    ->where('invoice_id', $request->invoice_id)
    ->sum(DB::raw('rqty * price'));	
 $msg=__($msgContent);
	return response()->json(['success'=>$msg,'tpq'=>$tpq,'tsq'=>$tsq,'tdq'=>$tdq,'tamount'=>$tamount]);
}

function ApproveInventory($lang,Request $request){
$someArray = [];
$someArray=json_decode($request->data,true); 
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
$invoice_id=$request->invoice_id; 
$comment=$request->comment; 
$approve_date = Carbon::now()->format('Y-m-d H:i');
$sqlRequpdate = "update tbl_inventory_materials_request set 
						  date_approve='".$approve_date."',
						  remark='".$request->comment."',
						  approve='Y',
						  user_id='".$user_id."',
						  active='O' where id=". $invoice_id;
							   
				DB::select(DB::raw("$sqlRequpdate"));		
$ReqInvDetails=TblInventoryMaterialsDetails::where('active','O')->where('invoice_id',$invoice_id)
            ->get();
foreach ($ReqInvDetails as $sReqInvDetails) {
  $sqlDetupdate = "update tbl_inventory_items set 
						  qty='".$sReqInvDetails->rqty."' where id='".$sReqInvDetails->item_id."'";
							   
				DB::select(DB::raw("$sqlDetupdate"));		

 }	 
 $msgContent=__("Finalized");
 
 $msg= $msgContent;
	return response()->json(['success'=>$msg]);
		  }   


function ApproveInventoryAdj($lang,Request $request){
$someArray = [];
$someArray=json_decode($request->data,true); 
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
$invoice_id=$request->invoice_id; 
$comment=$request->comment; 
$approve_date = Carbon::now()->format('Y-m-d H:i');
$sqlRequpdate = "update tbl_inventory_materials_request set 
						  date_approve='".$approve_date."',
						  remark='".$request->comment."',
						  approve='Y',
						  user_id='".$user_id."',
						  active='O' where id=". $invoice_id;
							   
				DB::select(DB::raw("$sqlRequpdate"));
TblInventoryMaterialsDetails::where('invoice_id',$invoice_id)->where('dqty','0')->delete();				
$ReqInvDetails=TblInventoryMaterialsDetails::where('active','O')->where('invoice_id',$invoice_id)
            ->get();
foreach ($ReqInvDetails as $sReqInvDetails) {
  $sqlDetupdate = "update tbl_inventory_items set 
						  qty='".$sReqInvDetails->rqty."' where id='".$sReqInvDetails->item_id."'";
							   
				DB::select(DB::raw("$sqlDetupdate"));		

 }	 
 $msgContent=__("Finalized");
 
 $msg= $msgContent;
	return response()->json(['success'=>$msg]);
		  }   

function deleteMaterials($lang,Request $request){

		
$user_id = auth()->user()->id;

$invoice_id=$request->id;

$state = $request->state;

	TblInventoryMaterialsRequest::where('id',$invoice_id)->delete();
	
	TblInventoryMaterialsDetails::where('invoice_id',$invoice_id)->delete();
	$html1='<thead>
		<tr class="txt-bg text-white text-center">
		<th scope="col" style="font-size:16px;">'.__("#").'</th>
		<th scope="col" style="font-size:16px;">'.__("BarCode").'</th>
		<th scope="col" style="font-size:16px;">'.__("Item name").'</th>
		<th scope="col" style="font-size:16px;">'.__("Stock Qty").'</th>
		<th scope="col" style="font-size:16px;">'.__("Phys Qty").'</th>
		<th scope="col" style="font-size:16px;">'.__("Diff Qty").'</th>
		<th scope="col"></th>
		<th scope="col" style="font-size:16px;display:none;">'.__("item id").'</th>
		</tr>
		</thead>
		<tbody></tbody>';
  	
    $msg=__('Deleted successfully');
	
	return response()->json(['success'=>$msg,'html1'=>$html1]);

}
public function AuditPDF($lang,Request $request){
	return $this->getPDF($request->id);
}	
function getPDF($id){
  $mat = TblInventoryMaterialsRequest::find($id);
  $mat_det = TblInventoryMaterialsDetails::where('active','O')->where('invoice_id',$mat->id)->orderBy('id','desc')->get();
  $clinic=Clinic::find($mat->clinic_num);
  	$tsq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$mat->id)->sum('qty');
	$tpq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$mat->id)->sum('rqty');
	$tdq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$mat->id)->sum('dqty');
	$tamount = DB::table('tbl_inventory_materials_details')
    ->where('active', 'O')
    ->where('invoice_id', $mat->id)
    ->sum(DB::raw('rqty * price'));	
  $data = [
                'title' => __('Inventory Audit'),
                'date' => date('m/d/Y'),
				'mat' => $mat,
				'mat_det'=>$mat_det,
				'tsq'=>$tsq,
				'tpq'=>$tpq,
				'tdq'=>$tdq,
				'tamount'=>$tamount,
                'clinic' => $clinic
                 ]; 
    
            $pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
                        -> loadView('inventory.materials.AuditPDF', $data);
           
		    $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
			return $pdf->stream(); 
   
}	  
public function NewMaterialsAdj($lang,Request $request) 
{
	$myId=auth()->user()->id;	
	    if(auth()->user()->type==1){
		
				 $branch = Clinic::where('id',Session::get('inventory_branch_num'))->where('active','O')->first();
                 $idclinic =$branch->id;
	   
	     }	
		 
		 if(auth()->user()->type==2){
	      $idclinic = auth()->user()->clinic_num; 
		
		  $branch = Clinic::where('id',$idclinic)->where('active','O')->first();
	 
		 }		
	 
	 $sqlCode = "select a.id,a.description as name,b.name as namefournisseur from tbl_inventory_items a,tbl_inventory_items_fournisseur b where a.fournisseur=b.id and a.active='O' order by a.id desc";
     $code= DB::select(DB::raw("$sqlCode"));
	
	 $rates=TblBillRate::where('status','O')->get();
	 
	 if($request->ajax()){
		 	 
		 $data= collect();
		 if(isset($request->invoice_id) && $request->invoice_id !='0'){
		 		 $id = $request->invoice_id;
				 $data=TblInventoryMaterialsDetails::where('active','O')->where('invoice_id',$id)->get();
				 //dd($data);
	     
		 }
		
		return Datatables::of($data)
		       ->addIndexColumn()
			   ->make(true);
		 
	    }

  return view('inventory.materials.NewMaterialsAdj')->with(['branch'=>$branch,'code'=>$code,'rates'=>$rates]);
	}

function GetItemsDetailsAdj($lang,Request $request){
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
$sqlReq = "insert into tbl_inventory_materials_request(date_invoice,remark,clinic_num,user_id,adj,active)".
          "values('".$request->invoice_date_val."','".
                    $request->comment."','".
                    $request->clinic_id."','".
                    $user_id."','Y','O')";

DB::select(DB::raw("$sqlReq"));

$last_id = DB::getPdo()->lastInsertId();

$dataToInsert = DB::table('tbl_inventory_items')
    ->select('barcode', 'description', 'qty','cost_price','id')
    ->where('active', 'O')
	->where('typecode', '1')	
    ->get()
    ->toArray();

$dataToInsert = array_map(function ($item) use ($last_id) {
    $item->invoice_id = $last_id;
    return (array)$item;
}, $dataToInsert);

foreach ($dataToInsert as $data) {
    DB::table('tbl_inventory_materials_details')->insert([
        'invoice_id' => $data['invoice_id'],
        'item_code' => $data['barcode'],
        'item_name' => $data['description'],
        'qty' => $data['qty'],
		'rqty' => $data['qty'],
		'price' => $data['cost_price'],
		'item_id' => $data['id'],
		'dqty' => '0',
		'active' =>'O'
    ]);
}	
   
   $ReqInvDetails=TblInventoryMaterialsDetails::where('active','O')->where('invoice_id',$last_id)->get();
   $cpt= $ReqInvDetails->count();
   $tsq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$last_id)->sum('qty');
   $tpq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$last_id)->sum('rqty');
   $tdq = DB::table('tbl_inventory_materials_details')->where('active', 'O')->where('invoice_id',$last_id)->sum('dqty');
   $tamount = DB::table('tbl_inventory_materials_details')
              ->where('active', 'O')
              ->where('invoice_id', $last_id)
              ->sum(DB::raw('dqty * price'));	
 
$cpt = 1;
/*$html1='<thead>
		<tr class="txt-bg text-white text-center">
		<th scope="col" style="font-size:16px;">'.__("#").'</th>
		<th scope="col" style="font-size:16px;">'.__("BarCode").'</th>
		<th scope="col" style="font-size:16px;">'.__("Item name").'</th>
		<th scope="col" style="font-size:16px;">'.__("Stock Qty").'</th>
		<th scope="col" style="font-size:16px;">'.__("Phys Qty").'</th>
		<th scope="col" style="font-size:16px;">'.__("Diff Qty").'</th>
		<th scope="col"></th>
		<th scope="col" style="font-size:16px;display:none;">'.__("item id").'</th>
		</tr>
		</thead>
		<tbody>';
foreach ($ReqInvDetails as $sReqInvDetails) {
	
		$DiffQty=$sReqInvDetails->dqty;
		$id_jqty= ' onchange=ChangePhysQty(this,"jqty'.$cpt.'") oninput=ChangePhysQty(this,"jqty'.$cpt.'")';
		$html1 .='<tr>
		<td>'.$cpt.'</td>
		<td>'.$sReqInvDetails->item_code.'</td>
		<td>'.$sReqInvDetails->item_name.'</td>
		<td>'.$sReqInvDetails->qty.'</td>
		<td><input type="text" class="form-control" size="3"  id="jqty'.$cpt.'" value="0"'.$id_jqty.' ></td>
		<td><input type="text" class="form-control" size="3"  id="dqty'.$cpt.'" value="'.$DiffQty.'" disabled ></td>
	    <td><input type="button" class="btn btn-danger" data-type="minus" id="MinusItem'.$cpt.'" value=" - " style="border-radius:50%;" onclick="MinusOne(this)"  />
		<input type="button" class="btn btn-success" data-type="plus" id="AddItem'.$cpt.'" value=" + " style="border-radius:50%;" onclick="AddOne(this)"  /></td>
		<td style="display:none;">'.$sReqInvDetails->item_id.'</td>
		</tr>';
$cpt++;
}

  $html1 .='</tbody>';*/
 $loc = route('inventory.materials.editmaterials',[app()->getLocale(),$last_id,'Ad']);
return response()->json(['loc'=>$loc]);
	
}

public function loadMaterials($lang,Request $request){
	  if(auth()->user()->type==1){
	    $branch_id = Session::get('inventory_branch_num');
      }
	 if(auth()->user()->type==2){
	    $branch_id = auth()->user()->clinic_num; 
	 }
	 
	 $all_materials = TblInventoryMaterialsRequest::select('id',DB::raw("IF(adj='Y',CONCAT(date_invoice,' ','".__("Adj")."'),date_invoice) as text"))->where('clinic_num',$branch_id);
	 $search = $request->input('q');
	 if($search !='' && $search!=NULL){
	       $all_materials =  $all_materials->where('date_invoice', 'like', '%'.$search.'%');   
	}
    
	$all_materials =  $all_materials->orderBy('id','desc')->paginate(100);
    
    return response()->json([
        'results' => $all_materials->items(),
        'pagination' => [
            'more' => $all_materials->hasMorePages(),
        ],
    ]);	
	
}		

	
}