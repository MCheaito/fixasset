<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Auth;
use UserHelper;
class SetLocale
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
        
		
		if(Auth::check()){
									
			app()->setLocale(auth()->user()->user_language);
		
		}else{	
		    app()->setLocale($request->segment(1));
		}
		
		//$locale = 'en'; // set en as the fallback locale
        //if ($request->is('/fr/*')) { // if the route starts with /fr/* set locale to fr
          //  $locale = 'fr';
        //} 

        //set the derived locale
        //App::setLocale($locale);
        //if (session()->has('locale')) {
          //  App::setLocale(session()->get('locale'));
       // }
        
        return $next($request);
    }
}
