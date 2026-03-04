<?php //dtu

namespace App\Models\eros;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\Controller;

class ErosDB extends Model
{

	public static function getDoctorDatas($id = null) // DOCTORS MODULE MASTER LIST
	{
		$isApprover = strpos(session('userRole'), '"ldap_role":"[PHYSICIAN-APPROVER]"') !== false;

		if (!empty($id)) {
			return DB::connection('Eros')->table('Physician')->where('Id', '=', $id)->get();
		}

		$query = DB::connection('Eros')->table('Physician');

		if ($isApprover) {
			$query->whereIn('SubGroup', ['PCP', 'SPL']);
			$query->whereIn('Status', ['Approved', 'Active']);
		} else {
			$query->whereIn('SubGroup', ['PCP', 'SPL', 'RP']);
			$query->whereIn('Status', ['RP - Leads', 'Active', 'Approved']);
		}
	
		return $query->get();
	}

	public static function getDoctorData($id = null)
	{
		if (empty($id)) {
			$query = DB::connection('Eros')->table('Physician');
			$isApprover = strpos(session('userRole'), '"ldap_role":"[PHYSICIAN-APPROVER]"') !== false;        //physician accreditation module
	
			if (!$isApprover) {
				//$query->where('BranchCode', '=', session('userClinicCode'));   // change to only see the requested clinic if for approval and disproved
				$query->where('BranchRequestCode', '=', session('userClinicCode'));
				$query->whereIn('Status', ['For Approval', 'Disapproved']);
			} else {
				$query->where('Status', '=', 'For Approval');
			}
	
			$data = $query->get();
		} else {
			$data = DB::connection('Eros')->table('Physician')->where('Id', '=', $id)->get();
		}
	
		return collect($data);
	}


	public static function getPhysicianDatas($id = null) 								//RP ENROLLEMNT - SEARCHING OF OUTSIDEPHYSICIANCONTROLLER
	{
		$query = DB::connection('Eros')->table('Physician'); 
	
		if (!empty($id)) {
			$query->where('Id', $id); 
		}
	
		return $query; 
	}

	public static function getPhysicianData($id = NULL) //RP ENROLLEMNT
	{ 
		if(empty($id) ){
			$data = DB::connection('Eros')->select("SELECT * FROM Physician");
		}else{
			$data = DB::connection('Eros')->select("SELECT * FROM Physician WHERE Id = '".$id."' ");
		}
	
		// Convert array to Collection
		return collect($data);
	}

	public static function getPhysicianType($id = null) 								//Physician accreditation Type
	{
		if(empty($id) ){
			$data = DB::connection('Eros')->select("SELECT * FROM PhysicianType");
		}else{
			$data = DB::connection('Eros')->select("SELECT * FROM PhysicianType WHERE Id = '".$id."' ");
		}
	
		return $data; 
	}

	
	public static function getPhysicianMAX($erosCode = NULL)
	{
		//$max = DB::connection('Eros')->select("SELECT SUBSTR(MAX(Id),-6) as iMax from Physician " );
		$max = DB::connection('Eros')->select("SELECT MAX(Id) as iMax from Physician " );
		return  $erosCode.sprintf('%06d', $max[0]->iMax+1);
	}
	
	public static function getPatientMAX($code)
	{
		$max = DB::connection('Eros')->select("SELECT SUBSTR(MAX(Code),-4) as iMax from Patient WHERE  Code like '".$code.date('Ymd')."%'  " );
		return  $code.date('Ymd').sprintf('%04d', $max[0]->iMax+1);
	}
	
	public static function getErosByCode($erosCode = NULL)
	{
		if(is_null($erosCode))
			die('Missing Eros Code');
		else
			return DB::connection('Eros')->select("SELECT * from Physician WHERE `ErosCode` LIKE  '".$erosCode."'  " );
	}
	
	public static function getCompanyData($id = NULL)
	{
		$myServer = Controller::getMyDBID();
		if(empty($id) ){
			$data = DB::connection('Eros')->select("SELECT * FROM Company");
		}else{
			$data = DB::connection('Eros')->select("SELECT * FROM Company WHERE Id = '".$id."' ");
		}
		return $data;
	}
	
	public static function getItemMasterData($id = NULL, $status = NULL, $group = NULL)
	{
		if (empty($id) && empty($status) && empty($group)) {
			$data = DB::connection('Eros')->select("SELECT * FROM ItemMaster");
		} else if (!empty($status)) {
			$data = DB::connection('Eros')->select("SELECT * FROM ItemMaster WHERE OrderStatus LIKE 'Y' AND ItemStatus LIKE '".$status."' ");
		} else if (!empty($group)) {
			$data = DB::connection('Eros')->select("SELECT * FROM ItemMaster WHERE `Group` = '".$group."' ");
		} else {
			$data = DB::connection('Eros')->select("SELECT * FROM ItemMaster WHERE Id = '".$id."' ");
		}
		return $data;
	}
	
	public static function getItemData($id = NULL, $companyCode = NULL)
	{
		if(empty($id) && empty($companyCode) ){
			$data = DB::connection('Eros')->select("SELECT * FROM ItemMaster WHERE `OrderStatus` LIKE 'Y'");
		}elseif(empty($id) && !empty($companyCode) ){ // slow query neeed to update
			$data = DB::connection('Eros')->select("
			select `Id`, `Code`, `Description`, `PriceGroup` as `PriceGroup`, `TagPrice`  from 
				(
					(select `Id`, `Code`, `Description`, `PriceGroup`, `Price`  as `TagPrice` from `ItemPrice` where `CompanyCode` like '".$companyCode."' AND `ClinicCode` LIKE 'ALL' )
				union 
					(select `Id`, `Code`, `Description`,  `Type` as PriceGroup, '0' as TagPrice from `ItemMaster` WHERE `OrderStatus` LIKE 'Y' )
				) as `p_pn`
				GROUP BY `p_pn`.`Code` 
			
			");
		}elseif(!empty($id) && !empty($companyCode) ){
			$data = DB::connection('Eros')->select("SELECT *
				FROM ItemPrice WHERE `Code` = '".$id."' AND `CompanyCode` = '".$companyCode."'  ");
		}else{
			//$data = DB::connection('Eros')->select("SELECT * FROM ItemMaster WHERE Id = '".$id."' ");
			$data = DB::connection('Eros')->select("SELECT tb1.* , IFNULL(tb1.HCLABCode, tb1.OldCode) AS 'SystemFrom'  
			FROM ItemMaster tb1 WHERE `OrderStatus` LIKE 'Y' and  NOT EXISTS  (SELECT tb2.ItemCode FROM Package tb2 WHERE tb1.Code = tb2.ItemCode and tb2.ItemPriceId = '".$id."' ) ");
		}
		return $data;
	}
	
	public static function getItemPriceData($id = NULL, $status = NULL)
	{
		if(empty($id) && empty($status) ){
			$data = DB::connection('Eros')->select("SELECT * FROM ItemPrice");
		}else if(!empty($id) && !empty($status) )
		{
			$data = DB::connection('Eros')->select("SELECT * FROM ItemPrice WHERE `Status` = '".$status."' AND `CompanyCode` = '".$id."' ");
		}
		else{
			$data = DB::connection('Eros')->select("SELECT * FROM ItemPrice WHERE `CompanyCode` = '".$id."' ");
		}
		return $data;
	}
	
	public static function getCompanyPackageName($id = NULL)
	{
		if(empty($id) ){
			return 'Missing Id';
		}else{
			$data = DB::connection('Eros')->select("SELECT * FROM ItemPrice WHERE `Id` = '".$id."' ");
		}
		return $data;
	}
	
	public static function getCompanyPackageData($id = NULL)
	{
		if(empty($id) ){
			return 'Missing Id';
		}else{
			$data = DB::connection('Eros')->select("SELECT tb2.Id, tb2.Code, tb2.Description, tb2.DepartmentGroup, IFNULL(tb2.HCLABCode, tb2.OldCode) AS 'SystemFrom' FROM Package tb1 LEFT JOIN ItemMaster tb2 ON (tb1.ItemCode = tb2.Code)  WHERE tb1.`ItemPriceId` = '".$id."' ");
		
		}
		return $data;
	}
	
	public static function getCompanyMAX($erosCode = NULL)
	{
		$max = DB::connection('Eros')->select("SELECT MAX(Num) as iMax from Company " );
		return $max[0]->iMax+1;
		//return  $erosCode.sprintf('%06d', $max[0]->iMax+1);
	}
	
	public static function getClinicData($code = NULL, $ips = NULL, $strCode = NULL)
	{
		if(!empty($ips))
		{
			return DB::connection('Eros')->select("SELECT * FROM BusinessUnits WHERE ( `IPs` LIKE '".$ips."%' OR `IPs2` LIKE '".$ips."%' ) ORDER BY `Code` ASC "); 
		}
		if(!empty($strCode))
		{
			$strCode = implode("','", $strCode);
			$strCode = "('" . $strCode . "')";
			return DB::connection('Eros')->select("SELECT * FROM BusinessUnits WHERE `Code` IN $strCode ORDER BY `Code` ASC");
		}
		if(empty($code) ){
			$data = DB::connection('Eros')->select("SELECT * FROM BusinessUnits ORDER BY `Code` ASC");
		}else{
			$data = DB::connection('Eros')->select("SELECT * FROM BusinessUnits WHERE `Code` LIKE '".$code."' ORDER BY `Code` ASC ");
		}
		return $data;
	
	}
		
	public static function checkErosNameInsertTemp($params = array())  
	{
		//check first if exsit in temp
		$temp = DB::connection('Eros')->table('Patient_temp')
		     ->select('Id')
		     ->where('LastName', 'LIKE', $params['LastName'])
		     ->where('FirstName', 'LIKE', $params['FirstName'])
		     ->where('Gender', 'LIKE', $params['Gender'])
		     ->where('DOB', '=', $params['DOBORG'])
		     ->where('APEDATE', '=', $params['APEDate'])
		     ->get();
		
		
		//$temp = DB::connection('Eros')->select("SELECT `Id` FROM `Patient_temp` WHERE  `LastName` LIKE '".$params['LastName']."' and `FirstName` like '".$params['FirstName']."'
		//							and `Gender` LIKE '".$params['Gender']."%' and `DOB` = '".$params['DOBORG']."' and  `APEDATE` = '".$params['APEDate']."' LIMIT 1 ");
	
		if(count($temp) != 0)
		{ 
			DB::connection('Eros')->update("UPDATE `Patient_temp` SET `FileStatus` = '".$params['UploadID']."'   WHERE  `Id` = '".$temp[0]->Id."'  ");
			return "1"; 
		}
			
		
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.154.8)(PORT = 1521))  (CONNECT_DATA= (SID = erosprod))     )';
		$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
		
		///htmlentities(oci_result($stid, 'CD_NAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
		
		$sql = "SELECT PM_PID, PM_FULLNAME, PM_LASTNAME, PM_FIRSTNAME, PM_MIDNAME, PM_GENDER, PM_SUFFIX, TO_CHAR(PM_DOB, 'YYYY-MM-DD') AS PM_DOB, PM_ADDRESS, PM_CITY, PM_MOBILENO, PM_ACTIVE
		FROM patient_master WHERE PM_LASTNAME like '".htmlentities($params['LastName'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."' 
		AND PM_FIRSTNAME like '".htmlentities($params['FirstName'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."' 
		AND PM_GENDER like '".$params['Gender']."' AND PM_DOB = '".$params['DOB']."'  AND ROWNUM <= 1  ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		$i =1;
		$return = 0;
		while (oci_fetch($stid)) {
			$eDOB = (oci_result($stid, 'PM_ACTIVE') == 'Y')?'Active':'Inactive';
		
			DB::connection('Eros')->insert("INSERT INTO `Patient_temp` (`Code`, `FullName`, `LastName`, `FirstName`, `MiddleName`, `Suffix`, `Gender`, `DOB`, `Address`, `City`, `Status`, `Remarks`, `UploadID`, `APEDate`, `WithTest`)  VALUE
					('".oci_result($stid, 'PM_PID')."' 
					,'".htmlentities(oci_result($stid, 'PM_FULLNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."' 
					,'".htmlentities(oci_result($stid, 'PM_LASTNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."' 
					,'".htmlentities(oci_result($stid, 'PM_FIRSTNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."' 
					,'".htmlentities(oci_result($stid, 'PM_MIDNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."' 
					,'".oci_result($stid, 'PM_SUFFIX')."' 
					,'".oci_result($stid, 'PM_GENDER')."' 
					,'".date('Y-m-d', strtotime(oci_result($stid, 'PM_DOB')))."' 
					,'".htmlentities($params['Address'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."' 
					,'".oci_result($stid, 'PM_CITY')."' 
					,'".$eDOB."' 
					,'FROM EROS'
					,'".$params['UploadID']."'
					,'".$params['APEDate']."'
					,'".$params['wTest']."'
					)
			");
			$return++;
		
		}
		oci_close($conn);
		
		return $return;
	}
	//Insert new patient from 
	public static function insertNewPatient($params = array())
	{
		//local
		$data = 
		[
		    [
			'Code'		=> $params['Code'],
			'FullName'		=> $params['FullName'],
			'LastName'	=> $params['LastName'],
			'FirstName'	=> $params['FirstName'],
			'MiddleName'	=> $params['MiddleName'],
			'Suffix'		=> $params['Suffix'],
			'Gender'		=> $params['Gender'],
			'DOB'		=> $params['DOB'],
			'Address'		=> $params['Address'],
			'City'			=> $params['City'],
			'Nationality'	=> $params['Nationality'],
			'Status'		=> $params['Status'],
			'Remarks'		=> $params['Remarks'],
			'UploadID'		=> $params['UploadID'],
			'InputBy'		=> $params['InputBy'],
			'InputDate'	=> $params['InputDate']
		    ]
		];
		
		DB::connection('Eros')->table('Patient')->insert($data);
		
		
		//Temp
		$dataTemp = 
		[
		    [
			'Code'		=> $params['Code'],
			'FullName'		=> $params['FullName'],
			'LastName'	=> $params['LastName'],
			'FirstName'	=> $params['FirstName'],
			'MiddleName'	=> $params['MiddleName'],
			'Suffix'		=> $params['Suffix'],
			'Gender'		=> $params['Gender'],
			'DOB'		=> $params['DOB'],
			'Address'		=> $params['Address'],
			'City'			=> $params['City'],
			'Status'		=> $params['Status'],
			'Remarks'		=> $params['Remarks'],
			'UploadID'		=> $params['UploadID'],
			'APEDate'		=> $params['APEDate'],
			'WithTest'		=> $params['wTest']
		    ]
		];
		
		DB::connection('Eros')->table('Patient_temp')->insert($dataTemp);
	
	}
	//Check local DB
	public static function checkLocalName($params = array())
	{
		$return = DB::connection('Eros')->table('Patient')
		     ->select('*')
		     ->where('LastName', 'LIKE', $params['LastName'])
		     ->where('FirstName', 'LIKE', $params['FirstName'])
		     ->where('Gender', 'LIKE', $params['Gender'])
		     ->where('DOB', '=', $params['DOB'])
		     ->get();
		
		//$return = DB::connection('Eros')->select("SELECT * from Patient where `LastName` like '".$params['LastName']."' and `FirstName` like '".$params['FirstName']."' and `Gender` like '".$params['Gender']."' and `DOB` = '".$params['DOB']."'   ");
		if(count($return) != 0)
		{
			$dataTemp = 
			[
			    [
				'Code'		=> $return[0]->Code,
				'FullName'		=> $return[0]->FullName,
				'LastName'	=> $return[0]->LastName,
				'FirstName'	=> $return[0]->FirstName,
				'MiddleName'	=> $return[0]->MiddleName,
				'Suffix'		=> $return[0]->Suffix,
				'Gender'		=> $return[0]->Gender,
				'DOB'		=> $return[0]->DOB,
				'Address'		=> $params['Address'],
				'City'			=> $return[0]->City,
				'Status'		=> $return[0]->Status,
				'Remarks'		=> $return[0]->Remarks,
				'UploadID'		=> $params['UploadID'],
				'APEDate'		=> $params['APEDate'],
				'WithTest'		=> $params['wTest']
			    ]
			];
			DB::connection('Eros')->table('Patient_temp')->insert($dataTemp);
			
		}
		return count($return);
	}
	
	public static function getPatientServer($params = array())
	{
		DB::connection('Eros')->beginTransaction();
		$today = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y');
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.154.8)(PORT = 1521))  (CONNECT_DATA= (SID = erosprod))     )';
		$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
		
		$sql = "SELECT PM_PID,  PM_FULLNAME , BTH_NPI ,  BTH_PID , BTH_TRXNO , BTH_TRXDT , to_char(PM_DOB, 'yyyy-mm-dd') as PM_DOB, BTH_TRXDT, CD_NAME, PM_ADDRESS, PM_CITY, PM_EMAIL, PM_MOBILENO,
				PM_LASTNAME, PM_FIRSTNAME, PM_MIDNAME, PM_SUFFIX, PM_GENDER
		FROM billing_trx_hdr
		INNER JOIN patient_master on bth_pid = pm_pid
		LEFT JOIN company_details on bth_company = company_details.cd_code
		WHERE  ROWNUM <= 1000 ";
		//to_char(BTH_TRXDT, 'yyyy-mm-dd')  = '".date('Y-m-d')."' and 
		if(!empty($params['fullname']))
		{
			$sql = $sql . "and PM_FULLNAME like  '%".strtoupper($params['fullname'])."%' ";
		}
		if(!empty($params['byId']))
		{
			$sql = $sql . "and BTH_TRXNO =  '".$params['byId']."' ";
		}
		
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		$return =  array();
		while (oci_fetch($stid)) {
		//PM_FULLNAME like '%".$param."%'  AND 
		
			array_push($return, array(
				'id'			=> oci_result($stid, 'BTH_TRXNO')
				,'FullName'	=> oci_result($stid, 'PM_FULLNAME')
				,'or_no'		=> oci_result($stid, 'BTH_NPI')
				,'patient_id'	=> oci_result($stid, 'BTH_PID')
				,'trans_no'		=> oci_result($stid, 'BTH_TRXNO')
				,'order_date'	=> oci_result($stid, 'BTH_TRXDT')
				,'birthdate' 	=> date('M-d-Y', strtotime(oci_result($stid, 'PM_DOB')))
				,'created_at'	=> date('M-d-Y', strtotime(oci_result($stid, 'BTH_TRXDT')))
				,'Company'	=> oci_result($stid, 'CD_NAME')
				,'Address'		=> oci_result($stid, 'PM_ADDRESS')
				,'City'		=> oci_result($stid, 'PM_CITY')
				,'Email'		=> oci_result($stid, 'PM_EMAIL')
				,'Phone'		=> oci_result($stid, 'PM_MOBILENO')
				));
				
				if( !empty($params['AddPatientTemp']) )
				{
					//check if data already exist to bizbox
					
					$findPatient = DB::connection('Eros')->select("select `Code`  FROM Patient_temp WHERE `Code` = '".oci_result($stid, 'PM_PID')."'  and   `APEDate` = '".date('Y-m-d')."'  ");
					
					if(count($findPatient) == 0) // insert
					{ 
						DB::connection('Eros')->insert("INSERT INTO `Patient_temp` (`Code`, `FullName`, `LastName`, `FirstName`, `MiddleName`, `Suffix`, `Gender`, `DOB`, `Address`, `City`, `Status`, `Remarks`, `UploadID`, `APEDate`)  VALUE
								('".oci_result($stid, 'PM_PID')."' 
								,'".oci_result($stid, 'PM_FULLNAME')."' 
								,'".oci_result($stid, 'PM_LASTNAME')."' 
								,'".oci_result($stid, 'PM_FIRSTNAME')."' 
								,'".oci_result($stid, 'PM_MIDNAME')."' 
								,'".oci_result($stid, 'PM_SUFFIX')."' 
								,'".oci_result($stid, 'PM_GENDER')."' 
								,'".date('Y-m-d', strtotime(oci_result($stid, 'PM_DOB')))."' 
								,'".oci_result($stid, 'PM_ADDRESS')."' 
								,'".oci_result($stid, 'PM_CITY')."' 
								,'toBizBox' 
								,'FROM EROS'
								,'".$params['transaction']."'
								,'".date('Y-m-d')."'
								)
						");
					}
				}
		
		}
		oci_close($conn);
		DB::connection('Eros')->commit();
		return $return;
	
	}

	public static function getItemMasterDeptGroup($idItemCodes)
	{

		$existingCodes = DB::connection('Eros')
			->table('ItemMaster')
			->whereIn('Code', $idItemCodes)
			->pluck('Code')
			->toArray();
		
		// Fetch department groups for existing codes
		$deptGroups = DB::connection('Eros')
			->table('ItemMaster')
			->whereIn('Code', $existingCodes)
			->select(
				DB::raw('CASE 
							WHEN DepartmentGroup = "IMAGING" THEN SubGroup
							ELSE DepartmentGroup
						END AS dept_group')
			)
			->unionAll(
				DB::connection('Eros')
					->table('ItemMaster')
					->whereIn('Code', $existingCodes)
					->where('DepartmentGroup', 'LABORATORY')
					->select(DB::raw('SubGroup AS dept_group'))
			)
			->pluck('dept_group')
			->toArray();
		

		// Get the missing codes (those not found in ItemMaster specifically PACKAGE that is not Standard)
		$itemPricePackage = array_diff($idItemCodes, $existingCodes);
		
		$priceGroups = [];
		if (!empty($itemPricePackage)) {
			$priceGroups = DB::connection('Eros')
				->table('ItemPrice')
				->whereIn('Code', $itemPricePackage)
				->pluck('PriceGroup')
				->toArray();
		}
	
		// Merge both arrays
		$mergedGroups = array_merge($deptGroups, $priceGroups);
	
		return collect($mergedGroups);	
	}

	public static function getPackageDeptGroup($idItemCodes)
    {
        $Pack = DB::connection('Eros')->table('StandardPackage')
			->leftJoin('ItemMaster', 'ItemMaster.Code', '=', 'ItemMasterItemCode')
			->where('ItemMasterPackageCode', $idItemCodes)
			->pluck('ItemMaster.DepartmentGroup');

        $Package = null;
        $TransactionsIdItemPrice = DB::connection('CMS')->table('Transactions')->where('CodeItemPrice', $idItemCodes)->get(array('IdItemPrice'))[0];
        if ($Pack->isEmpty()) {
        $Package = DB::connection('Eros')->table('ItemPrice')
				->leftJoin('Package', 'ItemPrice.Id', '=', 'ItemPriceId')
				->leftJoin('ItemMaster', 'Package.ItemCode', '=', 'ItemMaster.Code')
				->where('ItemPrice.Code', $idItemCodes)
				->where('ItemPrice.Id', $TransactionsIdItemPrice->IdItemPrice)
				->where('ItemPrice.PriceGroup', 'Package')
				->groupby('ItemMaster.Code')
				->pluck('ItemMaster.DepartmentGroup');

			return $Package;
        }

		return $Pack;
    }
    
	// public static function getPackageDeptGroupMultiple($idItemCodes)
	// {
	// 	$allDepartmentGroups = [];

	// 	foreach ($idItemCodes as $codeItemPrice) {
	// 		$Pack = DB::connection('Eros')->table('StandardPackage')
	// 			->leftJoin('ItemMaster', 'ItemMaster.NewCode', '=', 'ItemMasterItemCode')
	// 			->where('ItemMasterPackageCode', $codeItemPrice)
	// 			->get(['ItemMaster.DepartmentGroup', 'ItemMaster.Code','ItemMaster.Description']);

	// 		$Package = null;

	// 		// $TransactionsIdItemPrice = DB::connection('CMS')->table('Transactions')
	// 		// 	->where('CodeItemPrice', $codeItemPrice)
	// 		// 	->get(['IdItemPrice'])->first();

	// 		if ($Pack->isEmpty()) {
	// 			$Package = DB::connection('Eros')->table('ItemPrice')
	// 				->leftJoin('Package', 'ItemPrice.Id', '=', 'ItemPriceId')
	// 				->leftJoin('ItemMaster', 'Package.ItemCode', '=', 'ItemMaster.Code')
	// 				->where('ItemPrice.Code', $codeItemPrice)
	// 				// ->where('ItemPrice.Id', $TransactionsIdItemPrice->IdItemPrice)
	// 				->where('ItemPrice.PriceGroup', 'Package')
	// 				->groupBy('ItemMaster.Code')
	// 				->get(['ItemMaster.DepartmentGroup', 'ItemMaster.Code','ItemMaster.Description']);
	// 		}

	// 		$allDepartmentGroups[$codeItemPrice] = $Pack->isEmpty() ? $Package : $Pack;
	// 	}

	// 	return $allDepartmentGroups;
	// }

	// NEW 01/08/2025
	public static function getPackageDeptGroupMultiple($idItemCodes)
	{
		$allDepartmentGroups = [];
		
		foreach ($idItemCodes as $codeItemPrice) {
			// Get data from 'StandardPackage'
			$Pack = DB::connection('Eros')->table('StandardPackage')
				->leftJoin('ItemMaster', 'ItemMaster.NewCode', '=', 'ItemMasterItemCode')
				->where('ItemMasterPackageCode', $codeItemPrice)
				->select(
					DB::raw('CASE 
								WHEN ItemMaster.DepartmentGroup = "IMAGING" THEN ItemMaster.SubGroup
								ELSE ItemMaster.DepartmentGroup
							END AS DepartmentGroup'),
					'ItemMaster.Code',
					'ItemMaster.Description'
				)
				->unionAll(
					DB::connection('Eros')->table('StandardPackage')
						->leftJoin('ItemMaster', 'ItemMaster.NewCode', '=', 'ItemMasterItemCode')
						->where('ItemMasterPackageCode', $codeItemPrice)
						->where('ItemMaster.DepartmentGroup', 'LABORATORY')
						->select(
							DB::raw('ItemMaster.SubGroup AS DepartmentGroup'),
							'ItemMaster.Code',
							'ItemMaster.Description'
						)
				)				
				->get();
	
			$Package = null;
	
			// Get Transactions IdItemPrice from 'CMS' database
			// $TransactionsIdItemPrice = DB::connection('CMS')->table('Transactions')
			// 	->where('CodeItemPrice', $codeItemPrice)
			// 	->get(['IdItemPrice'])->first();
	
			// If no results from 'Pack', check in 'ItemPrice' table
			if ($Pack->isEmpty()) {
				$Package = DB::connection('Eros')->table('ItemPrice')
					->leftJoin('Package', 'ItemPrice.Id', '=', 'ItemPriceId')
					->leftJoin('ItemMaster', 'Package.ItemCode', '=', 'ItemMaster.Code')
					->where('ItemPrice.Code', $codeItemPrice)
					// ->where('ItemPrice.Id', $TransactionsIdItemPrice->IdItemPrice)
					->where('ItemPrice.PriceGroup', 'Package')
					->select(
						DB::raw('CASE 
									WHEN ItemMaster.DepartmentGroup = "IMAGING" THEN ItemMaster.SubGroup
									ELSE ItemMaster.DepartmentGroup
								END AS DepartmentGroup'),
						'ItemMaster.Code',
						'ItemMaster.Description'
					)
					->unionAll(
						DB::connection('Eros')->table('ItemPrice')
							->leftJoin('Package', 'ItemPrice.Id', '=', 'ItemPriceId')
							->leftJoin('ItemMaster', 'Package.ItemCode', '=', 'ItemMaster.Code')
							->where('ItemPrice.Code', $codeItemPrice)
							->where('ItemPrice.PriceGroup', 'Package')
							->where('ItemMaster.DepartmentGroup', 'LABORATORY')
							->select(
								DB::raw('ItemMaster.SubGroup AS DepartmentGroup'),
								'ItemMaster.Code',
								'ItemMaster.Description'
							)
					)
					->groupBy('ItemMaster.Code')
					->get();
			}
	
			// Store the result (Pack or Package) in the array
			$allDepartmentGroups[$codeItemPrice] = $Pack->isEmpty() ? $Package : $Pack;
		}
	
		return $allDepartmentGroups;
	}
		
	public static function getRejectionData()
	{
		return DB::connection('Eros')
			->table('Rejection')
			->where('Status', 1)
			->get();
	}

	
}
