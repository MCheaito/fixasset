<?php

namespace App\Http\Middleware;
use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Auth;

class CheckPatientURL
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
			abort(404);
		}
		
		if(Auth::check()){
	 	   //check if user is patient then no access for other users to urls
			if(Auth::user()->type !=4){
				abort(404);
			}
		}
        
        return $next($request);
    }
}
