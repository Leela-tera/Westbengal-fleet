<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
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

		if (Auth::guard($guard)->check()) {

			$requested_route = explode("/", $request->route()->uri())[1];
			$user_permissions = explode(",", auth()->user()->permissions);
			if (!in_array($requested_route,$user_permissions)){
				if($requested_route != 'unassigned_role')
					return redirect('admin/unassigned_role');
			}
		}
		
	    return $next($request);
	}
}