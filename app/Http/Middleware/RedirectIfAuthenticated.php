<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use UserHelper;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
				app()->setlocale(auth()->user()->user_language);
                if(Auth::user()->type==4){
				  return redirect(app()->getLocale().'/patient_dash/info');
				}else{
				  //$access_dash = UserHelper::can_access(auth()->user(),'dashboard');
				  $access_visits = UserHelper::can_access(auth()->user(),'lab_requests');
				  if($access_visits){
				    return redirect(app()->getLocale() .'/lab/requests');
				  }else{
					      $access_phlebotomy = UserHelper::can_access(auth()->user(),'phlebotomy');
						  if($access_phlebotomy){
							 return redirect(app()->getLocale().'/phlebotomy/all'); 
						  }else{
							$access_pat = UserHelper::can_access(auth()->user(),'all_patients'); 
							if($access_pat){
								return redirect(app()->getLocale().'/patientslist');
							}
						  }
				     
				  }
				}
            }
        }

        return $next($request);
    }
}
