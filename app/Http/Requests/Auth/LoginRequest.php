<?php

namespace App\Http\Requests\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Ldap;
use App\Models\Setting;
use App\Models\User;

use Log;
use Redirect;


class LoginRequest extends FormRequest
{
    public function CheckBranchAccess($buCode)
    {
	if( strpos(User::getRoleModule() , '"ldap_role":"['.$buCode.'-BRANCH]"') !== false  )

		return true;
	else
		return false;
    }	

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
    
  
        $this->ensureIsNotRateLimited();

  // Start Local Account Auth DB
/*	
	if (! Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
	    
	    // for updating local password  =  $user->password = bcrypt($request->input('password'));
        }
*/
 // End Local Account
    


	/////////////////////////////////
	// LDAP
	Log::debug("Binding user to LDAP.");
         $ldap_user = Ldap::findAndBindUserLdap($this->input('username'), $this->input('password'));
	 $user = User::where('username', '=', $this->input('username'))->whereNull('deleted_at')->where('ldap_import', '=', 1)->where('activated', '=', '1')->first();
         if (!$ldap_user) {
           
		Log::debug("Connection error to LDAP Server.");
		
		if (! Auth::attempt(array('username'=>$this->input('username'), 'password'=>$this->input('password')), $this->boolean('remember'))) {
		   RateLimiter::hit($this->throttleKey());

		   throw ValidationException::withMessages([
			'username' => trans('auth.failed'),
		    ]);
		} 
		
		/*RateLimiter::hit($this->throttleKey());
		throw ValidationException::withMessages([
			'username' => trans('auth.failed'),
			'account' => 'Username : '.$this->input('username'),
			'clinic' => 'Business Unit  for '. $this->input('modalClinics'),
			'reset'=> 'Contact ICT to reset your password.'
		]);*/
		
		if( !is_null(Auth::user()->role)  )
		{
			
			####LAST CHECK IF selected branch have an access right
			
			
			if( ! $checking = $this->CheckBranchAccess($this->input('modalClinics')) )
			{
				Log::debug('"No Access for this ":"['.$this->input('modalClinics').'-BRANCH]"');
				Auth::guard('web')->logout();
				
				throw ValidationException::withMessages([
					'error' => [
						'code' => 'Branch Access  for '. $this->input('modalClinics'),
						'description' => 'You are not authorized to access this branch.'
					]
				]);
			}
						//
			$dataBU = User::getBUDefault($this->input('modalClinics'))->get(array('Company.Id','BusinessUnits.DefaultPrice'));
			session(['userDepartment' => Auth::user()->department ]);
			session(['userDepartmentCode' =>  User::getDepartmentCode(Auth::user()->department) ]);
			session(['userClinicDefault' => $dataBU[0]->Id ]);
			session(['userPriceDefault' => $dataBU[0]->DefaultPrice ]);
			session(['userRole' => User::getRoleModule()]);
			session(['userClinicCode' => $this->input('modalClinics')]);
			session(['userClinicName' => $this->input('modalClinicName')]);
			Log::debug("User Select Clinic : ".$this->input('modalClinics'));
		
		}
	    
		$ldap_host = Setting::getSettings()->ldap_server;
		
		$user->last_login = date('Y-m-d H:i:s');
		$user->ldap_server_status = 'LDAP Server : Unable to connect '.$ldap_host;
		$user->save();	     
         } else {
	 
		//Added  ricky to check if username already existed
		 // Check if the user already exists in the database and was imported via LDAP
		 $user = User::where('username', '=', $this->input('username'))->whereNull('deleted_at')->where('ldap_import', '=', 1)->where('activated', '=', '1')->first(); // FIXME - if we get more than one we should fail. and we sure about this ldap_import thing?
		 Log::debug("Connecting to the Server : Successfully!");
		// The user does not exist in the database. Try to get them from LDAP.
		 // If user does not exist and authenticates successfully with LDAP we
		 // will create it on the fly and sign in with default permissions
		 if (!$user) {
		     Log::debug("Local user ".$this->input('username')." does not exist");
		     Log::debug("Creating local user ".$this->input('username'));

		     if ($user = Ldap::createUserFromLdap($ldap_user, $this->input('password'))) {
			 Log::debug("Local user created.");
		     } else {
			 Log::debug("Could not create local user.");
			 throw new \Exception("Could not create local user");
		     }
		     // If the user exists and they were imported from LDAP already
		 } else {
			Log::debug("Local user ".$this->input('username')." exists in database. Updating existing user against LDAP.");
			
			$ldap_attr = Ldap::parseAndMapLdapAttributes($ldap_user);
		 // print_r($ldap_attr); die();
			$cn_roles = "";
			foreach($ldap_attr['memberof'] as $userGroup)
			{
				if(str_contains($userGroup, "CN=CMS-"))
				{	//preg_replace('/\s+/', '_', $journalName);
					$cn = explode(",", $userGroup, 2);
					$cn_userGroup = Ldap::findAndBindUserLdap($this->input('username'), $this->input('password'), $cn[0]);
					$cn_roles .= $cn_userGroup['info'][0]."\n";
				}
			}
			$user->department = $ldap_attr['department'];
			$user->email = $ldap_attr['email'];
			$user->first_name = $ldap_attr['firstname'];
			$user->last_name = $ldap_attr['lastname']; //FIXME (or TODO?) - do we need to map additional fields that we now support? E.g. country, phone, etc.
			$user->role = $cn_roles;
			$user->password = bcrypt($this->input('password'));
			$user->ldap_server_status = '';
			$user->last_login = date('Y-m-d H:i:s');
			$user->AccessMapId = $ldap_attr['AccessMapId'];
			$user->save();
			Log::debug("LDAP Employee Number ".$ldap_attr['AccessMapId']." successfully Update to local DB");
		} // End if(!user)
		Log::debug("LDAP user ".$this->input('username')." successfully bound to LDAP");
		 
		 
		if (! Auth::attempt(array('username'=>$this->input('username'), 'password'=>$this->input('password')), $this->boolean('remember'))) {
		   RateLimiter::hit($this->throttleKey());

		   throw ValidationException::withMessages([
			'username' => trans('auth.failed'),
		    ]);
		}
		else if( !is_null(Auth::user()->role)  )
		{
			
			####LAST CHECK IF selected branch have an access right
			
			
			if( ! $checking = $this->CheckBranchAccess($this->input('modalClinics')) )
			{
				Log::debug('"No Access for this ":"['.$this->input('modalClinics').'-BRANCH]"');
				Auth::guard('web')->logout();
				
				throw ValidationException::withMessages([
					'error' => [
						'code' => 'Branch Access  for '. $this->input('modalClinics'),
						'description' => 'You are not authorized to access this branch.'
					]
				]);
			}
						//
			$dataBU = User::getBUDefault($this->input('modalClinics'))->get(array('Company.Id','BusinessUnits.DefaultPrice'));
			session(['userDepartment' => Auth::user()->department ]);
			session(['userDepartmentCode' =>  User::getDepartmentCode(Auth::user()->department) ]);
			session(['userClinicDefault' => $dataBU[0]->Id ]);
			session(['userPriceDefault' => $dataBU[0]->DefaultPrice ]);
			session(['userRole' => User::getRoleModule()]);
			session(['userClinicCode' => $this->input('modalClinics')]);
			session(['userClinicName' => $this->input('modalClinicName')]);
			Log::debug("User Select Clinic : ".$this->input('modalClinics'));
		
		}
	    
         }
	
	
	
	
	
	RateLimiter::clear($this->throttleKey());
	


    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('username')).'|'.$this->ip();
    }
}
