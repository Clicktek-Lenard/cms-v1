<?php 

namespace App\Http\Middleware;

use Closure;

class CheckCMSSettings{
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
		$cms = $request->segment(2);
		/**
		 * Get Url Segment 3
		 *
		 * @var string
		 */
		$userRole = $request->segment(3);
		/**
		 * Get Url Segment 4
		 *
		 * @var string
		 */
		$pages = $request->segment(4);
		/**
		 * Get Roll Access
		 *
		 * @var json
		 */
		//$role = $request->user()->getUserRole;
		/**
		 * Check if Role exists
		 *
		 * @var boolen
		 */
		//////if (strpos($role,"{".$cms.".".$userRole.".".$pages."}") !== false && $cms === 'cms') {
		////////	return $next($request);	
		//////////}
		if ( $cms === 'cms') {
			return $next($request);	
		}
		/**
		 * Return Error
		 *
		 * @var json
		 */
		return response([
			'error' => [
				//'code' => 'INSUFFICIENT ROLE'."{".$cms.".".$userRole.".".$pages."}",
				'code' => 'INSUFFICIENT ROLE',
				'description' => 'You are not authorized to access this resource.'
			]
		], 401);
	}
}