<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAdmin
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string|null  $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = 'admin')
	{
                //print_r(Auth::guard($guard)->user()); exit();
	    if (Auth::guard($guard)->check()) {
	        // return redirect('admin/dashboard');
	        
	        if (trim(Auth::guard($guard)->user()->role) == 'super_admin') {
		        return redirect('admin/dashboard');

		    } else if (trim(Auth::guard($guard)->user()->role) == 'admin') {
		        return redirect('admin/dashboard');

		    } else if (trim(Auth::guard($guard)->user()->role) == 'inventory') {
                          //print_r(Auth::guard($guard)->user());

		        return redirect('admin/inventorydashboard');

		    } else if (empty(trim(Auth::guard($guard)->user()->role))){
		    	return redirect('admin/unassigned_role');

		    }
	    }


	    return $next($request);
	}
}