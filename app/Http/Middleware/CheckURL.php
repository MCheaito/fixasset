<?php

namespace App\Http\Middleware;
use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Auth;
use UserHelper;

class CheckURL
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!Auth::check()){
			$access = false;
			
		}
		
		if(Auth::check()){
			$access = false;
			//check if user is patient then no access
			if(Auth::user()->type==4){
				abort(404);
			}
			
			if(Auth::user()->admin_perm=='O'){
				$access = true;
			}else{
			
			
			//check each written url after language
			switch($request->segment(2)){
				
				case 'tests': 
				case 'tests_fields': 
				case 'tests_profiles': 
				case 'tests_formulas': 
				case 'tests_groups':
				case 'tests_categories':
				    $access = UserHelper::can_access(auth()->user(),'tests_settings');
				break;
				case 'profile':
				  $access = UserHelper::can_access(auth()->user(),'profile');
				break;
				case 'inventory':
				  $access = UserHelper::can_access(auth()->user(),'inventory');
				break;
				case 'support':
				  $access = UserHelper::can_access(auth()->user(),'send_feedback');
				break;
				case 'dashboard':
				  $access = UserHelper::can_access(auth()->user(),'dashboard');
				break;
				case 'userslist':
				  $access = UserHelper::can_access(auth()->user(),'users');
				break;
				case 'patientslist':
				  $access = UserHelper::can_access(auth()->user(),'all_patients');
				break;
				case 'phlebotomy':
				  $access = UserHelper::can_access(auth()->user(),'phlebotomy');
				break;
				case 'lab':
					  switch($request->segment(3)){
						case 'requests':
						 $access = UserHelper::can_access(auth()->user(),'lab_requests');
						break;
						case 'reports':
						 $access = UserHelper::can_access(auth()->user(),'general_reports');
						break;
						case 'billing':
						$access = UserHelper::can_access(auth()->user(),'medical_billings');
						break;
						case 'custom_reports':
						$access = UserHelper::can_access(auth()->user(),'custom_reports');
						break;
						case 'referredlabs':
						$access = UserHelper::can_access(auth()->user(),'medical_billings');
						break;
					  }
				break;
								
				case 'external':
				case 'branches':
				case 'resources':
				case 'prices':
				  $access = UserHelper::can_access(auth()->user(),'all_resources');
				break;
				
				default:
				 //other internal routes pass them
				 $access = true;
			 }
			}					
		  }
		
		if(!$access){ abort(404); }
        
        return $next($request);
    }
}
