<?php
/*
* DEV APP
* Created date : 5-1-2023
*  
* update functions from time to time till today
*
*/
namespace App\Http\Controllers\resources;

use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\User;
use App\Models\RLDoctorClinic;
use App\Models\TblCategories;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Alert;
use Illuminate\Support\Facades\DB;
use App\Models\TblBillCurrency;
use Session;


class BranchesController extends Controller
{
	
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	
	/**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($lang)
    {
 	    $user_admin_perm = auth()->user()->admin_perm;
		$user_perm = auth()->user()->permission;
		$user_type = auth()->user()->type;
		
		return view('branches.index')->with(['user_type'=>$user_type,'user_perm'=>$user_perm,'user_admin_perm'=>$user_admin_perm]);

														
    }
	
	public function get_branches($lang,Request $request){
		
		
		if(isset($request->branch_status) && $request->branch_status!=''){
		  $clinics=Clinic::where('active',$request->branch_status)->Orderby('id','desc')->get(['tbl_clinics.*']);	
		}else{
		  $clinics=Clinic::Orderby('id','desc')->get(['tbl_clinics.*']);	
		}
		
		$user_type=auth()->user()->type;
		$user_admin_perm=auth()->user()->admin_perm;
		$user_perm = auth()->user()->permission;
		
		$data = $clinics->map(function ($clinic,$index)  
	                   use($lang,$user_perm,$user_admin_perm,$user_type){
						$count=$index + 1;   
					    $kind='';
						switch($clinic->kind){
							case 'lab': $kind=__('Lab'); break;
							case 'hospl': $kind=__('Hospital Lab'); break;
						}
						$name = $clinic->full_name;
						$priced = isset($clinic->priced)? $clinic->priced : '0';
						$pricel = isset($clinic->pricel)? $clinic->pricel : '0';
						$address = isset($clinic->full_address) && $clinic->full_address!=''? $clinic->full_address:'';
					    $tel = isset($clinic->telephone) && $clinic->telephone!=''?$clinic->telephone:'';
						$tel1 = isset($clinic->alternate_phone1) && $clinic->alternate_phone1!=''?$clinic->alternate_phone1:'';
						$tel2 = isset($clinic->alternate_phone2) && $clinic->alternate_phone2!=''?$clinic->alternate_phone2:'';
					    $email = isset($clinic->email) && $clinic->email!=''?$clinic->email:'';
						$fax = isset($clinic->fax) && $clinic->fax!=''?$clinic->fax:'';
					    $status='';
						  if($clinic->active=='O'){
							  $status='<span class="badge text-active">'.__('Active').'</span>'; 
						  }else{
							  $status='<span class="badge text-inactive">'.__('InActive').'</span>';
						  }
						
						$action='<a href="#showProfileModal" class="btn btn-xs btn-clean btn-icon  btn-action" data-toggle="modal"  data-target="#showProfileModal" onclick="showProfile('.$clinic->id.')">'.__('Show').'&#xA0;<i class="far fa-eye"></i></a>';

						if($user_admin_perm=='O' || ($user_type=='2' && ($user_perm=='S' || $user_perm=='A'))){  	
						   $disabled= ($clinic->active=='O')?'':'disabled'; 	
							if($clinic->active=='O'){
								if($clinic->has_prices=='Y'){
									 $action.='<a href="'.route('lab_prices.edit',[$lang,$clinic->id]).'" class="ml-1 btn btn-xs btn-clean btn-icon btn-action" title="'.__('Edit prices').'">Edit<i class="ml-1 fas fa-dollar-sign"></i></a>';
							     }else{
									 $action.='<a href="'.route('lab_prices.create',[$lang,$clinic->id]).'" class="ml-1 btn btn-xs btn-clean btn-icon btn-action" title="'.__('Create prices').'">Create<i class="ml-1 fas fa-dollar-sign"></i></a>';
							      } 
							}		
					       $action.='<a href="'.route('branches.edit',[$lang,$clinic->id]).'"   class="ml-1 btn btn-sm btn-clean btn-icon '.$disabled.'" title="'.__('Edit').'"><i class="text-primary far fa-edit"></i></a>';
						   $checked = ($clinic->active=='O')?'checked':'';
						   $action.='<label class="mt-2 ml-1 slideon slideon-xs  slideon-success"><input type="checkbox" class="toggle-chk" data-id="'.$clinic->id.'" '.$checked.'/><span class="slideon-slider"></span></label>';
						  }
						
						return [
							 'cnt'=>$count,
							 'kind'=>$kind,
							 'name'=>$name,
							 'priced'=>$priced,
							 'pricel'=>$pricel,
							 'address'=>$address,
							 'tel'=>$tel,
							 'tel1'=>$tel1,
							 'tel2'=>$tel2,
							 'email'=>$email,
							 'fax'=>$fax,
							 'status'=>$status,
							 'actions'=>$action
					      ];						
						 
					   });
	
	
	return response()->json(['data'=>$data]);
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($lang)
    {
		
		$currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
		$currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
		$lbl_usd = isset($currUSD)? $currUSD->price:15000;
        $lbl_euro = isset($currEURO)? $currEURO->price:15000;
		//get menus super admin clinic
		$menus = DB::table('users_profiles')->where('name_en','Super Admin')->pluck('access_menus')[0];
		$menus = explode(',',$menus);
		return view('branches.create')->with(['menus'=>$menus,'lbl_usd'=>$lbl_usd,
											 'lbl_euro'=>$lbl_euro]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($lang,Request $request)
    {
        		
		$validated = $request->validate([
											'name' => ['required', 'string', 'max:255'],
											'telephone' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
											'fax' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
											'whatsapp' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
											'email' => ['nullable','string', 'email', 'max:255','unique:tbl_clinics'],
											'pricel'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/'],
											'priced'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/'],
											'pricee'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/']
                                        ],[
										   'name.required' =>__('Please fill a branch name'),
										   'email.unique'=>__('Email already exists for another branch profile'),
										   'email.email' => __('Email is invalid'),
										   'telephone.regex' =>__('Enter a phone number in this format: 00-000000'),
										   'whatsapp.regex' =>__('Enter a phone number in this format: 00-000000'),
										   'fax.regex' => __('Enter fax number in this format: 00-000000'),
										   'pricel.regex' => __('Enter price lbp in this format: 0.00'),
										   'priced.regex' => __('Enter price $ in this format: 0.00'),
										   'pricee.regex' => __('Enter price euro in this format: 0.00')
									  
										  ]); 
        if($validated){
		
		$id=Clinic::create([
							   'full_name' => trim($request->name),
							   'full_address' => trim($request->address),
							   'city_name' => trim($request->city_name),
							   'zip_code' => $request->zip_code,
							   'province_code' => $request->province_code,
							   'country_code' => $request->country_code,
							   'region_name' => $request->region_name,
							   'telephone' =>str_replace("-","",$request->telephone),
							   'fax' => str_replace("-","",$request->fax),
							   'email' => $request->email,
							   'kind' => $request->type,
							   'remarks' => $request->remarks,
							   'appt_nb'=>$request->appt_nb,
							   'state'=>$request->state,
							   'alternate_phone1'=>str_replace("-","",$request->alternate_phone1),
							   'alternate_phone2'=>str_replace("-","",$request->alternate_phone2),
							   'whatsapp'=>str_replace("-","",$request->whatsapp),
							   'website'=>trim($request->website),
							   'pricel' => $request->pricel,
							   'priced' => $request->priced,
							   'pricee'=>$request->pricee,
							   'active' => 'O',
							   'user_num' => auth()->user()->id ] )->id;
		        		   
		           $clinic=Clinic::find($id);
			       $msg=__('New Branch Added Successfully.');
					   
				   Alert::toast($msg, 'success');
			      // redirect user
				  return redirect(route('branches.edit',[$lang,$clinic->id]));     
			  }
		  
       }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($lang,$id)
    {
			 $clinic=Clinic::find($id);
			 $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
		     $currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
		    $lbl_usd = isset($currUSD)? $currUSD->price:15000;
            $lbl_euro = isset($currEURO)? $currEURO->price:15000;
			 
		     if(isset($clinic->clinic_user_num))
			 {
				 $user= User::where('active','O')->where('id',$clinic->clinic_user_num)->first();
		     }
		    else{
				$user= User::join('tbl_clinics as clin','clin.id','users.clinic_num')
				       ->where('clin.active','O')->where('users.active','O')
					   ->where('users.clinic_num',$clinic->id)->first();
				}
				
				//get menus super admin clinic
				$menus = DB::table('users_profiles')->where('name_en','Super Admin')->pluck('access_menus')[0];
				$menus = explode(',',$menus);
		
				
				
			if(isset($user)){
		
		        return view('branches.create')->with([
													'clinic'=>$clinic,
												    'user' => $user,
													'menus'=>$menus,
													'lbl_usd'=>$lbl_usd,
											        'lbl_euro'=>$lbl_euro
												     ]);
			}else{
				
				return view('branches.create')->with([
													  'clinic'=>$clinic,
													  'menus'=>$menus,
													  'lbl_usd'=>$lbl_usd,
											          'lbl_euro'=>$lbl_euro
													]);
			}
		
    }
    /**
     * Show the form for editing the specified resource.
     *Not used version
     * @param  int  $id
     * @return Response
     */
    public function show($lang,$id)
    {
		
		
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($lang,Request $request,$id)
    {
    
	$clinic=Clinic::find($id);
	$msg=__('No update is done.');
		 
	if(isset($request->update_clinic)){
		 
		 $validated = $request->validate([
                                          'name' => ['required', 'string', 'max:255'],
										  'email' => ['nullable','email',Rule::unique('tbl_clinics')->ignore($clinic->id)],
                                          'telephone' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
                                          'fax' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
										  'whatsapp' => ['nullable','regex:/^[0-9]{2}-[0-9]{6}$/'],
										  'pricel'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/'],
										  'priced'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/'],
										  'pricee'=>['nullable','regex:/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/']
										  
                                          ],[
										   'name.required' =>__('Please fill a branch name'),
										   'email.unique'=>__('Email already exists for another branch profile'),
										   'email.email' => __('Email is invalid'),
										   'telephone.regex' =>__('Enter a phone number in this format: 00-000000'),
										   'fax.regex' => __('Enter fax number in this format: 00-000000'),
										   'whatsapp.regex' => __('Enter fax number in this format: 00-000000'),
										   'pricel.regex' => __('Enter price lbp in this format: 0.00'),
										   'priced.regex' => __('Enter price $ in this format: 0.00'),
										   'pricee.regex' => __('Enter price euro in this format: 0.00')
										  
										  ]); 
		
	
			$clinic->update([
							   'full_name' => trim($request->name),
							   'full_address' => trim($request->address),
							   'city_name' => trim($request->city_name),
							   'zip_code' => $request->zip_code,
							   'province_code' => $request->province_code,
							   'country_code' => $request->country_code,
							   'region_name' => $request->region_name,
							   'telephone' =>str_replace("-","",$request->telephone),
							   'fax' => str_replace("-","",$request->fax),
							   'email' => $request->email,
							   'kind' => $request->type,
							   'remarks' => $request->remarks,
							   'appt_nb'=>$request->appt_nb,
							   'state'=>$request->state,
							   'alternate_phone1'=>str_replace("-","",$request->alternate_phone1),
							   'alternate_phone2'=>str_replace("-","",$request->alternate_phone2),
							   'whatsapp'=>str_replace("-","",$request->whatsapp),
							   'pricel' => $request->pricel,
							   'priced' => $request->priced,
							   'pricee'=>$request->pricee,
							   'website'=>trim($request->website),
							   'user_num' => auth()->user()->id
							]);
			
				  $msg=__('Branch Updated Successfully.');
		     
			  }
		      
		        
		if($request->has('update_account')) {
			$request->validate([
           	                    'username' => ['required', 'string', 'max:255','unique:users'],
			                    'password' => ['required', 'string', 'min:6'],
								'user_email' => ['nullable','email']
                               ],
							   [
								'username.required' => __('Please fill a username'),
								'username.unique' => __('Username already exists'),
								'password.required' => __('Please fill your password'),
								'password.min' => __('Password must be at least 6 characters'),
								'user_email.email' => __('Email is invalid')
							   ]);
							   
			$cnt_existing_email = User::where('email',$request->user_email)->get()->count();
			if($cnt_existing_email>0){
				return back()->withErrors(["user_email"=>__('Email already exists')]);
			}
			
			$user_name = $request->Prefix_Username.$request->username;
			$count_user_name=User::where('username',$user_name)->get()->count();
			if($count_user_name!=0){
				return back()->withErrors(['username'=>__('Username already exists!!')]);
			}
			
			$user_id=User::create([
									'type' =>2,
									'user_kind'=>'main_lab',
									'fname' => $clinic->name,
									'lname' =>  $clinic->name,
									'username' =>$user_name,
									'email' => $request->user_email,
									'password' => Hash::make($request->password),
									'permission' => $request->permission,
									'admin_perm' => 'N',
									'clinic_num'=> $clinic->id,
									'active' => 'O'
								   ])->id;
									
			//link first clinical account with super user account						
			Clinic::where('id',$clinic->id)->update(['clinic_user_num' => $user_id,'user_num' => auth()->user()->id]);
            $msg=__('Account Created Successfully.');
             			
		         }
         
	   
		  if($request->has('update_hours')){ 
		 
			$arr=array();
		    $monday = $tuesday = $wednesday = $thursday = $friday = $saturday = $sunday = null;   
		  
			if(null!==$request->start_time0 && null!==$request->end_time0){
				$monday=$request->start_time0."-".$request->end_time0;
			}
			if(null!==$request->start_time1 && null!==$request->end_time1){
				$tuesday=$request->start_time1."-".$request->end_time1;
			}
			if(null!==$request->start_time2 && null!==$request->end_time2){
				$wednesday=$request->start_time2."-".$request->end_time2;
			}
			if(null!==$request->start_time3 && null!==$request->end_time3){
				$thursday=$request->start_time3."-".$request->end_time3;
			}
			if(null!==$request->start_time4 && null!==$request->end_time4){
				$friday=$request->start_time4."-".$request->end_time4;
			}
			if(null!==$request->start_time5 && null!==$request->end_time5){
				$saturday=$request->start_time5."-".$request->end_time5;
			}
			if(null!==$request->start_time6 && null!==$request->end_time6){
				$sunday=$request->start_time6."-".$request->end_time6;
			}
			
			if(null!==$monday or null!==$tuesday or null!==$wednesday 
			   or null!==$thursday or null!==$friday or null!==$saturday or null!==$sunday){   
			
				$arr= array("Monday"=>$monday,
							"Tuesday" => $tuesday,
							"Wednesday" => $wednesday,
							"Thursday" => $thursday,
							"Friday" => $friday,
							"Saturday" => $saturday,
							"Sunday" => $sunday);
			   } 
			  $result = (empty($arr))? NULL:json_encode($arr);  
			 Clinic::where('id',$clinic->id)->update(['open_hours' => $result,'user_num' => auth()->user()->id]);
			 $msg=__('Schedule updated successfully');
			  			
		  
		  
		  }
		   		
         Alert::toast($msg, 'success');
        // redirect user
        return back();
						
						
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
     public function destroy($lang,Request $request,$id)
    {
	  
			   
	if($request->has('destroy_openingHours')){
		Clinic::where('id',$id)->update(['open_hours'=>NULL,'user_num' => auth()->user()->id]);
		$msg=__('Schedule deleted successfully');
		Alert::toast($msg, 'success');
		return back();
		}  
		
      if($request->ajax()){ 
			if($request->checked=='N'){
							$res=Clinic::where('id',$id)->update(['active'=>'N','user_num' => auth()->user()->id]);
							if($res){
																				 
								  User::where('clinic_num',$id)->update(['active'=>'N']);
								  //deactivate all clinic users permissions
								  //Permission::where('id_business',$id)->update(['active'=>'N','user_id' => auth()->user()->id]);

							// redirect user
							$msg=__('Branch Deactivated Successfully.');

							Alert::toast($msg, 'success');
							$url = route('branches.index',$lang);
							return response()->json(["url"=>$url]);
							}
					}		
				   
			if($request->checked=='O'){
							 $res=Clinic::where('id',$id)->update(['active'=>'O','user_num' => auth()->user()->id]);
							//dd($res);
							if($res){
							   User::where('clinic_num',$id)->update(['active'=>'O']);
							   //activate all clinic users permissions
							  //Permission::where('id_business',$id)->update(['active'=>'O','user_id' => auth()->user()->id]);
							 // redirect user
							 $msg=__('Branch Activated Successfully.');
							Alert::toast($msg, 'success');
							$url = route('branches.index',$lang);
							return response()->json(["url"=>$url]);
							} 
							  
							  
						  }	
	        }						  
		    
       }
	
      



}
