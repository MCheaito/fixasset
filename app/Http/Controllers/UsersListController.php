<?php
/*
*DEV APP
* Created date : 15-12-2022
*
*/
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Clinic;
use App\Models\ExtLab;
use App\Models\Permission;
use App\Models\TblInventoryClients;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use DB;
use Alert;
use Carbon\Carbon;

class UsersListController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	/**
     * Display all users
     * 
     * @return \Illuminate\Http\Response
     */
    public function index($lang) 
    {
        $user_type=auth()->user()->type;
		$user_perm=auth()->user()->permission;
		$user_admin_perm = auth()->user()->admin_perm;
	
	return view('users_list.index')->with(['user_type'=>$user_type,'user_perm'=>$user_perm,'user_admin_perm'=>$user_admin_perm]);

    }
	
	//5-9-2024
	//get users list
	public function get_users($lang,Request $request){
		$user_type=auth()->user()->type;
		$uid=auth()->user()->id;
		$user_admin_perm=auth()->user()->admin_perm;
		$user_perm = auth()->user()->permission;
		$lab_num = auth()->user()->clinic_num;
		
		switch($user_type){
			//guarantor
			case 3: 
			 $users = User::where('id',$uid);  
			break;
			//clinic account
			case 2:
			  if($user_admin_perm=='O'){
				$users = User::where('type','<>',4);
				}else{
				  
				  switch($user_perm){
					case 'S':
					 $users = User::where('clinic_num',$lab_num)
							  ->where('type','<>',4)
							  ->orderBy('id','DESC');
					 
					break;
					case 'A':
					 $users = User::where('clinic_num',$lab_num)
						   ->where('permission','<>','S')
						    ->where('type','<>',4);
					break;
					case 'U':
					  $users = User::where('clinic_num',$lab_num)
							 ->where('permission','U')
							 ->where('type','<>',4)
							 ->where('id',$uid);
					break;
				  }
				}
			break;
		}
	
	if(isset($request->user_status) && $request->user_status!=''){
		$users = $users->where('active',$request->user_status);
	}
	
	if(isset($request->user_type) && $request->user_type!=''){
		$users = $users->where('type',$request->user_type);
	}
	
	$users = $users->orderBy('id','desc')->get();
	
	$data = $users->map(function ($user,$index)  
	                   use($lang,$user_perm,$user_admin_perm,$user_type){
						   $count=$index + 1;
						   $username = $user->username;
						   $name = $user->fname.' '.$user->lname;
						   $usertype='';
						   switch($user->type){
							   case 1: $usertype=__('External Doctor'); break;
							   case 2: $usertype=__('Internal Lab'); break;
							   case 3: $usertype=__('Guarantor'); break;
							   case 4: $usertype=__('Patient'); break;
						   }
						  $email=(isset($user->email) && $user->email!='')?$user->email:'';
						  $userpermission='';
						  switch($user->permission){
							  case 'A': $userpermission=__('Admin'); break;
							  case 'S': $userpermission=__('Super user'); break;
							  case 'U': $userpermission=__('User'); break;
							  case 'D': $userpermission= __('External Doctor'); break;
							  case 'L': $userpermission= __('Guarantor'); break;
							  case 'P': $userpermission= __('Patient'); break;
						  }
  						  $status='';
						  if($user->active=='O'){
							  $status='<span class="badge text-active">'.__('Active').'</span>'; 
						  }else{
							  $status='<span class="badge text-inactive">'.__('InActive').'</span>';
						  }
					     $action = '';
						 $disabled = ($user->active=='O')?'':'disabled';
						 
						 $action.='<a href="'.route('userslist.edit',[$lang,$user->id]).'" class="btn btn-sm btn-clean btn-icon '.$disabled.'" title="'.__('Edit').'"><i  class="text-primary far fa-edit"></i></a>'; 
						
						 if(($user_type==2 && ($user_perm=='S' || $user_perm=='A')) || ($user_admin_perm=='O') ){
							 $checked=($user->active=='O')?'checked':'';
							 $action.='<label class="mt-2 ml-1 slideon slideon-xs  slideon-success"><input type="checkbox" class="toggle-chk" data-id="'.$user->id.'" '.$checked.'/><span class="slideon-slider"></span></label>';
						 }
						 
						
						 return [
							 'cnt'=>$count,
							 'username'=>$username,
							 'name'=>$name,
							 'usertype'=>$usertype,
							 'email'=>$email,
							 'userpermission'=>$userpermission,
							 'status'=>$status,
							 'actions'=>$action
					      ];
					   
					   });
	
	
	
	return response()->json(['data'=>$data]);
	}
	
    /**
     * Show form for creating user
     * 
     * @return \Illuminate\Http\Response
     */
    public function create($lang) 
    {
        //0 all profiles, 3: ressource profile, 4: external lab profile
		$profiles = DB::table('users_profiles')->whereNotIn('prof_id',[0,3,4])->orderBy('id')->get();
		$menus = DB::table('users_profiles')->where('prof_id',0)->first();
		$menus = explode(",",$menus->access_menus);
		$user = User::where('user_kind','main_lab')->first();
		$clinics = Clinic::select('id','full_name')->where('id',$user->clinic_num)->orderBy('id','desc')->orderBy('full_name')->get();
		$clients = TblInventoryClients::select('id','name')->where('active','O')->orderBy('id','desc')->orderBy('name')->get();


		
 	 return view('users_list.create')->with(['clients'=>$clients,'clinics'=>$clinics,'profiles'=>$profiles,'menus'=>$menus]);
	   
    }
	
	public function create_doctor($lang) 
    {
        
		
		//0 all profiles, 3: ressource profile, 4: external lab profile
		$profiles = DB::table('users_profiles')->where('prof_id',3)->orderBy('id')->get();
		$menus = DB::table('users_profiles')->where('prof_id',3)->first();
		$menus = explode(",",$menus->access_menus);
		
		$user = User::where('user_kind','main_lab')->first();
		$clinics = Clinic::select('id','full_name')->where('id',$user->clinic_num)->orderBy('id','desc')->orderBy('full_name')->get();
		
		$doctors = Doctor::select('id',DB::raw("CONCAT(first_name,' ',IFNULL(middle_name,''),' ',last_name) as full_name"))->where('active','O')->orderBy('id','desc')->get();
		
		
		
		return view('users_list.create_doctor')->with(['clinics'=>$clinics,'doctors'=>$doctors,'profiles'=>$profiles,'menus'=>$menus]);
	   
    }
	
	public function create_lab($lang) 
    {
        
		
		//0 all profiles, 3: ressource profile, 4: external lab profile
		$profiles = DB::table('users_profiles')->where('prof_id',4)->orderBy('id')->get();
		$menus = DB::table('users_profiles')->where('prof_id',4)->first();
		$menus = explode(",",$menus->access_menus);
		
		$user = User::where('user_kind','main_lab')->first();
		$clinics = Clinic::select('id','full_name')->where('id',$user->clinic_num)->orderBy('id','desc')->orderBy('full_name')->get();
		//get active guarantors without existing user name
		$ext_labs = ExtLab::select('id','full_name')->whereRaw('status="A" and (lab_user_num IS NULL or lab_user_num="")')->orderBy('full_name')->get();
		$clients = TblInventoryClients::select('id','name')->where('active','O')->orderBy('id','desc')->orderBy('name')->get();

		
		
		return view('users_list.create_lab')->with(['clients'=>$clients,'clinics'=>$clinics,'clinics'=>$clinics,'ext_labs'=>$ext_labs,'profiles'=>$profiles,'menus'=>$menus]);
	   
    }

    /**
     * Store a newly created user
     * 
     * @param User $user
     * @param StoreUserRequest $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function store($lang,Request $request) 
    {
        $type=$request->acc_type;
		
		$clients=json_encode($request->get('clients_code'));
		if($type==3){
			//guarantor
			$request->validate([
			'ext_lab_num'=>['required'],
			'fname' => ['required', 'string', 'max:255'],
			'lname' => ['required', 'string', 'max:255'],
			'email' => ['nullable','string', 'email', 'max:255','unique:users'],
			'username' => ['required', 'string', 'max:255', 'unique:users'],
			'password' => ['required', 'string', 'min:6', 'confirmed'],
            ],
			[
			'fname.required' => __('Please fill your first name'),
			'lname.required' => __('Please fill your last name'),
			'email.unique' => __('Email already exists'),
			'email.email' => __('Email is invalid!'),
			'username.required' => __('Please fill a username'),
			'username.unique' => __('Username already exists'),
			'password.required' => __('Please fill your password'),
			'password.confirmed' => __('Password confirmation does not match'),
			]
						
			);
		  }else{
		   //internal lab
		   $request->validate([
            'clinic_num'=>['required'],
			'fname' => ['required', 'string', 'max:255'],
			'lname' => ['required', 'string', 'max:255'],
			'email' => ['nullable','string', 'email', 'max:255','unique:users'],
			'username' => ['required', 'string', 'max:255', 'unique:users'],
			'password' => ['required', 'string', 'min:6', 'confirmed'],
			'permission' =>['required']
            ],
			[
			'clinic_num.required' => __('Please fill your lab name'),
			'fname.required' => __('Please fill your first name'),
			'lname.required' => __('Please fill your last name'),
			'email.unique' => __('Email already exists'),
			'email.email' => __('Email is invalid!'),
			'username.required' => __('Please fill a username'),
			'username.unique' => __('Username already exists'),
			'password.required' => __('Please fill your password'),
			'password.confirmed' => __('Password confirmation does not match'),
			'permission.required' => __('Please fill a permission')
			]
						
			);
		}
		
		
		
		  		 
		  $fname = $request->fname;
		  $lname = $request->lname;
          $email = $request->email;
          $password = Hash::make($request->password);
		  $username = $request->username;   
		 
			switch($type){	
		     case 3:
			  $new_user=User::create(['fname'=>$fname,'lname'=>$lname,'email'=>$email,'username'=>$username,
				               'type'=>$type,'password'=>$password,'permission'=>'L','admin_perm'=>'N',
							   'active'=>'O','clinic_num'=>$request->clinic_num,'user_kind'=>'external_lab'])->id;
              //update exterlab_user_num in table doctor
			  ExtLab::where('id',$request->ext_lab_num)->update(['lab_user_num'=>$new_user,'user_num'=>auth()->user()->id]);
			  $profile_permissions = $request->get('menu');
			  if(!empty($profile_permissions)){
				  Permission::create([
				   'uid'=>$new_user,
				   'clinic_num'=>$request->clinic_num,
				   'clients'=>$clients,
				   'profile_permissions'=>implode(",",$profile_permissions ),
				   'active'=>'O',
				   'user_num'=>auth()->user()->id
				  ]);
			    }
               break;
			  case 2:
			  $new_user=User::create(['fname'=>$fname,'lname'=>$lname,'email'=>$email,'username'=>$username,
				               'type'=>$type,'password'=>$password,'permission'=>$request->permission,'admin_perm'=>'N',
							   'active'=>'O','clinic_num'=> $request->clinic_num,'user_kind'=>'internal_lab' ])->id;
              $profile_permissions = $request->get('menu');
			  if(!empty($profile_permissions)){
				  Permission::create([
				   'uid'=>$new_user,
				   'clinic_num'=> $request->clinic_num,
  				   'clients'=>$clients,
				   'profile_permissions'=>implode(",",$profile_permissions ),
				   'active'=>'O',
				   'user_num'=>auth()->user()->id
				  ]);
			    }
               break;
			  
			}
        
		return redirect()->route('userslist.index',app()->getLocale())
            ->withSuccess(__('User created successfully.'));
    
  }

   

    /**
     * Edit user data
     * 
     * @param User $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function edit($lang,$id) 
    {
       
		$user =User::find($id);
		
		if($user->type==3){
		$profiles = DB::table('users_profiles')->where('prof_id',4)->orderBy('id')->get();
		$menus = DB::table('users_profiles')->where('prof_id',4)->first();
		$menus = explode(",",$menus->access_menus);
        $clinic = Clinic::where('id',$user->clinic_num)->where('active','O')->orderBy('id','DESC')->first();		
		$permission = Permission::where('uid',$user->id)->where('active','O')->first();
		
		$clients_code = json_decode($permission->clients,true);
	    $clients=DB::table('tbl_inventory_clients')->where('active','O')->orderBy('id')->get();
		return view('users_list.edit', compact('user'))->with(['clients_code'=>$clients_code,'clients'=>$clients,'clinic'=>$clinic,'profiles'=>$profiles,'menus'=>$menus,'permission'=>$permission]);
		}else{
		$profiles = DB::table('users_profiles')->whereNotIn('prof_id',[0,3,4])->orderBy('id')->get();
		$menus = DB::table('users_profiles')->where('prof_id',0)->first();
		$menus = explode(",",$menus->access_menus);	
		$clinic = Clinic::where('id',$user->clinic_num)->where('active','O')->orderBy('id','DESC')->first();
		$permission = Permission::where('uid',$user->id)->where('clinic_num',$user->clinic_num)->where('active','O')->first();
		$clients_code = json_decode($permission->clients,true);
		$clients=DB::table('tbl_inventory_clients')->where('active','O')->orderBy('id')->get();
		return view('users_list.edit', compact('user'))->with(['clients_code'=>$clients_code,'clients'=>$clients,'clinic'=>$clinic,'profiles'=>$profiles,'menus'=>$menus,'permission'=>$permission]);
	    }
		
	
		
    }

    /**
     * Update user data
     * 
     * @param User $user
     * @param UpdateUserRequest $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function update($lang,Request $request) 
    {
        
		$id = $request->id;
		$user = User::find($id);
        $clients=json_encode($request->get('clients_code'));
		if($user->type==3){
			
			$request->validate([
            'fname' => ['required','string', 'max:255'],
			'lname' => ['required', 'string', 'max:255'],
			'email' => ['nullable','email',Rule::unique('users')->ignore($id)],
			'username' => ['required','string', 'max:255',Rule::unique('users')->ignore($id)],
			'password' => ['nullable', 'string', 'min:6', 'confirmed']
			 ],
			 [
			'fname.required' => __('Please fill your first name'),
			'lname.required' => __('Please fill your last name'),
			'email.unique' => __('Email already exists for another user'),
			'email.email' => __('Email is invalid!'),
			'username.required' => __('Please fill a username'),
			'username.unique' => __('Username already exists'),
			'password.confirmed' => __('Password confirmation does not match'),
			]);
			
			
		  }else{
		
		   $request->validate([
			'fname' => ['required','string', 'max:255'],
			'lname' => ['required', 'string', 'max:255'],
			'email' => ['nullable','email',Rule::unique('users')->ignore($id)],
			'username' => ['required','string', 'max:255',Rule::unique('users')->ignore($id)],
			'password' => ['nullable', 'string', 'min:6', 'confirmed'],
			'permission' =>['required']
			 ],
			 [
			'fname.required' => __('Please fill your first name'),
			'lname.required' => __('Please fill your last name'),
			'email.unique' => __('Email already exists for another user'),
			'email.email' => __('Email is invalid!'),
			'username.required' => __('Please fill a username'),
			'username.unique' => __('Username already exists'),
			'password.confirmed' => __('Password confirmation does not match'),
			'permission.required' => __('Please fill a permission')
			]);
		     
			
			 
			 //check if clinic has a user then update email in table users
			    $clinic = Clinic::where('active','O')->where('clinic_user_num',$user->id)->first();
				
			   if(isset($clinic) && isset($clinic->clinic_user_num)){
				  $count_existing_email = Clinic::where('id','<>',$clinic->id)->where('email',trim($request->email))->get()->count();
				 
				  if($count_existing_email){
				    return back()->withErrors(__('Email already exists for another lab profile'));	
				   }
			
			    Clinic::where('id',$clinic->id)->update(['email'=>$request->email]);
			   }
			 
			 } 
           
			 //if everything is ok then update user info
			 $user->fname = $request->fname;
			 $user->lname = $request->lname;
			 $user->email = $request->email;
		     if(isset($request->password) && $request->password!='')
			 { $user->password = Hash::make($request->password); }
			 
			 $user->username = $request->username;
			 if($user->type==3){  
			    $user->permission = 'L';	
			 }else{
				$user->permission = $request->permission; 
			 }  
						 
			 $user->save();
			 //check if user has a profile only by admin and super user clinic
			 if( auth()->user()->admin_perm=='O' || (auth()->user()->type==2 && auth()->user()->permission=='S' && $user->admin_perm !='O') ){
			 
				 $user_profile = Permission::where('uid',$user->id)->where('active','O')->first();
				 $profile_permissions = ($request->has('menu'))? implode(",",$request->get('menu')):NULL;
				 
				 
				 //if user has a profile 
				 if(isset($user_profile)){
				  //update user profile permissions
				
					      Permission::where('uid',$user->id)->where('clinic_num',$user->clinic_num)
									   ->where('active','O')
									   ->update([
										   'profile_permissions'=>$profile_permissions,
										    'clients'=>$clients,
										   'user_num'=>auth()->user()->id
										   ]);
						
				   }
				   
				   //if user have inactive profile or no profile and permissions are not empty
				   if(!isset($user_profile)){
					   //user without profile then create it
						
							   Permission::create([
											   'uid'=>$user->id,
											   'clinic_num'=>$user->clinic_num,
											   'profile_permissions'=>$profile_permissions,
											    'clients'=>$clients,
											   'user_num'=>auth()->user()->id,
											   'active'=>'O'
											   ]);
								   
						}
						
			 }
		  return redirect()->route('userslist.index',app()->getLocale())->withSuccess(__('User updated successfully.'));
		 
        }
    
	/**
	*Modify user password
	*/
     public function password_change($lang,Request $request) {
	   $validator= Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
	   
		User::where('id',$request->userID)->update(['password' => Hash::make($request->password)]);
	   
	   $msg=__('Password Updated Successfully.');  	
		  Alert::toast($msg, 'success');
		 return back();
	  
   }
	 
    /**
     * Delete user data
     * 
     * @param User $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy($lang,$id,Request $request) 
    {
       $id = $request->id;
	   $user=User::find($id);
	  if($request->ajax()){ 
	   
		   if($request->checked=='N'){
			 
			  $user->update(['active'=>'N']);
			  
			  if($user->type==3){
				  ExtLab::where('lab_user_num',$user->id)->update(['status'=>'N','user_num' => auth()->user()->id]);
			  }
			  Permission::where('uid',$user->id)->update(['active'=>'N','user_num' => auth()->user()->id]);
			  $url = route('userslist.index',app()->getLocale());
			  $msg = __('User InActivated successfully.');
			  Alert::toast($msg, 'success');
			 return response()->json(["url"=>$url]);
		   }
		  
		  if($request->checked=='O'){	
			  
			 $user->update(['active'=>'O']);
			
			  if($user->type==3){
				  ExtLab::where('lab_user_num',$user->id)->update(['status'=>'A','user_num' => auth()->user()->id]);
			  }
			 Permission::where('uid',$user->id)->update(['active'=>'O','user_num' => auth()->user()->id]);
			  $url = route('userslist.index',app()->getLocale());
			  $msg = __('User Activated successfully.');
			  Alert::toast($msg, 'success');
			 return response()->json(["url"=>$url]);
			 
			
		   }
	     }
	
	}
	
	
	public function get_defined_profile($lang,Request $request){
		$type = $request->type;
		switch($type){
		case 'profile':
		 $profile_id = $request->id;
		 $access_menus = DB::table('users_profiles')->where('id',$profile_id)->first(); 
		 $access_menus = explode(",",$access_menus->access_menus);
		 return response()->json(["access_menus"=>$access_menus]);
		break;
		case 'extdoctor':
		  $id = $request->id;
		  $doctor = Doctor::find($id);
		  $fname = $doctor->first_name;
		  $lname = $doctor->last_name;
		  $username = $fname.'.'.$lname;
          $year = Carbon::now()->format('Y');
          $password = 'viva'.$year;
          return response()->json(['fname'=>$fname,'lname'=>$lname,'username'=>$username,'password'=>$password]);		  
		break;
		case 'extlab':
		  $id = $request->id;
		  $l = ExtLab::find($id);
		  $fname = $l->full_name;
		  $lname = $l->full_name;
		  $username = $l->full_name;
		  $year = Carbon::now()->format('Y');
          $password = 'viva'.$year;
          return response()->json(['fname'=>$fname,'lname'=>$lname,'username'=>$username,'password'=>$password]);		  
		break;
		case 'lab':
		  $id = $request->id;
		  $lab = Clinic::find($id);
		  $fname = $lab->full_name;
		  $lname = $lab->full_name;
		  $username = $lab->full_name;
		  $prefix = $lab->id.'_';
          $year = Carbon::now()->format('Y');
          $password = 'viva'.$year;
          return response()->json(['fname'=>$fname,'lname'=>$lname,'prefix'=>$prefix,'username'=>$username,'password'=>$password]);
		break;
		}
	}

}


