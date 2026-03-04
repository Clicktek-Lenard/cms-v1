<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\eros\ErosDB;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public static function create(Request $request)
    {
	$ips = explode(".", $request->ip());
	$ip = $ips[0].".".$ips[1];
	$ipClinic = ErosDB::getClinicData(NULL,$ip); //print_r($ipClinic);die();
	if( count($ipClinic) == 0)
	{
		$ip = $ips[0].".".$ips[1];
		$ipClinic = ErosDB::getClinicData(NULL,$ip);
	}
	$getClinic = NULL;
	
	if($request->ip() == "10.30.159.68" || $request->ip() == "10.30.169.172" || $request->ip() == "10.30.169.207" || $request->ip() == "10.30.169.120")
	{
		$getClinic = "DTU";
	}
	
	$clinics = ErosDB::getClinicData($getClinic);
        return view('auth.login', ['clinics'=> $clinics, 'defaultClinic' => $ipClinic[0]->Code ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
