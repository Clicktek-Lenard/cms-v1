<?php

namespace App\Http\Controllers\eros;							//pcp v2

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;
use App\Models\eros\Queue;

class PhysicianController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
     public function create()
    {

		$clinics = ErosDB::getClinicData(NULL, NULL, NULL); 
		$physicianType =  ErosDB::getPhysicianType(); 
		
    	return view('eros.physicianListCreate', ['clinics' => $clinics, 'physicianType' => $physicianType]);
    }
    
	public function PhysicianReUpdate()
	{
	// DB::connection('Eros')->beginTransaction();
	// $cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.154.8)(PORT = 1521))  (CONNECT_DATA= (SID = erosprod))     )';
	// $conn = oci_connect("erosbs", "erosbs", $cstr,   'AL32UTF8');

	// 	$erosCodeUpd = DB::connection('Eros')->select("SELECT * from Physician  " );
		
	// 	foreach($erosCodeUpd as $data)
	// 	{
		
	// 		$fullnameSpace = $data->LastName. ", ". $data->FirstName. " ".  $data->MiddleName;
	// 		$fullname = strtoupper(Str::of($fullnameSpace)->replaceMatches('/ {2,}/', ' '));
		
	// 		//Eros Clinician_Details
	// 		$sqlUpdate = "UPDATE CLINICIAN_DETAILS SET 
	// 			CD_NAME = '".$fullname."'
	// 			,CD_LICENSE = '".strtoupper(Str::of($data->PRCNo)->replaceMatches('/ {2,}/', ' '))."'
	// 			,CD_SPECIALIZATON = '".substr(strtoupper(Str::of($data->Description)->replaceMatches('/ {2,}/', ' ')),0, 50)."'
	// 			WHERE
	// 			CD_CODE LIKE '".$data->ErosCode."'
	// 		";
	// 		$compiled = oci_parse($conn, $sqlUpdate);
			
	// 		$result = oci_execute($compiled);
			
	// 		if (!$result) {
	// 		    $e = oci_error($query);  // For oci_execute errors pass the statement handle
	// 		    print htmlentities($e['message']);
	// 		    print "\n<pre>\n";
	// 		    print htmlentities($e['sqltext']);
	// 		    printf("\n%".($e['offset']+1)."s", "^");
	// 		    print  "\n</pre>\n";
	// 		}else{
	// 			print "<br>Connected and Inserted to Oracle! UAT EROS"; 
	// 			DB::connection('Eros')->update("UPDATE Physician SET
	// 				`FullName` = '".$fullname."'
	// 				,`UpdateBy` = '".Auth::user()->username."'
	// 				,`UpdateDate` = '".date('Y-m-d')."'
	// 				,`CebuStatus` = 'reUpdate'
	// 				,`SMBStatus` = 'reUpdate'
	// 				WHERE `ErosCode` LIKE '".$data->ErosCode."' and `Id` = '".$data->Id."'
	// 			");
	// 		}
			
	// 	}
		
	// 	oci_close($conn);
	// DB::connection('Eros')->commit();	
	}   

	public function index()
    {		
		$physicianData = ErosDB::getDoctorData();

		return view('eros.physicianList', ['physicianData' => $physicianData]);
    }
     /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
	public function store(Request $request)
    {
		//dd($request->all());
		$isApprover = strpos(session('userRole'), '"ldap_role":"[PHYSICIAN-APPROVER]"') !== false;
		$fullnameSpace = $request->input('lastname'). ", ". $request->input('firstname'). " ". $request->input('suffix'). " ".  $request->input('middlename');
		$fullname = strtoupper(Str::of($fullnameSpace)->replaceMatches('/ {2,}/', ' '));
		$erosCode = strtoupper(Str::of(substr($request->input('lastname'), 0, 1).substr($request->input('firstname'), 0, 1).substr($request->input('middlename'), 0, 1))->replaceMatches('/ {2,}/', ' '));
		$erosCodeMax = ErosDB::getPhysicianMAX($erosCode); 
		$dateOfBirthRaw = $request->input('dob');

		$dateOfBirth = !empty($dateOfBirthRaw) ? \Carbon\Carbon::parse($dateOfBirthRaw)->format('Y-m-d') : null;
		$validityDate = !empty($validityDateRaw) ? \Carbon\Carbon::parse($validityDateRaw)->format('Y-m-d') : null;	

		$prcNo = ltrim($request->input('prcno'), '0');                                             // Normalize PRC number by removing leading zeros
		$existingPhysician = DB::connection('Eros')->table('Physician')                            // Check if the PRC number already exists (ignoring leading zeros)
			->whereRaw("LPAD(PRCNo, 7, '0') = ?", [str_pad($prcNo, 7, '0', STR_PAD_LEFT)])
			->first();

			if ($existingPhysician) {
				return redirect()->back()->withInput()->with('error', 'Physician already exists!');
			}

		// Convert schedule array into JSON
		$schedule = $request->input('schedule', []);
		$nwdBranch = $request->input('nwdBranch', []);
		$timestart = $request->input('timestart', []);
		$timeend = $request->input('timeend', []);
		$firstengagement = $request->input('firstengagement', []);
		$lastengagement = $request->input('lastengagement', []);
		$byappointment = $request->input('appointment', []);
		$inputBy = $request->input('inputby',[]);
		
		$scheduleJson 		= json_encode($schedule);
		$branchJson 		= json_encode($nwdBranch);
		$timeStartJson 		= json_encode($timestart);
		$timeEndJson 		= json_encode($timeend);
		$byAppointmentJson = json_encode($byappointment);
		$firstengagementJson = json_encode($firstengagement);
		$lastengagementJson = json_encode($lastengagement);
		$inputByJSON		= json_encode($inputBy);

		$physicianId = DB::connection('Eros')->table('Physician')->insertGetId([
			'Code'				  => $erosCodeMax,
			'FullName'            => strtoupper(Str::of($fullname)->replaceMatches('/ {2,}/', ' ')),
			'PrintName'			  => strtoupper(Str::of($fullname)->replaceMatches('/ {2,}/', ' ')),
			'DisplayName'		  => strtoupper(Str::of($fullname)->replaceMatches('/ {2,}/', ' ')),
			'LastName'            => strtoupper(Str::of($request->input('lastname'))->replaceMatches('/ {2,}/', ' ')),
			'Suffix'              => $request->input('SuffixName'),
			'FirstName'           => strtoupper(Str::of($request->input('firstname'))->replaceMatches('/ {2,}/', ' ')),
			'MiddleName'          => strtoupper(Str::of($request->input('middlename'))->replaceMatches('/ {2,}/', ' ')),
			'PRCNo'               => $request->input('prcno'),
			'PRCValidity'         => $request->input('validity'),
			'DOB'                 => $dateOfBirth,
			'Email'               => $request->input('email'),
			'Mobile'              => $request->input('mobile'),
			'NWDBranch'       	  => $branchJson,
			'Schedule'        	  => $scheduleJson,
			'TimeStart'			  => $timeStartJson,
			'TimeEnd'			  => $timeEndJson,
			'FirstEngagement' 	  => $firstengagementJson,
			'LastEngagement'  	  => $lastengagementJson,
			'ByAppointment'		  => $byAppointmentJson,
			'ClinicScheduledBy'   => $inputByJSON,
			'PCP'                 => $request->has('primaryCarePhysician') ? 'Yes' : 'No',
			'Specialist'          => $request->has('specialistConsultant') ? 'Yes' : 'No',
			'RP'                  => $request->has('referringPhysician') ? 'Yes' : 'No',
			'Regular'             => $request->has('regular') ? 'Yes' : 'No',
			'Reliever'            => $request->has('reliever') ? 'Yes' : 'No',
			'Visiting'            => $request->has('visiting') ? 'Yes' : 'No',
			'Referring'           => $request->has('referringStatus') ? 'Yes' : 'No',
			'ApplicationLetter'   => $request->has('applicationLetter') ? 'Yes' : 'No',
			'CurriculumVitae'   	=> $request->has('curriculumVitae') ? 'Yes' : 'No',
			'Diploma'   			=> $request->has('medicalSchoolDiploma') ? 'Yes' : 'No',
			'PRCId'   				=> $request->has('prcId') ? 'Yes' : 'No',
			'ResidencyCertificate'   	=> $request->has('residencySpecialtyCert') ? 'Yes' : 'No',
			'DiplomateCertificate'   	=> $request->has('diplomateFellowCert') ? 'Yes' : 'No',
			'PhilHealth'   			=> $request->has('philHealth') ? 'Yes' : 'No',
			'PTR'   				=> $request->has('ptr') ? 'Yes' : 'No',
			'BIR'   				=> $request->has('bir') ? 'Yes' : 'No',
			'MOA'   				=> $request->has('MOA') ? 'Yes' : 'No',
			'SubGroup' 				=> $request->has('specialistConsultant') ? 'SPL' : 'PCP',
			'Description'         => strtoupper(Str::of($request->input('specialty'))->replaceMatches('/ {2,}/', ' ')),
			'SubDescription'      => strtoupper(Str::of($request->input('subSpecialty'))->replaceMatches('/ {2,}/', ' ')),
			'InputDate'           => date('Y-m-d'),
			'BranchCode'    	  => session('userClinicCode'),
			'BranchRequestCode'    => !$isApprover ? session('userClinicCode') : null,
			'RequestorBy'         => Auth::user()->username,
			'Status'              => 'For Approval',
			'ErosCode'            => $erosCodeMax,
		]);
		
		$physicianData = ErosDB::getDoctorData(); // Fetch only 1000 records
		return view('eros.physicianList', compact('physicianData'));

    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
    //     $datas = ErosDB::getPhysicianData($id);
	// return view('eros.physicianListEdit', ['datas' => $datas, 'postLink' => url(session('userBUCode').'/erosui/physician/'.$id)]);    
    }
    
     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
		$datas = ErosDB::getPhysicianData($id);
		$clinics = ErosDB::getClinicData(NULL, NULL, NULL);
		$physicianType =  ErosDB::getPhysicianType();
		$physician = DB::connection('Eros')->table('Physician')->where('Id', $id)->first();
		// Check if Schedule is null or an empty string before decoding
    	$schedule = $physician->Schedule ? json_decode($physician->Schedule, true) : [];
		$nwdBranch = $physician->NWDBranch ? json_decode($physician->NWDBranch, true) : [];
		$timestart = $physician->TimeStart ? json_decode($physician->TimeStart, true) : [];
		$timeend = $physician->TimeEnd ? json_decode($physician->TimeEnd, true) : [];
		$byappointment = $physician->ByAppointment ? json_decode($physician->ByAppointment, true) : [];
		$firstengagement = $physician->FirstEngagement ? json_decode($physician->FirstEngagement, true) : [];
		$lastengagement = $physician->LastEngagement ? json_decode($physician->LastEngagement, true) : [];
		$inputby = $physician->ClinicScheduledBy ? json_decode($physician->ClinicScheduledBy, true) : [];
				
		//dd($schedule,$nwdBranch, $timestart,$timeend, $byappointment, $firstengagement, $lastengagement);
	
	return view('eros.physicianListEdit', ['datas' => $datas, 'physician' => $physician,'physicianType' => $physicianType, 'clinics' => $clinics,'schedule' => $schedule,'nwdBranch' => $nwdBranch, 'timestart' => $timestart, 'timeend' => $timeend,
	 	'byappointment' => $byappointment, 'firstengagement' => $firstengagement, 'lastengagement' => $lastengagement, 'inputby' => $inputby, 'postLink' => url(session('userBUCode').'/cmsphysician/physician/'.$id)]);    
    }
    
   /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
	public function update(Request $request, $id)
	{
		
		//dd($request->all());
		// Check if the physician exists
		$physician = DB::connection('Eros')->table('Physician')->where('Id', $id)->first();

		if (!$physician) {
			return back()->withErrors(['error' => 'Physician not found.']);
		}
		$isApprover = strpos(session('userRole'), '"ldap_role":"[PHYSICIAN-APPROVER]"') !== false;
		$fullnameSpace = $request->input('lastname'). ", ". $request->input('firstname'). " ". $request->input('suffix'). " ".  $request->input('middlename');
		$fullname = strtoupper(Str::of($fullnameSpace)->replaceMatches('/ {2,}/', ' '));
		$erosCode = strtoupper(Str::of(substr($request->input('lastname'), 0, 1) . substr($request->input('firstname'), 0, 1) . substr($request->input('middlename'), 0, 1))->replaceMatches('/ {2,}/', ' '));
		$erosCodeMax = ErosDB::getPhysicianMAX($erosCode);
		$dateOfBirthRaw = $request->input('dob');
		$validityDateRaw =$request->input('validity');

		$dateOfBirth = !empty($dateOfBirthRaw) ? \Carbon\Carbon::parse($dateOfBirthRaw)->format('Y-m-d') : null;
		$validityDate = !empty($validityDateRaw) ? \Carbon\Carbon::parse($validityDateRaw)->format('Y-m-d') : null;

		// Convert schedule array into JSON
		$schedule = $request->input('schedule', []);
		$nwdBranch = $request->input('nwdBranch', []);
		$timestart = $request->input('timestart', []);
		$timeend = $request->input('timeend', []);
		$firstengagement = $request->input('firstengagement', []);
		$lastengagement = $request->input('lastengagement', []);
		$byappointment = $request->input('appointment', []);
		$inputBy = $request->input('inputby', []);
		$maxCount = max(count($schedule), count($nwdBranch), count($timestart), count($timeend), count($byappointment), count($firstengagement), count($lastengagement));
	
		if ($request->input('resigned') === 'Yes') {  	// Add this condition to clear the schedule of resigned physicians
			$schedule = [];
			$nwdBranch = [];
			$timestart = [];
			$timeend = [];
		}

		for ($i = 0; $i < $maxCount; $i++) {
			$byappointment[$i] = $byappointment[$i] ?? 'No';
			$firstengagement[$i] = $firstengagement[$i] ?? null; // Or any default value you want
			$lastengagement[$i] = $lastengagement[$i] ?? null; // Or any default value you want
		}
		
		$scheduleJson 		= json_encode($schedule);
		$branchJson 		= json_encode($nwdBranch);
		$timeStartJson 		= json_encode($timestart);
		$timeEndJson 		= json_encode($timeend);
		$byAppointmentJson = json_encode($byappointment);
		$firstengagementJson = json_encode($firstengagement);
		$lastengagementJson = json_encode($lastengagement);
		$inputByJSON = json_encode($inputBy);

		$updateData = [
			'Code'				  => $erosCodeMax,
			'FullName'            => strtoupper(Str::of($fullname)->replaceMatches('/ {2,}/', ' ')),
			'PrintName'			  => strtoupper(Str::of($fullname)->replaceMatches('/ {2,}/', ' ')),
			'DisplayName'		  => strtoupper(Str::of($fullname)->replaceMatches('/ {2,}/', ' ')),
			'LastName'            => strtoupper(Str::of($request->input('lastname'))->replaceMatches('/ {2,}/', ' ')),
			'Suffix'              => $request->input('SuffixName'),
			'FirstName'           => strtoupper(Str::of($request->input('firstname'))->replaceMatches('/ {2,}/', ' ')),
			'MiddleName'          => strtoupper(Str::of($request->input('middlename'))->replaceMatches('/ {2,}/', ' ')),
			'PRCNo'               => $request->input('prcno'),
			'PRCValidity'         => $validityDate,
			'DOB'                 => $dateOfBirth,
			'Email'               => $request->input('email'),
			'Mobile'              => $request->input('mobile'),
			'NWDBranch'       	  => $branchJson,
			'Schedule'        	  => $scheduleJson,
			'TimeStart'			  => $timeStartJson,
			'TimeEnd'			  => $timeEndJson,
			'FirstEngagement' 	  => $firstengagementJson,
			'LastEngagement'  	  => $lastengagementJson,
			'ByAppointment'		  => $byAppointmentJson,
			'ClinicScheduledBy'   => $inputByJSON,
			'PCP'                 => $request->input('primaryCarePhysician') == 'Yes' ? 'Yes' : 'No',
			'Specialist'          => $request->input('specialistConsultant') == 'Yes' ? 'Yes' : 'No',
			'ResignDoctor'        => $request->input('resigned') == 'Yes' ? 'Yes' : 'No',
			'RP'                  => $request->input('referringPhysician') == 'Yes' ? 'Yes' : 'No',
			'Regular'             => $request->input('regular') == 'Yes' ? 'Yes' : 'No',
			'Reliever'            => $request->input('reliever') == 'Yes' ? 'Yes' : 'No',
			'Visiting'            => $request->input('visiting') == 'Yes' ? 'Yes' : 'No',
			'Referring'           => $request->input('referringStatus') == 'Yes' ? 'Yes' : 'No',
			'ApplicationLetter'   => $request->input('applicationLetter') == 'Yes' ? 'Yes' : 'No',
			'CurriculumVitae'     => $request->input('curriculumVitae') == 'Yes' ? 'Yes' : 'No',
			'Diploma'             => $request->input('medicalSchoolDiploma') == 'Yes' ? 'Yes' : 'No',
			'PRCId'               => $request->input('prcId') == 'Yes' ? 'Yes' : 'No',
			'ResidencyCertificate'=> $request->input('residencySpecialtyCert') == 'Yes' ? 'Yes' : 'No',
			'DiplomateCertificate'=> $request->input('diplomateFellowCert') == 'Yes' ? 'Yes' : 'No',
			'PhilHealth'          => $request->input('philHealth') == 'Yes' ? 'Yes' : 'No',
			'PTR'                 => $request->input('ptr') == 'Yes' ? 'Yes' : 'No',
			'BIR'                 => $request->input('bir') == 'Yes' ? 'Yes' : 'No',
			'MOA'                 => $request->input('MOA') == 'Yes' ? 'Yes' : 'No',
			'SubGroup' 			  => $request->input('resigned') === 'Yes' ? 'RP' : ($request->filled('p_subgroup') && $request->input('specialistConsultant') !== 'Yes' ? strtoupper(Str::of($request->input('p_subgroup'))->replaceMatches('/ {2,}/', ' ')) : ($request->input('specialistConsultant') === 'Yes' ? 'SPL' : 'PCP')), //add this for the SubGroup type
			'Description'         => strtoupper($request->input('specialty')),
			'SubDescription'      => strtoupper($request->input('subSpecialty')),
			'UpdateDate'          => now(),
			'UpdateBy'            => Auth::user()->username,
			'Status' 			  => $request->input('resigned') === 'Yes' ? 'RP - Leads' : 'For Approval',   //add condtion on status if physician is resigned
		];

		if (!$isApprover) {
			$updateData['BranchRequestCode'] = session('userClinicCode');
		}

		//dd($updateData);

		DB::connection('Eros')->table('Physician')->where('Id', $id)->update($updateData);

		// Fetch updated physician data
		$physicianData = ErosDB::getDoctorData();
		return $this->edit($id);
	}



}
