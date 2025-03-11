<?php
/*
* DEV APP
* Created date : 10-11-2022
*  
* update functions from time to time till today
*
*/
namespace App\Http\Controllers\inventory\reports;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\TblInventoryItemsFournisseur;

use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;

use App\Models\EventStates;
use DB;
use Illuminate\Http\Request;
use Alert;
use DataTables;
use Session;
use UserHelper;
use PDF;

//Created date 10-5-2023
class InventoryReportsController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index($lang) 
    {
	
	$user_type=auth()->user()->type;
    $clinic_num = '';	
		switch($user_type){
		case 1:
		 $clinic_num = Session::get('inventory_branch_num');
		 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
							 
		break;
		case 2:
		
		 $clinic_num =auth()->user()->clinic_num;
		 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
		break;					 
		}
   
    $patients = Patient::where('clinic_num',$clinic_num)->get();
    $items = DB::table('tbl_inventory_fournisseur_types')->where('active','O')->get();
    $suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();  
	$payment_types =DB::table('tbl_bill_payment_mode')->where('status','O')->where('clinic_num',$clinic_num)->get(['id','name_eng','name_fr']);

	return view('inventory.reports.menu.index')->with(['resource'=>$resource,'payment_types'=>$payment_types,'items'=>$items,'patients'=>$patients,'suppliers'=>$suppliers]);	
	}
	

public function inventory_pay_refund($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
    
	$patients = Patient::select('id','first_name','last_name')->where('clinic_num',$clinic_num)->get();

	$payment_types =DB::table('tbl_bill_payment_mode')->where('status','O')->where('clinic_num',$clinic_num)->get(['id','name_eng','name_fr']);
	
	if($request->ajax()){
		   
		   $filter_patient="";
           	if(isset($request->filter_patient) && $request->filter_patient!="0" ){
			 $filter_patient = "and a.patient_id=".$request->filter_patient; 
			  
		    }
		   
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $filter_date= "and DATE(b.datein) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
			
			if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $filter_date= "and DATE(b.datein) <= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}		
				
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			     $filter_date = "and DATE(b.datein) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}	
			 
		  		  
		  
		  /*$filter_payment="";
		  if(isset($request->filter_payment) && $request->filter_payment!="0" ){
			 $filter_payment = "and b.payment_type='".$request->filter_payment."' ";  
		      }*/
			  
	      $filter_payment_type="";
		  if(isset($request->filter_payment_type) && $request->filter_payment_type!="0" ){
			 $filter_payment_type = "and e.id='".$request->filter_payment_type."' ";  
		      }
		  
		  $filter_due_amount="";
		  if(isset($request->filter_due_amount) && $request->filter_due_amount!="0" ){
			 $filter_due_amount = "and a.inv_balance>0";  
		      }
        
           $filter_inv_type="and (a.type='2' or a.type='3')";
         if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
			 if($request->filter_inv_type=='RP'){
			 $filter_inv_type = " and a.type = 3 and (a.cr_note='N' or a.cr_note IS NULL)";
			 }
			 if($request->filter_inv_type=='CN'){
			 $filter_inv_type = " and a.type = 3 and a.cr_note='Y' ";
			 }
		  }			
			 
			 $payment_name=($lang=='en')?'e.name_eng':'e.name_fr'; 
			
			
			  $data="SELECT b.datein as payment_date, concat(c.first_name,' ',c.last_name) as patName,
					d.full_name as branch_name,if(b.payment_type='P',b.payment_amount,-1*b.payment_amount) as payment_amount,CONCAT(IF(a.is_warranty='Y',CONCAT(a.clinic_inv_num,',','".__("Warranty")."'),a.clinic_inv_num),';',IFNULL(a.reference,'')) as bill_nb,
					DATE(a.date_invoice) as bill_date,{$payment_name} as payment_name,
					if(b.payment_type='P','Payment',if(b.payment_type='R','Refund','ND'))  as payment_type,
					IF(a.type=3,-1*(a.total+a.gst+a.qst),a.total+a.gst+a.qst) as total_pay,
					a.inv_balance as solde_du,a.type
					FROM  tbl_inventory_invoices_request a
					INNER JOIN tbl_inventory_payment b ON   b.invoice_num=a.id and b.status='Y'
					INNER JOIN tbl_patients c ON c.id = a.patient_id 
					INNER JOIN tbl_clinics d ON d.id = a.clinic_num AND d.active='O'
					INNER JOIN tbl_bill_payment_mode e ON   e.id=b.reference and e.status='O'
					WHERE a.active='O' and a.clinic_num='".$clinic_num."'  ".$filter_inv_type." ".$filter_patient."  ".$filter_date." ".$filter_payment_type." ".$filter_due_amount."
					ORDER BY b.reference desc,a.date_invoice desc
			 	     ";
              
            $bills_payments= DB::select(DB::raw("$data"));
			
			
			
		    return response()->json($bills_payments);				 
			  
	   }
      
	  return view('inventory.reports.inventory_list.inventory_payments')->with(['resource'=>$resource,'payment_types'=>$payment_types,'patients'=>$patients]);	
	 
	 
  }



public function inventory_sales($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
	
	$patients = Patient::where('clinic_num',$clinic_num)->get();
	$suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();

	
if($request->ajax()){	
	$filter_patient="";
           	if(isset($request->filter_patient) && $request->filter_patient!="0" ){
			 $filter_patient = "and tbl_inventory_invoices_request.patient_id=".$request->filter_patient; 
			  
		    }
			
			
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $filter_date= "and DATE(tbl_inventory_invoices_request.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
			
			if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $filter_date= "and DATE(tbl_inventory_invoices_request.date_invoice) <= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}		
				
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			     $filter_date = "and DATE(tbl_inventory_invoices_request.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}	
			 
		  $filter_due_amount="";
		  if(isset($request->filter_due_amount) && $request->filter_due_amount!="0" ){
			 $filter_due_amount = "and tbl_inventory_invoices_request.inv_balance>0";  
		      } 
		
         $filter_inv_type="and (tbl_inventory_invoices_request.type='2' or tbl_inventory_invoices_request.type='3')";
         if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
			 if($request->filter_inv_type=='RP'){
			 $filter_inv_type = " and tbl_inventory_invoices_request.type = 3 and (tbl_inventory_invoices_request.cr_note='N' or tbl_inventory_invoices_request.cr_note IS NULL)";
			 }
			 if($request->filter_inv_type=='CN'){
			 $filter_inv_type = " and tbl_inventory_invoices_request.type = 3 and tbl_inventory_invoices_request.cr_note='Y' ";
			 }
		  }	
		  
	   //dd($filter_inv_type);
	$undefined = __("Undefined");
	$data="select tbl_inventory_invoices_request.id as id,tbl_fournisseur.name,
			   CONCAT(IF(tbl_inventory_invoices_request.is_warranty='Y',CONCAT(tbl_inventory_invoices_request.clinic_inv_num,',','".__("Warranty")."'),tbl_inventory_invoices_request.clinic_inv_num),';',IFNULL(tbl_inventory_invoices_request.cmd_id,'nul'),';',IF(tbl_inventory_invoices_request.reference IS NOT NULL and tbl_inventory_invoices_request.reference<>'',tbl_inventory_invoices_request.reference,'nul')) as id_invoice, 
			   tbl_types.name as typename,
			   DATE_FORMAT(tbl_inventory_invoices_request.date_invoice,'%Y-%m-%d %H:%i') as date_invoice,
			   IF(tbl_inventory_invoices_request.type=3,-1*tbl_inventory_invoices_request.total,tbl_inventory_invoices_request.total) as sub_total,
			   IF(tbl_inventory_invoices_request.type=3,-1*tbl_inventory_invoices_request.qst,tbl_inventory_invoices_request.qst) as qst,
			   IF(tbl_inventory_invoices_request.type=3,-1*tbl_inventory_invoices_request.gst,tbl_inventory_invoices_request.gst) as gst,
			   tbl_inventory_invoices_request.type,
			   IF(tbl_inventory_invoices_request.type=3,-1*(tbl_inventory_invoices_request.qst+tbl_inventory_invoices_request.gst+tbl_inventory_invoices_request.total),tbl_inventory_invoices_request.qst+tbl_inventory_invoices_request.gst+tbl_inventory_invoices_request.total) as total,
			   CONCAT(tbl_patients.first_name,' ',tbl_patients.last_name,',',IFNULL(tbl_patients.ramq,'".$undefined."')) AS patDetail,tbl_inventory_invoices_request.active,clin.full_name as branch_name,
			   tbl_inventory_invoices_request.inv_balance as solde_du,ANY_VALUE(sum(if(b.payment_type='P',IFNULL(b.payment_amount,0.00),-1*IFNULL(b.payment_amount,0.00)))) as montant_payer
			   from tbl_inventory_invoices_request 
			   LEFT JOIN tbl_inventory_items_fournisseur as tbl_fournisseur on tbl_fournisseur.id=tbl_inventory_invoices_request.fournisseur_id 
			   LEFT JOIN tbl_inventory_types as tbl_types on tbl_types.id=tbl_inventory_invoices_request.type 
			   INNER JOIN tbl_patients ON tbl_patients.id=tbl_inventory_invoices_request.patient_id 
			   LEFT JOIN tbl_inventory_payment b ON  b.invoice_num=tbl_inventory_invoices_request.id and b.status='Y'
			   INNER JOIN tbl_clinics as clin ON clin.id=tbl_inventory_invoices_request.clinic_num and clin.active='O'
			   where tbl_inventory_invoices_request.active='O'  and tbl_inventory_invoices_request.clinic_num='".$clinic_num."'  ".$filter_date."  ".$filter_patient." ".$filter_due_amount." ". $filter_inv_type." 
			   GROUP BY tbl_inventory_invoices_request.id";
	  
	  
	  
	  $sales = DB::select(DB::raw("$data"));
      
       
      return Datatables::of($sales)->addIndexColumn()->make(true);	
	   }

  
	  return view('inventory.reports.inventory_list.inventory_sales')->with(['resource'=>$resource,'patients'=>$patients]);	
	     
		   
} 


public function inventory_purchases($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
  
	$suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();

if($request->ajax()){	
	
		   $filter_supplier="";
		   if( $request->filter_supplier!="0" &&  $request->filter_supplier!=NULL){
			  $filter_supplier = " and tbl_inventory_invoices_request.fournisseur_id = '".$request->filter_supplier."' "; 
		   }
		   
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $filter_date= "and DATE(tbl_inventory_invoices_request.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
			
			if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $filter_date= "and DATE(tbl_inventory_invoices_request.date_invoice) <= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}		
				
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			     $filter_date = "and DATE(tbl_inventory_invoices_request.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}	
			 
		  
		   $filter_due_amount="";
		  if(isset($request->filter_due_amount) && $request->filter_due_amount!="0" ){
			 $filter_due_amount = "and tbl_inventory_invoices_request.inv_balance>0";  
		      } 
		  
		  $filter_inv_type="and (tbl_inventory_invoices_request.type='1' or tbl_inventory_invoices_request.type='4' )";
          
		  if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
			 $filter_inv_type = " and tbl_inventory_invoices_request.type = 4";
			
			}	
		  
	
	$undefined = __("Undefined");


    $data="select tbl_inventory_invoices_request.id as id,tbl_fournisseur.name,
		   CONCAT(IFNULL(tbl_inventory_invoices_request.clinic_inv_num,''),';',IF(tbl_inventory_invoices_request.cr_note='G','".__("Warranty")."',''),';',IF(tbl_inventory_invoices_request.reference IS NULL or tbl_inventory_invoices_request.type=1,'',tbl_inventory_invoices_request.reference)) as id_invoice, 
		   tbl_types.name as typename,tbl_inventory_invoices_request.cr_note, 
		   DATE_FORMAT(tbl_inventory_invoices_request.date_invoice,'%Y-%m-%d') as date_invoice,
		   IF(tbl_inventory_invoices_request.type=4,-1*tbl_inventory_invoices_request.total,tbl_inventory_invoices_request.total) as sub_total,
		   IF(tbl_inventory_invoices_request.type=4,-1*tbl_inventory_invoices_request.qst,tbl_inventory_invoices_request.qst) as qst,
		   IF(tbl_inventory_invoices_request.type=4,-1*tbl_inventory_invoices_request.gst,tbl_inventory_invoices_request.gst) as gst,
		   IF(tbl_inventory_invoices_request.type=4,-1*(tbl_inventory_invoices_request.total+tbl_inventory_invoices_request.qst+tbl_inventory_invoices_request.gst),tbl_inventory_invoices_request.total+tbl_inventory_invoices_request.qst+tbl_inventory_invoices_request.gst) as total,
		   tbl_inventory_invoices_request.active,clin.full_name as branch_name,
		   tbl_inventory_invoices_request.inv_balance as solde_du,ANY_VALUE(sum(if(b.payment_type='P',IFNULL(b.payment_amount,0.00),-1*IFNULL(b.payment_amount,0.00)))) as montant_payer
		   from tbl_inventory_invoices_request 
		   LEFT JOIN tbl_inventory_items_fournisseur as tbl_fournisseur on tbl_fournisseur.id=tbl_inventory_invoices_request.fournisseur_id 
		   LEFT JOIN tbl_inventory_types as tbl_types on tbl_types.id=tbl_inventory_invoices_request.type 
		   LEFT JOIN tbl_inventory_payment b ON  b.invoice_num=tbl_inventory_invoices_request.id and b.status='Y'
		   INNER JOIN tbl_clinics as clin ON clin.id=tbl_inventory_invoices_request.clinic_num
		   where tbl_inventory_invoices_request.active='O'  and tbl_inventory_invoices_request.clinic_num='".$clinic_num."' ".$filter_date." ".$filter_supplier." ".$filter_inv_type." ".$filter_due_amount." 		       
		   GROUP BY tbl_inventory_invoices_request.id";
		   
      $purchases = DB::select(DB::raw("$data"));
      
	  
      return Datatables::of($purchases)->addIndexColumn()->make(true);	
	 
	 }

  
	  return view('inventory.reports.inventory_list.inventory_purchases')->with(['resource'=>$resource,'suppliers'=>$suppliers]);	
	     	 
}

public function inventory_sales_per_item($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
	
	$patients = Patient::where('clinic_num',$clinic_num)->get();
	$suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();
    $items = DB::table('tbl_inventory_fournisseur_types')->where('active','O')->orderBy('id','desc')->get();
  	$categorys = DB::table('tbl_inventory_category_types')->where('active','O')->orderBy('id','desc')->get();
	$allitems = DB::table('tbl_inventory_items')->where('active','O')->orderBy('id','desc')->get();
   
		if($request->ajax()){   
		   
		   $filter_supplier="";
		   if( $request->filter_supplier !="0" &&  $request->filter_supplier !=NULL){
			  $filter_supplier = " and supp.id = '".$request->filter_supplier."' "; 
		   }
		  $filter_category="";
		   if( $request->filter_category !="0" &&  $request->filter_category !=NULL){
			  $filter_category = " and item.category = '".$request->filter_category."' "; 
		   }
		   
		   $filter_item="";
		   if( $request->filter_item !="0" &&  $request->filter_item !=NULL){
			  $filter_item = " and item_type.id = '".$request->filter_item."' "; 
		   }
		  
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate =="" || $request->filter_todate ==NULL) ){
			     $filter_date= "and DATE(req.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
				
			if( ($request->filter_fromdate =="" && $request->filter_fromdate ==NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date= "and DATE(req.date_invoice) <= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}	
				
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date = "and DATE(req.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}		
			$filter_inv_type="and (req.type='2' or req.type='3')";
			 if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
				 if($request->filter_inv_type=='RP'){
				 $filter_inv_type = " and req.type = 3 and (req.cr_note='N' or req.cr_note IS NULL)";
				 }
				 if($request->filter_inv_type=='CN'){
				 $filter_inv_type = " and req.type = 3 and req.cr_note='Y' ";
				 }
			  }					 
		   
       
	   $data = "select req.id, DATE_FORMAT(req.date_invoice,'%Y-%m-%d') as date_invoice,
	                   CONCAT(IF(req.is_warranty='Y',CONCAT(req.clinic_inv_num,',','".__("Warranty")."'),req.clinic_inv_num),';',IFNULL(req.reference,'')) as invoice_num,
	                   CASE WHEN item_type.name='Glass Frame' THEN '".__("Glass Frame")."'
    					   WHEN item_type.name='Contact Lens' THEN '".__("Contact Lens")."'
						   WHEN item_type.name='Products' THEN '".__("Products")."'
    					   WHEN item_type.name='Lens' THEN '".__("Lens")."'
					    END 	   
					    as item_type,
					    det.qty,det.price,det.discount,
						IF(req.type=3,-1*(det.total),det.total) as subtotal,
						IF(req.type=3,-1*IFNULL(det.qst,0),IFNULL(det.qst,0)) as qst,
						IF(req.type=3,-1*IFNULL(det.gst,0),IFNULL(det.gst,0)) as gst,
						IF(req.type=3,-1*(det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)),det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)) as total, 
					    CONCAT(pat.first_name,' ',pat.last_name) AS patName,supp.name as supplier_name,clin.full_name as branch_name,
					    item.description as item_name,IF(req.type=3,-1*item.cost_price*det.qty,item.cost_price*det.qty) as cost_price,
					    IF(req.type=3,-1*(det.total-(item.cost_price*det.qty)),det.total-(item.cost_price*det.qty)) as profit,
					    IF(item.cost_price=0.00,IF(req.type=3,-1*det.total,det.total),IF(type=3,-1*( (det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty)),(det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty))) as profit_percent
					    from tbl_inventory_invoices_request as req
					    INNER JOIN tbl_inventory_invoices_details as det ON det.status='O' and det.invoice_id=req.id
	                    INNER JOIN tbl_inventory_items as item ON item.id=det.item_code  and typecode=1 and item.active='O'
					    INNER JOIN tbl_inventory_fournisseur_types as item_type ON item_type.id=item.gen_types and item_type.active='O'
					    INNER JOIN tbl_patients as pat ON pat.id=req.patient_id 
					    INNER JOIN tbl_inventory_items_fournisseur as supp ON supp.id=item.fournisseur and supp.active='O'
					    INNER JOIN tbl_clinics as clin ON clin.id = req.clinic_num and clin.active='O'
	                    where req.active='O' and req.clinic_num='".$clinic_num."' ".$filter_inv_type." ".$filter_date." ".$filter_item." ".$filter_category." ".$filter_supplier."  
	                    order by  item_type.name, req.date_invoice desc";
	   
	   $sales_per_item = DB::select(DB::raw("$data"));
	   
	   return response()->json($sales_per_item);
		}
	  return view('inventory.reports.inventory_list.inventory_sales_per_item')->with(['allitems'=>$allitems,'categorys'=>$categorys,'resource'=>$resource,'patients'=>$patients,'suppliers'=>$suppliers,'items'=>$items]);	
	    
} 

public function inventory_sales_per_supplier($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
	
	$patients = Patient::where('clinic_num',$clinic_num)->get();
	$suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();
    $items = DB::table('tbl_inventory_fournisseur_types')->where('active','O')->orderBy('id','desc')->get();
  	 
		  
		if($request->ajax()){   
		   
		   $filter_supplier="";
		   if( $request->filter_supplier !="0" &&  $request->filter_supplier !=NULL){
			  $filter_supplier = " and supp.id = '".$request->filter_supplier."' "; 
		   }
		   
		   $filter_patient="";
           	if($request->filter_patient !=NULL && $request->filter_patient !="0" ){
			 $filter_patient = "and pat.id='".$request->filter_patient."' "; 
			  
		    }
		   
		   $filter_item="";
		   if( $request->filter_item !="0" &&  $request->filter_item !=NULL){
			  $filter_item = " and item_type.id = '".$request->filter_item."' "; 
		   }
		  
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate =="" || $request->filter_todate ==NULL) ){
			     $filter_date= "and DATE(req.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
				
			if( ($request->filter_fromdate =="" && $request->filter_fromdate ==NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date= "and DATE(req.date_invoice) <= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}	
				
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date = "and DATE(req.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}		
			
            $filter_inv_type="and (req.type='2' or req.type='3')";
			 if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
				 if($request->filter_inv_type=='RP'){
				 $filter_inv_type = " and req.type = 3 and (req.cr_note='N' or req.cr_note IS NULL)";
				 }
				 if($request->filter_inv_type=='CN'){
				 $filter_inv_type = " and req.type = 3 and req.cr_note='Y' ";
				 }
			  }			
       
	   $data = "select req.id, DATE_FORMAT(req.date_invoice,'%Y-%m-%d') as date_invoice,
						CONCAT(IF(req.is_warranty='Y',CONCAT(req.clinic_inv_num,',','".__("Warranty")."'),req.clinic_inv_num),';',IFNULL(req.reference,'')) as invoice_num,	                    
						CASE WHEN item_type.name='Glass Frame' THEN '".__("Glass Frame")."'
    					   WHEN item_type.name='Contact Lens' THEN '".__("Contact Lens")."'
						   WHEN item_type.name='Products' THEN '".__("Products")."'
    					   WHEN item_type.name='Lens' THEN '".__("Lens")."'
					    END 	   
					    as item_type,
						det.qty,det.price,det.discount,
						IF(req.type=3,-1*(det.total),det.total) as subtotal,
						IF(req.type=3,-1*IFNULL(det.qst,0),IFNULL(det.qst,0)) as qst,
						IF(req.type=3,-1*IFNULL(det.gst,0),IFNULL(det.gst,0)) as gst,
						IF(req.type=3,-1*det.total+-1*IFNULL(det.qst,0)+-1*IFNULL(det.gst,0),det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)) as total,  
					    CONCAT(pat.first_name,' ',pat.last_name) AS patName,supp.name as supplier_name,clin.full_name as branch_name,
					    item.description as item_name,IF(req.type=3,-1*(item.cost_price*det.qty),item.cost_price*det.qty) as cost_price,
						IF(req.type=3,-1*(det.total-item.cost_price*det.qty),det.total-item.cost_price*det.qty) as profit,
					    IF(item.cost_price=0.00,IF(req.type=3,-1*det.total,det.total),IF(type=3,-1*( (det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty)),(det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty))) as profit_percent
					  from tbl_inventory_invoices_request as req
					  INNER JOIN tbl_inventory_invoices_details as det ON det.status='O' and det.invoice_id=req.id
	                  INNER JOIN tbl_inventory_items as item ON item.id=det.item_code  and typecode=1 and item.active='O'
					  INNER JOIN tbl_inventory_fournisseur_types as item_type ON item_type.id=item.gen_types and item_type.active='O'
					  INNER JOIN tbl_patients as pat ON pat.id=req.patient_id 
					  INNER JOIN tbl_inventory_items_fournisseur as supp ON supp.id=item.fournisseur and supp.active='O'
					  INNER JOIN tbl_clinics as clin ON clin.id = req.clinic_num and clin.active='O'
	            where req.active='O' and req.clinic_num='".$clinic_num."' ".$filter_inv_type." ".$filter_date." ".$filter_item." ".$filter_supplier." ".$filter_patient."  
	            order by  item_type.name, req.date_invoice desc";
	   
	   $sales_per_supplier = DB::select(DB::raw("$data"));
	   
	   return response()->json( $sales_per_supplier);
		}
	  return view('inventory.reports.inventory_list.inventory_sales_per_supplier')->with(['resource'=>$resource,'patients'=>$patients,'suppliers'=>$suppliers,'items'=>$items]);	
	    
} 

public function inventory_purchases_per_supplier($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
	
	$suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();
    $items = DB::table('tbl_inventory_fournisseur_types')->where('active','O')->orderBy('id','desc')->get();
  		   
		if($request->ajax()){   
		   
		   $filter_supplier="";
		   if( $request->filter_supplier !="0" &&  $request->filter_supplier !=NULL){
			  $filter_supplier = " and supp.id = '".$request->filter_supplier."' "; 
		   }
		   
		   
		   $filter_item="";
		   if( $request->filter_item !="0" &&  $request->filter_item !=NULL){
			  $filter_item = " and item_type.id = '".$request->filter_item."' "; 
		   }
		  
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate =="" || $request->filter_todate ==NULL) ){
			     $filter_date= "and DATE(req.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
				
			if( ($request->filter_fromdate =="" && $request->filter_fromdate ==NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date= "and DATE(req.date_invoice) <= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}	
				
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date = "and DATE(req.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}

             $filter_inv_type="and (req.type='1' or (req.type='4' and req.cr_note<>'G'))";
			 if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
				  $filter_inv_type = " and req.type = '4' and req.cr_note<>'G'";
				 
			  }							
					 
	    $data = "select req.id, DATE_FORMAT(req.date_invoice,'%Y-%m-%d') as date_invoice,
						CONCAT(req.clinic_inv_num,';',IF(req.reference IS NULL or req.type=1,'',req.reference)) as invoice_num,	                    
						CASE WHEN item_type.name='Glass Frame' THEN '".__("Glass Frame")."'
    					   WHEN item_type.name='Contact Lens' THEN '".__("Contact Lens")."'
						   WHEN item_type.name='Products' THEN '".__("Products")."'
    					   WHEN item_type.name='Lens' THEN '".__("Lens")."'
					    END 	   
					    as item_type,
						det.qty,det.price,det.discount,supp1.name as supplier_name_invoice,
						IF(req.type=4,-1*(det.total),det.total) as subtotal,
						IF(req.type=4,-1*IFNULL(det.qst,0),IFNULL(det.qst,0)) as qst,
						IF(req.type=4,-1*IFNULL(det.gst,0),IFNULL(det.gst,0)) as gst,
						IF(req.type=4,-1*(det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)),det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)) as total,  
					    supp.name as supplier_name,clin.full_name as branch_name,
					    item.description as item_name,
						IF(req.type=4,-1*(item.cost_price*det.qty),item.cost_price*det.qty) as cost_price,
						IF(req.type=4,-1*(det.total-item.cost_price*det.qty),det.total-item.cost_price*det.qty) as profit,
					    IF(item.cost_price=0.00,IF(req.type=4,-1*det.total,det.total),IF(type=4,-1*( (det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty)),(det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty))) as profit_percent
					  from tbl_inventory_invoices_request as req
                      INNER JOIN tbl_inventory_invoices_details as det ON det.status='O' and det.invoice_id=req.id                      
                      INNER JOIN tbl_inventory_items as item ON item.id=det.item_code  and typecode=1 and item.active='O'
					  INNER JOIN tbl_inventory_items_fournisseur as supp1 ON supp1.id=req.fournisseur_id and supp1.active='O'
					  INNER JOIN tbl_inventory_fournisseur_types as item_type ON item_type.id=item.gen_types and item_type.active='O'
					  INNER JOIN tbl_inventory_items_fournisseur as supp ON supp.id=item.fournisseur and supp.active='O'
					  INNER JOIN tbl_clinics as clin ON clin.id = req.clinic_num and clin.active='O'
	            where req.active='O' and req.clinic_num='".$clinic_num."' ".$filter_inv_type." ".$filter_date." ".$filter_item." ".$filter_supplier." 
	            order by  item_type.name, req.date_invoice desc";	 	
       
	  
	   
	   $sales_per_item = DB::select(DB::raw("$data"));
	   
	   return response()->json($sales_per_item);
		}
	  return view('inventory.reports.inventory_list.inventory_purchases_per_supplier')->with(['resource'=>$resource,'suppliers'=>$suppliers,'items'=>$items]);	
	    
}

public function inventory_purchases_per_item($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
	
	$suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();
    $items = DB::table('tbl_inventory_fournisseur_types')->where('active','O')->orderBy('id','desc')->get();
    $allitems = DB::table('tbl_inventory_items')->where('active','O')->orderBy('id','desc')->get();
  	$categorys = DB::table('tbl_inventory_category_types')->where('active','O')->orderBy('id','desc')->get();
   
		if($request->ajax()){   
		   
		   $filter_supplier="";
		   if( $request->filter_supplier !="0" &&  $request->filter_supplier !=NULL){
			  $filter_supplier = " and supp.id = '".$request->filter_supplier."' "; 
		   }
		   
		   
		   $filter_item="";
		   if( $request->filter_item !="0" &&  $request->filter_item !=NULL){
			  $filter_item = " and item_type.id = '".$request->filter_item."' "; 
		   }
		   $filter_allitem="";
		   if( $request->filter_allitem !="0" &&  $request->filter_allitem !=NULL){
			  $filter_allitem = " and item.id = '".$request->filter_allitem."' "; 
		   }
		   $filter_category="";
		   if( $request->filter_category !="0" &&  $request->filter_category !=NULL){
			  $filter_category = " and item.category = '".$request->filter_category."' "; 
		   }
		   
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate =="" || $request->filter_todate ==NULL) ){
			     $filter_date= "and DATE(req.date_invoice) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
				
			if( ($request->filter_fromdate =="" && $request->filter_fromdate ==NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date= "and DATE(req.date_invoice) <= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}	
				
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date = "and DATE(req.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}		
					 
		  
      $filter_inv_type="and (req.type='1' or (req.type='4' and req.cr_note<>'G'))";
			 if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
				  $filter_inv_type = " and req.type = '4' and req.cr_note<>'G'";
				 
			  }							
					 
	    $data = "select req.id, DATE_FORMAT(req.date_invoice,'%Y-%m-%d') as date_invoice,
						CONCAT(req.clinic_inv_num,';',IF(req.reference IS NULL or req.type=1,'',req.reference)) as invoice_num,	                    
						CASE WHEN item_type.name='Glass Frame' THEN '".__("Glass Frame")."'
    					   WHEN item_type.name='Contact Lens' THEN '".__("Contact Lens")."'
						   WHEN item_type.name='Products' THEN '".__("Products")."'
    					   WHEN item_type.name='Lens' THEN '".__("Lens")."'
					    END 	   
					    as item_type,
						det.qty,det.price,det.discount,supp1.name as supplier_name_invoice,
						IF(req.type=4,-1*(det.total),det.total) as subtotal,
						IF(req.type=4,-1*IFNULL(det.qst,0),IFNULL(det.qst,0)) as qst,
						IF(req.type=4,-1*IFNULL(det.gst,0),IFNULL(det.gst,0)) as gst,
						IF(req.type=4,-1*(det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)),det.total+IFNULL(det.qst,0)+IFNULL(det.gst,0)) as total,  
					    supp.name as supplier_name,clin.full_name as branch_name,
					    item.description as item_name,
						IF(req.type=4,-1*(item.cost_price*det.qty),item.cost_price*det.qty) as cost_price,
						IF(req.type=4,-1*(det.total-item.cost_price*det.qty),det.total-item.cost_price*det.qty) as profit,
					    IF(item.cost_price=0.00,IF(req.type=4,-1*det.total,det.total),IF(type=4,-1*( (det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty)),(det.total-(item.cost_price*det.qty))/(item.cost_price*det.qty))) as profit_percent
					  from tbl_inventory_invoices_request as req
                      INNER JOIN tbl_inventory_invoices_details as det ON det.status='O' and det.invoice_id=req.id                      
                      INNER JOIN tbl_inventory_items as item ON item.id=det.item_code  and typecode=1 and item.active='O'
					  INNER JOIN tbl_inventory_items_fournisseur as supp1 ON supp1.id=req.fournisseur_id and supp1.active='O'
					  INNER JOIN tbl_inventory_fournisseur_types as item_type ON item_type.id=item.gen_types and item_type.active='O'
					  INNER JOIN tbl_inventory_items_fournisseur as supp ON supp.id=item.fournisseur and supp.active='O'
					  INNER JOIN tbl_clinics as clin ON clin.id = req.clinic_num and clin.active='O'
	            where req.active='O' and req.clinic_num='".$clinic_num."' ".$filter_inv_type." ".$filter_date." ".$filter_item." ".$filter_allitem." ".$filter_category." ".$filter_supplier." 
	            order by  item_type.name, req.date_invoice desc";	 	
	   
	   $sales_per_item = DB::select(DB::raw("$data"));
	   
	   return response()->json($sales_per_item);
		}
	  return view('inventory.reports.inventory_list.inventory_purchases_per_item')->with(['categorys'=>$categorys,'allitems'=>$allitems,'resource'=>$resource,'suppliers'=>$suppliers,'items'=>$items]);	
	    
}

public function Filter_cat($lang,Request $request) 
{
	    $filter_supplier="";
		if(isset($request->filter_supplier)&& $request->filter_supplier !="0" && $request->filter_supplier !=""){
			$filter_supplier= " and b.id = '".$request->filter_supplier."'";
		}
		//dd($filter_supplier);
		$html='<option value="0">'.__("All Category").'</option>';;
		$sqlCode = "select DISTINCT(c.id) as id ,c.name,b.name as namefournisseur 
					  from tbl_inventory_items a,tbl_inventory_items_fournisseur b,tbl_inventory_category_types c 
					  where a.fournisseur=b.id and 
					  c.id=a.category and a.active='O' ".$filter_supplier." group by c.id order by c.id desc";
	     $code= DB::select(DB::raw("$sqlCode"));
     foreach ($code as $codes) {
       $html .= '<option value="' . $codes->id.'">'.$codes->name.'-(Supplier:'.$codes->namefournisseur.')</option>';
           }
		
		$html1='<option value="0">'.__("All Codes").'</option>';;
	    $sqlCode1 = "select a.id,a.description as name,b.name as namefournisseur from tbl_inventory_items a,tbl_inventory_items_fournisseur b where a.fournisseur=b.id and a.active='O' ".$filter_supplier." order by a.id desc";
	    	//dd($sqlCode);
		//$sqlCode = "select DISTINCT(id) AS id,description as name from tbl_inventory_items where active='O' ".$filter_supplier." order by id desc";
		$code1= DB::select(DB::raw("$sqlCode1"));
     foreach ($code1 as $codes1) {
       $html1 .= '<option value="' . $codes1->id.'">'.$codes1->name.'-(Supplier:'.$codes1->namefournisseur.')</option>';
   
	 }   
			return response()->json(["html"=>$html,"html1"=>$html1]);   
	}	
	
public function Filter_item($lang,Request $request) 
{
	    $filter_category="";
		if(isset($request->filter_category)&& $request->filter_category !="0" && $request->filter_category !=""){
			$filter_category= " and a.category = '".$request->filter_category."'";
		}
		$filter_supplier="";
		if(isset($request->filter_supplier)&& $request->filter_supplier !="0" && $request->filter_supplier !=""){
			$filter_supplier= " and a.fournisseur = '".$request->filter_supplier."'";
		}
		//dd($filter_supplier);
		$html='<option value="0">'.__("All Codes").'</option>';;
	    $sqlCode = "select a.id,a.description as name,b.name as namefournisseur from tbl_inventory_items a,tbl_inventory_items_fournisseur b where a.fournisseur=b.id and a.active='O' ".$filter_category." ".$filter_supplier." order by a.id desc";
	    //dd($sqlCode);
		//$sqlCode = "select DISTINCT(id) AS id,description as name from tbl_inventory_items where active='O' ".$filter_supplier." order by id desc";
		$code= DB::select(DB::raw("$sqlCode"));
     foreach ($code as $codes) {
       $html .= '<option value="' . $codes->id.'">'.$codes->name.'-(Supplier:'.$codes->namefournisseur.')</option>';
           }
			return response()->json(["html"=>$html]);   
	}	
public function inventory_purchases_per_pay($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
    
	$suppliers = DB::table('tbl_inventory_items_fournisseur')->where('clinic_num',$clinic_num)->where('active','O')->orderBy('id','desc')->get();

	$payment_types =DB::table('tbl_bill_payment_mode')->where('status','O')->where('clinic_num',$clinic_num)->get(['id','name_eng','name_fr']);
	
	if($request->ajax()){
		   
		   $filter_supplier="";
           	if(isset($request->filter_supplier) && $request->filter_supplier!="0" ){
			 $filter_supplier = "and a.fournisseur_id=".$request->filter_supplier; 
			  
		    }
		   
		   $filter_date= "";
				
			
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate=="" || $request->filter_todate==NULL) ){
			     $filter_date= "and DATE(b.datein) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
			
			if( ($request->filter_todate!="" &&  $request->filter_todate!=NULL)
				&& ($request->filter_fromdate=="" || $request->filter_fromdate==NULL) ){
			     $filter_date= "and DATE(b.datein) <= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}		
				
			if( ($request->filter_fromdate!="" &&  $request->filter_fromdate!=NULL)
				&& ($request->filter_todate!="" &&  $request->filter_todate!=NULL) ){
			     $filter_date = "and DATE(b.datein) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}	
			 
		  		  
		  
		  $filter_payment="";
		  if(isset($request->filter_payment) && $request->filter_payment!="0" ){
			 $filter_payment = "and b.payment_type='".$request->filter_payment."' ";  
		      }
			  
	      $filter_payment_type="";
		  if(isset($request->filter_payment_type) && $request->filter_payment_type!="0" ){
			 $filter_payment_type = "and e.id='".$request->filter_payment_type."' ";  
		      }
			  
			  $filter_due_amount="";
		  if(isset($request->filter_due_amount) && $request->filter_due_amount!="0" ){
			 $filter_due_amount = "and a.inv_balance>0";  
		      } 
		  
		  $filter_inv_type="and (a.type='1' or a.type='4')";
          
		  if(isset($request->filter_inv_type) && $request->filter_inv_type!="0"){
			 $filter_inv_type = " and a.type = 4";
			
			}	
			  
			 $payment_name=($lang=='en')?'e.name_eng':'e.name_fr'; 
								 
			  $data="SELECT DATE(b.datein) as payment_date, c.name as suppName,
					d.full_name as branch_name,if(b.payment_type='P',b.payment_amount,-1*b.payment_amount) as payment_amount,
					CONCAT(a.clinic_inv_num,';',IF(a.type=1 or a.reference IS NULL,'',a.reference)) as bill_nb,
					DATE(a.date_invoice) as bill_date,{$payment_name} as payment_name,
					if(b.payment_type='P','Payment',if(b.payment_type='R','Refund','ND'))  as payment_type,
					IF(a.type=4,-1*(a.total+a.gst+a.qst),a.total+a.gst+a.qst) as total_pay,
					a.inv_balance as solde_du,a.type
					FROM  tbl_inventory_invoices_request a
					INNER JOIN tbl_inventory_payment b ON   b.invoice_num=a.id and b.status='Y'
					INNER JOIN tbl_inventory_items_fournisseur c ON c.id = a.fournisseur_id AND c.active='O' 
					INNER JOIN tbl_clinics d ON d.id = a.clinic_num AND d.active='O'
					INNER JOIN tbl_bill_payment_mode e ON   e.id=b.reference and e.status='O'
					WHERE a.active='O' and a.clinic_num='".$clinic_num."' ".$filter_supplier." ".$filter_payment." ".$filter_date." ".$filter_payment_type." ".$filter_due_amount." ".$filter_inv_type."
					ORDER BY b.reference desc,a.date_invoice desc
			 	     ";
              
            $bills_payments= DB::select(DB::raw("$data"));
			
			
			
		    return response()->json($bills_payments);				 
			  
	   }
      
	  return view('inventory.reports.inventory_list.inventory_purchases_per_pay')->with(['resource'=>$resource,'payment_types'=>$payment_types,'suppliers'=>$suppliers]);	
	 
	 
  }

public function inventory_items_per_qty($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
	
    $items = DB::table('tbl_inventory_fournisseur_types')->where('active','O')->orderBy('id','desc')->get();
  		   
		if($request->ajax()){   
		   
		   $filter_item_code="";
		   if( $request->filter_item_code !="0" &&  $request->filter_item_code !=NULL){
			  $filter_item_code = " and det.item_code = '".$request->filter_item_code."' "; 
		   }
		   
		  
		   $filter_date= "";
		   $filter_mat_date="";		
			
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate =="" || $request->filter_todate ==NULL) ){
			     $filter_date= "and ((DATE(req.date_invoice) >= '".$request->filter_fromdate."') ";
				 $filter_date.= "OR (DATE(req.date_cmd) >= '".$request->filter_fromdate."') ";
				 $filter_date.= "OR (DATE(material.date_approve) >= '".$request->filter_fromdate."')) ";
				 $filter_mat_date= "and DATE(matreq.date_approve) >= '".$request->filter_fromdate."' ";
				 //dd( $filter_date);
				}
				
			if( ($request->filter_fromdate =="" && $request->filter_fromdate ==NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date= "and ( (DATE(req.date_invoice) <= '".$request->filter_todate."') ";
				 $filter_date.= "OR ( DATE(req.date_cmd) <= '".$request->filter_todate."') ";
				 $filter_date.= "OR ( DATE(material.date_approve) <= '".$request->filter_todate."')) ";
				 $filter_mat_date= "and DATE(matreq.date_approve) <= '".$request->filter_todate."' ";
				 //dd( $filter_date);
				}	
				
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date = " and  (( DATE(date_invoice) IS NOT NULL AND DATE(date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."' )";
				 $filter_date .= " OR ( DATE(date_cmd) IS NOT NULL AND DATE(date_cmd) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."' ) ";
			     $filter_date .= " OR ( DATE(date_approve) IS NOT NULL AND DATE(date_approve) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."' ) )";

				 $filter_mat_date = "and DATE(matreq.date_approve) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."'";
				 //dd( $filter_date);
				}		
					 
		 $data="SELECT  det.item_code,ANY_VALUE(det.item_name) as item_name,
		        ANY_VALUE(IFNULL(itm.sel_price,0)) as sell_price,
				ANY_VALUE(IFNULL(itm.cost_price,0)) as cost_price,
				ANY_VALUE(IFNULL(material.matQty,0)) as matQty,
				ANY_VALUE(IFNULL(req.date_invoice,'')) as date_invoice,
				ANY_VALUE(IFNULL(req.date_cmd,'')) as date_cmd,
				ANY_VALUE(IFNULL(material.date_approve,'')) as date_approve,
				SUM(IF((req.type = 1) or (req.type = 3) or (req.type=5 and req.typeadjacement=1), det.qty, 0)) AS QtyPlus,
				SUM(IF((req.type = 2) or (req.type = 4) or (req.type=5 and req.typeadjacement=2) or (req.cmd_id IS NOT NULL and req.type IS NULL), det.qty, 0)) AS QtyMinus,
				SUM(IF((req.type = 1) or (req.type = 3) or (req.type=5 and req.typeadjacement=1), det.qty, 0))-SUM(IF((req.type = 2) or (req.type = 4) or (req.type=5 and req.typeadjacement=2) or (req.cmd_id IS NOT NULL and req.type IS NULL), det.qty, 0)) AS QtyDiff,
			    ANY_VALUE(IFNULL(itm.cost_price,0))*(SUM(IF((req.type = 1) or (req.type = 3) or (req.type=5 and req.typeadjacement=1), det.qty, 0))-SUM(IF((req.type = 2) or (req.type = 4) or (req.type=5 and req.typeadjacement=2) or (req.cmd_id IS NOT NULL and req.type IS NULL), det.qty, 0))) as totalPrice
				FROM     tbl_inventory_invoices_details as det
                INNER JOIN  tbl_inventory_invoices_request as req ON req.id = det.invoice_id or (req.id=det.cmd_id and req.type IS NULL and req.clinic_inv_num IS NULL)
				INNER JOIN tbl_inventory_items as itm ON itm.id = det.item_code
				LEFT JOIN (select SUM(mat.dqty) as matQty,ANY_VALUE(mat.item_id) as item_id,ANY_VALUE(matreq.date_approve) as date_approve
				            from tbl_inventory_materials_details as mat
                            INNER JOIN tbl_inventory_materials_request as matreq ON matreq.id=mat.invoice_id and matreq.approve='Y'
							where matreq.active='O'  ".$filter_mat_date."
							GROUP BY mat.item_id
			                HAVING matQty <>0
			                ) as material ON material.item_id=itm.id						
                WHERE req.clinic_num='".$clinic_num."'  ".$filter_date." ".$filter_item_code." 
                GROUP BY    det.item_code
				
                ORDER BY    det.item_code desc
				 "; 
     	 //dd($data);
	     $items_per_qty = DB::select(DB::raw("$data"));
	     //dd($items_per_qty);
	     return response()->json($items_per_qty);
		}
	  return view('inventory.reports.inventory_list.inventory_items_per_qty')->with(['resource'=>$resource]);	
	    
}  
 
public function inventory_accounting($lang,Request $request){
$user_type=auth()->user()->type;
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
	
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->get();	
    $all_user=User::where('active','O')->get();	
	
	
	
  		//$inventory_acc = DB::table('tbl_account_head')
					//		->where('tbl_account_head.clinic_num', $FromFacility->id)
					//		->leftJoin('tbl_account_details', 'tbl_account_head.id', '=', 'tbl_account_details.account_id')
					//		->select(
					//			'tbl_account_head.*',
					////			tbl_account_details.name1 AS ffrom,
					//			tbl_account_details.name2 AS fto
						//	)
						//	->groupBy('tbl_account_head.id')
						//	->get();   
		if($request->ajax()){   
		   
		   $filter_from_acc="";
		   if( $request->filter_from_acc !="" &&  $request->filter_from_acc !=NULL){
			  $filter_from_acc = " and det.admi1 = '".$request->filter_from_acc."' "; 
		   }
		   $filter_to_acc="";
		   if( $request->filter_to_acc !="" &&  $request->filter_to_acc !=NULL){
			  $filter_to_acc = " and det.admi2 = '".$request->filter_to_acc."' "; 
		   }
		  
		  $filter_currency="";
		   if( $request->filter_currency !="" &&  $request->filter_currency !=NULL){
			  $filter_currency = " and det.curency1 = '".$request->filter_currency."' "; 
		   }
		   
		   $filter_type="";
		   if( $request->filter_type !="" &&  $request->filter_type !=NULL){
			  $filter_type = " and req.type = '".$request->filter_type."' "; 
		   }
		   
		   $filter_date= "";
		
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate =="" || $request->filter_todate ==NULL) ){
			     $filter_date= "and (DATE(req.datein) >= '".$request->filter_fromdate."') ";
				}
			if( ($request->filter_fromdate =="" && $request->filter_fromdate ==NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date= "and (DATE(req.datein) <= '".$request->filter_todate."') ";
				 //dd( $filter_date);
				}	
				
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date = " and  (DATE(req.datein) IS NOT NULL AND DATE(req.datein) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."' )";
								}	
			$filter_user="";
		   if( $request->filter_user !="" &&  $request->filter_user !=NULL){
			  $filter_user = " and det.user_id = '".$request->filter_user."' "; 
		   }								
				$data="SELECT  det.*,req.rq as rq1
    			FROM     tbl_account_details as det
                INNER JOIN  tbl_account_head as req ON req.id = det.account_id
				WHERE req.clinic_num='".$clinic_num."'  ".$filter_date." ".$filter_from_acc." ".$filter_user." ".$filter_to_acc." ".$filter_currency." ".$filter_type." 
                ORDER BY    req.id desc
				 "; 
     	 //dd($data);
	     $inventory_acc = DB::select(DB::raw("$data"));
		
	     //dd($items_per_qty);
	     return response()->json($inventory_acc);
		}
	  return view('inventory.reports.inventory_list.inventory_accounting')->with(['resource'=>$resource,'fournisseur'=>$fournisseur,'all_user'=> $all_user]);	
	    
}  
 
 public function inventory_orders($lang,Request $request){
$user_type=auth()->user()->type; 
$clinic_num = '';	
	switch($user_type){
	case 1:
	 $clinic_num = Session::get('inventory_branch_num');
	 $resource = Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->where('doctor_user_num',auth()->user()->id)->first(); 
						 
	break;
	case 2:
	
	 $clinic_num =auth()->user()->clinic_num;
	 $resource = Clinic::select('id','full_name')->where('id',$clinic_num)->first();
	break;					 
	}
	
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->get();	
    $all_user=User::where('active','O')->get();	
	
	
	
  		//$inventory_acc = DB::table('tbl_account_head')
					//		->where('tbl_account_head.clinic_num', $FromFacility->id)
					//		->leftJoin('tbl_account_details', 'tbl_account_head.id', '=', 'tbl_account_details.account_id')
					//		->select(
					//			'tbl_account_head.*',
					////			tbl_account_details.name1 AS ffrom,
					//			tbl_account_details.name2 AS fto
						//	)
						//	->groupBy('tbl_account_head.id')
						//	->get();   
		if($request->ajax()){   
		   $filter_status="";
		if (isset($request->filter_status)) {
						$filter_status=" and  tbl_inventory_invoices_request.active ='". $request->filter_status."'";
						}				
				//if(isset($request->selectpro)){
				//	$inventory_com = $inventory_com->where('tbl_inventory_invoices_request.fournisseur_id',$request->selectpro);
				//}		
				$selectpro="";
				if (isset($request->selectpro) and ($request->selectpro!="0")) {
					
					$selectpro=" and  tbl_inventory_invoices_request.fournisseur_id='". $request->selectpro."'";
				}
				 $selectcode="";
				 if (isset($request->selectcode)) {
					
						$selectcode=" and  tbl_details.item_code='".$request->selectcode."'";
					}
					$filter_rp_com="";
					if (isset($request->filter_rp_com)) {
						
						$filter_rp_com=" and  tbl_inventory_invoices_request.email_sent='".$request->filter_rp_com."'";
					}
					$filter_v_com="";
					if (isset($request->filter_v_com)) {
						
						switch ($request->filter_v_com) {
							case 'Y':
								$filter_v_com=" and tbl_inventory_invoices_request.is_valid1='Y'";
								break;
							case 'N':
								$filter_v_com=" and  tbl_inventory_invoices_request.is_valid2= 'Y'";
								break;
							case 'A':
								$filter_v_com=" and  tbl_inventory_invoices_request.is_valid1= 'Y'
									and tbl_inventory_invoices_request.is_valid2='Y'";
								break;
						}
					}
					$filter_p_com="";
					if (isset($request->filter_p_com)) {
						
						$filter_p_com=" and tbl_inventory_invoices_request.cpaid='".$request->filter_p_com."' ";
					}
					$filter_d_com="";
					if (isset($request->filter_d_com)) {
						
						$filter_d_com=" and tbl_inventory_invoices_request.cdone='".$request->filter_d_com."' ";
					}
		   $filter_date= "";
		
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate =="" || $request->filter_todate ==NULL) ){
			     $filter_date= "and (DATE(tbl_inventory_invoices_request.date_invoice) >= '".$request->filter_fromdate."') ";
				}
			if( ($request->filter_fromdate =="" && $request->filter_fromdate ==NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date= "and (DATE(tbl_inventory_invoices_request.date_invoice) <= '".$request->filter_todate."') ";
				 //dd( $filter_date);
				}	
				
			if( ($request->filter_fromdate !="" && $request->filter_fromdate !=NULL)
				&& ($request->filter_todate !="" || $request->filter_todate !=NULL) ){
			     $filter_date = " and  (DATE(tbl_inventory_invoices_request.date_invoice) IS NOT NULL AND DATE(tbl_inventory_invoices_request.date_invoice) BETWEEN '".$request->filter_fromdate."' AND '".$request->filter_todate."' )";
								}	
	        $filter_status="";
			if(isset($request->filter_status_com) && $request->filter_status_com!=""){
				$filter_status=" and tbl_inventory_invoices_request.active ='".$request->filter_status_com."'"; 
			}
			
			
			$data = "
SELECT 
    main.id AS id,
    main.name,
    DATE_FORMAT(main.date_invoice, '%Y-%m-%d') AS date_invoice,
    main.supplier_id,
    main.id_invoice, 
    main.email_sent,
    main.cpaid,
    main.is_valid1,
    main.is_valid2,
    main.cdone,
    main.total,
    main.inv_balance,
    main.clinic_inv_num,
    COALESCE(b.total_pay, 0) AS total_pay
FROM (
    SELECT 
        tbl_inventory_invoices_request.id AS id,
        tbl_fournisseur.name,
        tbl_inventory_invoices_request.date_invoice,
        tbl_fournisseur.id AS supplier_id,
        tbl_inventory_invoices_request.clinic_inv_num AS id_invoice, 
        tbl_inventory_invoices_request.email_sent,
        tbl_inventory_invoices_request.cpaid,
        tbl_inventory_invoices_request.is_valid1,
        tbl_inventory_invoices_request.is_valid2,
        tbl_inventory_invoices_request.cdone,
        tbl_inventory_invoices_request.total,
        tbl_inventory_invoices_request.inv_balance,
        tbl_inventory_invoices_request.clinic_inv_num
    FROM 
        tbl_inventory_invoices_request
	LEFT JOIN 
        tbl_inventory_invoices_details AS tbl_details
        ON tbl_details.invoice_id = tbl_inventory_invoices_request.id								                      
    INNER JOIN 
        tbl_clinics AS clin 
        ON clin.id = tbl_inventory_invoices_request.clinic_num
	LEFT JOIN 
	tbl_inventory_items_fournisseur AS tbl_fournisseur 
        ON tbl_fournisseur.id = tbl_inventory_invoices_request.fournisseur_id  and tbl_fournisseur.active='O'
    WHERE 
        tbl_inventory_invoices_request.type = '6'
        and tbl_inventory_invoices_request.clinic_num = '".$clinic_num."' 
        ".$filter_date." 
        ".$filter_status." 
        ".$filter_rp_com." 
        ".$filter_v_com." 
        ".$selectpro."
        ".$selectcode."
        ".$filter_p_com."
        ".$filter_d_com."
    GROUP BY 
        tbl_inventory_invoices_request.id, 
        tbl_fournisseur.name, 
        tbl_fournisseur.id, 
        tbl_inventory_invoices_request.date_invoice, 
        tbl_inventory_invoices_request.clinic_inv_num, 
        tbl_inventory_invoices_request.email_sent, 
        tbl_inventory_invoices_request.cpaid, 
        tbl_inventory_invoices_request.is_valid1, 
        tbl_inventory_invoices_request.is_valid2, 
        tbl_inventory_invoices_request.cdone, 
        tbl_inventory_invoices_request.total, 
        tbl_inventory_invoices_request.inv_balance, 
        tbl_inventory_invoices_request.clinic_inv_num
) AS main
LEFT JOIN (
    SELECT 
        invoice_num, 
        SUM(IFNULL(payment_amount, 0)) AS total_pay
    FROM 
        tbl_inventory_payment
    WHERE 
        status = 'Y' 
        AND payment_type = 'P'
    GROUP BY 
        invoice_num
) AS b
ON main.id = b.invoice_num
ORDER BY 
    main.id DESC";

	
	     $inventory_order = DB::select(DB::raw("$data"));
		
	     return response()->json($inventory_order);
		}
	  return view('inventory.reports.inventory_list.inventory_orders')->with(['resource'=>$resource,'fournisseur'=>$fournisseur]);	
	    
}  
 
}	