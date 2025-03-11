<?php
/*
* DEV APP
* Created date : 20-10-2022
*
*/
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SettingMail;
use DB;



class SupportController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function inquiry_message($lang,Request $request) 
    {
     $id=$request->UserId;
	 $user=User::where('id',$id)->first();
	 $user_name = $user->fname . " " . $user->lname;
	 if($user->type==1){
	  $doctor = Doctor::select('first_name','last_name')->where('doctor_user_num',$user->id)->first();
	  $full_name = $doctor->first_name.' '.$doctor->last_name;
	 }
	 if($user->type==2){
	  
	  $full_name = Clinic::where('id',$user->clinic_num)->pluck('full_name')[0];	 
	 }
	 $email= (isset(auth()->user()->email))?auth()->user()->email: __('Email is not provided');
     $msg =	 str_replace("\n", "<br/>", $request->txtComment);
	 $type=$this->get_sender_type($lang,$id);
	 $details = [ 'msg' => $msg,'user_name'=>$user_name,'full_name'=>$full_name,'email'=>$email,'type'=>$type ];
	  $subject = 'Support'.' : '.$request->inputSubject;  
		//dd($details);
		Mail::to([])->send(new SettingMail($details,$subject,'support'));			 
		
		if (Mail::failures()) {
           return response()->json(['error' => __('An error has occured while sending email!!')]);
          }else{
           return response()->json(['success' => __('Thank you,we have received your inquiry')]);
          }
       
	}
	
function get_sender_type($lang,$id){
        $type="";
		 $user=User::where('id',$id)->first();
		if($user->type==1){
		
	    $doc=Doctor::where('doctor_user_num',$id)->first();
		
		
		
		$type=($lang=='en')?$doc->medical_div_en:$doc->medical_div_fr;
		}
		
		if($user->type==2){
		
		$kind=Clinic::where('id',$user->clinic_num)->pluck('kind')[0];
		
		$type=($kind=='clin')?__("Clinic"):__("Hospital");	
        }
		  return $type;      
    }
  		
public function insert_remote_link($lang,Request $request){
	$link = trim($request->link_name);
	DB::table('zoom_links')->delete();
	$id = DB::table('zoom_links')->insertGetId(['link'=>$link]);
	$cnt=1;
	$l = DB::table('zoom_links')->find($id);
	$html = '';
	if(isset($l)){
	  $html = '<tr><td>'.$cnt.'</td><td class="text-center"><a href="'.$l->link.'" target="_blank">'.$l->link.'</a></td></tr>';	
	}else{
	  $html = '<tr><td colspan="3" class="text-center">'.__("No data is found").'</td></tr>';	
	}
   return response()->json(['html'=>$html]);	
}



}


