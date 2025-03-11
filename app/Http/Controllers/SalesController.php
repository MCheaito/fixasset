<?php
/*
*
* DEV APP
* Created date : 20-12-2022
* Created date : 31-3-2023 (generate pdf for label)
*
*/
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\Permission;
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
use App\Models\TblInventoryClients;

use Alert;
use DataTables;
use PDF;
use UserHelper;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Session;
use Image;
use Illuminate\Support\Facades\Mail;
use App\Mail\SettingMailAttach;
use App\Mail\SettingMailAttachSupp;
use App\Models\TblAccountDetails;
use App\Models\TblAccountRequest;
use App\Models\DOCSCmd;
use Storage;
use DateTime;

class SalesController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index($lang,Request $request) 
    {
		 $filters = [
        'filter_status' => $request->input('filter_status', session('filter_status', '')),
        'selectpro' => $request->input('selectpro', session('selectpro', '')),
		 'selectuser' => $request->input('selectuser', session('selectuser', '')),
        'selectcode' => $request->input('selectcode', session('selectcode', '')),
        'filter_rp_com' => $request->input('filter_rp_com', session('filter_rp_com', '')),
        'filter_v_com' => $request->input('filter_v_com', session('filter_v_com', '')),
		'filter_typein' => $request->input('filter_typein', session('filter_typein', '')),
		 'filter_d_com' => $request->input('filter_d_com', session('filter_d_com', '')),
        'filter_p_com' => $request->input('filter_p_com', session('filter_p_com', ''))
    ];

    // Save filters to session
    session($filters);
	
	  $myId=auth()->user()->id;	
	  if(auth()->user()->type==1){
		$FromFacility = Clinic::where('id',Session::get('inventory_branch_num'))->where('active','O')->first();
	     }
	 if(auth()->user()->type==2){
	    $idFacility = auth()->user()->clinic_num; 
		$FromFacility = Clinic::where('id',$idFacility)->where('active','O')->first();
	 }
       $view_command = UserHelper::can_access(auth()->user(),'command');
   //	$view_sales = UserHelper::can_access(auth()->user(),'sales');
   //	$view_purchasing = UserHelper::can_access(auth()->user(),'purchasing');
  // 	$view_warranty = UserHelper::can_access(auth()->user(),'warranty');
  // 	$view_adjustment = UserHelper::can_access(auth()->user(),'adjustment');
  // 	$view_accounting = UserHelper::can_access(auth()->user(),'accounting');
	//	dd($view_command_edit);
	 $type=TblInventoryTypes::where('active','O')->get();
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->get();
	$user_perm = auth()->user()->permission;
	if ($user_perm == 'U') {
    // Get client permissions
    $client_permission_ids = Permission::where('clinic_num', $idFacility)
        ->where('uid', $myId)
        ->where('active', 'O')
        ->pluck('clients'); // Assuming 'clients' is a JSON array in the Permission table
    // Extract and query clients
    $clients = TblInventoryClients::where('active', 'O')
        ->where(function ($query) use ($client_permission_ids) {
            foreach ($client_permission_ids as $client_ids_json) {
                $client_ids = json_decode($client_ids_json, true); // Decode JSON to array
                $query->orWhereIn('id', $client_ids); // Check IDs in the clients JSON array
            }
        })
        ->get();
$users = User::select(DB::raw("id,CONCAT(fname, ' ', lname) as name"))
    ->where('active', 'O')
	->where('id', $myId)
    ->get();		
} else {
    // Retrieve all active clients
    $clients = TblInventoryClients::where('active', 'O')->get();
	$users = User::select(DB::raw("id,CONCAT(fname, ' ', lname) as name"))
    ->where('active', 'O')
    ->get();	
}
 
		    $inventory_com=DB::table('tbl_inventory_invoices_request')
		                       ->select(DB::raw("DISTINCT(tbl_inventory_invoices_request.id) as id"),'tbl_clients.name','tbl_clients.id as supplier_id',
		                         'tbl_inventory_invoices_request.clinic_inv_num as id_invoice', 'tbl_inventory_invoices_request.email_sent','tbl_inventory_invoices_request.cpaid',
								 'tbl_inventory_invoices_request.cdone','tbl_inventory_invoices_request.date_due',	'tbl_inventory_invoices_request.date_delivery',									 
		                         DB::raw("DATE_FORMAT(tbl_inventory_invoices_request.date_invoice,'%Y-%m-%d %H:%i') as date_invoice"),
		                         'tbl_inventory_invoices_request.total as sub_total','tbl_inventory_invoices_request.is_valid1','tbl_inventory_invoices_request.is_valid2','tbl_inventory_invoices_request.quote',
		                         'tbl_inventory_invoices_request.qst','tbl_inventory_invoices_request.gst','tbl_inventory_invoices_request.notes',
		                         DB::raw("(tbl_inventory_invoices_request.qst+tbl_inventory_invoices_request.gst+tbl_inventory_invoices_request.total) as total"),
								 'tbl_inventory_invoices_request.active','tbl_inventory_invoices_request.inv_balance','tbl_inventory_invoices_request.typein',
 		                         'tbl_inventory_invoices_request.clinic_inv_num as clin_inv',
								 DB::raw("CONCAT(users.fname, ' ', users.lname) as nameuser"),
								 DB::raw("SUM(tbl_details.QTY)-SUM(tbl_details.RQTY) as tQTY")
									) 
							->leftjoin('tbl_inventory_clients as tbl_clients','tbl_clients.id','tbl_inventory_invoices_request.fournisseur_id')	  ->leftjoin('tbl_inventory_invoices_details as tbl_details','tbl_details.invoice_id','tbl_inventory_invoices_request.id') 				
							->leftjoin('users','users.id','tbl_inventory_invoices_request.user_id')	 				
							->where('tbl_inventory_invoices_request.type','6')
							->groupBy('tbl_inventory_invoices_request.id')
							 ->orderBy('tbl_inventory_invoices_request.id', 'desc'); // Order by id in ascending order   
		  
		//dd($inventory_com->get()->toArray());
		
		   if ($request->ajax()) {
			     
			if(isset($request->filter_status)){
			$inventory_com = $inventory_com->where('tbl_inventory_invoices_request.active',$request->filter_status);
		}
				if ($filters['filter_status']) {
				$inventory_com->where('tbl_inventory_invoices_request.active', $filters['filter_status']);
						}				
			if(isset($request->selectpro)){
					$inventory_com = $inventory_com->where('tbl_inventory_invoices_request.fournisseur_id',$request->selectpro);
				}		
			//	if ($filters['selectpro']) {
			//		$inventory_com->where('tbl_inventory_invoices_request.fournisseur_id', $filters['selectpro']);
			//	}
				
				if ($filters['filter_rp_com']) {
						$inventory_com->where('tbl_inventory_invoices_request.email_sent', $filters['filter_rp_com']);
					}
				if ($filters['filter_v_com']) {
						switch ($filters['filter_v_com']) {
						case 'Y':
							$inventory_com->where('tbl_inventory_invoices_request.is_valid1', 'Y');
							break;
							case 'N':
								$inventory_com->where('tbl_inventory_invoices_request.is_valid2', 'Y');
							break;
						case 'A':
								$inventory_com->where('tbl_inventory_invoices_request.is_valid1', 'Y')
									->where('tbl_inventory_invoices_request.is_valid2', 'Y');
								break;
						}
				}
				//	if ($filters['filter_p_com']) {
				//		$inventory_com->where('tbl_inventory_invoices_request.cpaid', $filters['filter_p_com']);
				//	}
				if ($filters['filter_d_com']) {
					$inventory_com->where('tbl_inventory_invoices_request.quote', $filters['filter_d_com']);
				}
				
			if(isset($request->filter_user)){
				$filter_user = $inventory_com->where('tbl_inventory_invoices_request.user_id',  $request->filter_user );

					}	
			if(isset($request->filter_typein)){
				$filter_typein = $inventory_com->where('tbl_inventory_invoices_request.typein',  $request->filter_typein );

					}			
			 $inventory_com = $inventory_com->groupBy('tbl_inventory_invoices_request.id');
             if ($request->ajax()) {
            return Datatables::of($inventory_com)
                    ->addIndexColumn()
                    ->addColumn('return_invoices',function($row){
					   $return_inv = DB::table('tbl_inventory_invoices_request as det')
 					                 ->where('det.reference',$row->id_invoice)
					                 ->where('det.active','O')
					                 ->where('det.type','6')->pluck('det.clinic_inv_num')->toArray();
                      return count($return_inv)>0?implode(',',$return_inv):__('None');
					})
					 ->addColumn('Email', function($row){
				            $checked = ($row->email_sent=='Y')?'checked':'';
							$disabled= ($row->email_sent=='Y' )?'':'disabled';
							$state = ($row->email_sent=='Y')?'nosent':'sent';
							$user_perm = auth()->user()->permission;
						   if ($user_perm == 'U') {
						    $btn=' <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" onclick="event.preventDefault();EmailSent('.$row->id.',\''.$state.'\')"  '.$checked.' disabled ><span class="slideon-slider" title="'.__("Send Mail").'"></span></label>';
						   }else{
							$btn=' <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" onclick="event.preventDefault();EmailSent('.$row->id.',\''.$state.'\')"  '.$checked.'><span class="slideon-slider" title="'.__("Send Mail").'"></span></label>';
   
						   }
						  return $btn;
                    })
					 ->addColumn('validate', function($row){
				            $checked1 = ($row->is_valid1=='Y')?'checked':'';
							$disabled1 = ($row->is_valid1=='Y' )?'':'disabled';
							$checked2 = ($row->is_valid2=='Y')?'checked':'';
							$disabled2 = ($row->is_valid2=='Y')?'':'disabled';
							$state1 = ($row->is_valid1=='Y')?'invalid':'validated';
							$state2 = ($row->is_valid2=='Y')?'invalid':'validated';
							$user_perm = auth()->user()->permission;
							$view_command_valid1 = UserHelper::can_access(auth()->user(),'command_valid1');
							$view_command_valid2 = UserHelper::can_access(auth()->user(),'command_valid2');

						   if ($view_command_valid1) {
							   $btn=' <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" onclick="event.preventDefault();validateCmd1('.$row->id.',\''.$state1.'\')"  '.$checked1.'><span class="slideon-slider" title="'.__("Validated").'"></span></label>';

						   	}else{
							$btn=' <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" onclick="event.preventDefault();validateCmd1('.$row->id.',\''.$state1.'\')"  '.$checked1.' disabled ><span class="slideon-slider" title="'.__("Validated").'"></span></label>';
													   }
						    if ($view_command_valid2) { 
							$btn.=' <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" onclick="event.preventDefault();validateCmd2('.$row->id.','.$row->supplier_id.',\''.$state2.'\')"  '.$checked2.'><span class="slideon-slider" title="'.__("Facturation").'"></span></label>';
   
						   }else{
								   $btn.=' <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" onclick="event.preventDefault();validateCmd2('.$row->id.','.$row->supplier_id.',\''.$state2.'\')"  '.$checked2.' disabled ><span class="slideon-slider" title="'.__("Facturation").'"></span></label>';						 
						   }
						   
						  return $btn;
                    })
                    ->addColumn('action', function($row){
				            $checked = ($row->active=='O')?'checked':'';
							$disabled = ($row->active=='O' || $row->active=='W')?'':'disabled';
							$state = ($row->active=='O')?'inactivate':'activate';
							$checked3 = ($row->cpaid=='Y')?'checked':'';
							$disabled3 = ($row->cpaid=='Y' )?'':'disabled';
							$checked4 = ($row->cdone=='Y')?'checked':'';
							$checked5= ($row->quote=='Y')?'checked':'';
							$disabled4 = ($row->cdone=='Y')?'':'disabled';
							$disabled5 = ($row->quote=='Y')?'':'disabled';
							$state3 = ($row->cpaid=='Y')?'NotPaid':'Paid';
							$state4 = ($row->cdone=='Y')?'Pending':'Done';
							$state5 = ($row->quote=='Y')?'NotQuote':'Quote';
							$lang = app()->getLocale();
							$user_perm = auth()->user()->permission;
							$view_command_edit = UserHelper::can_access(auth()->user(),'command_edit');
						   if ($user_perm == 'U') {
							    if ($view_command_edit==true){
									$btn = '<a href="'.route('sales.editcommandsales',[app()->getLocale(),$row->id,$row->typein]).'" title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editConsultation '.$disabled.'"><i class="far fa-edit text-primary"></i></a>';
		   
							   }else{
								if($row->is_valid1=='Y'){   
								$btn = '<a href="'.route('sales.editcommandsales',[app()->getLocale(),$row->id,$row->typein]).'" title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editConsultation '.$disabled.' disabled "><i class="far fa-edit text-primary"></i></a>';   
								}else{
									$btn = '<a href="'.route('sales.editcommandsales',[app()->getLocale(),$row->id,$row->typein]).'" title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editConsultation '.$disabled.'"><i class="far fa-edit text-primary"></i></a>';
		   						
									
								}
							   }
							if($row->is_valid1=='Y'){
							
       					    $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox"   onclick="event.preventDefault();deleteInventory('.$row->id.',\''.$state.'\')"  '.$checked.' disabled > <span class="slideon-slider" title="'.__("Active").'"></span></label>';
							}else{
						    $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox"   onclick="event.preventDefault();deleteInventory('.$row->id.',\''.$state.'\')"  '.$checked.'> <span class="slideon-slider" title="'.__("Active").'"></span></label>';

							}
   						   $btn .= '<a href="javascript:void(0)"  title="'.__("PDF").'" class="btn btn-md btn-clean btn-icon   '.$disabled.'" onclick="getPDF('.$row->id.','.$row->supplier_id.',\''.$row->id_invoice.'\',\''.$row->name.'\')  "><i class="far fa-file-pdf text-primary"></i></a>';
                           $btn .= '<a href="javascript:void(0)"  title="'.__("Email").'" class="btn btn-md btn-clean btn-icon   '.$disabled.'" onclick="sendPDF('.$row->id.') disabled "><i class="far fa-envelope text-primary"  ></i></a>';
						   $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox"   onclick="event.preventDefault();cpaidCmd1('.$row->id.',\''.$state3.'\')"  '.$checked3.' disabled > <span class="slideon-slider" title="'.__("Cash").'"></span></label>';
						   $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox"   onclick="event.preventDefault();cdoneCmd1('.$row->id.',\''.$state4.'\')"  '.$checked4.'  > <span class="slideon-slider" title="'.__("Done").'" hidden ></span></label>';
   						   $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox"   onclick="event.preventDefault();quoteCmd1('.$row->id.',\''.$state5.'\')"  '.$checked5.'  > <span class="slideon-slider" title="'.__("Quotation").'"></span></label>';
						   $btn .= '<a href="javascript:void(0)" title="'.__("Attach").'" class="btn btn-md btn-clean btn-icon '.$disabled.'" onclick="downloadPDF('.$row->id.', \''.$lang.'\') disabled "  ><i class="fas fa-paperclip text-primary"></i></a>';
							if($row->tQTY>0){
											$btn.='<button type="button" class="p-1 btn btn-md btn-clean btn-icon"><i title="'.__("Qty Modified").'" class="fa fa-exclamation-circle text-success"></i></button>';
											}	
						   }else{ 
						   $btn = '<a href="'.route('sales.editcommandsales',[app()->getLocale(),$row->id,$row->typein]).'" title="'.__("edit").'" class="btn btn-sm btn-clean btn-icon btn-icon-md editConsultation '.$disabled.'" ><i class="far fa-edit text-primary"></i></a>';
						   $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox"   onclick="event.preventDefault();deleteInventory('.$row->id.',\''.$state.'\')"  '.$checked.'> <span class="slideon-slider" title="'.__("Active").'"></span></label>';
   						   $btn .= '<a href="javascript:void(0)"  title="'.__("PDF").'" class="btn btn-md btn-clean btn-icon   '.$disabled.'" onclick="getPDF('.$row->id.','.$row->supplier_id.',\''.$row->id_invoice.'\',\''.$row->name.'\')"><i class="far fa-file-pdf text-primary"></i></a>';
                           $btn .= '<a href="javascript:void(0)"  title="'.__("Email").'" class="btn btn-md btn-clean btn-icon   '.$disabled.'" onclick="sendPDF('.$row->id.')"><i class="far fa-envelope text-primary"></i></a>';
						   $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox"   onclick="event.preventDefault();cpaidCmd1('.$row->id.',\''.$state3.'\')"  '.$checked3.'> <span class="slideon-slider" title="'.__("Cash").'"></span></label>';
						   $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox"   onclick="event.preventDefault();cdoneCmd1('.$row->id.',\''.$state4.'\')"  '.$checked4.'> <span class="slideon-slider" title="'.__("Done").'" hidden ></span></label>';
						   $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox"   onclick="event.preventDefault();quoteCmd1('.$row->id.',\''.$state5.'\')"  '.$checked5.'> <span class="slideon-slider" title="'.__("Quotation").'"></span></label>';   
						   $btn .= '<a href="javascript:void(0)" title="'.__("Attach").'" class="btn btn-md btn-clean btn-icon '.$disabled.'" onclick="downloadPDF('.$row->id.', \''.$lang.'\')"><i class="fas fa-paperclip text-primary"></i></a>';
						   if($row->tQTY>0){
											$btn.='<button type="button" class="p-1 btn btn-md btn-clean btn-icon"><i title="'.__("Qty Modified").'" class="fa fa-exclamation-circle text-success"></i></button>';
											}	
						   }
						  return $btn;
                    })
					->filterColumn('total_pay',function($query, $keyword) {
						$sql= "(tbl_inventory_invoices_request.qst+tbl_inventory_invoices_request.gst+tbl_inventory_invoices_request.total) like ?";
					    $query->whereRaw($sql, ["%{$keyword}%"]);
					})
			        ->rawColumns(['return_invoices','action','validate','Email'])
                    ->make(true);
			 }
		   }
			
		   return view('sales.index')->with(['Type'=>$type,'branch'=>$FromFacility,'fournisseur'=>$fournisseur,
														  'clients'=>$clients, 'users'=>$users
														  ]);  
	  
}

public function editcommandsales($lang,$id,$typein)
    {
		
	$myId=auth()->user()->id;	
   	$view_modify_command = UserHelper::can_access(auth()->user(),'modify_command');
	$view_command_valid1 = UserHelper::can_access(auth()->user(),'command_valid1');
	$view_command_valid2 = UserHelper::can_access(auth()->user(),'command_valid2');
	$view_command_edit = UserHelper::can_access(auth()->user(),'command_edit');

	$ReqPay=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','P')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
	
	
	$cptpCount= $ReqPay->count();
	$ReqRef=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','R')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
	
	
	$cptrCount= $ReqRef->count();
    $ReqRemise=TblInventoryRemise::where('invoice_id',$id)->where('status','Y')->where('active','O')->get();
	$cptsCount= $ReqRemise->count();
		
	$sqlPR = "select item_code as id,item_name as name from tbl_inventory_invoices_details where invoice_id='".$id."' and active='O' order by id desc";
    $coderemise= DB::select(DB::raw("$sqlPR"));
 if(auth()->user()->type==1){
		
		/*$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		$FromFacility =DB::select(DB::raw("$sqlFromFacility"));*/
				
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
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->orderBy('name')->get();	
	$ReqDeails=TblInventoryInvoicesDetails::where('invoice_id',$id)->where('status','O')->get();
	$cptCount= $ReqDeails->count();
	$ReqDeails1=TblInventoryInvoicesDetails::where('invoice_id',$id)->where('status','O')->first();
	$refinvoice="";
	if (isset($ReqDeails1)){
	$refinvoice=$ReqDeails1->ref_invoice;	
	}
	$ReqPatient=TblInventoryInvoicesRequest::where('id',$id)->where(function($query) {
															$query->where('active','O')->orWhere('active','W');})->first();
	if ($ReqPatient->type=='99'){
	$ReqIDRemise=TblInventoryInvoicesRequest::where('id',$ReqPatient->reference)->where('active','O')->first();
	$ref=$ReqIDRemise->clinic_inv_num;
	$sqlP = "select item_code as id,item_name as name from tbl_inventory_invoices_details where invoice_id=".$ReqPatient->reference." and status='O' and active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlP"));
    $type= DB::select(DB::raw("select id,name from tbl_inventory_types where id='99'"));
	}else{
	$ref=$ReqPatient->reference;		
	//$sqlP = "select id,description as name from tbl_inventory_items where active='O' order by id desc";
    //$code= DB::select(DB::raw("$sqlP"));
	 $code = DB::table('tbl_inventory_items as a')
	        ->select('a.id as id',    DB::raw("CONCAT(a.description, ', Lot Nb: ', IFNULL(a.nblot, ''), ', Expiry Date: ', IFNULL(a.dexpiry, 'N/A')) as text"))
    ->join('tbl_inventory_items_fournisseur as s', 'a.fournisseur', '=', 's.id')
    ->where('a.active', 'O')->get();
	$type= DB::select(DB::raw("select id,name from tbl_inventory_types"));  
	}
	$patient=Patient::where('id',$ReqPatient->patient_id)->where('status','O')->get();	
	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->get();
	
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->orderBy('ord')->get();
    $FromFacility = Clinic::where('id',$ReqPatient->clinic_num)->where('active','O')->first();
	
	$iCategory= DB::table('tbl_inventory_category_types')->where('active','O')->where('types','1')->orderBy('id')->get();
	
	$typediscount=TblInventoryDiscountTypes::where('active','O')->get();
    $pay = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('payment_amount');
	$refund = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('payment_amount');
	if($lang=="fr"){
		$sql="select a.id,a.name from tbl_inventory_collection a where a.clinic_num = ".$FromFacility->id." and a.active='O' order by a.id";
		}else{
		$sql="select a.id,a.name_eng as name from tbl_inventory_collection a where a.clinic_num = ".$FromFacility->id." and a.active='O' order by a.id";
		}
	$collection=DB::select(DB::raw("$sql"));	
	$balance=$ReqPatient->balance;
	$stotal=$ReqPatient->total;
	$stotal=$ReqPatient->total;
		 $ReqPatientRet=TblInventoryInvoicesRequest::where('reference',$ReqPatient->clinic_inv_num)->where('active','O')->orderby('id','desc')->get();
		$ReqPatientRetCount= $ReqPatientRet->count();
	$rates=TblBillRate::where('status','O')->get();
	
		//check if there is documents for rx
		$documents = collect();
		$documents = DOCSCmd::where('order_id',$id)->where('active','Y')->get();
$user_perm = auth()->user()->permission;
	if ($user_perm == 'U') {
    // Get client permissions
    $client_permission_ids = Permission::where('clinic_num', $idFacility)
        ->where('uid', $myId)
        ->where('active', 'O')
        ->pluck('clients'); // Assuming 'clients' is a JSON array in the Permission table
    // Extract and query clients
    $clients = TblInventoryClients::where('active', 'O')
        ->where(function ($query) use ($client_permission_ids) {
            foreach ($client_permission_ids as $client_ids_json) {
                $client_ids = json_decode($client_ids_json, true); // Decode JSON to array
                $query->orWhereIn('id', $client_ids); // Check IDs in the clients JSON array
            }
        })
		->orderBy('name')
        ->get();
$users = User::select(DB::raw("id,CONCAT(fname, ' ', lname) as name"))
    ->where('active', 'O')
	->where('id', $myId)
    ->get();		
} else {
    // Retrieve all active clients
    $clients = TblInventoryClients::where('active', 'O')->orderBy('name')->get();
	$users = User::select(DB::raw("id,CONCAT(fname, ' ', lname) as name"))
    ->where('active', 'O')
    ->get();	
}
$category=DB::table('tbl_inventory_category_types')->where('active','O')->get();
	$analyzer=DB::table('tbl_inventory_collection')->where('active','O')->get();
 $code = DB::table('tbl_inventory_items as a')
       ->select('a.id as id',    DB::raw("CONCAT(a.description, ', Lot Nb: ', IFNULL(a.nblot, ''), ', Expiry Date: ', IFNULL(a.dexpiry, 'N/A')) as description"))
    ->where('a.active', 'O')->orderBy('description')->get();
	
	return view('sales.EditCommandSales')->with(['typein'=>$typein,'category'=>$category,'code'=>$code,'analyzer'=>$analyzer,'clients'=>$clients,'documents'=>$documents,'collection'=>$collection,
	                                                     'rates'=>$rates,'ReqPatientRetCount'=>$ReqPatientRetCount,
														 'ReqPatientRet'=>$ReqPatientRet,'stotal'=>$stotal,'ref'=>$ref,
														 'pay'=>$pay,'refund'=>$refund,'coderemise'=>$coderemise,
														 'ReqRemise'=>$ReqRemise,'cptsCount'=>$cptsCount,'pay'=>$pay,
														 'refund'=>$refund,'methodepay'=>$methodepay,'ReqPay'=>$ReqPay,
														 'ReqRef'=>$ReqRef,'cptCount'=>$cptCount,'cptrCount'=>$cptrCount,
														 'cptpCount'=>$cptpCount,'typediscount'=>$typediscount,
														 'iCategory'=>$iCategory,'FromFacility'=>$FromFacility,
														 'cptCount'=>$cptCount,'lunette_specs'=>$lunette_specs,'type'=>$type,
														 'Formula'=>$Formula,'iType'=>$iType,'patient'=>$patient,
														 'ReqPatient'=>$ReqPatient,'fournisseur'=>$fournisseur,'code'=>$code,
														 'ReqDeails'=>$ReqDeails,'balance'=>$balance,
														 'view_modify_command'=>$view_modify_command,
														 'view_command_valid1'=>$view_command_valid1,
														 'view_command_valid2'=>$view_command_valid2,
														 'view_command_edit'=>$view_command_edit,
														 'refinvoice'=>$refinvoice
														 ]);
		}
		


public function edit($lang,$id)
    {
		
	$myId=auth()->user()->id;	
	
	$ReqPay=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','P')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
	
	
	$cptpCount= $ReqPay->count();
	$ReqRef=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','R')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
	
	
	$cptrCount= $ReqRef->count();
    $ReqRemise=TblInventoryRemise::where('invoice_id',$id)->where('status','Y')->where('active','O')->get();
	$cptsCount= $ReqRemise->count();
		
	$sqlPR = "select item_code as id,item_name as name from tbl_inventory_invoices_details where invoice_id='".$id."' and active='O' order by id desc";
    $coderemise= DB::select(DB::raw("$sqlPR"));
 if(auth()->user()->type==1){
		
		/*$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		$FromFacility =DB::select(DB::raw("$sqlFromFacility"));*/
				
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
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->get();	
	$ReqDeails=TblInventoryInvoicesDetails::where('invoice_id',$id)->where('status','O')->get();
	$cptCount= $ReqDeails->count();	
	$ReqPatient=TblInventoryInvoicesRequest::where('id',$id)->where(function($query) {
															$query->where('active','O')->orWhere('active','W');})->first();
	if ($ReqPatient->type=='99'){
	$ReqIDRemise=TblInventoryInvoicesRequest::where('id',$ReqPatient->reference)->where('active','O')->first();
	$ref=$ReqIDRemise->clinic_inv_num;
	$sqlP = "select item_code as id,item_name as name from tbl_inventory_invoices_details where invoice_id=".$ReqPatient->reference." and status='O' and active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlP"));
    $type= DB::select(DB::raw("select id,name from tbl_inventory_types where id='99'"));
	}else{
	$ref=$ReqPatient->reference;		
	$sqlP = "select id,description as name from tbl_inventory_items where active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlP"));
	$type= DB::select(DB::raw("select id,name from tbl_inventory_types"));  
	}
	$patient=Patient::where('id',$ReqPatient->patient_id)->where('status','O')->get();	
	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->get();
	
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->orderBy('ord')->get();
    $FromFacility = Clinic::where('id',$ReqPatient->clinic_num)->where('active','O')->first();
	
	$iCategory= DB::table('tbl_inventory_category_types')->where('active','O')->where('types','1')->orderBy('id')->get();
	
	$typediscount=TblInventoryDiscountTypes::where('active','O')->get();
    $pay = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('payment_amount');
	$refund = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('payment_amount');
	if($lang=="fr"){
		$sql="select a.id,a.name from tbl_inventory_collection a where a.clinic_num = ".$FromFacility->id." and a.active='O' order by a.id";
		}else{
		$sql="select a.id,a.name_eng as name from tbl_inventory_collection a where a.clinic_num = ".$FromFacility->id." and a.active='O' order by a.id";
		}
	$collection=DB::select(DB::raw("$sql"));	
	$balance=$ReqPatient->balance;
	$stotal=$ReqPatient->total-$ReqPatient->discount+$ReqPatient->gst+$ReqPatient->qst;
	$stotal=$ReqPatient->total+$ReqPatient->gst+$ReqPatient->qst;
		//dd($balance);
		 $ReqPatientRet=TblInventoryInvoicesRequest::where('reference',$ReqPatient->clinic_inv_num)->where('active','O')->orderby('id','desc')->get();
		$ReqPatientRetCount= $ReqPatientRet->count();
	$rates=TblBillRate::where('status','O')->get();
	return view('inventory.invoices.EditInvoices')->with(['collection'=>$collection,'rates'=>$rates,'ReqPatientRetCount'=>$ReqPatientRetCount,'ReqPatientRet'=>$ReqPatientRet,'stotal'=>$stotal,'ref'=>$ref,'pay'=>$pay,'refund'=>$refund,'coderemise'=>$coderemise,'ReqRemise'=>$ReqRemise,'cptsCount'=>$cptsCount,'pay'=>$pay,'refund'=>$refund,'methodepay'=>$methodepay,'ReqPay'=>$ReqPay,'ReqRef'=>$ReqRef,'cptCount'=>$cptCount,'cptrCount'=>$cptrCount,'cptpCount'=>$cptpCount,'typediscount'=>$typediscount,'iCategory'=>$iCategory,'FromFacility'=>$FromFacility,'cptCount'=>$cptCount,'lunette_specs'=>$lunette_specs,'type'=>$type,'Formula'=>$Formula,'iType'=>$iType,'patient'=>$patient,'ReqPatient'=>$ReqPatient,'fournisseur'=>$fournisseur,'code'=>$code,'ReqDeails'=>$ReqDeails,'balance'=>$balance]);
		}
		
public function editadj($lang,$id)
    {
		
	$myId=auth()->user()->id;	
	
	$ReqPay=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','P')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
	
	
	$cptpCount= $ReqPay->count();
	$ReqRef=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','R')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
	
	
	$cptrCount= $ReqRef->count();
    $ReqRemise=TblInventoryRemise::where('invoice_id',$id)->where('status','Y')->where('active','O')->get();
	$cptsCount= $ReqRemise->count();
		
	$sqlPR = "select item_code as id,item_name as name from tbl_inventory_invoices_details where invoice_id='".$id."' and active='O' order by id desc";
    $coderemise= DB::select(DB::raw("$sqlPR"));
 if(auth()->user()->type==1){
		
		/*$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		$FromFacility =DB::select(DB::raw("$sqlFromFacility"));*/
				
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
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->get();	
	$ReqDeails=TblInventoryInvoicesDetails::where('invoice_id',$id)->where('status','O')->get();
	$cptCount= $ReqDeails->count();	
	$ReqPatient=TblInventoryInvoicesRequest::where('id',$id)->where('active','O')->first();
	if ($ReqPatient->type=='99'){
	$ReqIDRemise=TblInventoryInvoicesRequest::where('id',$ReqPatient->reference)->where('active','O')->first();
	$ref=$ReqIDRemise->clinic_inv_num;
	$sqlP = "select item_code as id,item_name as name from tbl_inventory_invoices_details where invoice_id=".$ReqPatient->reference." and status='O' and active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlP"));
    $type= DB::select(DB::raw("select id,name from tbl_inventory_types where id='99'"));
	}else{
	$ref=$ReqPatient->reference;		
	$sqlP = "select id,description as name from tbl_inventory_items where active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlP"));
	$type= DB::select(DB::raw("select id,name from tbl_inventory_types"));  
	}
	$patient=Patient::where('id',$ReqPatient->patient_id)->where('status','O')->get();	
	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->get();
	
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->orderBy('ord')->get();
    $FromFacility = Clinic::where('id',$ReqPatient->clinic_num)->where('active','O')->first();
	$iCategory= DB::table('tbl_inventory_category_types')->where('active','O')->where('types','1')->orderBy('id')->get();
	$typediscount=TblInventoryDiscountTypes::where('active','O')->get();
    $pay = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('payment_amount');
	$refund = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('payment_amount');
	if($lang=="fr"){
		$sql="select a.id,a.name from tbl_inventory_collection a where a.clinic_num = ".$FromFacility->id." and a.active='O' order by a.id";
		}else{
		$sql="select a.id,a.name_eng as name from tbl_inventory_collection a where a.clinic_num = ".$FromFacility->id." and a.active='O' order by a.id";
		}
	$collection=DB::select(DB::raw("$sql"));	
	$balance=$ReqPatient->balance;
	$stotal=$ReqPatient->total-$ReqPatient->discount+$ReqPatient->gst+$ReqPatient->qst;
	$stotal=$ReqPatient->total+$ReqPatient->gst+$ReqPatient->qst;
		//dd($balance);
		 $ReqPatientRet=TblInventoryInvoicesRequest::where('reference',$ReqPatient->clinic_inv_num)->where('active','O')->orderby('id','desc')->get();
		$ReqPatientRetCount= $ReqPatientRet->count();
	$rates=TblBillRate::where('status','O')->get();
	return view('inventory.invoices.EditAdj')->with(['collection'=>$collection,'rates'=>$rates,'ReqPatientRetCount'=>$ReqPatientRetCount,'ReqPatientRet'=>$ReqPatientRet,'stotal'=>$stotal,'ref'=>$ref,'pay'=>$pay,'refund'=>$refund,'coderemise'=>$coderemise,'ReqRemise'=>$ReqRemise,'cptsCount'=>$cptsCount,'pay'=>$pay,'refund'=>$refund,'methodepay'=>$methodepay,'ReqPay'=>$ReqPay,'ReqRef'=>$ReqRef,'cptCount'=>$cptCount,'cptrCount'=>$cptrCount,'cptpCount'=>$cptpCount,'typediscount'=>$typediscount,'iCategory'=>$iCategory,'FromFacility'=>$FromFacility,'cptCount'=>$cptCount,'lunette_specs'=>$lunette_specs,'type'=>$type,'Formula'=>$Formula,'iType'=>$iType,'patient'=>$patient,'ReqPatient'=>$ReqPatient,'fournisseur'=>$fournisseur,'code'=>$code,'ReqDeails'=>$ReqDeails,'balance'=>$balance]);
		}
		
public function EditRInvoices($lang,$id)
    {
		
	$myId=auth()->user()->id;	
	$ReqPatient=TblInventoryInvoicesRequest::where('id',$id)->where('active','O')->first();
	$ReqPay=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','P')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
	
	
	$cptpCount= $ReqPay->count();
	if ($ReqPatient->cr_note=='Y'){
		$cnote='Y';
		$ReqRef=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','C')
			->where('crnote','Y')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
		$refund = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'R')->where('crnote', '=', 'Y')->where('status', '=', 'Y')->sum('payment_amount');
	}else{
		$cnote='N';
		$ReqRef=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','R')
			->where('crnote','N')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
			$refund = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'R')->where('crnote', '=', 'N')->where('status', '=', 'Y')->sum('payment_amount');

	}	
	
	$cptrCount= $ReqRef->count();
    $ReqRemise=TblInventoryRemise::where('invoice_id',$id)->where('status','Y')->where('active','O')->get();
	$cptsCount= $ReqRemise->count();
		
	$sqlPR = "select item_code as id,item_name as name from tbl_inventory_invoices_details where invoice_id='".$id."' and active='O' order by id desc";
    $coderemise= DB::select(DB::raw("$sqlPR"));
 if(auth()->user()->type==1){
		
		/*$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		$FromFacility =DB::select(DB::raw("$sqlFromFacility"));*/
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
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->get();	
	$ReqDeails=TblInventoryInvoicesDetails::where('invoice_id',$id)->where('status','O')->get();
	$cptCount= $ReqDeails->count();	
	
	$ref=$ReqPatient->reference;		
	$sqlP = "select id,description as name from tbl_inventory_items where fournisseur=".$ReqPatient->fournisseur_id." and active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlP"));
	$type= DB::select(DB::raw("select id,name from tbl_inventory_types"));  

	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->get();
	
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->orderBy('ord')->get();
    $FromFacility = Clinic::where('id',$ReqPatient->clinic_num)->where('active','O')->first();
	$iCategory= DB::table('tbl_inventory_category_types')->where('active','O')->where('types','1')->orderBy('id')->get();
	$typediscount=TblInventoryDiscountTypes::where('active','O')->get();
    $pay = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('payment_amount');
	$refund = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('payment_amount');

	$balance=$ReqPatient->balance;
		//dd($balance);
	$sQLReqInvNb = "SELECT a.id, a.reference as clinic_inv_num from 
                           tbl_inventory_invoices_request a where 
                          a.id ='". $id."' and a.active = 'O'
						 and a.type='4'";
        
$facture =DB::select(DB::raw("$sQLReqInvNb"));	
	$rates=TblBillRate::where('status','O')->get();
	$stotal=$ReqPatient->total+$ReqPatient->qst+$ReqPatient->gst;
if ($ReqPatient->cr_note=='G'){
$garanty='G';
}else{
$garanty='N';
	
}
	return view('inventory.invoices.EditRInvoices')->with(['garanty'=>$garanty,'cnote'=>$cnote,'rates'=>$rates,'ref'=>$ref,'pay'=>$pay,'refund'=>$refund,'coderemise'=>$coderemise,'ReqRemise'=>$ReqRemise,'cptsCount'=>$cptsCount,'pay'=>$pay,'refund'=>$refund,'methodepay'=>$methodepay,'ReqPay'=>$ReqPay,'ReqRef'=>$ReqRef,'cptCount'=>$cptCount,'cptrCount'=>$cptrCount,'cptpCount'=>$cptpCount,'typediscount'=>$typediscount,'iCategory'=>$iCategory,'FromFacility'=>$FromFacility,'cptCount'=>$cptCount,'lunette_specs'=>$lunette_specs,'type'=>$type,'Formula'=>$Formula,'iType'=>$iType,'facture'=>$facture,'ReqPatient'=>$ReqPatient,'fournisseur'=>$fournisseur,'code'=>$code,'ReqDeails'=>$ReqDeails,'balance'=>$balance,'stotal'=>$stotal]);
		}	
public function editsales($lang,$id)
    {
		
	$myId=auth()->user()->id;	
	
	$ReqPay=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','P')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
	
	
	$cptpCount= $ReqPay->count();
	$ReqRef=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','R')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
	
	
	$cptrCount= $ReqRef->count();
    $ReqRemise=TblInventoryRemise::where('invoice_id',$id)->where('status','Y')->where('active','O')->get();
	$cptsCount= $ReqRemise->count();
		
	$sqlPR = "select id as idrow,item_code as id,item_name as name from tbl_inventory_invoices_details where invoice_id='".$id."' and status='O' order by idrow desc";
    $coderemise= DB::select(DB::raw("$sqlPR"));
 if(auth()->user()->type==1){
		
		/*$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		$FromFacility =DB::select(DB::raw("$sqlFromFacility"));*/
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
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->get();	
	$ReqDeails=TblInventoryInvoicesDetails::where('invoice_id',$id)->where('status','O')->get();
	$cptCount= $ReqDeails->count();	
	$ReqPatient=TblInventoryInvoicesRequest::where('id',$id)->where(function($query) {
															$query->where('active','O')->orWhere('active','W');})->first();
	$UserName=User::where('id',$myId)->first()->username;														
	if ($ReqPatient->type=='99'){
	$ReqIDRemise=TblInventoryRemise::where('id',$ReqPatient->reference)->where('active','O')->where('status','Y')->first();
	//$cptsCount= $ReqRemise->count();
    $ReqPatientRef=TblInventoryInvoicesRequest::where('id',$ReqPatient->reference)->where('active','O')->first();
	$ref=$ReqPatientRef->clinic_inv_num;
	$sqlP = "select item_code as id,item_name as name,'' as namefournisseur from tbl_inventory_invoices_details where invoice_id=".$ReqPatient->reference." and status='O' and active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlP"));
    $type= DB::select(DB::raw("select id,name from tbl_inventory_types where id='99'"));
	}else{
		 
		$ref=$ReqPatient->reference;		
		 $sqlCode = "select a.id,a.description as name,b.name as namefournisseur from tbl_inventory_items a,tbl_inventory_items_fournisseur b where a.fournisseur=b.id and a.active='O' order by a.id desc";
		$code= DB::select(DB::raw("$sqlCode"));
		$type= DB::select(DB::raw("select id,name from tbl_inventory_types"));  
	    
	}
	$patient=Patient::get();	
	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->get();
	
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->orderBy('ord')->get();
    $FromFacility = Clinic::where('id',$ReqPatient->clinic_num)->where('active','O')->first();
	$iCategory= DB::table('tbl_inventory_category_types')->where('active','O')->where('types','1')->orderBy('id')->get();
	if($lang=="fr"){
		$sql="select a.id,a.name from tbl_inventory_collection a where a.clinic_num = ".$FromFacility->id." and a.active='O' order by a.id";
		}else{
		$sql="select a.id,a.name_eng as name from tbl_inventory_collection a where a.clinic_num = ".$FromFacility->id." and a.active='O' order by a.id";
		}
	$collection=DB::select(DB::raw("$sql"));	
	$typediscount=TblInventoryDiscountTypes::where('active','O')->get();
    $pay = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('payment_amount');
	$refund = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'R')->where('status', '=', 'Y')->sum('payment_amount');
	$stotal=$ReqPatient->total+$ReqPatient->qst+$ReqPatient->gst;
	$balance=$ReqPatient->balance;
	$ReqPatientRet=TblInventoryInvoicesRequest::where(function ($q) use ($ReqPatient){ 
	                                            $q->where('reference',$ReqPatient->clinic_inv_num)
												  ->orWhere('reference',$ReqPatient->id);
												})->where('active','O')->orderby('id','desc')->get();
												
	$ReqPatientRetCount= $ReqPatientRet->count();
	$rates=TblBillRate::where('status','O')->get();
	return view('inventory.invoices.EditSales')->with(['myId'=>$myId,'username'=>$UserName,'collection'=>$collection,'rates'=>$rates,'ReqPatientRetCount'=>$ReqPatientRetCount,'ReqPatientRet'=>$ReqPatientRet,'stotal'=>$stotal,'ref'=>$ref,'pay'=>$pay,'refund'=>$refund,'coderemise'=>$coderemise,'ReqRemise'=>$ReqRemise,'cptsCount'=>$cptsCount,'pay'=>$pay,'refund'=>$refund,'methodepay'=>$methodepay,'ReqPay'=>$ReqPay,'ReqRef'=>$ReqRef,'cptCount'=>$cptCount,'cptrCount'=>$cptrCount,'cptpCount'=>$cptpCount,'typediscount'=>$typediscount,'iCategory'=>$iCategory,'FromFacility'=>$FromFacility,'cptCount'=>$cptCount,'lunette_specs'=>$lunette_specs,'type'=>$type,'Formula'=>$Formula,'iType'=>$iType,'patient'=>$patient,'ReqPatient'=>$ReqPatient,'fournisseur'=>$fournisseur,'code'=>$code,'ReqDeails'=>$ReqDeails,'balance'=>$balance]);
		}
     

public function EditRSales($lang,$id)
    {
		
	$myId=auth()->user()->id;	
	$ReqPatient=TblInventoryInvoicesRequest::where('id',$id)->where('active','O')->first();
	$ReqPay=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','P')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
	
	
	$cptpCount= $ReqPay->count();
if ($ReqPatient->cr_note=='Y'){
		$cnote='Y';
		$ReqRef=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','C')
			->where('crnote','Y')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
		$refund = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'R')->where('crnote', '=', 'Y')->where('status', '=', 'Y')->sum('payment_amount');
	}else{
		$cnote='N';
		$ReqRef=TblInventoryPayment::join('tbl_bill_payment_mode', 'tbl_bill_payment_mode.id', '=', 'tbl_inventory_payment.reference')
            ->where('tbl_bill_payment_mode.status', 'O')
            ->where('tbl_inventory_payment.status','Y')
			->where('invoice_num',$id)
			->where('payment_type','R')
			->where('crnote','N')
            ->get(['tbl_inventory_payment.*', 'tbl_bill_payment_mode.name_fr']);
			$refund = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'R')->where('crnote', '=', 'N')->where('status', '=', 'Y')->sum('payment_amount');

	}	
	
	
	
	$cptrCount= $ReqRef->count();
    $ReqRemise=TblInventoryRemise::where('invoice_id',$id)->where('status','Y')->where('active','O')->get();
	$cptsCount= $ReqRemise->count();
		
	$sqlPR = "select item_code as id,item_name as name from tbl_inventory_invoices_details where invoice_id='".$id."' and active='O' order by id desc";
    $coderemise= DB::select(DB::raw("$sqlPR"));
 if(auth()->user()->type==1){
		
		/*$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		$FromFacility =DB::select(DB::raw("$sqlFromFacility"));*/
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
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->get();	
	$ReqDeails=TblInventoryInvoicesDetails::where('invoice_id',$id)->where('status','O')->get();
	$cptCount= $ReqDeails->count();	
	if ($ReqPatient->type=='99'){
	$ReqIDRemise=TblInventoryInvoicesRequest::where('id',$ReqPatient->reference)->where('active','O')->first();
	$ref=$ReqIDRemise->clinic_inv_num;
	$sqlP = "select item_code as id,item_name as name from tbl_inventory_invoices_details where invoice_id=".$ReqPatient->reference." and status='O' and active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlP"));
    $type= DB::select(DB::raw("select id,name from tbl_inventory_types where id='99'"));
	}else{
	$ref=$ReqPatient->reference;		
	$sqlP = "select id,description as name from tbl_inventory_items where active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlP"));
	$type= DB::select(DB::raw("select id,name from tbl_inventory_types"));  
	}
	$patient=Patient::get();	
	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->get();
	
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->orderBy('ord')->get();
    $FromFacility = Clinic::where('id',$ReqPatient->clinic_num)->where('active','O')->first();
	$iCategory= DB::table('tbl_inventory_category_types')->where('active','O')->where('types','1')->orderBy('id')->get();
	$typediscount=TblInventoryDiscountTypes::where('active','O')->get();
    $pay = DB::table('tbl_inventory_payment')->where('invoice_num', '=', $id)->where('payment_type', '=', 'P')->where('status', '=', 'Y')->sum('payment_amount');

	$balance=$ReqPatient->balance;
	$stotal=$ReqPatient->total+$ReqPatient->qst+$ReqPatient->gst;
		//dd($balance);
	$facture=TblInventoryInvoicesRequest::where('patient_id',$ReqPatient->patient_id)
			->where('active','O')
			->where('type','2')
			->orderby('id','desc')
            ->get();	
	$rates=TblBillRate::where('status','O')->get();
	
	return view('inventory.invoices.EditRSales')->with(['cnote'=>$cnote,'rates'=>$rates,'stotal'=>$stotal,'facture'=>$facture,'ref'=>$ref,'pay'=>$pay,'refund'=>$refund,'coderemise'=>$coderemise,'ReqRemise'=>$ReqRemise,'cptsCount'=>$cptsCount,'pay'=>$pay,'refund'=>$refund,'methodepay'=>$methodepay,'ReqPay'=>$ReqPay,'ReqRef'=>$ReqRef,'cptCount'=>$cptCount,'cptrCount'=>$cptrCount,'cptpCount'=>$cptpCount,'typediscount'=>$typediscount,'iCategory'=>$iCategory,'FromFacility'=>$FromFacility,'cptCount'=>$cptCount,'lunette_specs'=>$lunette_specs,'type'=>$type,'Formula'=>$Formula,'iType'=>$iType,'patient'=>$patient,'ReqPatient'=>$ReqPatient,'fournisseur'=>$fournisseur,'code'=>$code,'ReqDeails'=>$ReqDeails,'balance'=>$balance]);
		}
     
public function create($lang)
    {
		return view('inventory.invoices.NewInvoices');
    }
	
public function NewInvoices($lang,$id,Request $request) 
    {
	$myId=auth()->user()->id;	
	 if(auth()->user()->type==1){
		
		/*$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		$FromFacility =DB::select(DB::raw("$sqlFromFacility"));*/
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
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->get();	
    $sqlCode = "select id,description as name from tbl_inventory_items where active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlCode"));
	if ($id=="3"){
    $sqlP = "select id,name from tbl_inventory_types where id='3' or id='4'";
	}else{
		$sqlP = "select id,name from tbl_inventory_types where id='".$id."'";
	}
    $type= DB::select(DB::raw("$sqlP"));  
	$type1=TblInventoryTypes::where('active','O')->where('id',$id)->first();	
	$patient=Patient::where('status','O')->get();	
	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->get();
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->where('fournisseur_type','1')->orderBy('ord')->get();
    $iCategory= DB::table('tbl_inventory_category_types')->where('active','O')->where('types','1')->orderBy('id')->get();
    $typediscount=TblInventoryDiscountTypes::where('active','O')->get();
	$rates=TblBillRate::where('status','O')->get();
  return view('inventory.invoices.NewInvoices')->with(['collection'=>$collection,'rates'=>$rates,'typediscount'=>$typediscount,'methodepay'=>$methodepay,'fournisseur'=>$fournisseur,'type'=>$type,'type1'=>$type1,
	                                                     'Formula'=>$Formula,'iType'=>$iType,'lunette_specs'=>$lunette_specs,
														 'code'=>$code,'patient'=>$patient,'iCategory'=>$iCategory,'FromFacility'=>$FromFacility]);
	}
public function NewRInvoices($lang,$id,Request $request) 
    {
	$myId=auth()->user()->id;	
	 if(auth()->user()->type==1){
		
		/*$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		$FromFacility =DB::select(DB::raw("$sqlFromFacility"));*/
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
	
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->get();	
    $sqlCode = "select id,description as name from tbl_inventory_items where active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlCode"));
	if ($id=="3"){
    $sqlP = "select id,name from tbl_inventory_types where id='3' or id='4'";
	}else{
		$sqlP = "select id,name from tbl_inventory_types where id='".$id."'";
	}
    $type= DB::select(DB::raw("$sqlP"));  
	$type1=TblInventoryTypes::where('active','O')->where('id',$id)->first();	
	$patient=Patient::where('status','O')->get();	
	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->get();
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->where('fournisseur_type','1')->orderBy('ord')->get();
    $iCategory= DB::table('tbl_inventory_category_types')->where('active','O')->where('types','1')->orderBy('id')->get();
    $typediscount=TblInventoryDiscountTypes::where('active','O')->get();
	$rates=TblBillRate::where('status','O')->get();
  return view('inventory.invoices.NewRInvoices')->with(['rates'=>$rates,'typediscount'=>$typediscount,'methodepay'=>$methodepay,'fournisseur'=>$fournisseur,'type'=>$type,'type1'=>$type1,
	                                                     'Formula'=>$Formula,'iType'=>$iType,'lunette_specs'=>$lunette_specs,
														 'code'=>$code,'patient'=>$patient,'iCategory'=>$iCategory,'FromFacility'=>$FromFacility]);
	}	
public function NewSales($lang,$id,Request $request) 
    {
	$myId=auth()->user()->id;	
	$myName=auth()->user()->username;	
	 if(auth()->user()->type==1){
		
		/*$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		$FromFacility =DB::select(DB::raw("$sqlFromFacility"));*/
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
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->get();	
    $sqlCode = "select a.id,a.description as name,b.name as namefournisseur from tbl_inventory_items a,tbl_inventory_items_fournisseur b where a.fournisseur=b.id and a.active='O' order by a.id desc";
    $code= DB::select(DB::raw("$sqlCode"));
	if ($id=="3"){
    $sqlP = "select id,name from tbl_inventory_types where id='3' or id='4'";
	}else{
		$sqlP = "select id,name from tbl_inventory_types where id='".$id."'";
	}
    $type= DB::select(DB::raw("$sqlP"));  
	$type1=TblInventoryTypes::where('active','O')->where('id',$id)->first();	
	$patient=Patient::where('status','O')->get();	
	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->get();
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->where('fournisseur_type','1')->orderBy('ord')->get();
    $iCategory= DB::table('tbl_inventory_category_types')->where('active','O')->where('types','1')->orderBy('id')->get();
    $typediscount=TblInventoryDiscountTypes::where('active','O')->get();
	$rates=TblBillRate::where('status','O')->get();

  return view('inventory.invoices.NewSales')->with(['myId'=>$myId,'myName'=>$myName,'collection'=>$collection,'rates'=>$rates,'typediscount'=>$typediscount,'methodepay'=>$methodepay,'fournisseur'=>$fournisseur,'type'=>$type,'type1'=>$type1,
	                                                     'Formula'=>$Formula,'iType'=>$iType,'lunette_specs'=>$lunette_specs,
														 'code'=>$code,'patient'=>$patient,'iCategory'=>$iCategory,'FromFacility'=>$FromFacility]);
	}
public function NewRSales($lang,$id,Request $request) 
    {
	$myId=auth()->user()->id;	
	 if(auth()->user()->type==1){
		
		/*$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		$FromFacility =DB::select(DB::raw("$sqlFromFacility"));*/
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
	
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->get();	
    $sqlCode = "select id,description as name from tbl_inventory_items where active='O' order by id desc";
    $code= DB::select(DB::raw("$sqlCode"));
	if ($id=="3"){
    $sqlP = "select id,name from tbl_inventory_types where id='3' or id='4'";
	}else{
		$sqlP = "select id,name from tbl_inventory_types where id='".$id."'";
	}
    $type= DB::select(DB::raw("$sqlP"));  
	$type1=TblInventoryTypes::where('active','O')->where('id',$id)->first();	
	$patient=Patient::where('status','O')->get();	
	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->get();
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->where('fournisseur_type','1')->orderBy('ord')->get();
    $iCategory= DB::table('tbl_inventory_category_types')->where('active','O')->where('types','1')->orderBy('id')->get();
    $typediscount=TblInventoryDiscountTypes::where('active','O')->get();
	$rates=TblBillRate::where('status','O')->get();
  return view('inventory.invoices.NewRSales')->with(['rates'=>$rates,'typediscount'=>$typediscount,'methodepay'=>$methodepay,'fournisseur'=>$fournisseur,'type'=>$type,'type1'=>$type1,
	                                                     'Formula'=>$Formula,'iType'=>$iType,'lunette_specs'=>$lunette_specs,
														 'code'=>$code,'patient'=>$patient,'iCategory'=>$iCategory,'FromFacility'=>$FromFacility]);
	}	
	
public function NewCommandSales($lang,$id,$typein,Request $request) 
    {
	$myId=auth()->user()->id;	
	 if(auth()->user()->type==1){
		
		/*$FromPro=Doctor::where('doctor_user_num',$myId)->where('active','O')->first();
        
		
        $sqlFromFacility = "SELECT a.clinic_num as id, b.full_name, b.full_address, b.city_num from 
                            tbl_doctors_clinics a, tbl_clinics b where 
                            a.clinic_num = b.id and a.doctor_num = ".$FromPro->id." and a.active = 'O'";
        
		$FromFacility =DB::select(DB::raw("$sqlFromFacility"));*/
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
	$user_perm = auth()->user()->permission;	
	if ($user_perm == 'U') {
    // Get client permissions
    $client_permission_ids = Permission::where('clinic_num', $idFacility)
        ->where('uid', $myId)
        ->where('active', 'O')
        ->pluck('clients'); // Assuming 'clients' is a JSON array in the Permission table
    // Extract and query clients
    $clients = TblInventoryClients::where('active', 'O')
        ->where(function ($query) use ($client_permission_ids) {
            foreach ($client_permission_ids as $client_ids_json) {
                $client_ids = json_decode($client_ids_json, true); // Decode JSON to array
                $query->orWhereIn('id', $client_ids); // Check IDs in the clients JSON array
            }
        })
		->orderBy('name')
        ->get();
		$users = User::select(DB::raw("id,CONCAT(fname, ' ', lname) as name"))
			->where('active', 'O')
			->where('id', $myId)
			->get();		
		} else {
			// Retrieve all active clients
			$clients = TblInventoryClients::where('active', 'O')->orderBy('name')->get();
			$users = User::select(DB::raw("id,CONCAT(fname, ' ', lname) as name"))
			->where('active', 'O')
			->get();	
		}
	$collection=DB::select(DB::raw("$sql"));	
	$fournisseur=TblInventoryItemsFournisseur::where('active','O')->where('fournisseur','Y')->orderBy('name')->get();	

    $sqlCode = "select id,CONCAT(description, ', Lot Nb: ', IFNULL(nblot, ''), ', Expiry Date: ', IFNULL(dexpiry, 'N/A')) as name from tbl_inventory_items where active='O' order by name asc";
    $code= DB::select(DB::raw("$sqlCode"));
	if ($id=="3"){
    $sqlP = "select id,name from tbl_inventory_types where id='3' or id='4'";
	}else{
		$sqlP = "select id,name from tbl_inventory_types where id='".$id."'";
	}
    $type= DB::select(DB::raw("$sqlP"));  
	$type1=TblInventoryTypes::where('active','O')->where('id',$id)->first();	
	$patient=Patient::where('status','O')->get();	
	$Formula=TblInventoryItemsFormula::where('active','O')->get();
	$iType=TblInventoryItemsTypes::where('active','O')->get();
	$lunette_specs= DB::table('tbl_inventory_lunette_types')->where('active','O')->where('fournisseur_type','1')->orderBy('ord')->get();
    $iCategory= DB::table('tbl_inventory_category_types')->where('active','O')->where('types','1')->orderBy('id')->get();
    $typediscount=TblInventoryDiscountTypes::where('active','O')->get();
	$rates=TblBillRate::where('status','O')->get();
	$category=DB::table('tbl_inventory_category_types')->where('active','O')->get();
	$analyzer=DB::table('tbl_inventory_fournisseur_collection')->select('id','name','name_eng')->where('active','O')->get();
	$code=DB::table('tbl_inventory_items')->select('id',    DB::raw("CONCAT(description, ', Lot Nb: ', IFNULL(nblot, ''), ', Expiry Date: ', IFNULL(dexpiry, 'N/A')) as description"))
										  ->where('active','O')->orderBy('description')->get();
 
    
  return view('sales.NewCommandSales')->with(['typein'=>$typein,'code'=>$code,'analyzer'=>$analyzer,'category'=>$category,'collection'=>$collection,'rates'=>$rates,'typediscount'=>$typediscount,'methodepay'=>$methodepay,'fournisseur'=>$fournisseur,'type'=>$type,'type1'=>$type1,
	                                                     'Formula'=>$Formula,'iType'=>$iType,'lunette_specs'=>$lunette_specs,
														 'code'=>$code,'clients'=>$clients,'users'=>$users,'patient'=>$patient,'iCategory'=>$iCategory,'FromFacility'=>$FromFacility]);
	}	
function fillSalesDel($lang,Request $request){
	    
   $descrip0=TblInventoryItems::where('id',$request->code)->first();
//	$tax=TblBillRate::where('id',$request->selecttax)->first();
 // if($request->taxable=='Y'){
	//			$taxqst=$tax->tvq;
	//			$taxgst= $tax->tvs;
	//	 }else{
				$taxqst=1;
				$taxgst=1; 
	//	 }	
	     
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

function fillPriceInvSales($lang,Request $request){
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
		$offre=$Price->offre;
		$ddiscount=$Price->discount;
   $torder = DB::table('tbl_inventory_invoices_details')
                    ->leftJoin('tbl_inventory_invoices_request', 'tbl_inventory_invoices_details.invoice_id', '=', 'tbl_inventory_invoices_request.id')
                    ->where('tbl_inventory_invoices_details.item_code', $txtCode)
                    ->where('tbl_inventory_invoices_details.rqty', '=', '0')
                    ->where('tbl_inventory_invoices_details.status', 'O')
					->where('tbl_inventory_invoices_request.active', 'O')
					->where('tbl_inventory_invoices_request.type', '6')
                    ->select('tbl_inventory_invoices_request.clinic_inv_num')
                    ->get();
					$resultsArray = $torder->toArray();
    return response()->json(['invoice_details' => $resultsArray ,'offre'=>$offre,'ddiscount'=>$ddiscount,'gen_types'=>$Price->typecode,'Category'=>$Price->category,'Price'=>$sel_price,'NbItemStock'=>$NbItemStock,'tax'=>$Price->taxable,'cost_price'=>$Price->cost_price,'initprice'=>$Price->initprice,'formula_id'=>$Price->formula_id]);
		  }   

function EfillPriceInvSales($lang,Request $request){
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
		$offre=$Price->offre;
		$ddiscount=$Price->discount;
   $torder = DB::table('tbl_inventory_invoices_details')
                    ->leftJoin('tbl_inventory_invoices_request', 'tbl_inventory_invoices_details.invoice_id', '=', 'tbl_inventory_invoices_request.id')
                    ->where('tbl_inventory_invoices_details.item_code', $txtCode)
                    ->where('tbl_inventory_invoices_details.rqty', '=', '0')
                    ->where('tbl_inventory_invoices_details.status', 'O')
					->where('tbl_inventory_invoices_request.active', 'O')
					->where('tbl_inventory_invoices_request.type', '6')
                    ->select('tbl_inventory_invoices_request.clinic_inv_num')
                    ->get();
					$resultsArray = $torder->toArray();
    return response()->json(['invoice_details' => $resultsArray ,'offre'=>$offre,'ddiscount'=>$ddiscount,'gen_types'=>$Price->typecode,'Category'=>$Price->category,'Price'=>$sel_price,'NbItemStock'=>$NbItemStock,'tax'=>$Price->taxable,'cost_price'=>$Price->cost_price,'initprice'=>$Price->initprice,'formula_id'=>$Price->formula_id]);
		  }   
function fillInvoiceType($lang,Request $request){
	    $txtType=$request->selecttype;
		
	$type = TblInventoryTypes::where('id',$txtType)->where('active','O')->first();
	return response()->json(['type'=>$type->type]);
		  }   
function addInvoiceRowCommand($lang,Request $request){
  // alert($this->cpt);
    	 
	  
      if($request->selectcode=="0" || $request->selectcode==NULL){
		  $msg=__('Please select your Code!');
		  return response()->json(['warning' =>$msg]);	
		
			 }
	//	if ($request->selecttype=="6"){	 
	//  if($request->selectpro=="" || $request->selectpro==NULL){
	//		 $msg=__('Please select your Professional!');
	//		 return response()->json(['warning' =>$msg]);	

			
	//		 }
		
	//		}
	   if (is_numeric($request->valqty)!=1){
				$msg=__('Please enter numeric value in Qty');
				return response()->json(['warning' =>$msg]);	
		
		}
	
		if (is_numeric($request->valprice)!=1){
				$msg=__('Please enter numeric value in Price');
				return response()->json(['warning' =>$msg]);	
		
		}	

	  $price=$request->valprice;
	  $qty=$request->valqty;
      $cpt=$request->cpt;
	 $rqty=$request->rqty;
	 $rprice=$request->rprice;
	  $total=($qty*$price);
	  $totalf=$request->totalf+(($price*$qty));

	// if ($totalf<0){
		//		$msg=__('You cannot Discount more than the total price');
		//		return response()->json(['warning' =>$msg]);	
	
	//	}
     $descrip0=TblInventoryItems::where('id',$request->selectcode)->first();
	 $descrip='';
	
  	  $descrip=$descrip0->description ;
        
	   $arrTaping = array("id"=>$cpt,
								"id_details"=>"",
                                "invoice_id"=>$request->invoice_id,
                                "code"=>$request->selectcode,
                                "descrip"=>$descrip,
                                "qty"=>$qty,
								"price"=>$price,
								"total"=>$total,
								"rqty"=>$rqty,
								"rprice"=>$rprice
								);
								
	

		  $balance=$totalf;
		
	  	 $balance=number_format((float)$balance, 3, '.', '');
		 $stotal=$totalf;
	  	 $stotal=number_format((float)$stotal, 3, '.', '');	
	   			  
			$msg=__('Added Success');
				return response()->json(['success' =>$msg,'arrTaping'=>$arrTaping,'stotal'=>$stotal,'totalf'=>$totalf,'balance'=>$balance]);	
  
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

function SaveCommandSales($lang,Request $request){
$price=0.00;
$stotal=0.00;
$totalf=0.00;
$tdiscount=0.00;
$gst=0;
$qst=0;
$rtotal=0.00;


$someArray = [];
$someArray=json_decode($request->data,true); 
$result='';
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
$newstotal=floatval(preg_replace('/[^\d.-]/', '', $request->stotal));
$Nbalance=number_format((float)$newstotal-$request->tpay+$request->trefund, 3, '.', '');
$Nbalance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));
$typein=$request->typein;
if ($request->status=='M'){
$ReqRequest=TblInventoryInvoicesRequest::where('id',$request->invoice_id)->where('active','O')->first();
	 $sqlReq = "delete from tbl_inventory_invoices_details  where invoice_id=".$request->invoice_id;   			   
		 DB::select(DB::raw("$sqlReq"));
		$last_id=$request->invoice_id; 
		$atotalf=$request->stotal;
		
		$sqlRequpdate = "update tbl_inventory_invoices_request set 
						  fournisseur_id='".$request->selectpro."',
						  fournisseur_fid='".$request->subselectpro."',
						  type='".$request->selecttype."',
						   notes='".$request->invoice_rq."',
						   newitem='".$request->newitem."',
						  total='".$atotalf."',
						  typein='".$typein."',
						  inv_balance='".$Nbalance."',
						  clinic_num='".$request->clinic_id."',
     					  active='O' where id=". $last_id;
   
				DB::select(DB::raw("$sqlRequpdate"));		
				$FacInventory = TblInventoryItemsFournisseur::where('id',$request->selectpro)->first();
				$reqID=$request->reqID;
}else{
		if (isset($request->stotal)){
		$atotalf=$request->stotal;
		}else{
		$atotalf="0.00";	
		}
	   $date_due=now()->format('Y-m-d H:i');			
	   $sqlReq = "insert into tbl_inventory_invoices_request(fournisseur_id,fournisseur_fid,type,date_invoice,".
	              "date_due,notes,newitem,typein,total,inv_balance,clinic_num,user_id,active)".
                  "values('".$request->selectpro."','".
							$request->subselectpro."','".
							$request->selecttype."','".
							$request->invoice_date_val."','".
							$date_due."','".
							$request->invoice_rq."','".
							$request->newitem."','".
							$typein."','".
							$atotalf."','".
							$Nbalance."','".
							$request->clinic_id."','".
							$user_id."','O')";
		DB::select(DB::raw("$sqlReq"));
        $last_id=DB::getPdo()->lastInsertId(); 
	
		$FacInventory = TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->first();
      
			$SerieFacInventory = $FacInventory->cmd_serial_code;
			$SeqFacInventory = $FacInventory-> cmd_sequence_num ;
			$reqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
		    TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->update([
				                  'cmd_sequence_num' => $SeqFacInventory+1
								  ]);
		
		TblInventoryInvoicesRequest::where('id',$last_id)->update([
				                  'clinic_inv_num'=>$reqID	
								  ]);		
}
foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0 ){
    $code = $area["CODE"];
   $descrip = html_entity_decode(trim($area["DESCRIP"]));
   $quantity=$area["QTY"];
   $price = $area["PRICE"];
   $total = $area["TOTAL"];
   if ($request->status=='M'){

   $rqty=$area["RQTY"];  
   $rprice=$area["RPRICE"];
   }else{
    $rqty=$area["QTY"];  
   $rprice=$area["PRICE"];
   }
   $tpackage=$area["TPACKAGE"];
   if (isset($area["RFACTURE"]))
   {
   $rfacture=$area["RFACTURE"];  
   }else{
	$rfacture="";   
   }
   if (isset($area["RPAY"]))
   {
   $rpay=$area["RPAY"];  
   }else{
	$rpay="N";   
   }
    $sqlInsertF = "insert into tbl_inventory_invoices_details(invoice_id,item_code,item_name,tpackage,qty,rqty,price,rprice,total,cfacture,cpay,notes,status,user_id,active) values('".
                 $last_id."','".
				 $code."','".
				 $descrip."','".
				 $tpackage."','".
				 $quantity."','".
				 $rqty."','".
				 $price."','".
				 $rprice."','".
				 $total."','".
				 $rfacture."','".
				 $rpay."','".
				 $request->invoice_rq."','O','".
				 $user_id."','O')";
	DB::select(DB::raw("$sqlInsertF"));
	$last_id_details=DB::getPdo()->lastInsertId(); 
$stotal=$stotal+$total;
$rtotal=$rtotal+($rqty*$rprice);	
}
}
$sumpay="0.00";
$sumref="0.00";
$Nbalance="0.00";
$totalf="0.00";
$tdiscount="0.00";
//$stotal="0.00";

$sumpay=TblInventoryPayment::where('invoice_num','=',$last_id)->where('status','Y')->where('payment_type','=','P')->sum('payment_amount');
$sumref=TblInventoryPayment::where('invoice_num','=',$last_id)->where('status','Y')->where('payment_type','=','R')->sum('payment_amount');
$Nbalance = number_format((float)$rtotal,3,'.','');
$totalf=number_format($stotal,3,'.','');
$tdiscount=number_format($tdiscount,3,'.','');
$stotal=number_format($stotal,3,'.','');
$sqlRequpdate = "update tbl_inventory_invoices_request set 
						  total='".$totalf."',
						   inv_balance='".$Nbalance."',
						  user_id='".$user_id."',
						  active='O' where id=". $last_id;
				DB::select(DB::raw("$sqlRequpdate"));
if ($request->status=='M'){		
$sumqty=TblInventoryInvoicesDetails::where('invoice_id','=',$request->invoice_id)->where('status','O')->sum('qty');
$sumrqty=TblInventoryInvoicesDetails::where('invoice_id','=',$request->invoice_id)->where('status','O')->sum('rqty');
if ($sumqty==$sumrqty){
$sqlQtyupdate = "update tbl_inventory_invoices_request set cdone='Y' where id=". $request->invoice_id;
DB::select(DB::raw("$sqlQtyupdate"));			
}
}
$msg=__('Command Saved Success');
if ($request->status=='M'){		
	return response()->json(['success'=>$msg,'reqID'=>$reqID,'last_id'=>$last_id,	
							'balance'=>$Nbalance,'totalf'=>$totalf,
							'stotal'=>$stotal]);
}else{
  $location = route('sales.editcommandsales',[$lang,$last_id,$typein]);
  return response()->json(['success'=>$msg,'location'=>$location]);
}
}
	
function SaveInventory($lang,Request $request){
	//dd(json_decode($request->data,true));
if ($request->selecttype=="1" || $request->selecttype=="4"){	 
	  if($request->selectpro=="0" || $request->selectpro==NULL){
			 $msg=__('Please select your Supplier!');
			 return response()->json(['warning' =>$msg]);	

			
			 }
			}
			
if ($request->selecttype=='3' || $request->selecttype=='4'){
		$ref=substr($request->invoice_ref,0,strpos($request->invoice_ref,",")); 
		}else{
			$ref=$request->invoice_ref;
		}
		
//if ($request->selecttype=='99' || $request->selecttype=='3' || $request->selecttype=='4'){			
$price=0.00;
$stotal=0.00;
$totalf=0.00;
$tdiscount=0.00;
$gst=0;
$qst=0;
//}

$someArray = [];
$someArray=json_decode($request->data,true); 
//$i=1;
//dd(json_decode($request->data,true));
$result='';
$user_id = auth()->user()->id;
$user_type= auth()->user()->type;
//$qst=number_format((float)$request->valqst, 2, '.', ',');
//$gst=number_format((float)$request->valgst, 2, '.', ',');
$newstotal=floatval(preg_replace('/[^\d.-]/', '', $request->stotal));

$Nbalance=number_format((float)$newstotal-$request->tpay+$request->trefund, 3, '.', '');
$Nbalance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));
if ($request->cnote=='on'){
	$cnote="Y";
}else{
if ($request->garanty=='on'){	
	$cnote="G";
}else{
$cnote="N";
}
}
if ($request->status=='M'){
$ReqRequest=TblInventoryInvoicesRequest::where('id',$request->invoice_id)->where('active','O')->first();
//if ($request->selecttype=='2'){
//$Nbalance=floatval(preg_replace('/[^\d.]/', '', $Nbalance));
//}
 if ($request->selecttype=='99'){
		$reqID=$request->reqID;
		 //$sqlReq = "update tbl_inventory_invoices_details set status='N' where invoice_id=".$request->invoice_id;
		$sqlReq = "delete from tbl_inventory_invoices_details where invoice_id=".$request->invoice_id;
		 DB::select(DB::raw("$sqlReq"));
		// $sqlReq = "update tbl_inventory_remise set status='N' where invoice_id=".$ReqRequest->reference;
         $sqlReq = "delete from tbl_inventory_remise where invoice_id=".$ReqRequest->reference;		 
		 DB::select(DB::raw("$sqlReq"));

		foreach ($someArray as $key=>$area)
		{
		 //key equal zero is the first row 
		 
		 if($key!=0){
		   $code = $area["CODE"];
		   $date = $request->invoice_date_val;
		   $name=html_entity_decode(trim($area["DESCRIP"]));
		   $qty = $area["QTY"];
			
		   $sqlInsertF = "insert into tbl_inventory_remise(datein,invoice_id,item_code,item_name,qty,patient_id,user_id,status,active) values('".
						 $date."','".
						 $ReqRequest->reference."','".
						 $code."','".
						 $name."','".
						 $qty."','".
						 $request->selectpatient."','".
						 $user_id."','Y','O')";
			DB::select(DB::raw("$sqlInsertF"));
		 
		 }	
		 }
 //$sqlReq = "update tbl_inventory_invoices_details set status='N' where invoice_id=".$request->invoice_id;   			   
 $sqlReq = "delete from tbl_inventory_invoices_details  where invoice_id=".$request->invoice_id;   			   

 DB::select(DB::raw("$sqlReq"));		
 
 }else{
		// to add or minus qty and deleted from table details before add the item 
		 $InvoiceDetails= TblInventoryInvoicesDetails::where('invoice_id',$request->invoice_id)->where('status','O')->get();
		 foreach($InvoiceDetails as $IDetails){ 
		 $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
		 if ($ReqRequest->gstock=='Y'){
		 $QtyItemD = $SQtyD->gqty;	
		 }else{
		 $QtyItemD = $SQtyD->qty;	 
		 }
		 if ($request->selecttype=='1' || $request->selecttype=='3'){
		 $qtyD=$QtyItemD-$IDetails->qty;
		 }else{
			// return fournisseur and garanty
			if($request->garanty=='on'){
				$qtyD=$QtyItemD;
			}else{	
				$qtyD=$QtyItemD+$IDetails->qty;
			}
		 }
		 if ($ReqRequest->gstock=='Y'){
		 $DeleteQty="update tbl_inventory_items set gqty=".$qtyD." where id='".$IDetails->item_code."'";
		 }else{
		 $DeleteQty="update tbl_inventory_items set qty=".$qtyD." where id='".$IDetails->item_code."'";
		 }
			 DB::select(DB::raw("$DeleteQty"));
			}
		
		// $sqlReq = "update tbl_inventory_invoices_details set status='N' where invoice_id=".$request->invoice_id;   			   
		 $sqlReq = "delete from tbl_inventory_invoices_details  where invoice_id=".$request->invoice_id;   			   
		 DB::select(DB::raw("$sqlReq"));
		 
		// delete in table return 
		//if ($request->selecttype=='3' || $request->selecttype=='4'){			
		//$sqlReqR = "delete from tbl_inventory_invoices_details_return  where invoice_id=".$request->invoice_id;   			   
		// DB::select(DB::raw("$sqlReqR"));
		//}
		//end delete in return 
 		//if(!isset($request->selectpatient)){$patient_id=0;}
		$last_id=$request->invoice_id; 
		$atotalf=$request->totalf;
		$atdiscount=$request->tdiscount;
		if($request->garanty=='on'){
		$atotalf=0.00;
		$atdiscount=0.00;
		$qst=0.00;
		$gst=0.00;
		$Nbalance=0.00;
		//patient_id='".$request->selectpatient."',

		}
		$sqlRequpdate = "update tbl_inventory_invoices_request set 
						  fournisseur_id='".$request->selectpro."',
						  type='".$request->selecttype."',
						  date_invoice='".$request->invoice_date_val."',
						  date_due='".$request->invoice_date_due."',
						  reference='".$ref."',
						  qst='".$qst."',
						  gst='".$gst."',
						  notes='".$request->invoice_rq."',
						  total='".$atotalf."',
						  discount='".$atdiscount."',
						  inv_balance='".$Nbalance."',
						  clinic_num='".$request->clinic_id."',
						  user_id='".$user_id."',
						  active='O' where id=". $last_id;
							   
				DB::select(DB::raw("$sqlRequpdate"));		
				$FacInventory = TblInventoryItemsFournisseur::where('id',$request->selectpro)->first();
				$reqID=$request->reqID;
 }
}else{
       //New Invoice	   
	    $atotalf=$request->totalf;
		$atdiscount=$request->tdiscount;
		if($request->garanty=='on'){
		$atotalf=0.00;
		$atdiscount=0.00;
		$qst=0.00;
		$gst=0.00;
		$Nbalance=0.00;
		}
		if (isset($request->fromstock)|| (($request->selecttype=='2') and ($request->fromstock==''))){
			$fromstock=$request->fromstock; 
		}else{
			$fromstock='N';
		}
	   $sqlReq = "insert into tbl_inventory_invoices_request(fournisseur_id,patient_id,type,date_invoice,".
	              "date_due,reference,qst,gst,notes,total,discount,inv_balance,clinic_num,free,cr_note,invoice_sup,gstock,user_id,active)".
                  "values('".$request->selectpro."','".
				            $request->selectpatient."','".
							$request->selecttype."','".
							$request->invoice_date_val."','".
							$request->invoice_date_due."','".
							$ref."','".
							$qst."','".
							$gst."','".
							$request->invoice_rq."','".
							$atotalf."','".
							$atdiscount."','".
							$Nbalance."','".
							$request->clinic_id."','".
							$request->is_free."','".
							$cnote."','".
							$request->invoice_sup."','".
							$fromstock."','".
							$user_id."','O')";
  	     
		DB::select(DB::raw("$sqlReq"));
        $last_id=DB::getPdo()->lastInsertId(); 
		//$reqID=$last_id;
		
	//	$FacInventory = Clinic::where('id',$request->clinic_id)->first();
     // 	$SerieFacInventory = $FacInventory->inv_serial_code;
	//	$SeqFacInventory = $FacInventory-> inv_sequence_num ;
	//	$reqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
	//	Clinic::where('id',$request->clinic_id)->update([
	//			                  'inv_sequence_num' => $SeqFacInventory+1
	//							  ]);
		$FacInventory = TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->first();
      	switch($request->selecttype){
		case "99":
			$SerieFacInventory = $FacInventory->rm_serial_code;
			$SeqFacInventory = $FacInventory-> rm_sequence_num ;
			$reqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
		    TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->update([
				                  'rm_sequence_num' => $SeqFacInventory+1
								  ]);
		break;						  
		case "1":
		    $SerieFacInventory = $FacInventory->p_serial_code;
			$SeqFacInventory = $FacInventory-> p_sequence_num ;
			$reqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
		    TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->update([
				                  'p_sequence_num' => $SeqFacInventory+1
								  ]);
			break;	
		case "2":
		    $SerieFacInventory = $FacInventory->s_serial_code;
			$SeqFacInventory = $FacInventory-> s_sequence_num ;	
			$reqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
		    TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->update([
				                  's_sequence_num' => $SeqFacInventory+1
								  ]);
		break;
		case "3":
		if ($request->cnote=='on'){
			$SerieFacInventory = $FacInventory->cn_serial_code;
			$SeqFacInventory = $FacInventory-> cn_sequence_num ;	
			$reqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
		    TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->update([
				                  'cn_sequence_num' => $SeqFacInventory+1
								  ]);
			}else{
		    $SerieFacInventory = $FacInventory->rs_serial_code;
			$SeqFacInventory = $FacInventory-> rs_sequence_num ;	
			$reqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
		    TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->update([
				                  'rs_sequence_num' => $SeqFacInventory+1
								  ]);
			}
		break;
		case "4":
			if ($request->cnote=='on'){
			 $SerieFacInventory = $FacInventory->cns_serial_code;
			$SeqFacInventory = $FacInventory-> cns_sequence_num ;	
			$reqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
		    TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->update([
				                  'cns_sequence_num' => $SeqFacInventory+1
								  ]);	
			}else{	
		   $SerieFacInventory = $FacInventory->rp_serial_code;
			$SeqFacInventory = $FacInventory-> rp_sequence_num ;	
			$reqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
		   TblInventoryInvSerials::where('clinic_num',$request->clinic_id)->update([
			                  'rp_sequence_num' => $SeqFacInventory+1
								  ]);
			}
		break;								
		}			
		
		
		TblInventoryInvoicesRequest::where('id',$last_id)->update([
				                  'clinic_inv_num'=>$reqID	
								  ]);	
		
		
		
      //  $FacInvoice = TblInventoryItemsFournisseur::where('id',$request->selectpro)->first();
      //	$SeqFacInvoice = $FacInvoice->item_seq ;
		//$reqID=$SeqFacInvoice+1;
		//TblInventoryItemsFournisseur::where('id',$request->selectpro)->update([
			//	                  'item_seq' => $SeqFacInvoice+1
			//					  ]);
	//	TblInventoryInvoicesRequest::where('id',$last_id)->update([
	//			                  'id'=>$reqID	
		//						  ]);	
	}					  
//fac_bill_id to update reqID
  $STypes = TblInventoryTypes::where('id',$request->selecttype)->first();
  $SignTypes = $STypes->sign ;
 


foreach ($someArray as $key=>$area)
{
 //key equal zero is the first row 
 
 if($key!=0 || $request->selecttype=='3' || $request->selecttype=='4'){
   if ($request->selecttype=='99'){
   $last_id=$request->invoice_id;	 
   $code = $area["CODE"];
   $descrip = html_entity_decode(trim($area["DESCRIP"]));
   $quantity=$area["QTY"];
   $price = "0.00";
   $discount = "0.00";
   $initprice = "0.00";
   $sel_price = "0.00";
   $total = "0.00";
   $date_exp = $request->invoice_date_val;
   $taxable="N";
   $typediscount="0";
   $ref_invoice=$request->invoice_ref;
   //$taxable=($taxable0=='on')?'Y':'N';
   //no need for this
  // $result.= $code.",".$descrip.",".$quantity.",".$price.",".$discount.",".$total.PHP_EOL;
  $sqlInsertF = "insert into tbl_inventory_invoices_details(invoice_id,ref_invoice,item_code,item_name,qty,price,discount,
															total,date_exp,tdiscount,tax,notes,
															status,user_id,active) values('".
                 $last_id."','".
				 $ref_invoice."','".
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
   
   
   
	}else{
   //other invoices: 1:Purchase,2:Sale,3:Return Patient,4:Return Supplier		
   $code = $area["CODE"];
   $descrip = html_entity_decode(trim($area["DESCRIP"]));
   $quantity=$area["QTY"];
   $price = $area["PRICE"];
   $discount = $area["DISCOUNT"];
   $total = $area["TOTAL"];
   $date_exp = $area["EXPIRY_DATE"];
   $taxable=$area["TAXABLE"];
   $typediscount=trim($area["TYPEDISCOUNT"]);
   $formulaid=$area["FORMULAID"];  
   $sel_price=$area["SELPRICE"];
   $initprice=$area["INITPRICE"];
   // remove from hassan : error in modfiy purchase (tps,rqty,)
	//if (isset($area["TVQ"])){
	//	$tvq=$area["TVQ"];
	//}else{
		$tvq=0.00;
	//}
	//if (isset($area["TPS"])){
	//	$tps=$area["TPS"];
//	}else{
		$tps=0.00;
//	}
   if (isset($area["ID_DETAILS"])){
   $id_details=$area["ID_DETAILS"];
   }else{
	   $id_details=0;
   }
//    if (isset($area["RQTY"])){
//	$rqty=$area["RQTY"];
//	}else{
		$rqty=0;
//	}
//	if (isset($area["RLID"])){
//	$rlid=$area["RLID"];
//	}else{
		$rlid=0;
//	}
   //$taxable=($taxable0=='on')?'Y':'N';
   //no need for this
  // $result.= $code.",".$descrip.",".$quantity.",".$price.",".$discount.",".$total.PHP_EOL;
  $sqlInsertF = "insert into tbl_inventory_invoices_details(invoice_id,ref_invoice,item_code,item_name,qty,rqty,rlid,price,discount,total,date_exp,tdiscount,tax,formula_id,qst,gst,initprice,sel_price,notes,status,user_id,active) values('".
                 $last_id."','".
				 $ref."','".
				 $code."','".
				 $descrip."','".
				 $quantity."','".
				 $rqty."','".
				 $rlid."','".
				 $price."','".
				 $discount."','".
				 $total."','".
				 $date_exp."','".
				 $typediscount."','".
				 $taxable."','".
				 $formulaid."','".
				 $tvq."','".
				 $tps."','".
				 $initprice."','".
				 $sel_price."','".				 
				 $request->invoice_rq."','O','".
				 $user_id."','O')";
	DB::select(DB::raw("$sqlInsertF"));
	$last_id_details=DB::getPdo()->lastInsertId(); 
	//
	//if($request->selecttype=='3' || $request->selecttype=='4'){
	// $sqlInsertFR = "insert into tbl_inventory_invoices_details_return(invoice_id,ref_invoice,item_code,item_name,qty,price,discount,total,date_exp,tdiscount,tax,formula_id,initprice,sel_price,notes,status,user_id,active) values('".
  //               $last_id."','".
	//			 $ref."','".
	//			 $code."','".
	//			 $descrip."','".
	//			 $quantity."','".
	//			 $price."','".
	//			 $discount."','".
	//			 $total."','".
	//			 $date_exp."','".
	//			 $typediscount."','".
	//			 $taxable."','".
	//			 $formulaid."','".
	//			 $initprice."','".
	//			 $sel_price."','".				 
	//			 $request->invoice_rq."','O','".
	//			 $user_id."','O')";
	//DB::select(DB::raw("$sqlInsertFR"));
	//}
	// end insert in table return 
  if (($request->selecttype=='1') && ($price>=0) ){
  $sqlUpdate="update tbl_inventory_items set initprice=".$initprice.",sel_price=".$sel_price.",cost_price=".$price.",formula_id='".$formulaid."' where id='".$code."'";
   DB::select(DB::raw("$sqlUpdate"));
  }   

	
 $tdiscount=$tdiscount+$discount;
 $t=$price*$quantity-$discount;
 $totalf=$totalf+$t;
 // $this->tdiscount=$this->tdiscount+$this->arrTaping[$this->cpt]["discount"];
 //$this->totalf=$this->totalf+($this->arrTaping[$this->cpt]["price"]*$this->arrTaping[$this->cpt]["qty"])-$this->arrTaping[$this->cpt]["discount"];
  	// alert($this->selectTax->getValue());
	
	 if ($taxable=='Y'){
			// if ($request->selecttax!="N"){
				  
		 $tax=TblBillRate::where('id',$request->selecttax)->first();
		 //if($descrip0->taxable=='Y'){
				$taxqst=$tax->tvq;
				$taxgst= $tax->tvs; 
				$qst=$qst+($t*$taxqst)/100;
				$gst=$gst+($t*$taxgst)/100;
				$qst=number_format((float)$qst, 3, '.', '');
				$gst=number_format((float)$gst, 3, '.', '');
		 }else{
			$taxqst=0;
			$taxgst=0;
			$qst=$qst+($t*$taxqst)/100;
			$gst=$gst+($t*$taxgst)/100;
			$qst=number_format((float)$qst, 3, '.', '');
			$gst=number_format((float)$gst, 3, '.', '');			
		 }		
	
$stotal=$totalf+$qst+$gst;
//$Nbalance=$stotal;

if ($request->selecttype=='3' || $request->selecttype=='4'){
if($request->garanty=='on'){
		$stotal=0.00;
		$totalf=0.00;
		$qst=0.00;
		$gst=0.00;
		$quantity=0;
		$Nbalance=0.00;
		$tdiscount=0.00;
		}	
$sqlRqtyUpdate="update tbl_inventory_invoices_details set rlid='".$last_id_details."',rqty=rqty+".$quantity." where id='".$id_details."'";
   DB::select(DB::raw("$sqlRqtyUpdate"));

}	

 $SQty = TblInventoryItems::where('id',$code)->first();
 if (isset($request->fromstock)){
			$fromstock=$request->fromstock; 
		}else{
			if($request->selecttype=='5'){
			$fromstock=$ReqRequest->gstock;	
			}else{
			$fromstock='N';
			}
		}
		if($request->selecttype=='2'){
			 if ($fromstock==''){			
					 $QtyItem = $SQty->qty;	 
					 $qty0=$QtyItem-$quantity;
					 $qty=strval($qty0);
					 $UpdateQty="update tbl_inventory_items set qty=".$qty." where id='".$code."'";   
					 } 
			 if ($fromstock=='Y'){			
					 $QtyItem = $SQty->qty;
					 $QtyItemG = $SQty->gqty;					 
					 $qty0G=$QtyItemG-$quantity;
					 $qty0=$QtyItem+$quantity;
					 $qty=strval($qty0);
					 $qtyG=strval($qty0G);
					 $UpdateQty="update tbl_inventory_items set qty=".$qty.",gqty=".$qtyG." where id='".$code."'";   
					 } 
			if ($fromstock=='N'){			
					 $QtyItem = $SQty->qty;
					 $QtyItemG = $SQty->gqty;					 
					 $qty0G=$QtyItemG+$quantity;
					 $qty0=$QtyItem-$quantity;
					 $qty=strval($qty0);
					 $qtyG=strval($qty0G);
					 $UpdateQty="update tbl_inventory_items set qty=".$qty.",gqty=".$qtyG." where id='".$code."'";   
					 } 	  
			 
				DB::select(DB::raw("$UpdateQty"));
		}else{
		 if ($fromstock=='Y'){
					 $QtyItem = $SQty->gqty;
					 }else{
					 $QtyItem = $SQty->qty;	 
					 } 

			 if ($SignTypes=='+'){
				   $qty0=$QtyItem+$quantity;
				}else{
				  $qty0=$QtyItem-$quantity;
				}
			  
			  $qty=strval($qty0);
			   if ($fromstock=='Y'){
				$UpdateQty="update tbl_inventory_items set gqty=".$qty." where id='".$code."'";
			   }else{
				$UpdateQty="update tbl_inventory_items set qty=".$qty." where id='".$code."'";   
			   }
				DB::select(DB::raw("$UpdateQty"));	
			
		}
 
 }	

 }
 }//end details in new invoice
 
if($request->selecttype !='99'){
$sumpay=TblInventoryPayment::where('invoice_num','=',$last_id)->where('status','Y')->where('payment_type','=','P')->sum('payment_amount');
$sumref=TblInventoryPayment::where('invoice_num','=',$last_id)->where('status','Y')->where('payment_type','=','R')->sum('payment_amount');
$Nbalance = number_format((float)$stotal-$sumpay+$sumref,3,'.','');
$totalf=number_format($totalf,3,'.','');
$tdiscount=number_format($tdiscount,3,'.','');
$stotal=number_format($stotal,3,'.','');

$sqlRequpdate = "update tbl_inventory_invoices_request set 
						  qst='".$qst."',
						  gst='".$gst."',
						  total='".$totalf."',
						  discount='".$tdiscount."',
						   inv_balance='".$Nbalance."',
						  user_id='".$user_id."',
						  active='O' where id=". $last_id;
				DB::select(DB::raw("$sqlRequpdate"));		
}					   

switch($request->selecttype){
case "99":
 $msg=__('Warranty Saved Success');
 $tdiscount=0.00;
 $totalf=0.00;
 $qst=0.00;
 $gst=0.00;
 $stotal=0; 
	return response()->json(['success'=>$msg,'reqID'=>$reqID,'balance'=>$Nbalance,'tdiscount'=>$tdiscount,
	'totalf'=>$totalf,'qst'=>$qst,'gst'=>$gst,'stotal'=>$stotal]);
break;
case "3":
$msg=__('Inventory Saved Success');
if ($request->status=='M'){		
	return response()->json(['success'=>$msg,'reqID'=>$reqID,'last_id'=>$last_id,	
							'balance'=>$Nbalance,'tdiscount'=>$tdiscount,'totalf'=>$totalf,'qst'=>$qst,'gst'=>$gst,
							'stotal'=>$stotal]);
}else{
  $location = route('inventory.invoices.EditRSales',[$lang,$last_id]);
  return response()->json(['success'=>$msg,'location'=>$location]);
}
break;
case "4":
$msg=__('Inventory Saved Success');
if ($request->status=='M'){	
	return response()->json(['success'=>$msg,'reqID'=>$reqID,'last_id'=>$last_id,	
							'balance'=>$Nbalance,'tdiscount'=>$tdiscount,'totalf'=>$totalf,'qst'=>$qst,'gst'=>$gst,
							'stotal'=>$stotal]);
}else{
  $location = route('inventory.invoices.EditRinvoices',[$lang,$last_id]);
  return response()->json(['success'=>$msg,'location'=>$location]);
}				
break;
case "2":
$msg=__('Inventory Saved Success');
if ($request->status=='M'){
	return response()->json(['success'=>$msg,'reqID'=>$reqID,'last_id'=>$last_id,	
							'balance'=>$Nbalance,'tdiscount'=>$tdiscount,'totalf'=>$totalf,'qst'=>$qst,'gst'=>$gst,
							'stotal'=>$stotal]);
}else{
  $location = route('inventory.invoices.editsales',[$lang,$last_id]);
  return response()->json(['success'=>$msg,'location'=>$location]);
}
break;
case "1":
$msg=__('Inventory Saved Success');
if ($request->status=='M'){
	return response()->json(['success'=>$msg,'reqID'=>$reqID,'last_id'=>$last_id,	
							'balance'=>$Nbalance,'tdiscount'=>$tdiscount,'totalf'=>$totalf,'qst'=>$qst,'gst'=>$gst,
							'stotal'=>$stotal]);
}else{
  $location = route('inventory.invoices.edit',[$lang,$last_id]);
  return response()->json(['success'=>$msg,'location'=>$location]);
}
break;
case "5":
$msg=__('Inventory Saved Success');
	return response()->json(['success'=>$msg,'reqID'=>$reqID,'last_id'=>$last_id,
							'balance'=>$Nbalance,'tdiscount'=>$tdiscount,'totalf'=>$totalf,'qst'=>$qst,'gst'=>$gst,
							'stotal'=>$stotal]);
break;
default:
 $msg=__('Inventory Saved Success');
	return response()->json(['success'=>$msg,'reqID'=>$reqID,'last_id'=>$last_id,
							'balance'=>$Nbalance]);
	}
   

		  
}  

public function sendPDFInvoice($lang,Request $request){

}


public function salesdownloadPDFInvoice($lang,Request $request){
            
		if($request->has('print_type')){	
		  	switch($request->print_type){
				case 'All':
                $response=$this->salesgeneratePDFInvoice($request->id,$request->desc);				
				break;
				case 'Payment':
				$response=$this->generatePDFInvoicePayment($request->id,$request->desc);
				break;
			}
		
		 }else{
			//Purchase,Return supplier cases always with description
			$response=$this->salesgeneratePDFInvoice($request->id,'O'); 
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
    public function salessendgeneratePDFInvoice($id,$desc)
    
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
			 $ref_invoice = TblInventoryInvoicesRequest::where('clinic_inv_num',$Invoice->clinic_inv_num)->first();
				//  dd($ref_invoice);
				  $supplier=TblInventoryClients::where('active','O')->where('id',$Invoice->fournisseur_id)->first();
				  $clinic_ref=Clinic::where('active','O')->where('id',$ref_invoice->clinic_num)->first();
				  $serial_ref = TblInventoryInvSerials::where('clinic_num',$clinic_ref->id)->first();       
				  $result_ref=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$ref_invoice->id)->get();
				  $logo_ref=DB::table('tbl_bill_logo')->where('clinic_num',$ref_invoice->clinic_num)->where('status','O')->first();
				 
				  $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $Invoice,'clinic' => $clinic,
                          'supplier' =>$supplier,'result' => $result,'ref_invoice'=>$ref_invoice]; 
    
				$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.commandPDF', $data);
				$pdf->output();
				$dom_pdf = $pdf->getDomPDF();
				$canvas = $dom_pdf->get_canvas();
				$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));         
			      
			return $pdf->output();
		}
   public function salesgeneratePDFInvoice($id,$desc)
    
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
				  $supplier=TblInventoryClients::where('active','O')->where('id',$Invoice->fournisseur_id)->first();
				  
			
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
                     $supplier_ri= TblInventoryClients::where('active','O')->where('id',$ri->fournisseur_id)->first();
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
					$supplier1 = TblInventoryClients::find($Invoice->fournisseur_id);
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
				  $supplier=TblInventoryClients::where('active','O')->where('id',$Invoice->fournisseur_id)->first();
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
				case 6:
				  $ref_invoice = TblInventoryInvoicesRequest::where('active','O')->where('clinic_inv_num',$id)->first();
				  $supplier=TblInventoryClients::find($ref_invoice->fournisseur_id);
				  $clinic_ref=Clinic::where('active','O')->find($ref_invoice->clinic_num);
				  $serial_ref = TblInventoryInvSerials::find($clinic_ref->id);       
				  $result_ref=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$ref_invoice->id)->get();
				  $logo_ref=DB::table('tbl_bill_logo')->where('clinic_num',$ref_invoice->clinic_num)->where('status','O')->first();
				  
				  $data = ['title' => __('Invoice'),'date' => date('m/d/Y'),'logo'=>$logo,'Invoice' => $Invoice,'clinic' => $clinic,
                          'supplier' =>$supplier,'result' => $result,'ref_invoice'=>$ref_invoice]; 
    
				$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.commandPDF', $data);
				$pdf->output();
				$dom_pdf = $pdf->getDomPDF();
				$canvas = $dom_pdf->get_canvas();
				$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));         
			    
				return  $pdf->stream();
				break;
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

function deleteInventory($lang,Request $request){

		
$user_id = auth()->user()->id;

$invoice_id=$request->id;

$state = $request->state;

switch($state){
	case 'activate':
	$type = TblInventoryInvoicesRequest::join('tbl_inventory_types as t','t.id','tbl_inventory_invoices_request.type')
                                    ->where('t.active','O')
									->where('tbl_inventory_invoices_request.id',$invoice_id)->pluck('t.name')[0];

    $type= str_replace(' ','',$type);

	TblInventoryInvoicesRequest::where('id',$invoice_id)->update([
									   'active' => 'O',
									   'user_id' => $user_id 
									]);
	
		// to change the qty 	
		$selecttype = TblInventoryInvoicesRequest::where('id',$invoice_id)->first();
		$gstock=$selecttype->gstock;
	    $InvoiceDetails= TblInventoryInvoicesDetails::where('invoice_id',$invoice_id)->where('status','O')->get();
		
		switch($selecttype->type){
			case '1': 
			 foreach($InvoiceDetails as $IDetails){ 
		       $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
		       $QtyItemD = $SQtyD->qty;
			   $qtyD=$QtyItemD+$IDetails->qty;
			   $rqty = isset($IDetails->rqty)?$IDetails->rqty:0;
		       TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD-$rqty]);
		     }
			break;
			case '2':
			foreach($InvoiceDetails as $IDetails){ 
		       $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
			   if ($gstock=='Y'){
		       $QtyItemD = $SQtyD->gqty;
			   }else{
				$QtyItemD = $SQtyD->qty;   
			   }
			   $qtyD=$QtyItemD-$IDetails->qty;
			   $qtyD1=$QtyItemD+$IDetails->qty;
			    if ($gstock=='Y'){
		        TblInventoryItems::where('id',$IDetails->item_code)->update(['gqty'=>$qtyD]);
			    TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD1]);
				}else{
				if($gstock=='N'){
				TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD]);
				TblInventoryItems::where('id',$IDetails->item_code)->update(['gqty'=>$qtyD1]);	
				}else{			
				TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD]);			
				}
				}
		     }
			break;
			case '3':
			foreach($InvoiceDetails as $IDetails){ 
		       $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
		       $QtyItemD = $SQtyD->qty;
			   $qtyD=$QtyItemD+$IDetails->qty;
		       TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD]);
		     }
			break;
			case '4':
			if($selecttype->cr_note<>'G')
		{
			foreach($InvoiceDetails as $IDetails){ 
		       $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
		       $QtyItemD = $SQtyD->qty;
			   $qtyD=$QtyItemD-$IDetails->qty;
		       TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD]);
		     }
		} 
			break;
			case '5':
			foreach($InvoiceDetails as $IDetails){ 
		       $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
			   if ($gstock=='Y'){
		       $QtyItemD = $SQtyD->gqty;
			   }else{
				$QtyItemD = $SQtyD->qty;   
			   }
			   if ($selecttype->typeadjacement=="1"){
				   $qtyD=$QtyItemD+$IDetails->qty;
			   }else{   
			   $qtyD=$QtyItemD-$IDetails->qty;
			   }
			    if ($gstock=='Y'){
		       TblInventoryItems::where('id',$IDetails->item_code)->update(['gqty'=>$qtyD]);
				}else{
				TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD]);	
				}
		     }
			
		   }
		
		 /*foreach($InvoiceDetails as $IDetails){ 
		 $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
		 $QtyItemD = $SQtyD->qty;
		 
		 if ($selecttype->type=='1'){
		 $qtyD=$QtyItemD+$IDetails->qty;
         $DeleteQty="update tbl_inventory_items set qty=".$qtyD." where id='".$IDetails->item_code."'";
		 DB::select(DB::raw("$DeleteQty"));			 }
		 if ($selecttype->type=='2'){
		 $qtyD=$QtyItemD-$IDetails->qty;
         $DeleteQty="update tbl_inventory_items set qty=".$qtyD." where id='".$IDetails->item_code."'";
		 DB::select(DB::raw("$DeleteQty"));		 
		 }
		 
			}*/
		// end change qty 
									
    $msg=__('Activated successfully');
	break;
	case 'inactivate':
	 $type = TblInventoryInvoicesRequest::join('tbl_inventory_types as t','t.id','tbl_inventory_invoices_request.type')
                                    ->where('t.active','O')
									->where('tbl_inventory_invoices_request.id',$invoice_id)->pluck('t.name')[0];

     $type= str_replace(' ','',$type);

	TblInventoryInvoicesRequest::where('id',$invoice_id)->update([
									   'active' => 'N',
									   'user_id' => $user_id 
									]);
	// to change the qty 	
		$selecttype = TblInventoryInvoicesRequest::where('id',$invoice_id)->first();
		$gstock=$selecttype->gstock;
	    $InvoiceDetails= TblInventoryInvoicesDetails::where('invoice_id',$invoice_id)->where('status','O')->get();
		switch($selecttype->type){
			case '1': 
			 foreach($InvoiceDetails as $IDetails){ 
		       $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
		       $QtyItemD = $SQtyD->qty;
			   $qtyD=$QtyItemD-$IDetails->qty;
		       TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD]);
		     }
			break;
			case '2':
			foreach($InvoiceDetails as $IDetails){ 
		       $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
		        if ($gstock=='Y'){
		       $QtyItemD = $SQtyD->gqty;
			   }else{
				$QtyItemD = $SQtyD->qty;   
			   }
			   $qtyD=$QtyItemD+$IDetails->qty;
			   $qtyD1=$QtyItemD-$IDetails->qty;
			   
			    if ($gstock=='Y'){
		        TblInventoryItems::where('id',$IDetails->item_code)->update(['gqty'=>$qtyD]);
			    TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD1]);
				}else{
				if($gstock=='N'){
				TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD]);
				TblInventoryItems::where('id',$IDetails->item_code)->update(['gqty'=>$qtyD1]);	
				}else{			
				TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD]);			
				}
				}
		     }
			break;
		case '3':
			foreach($InvoiceDetails as $IDetails){ 
		       $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
		       $QtyItemD = $SQtyD->qty;
			   $qtyD=$QtyItemD-$IDetails->qty;
		       TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD]);
			
			
			  // $InvID=TblInventoryInvoicesRequest::where('clinic_inv_num',$IDetails->ref_invoice)->first();
			  // $InvIDetails=TblInventoryInvoicesDetails::where('invoice_id',$InvID->id)->where('item_code',$IDetails->item_code)->first();
			  // $rqty=$InvIDetails->rqty;
	   		    $InvIDetails=TblInventoryInvoicesDetails::where('rlid',$IDetails->id)->first();
    		    $rqty=$InvIDetails->rqty;
				$rqtyD=$rqty-$IDetails->qty;
			   //TblInventoryInvoicesDetails::where('invoice_id',$InvID->id)->where('item_code',$InvIDetails->item_code)->update(['rqty'=>$rqtyD]);
				TblInventoryInvoicesDetails::where('rlid',$IDetails->id)->update(['rqty'=>$rqtyD]);

		     }
			break;
		case '4':
		if($selecttype->cr_note<>'G')
		{
			foreach($InvoiceDetails as $IDetails){ 
		       $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
		       $QtyItemD = $SQtyD->qty;
			   $qtyD=$QtyItemD+$IDetails->qty;
		       TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD]);
			  
			//   $InvID=TblInventoryInvoicesRequest::where('clinic_inv_num',$IDetails->ref_invoice)->first();
			 //  $InvIDetails=TblInventoryInvoicesDetails::where('invoice_id',$InvID->id)->where('item_code',$IDetails->item_code)->first();
			 //  $rqty=$InvIDetails->rqty;
			 //  $rqtyD=$rqty-$IDetails->qty;
			  // TblInventoryInvoicesDetails::where('invoice_id',$InvID->id)->where('item_code',$InvIDetails->item_code)->update(['rqty'=>$rqtyD]);
				$InvIDetails=TblInventoryInvoicesDetails::where('rlid',$IDetails->id)->first();
    		    $rqty=$InvIDetails->rqty;
				$rqtyD=$rqty-$IDetails->qty;
				TblInventoryInvoicesDetails::where('rlid',$IDetails->id)->update(['rqty'=>$rqtyD]);

		     }
		}
			break;	
		case '5':
			foreach($InvoiceDetails as $IDetails){ 
		       $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
 			   if ($gstock=='Y'){
		       $QtyItemD = $SQtyD->gqty;
			   }else{
				$QtyItemD = $SQtyD->qty;   
			   }
			   if ($selecttype->typeadjacement=="1"){
				   $qtyD=$QtyItemD-$IDetails->qty;
			   }else{   
			   $qtyD=$QtyItemD+$IDetails->qty;
			   }
			   if ($gstock=='Y'){
		       TblInventoryItems::where('id',$IDetails->item_code)->update(['gqty'=>$qtyD]);
			   }else{
				TblInventoryItems::where('id',$IDetails->item_code)->update(['qty'=>$qtyD]);   
			   }
		     }
			break;
			
			
		}
		
		/*foreach($InvoiceDetails as $IDetails){ 
		 $SQtyD = TblInventoryItems::where('id',$IDetails->item_code)->first();
		 $QtyItemD = $SQtyD->qty;
		 
		 if ($selecttype->type=='1'){
		 $qtyD=$QtyItemD-$IDetails->qty;
		 $DeleteQty="update tbl_inventory_items set qty=".$qtyD." where id='".$IDetails->item_code."'";
		 DB::select(DB::raw("$DeleteQty"));
		 }
		 if ($selecttype->type=='2'){
		 $qtyD=$QtyItemD+$IDetails->qty;
		 $DeleteQty="update tbl_inventory_items set qty=".$qtyD." where id='".$IDetails->item_code."'";
		 DB::select(DB::raw("$DeleteQty"));
		 }
		
			}*/
			
		// end change qty 
										
    $msg=__('InActivated successfully');
	break;
}

	return response()->json(['success'=>$msg,'type'=>$type]);

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
 
function SavePayCommand($lang,Request $request){

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

//$Nbalance=number_format((float)$ReqPatient->total-$sumpay+$sumref+$ReqPatient->qst+$ReqPatient->gst, 3, '.', '');
//$balance=floatval(preg_replace('/[^\d.-]/', '', $Nbalance));

//TblInventoryInvoicesRequest::where('id',$invoice_id)->update([
			               //   'inv_balance'=>$balance
						//	  ]);	
$msg=__('Payment Saved Success');
	return response()->json(['success'=>$msg,'sumpay'=>$sumpay,'sumref'=>$sumref]);

		  
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
  $sQLReqInvNb = "SELECT distinct(a.id) as id, a.clinic_inv_num,a.date_invoice,a.reference 
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
    $html .= '<option value="' . $sReqInvNb->id.'" >'.$sReqInvNb->clinic_inv_num.','.__("Date").':'.$sReqInvNb->date_invoice.','.__("Invoice").':'.$sReqInvNb->reference.'</option>';
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
	  $invoice_sup=$ReqPatient->reference;
return response()->json(['invoice_sup' => $invoice_sup,'html1' => $html1,'pay'=>$pay,'refund'=>$refund,
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

public function salessend_supplier_email($lang, Request $request)
{
    $id = $request->id;

    // Validate if the invoice ID is provided
    if (!$id) {
        return response()->json(['error' => __('Invalid invoice ID')]);
    }

    // Retrieve the invoice
    $invoice = TblInventoryInvoicesRequest::find($id);
    if (!$invoice) {
        return response()->json(['error' => __('Invoice not found')]);
    }

    // Retrieve the supplier's email
    $supplier = TblInventoryClients::find($invoice->fournisseur_id);
    //$emails = [$supplier->email]; // Assuming this is an array of emails
    $emailString = $supplier->email; // Assuming this is a comma-separated string of emails

    // Convert the comma-separated email string to an array
    $emails = explode(',', $emailString);

    // Trim any extra whitespace from each email
    $emails = array_map('trim', $emails);
   $ccEmails = ['hascheaito@hotmail.com']; // Add your CC emails here
   // Define BCC emails
    $bccEmails = ['hascheaito@gmail.com'];
   // $ccEmailString = "hascheaito@gmail.com,racha_haydar@lab-alhadi.com"; // Assuming this is passed in the request
  //  $ccEmails = explode(',', $ccEmailString);
  //  $ccEmails = array_map('trim', $ccEmails);
    // Check if the supplier email is provided and valid
    foreach ($emails as $email) {
        if (is_null($email) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['error' => __('One or more supplier emails are not valid')]);
        }
    }

    // Retrieve branch information
    $branch = Clinic::find($invoice->clinic_num);

    // Generate the PDF to attach to the email
    $pdf = $this->salesgenerate_email_pdf($id, $supplier->id);
   // $pdf_name = 'CMD ' . Carbon::parse($invoice->date_invoice)->format('d/m/Y') . '.pdf';
	   $pdf_name = $invoice->clinic_inv_num.'.pdf';

    // Compose the email details
    $title = __('Dear Sir/Madam') . ', ' . $supplier->name . ' , ';
    $msg = __('I hope this email finds you well') . '.<br/>';
    $msg .= __('Please find attached the order'). ' ' .$invoice->clinic_inv_num.' ';
	$msg .= __('placed by Fast Med s.a.r.l with your esteemed company on Date') . ' ' . Carbon::parse($invoice->date_invoice)->format('d/m/Y') . '.<br/>';
    $msg .= __('If you require any further information or clarification, please do not hesitate to contact us') . '.<br/>';
    $msg .= __('Best regards') . ' , ';
    $from = $branch->full_name;
    $reply_to_name = __("No reply") . ',' . $branch->full_name;
    $reply_to_address = $branch->email ?? 'noreply@email.com';
    $subject = __('Order Placement from Fast Med s.a.r.l') . ' ' . $invoice->clinic_inv_num . '-' . Carbon::parse($invoice->date_invoice)->format('d/m/Y') . '.';
	$branch_name1='Sales Department';
    $details = [
        'title' => $title,
        'msg' => $msg,
        'branch_name1' => $branch_name1,
        'branch_name2' => 'Fast Med s.a.r.l',
        'branch_address' => 'Hazmieh Gardenia Street Makateb Bld 4th floor ',
        'branch_tel' => '+961 5 452 766, Mob: +961 3 309 094',
        'branch_fax' => '',
        'branch_email' => $branch->email,
        'from' => $from,
        'reply_to_name' => $reply_to_name,
        'reply_to_address' => $reply_to_address,
        'subject' => $subject
    ];

    try {
        // Send the email to multiple recipients and CC multiple addresses
        $mail = Mail::to($emails);

        if (!empty($ccEmails)) {
            $mail->cc($ccEmails);
        }
		if (!empty($bccEmails)) {
            $mail->bcc($bccEmails);
        }

        $mail->send(new SettingMailAttachSupp($details, $pdf, $pdf_name));

        if (Mail::failures()) {
            return response()->json(['fail' => __('Email failed to be sent')]);
        }

        // Update the invoice to indicate the email was sent
        $invoice->update(['email_sent' => 'Y']);

        return response()->json(['success' => __('Email is sent successfully to supplier')]);
    } catch (\Exception $e) {
        // Log the error or handle it accordingly
        return response()->json(['error' => __('An error occurred while sending the email')]);
    }
}





public function salesgenerate_cmd_pdf($lang,Request $request){
	$ref_invoice = TblInventoryInvoicesRequest::find($request->id);
	$supplier=TblInventoryClients::find($request->supplier_id);
	$clinic=Clinic::where('active','O')->find($ref_invoice->clinic_num);
	$user_perm = auth()->user()->permission;
	if ($user_perm == 'U') {
			$result_ref=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$ref_invoice->id)->get();
	}else{
	$result_price_id = TblInventoryInvoicesRequest::where('active', 'O')
                    ->where('client_id', $ref_invoice->fournisseur_id)
                    ->value('id');
	
	$result_ref = DB::select("
    SELECT a.item_name, a.item_code, a.qty,IFNULL(b.price, 0) AS price, 
           (a.qty * IFNULL(b.price, 0)) AS total 
    FROM tbl_inventory_invoices_details a
    LEFT JOIN tbl_inventory_invoices_details b 
    ON a.item_code = b.item_code 
    AND b.invoice_id = ? 
    AND b.active = 'O'
    WHERE a.invoice_id = ? 
    ORDER BY a.id DESC", [$result_price_id, $ref_invoice->id]);
	}
   // $code= DB::select(DB::raw("$sqlCode"));
	
	
	$logo_ref=DB::table('tbl_bill_logo')->where('clinic_num',$ref_invoice->clinic_num)->where('status','O')->first();
				  
	$data = [
	         'title' => __('Order'),
	         'date' => date('m/d/Y'),
	         'logo'=>$logo_ref,
			 'Invoice' => $ref_invoice,
	         'clinic' => $clinic,
             'supplier' =>$supplier,
			 'result' => $result_ref
			 ]; 
    
	$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.commandPDF', $data);
    $dom_pdf = $pdf->getDomPDF();
	$canvas = $dom_pdf->get_canvas();
	$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));         
			    
	return  $pdf->stream();
}

private function salesgenerate_email_pdf($id,$supplier_id){
	 $ref_invoice = TblInventoryInvoicesRequest::find($id);
	$supplier=TblInventoryClients::find($supplier_id);
	$clinic=Clinic::where('active','O')->find($ref_invoice->clinic_num);
	$result_ref=TblInventoryInvoicesDetails::where('status','O')->where('invoice_id',$ref_invoice->id)->get();
	$logo_ref=DB::table('tbl_bill_logo')->where('clinic_num',$ref_invoice->clinic_num)->where('status','O')->first();
				  
	$data = [
	         'title' => __('Command'),
	         'date' => date('m/d/Y'),
	         'logo'=>$logo_ref,
			 'Invoice' => $ref_invoice,
	         'clinic' => $clinic,
             'supplier' =>$supplier,
			 'result' => $result_ref
			 ]; 
    
	$pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
							-> loadView('inventory.invoices.pdfs.commandPDF', $data);
    $dom_pdf = $pdf->getDomPDF();
	$canvas = $dom_pdf->get_canvas();
	$canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0)); 
	
return  $pdf->output();
}	



public function SalesValidate1($lang,Request $request){
	$id = $request->id;
	$type = $request->is_valid1;
	$user_num = auth()->user()->id;
	switch($type){
		case 'validated': 
		//	$is_valid1 = $request->is_valid1;
			
		//	if($is_valid1 == 'Y'){
			   
			   TblInventoryInvoicesRequest::where('id',$id)->update(['is_valid1'=>'Y']);
			   //only update if there is no report datetime 
			   
			   $msg = __('Order are validated succssfully');
		break;
		
		case 'invalid': 
	
			   TblInventoryInvoicesRequest::where('id',$id)->update(['is_valid1'=>'N']);
			   $msg = __('Order are unvalidated succssfully');
			 //$location = route('lab.visit.edit',[$lang,$id]);
			
		break;
		}
	 return response()->json(['msg'=>$msg,'type'=>$type]);
}

public function SalesFree($lang,Request $request){
	$id = $request->id;
	$type = $request->is_free;
	$user_num = auth()->user()->id;
	switch($type){
		case 'Y': 
		//	$is_valid1 = $request->is_valid1;
			
		//	if($is_valid1 == 'Y'){
			   
			   TblInventoryInvoicesRequest::where('id',$id)->update(['free'=>'Y']);
			   //only update if there is no report datetime 
			   
			   $msg = __('Invoice are Free succsesfully');
		break;
		
		case 'N': 
	
			   TblInventoryInvoicesRequest::where('id',$id)->update(['free'=>'N']);
			   $msg = __('Invoice are Not Free successfully');
			 //$location = route('lab.visit.edit',[$lang,$id]);
			
		break;
		}
	 return response()->json(['msg'=>$msg,'type'=>$type]);
}



public function SalesValidate2($lang,Request $request){
	$id = $request->id;
	$type = $request->is_valid2;
	$cleintid=$request->cleintid;
    $delivery=TblInventoryClients::where('id',$cleintid)->first();
	$deliveryDays=$delivery->delivery;
	$user_num = auth()->user()->id;
    $clinic_id = auth()->user()->clinic_num; 
	switch($type){
		case 'validated': 
	
$deliveryDaysArray = json_decode($deliveryDays, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Invalid JSON: " . json_last_error_msg());
}

// Map days of the week to numbers (Sunday = 0, Monday = 1, ..., Saturday = 6)
$dayMapping = [
    "Sunday" => 0, "Monday" => 1, "Tuesday" => 2,
    "Wednesday" => 3, "Thursday" => 4, "Friday" => 5, "Saturday" => 6,
];

// Initialize current date and day using Carbon
$currentDateTime = Carbon::now();
$currentDayOfWeek = $currentDateTime->format('l');  // Get current day name (e.g., "Monday")
$currentDayNumber = $dayMapping[$currentDayOfWeek];  // Get corresponding day number (e.g., 1 for Monday)

// Variables to track the next delivery
$nextDelivery = null;
$minDaysToNext = PHP_INT_MAX;

// Iterate through the delivery days array (expecting pairs of offset and day name)
foreach ($deliveryDaysArray as $entry) {
    // Validate the entry format (should be "offset;dayName")
    $parts = explode(";", $entry);

    // Ensure that the entry has exactly two parts (offset and day name)
    if (count($parts) !== 2) {
     //   echo "Skipping invalid entry: $entry\n";  // Debugging message
        continue;
    }

    // Parse offset and day name
    $offset = (int) $parts[0];
    $dayName = $parts[1];

    // Ensure day name exists in the mapping
    if (!isset($dayMapping[$dayName])) {
     //   echo "Skipping invalid day name: $dayName\n";  // Debugging message
        continue;
    }

    $deliveryDayNumber = $dayMapping[$dayName];

    // Calculate days to the next delivery
    $daysToNext = ($deliveryDayNumber - $currentDayNumber + 7) % 7;
    if ($daysToNext === 0) {
        $daysToNext += $offset; // Adjust for offset if delivery is today
    }

    // Update next delivery if this one is closer
    if ($daysToNext < $minDaysToNext) {
        $minDaysToNext = $daysToNext;
        $nextDelivery = [$offset, $dayName];
    }
}

// Calculate the next delivery date
if ($nextDelivery) {
    $nextDeliveryDate = clone $currentDateTime;
    $nextDeliveryDate->modify("+$minDaysToNext days");
    $nextDeliveryDayF = $nextDelivery[1];  // The day of the week
    $nextDeliveryDateF = $nextDeliveryDate->format("Y-m-d");  // The date of next delivery
}
TblInventoryInvoicesRequest::where('id',$id)->update(['is_valid2'=>'Y','date_delivery'=>$nextDeliveryDateF]);

//  copy order to sales 
 
$newType = '2'; // The new value for the 'type' field

// Retrieve the original record
$originalRecord = TblInventoryInvoicesRequest::where('id', $id)->first();

if ($originalRecord) {
    $newRecord = $originalRecord->replicate();
    $newRecord->type = $newType;
    $newRecord->save();
    $lastInsertedId = $newRecord->id;
$FacInventory = TblInventoryInvSerials::where('clinic_num',$clinic_id)->first();
		
		
		 $SerieFacInventory = $FacInventory->s_serial_code;
			$SeqFacInventory = $FacInventory-> s_sequence_num ;	
			$reqID=trim($SerieFacInventory)."-".($SeqFacInventory+1);
		    TblInventoryInvSerials::where('clinic_num',$clinic_id)->update([
				                  's_sequence_num' => $SeqFacInventory+1
								  ]);
								  
TblInventoryInvoicesRequest::where('id',$lastInsertedId)->update([
				                  'clinic_inv_num'=>$reqID	
								  ]);	
TblInventoryInvoicesRequest::where('id',$id)->update([
				                  'cmd_id'=>$lastInsertedId	
								  ]);									  
//STypes = TblInventoryTypes::where('id','2')->first();
//$SignTypes = $STypes->sign ;

$recordsToCopy = TblInventoryInvoicesDetails::where('invoice_id', $id)->get();
$total=0.00;
if ($recordsToCopy->isNotEmpty()) {
    foreach ($recordsToCopy as $record) {
        // Create a new instance and copy the attributes
        $newRecord = $record->replicate();

        // Update the 'invoice_id' or any other fields as needed
        $newRecord->invoice_id = $lastInsertedId;
        $newRecord->qty = $record->rqty;
		$newRecord->price = $record->rprice;
		$newRecord->total = $record->rprice*$record->rqty;
		$total=$total+($record->rprice*$record->rqty);
        // Save the new record
        $newRecord->save();
		$code=$record->item_code;	
		$quantity = $record->rqty;	 
		$SQty = TblInventoryItems::where('id',$code)->first();
		$QtyItem = $SQty->qty;	
		$qty0=$QtyItem-$quantity;
	    $qty1=strval($qty0);
		$UpdateQty="update tbl_inventory_items set qty=".$qty1." where id='".$code."'";
		 DB::select(DB::raw("$UpdateQty"));
   		
		TblInventoryInvoicesDetails::where('invoice_id',$id)->update([
				                  'cmd_id'=>$lastInsertedId,'ref_invoice'=>$reqID]);			
		
    }
} 
TblInventoryInvoicesRequest::where('id',$lastInsertedId)->update([
				                  'total'=>$total	
								  ]);	



}



 
			   $msg = __('Order are Facturation succssfully');
		break;
		
		case 'invalid': 
	
			   TblInventoryInvoicesRequest::where('id',$id)->update(['is_valid2'=>'N','date_delivery'=>Null]);
			    TblInventoryInvoicesDetails::where('invoice_id',$id)->update(['ref_invoice'=>Null]);
			   $msg = __('Order are UnFacturation succssfully');
			   $reqID='';
			   $nextDeliveryDateF='';
			 //$location = route('lab.visit.edit',[$lang,$id]);
			
		break;
		}
	 return response()->json(['msg'=>$msg,'type'=>$type,'refinvoice'=>$reqID,'datedelivery'=>$nextDeliveryDateF]);
}
public function SalesDone($lang,Request $request){
		$id = $request->id;
	$type = $request->cdone;
	$user_num = auth()->user()->id;
	switch($type){
		case 'Done': 
		//	$is_valid1 = $request->is_valid1;
			
		//	if($is_valid1 == 'Y'){
			   
			   TblInventoryInvoicesRequest::where('id',$id)->update(['cdone'=>'Y']);
			   //only update if there is no report datetime 
			   
			   $msg = __('Order are Done succssfully');
		break;
		
		case 'Pending': 
	
			   TblInventoryInvoicesRequest::where('id',$id)->update(['cdone'=>'N']);
			   $msg = __('Order are Pending succssfully');
			 //$location = route('lab.visit.edit',[$lang,$id]);
			
		break;
		}
	 return response()->json(['msg'=>$msg,'type'=>$type]);
}

public function SalesPaid($lang,Request $request){
		$id = $request->id;
	$type = $request->cpaid;
	$user_num = auth()->user()->id;
	switch($type){
		case 'Paid': 
		//	$is_valid1 = $request->is_valid1;
			
		//	if($is_valid1 == 'Y'){
			   $cpay='Y';
			   TblInventoryInvoicesRequest::where('id',$id)->update(['cpaid'=>'Y']);
			   //only update if there is no report datetime 
   			   TblInventoryInvoicesDetails::where('invoice_id',$id)->update(['cpay'=>'Y']);
			   $msg = __('Order are Paid succssfully');
			   
		break;
		
		case 'NotPaid': 
			   $cpay='N';
			   TblInventoryInvoicesRequest::where('id',$id)->update(['cpaid'=>'N']);
   			   TblInventoryInvoicesDetails::where('invoice_id',$id)->update(['cpay'=>'N']);
			   $msg = __('Order are Unpaid succssfully');
			 //$location = route('lab.visit.edit',[$lang,$id]);
		break;
		}
	 return response()->json(['msg'=>$msg,'type'=>$type,'cpay'=>$cpay]);
}



public function EmailSentValidate($lang,Request $request){
$id = $request->id;
	$type = $request->state;
	$user_num = auth()->user()->id;
	switch($type){
		case 'sent': 
		//	$is_valid1 = $request->is_valid1;
			
		//	if($is_valid1 == 'Y'){
			   
			   TblInventoryInvoicesRequest::where('id',$id)->update(['email_sent'=>'Y','user_id'=>$user_num]);
			   //only update if there is no report datetime 
			   
			   $msg = __('Change To Email Sent');
		break;
		
		case 'nosent': 
	
			   TblInventoryInvoicesRequest::where('id',$id)->update(['email_sent'=>'N','user_id'=>$user_num]);
			   $msg = __('Change To Email Not Sent');
			 //$location = route('lab.visit.edit',[$lang,$id]);
			
		break;
		}
	 return response()->json(['msg'=>$msg,'type'=>$type]);

}


public function loadOtherItemsCMD($lang,Request $request){
			
	$search = $request->input('q');
   	$code = DB::table('tbl_inventory_items as a')
	        ->select('a.id as id',    DB::raw("CONCAT(a.description, ', Lot Nb: ', IFNULL(a.nblot, ''), ', Expiry Date: ', IFNULL(a.dexpiry, 'N/A')) as text"))
    ->where('a.active', 'O');
//	$code = DB::table('tbl_inventory_items as a')
	  //      ->select('a.id as id',DB::raw("a.description as text"))
		//	->where('a.active','O');

 //  if(isset($request->supplier) && $request->supplier !='0'){
	//	$supplier= $request->supplier;
	//	$code = $code->where('a.fournisseur',$supplier);
	//   }
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

public function cuploadAttach($lang,Request $request)

    {
        
		$this->validate($request, [

				'files' => 'required',
				'files.*'=>'mimes:jpg,jpeg,png,gif,bmp,tiff,svg,webp,pdf|max:3072'
			  ],[
				 'files.required' => __('Please choose a document of type image/pdf'),
				 'files.*.mimes' => __('Please insert a document of type image/pdf'),
				 'files.*.max'   => __('Document should be less than 3 MB')
			
			]);
       
		$uid = auth()->user()->id;
		$order_id = $request->doc_order_id;
		
		$image_path = storage_path('app/7mJ~33/Documents');
		if (!file_exists($image_path)){
				mkdir($image_path, 0775, true);
			}
		
		if($request->hasfile('files')) {
			
			  foreach($request->file('files') as $file){
			  
			  $image_name =  date('Ymd').'_'.uniqid().'.'.$file->getClientOriginalExtension();
              $file->move($image_path, $image_name);
			  $notes = $request->description;
			  $path = 'Documents';
			  
				DOCSCmd::create([
					'order_id' => $order_id,
					'notes' => $notes,
					'name' => $file->getClientOriginalName(),
					'path' => $path.'/'.$image_name,
					'user_num' => $uid,
					'active' => 'Y'
				]);
			  }
           
			return back()->with('success',__('Documents Uploaded successfully'));
			
			 }
    
	}

 public function cdestroyAttach($lang,Request $request)

    {
    	$doc = DOCSCmd::find($request->image_id);
        Storage::disk('private')->delete($doc->path);
        $doc->delete();		
    	return back()->with('success','Document removed successfully.');	

    }

public function download(Request $request, $lang)
{
    // Retrieve the file record from the database
    $id = $request->input('id');
	
    $doc = DOCSCmd::where('order_id',$id)->firstOrFail();
    // Get the file path from the database
    $filePath = storage_path('app/7mJ~33/' . $doc->path);
	
    // Check if the file exists in the storage
    if (file_exists($filePath)) {
        // Return the file as a download response with the appropriate headers
        return response()->download($filePath, $doc->name, [
            'Content-Disposition' => 'attachment; filename="' . $doc->name . '"'
        ]);
    }

    // If the file doesn't exist, return a 404 response
    return response()->json(['message' => 'File not found'], 404);
}

public function fill_code_categoty($lang,Request $request){
if($request->filter=='1'){
$select = $request->selectpro;
$code = DB::table('tbl_inventory_clients as a')
    ->select('a.id as id', DB::raw('a.analyzer as analyzer'))
    ->where('a.active', 'O')
    ->where('a.id', $select)
    ->distinct()
    ->orderBy('a.id', 'desc')
    ->get();

$analyzer = $code->first()->analyzer;
$analyzerArray = json_decode($analyzer, true);

$html = '<option value="0">' . __("Choose Analyzer") . '</option>';
if ($code->isNotEmpty()) {
	$analyzer = $code->first()->analyzer;
$analyzerArray = json_decode($analyzer, true);
if (isset($analyzerArray) && is_array($analyzerArray)) {
foreach ($analyzerArray as $item) {
    list($value, $text) = explode(';', $item);
    $html .= '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($text) . '</option>';
}
}
  	    return response()->json(['html'=>$html]);	   
}
}	
	
if($request->filter=='3'){
$select = $request->selectfou;
if ($select=='0'){
	$code = DB::table('tbl_inventory_category_types as a')
   ->select('a.id as id','a.name as analyzer')
    ->where('a.active', 'O')
   ->distinct()
   ->orderBy('a.id', 'desc')
    ->get();
 $html='<option value="0">'.__("Choose a category").'</option>';
     
	  foreach($code as $c){
		$name = $c->analyzer;
	   
		$html.='<option value="'.$c->id.'">'.$name.'</option>';
	   }	
}else{
$code = DB::table('tbl_inventory_items_fournisseur as a')
   ->select('a.id as id', DB::raw('a.types as analyzer'))
    ->where('a.active', 'O')
    ->where('a.id', $select)
   ->distinct()
   ->orderBy('a.id', 'desc')
    ->get();
$analyzer = $code->first()->analyzer;
$analyzerArray = json_decode($analyzer, true);


$html = '<option value="0">' . __("Choose a category") . '</option>';
if ($code->isNotEmpty()) {
	$analyzer = $code->first()->analyzer;
$analyzerArray = json_decode($analyzer, true);
if (isset($analyzerArray) && is_array($analyzerArray)) {
foreach ($analyzerArray as $item) {
$text = DB::table('tbl_inventory_category_types as a') 
->where('a.active', 'O') 
->where('a.id', $item) 
->pluck('a.name')
 ->first();
if ($text) {
$html .= '<option value="' . htmlspecialchars($item) . '">' . htmlspecialchars($text) . '</option>';
}
}
}
}
}

$code = TblInventoryItems::select(
    'id', 
    'brand', 
    'category', 
    'materiel', 
       DB::raw("CONCAT(description, ', Lot Nb: ', IFNULL(nblot, ''), ', Expiry Date: ', IFNULL(dexpiry, 'N/A')) as name"))
->where('active', 'O')
->orderBy('name', 'asc');

// DB::raw("CONCAT(description, ' -(', nbtest, ')') as name")
//->orderBy(DB::raw("CONCAT(description, ' -(', nbtest, ')')"), 'asc');

if(!empty($select)) { $code->where('fournisseur', $select); } 

$code = $code->distinct()->get();

       $html1='<option value="0">'.__("All Codes").'</option>';
     
	  foreach($code as $c){
		$name = $c->name;
	   
		$html1.='<option value="'.$c->id.'">'.$name.'</option>';
	   }



  	    return response()->json(['html'=>$html,'html1'=>$html1]);	   
}
	
	
if($request->filter=='2'){
$category = $request->category;
$analyzer = $request->analyzer;
$type = $request->type;

$code = TblInventoryItems::select(
    'id', 
    'brand', 
    'category', 
    'materiel', 
       DB::raw("CONCAT(description, ', Lot Nb: ', IFNULL(nblot, ''), ', Expiry Date: ', IFNULL(dexpiry, 'N/A')) as name"))
->where('active', 'O')
->orderBy('name', 'asc');

if(!empty($category)) { $code->where('category', $category); }
if(!empty($analyzer)) {
if(($analyzer==15) || ($analyzer==8) || ($analyzer==11) || ($analyzer==12)){
	
if(($analyzer==15) || ($analyzer==8)){
$analyzer = [15,8];  // Array of values
$code->whereIn('brand', $analyzer);
}
if(($analyzer==11) || ($analyzer==12)){
$analyzer = [11,12];  // Array of values
$code->whereIn('brand', $analyzer);	
}
//if(($analyzer!=15) && ($analyzer!=8) && ($analyzer!=11) && ($analyzer!=12)){
//$code->where('brand', $analyzer); } 
}else{
		$code->where('brand', $analyzer);

	
}
}
if(!empty($type)) { $code->where('materiel', $type); }
if(!empty($selectfou)) { $code->where('fournisseur', $selectfou); }
//dd($code->toSql());
$code = $code->distinct()->get();

       $html='<option value="0">'.__("All Codes").'</option>';
     
	  foreach($code as $c){
		$name = $c->name;
	   
		$html.='<option value="'.$c->id.'">'.$name.'</option>';
	   }
	    return response()->json(['html'=>$html]);	   

}





}




	  		  
}








