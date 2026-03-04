<?php

namespace App\Http\Controllers\cms;
use App\Patient;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cms/pages.queuePatientAdd');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$patientId = Patient::postInsert($request,Auth::user());
		 
		if( $request->input("_selected") === "true")
		{
			return Patient::where('Id',$patientId)->get(array('Id','DOB','Gender','FullName'))[0];
		}
		else
		{
			return $patientId;
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($patientId)
    {
        return 'okay';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($patientId)
    {
		$patient = Patient::find($patientId);
		$patientInfo = \App\Models\PatientInfo::find($patientId);
		return view('cms/pages.queuePatientEdit', ['postLink' =>url('cms/pages/patient/'.$patientId), 'patient' => $patient, 'patientInfo' => $patientInfo]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $patientId)
    {
       	Patient::postUpdate($request,$patientId);
		if( $request->input("_selected") === "true")
		{
			return Patient::where('Id',$patientId)->get(array('Id','DOB','Gender','FullName'))[0];
		}
		else
		{
			return $patientId;
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
