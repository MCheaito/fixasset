<?php
/*
*    DEV APP
*    Created date : 13-7-2022
*	Updated date : 14-9-2022, changed interface
*	Auto created from laravel auth package
*  
*
*/
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use Alert;
use Location;
use Carbon\Carbon;
use App\Models\UserLogs;
use UserHelper;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function redirectTo()
			{
				//dd(auth()->user()->id);
				
				//alert(app()->getLocale());
				app()->setlocale(auth()->user()->user_language);
                $type = auth()->user()->type;
				switch($type){
					case 1: case 2: case 3:
					  //$access_dash = UserHelper::can_access(auth()->user(),'dashboard');
					 // $access_visits = UserHelper::can_access(auth()->user(),'lab_requests');
						//  if( $access_visits){
							return app()->getLocale() .'/inventory/items';
						 // }else{
							//	  $access_phlebotomy = UserHelper::can_access(auth()->user(),'phlebotomy');
							//	  if($access_phlebotomy){
							//		 return app()->getLocale().'/phlebotomy/all'; 
							//	  }else{
									$access_pat = UserHelper::can_access(auth()->user(),'all_patients'); 
							////		if($access_pat){
								//		return app()->getLocale().'/patientslist';
								//	}
							//	  }
							 
						//  }
					//
					break;
					case 4:
					  return app()->getLocale() . '/patient_dash/info';
					break;
				    default:
					 Auth::logout();
                     return  redirect(app()->getLocale().'/login');					
				}
			}
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
	
	/*rewrite authentication method*/
	protected function authenticated(Request $request, $user)
    {
			
			
			$ip=$request->getClientIp();
			$location = Location::get($ip);
		
			// UserLogs::create([
			 //  'user_id' => $user->id,
			//   'ip_address' => $ip,
			  // 'countryName' => $location->countryName,
			//   'countryCode' => $location->countryCode,
			 //  'regionCode' => $location->regionCode,
			 //  'regionName' => $location->regionName,
			 //  'cityName' => $location->cityName,
			 //  'zipCode' => $location->zipCode,
			  // 'latitude' => $location->latitude,
			 //  'longitude'=> $location->longitude,
			//   'active' => 'O'
			 
			// ]);
            			
		if($user->active != 'O') {
            Auth::logout();
            return  redirect(app()->getLocale().'/login')->withErrors(['status'=>__('Oops, Your account is not active !')])->withInput();
        }
		
		if($user->type==4){
			$patient = Patient::where('patient_user_num',$user->id)->first();
			if($patient->status != 'O'){
			  Auth::logout();
              return  redirect(app()->getLocale().'/login')->withErrors(['status'=>__('Your patient account is deactivated , please call the lab for assistance.')])->withInput();	
			}
		}
		    
    }
	
		
		
		
        /**
         * Log the user out of the application.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function logout(Request $request)
        {
            $language=auth()->user()->user_language;
			
			Auth::logout();
        
            $request->session()->invalidate();
        
            $request->session()->regenerateToken();
        
            return redirect($language.'/login');
        }
	
}
