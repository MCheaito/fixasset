<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\ExtLab;
use App\Models\ExtIns;
use App\Models\LabTests;
use App\Models\TblBillCurrency;

use DB;
use DataTables;
use Alert;
use Carbon\Carbon;
use Session;

class PricesController extends Controller{
public function __construct(){  $this->middleware('auth'); }
  
public function index($lang,Request $request){
	    Session::forget('extLAB');
		Session::forget('INS');
		Session::forget('DOCTOR');
		
	    
		$user_type = auth()->user()->type;
	    $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
		$currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
		$lbl_usd = isset($currUSD)? $currUSD->price:15000;
        $lbl_euro = isset($currEURO)? $currEURO->price:15000;
		$user_clinic_num=auth()->user()->clinic_num;
		$lab = Clinic::find($user_clinic_num);
        $ext_labs = ExtLab::where('status','A')->where('clinic_num',$lab->id)->orderBy('id','desc')->get();
		$ext_labs_cats = DB::table('tbl_external_labs_categories')->orderBy('id')->get();
	    $codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and cnss IS NOT NULL and cnss<>""))')->orderBy('testord')->get();
		$insurance = ExtIns::where('status','Y')->where('clinic_num',$lab->id)->orderBy('id','desc')->get();
		$doctors = Doctor::where('active','O')->orderBy('id','desc')->get();
		
		if($request->ajax()){
	   	  switch($request->type){
			  case 'extlab':
			   
				   $filter_extlab='';
			   
				  if(isset($request->filter_extlab) && $request->filter_extlab!=''){
				   $filter_extlab = $request->filter_extlab;
				   }
				   
				 

			   $cat_name = ($lang=='fr')?'cat.name_fr':'cat.name_en';
			   $data = DB::table('tbl_external_labs as p')
						  ->select('p.id','p.status','p.created_at','p.updated_at',
									DB::raw("IF(p.code IS NOT NULL and p.code<>'',CONCAT(p.full_name,'(',p.code,')'),p.full_name) as lab_name"),
									'p.has_prices',DB::raw("IFNULL({$cat_name},'') as category_name"))							
						  ->leftjoin('tbl_external_labs_categories as cat','cat.id','p.category')
						  ->where('p.clinic_num',$user_clinic_num)
						  ->where('p.has_prices','Y')
						  ->where('p.status','A');
						  
					     if($filter_extlab !=''){
					      $data = $data->where('p.id',$filter_extlab);
						 }
						
					   
					
				   $data = $data->distinct()->orderBy('p.id','desc');	
			   
			   
			       return Datatables::of($data)
							->addIndexColumn()
							->addColumn('action', function($row) use($lang){
								   $edit_url=route('extlab_prices.edit',[$lang,$row->id]); 
								   $btn = '<a href="'.$edit_url.'"  class="btn btn-icon btn-md"><i class="far fa-edit text-primary" title="'.__('Edit').'"></i></a>';
								   return $btn;
							})
							->addColumn('created_on', function($row){
								$created_on = Carbon::parse($row->created_at);
								$created_on = $created_on->addHours(2);
								$created_on = $created_on->format('d/m/Y H:i');
								return $created_on;
							})
							->addColumn('updated_on',function($row){
								if(isset($row->updated_at)){
								$updated_on = Carbon::parse($row->updated_at);
								$updated_on = $updated_on->addHours(2);
								$updated_on = $updated_on->format('d/m/Y H:i');						
								}else{
									$updated_on=NULL;
								}
								return $updated_on;
							})
							->filterColumn('lab_name', function($query, $keyword) {
							   $sql = "IF(p.code IS NOT NULL and p.code<>'',CONCAT(p.full_name,'(',p.code,')'),p.full_name)  like ?";
							   $query->whereRaw($sql, ["%{$keyword}%"]);
							 })
							->filterColumn('category_name', function($query, $keyword) use($lang){
							   $cat_name = ($lang=='fr')?'cat.name_en':'cat.name_fr';
							   $sql = "{$cat_name} like ?";
							   $query->whereRaw($sql, ["%{$keyword}%"]);
							 }) 
							->filterColumn('created_on', function($query, $keyword) {
							   $sql = "DATE(p.created_at)  like ?";
							   $query->whereRaw($sql, ["%{$keyword}%"]);
							 })
							->filterColumn('updated_on', function($query, $keyword) {
							   $sql = "DATE(p.updated_at)  like ?";
							   $query->whereRaw($sql, ["%{$keyword}%"]);
							 }) 
							->rawColumns(['action'])
							->make(true);
			  break;
			  case 'doctor':
			    
				   $filter_doctor='';
			   
				  if(isset($request->filter_doctor) && $request->filter_doctor!=''){
				   $filter_doctor = $request->filter_doctor;
				   }
				   
				 

			   $sp_name = ($lang=='fr')?'sp.name_fr':'sp.name_en';
			   $data = DB::table('tbl_doctors as p')
						  ->select('p.id','p.active as status','p.created_at','p.updated_at',
									DB::raw("IF(p.middle_name!='' and p.middle_name IS NOT NULL,concat(p.first_name,' ',p.middle_name,' ',p.last_name),concat(p.first_name,' ',p.last_name)) as doctor_name"),
									'p.has_prices',
									DB::raw("IFNULL({$sp_name},'') as speciality_name"))							
						  ->leftjoin('tbl_doctors_specia as sp','sp.id','p.specia')
						   ->where('p.has_prices','Y')
						  ->where('p.active','O');
			
					     if($filter_doctor !=''){
					      $data = $data->where('p.id',$filter_doctor);
						 }
						
					   
					
				   $data = $data->distinct()->orderBy('p.id','desc');	
			   
			   
			       return Datatables::of($data)
							->addIndexColumn()
							->addColumn('action', function($row) use($lang){
								   $edit_url=route('doctor_prices.edit',[$lang,$row->id]); 
								   $btn = '<a href="'.$edit_url.'"  class="btn btn-icon btn-md"><i class="far fa-edit text-primary" title="'.__('Edit').'"></i></a>';
								   return $btn;
							})
							->addColumn('created_on', function($row){
								$created_on = Carbon::parse($row->created_at);
								$created_on = $created_on->addHours(2);
								$created_on = $created_on->format('d/m/Y H:i');
								return $created_on;
							})
							->addColumn('updated_on',function($row){
								if(isset($row->updated_at)){
								$updated_on = Carbon::parse($row->updated_at);
								$updated_on = $updated_on->addHours(2);
								$updated_on = $updated_on->format('d/m/Y H:i');						
								}else{
									$updated_on=NULL;
								}
								return $updated_on;
							})
							->filterColumn('doctor_name', function($query, $keyword) {
							   $sql = "IF(p.middle_name!='' and p.middle_name IS NOT NULL,concat(p.first_name,' ',p.middle_name,' ',p.last_name),concat(p.first_name,' ',p.last_name))  like ?";
							   $query->whereRaw($sql, ["%{$keyword}%"]);
							 })
							->filterColumn('speciality_name', function($query, $keyword) use($lang){
							   $sp_name = ($lang=='fr')?'sp.name_en':'sp.name_fr';
							   $sql = "{$sp_name} like ?";
							   $query->whereRaw($sql, ["%{$keyword}%"]);
							 }) 
							->filterColumn('created_on', function($query, $keyword) {
							   $sql = "DATE(p.created_at)  like ?";
							   $query->whereRaw($sql, ["%{$keyword}%"]);
							 })
							->filterColumn('updated_on', function($query, $keyword) {
							   $sql = "DATE(p.updated_at)  like ?";
							   $query->whereRaw($sql, ["%{$keyword}%"]);
							 }) 
							->rawColumns(['action'])
							->make(true);
			  break;
			  case 'insurance':
					
				   $filter_ins='';
				  if(isset($request->filter_ins) && $request->filter_ins!=''){
					$filter_ins = $request->filter_ins;
				   }
		  	   
	          $data = DB::table('tbl_referred_labs as p')
	              ->select('p.id','p.status','p.created_at','p.updated_at',
							DB::raw("IF(p.code IS NOT NULL and p.code<>'',CONCAT(p.full_name,'(',p.code,')'),p.full_name) as lab_name"),
							'p.has_prices')
				  ->where('p.clinic_num',$user_clinic_num)
				  ->where('p.has_prices','Y')
				  ->where('category','=','2')
				  ->where('p.status','Y');
	
				if($filter_ins !=''){
				   $data = $data->where('p.id',$filter_ins);
					 }
			   
			   $data = $data->distinct()->orderBy('p.id','desc');	
	   
	   	       return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) use($lang){
						   $edit_url=route('ins_prices.edit',[$lang,$row->id]); 
						   $btn = '<a href="'.$edit_url.'"  class="btn btn-icon btn-md"><i class="far fa-edit text-primary" title="'.__('Edit').'"></i></a>';
						   return $btn;
                    })
					->addColumn('created_on', function($row){
						$created_on = Carbon::parse($row->created_at);
						$created_on = $created_on->addHours(2);
						$created_on = $created_on->format('d/m/Y H:i');
						return $created_on;
					})
					->addColumn('updated_on',function($row){
						if(isset($row->updated_at)){
                        $updated_on = Carbon::parse($row->updated_at);
						$updated_on = $updated_on->addHours(2);
						$updated_on = $updated_on->format('d/m/Y H:i');						
						}else{
							$updated_on=NULL;
						}
						return $updated_on;
					})
				    ->filterColumn('lab_name', function($query, $keyword) {
                       $sql = "IF(p.code IS NOT NULL and p.code<>'',CONCAT(p.full_name,'(',p.code,')'),p.full_name)  like ?";
                       $query->whereRaw($sql, ["%{$keyword}%"]);
                     })
					->filterColumn('created_on', function($query, $keyword) {
                       $sql = "DATE(p.created_at)  like ?";
                       $query->whereRaw($sql, ["%{$keyword}%"]);
                     })
					->filterColumn('updated_on', function($query, $keyword) {
                       $sql = "DATE(p.updated_at)  like ?";
                       $query->whereRaw($sql, ["%{$keyword}%"]);
                     }) 
                    ->rawColumns(['action'])
                    ->make(true); 
			  break;
		  } 
	      
				
	}	
		
		
	return view('prices.index')->with(['lab'=>$lab,'ext_labs'=>$ext_labs,'codes'=>$codes,'lbl_usd'=>$lbl_usd,'lbl_euro'=>$lbl_euro,'insurance'=>$insurance,'doctors'=>$doctors]);	
	}
	
public function extlab_getCats($lang,Request $request){
	
	$lab = DB::table('tbl_external_labs')->find($request->id);
	$cat = DB::table('tbl_external_labs_categories')->find($lab->category);
	$category="";
	if(isset($request->category_num)){
	  
	  if($request->category_num!="0"){	
		$category = $request->category_num;
	   }
	}else{
		if(isset($cat)){
		 $category=$cat->id;	
		}
	}
	//get external labs of same category
	$other_labs = DB::table('tbl_external_labs')->where('id','<>',$lab->id);
	if($category!=""){
		$other_labs = $other_labs->where('category',$category);
	}
	$other_labs = $other_labs->orderBy('id','desc')->get();
	
	$html='';
	foreach($other_labs as $o){
		$html.='<div class="form-group col-md-4 col-6" style="font-size:14px;"><input type="checkbox" name="ext_lab[]" class="ml-2 extLAB" data-category="'.$o->category.'" value="'.$o->id.'"/><label class="ml-2">'.$o->full_name.'</label></div>';
	}
return response()->json(['html'=>$html,'category'=>$category]);
}

public function extlab_getPrices($lang,Request $request){
	$codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL))');
	$priced=$pricel='';
	if(isset($request->filter_lab) && $request->filter_lab!=''){
		$lab = ExtLab::find($request->filter_lab);
		$priced = $lab->priced;
		$pricel = $lab->pricel;
		$cat = DB::table('tbl_external_labs_categories')->find($lab->category);
	 }

     $codes=$codes->orderBy('testord')->distinct(); 	 
	 
	 return Datatables::of($codes)
                    ->addIndexColumn()
                    ->addColumn('manual', function($row){
                          $btn='<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" value="'.$row->id.'" class="chk_manual" onchange="event.preventDefault();chg_state(this);"/><span class="slideon-slider"></span></label>';
						  return $btn;					  
                    })
					->addColumn('totald', function($row) use($lang){
                          $btn = '<input type="text" size="7" id="totald" oninput="event.preventDefault();calcOnePrice(this);" onkeypress="return isNumberKey(event)" disabled="true" />';
						  return $btn;					  
                    })
					->addColumn('totall', function($row){
						$btn = '<input type="text" size="10" id="totall" oninput="event.preventDefault();calcLBPrice(this);" onkeypress="return isNumberKey(event)" disabled="true" />';
						return $btn;
					})
					
				    ->rawColumns(['manual','totald','totall'])
                    ->with('priced', $priced)
                    ->with('pricel', $pricel)
					->toJson();
}	

public function extlab_create($lang,$id=NULL){
	   $user_clinic_num=auth()->user()->clinic_num;
	   $EXTLABID=NULL;
	   if(isset($id)){
		   $EXTLABID = $id;
	   }
	   $lab = Clinic::find($user_clinic_num);
	   $lab_prices = ExtLab::where('clinic_num',$lab->id)->where('has_prices','Y')->pluck('id')->toArray();
	   $cat_name = ($lang=='fr')?'cat.name_fr':'cat.name_en';
	   $ext_labs = ExtLab::select('tbl_external_labs.id','tbl_external_labs.full_name',
	                              DB::raw("IFNULL({$cat_name},'') as category_name"))
	               ->leftjoin('tbl_external_labs_categories as cat','cat.id','tbl_external_labs.category')
				   ->whereNotIn('tbl_external_labs.id',$lab_prices)->where('tbl_external_labs.clinic_num',$lab->id)
				   ->where('tbl_external_labs.status','A')->orderBy('tbl_external_labs.id','desc')->get();
	   
	   $codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL))')->orderBy('testord')->get();
	   $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
	   $currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
	   $lbl_usd = isset($currUSD)? $currUSD->price:90000;
       $lbl_euro = isset($currEURO)? $currEURO->price:90000;
	   return view('prices.external_labs.create')->with(['EXTLABID'=>$EXTLABID,'lab'=>$lab,'ext_labs'=>$ext_labs,'codes'=>$codes,'lbl_euro'=>$lbl_euro,'lbl_usd'=>$lbl_usd]);
   }
   
public function extlab_store($lang,Request $request){
		
		$data = json_decode($request->manual,true);
		$lab_id = $request->lab_id;
		$currencyEUR=TblBillCurrency::where('active','O')->where('abreviation','EUR')->first();
        $lbl_eur = isset($currencyEUR)?$currencyEUR->price:95000;
		
		
		ExtLab::where('id',$lab_id)->update([
			'priced'=>$request->priced,
			'pricel'=>$request->pricel,
			'pricee'=>number_format((float)$request->pricel / $lbl_eur, 2),
			'has_prices'=>'Y',
			'user_num'=>auth()->user()->id
			]);
		foreach($data as $d){
			DB::table('tbl_external_labs_prices')
			 ->insert([
			 'totall'=>$d['totall'],
			 'totald'=>$d['totald'],
			 'totale'=>number_format((float)$d['totall'] / $lbl_eur, 2),
			 'is_manual'=>$d['is_manual'],
			 'test_id'=>$d['test_id'],
			 'lab_id'=>$lab_id
			]);
		}	
	
	$location = route('extlab_prices.edit',[$lang,$lab_id]);
	return response()->json(['success'=>__('Saved successfully'),'location'=>$location]);
	}

public function extlab_edit($lang,$id){
	   
	   $pr = ExtLab::find($id);
	   $lab_prices = DB::table('tbl_external_labs_prices')->where('lab_id',$id)->get();
	   $code_prices = array();
	   
	   foreach($lab_prices as $v){
		  $tid = $v->test_id;
		  $code_prices[$tid]=array($v->is_manual,$v->totald,$v->totall); 
	   }
	   //dd($code_prices);
	   
       $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
	   $currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
	   
	   $lbl_usd = isset($currUSD)? $currUSD->price:90000;
       $lbl_euro = isset($currEURO)? $currEURO->price:90000;
	   
	   $user_clinic_num=auth()->user()->clinic_num;
	   $lab = Clinic::find($user_clinic_num);
	   $codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL))');
	   $cat = DB::table('tbl_external_labs_categories')->find($pr->category);
	   $codes = $codes->orderBy('testord')->get();		
	   
	   $other_ext_labs = ExtLab::where('id','<>',$pr->id)->where('clinic_num',$lab->id)->where('status','A')->orderBy('id','desc')->get();
	   
	   $cats = DB::table('tbl_external_labs_categories')->orderBy('id')->get();
	   
	   return view('prices.external_labs.edit')->with(['pr'=>$pr,'lab'=>$lab,'codes'=>$codes,'lbl_euro'=>$lbl_euro,'lbl_usd'=>$lbl_usd,'code_prices'=>$code_prices,'other_ext_labs'=>$other_ext_labs,'cats'=>$cats]);
  }

public function extlab_update($lang,Request $request){
		//get prices from DB
		$data = json_decode($request->manual,true);
		$currencyEUR=TblBillCurrency::where('active','O')->where('abreviation','EUR')->first();
        $lbl_eur = isset($currencyEUR)?$currencyEUR->price:95000;
		$id = $request->id;
		
		ExtLab::where('id',$request->id)->update([
			'priced'=>$request->priced,
			'pricel'=>$request->pricel,
			'pricee'=>number_format((float)$request->pricel / $lbl_eur, 2),
			'has_prices'=>'Y',
			'user_num'=>auth()->user()->id
			]);
			
		foreach($data as $d){
		 $exist = DB::table('tbl_external_labs_prices')->where('lab_id',$id)->where('test_id',$d['test_id'])->first();	
		 //dd($exist);
		 if(isset($exist)){
			DB::table('tbl_external_labs_prices')->where('id',$exist->id)
			    ->update([ 'totall'=>$d['totall'],
				           'totald'=>$d['totald'],
						   'totale'=>number_format((float)$d['totall'] / $lbl_eur, 2),
						   'is_manual'=>$d['is_manual']
						   ]);
			}else{
				DB::table('tbl_external_labs_prices')
				->insert([
				 'totall'=>$d['totall'],
				 'totald'=>$d['totald'],
				 'totale'=>number_format((float)$d['totall'] / $lbl_eur, 2),
				 'is_manual'=>$d['is_manual'],
				 'test_id'=>$d['test_id'],
				 'lab_id'=>$id
				]);
			 }	
			
		}	
		
	    $location = route('extlab_prices.edit',[$lang,$id]);
		return response()->json(['success'=>__('Updated successfully'),'location'=>$location]);
	}
	

public function extlab_copy($lang,Request $request){
	
	$id = $request->modal_price_id;
	$my_lab =  ExtLab::find($id);
	$labs = $request->input('ext_lab');
	$tests = explode(',',$request->testsCopy);
	$user_num = auth()->user()->id;
	//get only checked tests to copy prices to them
	$my_lab_prices = DB::table('tbl_external_labs_prices')->whereIn('test_id',$tests)->where('lab_id',$id)->get();
	
	$currencyEUR=TblBillCurrency::where('active','O')->where('abreviation','EUR')->first();
    $lbl_eur = isset($currencyEUR)?$currencyEUR->price:95000;
	//use db transaction to copy prices
	DB::beginTransaction();
    try {
         foreach ($labs as $lab_id) {
            $hasPrices = ExtLab::where('id',$lab_id)->value('has_prices');
			if($hasPrices=='N'){
				ExtLab::where('id',$lab_id)->update([
					'priced'=>$my_lab->priced,
					'pricel'=>$my_lab->pricel,
					'pricee'=>number_format((float)$my_lab->pricel / $lbl_eur, 2),
					'has_prices'=>'Y',
					'user_num'=>$user_num
					]);
			   }
		    foreach($my_lab_prices as $p){
			 $exist = DB::table('tbl_external_labs_prices')->where('lab_id',$lab_id)->where('test_id',$p->test_id)->first();
			  if(isset($exist)){
				DB::table('tbl_external_labs_prices')->where('id',$exist->id)
				    ->update(['totall'=>$p->totall,
					          'totald'=>$p->totald,
							  'totale'=>number_format((float)$p->totall / $lbl_eur, 2),
							  'is_manual'=>$p->is_manual]);
			  }else{
				DB::table('tbl_external_labs_prices')
				    ->insert(['lab_id'=>$lab_id,
					          'test_id'=>$p->test_id,
							  'totall'=>$p->totall,
							  'totald'=>$p->totald,
							  'totale'=>number_format((float)$p->totall / $lbl_eur, 2),
							  'is_manual'=>$p->is_manual]);
			   }
		     }
            
			
			}

            DB::commit();
            $msg = __('Copied successfully');
		    return response()->json(['status' => 'success','msg'=>$msg]);
        
		} catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
        }
	
}

public function ins_create($lang,$id=NULL){
	   $user_clinic_num=auth()->user()->clinic_num;
	   $INSID=NULL;
	  
	   $lab = Clinic::find($user_clinic_num);
	   $lab_prices = ExtIns::where('clinic_num',$lab->id)->where('has_prices','Y')->pluck('id')->toArray();
	   $ins = ExtIns::whereNotIn('id',$lab_prices)->where('clinic_num',$lab->id)->where('status','Y')->orderBy('id','desc')->get();
	   $referred_labs = ExtIns::where('clinic_num',$lab->id)->where('status','Y')->pluck('id')->toArray();
	   $codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL))');
	   
	   if(isset($id)){
		    $INSID = $id;
		    $codes=$codes->where('referred_tests',$INSID);
	   }else{
		    $codes=$codes->whereIn('referred_tests',$referred_labs);
	   }
	   
	   $codes = $codes->orderBy('testord')->get();		
	   
	   $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
	   $currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
	   $lbl_usd = isset($currUSD)? $currUSD->price:90000;
       $lbl_euro = isset($currEURO)? $currEURO->price:95000;
	   
	  
	   
	   return view('prices.insurance.create')->with(['INSID'=>$INSID,'lab'=>$lab,'ins'=>$ins,'codes'=>$codes,'lbl_euro'=>$lbl_euro,'lbl_usd'=>$lbl_usd]);
}
   
public function extins_getPrices($lang,Request $request){
	$type = $request->type;
	switch($type){
		case 'create':
		  $codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL))');
	      $priced=$pricel=$pricee='';
		  if(isset($request->filter_lab) && $request->filter_lab!=''){
				$lab = ExtIns::find($request->filter_lab);
				$priced = $lab->priced;
				$pricel = $lab->pricel;
				$code_ids = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL)) and referred_tests=?',$request->filter_lab)->pluck('id')->toArray();
				$codes=$codes->whereIn('id',$code_ids);
				
				if(isset($request->get_codes) && $request->get_codes!=''){
				 $insert_codes = explode(',',$request->get_codes);
				 $code_ids = LabTests::whereIn('id',$insert_codes)->pluck('id')->toArray();
				 $codes=$codes->orWhereIn('id',$code_ids);
				 }
			 
			 }

			 $codes=$codes->orderBy('testord')->distinct(); 	 
			 
			 return Datatables::of($codes)
							->addIndexColumn()
							->addColumn('manual', function($row){
								  $btn='<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" value="'.$row->id.'" class="chk_manual" onchange="event.preventDefault();chg_state(this);"/><span class="slideon-slider"></span></label>';
								  return $btn;					  
							})
							->addColumn('totald', function($row) use($lang){
								  $btn = '<input type="text" size="7" id="totald" oninput="event.preventDefault();calcOnePrice(this);" onkeypress="return isNumberKey(event)" disabled="true" />';
								  return $btn;					  
							})
							->addColumn('totall', function($row){
								$btn = '<input type="text" size="10" id="totall" oninput="event.preventDefault();calcLBPrice(this);" onkeypress="return isNumberKey(event)" disabled="true" />';
								return $btn;
							})
							
							->rawColumns(['manual','totald','totall'])
							->with('priced', $priced)
							->with('pricel', $pricel)
							->toJson();
		break;
		case 'edit':
		 $pr_id = $request->pr_id;
		 $lab_prices = DB::table('tbl_referred_labs_prices')->where('lab_id',$pr_id)->get();
		 $manual_auto = $request->manual_auto;
		 $code_ids = DB::table('tbl_referred_labs_prices')->where('lab_id',$pr_id);
		 if(isset($manual_auto) && $manual_auto!=''){
			 if($manual_auto=='1'){
				 $code_ids = $code_ids->where('is_manual','Y');
			 }else{
				 $code_ids = $code_ids->where('is_manual','<>','Y');
			 }
		 }
		 
		 $code_ids = $code_ids->pluck('test_id')->toArray();
		 
		 $code_prices = array();
	     foreach($lab_prices as $v){
		     $tid = $v->test_id;
		     $code_prices[$tid]=array($v->is_manual,$v->totald,$v->totall);
            }
		 $codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL))');
		 $codes=$codes->whereIn('id',$code_ids);
		 if(isset($request->get_codes) && $request->get_codes!=''){
			$insert_codes = explode(',',$request->get_codes);
			$code_ids1 = LabTests::whereIn('id',$insert_codes)->pluck('id')->toArray();
			$codes=$codes->orWhereIn('id',$code_ids1);
			}
		 $codes = $codes->orderBy('testord')->distinct();
		 return Datatables::of($codes)
							->addIndexColumn()
							->addColumn('manual',function($row) use($code_prices){
								$btn = '';
								if(isset($code_prices[$row->id])){ 
								   $checked = $code_prices[$row->id][0]=='Y'?'checked':''; 	
								   $btn ='<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" value="'.$row->id.'" class="chk_manual" '.$checked.' onchange="event.preventDefault();chg_state(this);"/><span class="slideon-slider"></span></label>';
								}else{
								   $btn = '<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" value="'.$row->id.'" class="chk_manual"  onchange="event.preventDefault();chg_state(this);"/><span class="slideon-slider"></span></label>';
								} 
							   return $btn;
							
							})
							->addColumn('chk_one', function($row){
								  $btn='<input type="checkbox" class="chk-one"/>';
								  return $btn;					  
							})
							->addColumn('totald', function($row) use($code_prices){
								  $btn = ''; 
								  if(isset($code_prices[$row->id])){ 
										if($code_prices[$row->id][0]=='Y'){
											$btn='<input type="text" size="7" id="totald" onkeypress="return isNumberKey(event)" value="'.$code_prices[$row->id][1].'" oninput="event.preventDefault();calcOnePrice(this);"/>';
								         }else{	
											$btn='<input type="text" size="7" id="totald" onkeypress="return isNumberKey(event)" value="'.$code_prices[$row->id][1].'" oninput="event.preventDefault();calcOnePrice(this);" disabled="true" />';
								         }
								   }else{
											$btn='<input type="text" size="7" id="totald" onkeypress="return isNumberKey(event)" oninput="event.preventDefault();calcOnePrice(this);" disabled="true" />';
								       } 	
								  return $btn;					  
							})
							->addColumn('totall', function($row) use($code_prices){
                                  $btn = ''; 
								  if(isset($code_prices[$row->id])){ 
										if($code_prices[$row->id][0]=='Y'){
											$btn='<input type="text" size="10" id="totall" onkeypress="return isNumberKey(event)" value="'.$code_prices[$row->id][2].'" oninput="event.preventDefault();calcLBPrice(this);"/>';
								         }else{	
											$btn='<input type="text" size="10" id="totall" onkeypress="return isNumberKey(event)" value="'.$code_prices[$row->id][2].'" oninput="event.preventDefault();calcLBPrice(this);" disabled="true"/>';
								         }
								   }else{
											$btn='<input type="text" size="10" id="totall" onkeypress="return isNumberKey(event)" oninput="event.preventDefault();calcLBPrice(this);" disabled="true" />';
								       } 									
								return $btn;
							})
							->rawColumns(['chk_one','manual','totald','totall'])
							->toJson();
		break;
	}
	
}

public function  ins_addcodes($lang,Request $request){
	$referred_lab = $request->referred_lab;
	$chosen_codes = explode(',',$request->chosen_codes);
	//get all active codes group and not subgroup
	$codes = DB::table('tbl_lab_tests as t')->select('t.id as id','t.testord as testord','t.test_name as test_name','t.cnss as cnss','l.full_name as referred_lab')
	         ->whereRaw('t.active="Y" and (t.is_group="Y" or (t.is_group<>"Y" and group_num IS NULL)) and (referred_tests IS NULL or referred_tests<>?)',$referred_lab)
			 ->leftjoin('tbl_referred_labs as l','l.id','t.referred_tests');
    $codes=$codes->orderBy('t.testord')->distinct()->get();
	
	return Datatables::of($codes)
                	->addIndexColumn()
                    ->addColumn('choose', function($row) use($chosen_codes){
                          $checked = in_array($row->id,$chosen_codes)?'checked':'';
						  $btn='<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" value="'.$row->id.'" class="add_ins_code" '.$checked.'/><span class="slideon-slider"></span></label>';
						  return $btn;					  
                         })
					->rawColumns(['choose'])
                    ->toJson();
   }   
   
public function ins_store($lang,Request $request){
		$data = json_decode($request->manual,true);
		$currencyEUR=TblBillCurrency::where('active','O')->where('abreviation','EUR')->first();
        $lbl_eur = isset($currencyEUR)?$currencyEUR->price:95000;
		
		$ins_id = $request->ins_id;
		
		ExtIns::where('id',$ins_id)->update([
			'user_num'=>auth()->user()->id,
			'has_prices'=>'Y',
			'priced'=>$request->priced,
			'pricel'=>$request->pricel,
			'pricee'=>number_format((float)$request->pricel / $lbl_eur,2)
			]);
			
	foreach($data as $d){
			DB::table('tbl_referred_labs_prices')
			 ->insert([
			 'totall'=>$d['totall'],
			 'totald'=>$d['totald'],
			 'totale'=>number_format((float)$d['totall'] / $lbl_eur, 2),
			 'is_manual'=>$d['is_manual'],
			 'test_id'=>$d['test_id'],
			 'lab_id'=>$ins_id
			]);
		}			
	
	$location = route('ins_prices.edit',[$lang,$ins_id]);
	return response()->json(['success'=>__('Saved successfully'),'location'=>$location]);
}

public function ins_edit($lang,$id){
	   $pr= ExtIns::find($id);
	   $lab_prices = DB::table('tbl_referred_labs_prices')->where('lab_id',$id)->get();
	   $code_prices = array();
	   $code_tests = array();
	   
	   foreach($lab_prices as $v){
		  $tid = $v->test_id;
		  $code_prices[$tid]=array($v->is_manual,$v->totald,$v->totall);
          if(!in_array($tid,$code_tests)){
		    array_push($code_tests,$tid);
		  }			
	   }
	   
	   
       $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
	   $currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
	   $lbl_usd = isset($currUSD)? $currUSD->price:15000;
       $lbl_euro = isset($currEURO)? $currEURO->price:15000;
	   $user_clinic_num=auth()->user()->clinic_num;
	   $lab = Clinic::find($user_clinic_num);
	   
	   $codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL))');
	   $codes=$codes->whereIn('id',$code_tests);
       $codes = $codes->orderBy('testord')->get();
	   
	   $other_ins = ExtIns::where('id','<>',$pr->id)->where('clinic_num',$lab->id)->where('status','Y')->orderBy('id','desc')->get();
	   
	   
	   return view('prices.insurance.edit')->with(['pr'=>$pr,'lab'=>$lab,'codes'=>$codes,'lbl_euro'=>$lbl_euro,'lbl_usd'=>$lbl_usd,'code_prices'=>$code_prices,'other_ins'=>$other_ins]);
}

public function ins_update($lang,Request $request){
		$data = json_decode($request->manual,true);
		$currencyEUR=TblBillCurrency::where('active','O')->where('abreviation','EUR')->first();
        $lbl_eur = isset($currencyEUR)?$currencyEUR->price:95000;
		
		$ins_id = $request->id;
		ExtIns::where('id',$ins_id)->update([
			'user_num'=>auth()->user()->id,
			'has_prices'=>'Y',
			'priced'=>$request->priced,
			'pricel'=>$request->pricel,
			'pricee'=>number_format((float)$request->pricel / $lbl_eur,2)
			]);
			
		foreach($data as $d){
		 $exist = DB::table('tbl_referred_labs_prices')->where('lab_id',$ins_id)->where('test_id',$d['test_id'])->first();	
		 //dd($exist);
		 if(isset($exist)){
			DB::table('tbl_referred_labs_prices')->where('id',$exist->id)
			    ->update([ 'totall'=>$d['totall'],
				           'totald'=>$d['totald'],
						   'totale'=>number_format((float)$d['totall'] / $lbl_eur, 2),
						   'is_manual'=>$d['is_manual']]);
			}else{
				DB::table('tbl_referred_labs_prices')
				->insert([
				 'totall'=>$d['totall'],
				 'totald'=>$d['totald'],
				 'totale'=>number_format((float)$d['totall'] / $lbl_eur, 2),
				 'is_manual'=>$d['is_manual'],
				 'test_id'=>$d['test_id'],
				 'lab_id'=>$ins_id
				]);
			 }	
			
		}	
		
	    $location = route('ins_prices.edit',[$lang,$ins_id]);
		return response()->json(['success'=>__('Updated successfully'),'location'=>$location]);
}
	


public function ins_copy($lang,Request $request){
	$id = $request->modal_price_id;
	$my_lab =  ExtIns::find($id);
	$labs = $request->input('ins');
	$tests = explode(',',$request->testsCopy);
	$user_num = auth()->user()->id;
	//get only checked tests to copy prices to them
	$my_lab_prices = DB::table('tbl_referred_labs_prices')->whereIn('test_id',$tests)->where('lab_id',$id)->get();
	
	$currencyEUR=TblBillCurrency::where('active','O')->where('abreviation','EUR')->first();
    $lbl_eur = isset($currencyEUR)?$currencyEUR->price:95000;
	
	DB::beginTransaction();
    try {
         foreach ($labs as $lab_id) {
            $hasPrices = ExtIns::where('id',$lab_id)->value('has_prices');
			if($hasPrices=='N'){
				ExtIns::where('id',$lab_id)->update([
					'priced'=>$my_lab->priced,
					'pricel'=>$my_lab->pricel,
					'pricee'=>number_format((float)$my_lab->pricel / $lbl_eur,2),
					'has_prices'=>'Y',
					'user_num'=>$user_num
					]);
			   }
		    foreach($my_lab_prices as $p){
				$exist = DB::table('tbl_referred_labs_prices')->where('lab_id',$lab_id)->where('test_id',$p->test_id)->first();
				if(isset($exist)){
					DB::table('tbl_referred_labs_prices')->where('id',$exist->id)
					    ->update(['totall'=>$p->totall,
						          'totald'=>$p->totald,
								  'totale'=>number_format((float)$p->totall / $lbl_eur, 2),
								  'is_manual'=>$p->is_manual]);
				}else{
					DB::table('tbl_referred_labs_prices')
					   ->insert(['lab_id'=>$lab_id,
					             'test_id'=>$p->test_id,
								 'totall'=>$p->totall,
								 'totald'=>$p->totald,
								 'totale'=>number_format((float)$p->totall / $lbl_eur, 2),
								 'is_manual'=>$p->is_manual]);
				}
		      }
            
			
			}

            DB::commit();
            $msg = __('Copied successfully');
		    return response()->json(['status' => 'success','msg'=>$msg]);
        
		} catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
        }
	
}

public function lab_create($lang,$id){
	   
	   $lab = Clinic::find($id);
	   $codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL))')->orderBy('testord')->get();
	   $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
	   $currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
	   $lbl_usd = isset($currUSD)? $currUSD->price:15000;
       $lbl_euro = isset($currEURO)? $currEURO->price:15000;
	   return view('prices.labs.create')->with(['lab'=>$lab,'codes'=>$codes,'lbl_euro'=>$lbl_euro,'lbl_usd'=>$lbl_usd]);
   }
   
public function lab_store($lang,Request $request){
		$data = json_decode($request->manual,true);
		$lab_id = $request->lab_id;
		Clinic::where('id',$lab_id)->update([
			'priced'=>$request->priced,
			'pricel'=>$request->pricel,
			'has_prices'=>'Y',
			'user_num'=>auth()->user()->id
			]);
	   
	   foreach($data as $d){
			DB::table('tbl_clinics_prices')
			 ->insert([
			 'totall'=>$d['totall'],
			 'totald'=>$d['totald'],
			 'is_manual'=>$d['is_manual'],
			 'test_id'=>$d['test_id'],
			 'lab_id'=>$lab_id
			]);
		}			
	$location = route('lab_prices.edit',[$lang,$lab_id]);
	return response()->json(['success'=>__('Saved successfully'),'location'=>$location]);
}

public function lab_edit($lang,$id){
	   $pr= Clinic::find($id);
	   $lab_prices = DB::table('tbl_clinics_prices')->where('lab_id',$id)->get();
	   $code_prices = array();
	   
	   foreach($lab_prices as $v){
		  $tid = $v->test_id;
		  $code_prices[$tid]=array($v->is_manual,$v->totald,$v->totall); 
	   }
	   
	  
       $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
	   $currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
	   $lbl_usd = isset($currUSD)? $currUSD->price:15000;
       $lbl_euro = isset($currEURO)? $currEURO->price:15000;
	   
	   $user_clinic_num=auth()->user()->clinic_num;
	   $codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL))')->orderBy('testord')->get();
  	   return view('prices.labs.edit')->with(['pr'=>$pr,'codes'=>$codes,'lbl_euro'=>$lbl_euro,'lbl_usd'=>$lbl_usd,'code_prices'=>$code_prices]);
}

public function lab_update($lang,Request $request){
		$data = json_decode($request->manual,true);
		$lab_id = $request->id;
		Clinic::where('id',$lab_id)->update([
			'user_num'=>auth()->user()->id,
			'has_prices'=>'Y',
			'priced'=>$request->priced,
			'pricel'=>$request->pricel
			]);
			
	    foreach($data as $d){
		 $exist = DB::table('tbl_clinics_prices')->where('lab_id',$lab_id)->where('test_id',$d['test_id'])->first();	
		 //dd($exist);
		 if(isset($exist)){
			DB::table('tbl_clinics_prices')->where('id',$exist->id)->update([ 'totall'=>$d['totall'],'totald'=>$d['totald'],'is_manual'=>$d['is_manual']]);
			}else{
				DB::table('tbl_clinics_prices')
				->insert([
				 'totall'=>$d['totall'],
				 'totald'=>$d['totald'],
				 'is_manual'=>$d['is_manual'],
				 'test_id'=>$d['test_id'],
				 'lab_id'=>$lab_id
				]);
			 }	
			
		}	
		
		$location = route('lab_prices.edit',[$lang,$ins_id]);
		return response()->json(['success'=>__('Updated successfully'),'location'=>$location]);
}

public function doctor_getPrices($lang,Request $request){
	$codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL))');
	$priced=$pricel=$pricee='';
	if(isset($request->filter_doc) && $request->filter_doc!=''){
		$doctor = Doctor::find($request->filter_doc);
		$priced = $doctor->priced;
		$pricel = $doctor->pricel;
	 }

     $codes=$codes->orderBy('testord')->distinct(); 	 
	 
	 return Datatables::of($codes)
                    ->addIndexColumn()
                    ->addColumn('manual', function($row){
                          $btn='<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" value="'.$row->id.'" class="chk_manual" onchange="event.preventDefault();chg_state(this);"/><span class="slideon-slider"></span></label>';
						  return $btn;					  
                    })
					->addColumn('totald', function($row) use($lang){
                          $btn = '<input type="text" size="5" id="totald" onfocusout="event.preventDefault();calcOnePrice(this);" onkeypress="return isNumberKey(event)" disabled="true" />';
						  return $btn;					  
                    })
					->addColumn('totall', function($row){
						$btn = '<input type="text" size="8" id="totall" onkeypress="return isNumberKey(event)" disabled="true" />';
						return $btn;
					})
					->rawColumns(['manual','totald','totall'])
                    ->with('priced', $priced)
                    ->with('pricel', $pricel)
                    ->toJson();
}

public function doctor_create($lang,$id=NULL){
	   $user_clinic_num=auth()->user()->clinic_num;
	   $DOCID = NULL;
	   if(isset($id)){
		   $DOCID = $id;
	   }
	   $lab = Clinic::find($user_clinic_num);
	   $lab_prices = Doctor::where('has_prices','Y')->pluck('id')->toArray();
	   $cat_name = ($lang=='fr')?'cat.name_fr':'cat.name_en';
	   $ext_labs = Doctor::select('tbl_doctors.id',
	                              DB::raw("IF(tbl_doctors.middle_name!='' and tbl_doctors.middle_name IS NOT NULL,concat(tbl_doctors.first_name,' ',tbl_doctors.middle_name,' ',tbl_doctors.last_name),concat(tbl_doctors.first_name,' ',tbl_doctors.last_name)) as full_name"),
	                              DB::raw("IFNULL({$cat_name},'') as category_name"))
	               ->leftjoin('tbl_doctors_specia as cat','cat.id','tbl_doctors.specia')
				   ->whereNotIn('id',$lab_prices)->where('tbl_doctors.active','O')->orderBy('id','desc')->get();
	   
	   $codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL))')->orderBy('testord')->get();
	   $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
	   $currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
	   $lbl_usd = isset($currUSD)? $currUSD->price:15000;
       $lbl_euro = isset($currEURO)? $currEURO->price:15000;
	   return view('prices.doctors.create')->with(['DOCID'=>$DOCID,'lab'=>$lab,'ext_labs'=>$ext_labs,'codes'=>$codes,'lbl_euro'=>$lbl_euro,'lbl_usd'=>$lbl_usd]);
   }	

public function doctor_store($lang,Request $request){
		
		$data = json_decode($request->manual,true);
		$doc_id = $request->doc_id;
		Doctor::where('id',$doc_id)->update([
			'priced'=>$request->priced,
			'pricel'=>$request->pricel,
			'pricee'=>$request->pricee,
			'has_prices'=>'Y',
			'user_num'=>auth()->user()->id
			]);
			
		foreach($data as $d){
			DB::table('tbl_doctors_prices')
			 ->insert([
			 'totall'=>$d['totall'],
			 'totald'=>$d['totald'],
			 'totale'=>$d['totale'],
			 'is_manual'=>$d['is_manual'],
			 'test_id'=>$d['test_id'],
			 'lab_id'=>$doc_id
			]);
		}		
	
	$location = route('doctor_prices.edit',[$lang,$doc_id]);
	return response()->json(['success'=>__('Saved successfully'),'location'=>$location]);
}

public function doctor_edit($lang,$id){
	   
	   $pr = Doctor::find($id);
	   $lab_prices = DB::table('tbl_doctors_prices')->where('lab_id',$pr->id)->get();
	   $code_prices = array();
	   
	   foreach($lab_prices as $k=>$v){
		  $tid = $v->test_id;
		  $code_prices[$tid]=array($v->is_manual,$v->totald,$v->totall,$v->totale); 
	   }
	   
	   
       $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
	   $currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
	   $lbl_usd = isset($currUSD)? $currUSD->price:15000;
       $lbl_euro = isset($currEURO)? $currEURO->price:15000;
	   $user_clinic_num=auth()->user()->clinic_num;
	   $lab = Clinic::find($user_clinic_num);
	   $codes = LabTests::whereRaw('active="Y" and (is_group="Y" or (is_group<>"Y" and group_num IS NULL))');
	   $codes = $codes->orderBy('testord')->get();		
	   
	   $other_ext_labs = Doctor::where('id','<>',$pr->id)->where('active','O')->orderBy('id','desc')->get();
	   
	   $cats = DB::table('tbl_doctors_specia')->orderBy('id')->get();
	   
	   return view('prices.doctors.edit')->with(['pr'=>$pr,'lab'=>$lab,'codes'=>$codes,'lbl_euro'=>$lbl_euro,'lbl_usd'=>$lbl_usd,'code_prices'=>$code_prices,'other_ext_labs'=>$other_ext_labs,'cats'=>$cats]);
 }

public function doctor_update($lang,Request $request){
		$data = json_decode($request->manual,true);
		$id = $request->id;
		Doctor::where('id',$request->id)->update([
			'priced'=>$request->priced,
			'pricel'=>$request->pricel,
			'pricee'=>$request->pricee,
			'has_prices'=>'Y',
			'user_num'=>auth()->user()->id
			]);
	  
	  foreach($data as $d){
		 $exist = DB::table('tbl_doctors_prices')->where('lab_id',$id)->where('test_id',$d['test_id'])->first();	
		 //dd($exist);
		 if(isset($exist)){
			DB::table('tbl_doctors_prices')->where('id',$exist->id)->update([ 'totall'=>$d['totall'],'totald'=>$d['totald'],'totale'=>$d['totale'],'is_manual'=>$d['is_manual']]);
			}else{
				DB::table('tbl_doctors_prices')
				->insert([
				 'totall'=>$d['totall'],
				 'totald'=>$d['totald'],
				 'totale'=>$d['totale'],
				 'is_manual'=>$d['is_manual'],
				 'test_id'=>$d['test_id'],
				 'lab_id'=>$id
				]);
			 }	
			
		}		
	    $location = route('doctor_prices.edit',[$lang,$id]);
		return response()->json(['success'=>__('Updated successfully'),'location'=>$location]);
	}
	


public function doctor_copy($lang,Request $request){
	$id = $request->modal_price_id;
	$my_lab =  Doctor::find($id);
	$docs = $request->input('doctor');
	$tests = explode(',',$request->testsCopy);
	$user_num = auth()->user()->id;
	//get only checked tests to copy prices to them
	$my_lab_prices = DB::table('tbl_doctors_prices')->whereIn('test_id',$tests)->where('lab_id',$id)->get();
	
	foreach($docs as $doc_id){
		$hasPrices = Doctor::where('id',$doc_id)->value('has_prices');
		if($hasPrices=='N'){
			Doctor::where('id',$doc_id)->update([
				'priced'=>$my_lab->priced,
				'pricel'=>$my_lab->pricel,
				'pricee'=>$my_lab->pricee,
				'has_prices'=>'Y',
				'user_num'=>$user_num
				]);
		   }
	
	    foreach($my_lab_prices as $p){
			$exist = DB::table('tbl_doctors_prices')->where('lab_id',$doc_id)->where('test_id',$p->test_id)->first();
			if(isset($exist)){
				DB::table('tbl_doctors_prices')->where('id',$exist->id)->update(['totall'=>$p->totall,'totald'=>$p->totald,'totale'=>$p->totale,'is_manual'=>$p->is_manual]);
			}else{
				DB::table('tbl_doctors_prices')->insert(['lab_id'=>$doc_id,'test_id'=>$p->test_id,'totall'=>$p->totall,'totald'=>$p->totald,'totale'=>$p->totale,'is_manual'=>$p->is_manual]);
			}
		}
	
	
	}
	
	
	$msg = __('Copied successfully');
	
	return response()->json(['success'=>$msg]);
	
}

public function extlab_export($lang,Request $request){
	$id = $request->id;
	$lab = ExtLab::find($id);
	$prices = json_decode($lab->prices,true);
	foreach($prices as $p){
		DB::table('tbl_external_labs_prices')->insert([
		 'lab_id'=>$lab->id,
		 'test_id'=>$p['test_id'],
		 'is_manual'=>$p['is_manual'],
		 'totall'=>$p['totall'],
		 'totald'=>$p['totald'],
		 'totale'=>$p['totale']
		]);
	}
	return response()->json(["success"=>true]);
} 
	
}	