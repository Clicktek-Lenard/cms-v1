<?php

namespace App\Http\Controllers\cms\settings;
use App\Models\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

class UsersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	
		return view('cms/settings.users', ['users' => User::getUsersByBU()
			->leftJoin('Clinics', 'UserInfo.IdClinic', '=', 'Clinics.Id')
			->get(array('Users.Id','Users.FullName','Users.Username','Users.Email','Users.InputBy','Users.Status','Clinics.Code as DefaultClinic'))]
		);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$clinics = Auth::user()->getUserClinics()->orderBy('Clinics.Code', 'asc')->get(array('Clinics.Id','Clinics.Code'));
        return view('cms/settings.usersCreate', ['clinics' => $clinics]);   
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$v = Validator::make($request->all(), [
			'Username' 	=> 'required|unique:Users|max:50',
			'FullName' 	=> 'required|max:255',
			'Email' 	=> 'required|email|unique:Users|max:50',
			'ClinicCode'=> 'required',
		]);
		
		if ($v->fails())
			return redirect()->back()->withInput(Input::all())->withErrors($v->errors());
		else
		{
			$userId = Users::postInsert($request,Auth::user());	
			return 	$this->edit($userId);
		}
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
     	return $this->edit($id);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		$user = Users::getUsersByBU($id)->get(array('Users.Id','Users.FullName','Users.Username','Users.Email','Users.InputBy','Users.Status','Users.created_at','UserInfo.IdClinic'));
		if( count($user) )
		{
			$clinics = Auth::user()->getUserClinics()->orderBy('Clinics.Code', 'asc')->get(array('Clinics.Id','Clinics.Code'));
			$access = Auth::user()
				->getUserAccess()
				->leftJoin('Roles', 'UserRole.IdRole', '=', 'Roles.Id')
				->leftJoin('Clinics', 'UserRole.IdClinic', '=', 'Clinics.Id')
				->get(array('Roles.Role','Roles.Access','UserRole.SpecialRole','Clinics.Code as Clinic'));
			return view('cms/settings.usersEdit',['clinics' => $clinics, 'access' => $access, 'datas' => $user[0] ,'postLink' => url('/'.session('userBUCode').'/cms/settings/users/'.$id)]);
		}
		else
			return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		$v = Validator::make($request->all(), [
			'FullName' 	=> 'required|max:255',
			'Email' 	=> 'required|email|unique:Users,Email,'.$id.'|max:50',
			'ClinicCode'=> 'required',
		]);
		
		if ($v->fails())
			return redirect()->back()->withInput(Input::all())->withErrors($v->errors());
		else
		{
			Users::postUpdate($request,$id);
	   		return $this->edit($id);
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
