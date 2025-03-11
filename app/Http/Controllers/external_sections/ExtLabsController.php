<?php

namespace App\Http\Controllers\external_sections;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\ExtLab;
use App\Models\TblBillCurrency;
use App\Models\User;
use App\Models\Permission;

use DB;
use DataTables;
use Alert;
use Session;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use UserHelper;
use Exception;

class ExtLabsController extends Controller
{
  public function __construct()
    {
        $this->middleware('auth',['except'=>['activate']]);
    }
  
  public function index($lang,Request $request)
    {
	  $user_type = auth()->user()->type;
	  $user_clinic_num=auth()->user()->clinic_num;
	  $my_labs = Clinic::where('id',$user_clinic_num)->get();
      $currUSD = TblBillCurrency::where('active','O')->where('abreviation','USD')->first();
	  $currEURO = TblBillCurrency ::where('active','O')->where('abreviation','EUR')->first();
	  $lbl_usd = isset($currUSD)? $currUSD->price:90000;
      $lbl_euro = isset($currEURO)? $currEURO->price:90000;
	  $cats = DB::table('tbl_external_labs_categories')->orderBy('id')->get();
	  
	  $delete_guarantor_acc = UserHelper::can_access(auth()->user(),'delete_guarantor_account');
	  //get profile menus for user
	  $menus = DB::table('users_profiles')->where('prof_id',4)->first();
	  $user_menus = explode(",",$menus->access_menus);	
		
		Session::forget('extLAB');
		Session::put('extLAB',true);
		
	if($request->ajax()){
	   
	   
	   
	   $filter_status='';
       
	   if(isset($request->filter_status) && $request->filter_status!=''){
		   $filter_status = $request->filter_status;
	   } 

        $filter_lab='';
       
	   if(isset($request->filter_lab) && $request->filter_lab!=''){
		   $filter_lab = $request->filter_lab;
	   }  	   	   
	   
	   $filter_cat='';
       
	   if(isset($request->filter_cat) && $request->filter_cat!=''){
		   $filter_cat = $request->filter_cat;
	   }  

       $filter_toCheck='';
       
	   if(isset($request->filter_toCheck) && $request->filter_toCheck!=''){
		   $filter_toCheck = $request->filter_toCheck;
	   }  	 	   
	   
	    $cat_name = ($lang=='fr')?'cat.name_fr':'cat.name_en';
	    $data = ExtLab::select('tbl_external_labs.id','tbl_external_labs.code','tbl_external_labs.percentage',
		                       'tbl_external_labs.full_name','tbl_external_labs.telephone','tbl_external_labs.fax',
							   'tbl_external_labs.email','tbl_external_labs.alternate_phone1','tbl_external_labs.alternate_phone2',
							   'tbl_external_labs.full_address','tbl_external_labs.pricel','tbl_external_labs.priced','tbl_external_labs.pricee',
							   'tbl_external_labs.status',DB::raw("IFNULL({$cat_name},'') as category_name"),
							   'tbl_external_labs.has_prices','tbl_external_labs.rate',DB::raw("IF(tbl_external_labs.is_valid='Y','Yes','No') as is_valid"),
							   'tbl_external_labs.lab_user_num','tbl_external_labs.email2','tbl_external_labs.email3')
						->leftjoin('tbl_external_labs_categories as cat','cat.id','tbl_external_labs.category');
						
           if($filter_lab !=''){
			   $data = $data->where('clinic_num',$filter_lab);
    			 }

            if($filter_status !=''){
				$data = $data->where('status',$filter_status);
			}

			if($filter_cat !=''){
				$data = $data->where('category',$filter_cat);
			}

            if($filter_toCheck!=''){
				$data = $data->where('is_valid',$filter_toCheck);
			}			
		
		   $data = $data->orderBy('id','desc');	
	   
	   
	 
	 return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) use($lang,$delete_guarantor_acc ){
                           $checked=($row->status=='A')?'checked':'';
						   $disabled=($row->status=='N')?'disabled':'';
						   $btn = '<button type="button"  class="btn btn-icon btn-md" onclick="editData('.$row->id.')" '.$disabled.'><i class="far fa-edit text-primary" title="'.__('Edit').'"></i></button>';
                           $btn.='  <label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'.$row->id.'" class="toggle-chk" '.$checked.'><span class="slideon-slider"></span></label>';
						   if($row->status=='A'){
							   $user = User::where('active','O')->find($row->lab_user_num);
							   if(isset($user)){
							     $btn.= '<button type="button" class="btn btn-icon btn-md" onclick="editUser('.$row->id.')"><i class="fas fa-user-edit" style="color:#1bbc9b" title="'.__("Edit user").'"></i></button>';
  							   	 if($delete_guarantor_acc){
								  $btn.= '<button type="button" class="btn btn-icon btn-md" onclick="deleteUser('.$row->id.')"><i class="fas fa-user-times" style="color:#1bbc9b" title="'.__("Delete user").'"></i></button>';
								 }
								 $btn.= '<button type="button" class="btn btn-icon btn-md" onclick="resendActivationEmail('.$row->id.')"><i class="far fa-envelope text-primary" title="'.__("Re-Send activation link").'"></i></button>';
							   }else{
							     $btn.= '<button type="button" class="btn btn-icon btn-md" onclick="createUser('.$row->id.')"><i class="fas fa-user-plus" style="color:#1bbc9b" title="'.__("Create user").'"></i></button>';
							   }
							  if($row->has_prices=='Y'){
								 $url = route('extlab_prices.edit',[$lang,$row->id]);
								 $btn .= '<a href="'.$url.'" class="ml-1 btn btn-action btn-icon btn-xs" title="'.__('Edit prices').'">Edit<i class="ml-1 fas fa-dollar-sign"></i></a>';
 							  }else{
								 $url = route('extlab_prices.create',[$lang,$row->id]);
								 $btn .= '<a href="'.$url.'"  class="ml-1 btn btn-action btn-xs" title="'.__('Create prices').'">Create<i class="ml-1 fas fa-dollar-sign"></i></a>';
 							  } 
						   
						    
						   
						   }
						   return $btn;
                    })
					->filterColumn('category_name', function($query, $keyword) use($lang){
                       $cat_name = ($lang=='fr')?'cat.name_fr':'cat.name_en';
					   $sql = "{$cat_name} like ?";
                        $query->whereRaw($sql, ["%{$keyword}%"]);
                     })
					->filterColumn('is_valid', function($query, $keyword){
                        $sql = "IF(tbl_external_labs.is_valid='Y','Yes','No') like ?";
                        $query->whereRaw($sql, ["%{$keyword}%"]);
                     }) 
					->rawColumns(['action'])
                    ->make(true);
				
	}	
		
		
	return view('external_labs.index')
	       ->with(['my_labs'=>$my_labs,'lbl_usd'=>$lbl_usd,'lbl_euro'=>$lbl_euro,'cats'=>$cats,'user_menus'=>$user_menus]);	
	}
	
	public function get($lang,Request $request)
    {
		
	    $id = $request->id;
		$data = ExtLab::find($id);
		return response()->json(['data'=>$data]);
	}
	
	public function getUserInfo($lang,Request $request)
    {
		
	    $id = $request->id;
		$type = $request->type;
		
		switch($type){
			case 'createUser':
			    $data = ExtLab::find($id);
				$clinic_num = $data->clinic_num;
				$name = $fname = $lname = trim($data->full_name);
				$email = trim($data->email);
				$button = __('Save');
				$header =__('Create new user');
				$user_name = str_replace(' ','_',strtolower(trim($data->full_name)));
				
				return response()->json(['id'=>$id,'clinic_num'=>$clinic_num,
		                         'name'=>$name,'fname'=>$fname,'lname'=>$lname,'email'=>$email,
								 'user_name'=>$user_name,'header'=>$header,'button'=>$button]);
			break;
			case 'editUser':
			    $data = ExtLab::find($id);
				$clinic_num = $data->clinic_num;
				$name = trim($data->full_name);
				$user = User::where('active','O')->find($data->lab_user_num);
				$fname = trim($user->fname);
			    $lname = trim($user->lname);
				$email = trim($user->email);
				$user_name = trim($user->username);
				$button = __('Update');
				$header =__('Edit user');
				return response()->json(['id'=>$id,'clinic_num'=>$clinic_num,
		                         'name'=>$name,'fname'=>$fname,'lname'=>$lname,'email'=>$email,
								 'user_name'=>$user_name,'header'=>$header,'button'=>$button]);
			break;
			case 'emailUser':
			  // Send activation email
              $lab = ExtLab::find($id);
			  $user = User::where('active','O')->find($data->lab_user_num);
		      
		      if($this->sendActivationEmail($lang,$user,$id)){
				 $msg='<p>'.__('Activation email is sent successfully').'</p>';
			   }else{
				 $msg='<p>'.__('Failed to send activation email').'</p>';
				}
		      return response()->json(['msg'=> $msg]);
			break;
			case 'deleteUser':
			 $lab = ExtLab::find($id);
			 $user = User::where('active','O')->find($data->lab_user_num);
			 $user_num = auth()->user()->id;
			 DB::beginTransaction();
			 try {
			   //remove lab_user_num from guarantor
			   ExtLab::where('id',$lab->id)->update(['lab_user_num'=>NULL,'user_num'=>$user_num]);
			   //remove user for this guarantor
			   User::where('id',$user->id)->delete();
			   DB::commit();
               $msg = __('User account is removed successfully');
               return response()->json(['status' => 'success','msg' => $msg]);
			 } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
                  }
			break;
		}
		
	}
	
	
	
	public function saveUserInfo($lang,Request $request){
		
		//create user procedure
		 $lab_id = $request->id;
		 $lab_num = $request->lab_num;
		 
		 $fname = $request->fname;
		 $lname = $request->lname;
         $email = $request->email;
        
		 $user_num = auth()->user()->id;
		 
		 $guarantor = ExtLab::find($lab_id);
		 $user = User::where('active','O')->find($guarantor->lab_user_num);
		 
		 if(isset($user)){
			   //edit user
			   User::where('id',$user->id)->update(['fname'=>$fname,'lname'=>$lname,'email'=>$email]);
		      
			   //get guarantor profile
		       $profile = implode(',',$request->menus);
		       Permission::where('uid',$user->id)
			            ->where('clinic_num',$lab_num)->update(['profile_permissions'=>$profile,'user_num'=>$user_num]);
			  
			  return response()->json(['msg'=>__('User updated successfully')]);
		 }else{
			  //auto password
		      //new user
			  $decodedPass = '123456';
		      $password = Hash::make($decodedPass);
		 	  
			  $username = $request->username;
			 
			 //new user creation
			 $new_user=User::create(['fname'=>$fname,'lname'=>$lname,'email'=>$email,'username'=>$username,
				                 'type'=>3,'password'=>$password,'permission'=>'L','admin_perm'=>'N',
							     'active'=>'N','clinic_num'=>$lab_num,'user_kind'=>'external_lab'])->id;
		     //update exterlab_user_num in table doctor
	         ExtLab::where('id',$lab_id)->update(['lab_user_num'=>$new_user,'user_num'=>$user_num]);
		     //get guarantor profile
		     $profile = implode(',',$request->menus);
		     Permission::create([
		      'uid'=>$new_user,
		      'clinic_num'=>$lab_num,
		      'profile_permissions'=>$profile,
		      'active'=>'N',
		      'user_num'=>$user_num
			  ]);
		     //procedure to send an email to activate account
		    // Send activation email
            $user = User::find($new_user);
			$msg = '<p>'.__('User created successfully').'</p>';
		    if($this->sendActivationEmail($lang,$user,$lab_id)){
				$msg .= '<p>'.__('Activation email is sent successfully').'</p>';
				
			}else{
				$msg .= '<p>'.__('Failed to send activation email').'</p>';
			}
		   
		    return response()->json(['msg'=>$msg]);
		 }
	}
	
	 private function sendActivationEmail($lang,$user,$lab_id){
        $lab = Clinic::find($user->clinic_num);
		$guarantor = ExtLab::where('id',$lab_id)->value('full_name');
		
		$email = ExtLab::where('id',$lab_id)->value('email');
		$email2 = ExtLab::where('id',$lab_id)->value('email2');
		$email3 = ExtLab::where('id',$lab_id)->value('email3');
		$ccEmails = [];
		
		if(isset($email) && $email!='' && trim($user->email)!=trim($email)){
			array_push($ccEmails,$email);
		}
		
		if(isset($email2) && $email2!='' && trim($user->email)!=trim($email2)){
			array_push($ccEmails,$email2);
		}
		
		if(isset($email3) && $email3!='' && trim($user->email)!=trim($email3)){
			array_push($ccEmails,$email3);
		}
		
		$validcc = $this->validateEmails($ccEmails);
		
		$hash = Crypt::encrypt($user->id);
		
		$temporaryUrl = URL::temporarySignedRoute(
          'external_labs.activate', now()->addHours(96), ['locale'=>$lang,'hash' => $hash]
          );
		
		try {
		Mail::send('emails.guarantor_activation', ['user' => $user,'lab'=>$lab,'guarantor'=>$guarantor,'url'=>$temporaryUrl], function ($message) use ($user,$lab,$validcc) {
            $fromName = $lab->full_name;
			$fromAddress = isset($lab->email) && filter_var($lab->email, FILTER_VALIDATE_EMAIL) ? $lab->email : 'noreply@email.com';
		    $subject = __('Activate your account').'-'.$lab->full_name;
			$message->to($user->email);
			$message->subject($subject);
			$message->from($fromAddress, $fromName);
			$message->replyTo($fromAddress,$fromName);
			if (!empty($validcc)) {
              $message->cc($validcc);
             }
		  });
		   return true;
		 
		 } catch (Exception $e) {
            return false;
           }
    
	}
	
	private  function validateEmails($emails){
     $validEmails = [];
     
	 foreach ($emails as $email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $validEmails[] = $email;
        }
     }

    return $validEmails;
   }
	
	public function activate($lang,Request $request,$hash)
    {
		if (!$request->hasValidSignature()) {
			abort(403, 'Unauthorized access.');
		}
		
		try {
         $userId = Crypt::decrypt($hash);
		 $user = User::where('id',$userId)->first();
		 
		 if(isset($user)){
			 //activate user and permissions to use system
			 if($user->active !='O'){
			  User::where('id',$user->id)->update(['active'=>'O']);
			  Permission::where('uid',$user->id)->where('clinic_num',$user->clinic_num)->update(['active'=>'O']);	 
			 }
			return  redirect(app()->getLocale().'/login')->withInput(['username' => $user->username]);
			 
		 }else{
			abort(403, 'User not found.'); 
		 }
		
		
		}catch(DecryptException $e) {
          abort(403, 'Unauthorized access.');
        }
		
	}
	
	public function store($lang,Request $request)
    {
	   
	   $id = $request->id;
	   $user_id=auth()->user()->id;
	   $user_clinic_num=$request->lab_num; ; 
	   if($id=='0'){
		  $lab = ExtLab::where('full_name',$request->full_name)->first();
		  if(isset($lab) && isset($request->full_name) && $request->full_name!=''){
			  return response()->json(['error'=>'Name already exists for another one']);
		  }
		  
		  $lab = ExtLab::where('code',$request->code)->first();
		  if(isset($lab) && isset($lab->code) && $lab->code!=''){
			  return response()->json(['error'=>'Code already exists for another one']);
		  }
		  
		  $lab = ExtLab::where('email',$request->email)->first();
		  if(isset($lab) && isset($lab->email) && $lab->email!=''){
			  return response()->json(['error'=>'Email already exists for another one']);
		  }
		  
		  ExtLab::create([
		     'full_name'=>strtoupper(trim($request->full_name)),
			 'code'=>$request->code,
			 'category'=>$request->category,
		     'full_address'=>trim($request->full_address),
			 'remarks'=>$request->remarks,
			 'telephone'=>str_replace("-","",$request->telephone),
			 'alternate_phone1'=>str_replace("-","",$request->alternate_phone1),
			 'alternate_phone2'=>str_replace("-","",$request->alternate_phone2),
			 'fax'=>str_replace("-","",$request->fax),
			 'email'=>$request->email,
			 'email2'=>$request->email2,
			 'email3'=>$request->email3,
			 'pricel' => $request->pricel,
			 'priced' => $request->priced,
			 'pricee'=>$request->pricee,
			 'rate'=>$request->rate,
			 'user_num'=>$user_id,
			 'is_valid'=>($request->is_valid=='on')?'Y':'N',
			 'clinic_num'=>$user_clinic_num,
			 'status'=>'A'
		    ]); 
	   
	   return response()->json(['success'=>__('Saved successfully')]);
	   }else{
		  $lab = ExtLab::where('full_name',$request->full_name)->where('id','<>',$id)->first();
		   if(isset($lab) && isset($request->full_name) && $request->full_name!=''){
			  return response()->json(['error'=>'Name already exists for another one']);
		  }
		  
		  $lab = ExtLab::where('code',$request->code)->where('id','<>',$id)->first();
		  if(isset($lab) && isset($lab->code) && $lab->code!=''){
			  return response()->json(['error'=>'Code already exists for another one']);
		  }
		  
		  $lab = ExtLab::where('email',$request->email)->where('id','<>',$id)->first();
		  if(isset($lab) && isset($lab->email) && $lab->email!=''){
			  return response()->json(['error'=>'Email already exists for another one']);
		  }
		   
		   ExtLab::where('id',$id)->update([
		     'full_name'=>strtoupper(trim($request->full_name)),
			 'category'=>$request->category,
			 'code'=>$request->code,
		     'full_address'=>trim($request->full_address),
			 'remarks'=>$request->remarks,
			 'telephone'=>str_replace("-","",$request->telephone),
			 'alternate_phone1'=>str_replace("-","",$request->alternate_phone1),
			 'alternate_phone2'=>str_replace("-","",$request->alternate_phone2),
			 'fax'=>str_replace("-","",$request->fax),
			 'email'=>$request->email,
			 'email2'=>$request->email2,
			 'email3'=>$request->email3,
			 'pricel' => $request->pricel,
			 'priced' => $request->priced,
			 'pricee'=>$request->pricee,
			 'rate'=>$request->rate,
			 'is_valid'=>($request->is_valid=='on')?'Y':'N',
			 'user_num'=>$user_id
			   ]); 
	   	 return response()->json(['success'=>__('Updated successfully')]);	

	   }		
		
	}
	
	public function delete($lang,Request $request)
    {
	$id = $request->id;
	$checked = $request->checked;
	$user_id = ExtLab::where('id',$id)->value('lab_user_num');
    if($checked=='N'){
     ExtLab::where('id',$id)->update(['status'=>'N','user_num'=>auth()->user()->id]);
	 if(isset($user_id)){User::where('id',$user_id)->update(['active'=>'N']);}
	  return response()->json(['success'=>__('InActivated successfully')]);	
	 }
	if($checked=='O'){
	 ExtLab::where('id',$id)->update(['status'=>'A','user_num'=>auth()->user()->id]);
	 if(isset($user_id)){ User::where('id',$user_id)->update(['active'=>'O']);}
      return response()->json(['success'=>__('Activated successfully')]);		
	 }
	
	}
	
}	