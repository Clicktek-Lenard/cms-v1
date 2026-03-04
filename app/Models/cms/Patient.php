<?php

namespace App\Models\cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Patient extends Model
{
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Patient';
	protected $primaryKey = 'Id';
	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
	/**
     * Relationship Table Queue.
     *
     * @var array
     */
	public function queue()
    {
        $this->hasMany('App\Models\cms\Queue', 'IdPatient', 'Id');
    }
	/**
     * Insert new patient.
     *
     * @var array Used
     */
	public static function postInsert($request)
	{
		$fullnameSpace = $request->input('lastname'). ", ". $request->input('firstname'). " ".  $request->input('middlename');
		$fullname = strtoupper(Str::of($fullnameSpace)->replaceMatches('/ {2,}/', ' '));
		
		$patientId = Patient::insertGetId([
				'FullName'		=> $fullname,
				'LastName' 	=> strtoupper($request->input('lastname')),
				'FirstName' 	=> strtoupper($request->input('firstname')),
				'MiddleName'	=> strtoupper($request->input('middlename')),
				'DOB'		=> $request->input('dob'),
				'Gender'		=> $request->input('gender'),
				'InputDate'	=> date('Y-m-d'),
				'InputBy'		=> Auth::user()->username,
				'Status'		=> 'Active'
			]); 
		
		return  $patientId;
	}
	/**
     * Insert new patient.
     *
     * @var array Used
     */
	public static function postUpdate($request, $id)
	{
		$fullnameSpace = $request->input('lastname'). ", ". $request->input('firstname'). " ".  $request->input('middlename');
		$fullname = strtoupper(Str::of($fullnameSpace)->replaceMatches('/ {2,}/', ' '));
		
		$patientId = Patient::where('Id',$id)
			->lockForUpdate()
			->update([
				'FullName'		=> $fullname,
				'LastName' 	=> strtoupper($request->input('lastname')),
				'FirstName' 	=> strtoupper($request->input('firstname')),
				'MiddleName'	=> strtoupper($request->input('middlename')),
				'DOB'		=> $request->input('dob'),
				'Gender'		=> $request->input('gender'),
				'UpdateDate'	=> date('Y-m-d'),
				'UpdateBy'		=> Auth::user()->username
				
			]); 
		
		return  $id;
	}
	
	public function insertCheckPatient($datas = array())
	{
		$iData = DB::connection('mysql')->select("SELECT `Id` FROM `Patient` WHERE `LastName` LIKE '".$datas['LastName']."' AND
						`FirstName` LIKE '".$datas['FirstName']."' AND `Gender` LIKE '".$datas['Gender']."%' AND  `DOB` = '".$datas['DOB']."' ");
		
		if(count($iData) == 0)
		{
			return Patient::insertGetId([
				'Code'		=> $datas['Code']
				,'FullName'	=> $datas['FullName']
				,'LastName' 	=> $datas['LastName']
				,'FirstName' 	=> $datas['FirstName']
				,'MiddleName'	=> $datas['MiddleName']
				,'DOB'		=> $datas['DOB']
				,'Gender'		=> $datas['Gender']
				,'InputDate'	=> date('Y-m-d')
				,'InputBy'		=> Auth::user()->username
				,'Status'		=> 'Active'
			]); 
		}
		else
		{
			return $iData[0]->Id;
		}
	}
}
