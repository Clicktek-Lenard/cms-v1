<?php

namespace App\Http\Controllers\eros;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

class CompanyController extends Controller
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
    // public function create()
    //{
//	 return 'ricky';//view('eros.physicianListCreate');
  //  }

public function index()
{


	$companyData = ErosDB::getCompanyData();
      return view('eros.companyList', ['companyData' => json_encode($companyData)]);

}
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
     public function create()
    {
	return view('eros.companyListCreate');
	
    }
   
	/**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
	if( ! $checking = $this->CheckModuleAccess() )
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
	
	DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr);
	$erosCode = strtoupper(Str::of(substr($request->input('guarantor'), 0, 3))->replaceMatches('/ {2,}/', ' '));
	$erosCodeiMax = ErosDB::getCompanyMAX($erosCode);  
	$erosCodeMax = $erosCode.sprintf('%06d',$erosCodeiMax);
	
		//Eros Clinician_Details
		$sqlInsert = "INSERT into COMPANY_DETAILS (CD_CODE,CD_NAME,CD_TYPE,CD_ACTIVE,CD_TELNO,CD_ADDRESS,CD_CITY,CD_EMAIL,CD_CREATED_ON,CD_CREATED_BY) VALUES (
		:CD_CODE,:CD_NAME,:CD_TYPE,:CD_ACTIVE,:CD_TELNO,:CD_ADDRESS,:CD_CITY,:CD_EMAIL,:CD_CREATED_ON,:CD_CREATED_BY
		)";
		$compiled = oci_parse($conn, $sqlInsert);
		
		$CD_NAME = substr(strtoupper(Str::of($request->input('guarantor'))->replaceMatches('/ {2,}/', ' ')),0, 300);
		$CD_TYPE = $request->input('group');
		$subGroup = $request->input('subgroup');
		$CD_ACTIVE = 'Y';
		$CD_TELNO = strtoupper(Str::of($request->input('phone'))->replaceMatches('/ {2,}/', ' '));
		$CD_ADDRESS = strtoupper(Str::of($request->input('address'))->replaceMatches('/ {2,}/', ' '));
		$CD_CITY = strtoupper(Str::of($request->input('city'))->replaceMatches('/ {2,}/', ' '));
		$CD_EMAIL = $request->input('email');
		$CD_CREATED_ON = date('d-M-Y');
		$CD_CREATED_BY = 'RAV';
		
		oci_bind_by_name($compiled, ":CD_CODE", $erosCodeMax);	
		oci_bind_by_name($compiled, ":CD_NAME", $CD_NAME);
		oci_bind_by_name($compiled, ":CD_TYPE", $CD_TYPE);
		//subGroup only in CMS
		oci_bind_by_name($compiled, ":CD_ACTIVE", $CD_ACTIVE);
		oci_bind_by_name($compiled, ":CD_TELNO", $CD_TELNO);
		oci_bind_by_name($compiled, ":CD_ADDRESS", $CD_ADDRESS);
		oci_bind_by_name($compiled, ":CD_CITY", $CD_CITY);
		oci_bind_by_name($compiled, ":CD_EMAIL", $CD_EMAIL);
		oci_bind_by_name($compiled, ":CD_CREATED_ON", $CD_CREATED_ON);
		oci_bind_by_name($compiled, ":CD_CREATED_BY", $CD_CREATED_BY);
		
		$result = oci_execute($compiled);
		
		if (!$result) {
		    $e = oci_error($query);  // For oci_execute errors pass the statement handle
		    print htmlentities($e['message']);
		    print "\n<pre>\n";
		    print htmlentities($e['sqltext']);
		    printf("\n%".($e['offset']+1)."s", "^");
		    print  "\n</pre>\n";
		}else{
			print "<br>Connected and Inserted to Oracle! PROD EROS";  
			$dataCompnay = 
			[
				[
					'Num'		=> $erosCodeiMax
					,'Code'		=> $erosCodeMax
					,'Name'		=> $CD_NAME
					,'Group'		=> $CD_TYPE
					,'ShortName'	=> $request->input('shortname')
					,'SubGroup'	=> $subGroup
					,'Address'		=> $CD_ADDRESS
					,'Phone'		=> $CD_TELNO
					,'City'		=> $CD_CITY
					,'Email'		=> $CD_EMAIL
					,'ResultUploading'=> $request->input('resultuploading')
					,'Status'		=> 'Active'
					,'InputBy'		=> Auth::user()->username
					,'InputDate'	=> date('Y-m-d')
					,'ErosCode'	=> $erosCodeMax
				]
			];
			DB::connection('Eros')->table('Company')->insert($dataCompnay);

		}
		oci_close($conn);
		DB::connection('Eros')->commit();
		
	$companyData = ErosDB::getCompanyData();
	return view('eros.companyList', ['companyData' => json_encode($companyData)]);
	
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $datas = ErosDB::getCompanyData($id);
	return view('eros.companyListEdit', ['datas' => $datas, 'postLink' => url(session('userBUCode').'/erosui/company/'.$id)]);    
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
	$datas = ErosDB::getCompanyData($id);
	return view('eros.companyListEdit', ['datas' => $datas, 'postLink' => url(session('userBUCode').'/erosui/company/'.$id)]);    
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
	if( ! $checking = $this->CheckModuleAccess() )
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
	DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr);
		$erosCodeUpd = DB::connection('Eros')->select("SELECT `ErosCode` from Company where `Id` = '".$request->input('_id')."' " );
		
		if(empty($erosCodeUpd[0]->ErosCode))
		{
			return "Eros Code not Found...";
			die();
		}
		//Eros Clinician_Details
		$CD_NAME = substr(strtoupper(Str::of($request->input('guarantor'))->replaceMatches('/ {2,}/', ' ')),0, 300);
		$sqlUpdate = "UPDATE COMPANY_DETAILS SET 
			CD_NAME = '".str_replace("'","''",$CD_NAME)."'
			,CD_TYPE = '".$request->input('group')."'
			,CD_TELNO = '".$request->input('phone')."'	
			,CD_ADDRESS = '".$request->input('address')."'	
			,CD_CITY =  '".$request->input('city')."'	
			,CD_EMAIL =  '".$request->input('email')."'	
			WHERE
			CD_CODE LIKE '".$erosCodeUpd[0]->ErosCode."'
		";
		$compiled = oci_parse($conn, $sqlUpdate);
		
		$result = oci_execute($compiled);
		
		if (!$result) {
		    $e = oci_error($query);  // For oci_execute errors pass the statement handle
		    print htmlentities($e['message']);
		    print "\n<pre>\n";
		    print htmlentities($e['sqltext']);
		    printf("\n%".($e['offset']+1)."s", "^");
		    print  "\n</pre>\n";
		}else{
			print "<br>Connected and Inserted to Oracle! PROD EROS"; 
			DB::connection('Eros')->table('Company')->where('Id',$request->input('_id'))
				->lockForUpdate()
				->update([
					'Name'		=> strtoupper(Str::of($request->input('guarantor'))->replaceMatches('/ {2,}/', ' '))
					,'Group'		=> $request->input('group')
					,'ShortName'	=> $request->input('shortname')
					,'SubGroup'	=> $request->input('subgroup')
					,'Address'		=> $request->input('address')
					,'Phone'		=> $request->input('phone')
					,'City'		=> $request->input('city')
					,'Email'		=> $request->input('email')
					,'ResultUploading'=> $request->input('resultuploading')
					,'UpdateBy'	=> Auth::user()->username
					,'UpdateDate'	=> date('Y-m-d')
					,'CebuStatus'	=> 'reUpdate'
					,'SMBStatus'	=> 'reUpdate'
					,'TARStatus'	=> 'reUpdate'
					,'DAVStatus'	=> 'reUpdate'				
				]); 
		}
		
		
		
		oci_close($conn);
	DB::connection('Eros')->commit();	 

	return $this->edit($id);
    } 
    
    
}
