<?php 

namespace App\Http\Middleware;

use Closure;

class CheckCMS{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		/**
		 * Get Url Segment 2
		 *
		 * @var string
		 */
		$module = $request->segment(1);
		/**
		 * Get Url Segment 2
		 *
		 * @var string
		 */
		$tab = $request->segment(2);
		/**
		 * Get Url Segment 3
		 *
		 * @var string
		 */
		$access = $request->segment(3);
		/**
		 * Get Roll Access
		 *
		 * @var json
		 */
		$role = session('userRole');
		/**
		 * Check if Role exists
		 *
		 * @var boolen
		 */
		 if( strpos( $role, '"module":"'.$module.'"') !== false  && strpos(session('userRole') , '"tab":"'.$tab.'"') !== false )
		 {	
			return $next($request);
		}
		
		/**
		 * Return Error
		 *
		 * @var json
		 */
		return response([
			'error' => [
				'code' => 'INSUFFICIENT ROLE for '. $tab,
				'description' => 'You are not authorized to access this resource.'
			]
		], 401);
	}
}