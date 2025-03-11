<?php
/*
*
* DEV APP
* Created date : 2
* Created date : 31-3-2023 (generate pdf for label)
*
*/
namespace App\Http\Controllers\accounting;
use App\Http\Controllers\Controller;


use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use App\Models\TblInventoryItemsFournisseur;
use App\Models\TblInventoryItemsFormula;
use App\Models\TblInventoryFormulaPrice;
use App\Models\TblInventoryItemsTypes;
use App\Models\TblInventoryInvoicesRequest;
use App\Models\TblInventoryInvoicesDetails;
use App\Models\TblInventoryDiscountTypes;
use App\Models\TblInventoryItems;
use App\Models\TblInventoryTypes;
use App\Models\TblInventoryPayment;
use App\Models\TblBillPaymentMode;
use App\Models\TblInventoryRemise;
use App\Models\TblBillTax;
use App\Models\TblBillPay;
use App\Models\TblBillRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TblBillRequest;
use App\Models\TblBillDetails;
use App\Models\DoctorSignature;
use App\Models\TblInventoryInvSerials;
use App\Models\TblAccountDetails;
use App\Models\TblAccountRequest;

use Alert;
use DataTables;
use PDF;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Session;
use Image;
use Illuminate\Support\Facades\Mail;
use App\Mail\SettingMailAttach;

class AccountingController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index($lang,Request $request) 
    {
	 
	  
}

public function EditAccounting($lang,$id)
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
		if($lang=="fr"){
		$sqls="select a.id,a.name_eng as name from tbl_account_source a where a.clinic_num = ".$FromFacility->id." and a.status='O' order by a.id";
		}else{
		$sqls="select a.id,a.name_fr as name from tbl_account_source a where a.clinic_num = ".$FromFacility->id." and a.status='O' order by a.id";
		}
	$source=DB::select(DB::raw("$sqls"));	
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->get();	
	$ReqDeails=TblAccountDetails::where('account_id',$id)->where('status','O')->get();
	$cptCount= $ReqDeails->count();	
	$ReqAccount=TblAccountRequest::where('id',$id)->where('active','O')->first();
	
	return view('accounting.EditAccounting')->with(['source'=>$source,'cptCount'=>$cptCount,'FromFacility'=>$FromFacility,'ReqAccount'=>$ReqAccount,'fournisseur'=>$fournisseur,'ReqDeails'=>$ReqDeails]);
		}
		


	
public function NewAccounting($lang,Request $request) 
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
	if ($lang=='fr'){	
  		 $sqlPay = "select id,name_fr as name from tbl_bill_payment_mode where clinic_num='".$idFacility."'  and status='O' order by id desc";
	}else{
	  		 $sqlPay = "select id,name_eng as name from tbl_bill_payment_mode where clinic_num='".$idFacility."'  and status='O' order by id desc";

	}
	
		 $methodepay= DB::select(DB::raw("$sqlPay"));
	if($lang=="fr"){
		$sql="select a.id,a.name from tbl_inventory_collection a where a.clinic_num = ".$FromFacility->id." and a.active='O' order by a.id";
		}else{
		$sql="select a.id,a.name_eng as name from tbl_inventory_collection a where a.clinic_num = ".$FromFacility->id." and a.active='O' order by a.id";
		}
	$collection=DB::select(DB::raw("$sql"));	
	if($lang=="fr"){
		$sqls="select a.id,a.name_eng as name from tbl_account_source a where a.clinic_num = ".$FromFacility->id." and a.status='O' order by a.id";
		}else{
		$sqls="select a.id,a.name_fr as name from tbl_account_source a where a.clinic_num = ".$FromFacility->id." and a.status='O' order by a.id";
		}
	$source=DB::select(DB::raw("$sqls"));	
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->get();	
    $sqlCode = "select id,description as name from tbl_inventory_items where active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlCode"));
	
	$type1=TblInventoryTypes::where('active','O')->first();	
		$sqlP = "select id,name from tbl_inventory_types ";
    $type= DB::select(DB::raw("$sqlP"));  
	$patient=Patient::where('status','O')->get();	
	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->get();
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->where('fournisseur_type','1')->orderBy('ord')->get();
    $iCategory= DB::table('tbl_inventory_category_types')->where('active','O')->where('types','1')->orderBy('id')->get();
    $typediscount=TblInventoryDiscountTypes::where('active','O')->get();
	$rates=TblBillRate::where('status','O')->get();
  return view('accounting.NewAccounting')->with(['source'=>$source,'collection'=>$collection,'type1'=>$type1,'rates'=>$rates,'typediscount'=>$typediscount,'methodepay'=>$methodepay,'fournisseur'=>$fournisseur,'type'=>$type,'type1'=>$type1,
	                                                     'Formula'=>$Formula,'iType'=>$iType,'lunette_specs'=>$lunette_specs,
														 'code'=>$code,'patient'=>$patient,'iCategory'=>$iCategory,'FromFacility'=>$FromFacility]);
	}
function DeleteAccounting ($lang,Request $request){

		
$user_id = auth()->user()->id;

$invoice_id=$request->id;

$state = $request->state;

switch($state){
	case 'activate':
	
	TblAccountRequest::where('id',$invoice_id)->update([
									   'active' => 'O',
									   'user_id' => $user_id 
									]);
	  $msg=__('Activated successfully');
	break;
	case 'inactivate':
	TblAccountRequest::where('id',$invoice_id)->update([
									   'active' => 'N',
									   'user_id' => $user_id 
									]);
	  $msg=__('InActivated successfully');
	break;
}
return response()->json(['success'=>$msg]);
}
	
function fillInvoiceDel($lang,Request $request){
	    
   $descrip0=TblInventoryItems::where('id',$request->code)->first();
	$tax=TblBillRate::where('id',$request->selecttax)->first();
  if($request->taxable=='Y'){
				$taxqst=$tax->tvq;
				$taxgst= $tax->tvs;
		 }else{
				$taxqst=0;
				$taxgst=0; 
		 }	
	     
		 $qst=number_format((float)$taxqst, 3, '.', '');
		 $gst=number_format((float)$taxgst, 3, '.', '');

	return response()->json(['gst'=>$gst,'qst'=>$qst]);
		  }   



function fillTax($lang,Request $request){
	    
$someArray = [];
$someArray=json_decode($request->data,true); 
$qst=0;
$gst=0;
foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0){
   $code = $area["CODE"];
   $total = $area["TOTAL"];
    $descrip0=TblBillAmounts::where('am_code',$code)->first();
	 $tax=TblBillRate::where('id',$request->selecttax)->first();
  if($descrip0->taxable=='Y'){
				$taxqst=$tax->tvq;
				$taxgst= $tax->tvs;
		 }else{
				$taxqst=0;
				$taxgst=0; 
		 }	
	     $qst=$qst+($total*$taxqst)/100;
	     $gst=$gst+($total*$taxgst)/100;
		
 }
}
		 $qst=number_format((float)$qst, 3, '.', '');
		 $gst=number_format((float)$gst, 3, '.', '');

	return response()->json(['gst'=>$gst,'qst'=>$qst]);
		  }   

function fillBarcode($lang,Request $request){
	    $txtCode=$request->barcode;
	    $Price = TblInventoryItems::where('barcode',$txtCode)->first();
		if(isset($Price)){
		  if($Price->typecode=="2")
		   {
		    $sel_price=-1*$Price->sel_price;	
		   }else{
		    $sel_price=$Price->sel_price;		
		   }
		   $item_description=$Price->description;
			if(isset($Price->fournisseur) && $request->inv_type=='sale'){
			  $supplier = TblInventoryItemsFournisseur::find($Price->fournisseur);
			  $item_description.='-'.'('.__('Supplier').':'.$supplier->name.')';	
			}
		   $item_id = $Price->id;
		if($request->fromstock=="Y"){
			$NbItemStock=$Price->gqty;
		}else{
			$NbItemStock=$Price->qty;
		}
			return response()->json(['item_id'=>$item_id,'item_description'=>$item_description,'gen_types'=>$Price->typecode,'Category'=>$Price->category,'Price'=>$sel_price,'NbItemStock'=>$NbItemStock,'tax'=>$Price->taxable,'cost_price'=>$Price->cost_price,'initprice'=>$Price->initprice,'formula_id'=>$Price->formula_id]);
		}else{
			return response()->json(["error"=>__("Please choose a valid bar code")]);
		}
		
		
		}   

function fillPriceInv($lang,Request $request){
	    $txtCode=$request->selectcode;
	    $Price = TblInventoryItems::where('id',$txtCode)->first();
		if($Price->typecode=="2")
		{
		$sel_price=-1*$Price->sel_price;	
		}else{	
		$sel_price=$Price->sel_price;		
		}
		
		
		if($Price->formula_id=="1"){
		$sel_price=$Price->sel_price;	
		}else
		{
			$divise=1;	
			$multiple=0;
			$plus=0;
			$minus=0;	
			// $sqlFormula = "select * from tbl_inventory_formula_price where formula_id='".$Price->formula_id."' and from_price<=".$Price->cost_price." and from_price<=".$Price->cost_price."  and active='O'";
		  //   $Formula= DB::select(DB::raw("$sqlFormula"));
		  $Formula = TblInventoryFormulaPrice::where('formula_id',$Price->formula_id)->where('from_price','<=',$Price->cost_price)->where('to_price','>=',$Price->cost_price)->where('active','O')->first();
			if (isset($Formula)){
				$divise=$Formula->divise;
				if($divise==0){
					$divise=1;
						}
				$multiple=$Formula->multiple;
				$plus=$Formula->plus;
				$minus=$Formula->minus;
				$sel_price=(($Price->cost_price*$multiple)/$divise)+$plus-$minus;
				$sel_price=number_format((float)$sel_price, 3, '.', '');
			}else{
				$divise=1;	
				$multiple=0;
				$plus=0;
				$minus=0;
				$sel_price=(($Price->cost_price*$multiple)/$divise)+$plus-$minus;
				$sel_price=number_format((float)$sel_price, 3, '.', '');
			}
		}
		if($request->fromstock=="Y"){
			$NbItemStock=$Price->gqty;
		}else{
			$NbItemStock=$Price->qty;
		}
	return response()->json(['gen_types'=>$Price->typecode,'Category'=>$Price->category,'Price'=>$sel_price,'NbItemStock'=>$NbItemStock,'tax'=>$Price->taxable,'cost_price'=>$Price->cost_price,'initprice'=>$Price->initprice,'formula_id'=>$Price->formula_id]);
		  }   


function fillInvoiceType($lang,Request $request){
	    $txtType=$request->selecttype;
		
	$type = TblInventoryTypes::where('id',$txtType)->where('active','O')->first();
	return response()->json(['type'=>$type->type]);
		  }   
function addRowAccount($lang,Request $request){
  // alert($this->cpt);
    	 
	  
      
	//   if (is_numeric($request->valqty)!=1){
	//		$msg=__('Please enter numeric value in Qty');
	//		return response()->json(['warning' =>$msg]);	
		
		//}
	
		

     			  
			$msg=__('Added Success');
				return response()->json(['success' =>$msg]);	
  
    }		  
function addInvoiceRow($lang,Request $request){
  // alert($this->cpt);
    	 
	  
      if($request->selectcode=="0" || $request->selectcode==NULL){
		  $msg=__('Please select your Code!');
		  return response()->json(['warning' =>$msg]);	
		
			 }
		if ($request->selecttype=="1"){	 
	  if($request->selectpro=="" || $request->selectpro==NULL){
			 $msg=__('Please select your Professional!');
			 return response()->json(['warning' =>$msg]);	

			
			 }
		
			}
	   if (is_numeric($request->valqty)!=1){
				$msg=__('Please enter numeric value in Qty');
				return response()->json(['warning' =>$msg]);	
		
		}
		if (is_numeric($request->valdiscount)!=1){
			$msg=__('Please enter numeric value in Discount');
				return response()->json(['warning' =>$msg]);	
		
		}
		if (is_numeric($request->valprice)!=1){
				$msg=__('Please enter numeric value in Price');
				return response()->json(['warning' =>$msg]);	
		
		}	
//alert($proUid['type']);
//alert($idFacility['type']);
		$uqst=0.00;
		$ugst=0.00;
	  $price=$request->valprice;
	  $discount=$request->valdiscount;
	  $qty=$request->valqty;
      $cpt=$request->cpt;
	 
	  $exp_date=$request->invoice_date_dexpiry;
	  $tdiscount=$request->tdiscount+$discount; 
	 
	  $total=($qty*$price)-$discount;
	  $totalf=$request->totalf+(($price*$qty)-$discount);
	  
	// if ($totalf<0){
		//		$msg=__('You cannot Discount more than the total price');
		//		return response()->json(['warning' =>$msg]);	
	
	//	}
     $descrip0=TblInventoryItems::where('id',$request->selectcode)->first();
	 $descrip='';
	
  	  $descrip=$descrip0->description ;
     $desripDisc0=TblInventoryDiscountTypes::where('id',$request->typediscount)->first();
	 $desripDisc='';
	
  	  $desripDisc=$desripDisc0->name ;
        //alert($id);
       //alert($this->arrTaping);
	   $arrTaping = array("id"=>$cpt,
								"id_details"=>"",
                                "invoice_id"=>$request->invoice_id,
                                "code"=>$request->selectcode,
                                "descrip"=>$descrip,
                                "qty"=>$qty,
								"price"=>$price,
								"discount"=>$discount,
								"total"=>$total,
								"taxable"=>($request->taxable=='on')?'Y':'N',
								"typediscount"=>$desripDisc,
								"invoice_date_dexpiry"=>$request->invoice_date_dexpiry,
								"sel_price"=>$request->sel_price,
								"initprice"=>$request->initprice,
								"formula_id"=>'1'
								);
								
	
	//alert($this->arrTaping[$this->cpt-1]["id"]);
      //  $this->arrTaping[$this->cpt] = $this->getTapingForm();
		

 // $this->tdiscount=$this->tdiscount+$this->arrTaping[$this->cpt]["discount"];
 //$this->totalf=$this->totalf+($this->arrTaping[$this->cpt]["price"]*$this->arrTaping[$this->cpt]["qty"])-$this->arrTaping[$this->cpt]["discount"];
  	// alert($this->selectTax->getValue());
	
	 if ($request->taxable=='Y'){
			// if ($request->selecttax!="N"){
				  
		 $tax=TblBillRate::where('id',$request->selecttax)->first();
		 //if($descrip0->taxable=='Y'){
				$taxqst=$tax->tvq;
				$taxgst= $tax->tvs; 
				$uqst=($total*$taxqst)/100;
				$ugst=($total*$taxgst)/100;
				$uqst=number_format((float)$uqst, 3, '.', '');
				$ugst=number_format((float)$ugst, 3, '.', '');
				$qst=$request->selectqst+($total*$taxqst)/100;
				$gst=$request->selectgst+($total*$taxgst)/100;
				$qst=number_format((float)$qst, 3, '.', '');
				$gst=number_format((float)$gst, 3, '.', '');
		 }else{
			$taxqst=0;
			$taxgst=0;
			$qst=$request->selectqst+($total*$taxqst)/100;
			$gst=$request->selectgst+($total*$taxgst)/100;
			$qst=number_format((float)$qst, 3, '.', '');
			$gst=number_format((float)$gst, 3, '.', '');			
		 }	
	    
	 //  }
	   //else{
	   //$qst=0;
	   //$gst=0;
	  // }
	 
	 //  $pay=0;
	// if ($request->totalf==0){
	//$balance=$totalf+$qst+$gst-$request->tpay+$request->trefund;
	//	}else{
		  $balance=$totalf+$qst+$gst;
		// $balance=$request->balance+(($price*$qty)-$discount)+$qst+$gst;
		//}
	  	 $balance=number_format((float)$balance, 3, '.', '');
		 $stotal=$totalf+$qst+$gst;
	  	 $stotal=number_format((float)$stotal, 3, '.', '');	
	   	// $balance=$totalf+$qst+$gst- $pay;
		//  $balance=$totalf+$qst+$gst-$tdiscount;
	  	// $balance=number_format((float)$balance, 2, '.', ',');
				  
			$msg=__('Added Success');
				return response()->json(['success' =>$msg,'arrTaping'=>$arrTaping,'stotal'=>$stotal,'tdiscount'=>$tdiscount,'totalf'=>$totalf,'balance'=>$balance,'qst'=>$qst,'gst'=>$gst,'uqst'=>$uqst,'ugst'=>$ugst]);	
  
    }

function SaveAccount($lang,Request $request){

$someArray = [];
$someArray=json_decode($request->data,true); 
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
if ($request->status=='M'){
$ReqRequest=TblAccountRequest::where('id',$request->invoice_id)->where('active','O')->first();
	 $sqlReq = "delete from tbl_account_details  where account_id=".$request->invoice_id;   			   
		 DB::select(DB::raw("$sqlReq"));
		$last_id=$request->invoice_id; 
		
		$sqlRequpdate = "update tbl_account_head set 
						  datein='".$request->datein."',
						  dateout='".$request->dateout."',
						  type='".$request->type."',
						   source='".$request->selectsource."',
						  refer='".$request->refer."',
						  rq='".$request->rq."',
						  clinic_num='".$request->clinic_id."',
						  user_id='".$user_id."',
						  active='O' where id=". $last_id;
   
				DB::select(DB::raw("$sqlRequpdate"));		
				$reqID=$request->reqID;
}else{

		$etamount1="0.000";
		$etamount2="0.000";
		$dtamount1="0.000";
		$dtamount2="0.000";
		$ltamount1="0.000";
		$ltamount2="0.000";
	   $sqlReq = "insert into tbl_account_head(datein,dateout,refer,source,rq,".
	              "type,etamount1,etamount2,ltamount1,ltamount2,dtamount1,dtamount2,clinic_num,user_id,active)".
                  "values('".$request->datein."','".
							$request->dateout."','".
							$request->refer."','".
							$request->selectsource."','".
							$request->rq."','".
							$request->type."','".
							$etamount1."','".
							$etamount2."','".
							$ltamount1."','".
							$ltamount2."','".
							$dtamount1."','".
							$dtamount2."','".
							$request->clinic_id."','".
							$user_id."','O')";
  	     
		DB::select(DB::raw("$sqlReq"));
        $last_id=DB::getPdo()->lastInsertId(); 
	
		$FacInventory = TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->first();
      
			$SerieFacAcc = $FacInventory->acc_serial_code;
			$SeqFacAcc = $FacInventory-> acc_sequence_num ;
			$reqID=trim($SerieFacAcc)."-".($SeqFacAcc+1);
		    TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->update([
				                  'acc_sequence_num' => $SeqFacAcc+1
								  ]);
		TblAccountRequest::where('id',$last_id)->update([
				                  'serial'=>$reqID	
								  ]);		
}
foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0 ){
	 $serial='';
	 $rq='';
    $fcode = $area["FCODE"];
   $fname = html_entity_decode(trim($area["FNAME"]));
   $fcurrency=$area["FCURRENCY"];
   $frate = $area["FRATE"];
   $famount = $area["FAMOUNT"];
   $tcode = $area["TCODE"];
   $tname = html_entity_decode(trim($area["TNAME"]));
   $tcurrency=$area["TCURRENCY"];
   $trate = $area["TRATE"];
   $tamount = $area["TAMOUNT"];
  $sqlInsertF = "insert into tbl_account_details(account_id,serial,admi1,name1,amount1,curency1,prcurren1,admi2,name2,amount2,curency2,prcurren2,notes,status,user_id,active) values('".
                 $last_id."','".
				 $serial."','".
				 $fcode."','".
				 $fname."','".
				 $famount."','".
				 $fcurrency."','".
				 $frate."','".
				 $tcode."','".
				 $tname."','".
				 $tamount."','".
				 $tcurrency."','".
				 $trate."','".
				 $rq."','O','".
				 $user_id."','O')";
	DB::select(DB::raw("$sqlInsertF"));
	$last_id_details=DB::getPdo()->lastInsertId(); 

	
}
}
$sltamount1="0.000";
$sltamount2="0.000";
$dltamount1="0.000";
$dltamount2="0.000";
$eltamount1="0.000";
$eltamount2="0.000";

if($request->status=='M'){
$sltamount1=TblAccountDetails::where('account_id','=',$request->invoice_id)->where('curency1','=','LBP')->where('status','=','O')->sum('amount1');
$sltamount2=TblAccountDetails::where('account_id','=',$request->invoice_id)->where('curency2','=','LBP')->where('status','=','O')->sum('amount2');
$dltamount1=TblAccountDetails::where('account_id','=',$request->invoice_id)->where('curency1','=','USD')->where('status','=','O')->sum('amount1');
$dltamount2=TblAccountDetails::where('account_id','=',$request->invoice_id)->where('curency2','=','USD')->where('status','=','O')->sum('amount2');
$eltamount1=TblAccountDetails::where('account_id','=',$request->invoice_id)->where('curency1','=','EUR')->where('status','=','O')->sum('amount1');
$eltamount2=TblAccountDetails::where('account_id','=',$request->invoice_id)->where('curency2','=','EUR')->where('status','=','O')->sum('amount2');

}else{
$sltamount1=TblAccountDetails::where('account_id','=',$last_id)->where('curency1','=','LBP')->where('status','=','O')->sum('amount1');
$sltamount2=TblAccountDetails::where('account_id','=',$last_id)->where('curency2','=','LBP')->where('status','=','O')->sum('amount2');
$dltamount1=TblAccountDetails::where('account_id','=',$last_id)->where('curency1','=','USD')->where('status','=','O')->sum('amount1');
$dltamount2=TblAccountDetails::where('account_id','=',$last_id)->where('curency2','=','USD')->where('status','=','O')->sum('amount2');
$eltamount1=TblAccountDetails::where('account_id','=',$last_id)->where('curency1','=','EUR')->where('status','=','O')->sum('amount1');
$eltamount2=TblAccountDetails::where('account_id','=',$last_id)->where('curency2','=','EUR')->where('status','=','O')->sum('amount2');
}

$sltamount1=number_format($sltamount1,3,'.','');
$sltamount2=number_format($sltamount2,3,'.','');
$dltamount1=number_format($dltamount1,3,'.','');
$dltamount2=number_format($dltamount2,3,'.','');
$eltamount1=number_format($eltamount1,3,'.','');
$eltamount2=number_format($eltamount2,3,'.','');


$sqlRequpdate = "update tbl_account_head set 
						  ltamount1='".$sltamount1."',
						  ltamount2='".$sltamount2."',
						  etamount1='".$eltamount1."',
						  etamount2='".$eltamount2."',
						  dtamount1='".$dltamount1."',
						  dtamount2='".$dltamount2."',
						  user_id='".$user_id."',
						  active='O' where id=". $last_id;
				DB::select(DB::raw("$sqlRequpdate"));		
$msg=__('Saved Success');
if ($request->status=='M'){		
	return response()->json(['success'=>$msg,'reqID'=>$reqID,'last_id'=>$last_id,	
							'sltamount1'=>$sltamount1,'sltamount2'=>$sltamount2,
							'eltamount1'=>$eltamount1,'eltamount2'=>$eltamount2,
							'dltamount1'=>$dltamount1,'dltamount2'=>$dltamount2]);
}else{
  $location = route('EditAccounting',[$lang,$last_id]);
  return response()->json(['success'=>$msg,'location'=>$location]);
}
}
	
public function downloadPDFInvoice($lang,Request $request){
            
		if($request->has('print_type')){	
		  	switch($request->print_type){
				case 'All':
                $response=$this->generatePDFInvoice($request->id,$request->desc);				
				break;
				case 'Payment':
				$response=$this->generatePDFInvoicePayment($request->id,$request->desc);
				break;
			}
		
		 }else{
			//Purchase,Return supplier cases always with description
			$response=$this->generatePDFInvoice($request->id,'O'); 
		 } 
			
			return $response;
        
        }
		
   public function generatePDFInvoicePayment($id,$desc){
	                $Invoice =TblInventoryInvoicesRequest::where('clinic_inv_num',$id)->where(function($query) {
															$query->where('active','O')->orWhere('active','W');})
															->first();
					//dd($Invoice);
					if($Invoice->type==2){
							$clinic=Clinic::where('active','O')->where('id',$Invoice->clinic_num)->first();
							$result=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$Invoice->id)->get();
							$qst=($Invoice->total*$Invoice->qst)/100;
							$gst=($Invoice->total*$Invoice->gst)/100;
							$qst=number_format((float)$qst, 3, '.', '');
							$gst=number_format((float)$gst, 3, '.', '');
							$logo=DB::table('tbl_bill_logo')->where('clinic_num',$Invoice->clinic_num)->where('status','O')->first();
							$lang = app()->getLocale();
							$serial = TblInventoryInvSerials::where('clinic_num',$clinic->id)->first();            
							$patient=Patient::where('id',$Invoice->patient_id)->first();
					
						  $pay=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
						  DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
						  DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
						  'tbl_inventory_payment.payment_amount as pay_amount',
						  DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))             
								 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
								 ->where('tbl_bill_payment_mode.status', 'O')
								 ->where('tbl_inventory_payment.status','Y')
								 ->where('invoice_num',$Invoice->id)
								 ->where('payment_type','P')
								 ->get();
				 
						 $ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
						 DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
						 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
						 'tbl_inventory_payment.payment_amount as pay_amount',
						 DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
								 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
								 ->where('tbl_bill_payment_mode.status', 'O')
								 ->where('tbl_inventory_payment.status','Y')
								 ->where('invoice_num',$Invoice->id)
								 ->where('payment_type','R')
								 ->get();
													
						$data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $Invoice,'clinic' => $clinic,
								  'patient' =>$patient,'result' => $result,'pay' =>$pay,'ref'=>$ref,'serial'=>$serial,'qst'=>$qst,'gst'=>$gst,'desc'=>$desc]; 
			
						$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
										-> loadView('inventory.invoices.pdfs.salePDF', $data);
							$pdf->output();
							$dom_pdf = $pdf->getDomPDF();
							$canvas = $dom_pdf->get_canvas();
							$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
							
						
					}
				if($Invoice->type==99){
				   $clinic=Clinic::where('active','O')->where('id',$Invoice->clinic_num)->first();
				   $qst=($Invoice->total*$Invoice->qst)/100;
				   $gst=($Invoice->total*$Invoice->gst)/100;
				   $qst=number_format((float)$qst, 3, '.', '');
				   $gst=number_format((float)$gst, 3, '.', '');
				   $logo=DB::table('tbl_bill_logo')->where('clinic_num',$Invoice->clinic_num)->where('status','O')->first();
				   $result=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$Invoice->id)->get();
				   $serial = TblInventoryInvSerials::where('clinic_num',$clinic->id)->first();            
			       $ref_invoice = TblInventoryInvoicesRequest::where('id',$Invoice->reference)->first();
				   $patient=Patient::where('id',$Invoice->patient_id)->first();
				   $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $Invoice,'clinic' => $clinic,
							'patient' =>$patient,'result' => $result,'serial'=>$serial,'qst'=>$qst,'gst'=>$gst,'ref_invoice'=>$ref_invoice,'desc'=>$desc]; 
				   
				    $pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.warrantyPDF', $data);
					$pdf->output();
					$dom_pdf = $pdf->getDomPDF();
					$canvas = $dom_pdf->get_canvas();
					$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0)); 
				}
				if($Invoice->type==1){
				   $clinic=Clinic::where('active','O')->where('id',$Invoice->clinic_num)->first();
				   $qst=($Invoice->total*$Invoice->qst)/100;
				   $gst=($Invoice->total*$Invoice->gst)/100;
				   $qst=number_format((float)$qst, 3, '.', '');
				   $gst=number_format((float)$gst, 3, '.', '');
				   $lang = app()->getLocale();
				   $result=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$Invoice->id)->get();
				   $logo=DB::table('tbl_bill_logo')->where('clinic_num',$Invoice->clinic_num)->where('status','O')->first();
				   $supplier=TblInventoryItemsFournisseur::where('active','O')->where('id',$Invoice->fournisseur_id)->first();  
			
			      $pay=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
				  DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
				  DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
				  'tbl_inventory_payment.payment_amount as pay_amount',
				  DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))             
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$Invoice->id)
						 ->where('payment_type','P')
						 ->get();
		 
		         $ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
				 DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
				 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
				 'tbl_inventory_payment.payment_amount as pay_amount',
				 DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$Invoice->id)
						 ->where('payment_type','R')
						 ->get();
			
				 $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $Invoice,'clinic' => $clinic,
                          'supplier' =>$supplier,'result' => $result,'pay' =>$pay,'ref'=>$ref,'qst'=>$qst,'gst'=>$gst]; 
    
				$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.purchasePDF', $data);
				$pdf->output();
				$dom_pdf = $pdf->getDomPDF();
				$canvas = $dom_pdf->get_canvas();
				$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0)); 
				}
				if($Invoice->type==3){
				   $clinic=Clinic::where('active','O')->where('id',$Invoice->clinic_num)->first();
				   $qst=($Invoice->total*$Invoice->qst)/100;
				   $gst=($Invoice->total*$Invoice->gst)/100;
				   $qst=number_format((float)$qst, 3, '.', '');
				   $gst=number_format((float)$gst, 3, '.', '');
				   $lang = app()->getLocale();
				   $result=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$Invoice->id)->get();
				   $logo=DB::table('tbl_bill_logo')->where('clinic_num',$Invoice->clinic_num)->where('status','O')->first();
				   $ref_invoice = TblInventoryInvoicesRequest::where('clinic_inv_num',$Invoice->reference)->first();
				   $patient=Patient::where('id',$Invoice->patient_id)->first();
				   $ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
													DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
													DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
				                                   'tbl_inventory_payment.payment_amount as pay_amount',
													DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$Invoice->id)
						 ->where('payment_type','R')
						 ->get();
						 
				 $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $Invoice,'clinic' => $clinic,
                          'patient' =>$patient,'result' => $result,'qst'=>$qst,'gst'=>$gst,'ref'=>$ref,'ref_invoice'=>$ref_invoice,'desc'=>$desc]; 
    
				$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.rpatientPDF', $data);
				$pdf->output();
				$dom_pdf = $pdf->getDomPDF();
				$canvas = $dom_pdf->get_canvas();
				$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
					
				}

             return  $pdf->stream();				
					
   }		
   
   public function generatePDFInvoice($id,$desc)
    
        {
            
                   
            $Invoice =TblInventoryInvoicesRequest::where('active','O')->where('clinic_inv_num',$id)->first();
            $clinic=Clinic::where('active','O')->where('id',$Invoice->clinic_num)->first();
			$result=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$Invoice->id)->get();
			$qst=($Invoice->total*$Invoice->qst)/100;
	        $gst=($Invoice->total*$Invoice->gst)/100;
		    $qst=number_format((float)$qst, 3, '.', '');
		    $gst=number_format((float)$gst, 3, '.', '');
			$logo=DB::table('tbl_bill_logo')->where('clinic_num',$Invoice->clinic_num)->where('status','O')->first();
			$lang = app()->getLocale();
			$type = $Invoice->type;
			
			switch($type){
				//Purchase
				case 1:
				  $supplier=TblInventoryItemsFournisseur::where('active','O')->where('id',$Invoice->fournisseur_id)->first();
				  
			
			      $pay=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
				        DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
						DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
						'tbl_inventory_payment.payment_amount as pay_amount',
						DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))             
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$Invoice->id)
						 ->where('payment_type','P')
						 ->get();
		 
		         $ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
									 DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
									 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
									 'tbl_inventory_payment.payment_amount as pay_amount',
									 DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$Invoice->id)
						 ->where('payment_type','R')
						 ->get();
			
				 $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $Invoice,'clinic' => $clinic,
                          'supplier' =>$supplier,'result' => $result,'pay' =>$pay,'ref'=>$ref,'qst'=>$qst,'gst'=>$gst]; 
    
				$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.purchasePDF', $data);
				$pdf->output();
				$dom_pdf = $pdf->getDomPDF();
				$canvas = $dom_pdf->get_canvas();
				$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));         
			    $ref_invoice = TblInventoryInvoicesRequest::where('reference',$Invoice->clinic_inv_num)->where('active','O')->get();
				if(	$ref_invoice->count()==0){			
				return  $pdf->stream();
				}else{
					$oMerger = PDFMerger::init();
					$path = public_path('/custom/warranty_tmp/');
					if (!file_exists($path)) {
							mkdir($path, 0775, true);
					}
					$uid = auth()->user()->id;
					$name= date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
			        $pdf_file = $path . $name;
			        file_put_contents($pdf_file, $pdf->output());
					$oMerger->addPDF($pdf_file,'all');
					//merge all reference to purchase
					foreach($ref_invoice as $ri){
					 $ref_invoice = TblInventoryInvoicesRequest::where('clinic_inv_num',$ri->reference)->first();
                     $supplier_ri= TblInventoryItemsFournisseur::where('active','O')->where('id',$ri->fournisseur_id)->first();
				     $clinic_ri=Clinic::where('active','O')->where('id',$ri->clinic_num)->first();
			         $result_ri=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$ri->id)->get();
			         $qst_ri=($ri->total*$ri->qst)/100;
	                 $gst_ri=($ri->total*$ri->gst)/100;
		             $qst_ri=number_format((float)$qst_ri, 3, '.', '');
					 $gst_ri=number_format((float)$gst_ri, 3, '.', '');
					 $ref_ri=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
									 DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
									 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
									 'tbl_inventory_payment.payment_amount as pay_amount',
									 DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$ri->id)
						 ->where('payment_type','R')
						 ->get();
						 //dd($ref_ri);
			         $logo_ri=DB::table('tbl_bill_logo')->where('clinic_num',$ri->clinic_num)->where('status','O')->first();
					 $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo_ri,'Invoice' => $ri,'clinic' => $clinic_ri,
                              'supplier' =>$supplier_ri,'result' => $result_ri,'qst'=>$qst_ri,'gst'=>$gst_ri,'ref_invoice'=>$ref_invoice,
							  'ref'=>$ref_ri]; 
    
				     $pdf_ri = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							    ->loadView('inventory.invoices.pdfs.rsupplierPDF', $data);			 
				     $pdf_ri->output();
				     $dom_pdf = $pdf_ri->getDomPDF();
				     $canvas = $dom_pdf->get_canvas();
				     $canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0)); 
					  $name= date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
			          $pdf_file = $path . $name;
			          file_put_contents($pdf_file, $pdf_ri->output());
					  $oMerger->addPDF($pdf_file,'all');
					}
					
					$oMerger->merge();
				//Delete tmp folder
							$files = glob($path.'*'); // get all file names
							foreach($files as $file){ // iterate files
							  if(is_file($file)) {
								unlink($file); // delete file
							  }
							}
					return $oMerger->stream();
				}
				break;
			    //Sales
				case 2:
				  $serial = TblInventoryInvSerials::where('clinic_num',$clinic->id)->first();            
			      $patient=Patient::where('id',$Invoice->patient_id)->first();
			
			      $pay=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
				        DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
						DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
						'tbl_inventory_payment.payment_amount as pay_amount',
						DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))             
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$Invoice->id)
						 ->where('payment_type','P')
						 ->get();
		 
		         $ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
									 DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
									 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
									 'tbl_inventory_payment.payment_amount as pay_amount',
									 DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$Invoice->id)
						 ->where('payment_type','R')
						 ->get();
				              				
				$data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $Invoice,'clinic' => $clinic,
                          'patient' =>$patient,'result' => $result,'pay' =>$pay,'ref'=>$ref,'serial'=>$serial,'qst'=>$qst,'gst'=>$gst,'desc'=>$desc]; 
                //check if sale is related to a command 
				if(isset($Invoice->cmd_id) && $Invoice->cmd_id != ''){
				 //generate pdf with command values	
				 $pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.salePDF', $data);
				 $pdf->output();
				 $dom_pdf = $pdf->getDomPDF();
				 $canvas = $dom_pdf->get_canvas();
				 $canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
                 
				   $doctor=Doctor::where('active','O')->where('id',$Invoice->doctor_num)->first();
				   $signature_path = NULL;
				   if(isset($doctor)){
			              if( (auth()->user()->type==1 && $doctor->show_sign=='true')
							 || (auth()->user()->type==2 && $doctor->show_sign=='true' && $doctor->show_sign_for_clinic=='true'))
							 {
						     $user_signature = DoctorSignature::where('id',$doctor->sign_num)->where('active','O')->first();
						     $mainDir = public_path('/custom/profile_signatures/tmp/');
							 $uid=auth()->user()->id;
													
							 if (!file_exists($mainDir)) {
									mkdir($mainDir, 0775, true);
							  }
							 
							 $image = Image::make(file_get_contents(public_path(substr($user_signature->path,7))))->orientate();
							 //delete old existing files
							 $files = glob($mainDir.'*'); // get all file names
								foreach($files as $file){ // iterate files
								  if(is_file($file)) {
									unlink($file); // delete file
								  }
								}
							 $filename= date('Y').date('m').date('d').'_'.$uid.'_'.uniqid().'.png';
							             
										if(($image->width() <= 200) and ($image->height() <= 200)) {
										}else{
											
												  $image
                                                  ->resize(200, 200, function ($constraint) {
												  $constraint->aspectRatio();

													// if you need to prevent upsizing, you can add:
												  $constraint->upsize();
												 
												  })->save($mainDir.$filename);

										}
						 
						 	$signature_path = $mainDir.$filename;				 
						   }//end sig chk
							}
					
                $clinic_inv_num = NULL;
				if(isset($Invoice->clinic_inv_num)){
					$clinic_inv_num = $Invoice->clinic_inv_num;
				}    			
				$supplier1=NULL;
				if(isset($Invoice->fournisseur_id)){
					$supplier1 = TblInventoryItemsFournisseur::find($Invoice->fournisseur_id);
				}
				
				$right_rx_data=array();
				$left_rx_data=array();
				if(isset($Invoice) && isset($Invoice->right_rx_data)){
					$right_rx_data = json_decode($Invoice->right_rx_data,true);
				}
				
				if(isset($Invoice) && isset($Invoice->left_rx_data)){
					$left_rx_data = json_decode($Invoice->left_rx_data,true);
				}
			    
				$cmd_details= collect();
				$lunette_specs = NULL;
			
					if(isset($Invoice)){
					 $cmd_details=TblInventoryInvoicesDetails::where('cmd_id',$Invoice->id)->where('status','O')->get();
					 $d = TblInventoryInvoicesDetails::where('cmd_id',$Invoice->id)->where('status','O')->where('cmd_type','lunette')->first();
					 if(isset($d) && isset($d->item_specs)){
						$lunette_specs = json_decode($d->item_specs,true);
					 }
					}					
					
					$data_cmd = [
						 'title' => __('Command'),
						 'date' => date('m/d/Y'),
						 'signature_path' => $signature_path,
						 'cmd' => $Invoice,
						 'cmd_details'=>$cmd_details,
						 'clinic' => $clinic,
						 'patient' => $patient,
						 'lunette_specs'=>$lunette_specs,
						 'right_rx_data'=>$right_rx_data,
						 'left_rx_data'=>$left_rx_data,
						 'doctor' =>$doctor,
						 'clinic_inv_num'=>$clinic_inv_num,
						 'supplier'=>$supplier1,
						 'pay'=>$pay,
						 'ref'=>$ref
						]; 
					if($Invoice->cmd_cl == 'Y')	
					{
						 $pdf1 = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
						  -> loadView('emr.visit.commands_cl.commandPDF', $data_cmd);
					}else{
				       $pdf1 = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
						  -> loadView('emr.visit.commands.commandPDF', $data_cmd);
					 }
				$pdf1->output();
				$dom_pdf = $pdf1->getDomPDF();
				$canvas = $dom_pdf->get_canvas();
				$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
				
				if($Invoice->cmd_cl == 'Y')	
					{
				$pdf_prices = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
                    -> loadView('emr.visit.commands_cl.commandPDF_Prices', $data_cmd);
					}else{
					$pdf_prices = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
                    -> loadView('emr.visit.commands.commandPDF_Prices', $data_cmd);	
					}
				
				$pdf_prices->output();
				$dom_pdf = $pdf_prices->getDomPDF();
				$canvas = $dom_pdf->get_canvas();
				$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
				
				$path = public_path('/custom/invoice_command/tmp/');
				$uid=auth()->user()->id;
				 if (!file_exists($path)) {
						mkdir($path, 0775, true);
				  }
				  
				  $uid = auth()->user()->id;
			      
				  $name= date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
			      $pdf_file = $path . $name;
			      file_put_contents($pdf_file, $pdf->output());
			      
				  $name1= date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
			      $pdf_file1 = $path . $name1;
			      file_put_contents($pdf_file1, $pdf1->output());
				  
				  $name2= date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
			      $pdf_file2 = $path . $name2;
			      file_put_contents($pdf_file2, $pdf_prices->output());
			
				$oMerger = PDFMerger::init();
				$oMerger->addPDF($pdf_file,'all');
				$oMerger->addPDF($pdf_file1,'all');
				$oMerger->addPDF($pdf_file2,'all');
				//check if there is also a ref invoices warranty command or ( return patient, warranty) in order to merge them
				
					//1- search for warranty invoices that refer for this invoice id
                    //2- search for return patient that refer for this invoice serial code
					//3- search for serial code of the related invoice for this warranty invoice 
					
					if($Invoice->is_warranty=='Y'){
						$ref_invoice = TblInventoryInvoicesRequest::where('clinic_inv_num',$Invoice->reference)->where('active','O')->get();
					}else{
						$ref_invoice = TblInventoryInvoicesRequest::where(function ($q) use($Invoice){
					                                         $q->where('reference',$Invoice->clinic_inv_num)
															   ->orWhere('reference',$Invoice->id);
				                                             })
					                  ->where('active','O')->get();
					}				  
				//dd($ref_invoice);
				if($ref_invoice->count() >0){
				  foreach ($ref_invoice as $ri){
						//get reference purchase
				  $inv_ri = TblInventoryInvoicesRequest::find($ri->id);
				  $inv_ri_ref = TblInventoryInvoicesRequest::where('clinic_inv_num',$ri->reference)->first();
				  $patient_ri=Patient::where('id',$ri->patient_id)->first();
				  $clinic_ri=Clinic::where('active','O')->where('id',$ri->clinic_num)->first();
			      $result_ri=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$ri->id)->get();
				  $ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
				  DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
				  DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
				  'tbl_inventory_payment.payment_amount as pay_amount',DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$inv_ri->id)
						 ->where('payment_type','R')
						 ->get();
				  
				  if($ri->type=='2'){
				  $pay=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
				  DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
				  DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
				  'tbl_inventory_payment.payment_amount as pay_amount',DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$inv_ri->id)
						 ->where('payment_type','P')
						 ->get();		 
				  }
				  $qst=($ri->total*$ri->qst)/100;
	              $gst=($ri->total*$ri->gst)/100;
		          $qst=number_format((float)$qst, 3, '.', '');
		          $gst=number_format((float)$gst, 3, '.', '');
			      $logo=DB::table('tbl_bill_logo')->where('clinic_num',$ri->clinic_num)->where('status','O')->first();
				 
					  if($ri->type== '99'){
                        $serial_ref = TblInventoryInvSerials::where('clinic_num',$clinic_ri->id)->first();;
						$data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $inv_ri,'clinic' => $clinic_ri,
								      'patient' =>$patient_ri,'result' => $result_ri,'serial'=>$serial_ref,'qst'=>$qst,'gst'=>$gst,'ref_invoice'=>$inv_ri_ref,'desc'=>$desc]; 
			            $pdf_ri = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
									-> loadView('inventory.invoices.pdfs.warrantyPDF', $data);					  
				       }
					  if($ri->type== '3'){
					    $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $inv_ri,'clinic' => $clinic_ri,
								  'patient' =>$patient_ri,'result' => $result_ri,'ref'=>$ref,'qst'=>$qst,'gst'=>$gst,'ref_invoice'=>$inv_ri_ref,'desc'=>$desc]; 
			            $pdf_ri = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
									-> loadView('inventory.invoices.pdfs.rpatientPDF', $data);
					  }
					  if($ri->type== '2'){
					    $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $inv_ri,'clinic' => $clinic_ri,
								  'patient' =>$patient_ri,'result' => $result_ri,'pay'=>$pay,'ref'=>$ref,'qst'=>$qst,'gst'=>$gst,'ref_invoice'=>$inv_ri_ref,'desc'=>$desc]; 
			            $pdf_ri = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
									-> loadView('inventory.invoices.pdfs.salePDF', $data); 
					  }
				  
				 
				  $pdf_ri->output();
				  $dom_pdf = $pdf_ri->getDomPDF();
				  $canvas = $dom_pdf->get_canvas();
				  $canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
                  $name_ri= date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
			      $pdf_file_ri = $path . $name_ri;
			      file_put_contents($pdf_file_ri, $pdf_ri->output());
				  $oMerger->addPDF($pdf_file_ri,'all');
					}
				}
				$oMerger->merge();
				//Delete tmp folder
							$files = glob($path.'*'); // get all file names
							foreach($files as $file){ // iterate files
							  if(is_file($file)) {
								unlink($file); // delete file
							  }
							}
					
				  return  $oMerger->stream();
				
				}else{
					//invoice is not an order then check if there is also ref invoices to merge them
					$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
								-> loadView('inventory.invoices.pdfs.salePDF', $data);
					$pdf->output();
					$dom_pdf = $pdf->getDomPDF();
					$canvas = $dom_pdf->get_canvas();
					$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
					
					$ref_invoice = TblInventoryInvoicesRequest::where(function ($q) use($Invoice){
					                                              $q->where('reference',$Invoice->clinic_inv_num)
															        ->orWhere('reference',$Invoice->id); 
				                                                })->where('active','O')->get();
					if($ref_invoice->count() >0){
					    $path = public_path('/custom/invoice_command/tmp/');
						$uid=auth()->user()->id;
						 if (!file_exists($path)) {
								mkdir($path, 0775, true);
						  }
				   	   $name= date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
			           $pdf_file = $path . $name;
			           file_put_contents($pdf_file, $pdf->output());	 
					   $oMerger = PDFMerger::init();
				       $oMerger->addPDF($pdf_file,'all');
					   //now merge all ref invoices to pdf
					   foreach ($ref_invoice as $ri){
						//get reference purchase
						  $inv_ri = TblInventoryInvoicesRequest::find($ri->id);
						  $inv_ri_ref = TblInventoryInvoicesRequest::where('clinic_inv_num',$ri->reference)->first();
						  $patient_ri=Patient::where('id',$ri->patient_id)->first();
						  $clinic_ri=Clinic::where('active','O')->where('id',$ri->clinic_num)->first();
						  $result_ri=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$ri->id)->get();
						  $qst=($ri->total*$ri->qst)/100;
						  $gst=($ri->total*$ri->gst)/100;
						  $qst=number_format((float)$qst, 3, '.', '');
						  $gst=number_format((float)$gst, 3, '.', '');
						 $ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
						 DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
						 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
						 'tbl_inventory_payment.payment_amount as pay_amount',
						 DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$inv_ri->id)
						 ->where('payment_type','R')
						 ->get();
						
						 
						  $logo=DB::table('tbl_bill_logo')->where('clinic_num',$ri->clinic_num)->where('status','O')->first();
						  
						  
						  if($ri->type=='99'){
							 $serial_ref = TblInventoryInvSerials::where('clinic_num',$clinic_ri->id)->first();;
							 $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $inv_ri,'clinic' => $clinic_ri,
								      'patient' =>$patient_ri,'result' => $result_ri,'serial'=>$serial_ref,'qst'=>$qst,'gst'=>$gst,'ref_invoice'=>$inv_ri_ref,'desc'=>$desc]; 
			                 $pdf_ri = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
									-> loadView('inventory.invoices.pdfs.warrantyPDF', $data);
						   
						  }else{
							
							$data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $inv_ri,'clinic' => $clinic_ri,
								  'patient' =>$patient_ri,'result' => $result_ri,'ref'=>$ref,'qst'=>$qst,'gst'=>$gst,'ref_invoice'=>$inv_ri_ref,'desc'=>$desc]; 
			                $pdf_ri = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
									-> loadView('inventory.invoices.pdfs.rpatientPDF', $data);
						   
						  }
						  
						  $pdf_ri->output();
						  $dom_pdf = $pdf_ri->getDomPDF();
						  $canvas = $dom_pdf->get_canvas();
						  $canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
						  $name_ri= date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
						  $pdf_file_ri = $path . $name_ri;
						  file_put_contents($pdf_file_ri, $pdf_ri->output());
						  $oMerger->addPDF($pdf_file_ri,'all');
							}
							$oMerger->merge();
							//Delete tmp folder
										$files = glob($path.'*'); // get all file names
										foreach($files as $file){ // iterate files
										  if(is_file($file)) {
											unlink($file); // delete file
										  }
										}
					
				           return  $oMerger->stream();
					   
					}else{
					         
					return  $pdf->stream();
					}
				}
				break;
			    //return patient
				case 3:
				 //get reference purchase
				 $ref_invoice = TblInventoryInvoicesRequest::where('clinic_inv_num',$Invoice->reference)->first();
				 $patient=Patient::where('id',$Invoice->patient_id)->first();
				 $ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
				 DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
				 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
				 'tbl_inventory_payment.payment_amount as pay_amount',
				 DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$Invoice->id)
						 ->where('payment_type','R')
						 ->get();
						 
				 $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $Invoice,'clinic' => $clinic,
                          'patient' =>$patient,'result' => $result,'qst'=>$qst,'gst'=>$gst,'ref'=>$ref,'ref_invoice'=>$ref_invoice,'desc'=>$desc]; 
    
				$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.rpatientPDF', $data);
				$pdf->output();
				$dom_pdf = $pdf->getDomPDF();
				$canvas = $dom_pdf->get_canvas();
				$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
			    
				//get reference sale
				
				//Prepare pdf for sales reference
					   
			        $patient_ref=Patient::where('id',$ref_invoice->patient_id)->first();
					$clinic_ref=Clinic::where('active','O')->where('id',$ref_invoice->clinic_num)->first();
					$serial_ref = TblInventoryInvSerials::where('clinic_num',$clinic_ref->id)->first();       
					$result_ref=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$ref_invoice->id)->get();
					$qst=($ref_invoice->total*$ref_invoice->qst)/100;
					$gst=($ref_invoice->total*$ref_invoice->gst)/100;
					$qst=number_format((float)$qst, 3, '.', '');
					$gst=number_format((float)$gst, 3, '.', '');
					$logo_ref=DB::table('tbl_bill_logo')->where('clinic_num',$ref_invoice->clinic_num)->where('status','O')->first();
					$pay_ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
					DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
					DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
					'tbl_inventory_payment.payment_amount as pay_amount',
					DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))             
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$ref_invoice->id)
						 ->where('payment_type','P')
						 ->get();
		 
		            $ref_ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
					DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
					DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
					'tbl_inventory_payment.payment_amount as pay_amount',
					DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$ref_invoice->id)
						 ->where('payment_type','R')
						 ->get();
					
					$data_ref = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo_ref,'Invoice' => $ref_invoice,'clinic' => $clinic_ref,
                             'patient' =>$patient_ref,'result' => $result_ref,'pay' =>$pay_ref,'ref'=>$ref_ref,'serial'=>$serial_ref,'qst'=>$qst,'gst'=>$gst,'desc'=>$desc]; 
					
					$pdf_ref = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.salePDF', $data_ref);
					$pdf_ref->output();
					$dom_pdf = $pdf_ref->getDomPDF();
					$canvas = $dom_pdf->get_canvas();
					$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0)); 
					
					
			        //return  $pdf->stream();
					$path = public_path('/custom/warranty_tmp/');
					if (!file_exists($path)) {
							mkdir($path, 0775, true);
					}
					$uid = auth()->user()->id;
					$name=date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
					$pdf_file = $path . $name;
					file_put_contents($pdf_file, $pdf->output());
					$name_ref=date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
					$pdf_file_ref = $path . $name_ref;
					file_put_contents($pdf_file_ref, $pdf_ref->output());
					
					$oMerger = PDFMerger::init();
					$oMerger->addPDF($pdf_file,'all');
					$oMerger->addPDF($pdf_file_ref,'all');
					$oMerger->merge();
					//$oMerger->Cell(0,10,'Page '.$oMerger->PageNo().'/{nb}',0,0,'C');

					
					//Delete tmp folder
					    $files = glob($path.'*'); // get all file names
						foreach($files as $file){ // iterate files
						  if(is_file($file)) {
							unlink($file); // delete file
						  }
						}
					
					return  $oMerger->stream();
				break;
				//return supplier
				case 4:
				  //get reference purchase
				  $ref_invoice = TblInventoryInvoicesRequest::where('clinic_inv_num',$Invoice->reference)->first();
				  $supplier=TblInventoryItemsFournisseur::where('active','O')->where('id',$Invoice->fournisseur_id)->first();
				  $ref_ri=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
									 DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
									 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
									 'tbl_inventory_payment.payment_amount as pay_amount',
									 DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$Invoice->id)
						 ->where('payment_type','R')
						 ->get();
				  $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $Invoice,'clinic' => $clinic,
                          'supplier' =>$supplier,'result' => $result,'qst'=>$qst,'gst'=>$gst,'ref_invoice'=>$ref_invoice,'ref'=>$ref_ri]; 
    
				$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.rsupplierPDF', $data);
				$pdf->output();
				$dom_pdf = $pdf->getDomPDF();
				$canvas = $dom_pdf->get_canvas();
				$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));         
			    
				return  $pdf->stream();
				break;
			    //Waranty
				case 99:
				   $serial = TblInventoryInvSerials::where('clinic_num',$clinic->id)->first();            
			       $ref_invoice = TblInventoryInvoicesRequest::where('id',$Invoice->reference)->first();
				   $patient=Patient::where('id',$Invoice->patient_id)->first();
				   $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $Invoice,'clinic' => $clinic,
							'patient' =>$patient,'result' => $result,'serial'=>$serial,'qst'=>$qst,'gst'=>$gst,'ref_invoice'=>$ref_invoice,'desc'=>$desc]; 
				   
				    $pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.warrantyPDF', $data);
					$pdf->output();
					$dom_pdf = $pdf->getDomPDF();
					$canvas = $dom_pdf->get_canvas();
					$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));         
					$path = public_path('/custom/warranty_tmp/');
					if (!file_exists($path)) {
							mkdir($path, 0775, true);
					}
					$uid = auth()->user()->id;
					$name=date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
					$pdf_file = $path . $name;
					file_put_contents($pdf_file, $pdf->output());
					$oMerger = PDFMerger::init();
					$oMerger->addPDF($pdf_file,'all');
					
					
					//Prepare pdf for sales reference
					if(isset($ref_invoice) && isset($ref_invoice->clinic_inv_num)){  
			        $patient_ref=Patient::where('status','O')->where('id',$ref_invoice->patient_id)->first();
					$clinic_ref=Clinic::where('active','O')->where('id',$ref_invoice->clinic_num)->first();
					$serial_ref = TblInventoryInvSerials::where('clinic_num',$clinic_ref->id)->first();       
					$result_ref=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$ref_invoice->id)->get();
					$qst=($ref_invoice->total*$Invoice->qst)/100;
					$gst=($ref_invoice->total*$Invoice->gst)/100;
					$qst=number_format((float)$qst, 3, '.', '');
					$gst=number_format((float)$gst, 3, '.', '');
					$logo_ref=DB::table('tbl_bill_logo')->where('clinic_num',$ref_invoice->clinic_num)->where('status','O')->first();
					$pay_ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
					DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
					 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
					'tbl_inventory_payment.payment_amount as pay_amount',
					DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))             
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$ref_invoice->id)
						 ->where('payment_type','P')
						 ->get();
		 
		            $ref_ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
					DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
					 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
					'tbl_inventory_payment.payment_amount as pay_amount',
					DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$ref_invoice->id)
						 ->where('payment_type','R')
						 ->get();
					
					$data_ref = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo_ref,'Invoice' => $ref_invoice,'clinic' => $clinic_ref,
                             'patient' =>$patient_ref,'result' => $result_ref,'pay' =>$pay_ref,'ref'=>$ref_ref,'serial'=>$serial_ref,'qst'=>$qst,'gst'=>$gst,'desc'=>$desc]; 
					
					$pdf_ref = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.salePDF', $data_ref);
					$pdf_ref->output();
					$dom_pdf = $pdf_ref->getDomPDF();
					$canvas = $dom_pdf->get_canvas();
					$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0)); 
	               	$name_ref=date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
					$pdf_file_ref = $path . $name_ref;
					file_put_contents($pdf_file_ref, $pdf_ref->output());
					
					
					$oMerger->addPDF($pdf_file_ref,'all');
					}
					//check if ref invoice has a command
					if($Invoice->is_warranty !='Y' && isset($ref_invoice->cmd_id) && $ref_invoice->cmd_id != ''){
					     $doctor=Doctor::where('active','O')->where('id',$ref_invoice->doctor_num)->first();
				         $signature_path = NULL;
						   if(isset($doctor)){
								  if( (auth()->user()->type==1 && $doctor->show_sign=='true')
									 || (auth()->user()->type==2 && $doctor->show_sign=='true' && $doctor->show_sign_for_clinic=='true'))
									 {
									 $user_signature = DoctorSignature::where('id',$doctor->sign_num)->where('active','O')->first();
									 $mainDir = public_path('/custom/profile_signatures/tmp/');
									 $uid=auth()->user()->id;
															
									 if (!file_exists($mainDir)) {
											mkdir($mainDir, 0775, true);
									  }
									 
									 $image = Image::make(file_get_contents(public_path(substr($user_signature->path,7))))->orientate();
									 //delete old existing files
									 $files = glob($mainDir.'*'); // get all file names
										foreach($files as $file){ // iterate files
										  if(is_file($file)) {
											unlink($file); // delete file
										  }
										}
									 $filename= date('Y').date('m').date('d').'_'.$uid.'_'.uniqid().'.png';
												 
												if(($image->width() <= 200) and ($image->height() <= 200)) {
												}else{
													
														  $image
														  ->resize(200, 200, function ($constraint) {
														  $constraint->aspectRatio();

															// if you need to prevent upsizing, you can add:
														  $constraint->upsize();
														 
														  })->save($mainDir.$filename);

												}
								 
									$signature_path = $mainDir.$filename;				 
								   }//end sig chk
									}
							
							$clinic_inv_num = NULL;
							if(isset($ref_invoice->clinic_inv_num)){
								$clinic_inv_num = $ref_invoice->clinic_inv_num;
							}    			
							$supplier1=NULL;
							if(isset($ref_invoice->fournisseur_id)){
								$supplier1 = TblInventoryItemsFournisseur::find($ref_invoice->fournisseur_id);
							}
							
							$right_rx_data=array();
							$left_rx_data=array();
							if(isset($ref_invoice) && isset($ref_invoice->right_rx_data)){
								$right_rx_data = json_decode($ref_invoice->right_rx_data,true);
							}
							
							if(isset($ref_invoice) && isset($ref_invoice->left_rx_data)){
								$left_rx_data = json_decode($ref_invoice->left_rx_data,true);
							}
			    
							$cmd_details= collect();
							$lunette_specs = NULL;
						
								if(isset($ref_invoice)){
								 $cmd_details=TblInventoryInvoicesDetails::where('cmd_id',$ref_invoice->id)->where('status','O')->get();
								 $d = TblInventoryInvoicesDetails::where('cmd_id',$ref_invoice->id)->where('status','O')->where('cmd_type','lunette')->first();
								 if(isset($d) && isset($d->item_specs)){
									$lunette_specs = json_decode($d->item_specs,true);
								 }
								}					
					            $from_warranty = __("Warranty");
								$pay_ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
										DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
										 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
										'tbl_inventory_payment.payment_amount as pay_amount',
										DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))             
											 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
											 ->where('tbl_bill_payment_mode.status', 'O')
											 ->where('tbl_inventory_payment.status','Y')
											 ->where('invoice_num',$ref_invoice->id)
											 ->where('payment_type','P')
											 ->get();
							 
		            $ref_ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
								DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
								 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
								'tbl_inventory_payment.payment_amount as pay_amount',
								DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))
									 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
									 ->where('tbl_bill_payment_mode.status', 'O')
									 ->where('tbl_inventory_payment.status','Y')
									 ->where('invoice_num',$ref_invoice->id)
									 ->where('payment_type','R')
									 ->get();
						 
								$data_cmd = [
									 'title' => __('Command'),
									 'date' => date('m/d/Y'),
									 'from_warranty'=>$from_warranty,
									 'signature_path' => $signature_path,
									 'cmd' => $ref_invoice,
									 'cmd_details'=>$cmd_details,
									 'clinic' => $clinic,
									 'patient' => $patient,
									 'lunette_specs'=>$lunette_specs,
									 'right_rx_data'=>$right_rx_data,
									 'left_rx_data'=>$left_rx_data,
									 'doctor' =>$doctor,
									 'clinic_inv_num'=>$clinic_inv_num,
									 'supplier'=>$supplier1,
									 'pay'=>$pay_ref,
									 'ref'=>$ref_ref
									]; 
						
									if($ref_invoice->cmd_cl=='Y'){
										$pdf1 = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
										-> loadView('emr.visit.commands_cl.commandPDF', $data_cmd);
									}else{
									$pdf1 = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
										-> loadView('emr.visit.commands.commandPDF', $data_cmd);
									}
									$pdf1->output();
									$dom_pdf = $pdf1->getDomPDF();
									$canvas = $dom_pdf->get_canvas();
									$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
									
									if($ref_invoice->cmd_cl=='Y'){
									$pdf_prices = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
										-> loadView('emr.visit.commands_cl.commandPDF_Prices', $data_cmd);	
									}else{
									$pdf_prices = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
										-> loadView('emr.visit.commands.commandPDF_Prices', $data_cmd);
									}
									$pdf_prices->output();
									$dom_pdf = $pdf_prices->getDomPDF();
									$canvas = $dom_pdf->get_canvas();
									$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
				
									
									  $name1= date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
									  $pdf_file1 = $path . $name1;
									  file_put_contents($pdf_file1, $pdf1->output());
									  
									  $name2= date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
									  $pdf_file2 = $path . $name2;
									  file_put_contents($pdf_file2, $pdf_prices->output());
			
										$oMerger->addPDF($pdf_file1,'all');
										$oMerger->addPDF($pdf_file2,'all');
 					
					}
					
					$oMerger->merge();
					
					//Delete tmp folder
					    $files = glob($path.'*'); // get all file names
						foreach($files as $file){ // iterate files
						  if(is_file($file)) {
							unlink($file); // delete file
						  }
						}
					
					return  $oMerger->stream();
				
				break;
			
			}
			
			
        }	



public function Refresh_code($lang,Request $request) 
{
	    $filter_supplier="";
		if(isset($request->filter_supplier)&& $request->filter_supplier !="0" && $request->filter_supplier !=""){
			$filter_supplier= " and b.id = '".$request->filter_supplier."'";
		}
		//dd($filter_supplier);
		$html='<option value="0">'.__("All Codes").'</option>';;
	    $sqlCode = "select a.id,a.description as name,b.name as namefournisseur from tbl_inventory_items a,tbl_inventory_items_fournisseur b where a.fournisseur=b.id and a.active='O' ".$filter_supplier." order by a.id desc";
		//dd($sqlCode);
		//$sqlCode = "select DISTINCT(id) AS id,description as name from tbl_inventory_items where active='O' ".$filter_supplier." order by id desc";
		$code= DB::select(DB::raw("$sqlCode"));
     foreach ($code as $codes) {
       $html .= '<option value="' . $codes->id.'">'.$codes->name.'-(Supplier:'.$codes->namefournisseur.')</option>';
           }
			return response()->json(["html"=>$html]);   
	}	
	


function GetPayInventory($lang,Request $request){
$sumpay=TblInventoryPayment::where('invoice_num','=',$request->invoice_id)->where('payment_type','=','P')->where('status','=','Y')->sum('payment_amount');
$sumref=TblInventoryPayment::where('invoice_num','=',$request->invoice_id)->where('payment_type','=','R')->where('status','=','Y')->sum('payment_amount');
$ReqPay=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$request->invoice_id)
			->where('payment_type','P')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);       
            					      
$cptpCount= $ReqPay->count();		
$cptp = 1;
$html1='<thead>
		<tr class="txt-bg text-white text-center">
		<th scope="col" style="font-size:16px;">'.__("#").'</th>
		<th scope="col" style="font-size:16px;">'.__("Date").'</th>
		<th scope="col" style="font-size:16px;">'.__("Type").'</th>
		<th scope="col" style="font-size:16px;">'.__("Amount").'</th>
		<th scope="col" style="font-size:16px;">'.__("Deposit").'</th>
		<th scope="col" style="font-size:16px;">'.__("Remark").'</th>
		<th scope="col"></th>
		</tr>
		</thead>
		<tbody>';
foreach ($ReqPay as $sReqPays) {
		$html1 .='<tr>
		<td>'.$cptp.'</td>
		<td>'.$sReqPays->datein.'</td>
		<td>'.$sReqPays->name_fr.'</td>
		<td>'.$sReqPays->payment_amount.'</td>
		<td>'.$sReqPays->deposit.'</td>
		<td>'.$sReqPays->remark.'</td>
		<td><input type="button" class="btn btn-delete" id="rowdeletepay'.$cptp.'" value="'.__("Delete").'" onclick="deleteRowPay(this)"  /></td>
		</tr>';
$cptp++;
}
  $html1 .='</tbody>';
return response()->json(['html1' => $html1,'sumpay'=>$sumpay,'sumref'=>$sumref]);
	
}

function GetRefInventory($lang,Request $request){
$sumpay=TblInventoryPayment::where('invoice_num','=',$request->invoice_id)->where('payment_type','=','P')->where('status','=','Y')->sum('payment_amount');
if(isset($request->cnote)&&$request->cnote=='Y'){
$sumref=TblInventoryPayment::where('invoice_num','=',$request->invoice_id)->where('payment_type','=','R')->where('status','=','Y')->where('crnote','=','Y')->sum('payment_amount');
$ReqRef=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$request->invoice_id)
			->where('payment_type','R')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
}else{
$sumref=TblInventoryPayment::where('invoice_num','=',$request->invoice_id)->where('payment_type','=','R')->where('crnote','=','N')->where('status','=','Y')->sum('payment_amount');
$ReqRef=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$request->invoice_id)
			->where('payment_type','R')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);	

}
$cptrCount= $ReqRef->count();		
$cptr = 1;
$html1='<thead>
		<tr class="txt-bg text-white text-center">
		<th scope="col" style="font-size:16px;">'.__("#").'</th>
		<th scope="col" style="font-size:16px;">'.__("Date").'</th>
		<th scope="col" style="font-size:16px;">'.__("Type").'</th>
		<th scope="col" style="font-size:16px;">'.__("Amount").'</th>
		<th scope="col" style="font-size:16px;">'.__("Remark").'</th>
		<th scope="col"></th>
		</tr>
		</thead>
		<tbody>';
foreach ($ReqRef as $sReqRefs) {
		$html1 .='<tr>
		<td>'.$cptr.'</td>
		<td>'.$sReqRefs->datein.'</td>
		<td>'.$sReqRefs->name_fr.'</td>
		<td>'.$sReqRefs->payment_amount.'</td>
		<td>'.$sReqRefs->remark.'</td>
		<td><input type="button" class="btn btn-delete" id="rowdeleteref'.$cptr.'" value="'.__("Delete").'" onclick="deleteRowRef(this)"  /></td>
		</tr>';
$cptr++;
}
  $html1 .='</tbody>';
 
return response()->json(['html1' => $html1,'sumpay'=>$sumpay,'sumref'=>$sumref]);
	
}	

function GetRemiseInventory($lang,Request $request){
//$ReqInvRemise=TblInventoryInvoicesRequest::where('reference',$request->invoice_id)
//			->where('active','O')
 //           ->first();
//if (isset($ReqInvRemise)){
//$IdInvRemise=$ReqInvRemise->clinic_inv_num;
//}else{
$IdInvRemise='';	
//}
//$RemiseRq=TblInventoryRemise::where('invoice_id',$request->invoice_id)
	//		->where('status','Y')
	//		->where('active','O')
  //          ->first();

$sqlPR = "select id as idrow,item_code as id,item_name as name from tbl_inventory_invoices_details where invoice_id='".$request->invoice_id."' and status='O' order by idrow asc";
$coderemise= DB::select(DB::raw("$sqlPR"));	
$htmlc='<option value="">'.__("Undefined").'</option>';;
		 foreach ($coderemise as $scoderemise) {
       $htmlc .= '<option value="' . $scoderemise->id.'">'.$scoderemise->name .'</option>';
           }


		
$ReqRemise=TblInventoryRemise::join('tbl_inventory_invoices_request', 'tbl_inventory_invoices_request.reference', '=', 'tbl_inventory_remise.invoice_id')
            ->where('tbl_inventory_remise.status', 'Y')
            ->where('tbl_inventory_invoices_request.active','O')
			->where('invoice_id',$request->invoice_id)
            ->get(['tbl_inventory_remise.*', 'tbl_inventory_invoices_request.clinic_inv_num','tbl_inventory_invoices_request.remark']);
			
$cptsCount= $ReqRemise->count();	
$cpts = 1;
$html1='<thead>
		<tr class="txt-bg text-white text-center">
		<th scope="col" style="font-size:16px;">'.__("#").'</th>
		<th scope="col" style="font-size:16px;">'.__("Warranty Nb").'</th>
		<th scope="col" style="font-size:16px;">'.__("Date").'</th>
		<th scope="col" style="font-size:16px;">'.__("Code").'</th>
		<th scope="col" style="font-size:16px;">'.__("Description").'</th>
		<th scope="col" style="font-size:16px;">'.__("Qty").'</th>
		<th scope="col"></th>
		</tr>
		</thead>
		<tbody>';
foreach ($ReqRemise as $ReqRemises) {
		$html1 .='<tr>
		<td>'.$cpts.'</td>
		<td>'.$ReqRemises->clinic_inv_num.'</td>
		<td>'.$ReqRemises->datein.'</td>
		<td>'.$ReqRemises->item_code.'</td>
		<td>'.$ReqRemises->item_name.'</td>
		<td>'.$ReqRemises->qty.'</td>
		<td><input type="button" class="btn btn-delete" id="rowdeleteremise'.$cpts.'" value="'.__("Delete").'" onclick="deleteRowRemise(this)"  disabled /></td>
		</tr>';
$cpts++;
}
  $html1 .='</tbody>';
return response()->json(['html1' => $html1,'IdInvRemise'=>$IdInvRemise,'htmlc'=>$htmlc]);
	
}	
function SavePayInventory($lang,Request $request){

$someArray = [];
$someArray=json_decode($request->data,true); 		
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
//$date_pay=$request->date_pay;
$invoice_id=$request->invoice_id;
$clinic_id=$request->clinic_id;
//$valamount=$request->valamount;
$balance=$request->balance;
//$type="P";
//$selectmethod=$request->selectmethod;
//if ($request->status=='M'){
 $sqlReq = "update tbl_inventory_payment set status='N' where payment_type='P' and invoice_num=".$request->invoice_id;   			   
 DB::select(DB::raw("$sqlReq"));		
//}

//$sqlReq = "insert into tbl_bill_payment (datein,bill_num,payment_amount,payment_type,reference,user_num,user_type) values('".$date_pay."','".$bill_id."','".$valamount."','".$type."','".$selectmethod."','".$user_id."','".$user_type."')";
//DB::select(DB::raw("$sqlReq"));
//$sumpay=TblBillPayment::where('bill_num','=',$bill_id)->where('payment_type','=','P')->sum('payment_amount');
//$sumref=TblBillPayment::where('bill_num','=',$bill_id)->where('payment_type','=','R')->sum('payment_amount');
foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0){
   $code = $area["CODE"];
   $date = $area["DATE"];
   $type=trim($area["TYPE"]);
   $price = $area["PRICE"];
   $rqpay=trim($area["RQPAY"]);
   $deposit = $area["DEPOSIT"];
   if ($lang=='fr'){	
  $typepay = TblBillPaymentMode::where('name_fr',$type)->where('status','O')->first();
   }else{
	$typepay = TblBillPaymentMode::where('name_eng',$type)->where('status','O')->first();   
   }
   $reference= $typepay->id;
   $assurance=$typepay->assurance;
   $sqlInsertF = "insert into tbl_inventory_payment(datein,invoice_num,clinic_num,payment_amount,payment_type,reference,user_type,assurance,deposit,remark,user_num,status) values('".
				 $date."','".
				 $invoice_id."','".
				 $clinic_id."','".
				 $price."','P','".
				 $reference."','".
				 $user_type."','".
				 $assurance."','".
				 $deposit."','".
				 $rqpay."','".
				 $user_id."','Y')";
	DB::select(DB::raw("$sqlInsertF"));
 
 }	
 }
$sumpay=TblInventoryPayment::where('invoice_num','=',$invoice_id)->where('status','Y')->where('payment_type','=','P')->sum('payment_amount');
$sumref=TblInventoryPayment::where('invoice_num','=',$invoice_id)->where('status','Y')->where('payment_type','=','R')->sum('payment_amount');
$ReqPatient=TblInventoryInvoicesRequest::where('id',$invoice_id)->where('active','O')->first();

$Nbalance=number_format((float)$ReqPatient->total-$sumpay+$sumref+$ReqPatient->qst+$ReqPatient->gst, 3, '.', '');
$balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));

TblInventoryInvoicesRequest::where('id',$invoice_id)->update([
			                  'inv_balance'=>$balance
							  ]);	
$msg=__('Payment Saved Success');
	return response()->json(['success'=>$msg,'sumpay'=>$sumpay,'sumref'=>$sumref,'nbalance'=>$balance]);

		  
}		
  
function SaveRefundInventory($lang,Request $request){
$someArray = [];
$someArray=json_decode($request->data,true); 		
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
//$date_pay=$request->date_pay;
$invoice_id=$request->invoice_id;
$clinic_id=$request->clinic_id;
//$valamount=$request->valamount;
$balance=$request->balance;
//$type="P";
//$selectmethod=$request->selectmethod;
//if ($request->status=='M'){
 $sqlReq = "update tbl_inventory_payment set status='N' where payment_type='R' and invoice_num=".$request->invoice_id;   			   
 DB::select(DB::raw("$sqlReq"));		
//}
//$sqlReq = "insert into tbl_bill_payment (datein,bill_num,payment_amount,payment_type,reference,user_num,user_type) values('".$date_pay."','".$bill_id."','".$valamount."','".$type."','".$selectmethod."','".$user_id."','".$user_type."')";
//DB::select(DB::raw("$sqlReq"));
//$sumpay=TblBillPayment::where('bill_num','=',$bill_id)->where('payment_type','=','P')->sum('payment_amount');
//$sumref=TblBillPayment::where('bill_num','=',$bill_id)->where('payment_type','=','R')->sum('payment_amount');
foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0){
   $code = $area["CODE"];
   $date = $area["DATE"];
   $type=trim($area["TYPE"]);
   $price = $area["PRICE"];
   $rqref=trim($area["RQREF"]);
   if ($lang=='fr'){	
  $typepay = TblBillPaymentMode::where('name_fr',$type)->where('status','O')->first();
   }else{
	$typepay = TblBillPaymentMode::where('name_eng',$type)->where('status','O')->first();   
   }
   $reference= $typepay->id;
   if ($reference=='99'){
	   $pay_type='R';
	   $pay_crnote='Y';
   }else{
       $pay_type='R';
	   $pay_crnote='N';
   }
   $assurance=$typepay->assurance;
   $sqlInsertF = "insert into tbl_inventory_payment(datein,invoice_num,clinic_num,payment_amount,payment_type,reference,user_type,crnote,assurance,remark,user_num,status) values('".
				 $date."','".
				 $invoice_id."','".
				 $clinic_id."','".
				 $price."','".
				 $pay_type."','".
				 $reference."','".
				 $user_type."','".
				 $pay_crnote."','".
				 $assurance."','".
				 $rqref."','".
				 $user_id."','Y')";
	DB::select(DB::raw("$sqlInsertF"));
 
 }	
 }
$sumpay=TblInventoryPayment::where('invoice_num','=',$invoice_id)->where('status','Y')->where('payment_type','=','P')->sum('payment_amount');
if ($request->cnote=='on'){
$sumref=TblInventoryPayment::where('invoice_num','=',$invoice_id)->where('status','Y')->where('crnote','Y')->where('payment_type','=','R')->sum('payment_amount');
}else{	
$sumref=TblInventoryPayment::where('invoice_num','=',$invoice_id)->where('status','Y')->where('payment_type','=','R')->sum('payment_amount');
}
$ReqPatient=TblInventoryInvoicesRequest::where('id',$invoice_id)->where('active','O')->first();
$selecttype=$ReqPatient->type;
if($selecttype=="3" || $selecttype=="4"){ 
$Nbalance=number_format((float)$ReqPatient->total-$sumpay-$sumref-$ReqPatient->qst-$ReqPatient->gst, 3, '.', '');
}else{
$Nbalance=number_format((float)$ReqPatient->total-$sumpay+$sumref+$ReqPatient->qst+$ReqPatient->gst, 3, '.', '');	
}
$balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));

TblInventoryInvoicesRequest::where('id',$invoice_id)->update([
			                  'inv_balance'=>$balance
							  ]);	
$msg=__('Refund Saved Success');
	return response()->json(['success'=>$msg,'sumpay'=>$sumpay,'sumref'=>$sumref,'nbalance'=>$balance]);


}		  		  

function SaveRemiseInventory($lang,Request $request){
	//save in remise
$someArray = [];
$someArray=json_decode($request->data,true); 		
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
//$date_pay=$request->date_pay;
$invoice_id=$request->invoice_id;
$remark=$request->remark;
 $sqlReq = "update tbl_inventory_remise set status='N' where invoice_id=".$request->invoice_id;   			   
 DB::select(DB::raw("$sqlReq"));		
foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0){
   $remisenb = $area["REMISENB"];
   $code = $area["CODE"];
   $date = $area["DATE"];
   $name=trim($area["NAME"]);
   $qty = $area["QTY"];
 IF ($remisenb==''){
   $sqlInsertF = "insert into tbl_inventory_remise(datein,invoice_id,item_code,item_name,qty,patient_id,user_id,status,active) values('".
				 $date."','".
				 $invoice_id."','".
				 $code."','".
				 $name."','".
				 $qty."','".
				 $request->selectpatient."','".
				 $user_id."','Y','O')";
	DB::select(DB::raw("$sqlInsertF"));
 
 }	
 }
}
 // save remise in inventory
 
 if ($request->NreqIDRemis!=''){
 $sqlReq = "update tbl_inventory_invoices_details set status='N' where invoice_id=".$request->invoice_id;   			   
 DB::select(DB::raw("$sqlReq"));		
 $last_id=$request->invoice_id;
$qst=0;
$gst=0; 
$Nbalance=0;
$sqlRequpdate = "update tbl_inventory_invoices_request set 
				  patient_id='".$request->selectpatient."',
				  type='99',
	              date_invoice='".$request->invoice_date_val."',
				  qst='".$qst."',
				  gst='".$gst."',
				  total='".$request->totalf."',
				  discount='".$request->tdiscount."',
				  inv_balance='".$Nbalance."',
				  clinic_num='".$request->clinic_id."',
				  notes='".$remark."',
				  user_id='".$user_id."',
				  active='O' where id=". $last_id;
  	     			   
		DB::select(DB::raw("$sqlRequpdate"));			
}else{
$qst=0;
$gst=0;

$sqlReq = "insert into tbl_inventory_invoices_request(fournisseur_id,patient_id,type,date_invoice,".
	              "reference,qst,gst,notes,total,discount,inv_balance,clinic_num,user_id,active)".
                  "values('0','".$request->selectpatient."','99','".
							$request->invoice_date_val."','".
							$invoice_id."','".
							$qst."','".
							$gst."','".$remark."','0.00','0.00','0.00','".
							$request->clinic_id."','".
							$user_id."','O')";
  	     
		DB::select(DB::raw("$sqlReq"));
        $last_id=DB::getPdo()->lastInsertId(); 
		//$reqID=$last_id;
		$FacInventory = TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->first();
		$SerieFacInventory = $FacInventory->rm_serial_code;
			$SeqFacInventory = $FacInventory-> rm_sequence_num ;
			$NreqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
		    TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->update([
				                  'rm_sequence_num' => $SeqFacInventory+1
								  ]);
		
		TblInventoryInvoicesRequest::where('id',$last_id)->update([
				                  'clinic_inv_num'=>$NreqID	
								  ]);	
foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0){			
   $remisenb = $area["REMISENB"]; 
   $code = $area["CODE"];
   $descrip = trim($area["NAME"]);
   $quantity=$area["QTY"];
   $price = "0.00";
   $discount = "0.00";
   $total = "0.00";
   $date_exp = $request->invoice_date_val;
   $taxable="N";
   $typediscount="0";
   //$taxable=($taxable0=='on')?'Y':'N';
   //no need for this
  // $result.= $code.",".$descrip.",".$quantity.",".$price.",".$discount.",".$total.PHP_EOL;
  if ($remisenb==''){
  $sqlInsertF = "insert into tbl_inventory_invoices_details(invoice_id,ref_invoice,item_code,item_name,qty,price,discount,
															total,date_exp,tdiscount,tax,notes,
															status,user_id,active) values('".
                 $last_id."','".
				 $invoice_id."','".
				 $code."','".
				 $descrip."','".
				 $quantity."','".
				 $price."','".
				 $discount."','".
				 $total."','".
				 $date_exp."','".
				 $typediscount."','".
				 $taxable."','".
				 $request->invoice_rq."','O','".
				 $user_id."','O')";
	DB::select(DB::raw("$sqlInsertF"));	
  }	
}
}
}
 
 
$msg=__('Remise Saved Success');
	return response()->json(['success'=>$msg,'NreqID'=>$NreqID]);


}	

//Generate PDf for label invoice
public function invoice_pdf_labels($lang,Request $request){
	 $invoice =TblInventoryInvoicesRequest::where('active','O')->where('clinic_inv_num',$request->id)->first();
	 $items = TblInventoryInvoicesDetails::select('item.gen_types','item.description','item.typecode',
	                                              'item.sel_price','item.barcode','item.items_specs','item.sku','four.name as supplier_name')
             ->join('tbl_inventory_items as item','item.id','tbl_inventory_invoices_details.item_code')
			 ->join('tbl_inventory_items_fournisseur as four','four.id','item.fournisseur')
			 ->where('tbl_inventory_invoices_details.invoice_id',$invoice->id)
			 ->where('item.typecode',1)
			 ->where('tbl_inventory_invoices_details.status','O')
			 ->get();
		//dd($items);	 
	
		$data = ['title' => __('Items'),'date' => date('m/d/Y'),'items'=>$items]; 
		$customPaper = array(0, 0, 180, 90.141732283 );
		$pdf = PDF::setOptions(['orientation' => 'landscape','defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
					->loadView('inventory.invoices.labelItemPDF', $data);
		$pdf->setPaper($customPaper);
		return $pdf->stream();
	
}


function GetInvoiceSalesNb($lang,Request $request){
$ReqInvPatient=TblInventoryInvoicesRequest::where('patient_id',$request->idpatient)
			->where('active','O')
			->where('type','2')
			->orderBy('id', 'desc')
            ->get();
$html='<option value="0">'.__("Undefined").'</option>';;
     foreach ($ReqInvPatient as $sReqInvPatient) {
		// $selected=($scollection->id==$item->brand)?'selected':'';
       //$html .= '<option value="' . $scollection->id.'" '.$selected.'>'.$scollection->name .'</option>';
    $html .= '<option value="' . $sReqInvPatient->id.'" >'.$sReqInvPatient->clinic_inv_num.','.__("Date").':'.$sReqInvPatient->date_invoice.'</option>';
		 }
			return response()->json(["html"=>$html]);   

}

function GetInvoicesNb($lang,Request $request){
  $sQLReqInvNb = "SELECT distinct(a.id) as id, a.clinic_inv_num,a.date_invoice 
                 from tbl_inventory_invoices_request a, tbl_inventory_invoices_details b 
				 where a.id = b.invoice_id and b.item_code ='".$request->selectcode."'  
				 and a.active = 'O' and a.type='1' and b.status='O' order by a.id desc";
        
	$ReqInvNb =DB::select(DB::raw("$sQLReqInvNb"));	
  $itm = DB::table('tbl_inventory_items')->find($request->selectcode);
  $supp=isset($itm) && isset($itm->fournisseur)?$itm->fournisseur:'0';

$html='<option value="0">'.__("Undefined").'</option>';;
     foreach ($ReqInvNb as $sReqInvNb) {
		// $selected=($scollection->id==$item->brand)?'selected':'';
       //$html .= '<option value="' . $scollection->id.'" '.$selected.'>'.$scollection->name .'</option>';
    $html .= '<option value="' . $sReqInvNb->id.'" >'.$sReqInvNb->clinic_inv_num.','.__("Date").':'.$sReqInvNb->date_invoice.'</option>';
		 }
			return response()->json(["supp"=>$supp,"html"=>$html]);   

}

function GetInvoiceSalesDetails($lang,Request $request){
	
$ReqInvDetails=TblInventoryInvoicesDetails::where('invoice_id',$request->idinvoice)
			->where('status','O')
            ->get();
$cptsCount= $ReqInvDetails->count();		
$cpts = 1;
$html1='<thead>
		<tr class="txt-bg text-white text-center">
		<th scope="col" style="font-size:16px;">'.__("Code").'</th>
		<th scope="col" style="font-size:16px;">'.__("Descrip").'</th>
		<th scope="col" style="font-size:16px;">'.__("Quantity").'</th>
		<th scope="col" style="font-size:16px;">'.__("Return Qty").'</th>
		<th scope="col" style="font-size:16px;">'.__("Price").'</th>
		<th scope="col" style="font-size:16px;">'.__("Discount").'</th>
		<th scope="col" style="font-size:16px;">'.__("Total").'</th>
		<th scope="col" style="font-size:16px;">'.__("T.Disc").'</th>
		<th scope="col" style="font-size:16px;">'.__("Tax").'</th>
		<th scope="col"></th>
		<th scope="col" style="font-size:16px;display:none;">'.__("TVQ").'</th>
		<th scope="col" style="font-size:16px;display:none;">'.__("TPS").'</th>
		<th scope="col" style="font-size:16px;display:none;">'.__("ID").'</th>
		<th scope="col" style="font-size:16px;">'.__("Ret Qty").'</th>
		</tr>
		</thead>
		<tbody>';
foreach ($ReqInvDetails as $sReqInvDetails) {
$Ret_qty= ' onchange=RetRow(this,"retqty'.$cpts.'")';
if ($sReqInvDetails->rqty>=$sReqInvDetails->qty){
	
$qtyDsisable=' disabled';
}else{
$qtyDsisable=' ';
}
		$html1 .='<tr>
		<td>'.$sReqInvDetails->item_code.'</td>
		<td>'.$sReqInvDetails->item_name.'</td>
		<td>'.$sReqInvDetails->qty.'</td>
		<td><input type="text" class="form-control" size="3"  id="retqty'.$cpts.'" value="'.$sReqInvDetails->qty.'"'.$Ret_qty.$qtyDsisable.' ></td>
		<td>'.$sReqInvDetails->price.'</td>
		<td>'.$sReqInvDetails->discount.'</td>
		<td>'.$sReqInvDetails->total.'</td>
		<td>'.$sReqInvDetails->tdiscount.'</td>
		<td>'.$sReqInvDetails->tax.'</td>
		<td><input type="checkbox" class="btn btn-delete" id="rowdelete'.$cpts.'"  onclick="deleteRow(this)"'.$qtyDsisable.' ></td>
		<td style="display:none;">'.$sReqInvDetails->qst.'</td>
		<td style="display:none;">'.$sReqInvDetails->gst.'</td>
		<td style="display:none;">'.$sReqInvDetails->id.'</td>
		<td>'.$sReqInvDetails->rqty.'</td>
	</tr>';
$cpts++;
}

  $html1 .='</tbody>';
  	$ReqPatient=TblInventoryInvoicesRequest::where('id',$request->idinvoice)->where('active','O')->first();

    $pay = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $request->idinvoice)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('payment_amount');
	$refund = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $request->idinvoice)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('payment_amount');

$balance=$ReqPatient->inv_balance;
$balance=floatval(preg_replace('/[^\d.-]/', '', $balance));
   $total=$ReqPatient->total;
    $qst=$ReqPatient->qst;
	 $gst=$ReqPatient->gst;
	 $tdiscount=$ReqPatient->discount;
	  $stotal=$ReqPatient->total+$ReqPatient->qst+$ReqPatient->gst;
return response()->json(['html1' => $html1,'pay'=>$pay,'refund'=>$refund,
                         'balance'=>$balance,'stotal'=>$stotal,'tdiscount'=>$tdiscount,
						 'total'=>$total,'gst'=>$gst,'qst'=>$qst,'cpts'=>$cpts
						 ]);
	
}	

function GetInvoicesDetails($lang,Request $request){
	
$ReqInvDetails=TblInventoryInvoicesDetails::where('invoice_id',$request->idinvoice)
			->where('status','O')
			->where('active','O')
			->get();          
//	->whereNotIn('item_code', function($query) {$query->select('item_code')->from('tbl_inventory_invoices_details_return');})
$cptsCount= $ReqInvDetails->count();		
$cpts = 1;
$html1='<thead>
		<tr class="txt-bg text-white text-center">
		<th scope="col" style="font-size:16px;">'.__("Code").'</th>
		<th scope="col" style="font-size:16px;">'.__("Descrip").'</th>
		<th scope="col" style="font-size:16px;">'.__("Quantity").'</th>
		<th scope="col" style="font-size:16px;">'.__("Return Qty").'</th>
		<th scope="col" style="font-size:16px;">'.__("Price").'</th>
		<th scope="col" style="font-size:16px;">'.__("Discount").'</th>
		<th scope="col" style="font-size:16px;">'.__("Total").'</th>
		<th scope="col" style="font-size:16px;">'.__("Expiry Date").'</th>
		<th scope="col" style="font-size:16px;">'.__("T.Disc").'</th>
		<th scope="col" style="font-size:16px;">'.__("Tax").'</th>
		<th scope="col" style="font-size:16px;display:none;">'.__("Sel Price").'</th>
		<th scope="col" style="font-size:16px;display:none;">'.__("Formula ID").'</th>
		<th scope="col" style="font-size:16px;display:none;">'.__("Initial Price").'</th>
		<th scope="col"></th>
		<th scope="col" style="font-size:16px;display:none;">'.__("TVQ").'</th>
		<th scope="col" style="font-size:16px;display:none;">'.__("TPS").'</th>
		<th scope="col" style="font-size:16px;display:none;">'.__("ID").'</th>
		<th scope="col" style="font-size:16px;">'.__("Ret Qty").'</th>
		</tr>
		</thead>
		<tbody>';
foreach ($ReqInvDetails as $sReqInvDetails) {
	$Ret_qty= ' onchange=RetRow(this,"retqty'.$cpts.'")';
if ($sReqInvDetails->rqty>=$sReqInvDetails->qty){
	
$qtyDsisable=' disabled';
}else{
$qtyDsisable=' ';
}
		$html1 .='<tr>
		<td>'.$sReqInvDetails->item_code.'</td>
		<td>'.$sReqInvDetails->item_name.'</td>
		<td>'.$sReqInvDetails->qty.'</td>
		<td><input type="text" class="form-control" size="3"  id="retqty'.$cpts.'" value="'.$sReqInvDetails->qty.'"'.$Ret_qty.$qtyDsisable.' ></td>
		<td>'.$sReqInvDetails->price.'</td>
		<td>'.$sReqInvDetails->discount.'</td>
		<td>'.$sReqInvDetails->total.'</td>
		<td>'.$sReqInvDetails->date_exp.'</td>
		<td>'.$sReqInvDetails->tdiscount.'</td>
		<td>'.$sReqInvDetails->tax.'</td>
		<td style="display:none;">'.$sReqInvDetails->formula_id.'</td>
		<td style="display:none;">'.$sReqInvDetails->price.'</td>
		<td style="display:none;">'.$sReqInvDetails->sel_price.'</td>
		<td><input type="checkbox" class="btn btn-delete" id="rowdelete'.$cpts.'"  onclick="deleteRow(this)"'.$qtyDsisable.' ></td>
		<td style="display:none;">'.$sReqInvDetails->qst.'</td>
		<td style="display:none;">'.$sReqInvDetails->gst.'</td>
		<td style="display:none;">'.$sReqInvDetails->id.'</td>
		<td>'.$sReqInvDetails->rqty.'</td>
		</tr>';
$cpts++;
}
  $html1 .='</tbody>';
  	$ReqPatient=TblInventoryInvoicesRequest::where('id',$request->idinvoice)->where('active','O')->first();

    $pay = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $request->idinvoice)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('payment_amount');
	$refund = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $request->idinvoice)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('payment_amount');

$balance=$ReqPatient->inv_balance;
$balance=floatval(preg_replace('/[^\d.-]/', '', $balance));
   $total=$ReqPatient->total;
    $qst=$ReqPatient->qst;
	 $gst=$ReqPatient->gst;
	 $tdiscount=$ReqPatient->discount;
	  $stotal=$ReqPatient->total+$ReqPatient->qst+$ReqPatient->gst;
return response()->json(['html1' => $html1,'pay'=>$pay,'refund'=>$refund,
                         'balance'=>$balance,'stotal'=>$stotal,'tdiscount'=>$tdiscount,
						 'total'=>$total,'gst'=>$gst,'qst'=>$qst,'cpts'=>$cpts
						 ]);
	
}


//get patients list created date : 26-4-2023
public function inv_pat_list($lang,Request $request){
 $inv = TblInventoryInvoicesRequest::find($request->inv_id);
 $status_patient = NULL;
  
 if(isset($inv) && isset($inv->inv_email_pat)){
	 $status_patient= __("Sent by email").":".Carbon::parse($inv->inv_email_pat)->format('Y-m-d H:i');
 }
 
 $patient=Patient::select('id','first_name','last_name','fax','email','receive_mail')->where('id',$request->patient_num)->first();
 
 $html_patient= view('inventory.invoices.send_by.patient_data')->with(['patient'=>$patient,'status_patient'=>$status_patient])->render();

 return response()->json(['html_patient'=>$html_patient]);	
}

//send email patient created date : 26-4-2023
public function send_email_pat($lang,Request $request){
   $desc = $request->desc;
   $Invoice =TblInventoryInvoicesRequest::where('active','O')->where('id',$request->inv_id)->first();
   $clinic=Clinic::where('active','O')->where('id',$Invoice->clinic_num)->first();
   $result=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$Invoice->id)->get();
   $qst=($Invoice->total*$Invoice->qst)/100;
   $gst=($Invoice->total*$Invoice->gst)/100;
   $qst=number_format((float)$qst, 3, '.', '');
   $gst=number_format((float)$gst, 3, '.', '');
   $logo=DB::table('tbl_bill_logo')->where('clinic_num',$Invoice->clinic_num)->where('status','O')->first();
   $lang = app()->getLocale();
   $type = $Invoice->type;
   $serial = TblInventoryInvSerials::where('clinic_num',$clinic->id)->first();            
   $patient=Patient::where('status','O')->where('id',$Invoice->patient_id)->first();
   $email = $patient->email;
   $pay=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
										DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
										 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
										'tbl_inventory_payment.payment_amount as pay_amount',
										DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name"))              
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$Invoice->id)
						 ->where('payment_type','P')
						 ->get();
   $ref=TblInventoryPayment::select(DB::raw("ANY_VALUE(tbl_inventory_payment.deposit) as deposit"),
										DB::raw("ANY_VALUE(tbl_inventory_payment.remark) as remark"),
										 DB::raw("DATE(tbl_inventory_payment.datein) as pay_date"),
										'tbl_inventory_payment.payment_amount as pay_amount',
										DB::raw("IF('$lang'='en',tbl_bill_payment_mode.name_eng,tbl_bill_payment_mode.name_fr) as pay_name")) 
						 ->join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
						 ->where('tbl_bill_payment_mode.status', 'O')
						 ->where('tbl_inventory_payment.status','Y')
						 ->where('invoice_num',$Invoice->id)
						 ->where('payment_type','R')
						 ->get();
				              				
   $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $Invoice,'clinic' => $clinic,
         'patient' =>$patient,'result' => $result,'pay' =>$pay,'ref'=>$ref,'serial'=>$serial,'qst'=>$qst,'gst'=>$gst,'desc'=>$desc]; 
            
   $pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
				-> loadView('inventory.invoices.pdfs.salePDF', $data);
   $pdf->output();
   $dom_pdf = $pdf->getDomPDF();
   $canvas = $dom_pdf->get_canvas();
   $canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
   //is checked email
   $is_checked_email = $request->is_checked_email; 
   $resp_email='';
   if($is_checked_email=="true"){
	   $data = TblInventoryInvoicesRequest::select(DB::raw("DATE(tbl_inventory_invoices_request.date_invoice) as date_invoice"),
	                                            DB::raw("CONCAT(pat.first_name,' ',pat.last_name) as pat_name"),
							                    'clin.full_name as branch_name','clin.telephone as branch_tel',
												'clin.fax as branch_fax','clin.email as branch_email',
												'clin.full_address as branch_address','tbl_inventory_invoices_request.clinic_inv_num')
	                    ->join('tbl_patients as pat','pat.id','tbl_inventory_invoices_request.patient_id')
	                    ->join('tbl_clinics as clin','clin.id','tbl_inventory_invoices_request.clinic_num')
						->where('tbl_inventory_invoices_request.id',$request->inv_id)->first();
   
       $logo=public_path("storage/images/logo-120-120.jpg");
	   $logo = str_replace(config('app.src_url'),config("app.url"),$logo);
	   $title=__('Hello').' '.$data->pat_name.' , ';
	   $msg1=__('You will find attached the invoice').' . ';
	   $msg2=__('This invoice is sent from branch').' : '.$data->branch_name.' . ';
	    $from = $data->branch_name;
	   $reply_to_name = __("No reply").','.$data->branch_name;
	   $reply_to_address = isset($data->branch_email)? $data->branch_email:'noreply@email.com';
	   $subject = __('Invoice').' '.$data->clinic_inv_num;
	   $details = ['logo'=>$logo,'title'=>$title,'msg1'=>$msg1,'msg2'=>$msg2,
	               'branch_name'=>$data->branch_name,'branch_address'=>$data->branch_address,'branch_tel'=>$data->branch_tel,
				   'branch_fax'=>$data->branch_fax,'branch_email'=>$data->branch_email,
				   'from'=>$from,'reply_to_name'=>$reply_to_name,'reply_to_address'=>$reply_to_address,'subject'=>$subject];
				   
	   $to  = $email;
	   
	   $pdf_name = 'INV-'.$data->date_invoice.'.pdf';
	   
	   Mail::to($to)->send(new SettingMailAttach($details,$pdf->output(),$pdf_name));
	    if (Mail::failures()) {
				$resp_email.= __("Email: fail");
			}else{
				$resp_email.= __("Email : success");
				$inv_email_pat = Carbon::now()->format('Y-m-d H:i');
				 TblInventoryInvoicesRequest::where('id',$request->inv_id)
				                            ->update(['inv_email_pat'=>$inv_email_pat]);
			}
   
   }
   return response()->json(["success"=>$resp_email]);

				
}


public function save_cmd($lang,Request $request){
	$id = $request->invoice_id;
	$date = $request->invoice_date_val;
	$note = $request->comment;
	$user_num = auth()->user()->id;
	TblInventoryInvoicesRequest::where('id',$id)->update(['user_id'=>$user_num,'date_invoice'=>$date,'notes'=>$note]);
	return response()->json(["success"=>__("Inventory Saved Success")]);
}	


public function to_account($lang,Request $request){
	$account_id = $request->account_id;
	$account = TblInventoryInvoicesRequest::find($account_id);
	
   if( isset($account->nbaccount) ){
	   $clinic_inv_num = $account->clinic_inv_num;
	   
	    $location = route('EditAccounting',[app()->getLocale(),$account_id]);
	  
   }else{
	   //create invoice for this new user
	  $uid = auth()->user()->id; 
     
	  $invRequest=TblInventoryInvoicesRequest::where('id',$account_id)->first();	  
      $etamount1="0.000";
		$etamount2="0.000";
		$dtamount1="0.000";
		$dtamount2="0.000";
		$ltamount1="0.000";
		$ltamount2="0.000";
        $datein = Carbon::now()->format("Y-m-d H:i");
	
	   $sqlReq = "insert into tbl_account_head(datein,dateout,refer,rq,".
	              "type,etamount1,etamount2,ltamount1,ltamount2,dtamount1,dtamount2,clinic_num,user_id,active)".
                  "values('".$datein."','".
							$datein."','".
							$invRequest->clinic_inv_num."','".
							$invRequest->notes."','".
							'0'."','".
							$etamount1."','".
							$etamount2."','".
							$ltamount1."','".
							$ltamount2."','".
							$invRequest->total."','".
							$invRequest->total."','".
							$invRequest->clinic_num."','".
							$uid."','O')";
  	     
		DB::select(DB::raw("$sqlReq"));
        $last_id=DB::getPdo()->lastInsertId(); 
	
		$FacInventory = TblInventoryInvSerials::where('clinic_num',$invRequest->clinic_num)->first();
      
			$SerieFacAcc = $FacInventory->acc_serial_code;
			$SeqFacAcc = $FacInventory-> acc_sequence_num ;
			$reqID=trim($SerieFacAcc)."-".($SeqFacAcc+1);
		    TblInventoryInvSerials::where('clinic_num',$invRequest->clinic_num)->update([
				                  'acc_sequence_num' => $SeqFacAcc+1
								  ]);
		TblAccountRequest::where('id',$last_id)->update([
				                  'serial'=>$reqID	
								  ]);	
		$Founisseur1 = TblInventoryItemsFournisseur::where('id',$invRequest->fournisseur_id)->first();
		$Founisseur2 = TblInventoryItemsFournisseur::where('id','117')->first();

$sqlInsertF = "insert into tbl_account_details(account_id,serial,admi1,name1,amount1,curency1,prcurren1,admi2,name2,amount2,curency2,prcurren2,notes,status,user_id,active) values('".
                 $last_id."','".
				 $reqID."','".
				 '117'."','".
				 $Founisseur2->name."','".
				 $invRequest->total."','".
				 'USD'."','".
				 '1.00'."','".
				 $invRequest->fournisseur_id."','".
				 $Founisseur1->name."','".
				 $invRequest->total."','".
				 'USD'."','".
				 '.00'."','".
				 ''."','O','".
				 $uid."','O')";
	DB::select(DB::raw("$sqlInsertF"));
	$last_id_details=DB::getPdo()->lastInsertId(); 
    TblInventoryInvoicesRequest::where('id',$account_id)->update(['nbaccount'=>$reqID]);	  
	  $location = route('EditAccounting',[app()->getLocale(),$last_id]);							  
}
	    
	  
	  	
	return response()->json(["location"=>$location,'nbaccount'=>$reqID]);
}

	  		  
}








