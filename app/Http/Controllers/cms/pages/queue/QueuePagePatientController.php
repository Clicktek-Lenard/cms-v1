<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class QueuePagePatientController extends Controller
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
	$province =   DB::connection('CMS')->table('province')->get(array('*'));
	return view('cms/pages.queuePatientAdd', ['postLink' =>url('cms/queue/pages/queuePatient'),  'province' => $province]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	$img = $request->myimage;
	//added 01-04-2024
	$BirthDate = $request->input('DOB');
	if ($BirthDate && strtotime($BirthDate) !== false) {
		$BirthDateFormated = date("Y-m-d", strtotime($BirthDate));
	} else {
		return;
	}
	if ($request->input('Gender') == 'Male') {
		$gender = 'M';
	} else {
		$gender = 'F';
	}
	$PwdExpiryDate = $request->input('ExpiryDatePWD');
	$PwdExpiryDateFormated = $PwdExpiryDate ? date("Y-m-d", strtotime($PwdExpiryDate)) : null;
	//end added
	$fileName = 'no-image.jpg';
	if(!empty($img) && isset($request->myimage))
	{
		$folderPath = public_path('uploads/PatientPicture/');
		
		$image_parts = explode(";base64,", $img);
		$image_type_aux = explode("image/", $image_parts[0]);
		$image_type = $image_type_aux[1];
		
		$image_base64 = base64_decode($image_parts[1]);
		$fileName = uniqid() . '.png';
		
		$file = $folderPath . $fileName;
		file_put_contents($file,$image_base64, FILE_USE_INCLUDE_PATH);
	}

	//$myDBId = sprintf('%02d', Controller::getMyDBID());
	$myDBId = Controller::getMyDBID();

	//$max = DB::connection('Eros')->select("SELECT SUBSTR(MAX(Code),-4) as iMax from Patient where Code like '".session('userClinicCode').$myDBId.date('ymd')."%' " );
	//$x = ($max[0]->iMax != 0)?$max[0]->iMax+1:1;
	$newCode =  $this->genPatientCode();
    	if( isset($newCode->PNum) )
	{
		$max = $newCode->PNum;
	}
	else
	{
		return  $newCode;
		die();
	}
	
	$barangay = (!empty($request->input('barangay')))?  DB::connection('CMS')->table('zip')->where('zip_id', $request->input('barangay'))->get(array('zip_name'))[0]->zip_name : '';
	$city = (!empty($request->input('city')))?  DB::connection('CMS')->table('city')->where('city_id', $request->input('city'))->get(array('city_name'))[0]->city_name : '';
	$province = (!empty($request->input('province')))?  DB::connection('CMS')->table('province')->where('province_id', $request->input('province'))->get(array('province_name'))[0]->province_name : '';
	$fullAddress = $request->input('Address1'). " ". $barangay . " " . $city . " " . $province;
	
	$patientId = DB::connection('Eros')->table('Patient')
				->insertGetId([
					'Code'		=> session('userClinicCode').$myDBId.date('ymd').sprintf('%04d', $max++)
					,'FullName'	=> strtoupper(Str::of($request->input('FullName'))->replaceMatches('/ {2,}/', ' '))
					,'LastName'	=> strtoupper(Str::of($request->input('LastName'))->replaceMatches('/ {2,}/', ' '))
					,'FirstName'	=> strtoupper(Str::of($request->input('FirstName'))->replaceMatches('/ {2,}/', ' '))
					,'MiddleName'	=> strtoupper(Str::of($request->input('MiddleName'))->replaceMatches('/ {2,}/', ' '))
					,'Prefix'		=> $request->input('PrefixName')
					,'Suffix'		=> $request->input('SuffixName')
					,'Gender'		=> $gender
					,'DOB'		=> $BirthDateFormated
					//,'Religion'		=> strtoupper(Str::of($request->input('MiddleName'))->replaceMatches('/ {2,}/', ' '))
					//,'PhilHealth'	=> oci_result($stid, 'PM_PHILHEALTH')
					,'SeniorId'		=>  $request->input('SeniorId')
					,'PWD'		=> $request->input('PWD')
					,'ExpiryDatePWD' => $PwdExpiryDateFormated
					,'FullAddress'	=> strtoupper(Str::of($fullAddress)->replaceMatches('/ {2,}/', ' '))
					,'Address'		=> strtoupper(Str::of($request->input('Address1'))->replaceMatches('/ {2,}/', ' '))
					,'Barangay'	=> strtoupper(Str::of($request->input('barangay'))->replaceMatches('/ {2,}/', ' '))
					,'City'		=> strtoupper(Str::of($request->input('city'))->replaceMatches('/ {2,}/', ' '))
					,'State'		=>  strtoupper(Str::of($request->input('province'))->replaceMatches('/ {2,}/', ' '))
					//,'ZipCode'	=> oci_result($stid, 'PM_ZIPCODE')	
					,'ContactNo'	=> strtolower(Str::of($request->input('Phone1'))->replaceMatches('/ {2,}/', ' '))
					//,'FaxNo'		=> oci_result($stid, 'PM_FAXNO')
					,'Email'		=> strtolower(Str::of($request->input('Email'))->replaceMatches('/ {2,}/', ' '))
					,'Moblie'		=> strtolower(Str::of($request->input('Phone2'))->replaceMatches('/ {2,}/', ' '))
					,'InputDate'	=> date('Y-m-d')
					,'InputBy'		=>  Auth::user()->username
					//,'UpdateDate'	=> date('Y-m-d')
					//,'UpdateBy'	=> Auth::user()->username
					,'Status'		=> 'Y'
					,'IsActive'		=> '1'
					,'Country'		=> $request->input('Address4')
					,'LastVisit'		=> date('Y-m-d')
					,'PictureLink'	=> $fileName
					//,'Nationality'	=> oci_result($stid, 'PM_NATIONALITY')
					,'PassPortNo'	=> $request->input('PassPortNo')
					//,'RDOB'		=> oci_result($stid, 'PM_DOB')
					
					
				]);
	if($request->input('_selected') == 'false')
	{
		return $patientId;
	}else{
		return DB::connection('Eros')->table('Patient')
			->where('IsActive',1) // active
			->where('Id',$patientId) // active
			->get(array('Id','DOB', 'Gender', 'FullName'))[0];
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
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
	$patient = DB::connection('Eros')->table('Patient')
			->where('IsActive',1) // active
			->where('Id',$id) // active
			->get(array('*'));
			$dob = $patient[0]->DOB;
			$PatientBirthday = date("m-d-Y", strtotime($dob));
	
	//$patient = Patient::find($patientId);
	//$patientInfo = \App\Models\PatientInfo::find($patientId);
	
	$province =   DB::connection('CMS')->table('province')->get(array('*'));
	$city =   DB::connection('CMS')->table('city')->where('city_province', $patient[0]->State)->get(array('*'));
	$zip =   DB::connection('CMS')->table('zip')->where('zip_city', $patient[0]->City)->get(array('*'));
	return view('cms/pages.queuePatientEdit', ['postLink' =>url('cms/queue/pages/queuePatient/'.$id), 'patient' => $patient[0], 'Birthday' => $PatientBirthday , 'province' => $province, 'city' => $city, 'zip' => $zip ]);
	
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

	$img = $request->myimage; 
	//added 01-04-2024
	$BirthDate = $request->input('DOB');

	if ($BirthDate && strtotime($BirthDate) !== false) {
		$BirthDateFormated = date("Y-m-d", strtotime($BirthDate));
	} else {
		return;
	}
	if ($request->input('Gender') == 'Male') {
		$gender = 'M';
	} else {
		$gender = 'F';
	}
	$PwdExpiryDate = $request->input('ExpiryDatePWD');
	$PwdExpiryDateFormated = $PwdExpiryDate ? date("Y-m-d", strtotime($PwdExpiryDate)) : null;
	//end added
	if(!empty($img) && isset($request->myimage))
	{
		$folderPath = public_path('uploads/PatientPicture/');
		
		$image_parts = explode(";base64,", $img);
		$image_type_aux = explode("image/", $image_parts[0]);
		$image_type = $image_type_aux[1];
		
		$image_base64 = base64_decode($image_parts[1]);
		$fileName = uniqid() . '.png';
		
		$file = $folderPath . $fileName;
		file_put_contents($file,$image_base64, FILE_USE_INCLUDE_PATH);
		
		DB::connection('Eros')->table('Patient')->where('Id',$id)
				->update([
					'PictureLink'	=> $fileName
				]); 
	}
	
	$barangay = (!empty($request->input('barangay')))?  DB::connection('CMS')->table('zip')->where('zip_id', $request->input('barangay'))->get(array('zip_name'))[0]->zip_name : '';
	$city = (!empty($request->input('city')))?  DB::connection('CMS')->table('city')->where('city_id', $request->input('city'))->get(array('city_name'))[0]->city_name : '';
	$province = (!empty($request->input('province')))?  DB::connection('CMS')->table('province')->where('province_id', $request->input('province'))->get(array('province_name'))[0]->province_name : '';
	$fullAddress = $request->input('Address1'). " ". $barangay . " " . $city . " " . $province;
	
	DB::connection('Eros')->table('Patient')->where('Id',$id)
				->lockForUpdate()
				->update([
					'FullName'		=> strtoupper(Str::of($request->input('FullName'))->replaceMatches('/ {2,}/', ' '))
					,'LastName'	=> strtoupper(Str::of($request->input('LastName'))->replaceMatches('/ {2,}/', ' '))
					,'FirstName'	=> strtoupper(Str::of($request->input('FirstName'))->replaceMatches('/ {2,}/', ' '))
					,'MiddleName'	=> strtoupper(Str::of($request->input('MiddleName'))->replaceMatches('/ {2,}/', ' '))
					,'Prefix'		=> $request->input('PrefixName')
					,'Suffix'		=> $request->input('SuffixName')
					,'Gender'		=> $gender
					,'DOB'		=> $BirthDateFormated
					//,'Religion'		=> strtoupper(Str::of($request->input('MiddleName'))->replaceMatches('/ {2,}/', ' '))
					//,'PhilHealth'	=> oci_result($stid, 'PM_PHILHEALTH')
					,'SeniorId'		=> $request->input('SeniorId')
					,'PWD'		=> $request->input('PWD')
					,'ExpiryDatePWD' => $PwdExpiryDateFormated
					,'FullAddress'	=> strtoupper(Str::of($fullAddress)->replaceMatches('/ {2,}/', ' '))
					,'Address'		=> strtoupper(Str::of($request->input('Address1'))->replaceMatches('/ {2,}/', ' '))
					,'Barangay'	=> strtoupper(Str::of($request->input('barangay'))->replaceMatches('/ {2,}/', ' '))
					,'City'		=> strtoupper(Str::of($request->input('city'))->replaceMatches('/ {2,}/', ' '))
					,'State'		=> strtoupper(Str::of($request->input('province'))->replaceMatches('/ {2,}/', ' '))
					//,'ZipCode'	=> oci_result($stid, 'PM_ZIPCODE')	
					,'ContactNo'	=> strtolower(Str::of($request->input('Phone1'))->replaceMatches('/ {2,}/', ' '))
					//,'FaxNo'		=> oci_result($stid, 'PM_FAXNO')
					,'Email'		=> strtolower(Str::of($request->input('Email'))->replaceMatches('/ {2,}/', ' '))
					,'Moblie'		=> strtolower(Str::of($request->input('Phone2'))->replaceMatches('/ {2,}/', ' '))
					//,'InputDate'	=> date()
					//,'InputBy'		=> oci_result($stid, 'PM_CREATED_BY')
					,'UpdateDate'	=> date('Y-m-d')
					,'UpdateBy'	=> Auth::user()->username
					,'Status'		=> 'Y'
					,'IsActive'		=> '1'
					,'Country'		=> $request->input('Address4')
					,'LastVisit'		=> date('Y-m-d')
					
					//,'Nationality'	=> oci_result($stid, 'PM_NATIONALITY')
					,'PassPortNo'	=> $request->input('PassPortNo')
					,'RDOB'		=> 'reUpdate'
					
				]); 

	if($request->input('_selected') == 'false')
	{
		return $id;
	}else{
		return DB::connection('Eros')->table('Patient')
			->where('IsActive',1) // active
			->where('Id',$id) // active
			->get(array('Id','DOB', 'Gender', 'FullName'))[0];
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
