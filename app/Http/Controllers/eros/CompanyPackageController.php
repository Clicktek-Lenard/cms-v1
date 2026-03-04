<?php

namespace App\Http\Controllers\eros;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;



class CompanyPackageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
	public function create()
	{
		//return 'ricky';//view('eros.physicianListCreate');
	}

	public function index()
	{
		
	}
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
      
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
	//$packageData = ErosDB::getCompanyPackageData($id);
	$itemData = ErosDB::getItemData();
	$clinics = ErosDB::getClinicData();
	return view('eros.companyPackageNew', ['itemData'=>json_encode($itemData), 'clinics'=> $clinics, 'datas' => $datas, 'postLink' => url(session('userBU').'/erosui/company/package/'.$id)]); 
    
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
	$packageName = ErosDB::getCompanyPackageName($_GET['id']);
	$packageData = ErosDB::getCompanyPackageData($_GET['id']);
	$itemData = ErosDB::getItemData($_GET['id']);
	$clinics = ErosDB::getClinicData();
	return view('eros.companyPackageEdit', ['itemData'=>json_encode($itemData), 'clinics'=> $clinics, 'packageData'=>json_encode($packageData), 'packageName' => $packageName, 'datas' => $datas, 'postLink' => url(session('userBU').'/erosui/company/itemspackages/package/'.$id.'/edit/?id='.$_GET['id'] )]); 
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
	
    }
    
     public function newSaveAjax()
    { 
	if( ! $checking = $this->CheckModuleAccess() )
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
    
	//Create ItemPrice code and PriceGroup as Package
	DB::connection('Eros')->beginTransaction();
	$CompanyCode = DB::connection('Eros')->select("SELECT `Code`, `ShortName`, `ErosCode` FROM `Company` WHERE `Id` = '".$_POST['Id']."'  ");
	$max = DB::connection('Eros')->select("SELECT SUBSTR(MAX(Code),-4) as iMax FROM ItemPrice WHERE `Code` LIKE  'PCK".date('ym')."%'  ");
	$iCode = 'PCK'.date('ym').sprintf('%04d', $max[0]->iMax+1);
	$descEros = Str::of(strtoupper($CompanyCode[0]->ShortName. " - ".$_POST['PackageName']))->replaceMatches('/ {2,}/', ' ');
	$ItemPriceId =  DB::connection('Eros')->table('ItemPrice')->insertGetId([
		'ClinicCode'		=> $_POST['ClinicCode'],
		'Code'			=> $iCode,
		'Description' 		=> Str::of(strtoupper($_POST['PackageName']))->replaceMatches('/ {2,}/', ' '),
		'DescriptionEros'	=> $descEros,
		'CompanyCode'		=> $CompanyCode[0]->ErosCode,
		'Price'			=> $_POST['PackageAmount'],
		'PriceGroup'		=> 'Package',
		'InputDate'		=> date('Y-m-d'),
		'InputBy'			=> Auth::user()->username
		//'ErosStatus'		=>  null
	]);
	
	// EROS Start
	
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
	//ITEM MASTER
	
	$Msql = "INSERT INTO ITEM_MASTER (IM_CODE, IM_DESCRIPTION, IM_SHORT_DESC, IM_CREATED_ON, IM_CREATED_BY, IM_ACTIVE, IM_CPTPANEL, IM_CPTPARTIAL, IM_ISPARENT, IM_GROUP, IM_PRICE, IM_TG) VALUES
	(:IM_CODE, :IM_DESCRIPTION, :IM_SHORT_DESC, :IM_CREATED_ON, :IM_CREATED_BY, :IM_ACTIVE, :IM_CPTPANEL, :IM_CPTPARTIAL, :IM_ISPARENT, :IM_GROUP, :IM_PRICE, :IM_TG)";
	$Mcompiled = oci_parse($conn, $Msql);
		
			$IM_CODE = $iCode;
			$IM_DESCRIPTION = (string)$descEros;
			$IM_SHORT_DESC = '';
			$IM_CREATED_ON = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y'); //"27-JUN-2022";
			$IM_CREATED_BY = Auth::user()->username;
			$IM_ACTIVE = 'Y';
			$IM_CPTPANEL = 'N';
			$IM_CPTPARTIAL = 'N';
			$IM_ISPARENT = 'N';
			$IM_GROUP = 'PACK';
			$IM_PRICE = $_POST['PackageAmount'];
			$IM_TG = 'PACK';
				
			oci_bind_by_name($Mcompiled, ":IM_CODE", $IM_CODE);
			oci_bind_by_name($Mcompiled, ":IM_DESCRIPTION", $IM_DESCRIPTION);
			oci_bind_by_name($Mcompiled, ":IM_SHORT_DESC", $IM_SHORT_DESC);
			oci_bind_by_name($Mcompiled, ":IM_CREATED_ON", $IM_CREATED_ON);
			oci_bind_by_name($Mcompiled, ":IM_CREATED_BY", $IM_CREATED_BY);
			oci_bind_by_name($Mcompiled, ":IM_ACTIVE", $IM_ACTIVE);
			oci_bind_by_name($Mcompiled, ":IM_CPTPANEL", $IM_CPTPANEL);
			oci_bind_by_name($Mcompiled, ":IM_CPTPARTIAL", $IM_CPTPARTIAL);
			oci_bind_by_name($Mcompiled, ":IM_ISPARENT", $IM_ISPARENT);
			oci_bind_by_name($Mcompiled, ":IM_GROUP", $IM_GROUP);
			oci_bind_by_name($Mcompiled, ":IM_PRICE", $IM_PRICE);
			oci_bind_by_name($Mcompiled, ":IM_TG", $IM_TG);
			

			$Mresult = oci_execute($Mcompiled);
		
			oci_close($conn);
	
	
	
	// ITEM PRICE
	$sql = "INSERT INTO ITEM_PRICE (IP_COMPANY, IP_ITEM_CODE, IP_ENABLED, IP_UPDATE_ON, IP_UPDATE_BY, IP_PRICE, IP_OLD_PRICE, IP_REGULAR_PRICE) VALUES
	(:IP_COMPANY, :IP_ITEM_CODE, :IP_ENABLED, :IP_UPDATE_ON, :IP_UPDATE_BY, :IP_PRICE, :IP_OLD_PRICE, :IP_REGULAR_PRICE)";
	$compiled = oci_parse($conn, $sql);
			$IP_COMPANY = $CompanyCode[0]->ErosCode;
			$IP_ITEM_CODE = $iCode;
			$IP_ENABLED = 'Y';
			$IP_UPDATE_ON = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y'); //"27-JUN-2022";
			$IP_UPDATE_BY = Auth::user()->username;
			$IP_PRICE = $_POST['PackageAmount'];
			$IP_OLD_PRICE = $_POST['PackageAmount'];
			$IP_REGULAR_PRICE = $_POST['PackageAmount'];
			
			oci_bind_by_name($compiled, ":IP_COMPANY", $IP_COMPANY);
			oci_bind_by_name($compiled, ":IP_ITEM_CODE", $IP_ITEM_CODE);
			oci_bind_by_name($compiled, ":IP_ENABLED", $IP_ENABLED);
			oci_bind_by_name($compiled, ":IP_UPDATE_ON", $IP_UPDATE_ON);
			oci_bind_by_name($compiled, ":IP_UPDATE_BY", $IP_UPDATE_BY);
			oci_bind_by_name($compiled, ":IP_PRICE", $IP_PRICE);
			oci_bind_by_name($compiled, ":IP_OLD_PRICE", $IP_OLD_PRICE);
			oci_bind_by_name($compiled, ":IP_REGULAR_PRICE", $IP_REGULAR_PRICE);

			$result = oci_execute($compiled);
			
			if (!$result){
			    $e = oci_error($result);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}else{
				DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`ErosStatus` = 'APPEND' WHERE tb1.Id = '".$ItemPriceId."'  ");
			}
			oci_close($conn);
	
	// EROS End
	
	
	//Create package composition
	foreach($_POST['itemSelected'] as $itemId)
	{
		$code  =  DB::connection('Eros')->select("SELECT `Code` FROM ItemMaster WHERE `Id` = '".$itemId."' ");
		DB::connection('Eros')->table('Package')->insertGetId([
			'ItemPriceId'		=> $ItemPriceId,
			'ItemCode'			=> $code[0]->Code
		]);
	}
	DB::connection('Eros')->commit();
	return $ItemPriceId;
	//print_r(array($_POST['itemSelected']));
    }
    
    public function editSaveAjax()
    { 
	if( ! $checking = $this->CheckModuleAccess() )
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
	
	//Create ItemPrice code and PriceGroup as Package
	DB::connection('Eros')->beginTransaction();
	$CompanyCode = DB::connection('Eros')->select("SELECT `Code`, `ShortName` FROM `Company` WHERE `Id` = '".$_POST['Id']."'  ");
	//$max = DB::connection('Eros')->select("SELECT SUBSTR(MAX(Code),-4) as iMax FROM ItemPrice WHERE `Code` LIKE  'PCK".date('y')."%'  ");
	
	$ItemPriceId =  DB::connection('Eros')->table('ItemPrice')
	->where('Id',$_POST['IdItem'])
	->lockForUpdate()
	->update([
		'ClinicCode'		=> $_POST['ClinicCode'],
		//'Code'			=> 'PCK'.date('y').sprintf('%04d', $max[0]->iMax+1),
		'Description' 		=> Str::of(strtoupper($_POST['PackageName']))->replaceMatches('/ {2,}/', ' '),
		'DescriptionEros'	=> Str::of(strtoupper($CompanyCode[0]->ShortName. " - ".$_POST['PackageName']))->replaceMatches('/ {2,}/', ' '),
		//'CompanyCode'	=> $CompanyCode[0]->Code,
		'Price'			=> $_POST['PackageAmount'],
		'UpdateDate'		=> date('Y-m-d'),
		'UpdateBy'			=>Auth::user()->username,
		'ErosStatus'		=> 'reUpdate'
		//'InputDate'		=> date('Y-m-d'),
		//'InputBy'			=> Auth::user()->username
		//'ErosStatus'		=>  null
	]);
	//Delete package composition
	DB::connection('Eros')->table('Package')->where('ItemPriceId', '=', $_POST['IdItem'] )->delete();
	//Create package composition
	foreach($_POST['itemSelected'] as $itemId)
	{
		$code  =  DB::connection('Eros')->select("SELECT `Code` FROM ItemMaster WHERE `Id` = '".$itemId."' ");
		DB::connection('Eros')->table('Package')->insertGetId([
			'ItemPriceId'		=> $_POST['IdItem'],
			'ItemCode'			=> $code[0]->Code
		]);
	}
	DB::connection('Eros')->commit();
	return $_POST['IdItem'];
	//print_r(array($_POST['itemSelected']));
    }
    
    
}
