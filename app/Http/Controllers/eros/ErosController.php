<?php

namespace App\Http\Controllers\eros;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

class ErosController extends Controller
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
	##########EROS Billing HDR SYNC START########
	/*
	Save every daily transaction to load package and make a CMS record from Eros daily transaction
	*/
	public function ErosBillingGetTransDaily() // change to push data from CMS to EROS
	{
		DB::connection('oraTESTe')->beginTransaction();
		$hdrTrx = DB::connection('oraTESTe')->table('AUTONO')->where('AUTO_TYPE', 'LIKE', 'TRX')->get(array('auto_number'))[0]->auto_number;
		$NEWhdrTrx = $hdrTrx + 1;
		DB::connection('oraTESTe')->table('AUTONO')->where('AUTO_TYPE', 'LIKE', 'TRX')->update(['auto_number' => $NEWhdrTrx ]);
		//insert hdr 210000001034
		
		
		
				
		
		
		DB::connection('oraTESTe')->commit();  
		
	}
	##########EROS Billing HDR SYNC END########
    
    ##########SMB START########
    public function SMBPhysicianUpdate()
    {

	echo 'Start';
	//DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');

	$sqlInsert = "INSERT into CLINICIAN_DETAILS (CD_CODE,CD_NAME,CD_ACTIVE,CD_CREATED_BY,CD_CREATED_ON,CD_LICENSE,CD_SPECIALIZATON) VALUES (
		:CD_CODE, :CD_NAME, :CD_ACTIVE, :CD_CREATED_BY, :CD_CREATED_ON, :CD_LICENSE,:CD_SPECIALIZATON
		)";
		
	$datas =  DB::connection('Eros')->select("
		SELECT * FROM `Physician`
		WHERE
		(`SMBStatus` LIKE 'reUpdate' OR  `SMBStatus` IS NULL)
	");
	
	foreach($datas as $data)
	{
		
		echo $data->Id . " - <br>";
		echo $CD_CODE = $data->ErosCode; echo "<br>";
		$CD_NAME = $data->FullName;
		$CD_ACTIVE = 'Y';
		$CD_CREATED_BY = 'RAV';
		$CD_CREATED_ON = date('d-M-Y');
		$CD_LICENSE = $data->PRCNo;
		$CD_SPECIALIZATON = substr(strtoupper(Str::of($data->Description)->replaceMatches('/ {2,}/', ' ')),0, 50);
		
		
		$sqlSelect = "SELECT  * FROM CLINICIAN_DETAILS WHERE  CD_CODE LIKE  '".$data->ErosCode."'  ";
		$itemSelect = oci_parse($conn, $sqlSelect);
		oci_execute($itemSelect);
		
		$withItem = '';
		while (oci_fetch($itemSelect)) {
			$withItem = oci_result($itemSelect, 'CD_CODE');
		}
		
		if(empty($withItem))
		{
			echo "<br>";
			echo  $data->Id."<br>";
			
			$compiled = oci_parse($conn, $sqlInsert);
			
			
			oci_bind_by_name($compiled, ":CD_CODE", $CD_CODE);	
			oci_bind_by_name($compiled, ":CD_NAME", $CD_NAME);	
			oci_bind_by_name($compiled, ":CD_ACTIVE", $CD_ACTIVE);
			oci_bind_by_name($compiled, ":CD_CREATED_BY", $CD_CREATED_BY);
			oci_bind_by_name($compiled, ":CD_CREATED_ON", $CD_CREATED_ON);
			oci_bind_by_name($compiled, ":CD_LICENSE", $CD_LICENSE);
			oci_bind_by_name($compiled, ":CD_SPECIALIZATON", $CD_SPECIALIZATON);
			
			$result = oci_execute($compiled);
			
			if (!$result){
			    $e = oci_error($result);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}else{
				DB::connection('Eros')->update("UPDATE `Physician` tb1  SET tb1.`SMBStatus` = 'APPEND' WHERE tb1.Id = '".$data->Id."'  ");
			}
			oci_close($conn);
		}
		else
		{
			$itemUpdate = oci_parse($conn, "UPDATE CLINICIAN_DETAILS SET CD_NAME ='" . $CD_NAME . "', CD_LICENSE = '". $CD_LICENSE ."', CD_SPECIALIZATON = '". $CD_SPECIALIZATON ."'
			WHERE  CD_CODE LIKE  '".$data->ErosCode."' ");
			$result = oci_execute($itemUpdate); 
			if (!$result){
			    $e = oci_error($result);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}else{
				DB::connection('Eros')->update("UPDATE `Physician` tb1  SET tb1.`SMBStatus` = 'UPDATED' WHERE tb1.Id = '".$data->Id."'  ");
			}
			oci_close($conn);
			echo "Updated";
			//DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`ErosStatus` = 'ERROR' WHERE tb1.Id = '".$data->Id."'  ");
		}
		//die();
	}
	//DB::connection('Eros')->commit();  
	echo '<br>End';
    
    
    }
    
      
    // SMB Update Item Price 
     public function SMBItemPriceUpdate()
    {
	echo 'Start';
	//DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');

	$sql = "INSERT INTO ITEM_PRICE (IP_COMPANY, IP_ITEM_CODE, IP_ENABLED, IP_UPDATE_ON, IP_UPDATE_BY, IP_PRICE, IP_OLD_PRICE, IP_REGULAR_PRICE) VALUES
	(:IP_COMPANY, :IP_ITEM_CODE, :IP_ENABLED, :IP_UPDATE_ON, :IP_UPDATE_BY, :IP_PRICE, :IP_OLD_PRICE, :IP_REGULAR_PRICE)";
		
	$datas =  DB::connection('Eros')->select("
		SELECT tb1.*, tb2.`ErosCode`, tb3.`ShortName`, tb3.`LISCode`
		FROM `ItemPrice` tb1
		LEFT JOIN `Company` tb2 ON (tb1.`CompanyCode` = tb2.`ErosCode`)
		LEFT JOIN `ItemMaster` tb3 ON (tb1.`Code` = tb3.`Code`)
		WHERE
		(tb1.`SMBStatus` LIKE 'reUpdate' OR  tb1.`SMBStatus` IS NULL) LIMIT 5000
	");
	
	foreach($datas as $data)
	{
		//ITEM MASTER
		$masterlSelect = "SELECT  * FROM ITEM_MASTER WHERE  IM_CODE LIKE  '".$data->Code."'  ";
		$itemMasterSelect = oci_parse($conn, $masterlSelect);
		oci_execute($itemMasterSelect);
		
		$withItemMaster = '';
		while (oci_fetch($itemMasterSelect)) {
			$withItemMaster = oci_result($itemMasterSelect, 'IM_CODE');
		}
		
		if(empty($withItemMaster))
		{
			$insertMaster = "INSERT INTO ITEM_MASTER (IM_CODE, IM_DESCRIPTION, IM_SHORT_DESC, IM_LIS_CODES, IM_CREATED_ON, IM_CREATED_BY ) 
			VALUES 
			(:IM_CODE, :IM_DESCRIPTION, :IM_SHORT_DESC, :IM_LIS_CODES, :IM_CREATED_ON, :IM_CREATED_BY ) ";
			$compiledMaster = oci_parse($conn, $insertMaster);
			$IM_CODE = $data->Code;
			$IM_DESCRIPTION = $data->Description;
			$IM_SHORT_DESC = $data->ShortName;
			$IM_LIS_CODES = $data->LISCode;
			$IM_CREATED_ON = date('d-M-Y');
			$IM_CREATED_BY = 'CMS';
			oci_bind_by_name($compiledMaster, ":IM_CODE", $IM_CODE);
			oci_bind_by_name($compiledMaster, ":IM_DESCRIPTION", $IM_DESCRIPTION);
			oci_bind_by_name($compiledMaster, ":IM_SHORT_DESC", $IM_SHORT_DESC);
			oci_bind_by_name($compiledMaster, ":IM_LIS_CODES", $IM_LIS_CODES);
			oci_bind_by_name($compiledMaster, ":IM_CREATED_ON", $IM_CREATED_ON);
			oci_bind_by_name($compiledMaster, ":IM_CREATED_BY", $IM_CREATED_BY);

			$result = oci_execute($compiledMaster);
			if (!$result){
			    $e = oci_error($result);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}
			
		}
		
	
		/// ITEM PRICE
		$sqlSelect = "SELECT  * FROM ITEM_PRICE WHERE  IP_COMPANY LIKE  '".$data->ErosCode."' AND  IP_ITEM_CODE LIKE '".$data->Code."' ";
		$itemSelect = oci_parse($conn, $sqlSelect);
		oci_execute($itemSelect);
		
		$withItem = '';
		while (oci_fetch($itemSelect)) {
			$withItem = oci_result($itemSelect, 'IP_ITEM_CODE');
		}
		
		if(empty($withItem))
		{
					
			echo "<br>";
			echo  $data->Id."<br>";
			$compiled = oci_parse($conn, $sql);
			echo $IP_COMPANY = $data->ErosCode; echo "<br>";
			echo $IP_ITEM_CODE = $data->Code; echo "<br>";
			$IP_ENABLED = 'Y';
			$IP_UPDATE_ON = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y'); //"27-JUN-2022";
			$IP_UPDATE_BY = 'CMS';
			$IP_PRICE = $data->Price;
			$IP_OLD_PRICE = $data->Price;
			$IP_REGULAR_PRICE = $data->Price;
			
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
				DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`SMBStatus` = 'APPEND' WHERE tb1.Id = '".$data->Id."'  ");
			}
			oci_close($conn);
		}
		else
		{
			$itemUpdate = oci_parse($conn, "UPDATE ITEM_PRICE SET IP_PRICE='" . $data->Price . "'  WHERE  IP_COMPANY LIKE  '".$data->ErosCode."' AND  IP_ITEM_CODE LIKE '".$data->Code."'  ");
			$result = oci_execute($itemUpdate); 
			if (!$result){
			    $e = oci_error($result);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}else{
				DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`SMBStatus` = 'UPDATED' WHERE tb1.Id = '".$data->Id."'  ");
			}
			oci_close($conn);
			//DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`ErosStatus` = 'ERROR' WHERE tb1.Id = '".$data->Id."'  ");
		}
		//die();
	}
	//DB::connection('Eros')->commit();  
	echo '<br>End';
	
	
	
	
    }
    
    // SMB Update Company 
    public function SMBCompanyUpdate()
    {
	echo 'Start';
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
			
		$cebuDatas = DB::connection('Eros')->table('Company')
			->whereNull('SMBStatus')
			->orWhere('SMBStatus', 'reUpdate')->get(array('*'));
		
		foreach($cebuDatas as $cebu)
		{
			
			$sqlSelect = "SELECT  * FROM COMPANY_DETAILS WHERE  CD_CODE LIKE  '".$cebu->ErosCode."'  ";
			$itemSelect = oci_parse($conn, $sqlSelect);
			oci_execute($itemSelect);
			
			$withItem = '';
			while (oci_fetch($itemSelect)) {
				$withItem = oci_result($itemSelect, 'CD_CODE');
			}
			
			echo "<br>";
			echo $CD_CODE = $cebu->ErosCode; echo "<br>";
			$CD_NAME = substr(strtoupper(Str::of($cebu->Name)->replaceMatches('/ {2,}/', ' ')),0, 300);
			$CD_TYPE = $cebu->Group;
			$subGroup = $cebu->SubGroup;
			$CD_ACTIVE = 'Y';
			$CD_TELNO = $cebu->Phone;
			$CD_ADDRESS = $cebu->Address;
			$CD_CITY = $cebu->City;
			$CD_EMAIL = $cebu->Email;
			$CD_CREATED_ON = date('d-M-Y');
			$CD_CREATED_BY = 'RAV';
			
			if(empty($withItem))
			{
			
				$sqlInsert = "INSERT into COMPANY_DETAILS (CD_CODE,CD_NAME,CD_TYPE,CD_ACTIVE,CD_TELNO,CD_ADDRESS,CD_CITY,CD_EMAIL,CD_CREATED_ON,CD_CREATED_BY) VALUES (
				:CD_CODE,:CD_NAME,:CD_TYPE,:CD_ACTIVE,:CD_TELNO,:CD_ADDRESS,:CD_CITY,:CD_EMAIL,:CD_CREATED_ON,:CD_CREATED_BY
				)";
				$compiled = oci_parse($conn, $sqlInsert);
				
				
				oci_bind_by_name($compiled, ":CD_CODE", $CD_CODE);	
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
					DB::connection('Eros')->table('Company')
					->where('Id',$cebu->Id)
					->lockForUpdate()
					->update([
						'SMBStatus'	=> 'Append'
					]); 
					echo $CD_CODE. ' => Append';
					oci_close($conn);
				}
			}else{
				$sqlUpdate = "UPDATE COMPANY_DETAILS SET 
					CD_NAME = '".str_replace("'","''",$CD_NAME)."'
					,CD_TYPE = '".$CD_TYPE."'
					,CD_TELNO = '".$CD_TELNO."'	
					,CD_ADDRESS = '".$CD_ADDRESS."'	
					,CD_CITY =  '".$CD_CITY."'	
					,CD_EMAIL =  '".$CD_EMAIL."'	
					WHERE
					CD_CODE LIKE '".$CD_CODE."'
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
					DB::connection('Eros')->table('Company')
					->where('Id',$cebu->Id)
					->lockForUpdate()
					->update([
						'SMBStatus'	=> 'Updated'
					]); 
					echo $CD_CODE. ' => Updated';
					oci_close($conn);
				}
			}
			
		}
	echo 'Done';	
    }
    
   ##########SMB END################### ##
   /*---------------------------------------------------------------*/
   ##########CEBU START################### 
    public function CebuPhysicianUpdate()
    {
	echo 'Start';
	//DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');

	$sqlInsert = "INSERT into CLINICIAN_DETAILS (CD_CODE,CD_NAME,CD_ACTIVE,CD_CREATED_BY,CD_CREATED_ON,CD_LICENSE,CD_SPECIALIZATON) VALUES (
		:CD_CODE, :CD_NAME, :CD_ACTIVE, :CD_CREATED_BY, :CD_CREATED_ON, :CD_LICENSE,:CD_SPECIALIZATON
		)";
		
	$datas =  DB::connection('Eros')->select("
		SELECT * FROM `Physician`
		WHERE
		(`CebuStatus` LIKE 'reUpdate' OR  `CebuStatus` IS NULL)
	");
	
	foreach($datas as $data)
	{
		
		echo $data->Id . " - <br>";
		echo $CD_CODE = $data->ErosCode; echo "<br>";
		$CD_NAME = $data->FullName;
		$CD_ACTIVE = 'Y';
		$CD_CREATED_BY = 'RAV';
		$CD_CREATED_ON = date('d-M-Y');
		$CD_LICENSE = $data->PRCNo;
		$CD_SPECIALIZATON = substr(strtoupper(Str::of($data->Description)->replaceMatches('/ {2,}/', ' ')),0, 50);
		
		
		$sqlSelect = "SELECT  * FROM CLINICIAN_DETAILS WHERE  CD_CODE LIKE  '".$data->ErosCode."'  ";
		$itemSelect = oci_parse($conn, $sqlSelect);
		oci_execute($itemSelect);
		
		$withItem = '';
		while (oci_fetch($itemSelect)) {
			$withItem = oci_result($itemSelect, 'CD_CODE');
		}
		
		if(empty($withItem))
		{
			echo "<br>";
			echo  $data->Id."<br>";
			
			$compiled = oci_parse($conn, $sqlInsert);
			
			
			oci_bind_by_name($compiled, ":CD_CODE", $CD_CODE);	
			oci_bind_by_name($compiled, ":CD_NAME", $CD_NAME);	
			oci_bind_by_name($compiled, ":CD_ACTIVE", $CD_ACTIVE);
			oci_bind_by_name($compiled, ":CD_CREATED_BY", $CD_CREATED_BY);
			oci_bind_by_name($compiled, ":CD_CREATED_ON", $CD_CREATED_ON);
			oci_bind_by_name($compiled, ":CD_LICENSE", $CD_LICENSE);
			oci_bind_by_name($compiled, ":CD_SPECIALIZATON", $CD_SPECIALIZATON);
			
			$result = oci_execute($compiled);
			
			if (!$result){
			    $e = oci_error($result);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}else{
				DB::connection('Eros')->update("UPDATE `Physician` tb1  SET tb1.`CebuStatus` = 'APPEND' WHERE tb1.Id = '".$data->Id."'  ");
			}
			oci_close($conn);
		}
		else
		{
			$itemUpdate = oci_parse($conn, "UPDATE CLINICIAN_DETAILS SET CD_NAME ='" . $CD_NAME . "', CD_LICENSE = '". $CD_LICENSE ."', CD_SPECIALIZATON = '". $CD_SPECIALIZATON ."'
			WHERE  CD_CODE LIKE  '".$data->ErosCode."' ");
			$result = oci_execute($itemUpdate); 
			if (!$result){
			    $e = oci_error($result);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}else{
				DB::connection('Eros')->update("UPDATE `Physician` tb1  SET tb1.`CebuStatus` = 'UPDATED' WHERE tb1.Id = '".$data->Id."'  ");
			}
			oci_close($conn);
			echo "Updated";
			//DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`ErosStatus` = 'ERROR' WHERE tb1.Id = '".$data->Id."'  ");
		}
		//die();
	}
	//DB::connection('Eros')->commit();  
	echo '<br>End';
    
    
    }
    
      
    // CEBU Update Item Price 
     public function CebuItemPriceUpdate()
    {
	echo 'Start';
	//DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');

	$sql = "INSERT INTO ITEM_PRICE (IP_COMPANY, IP_ITEM_CODE, IP_ENABLED, IP_UPDATE_ON, IP_UPDATE_BY, IP_PRICE, IP_OLD_PRICE, IP_REGULAR_PRICE) VALUES
	(:IP_COMPANY, :IP_ITEM_CODE, :IP_ENABLED, :IP_UPDATE_ON, :IP_UPDATE_BY, :IP_PRICE, :IP_OLD_PRICE, :IP_REGULAR_PRICE)";
		
	$datas =  DB::connection('Eros')->select("
		SELECT tb1.*, tb2.`ErosCode`, tb3.`ShortName`, tb3.`LISCode`
		FROM `ItemPrice` tb1
		LEFT JOIN `Company` tb2 ON (tb1.`CompanyCode` = tb2.`ErosCode`)
		LEFT JOIN `ItemMaster` tb3 ON (tb1.`Code` = tb3.`Code`)
		WHERE
		(tb1.`CebuStatus` LIKE 'reUpdate' OR  tb1.`CebuStatus` IS NULL) LIMIT 5000
	");
	
	foreach($datas as $data)
	{
		//ITEM MASTER
		$masterlSelect = "SELECT  * FROM ITEM_MASTER WHERE  IM_CODE LIKE  '".$data->Code."'  ";
		$itemMasterSelect = oci_parse($conn, $masterlSelect);
		oci_execute($itemMasterSelect);
		
		$withItemMaster = '';
		while (oci_fetch($itemMasterSelect)) {
			$withItemMaster = oci_result($itemMasterSelect, 'IM_CODE');
		}
		
		if(empty($withItemMaster))
		{
			$insertMaster = "INSERT INTO ITEM_MASTER (IM_CODE, IM_DESCRIPTION, IM_SHORT_DESC, IM_LIS_CODES, IM_CREATED_ON, IM_CREATED_BY ) 
			VALUES 
			(:IM_CODE, :IM_DESCRIPTION, :IM_SHORT_DESC, :IM_LIS_CODES, :IM_CREATED_ON, :IM_CREATED_BY ) ";
			$compiledMaster = oci_parse($conn, $insertMaster);
			$IM_CODE = $data->Code;
			$IM_DESCRIPTION = $data->Description;
			$IM_SHORT_DESC = $data->ShortName;
			$IM_LIS_CODES = $data->LISCode;
			$IM_CREATED_ON = date('d-M-Y');
			$IM_CREATED_BY = 'CMS';
			oci_bind_by_name($compiledMaster, ":IM_CODE", $IM_CODE);
			oci_bind_by_name($compiledMaster, ":IM_DESCRIPTION", $IM_DESCRIPTION);
			oci_bind_by_name($compiledMaster, ":IM_SHORT_DESC", $IM_SHORT_DESC);
			oci_bind_by_name($compiledMaster, ":IM_LIS_CODES", $IM_LIS_CODES);
			oci_bind_by_name($compiledMaster, ":IM_CREATED_ON", $IM_CREATED_ON);
			oci_bind_by_name($compiledMaster, ":IM_CREATED_BY", $IM_CREATED_BY);

			$result = oci_execute($compiledMaster);
			if (!$result){
			    $e = oci_error($result);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}
			
		}
		
	
		/// ITEM PRICE
		$sqlSelect = "SELECT  * FROM ITEM_PRICE WHERE  IP_COMPANY LIKE  '".$data->ErosCode."' AND  IP_ITEM_CODE LIKE '".$data->Code."' ";
		$itemSelect = oci_parse($conn, $sqlSelect);
		oci_execute($itemSelect);
		
		$withItem = '';
		while (oci_fetch($itemSelect)) {
			$withItem = oci_result($itemSelect, 'IP_ITEM_CODE');
		}
		
		if(empty($withItem))
		{
					
			echo "<br>";
			echo  $data->Id."<br>";
			$compiled = oci_parse($conn, $sql);
			echo $IP_COMPANY = $data->ErosCode; echo "<br>";
			echo $IP_ITEM_CODE = $data->Code; echo "<br>";
			$IP_ENABLED = 'Y';
			$IP_UPDATE_ON = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y'); //"27-JUN-2022";
			$IP_UPDATE_BY = 'CMS';
			$IP_PRICE = $data->Price;
			$IP_OLD_PRICE = $data->Price;
			$IP_REGULAR_PRICE = $data->Price;
			
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
				DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`CebuStatus` = 'APPEND' WHERE tb1.Id = '".$data->Id."'  ");
			}
			oci_close($conn);
		}
		else
		{
			$itemUpdate = oci_parse($conn, "UPDATE ITEM_PRICE SET IP_PRICE='" . $data->Price . "'  WHERE  IP_COMPANY LIKE  '".$data->ErosCode."' AND  IP_ITEM_CODE LIKE '".$data->Code."'  ");
			$result = oci_execute($itemUpdate); 
			if (!$result){
			    $e = oci_error($result);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}else{
				DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`CebuStatus` = 'UPDATED' WHERE tb1.Id = '".$data->Id."'  ");
			}
			oci_close($conn);
			//DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`ErosStatus` = 'ERROR' WHERE tb1.Id = '".$data->Id."'  ");
		}
		//die();
	}
	//DB::connection('Eros')->commit();  
	echo '<br>End';
	
	
	
	
    }
    
    // CEBU Update Company 
    public function CebuCompanyUpdate()
    {
	echo 'Start';
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
			
		$cebuDatas = DB::connection('Eros')->table('Company')
			->whereNull('CebuStatus')
			->orWhere('CebuStatus', 'reUpdate')->get(array('*'));
		
		foreach($cebuDatas as $cebu)
		{
			
			$sqlSelect = "SELECT  * FROM COMPANY_DETAILS WHERE  CD_CODE LIKE  '".$cebu->ErosCode."'  ";
			$itemSelect = oci_parse($conn, $sqlSelect);
			oci_execute($itemSelect);
			
			$withItem = '';
			while (oci_fetch($itemSelect)) {
				$withItem = oci_result($itemSelect, 'CD_CODE');
			}
			
			echo "<br>";
			echo $CD_CODE = $cebu->ErosCode; echo "<br>";
			$CD_NAME = substr(strtoupper(Str::of($cebu->Name)->replaceMatches('/ {2,}/', ' ')),0, 300);
			$CD_TYPE = $cebu->Group;
			$subGroup = $cebu->SubGroup;
			$CD_ACTIVE = 'Y';
			$CD_TELNO = $cebu->Phone;
			$CD_ADDRESS = $cebu->Address;
			$CD_CITY = $cebu->City;
			$CD_EMAIL = $cebu->Email;
			$CD_CREATED_ON = date('d-M-Y');
			$CD_CREATED_BY = 'RAV';
			
			if(empty($withItem))
			{
			
				$sqlInsert = "INSERT into COMPANY_DETAILS (CD_CODE,CD_NAME,CD_TYPE,CD_ACTIVE,CD_TELNO,CD_ADDRESS,CD_CITY,CD_EMAIL,CD_CREATED_ON,CD_CREATED_BY) VALUES (
				:CD_CODE,:CD_NAME,:CD_TYPE,:CD_ACTIVE,:CD_TELNO,:CD_ADDRESS,:CD_CITY,:CD_EMAIL,:CD_CREATED_ON,:CD_CREATED_BY
				)";
				$compiled = oci_parse($conn, $sqlInsert);
				
				
				oci_bind_by_name($compiled, ":CD_CODE", $CD_CODE);	
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
					DB::connection('Eros')->table('Company')
					->where('Id',$cebu->Id)
					->lockForUpdate()
					->update([
						'CebuStatus'	=> 'Append'
					]); 
					echo $CD_CODE. ' => Append';
					oci_close($conn);
				}
			}else{
				$sqlUpdate = "UPDATE COMPANY_DETAILS SET 
					CD_NAME = '".str_replace("'","''",$CD_NAME)."'
					,CD_TYPE = '".$CD_TYPE."'
					,CD_TELNO = '".$CD_TELNO."'	
					,CD_ADDRESS = '".$CD_ADDRESS."'	
					,CD_CITY =  '".$CD_CITY."'	
					,CD_EMAIL =  '".$CD_EMAIL."'	
					WHERE
					CD_CODE LIKE '".$CD_CODE."'
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
					DB::connection('Eros')->table('Company')
					->where('Id',$cebu->Id)
					->lockForUpdate()
					->update([
						'CebuStatus'	=> 'Updated'
					]); 
					echo $CD_CODE. ' => Updated';
					oci_close($conn);
				}
			}
			
		}
	echo 'Done';	
    }
    
    public function installRemote()
   {
	//echo 'die';die();
	//$user = "ravalmores";
	//$pwd = "a2CBD84998@1121";
	//$output = shell_exec('powershell -ExecutionPolicy Bypass -NoProfile -File "C:\Users\Public\RUSTY.ps1" ' . $user . ' ' . $pwd);
	//echo $output=exec('powershell -noprofile -command "&{ start-process powershell -ArgumentList -noprofile -file C:\Users\Public\RUSTY.ps1 ' . $user . ' ' . $pwd . ' -verb RunAs}"');
	//echo Shell_Exec ('powershell.exe -executionpolicy bypass -NoProfile -Command "Get-Process | ConvertTo-Html"');
	//echo 'run';
	//$output = exec 'cmd.exe';
	//echo "<pre>$output</pre>";
   }   
   
   public function updateMyPicture()
   {
	$myPictures = DB::connection('Eros')->select("SELECT * FROM `Patient` WHERE `PictureLink` like  'NULL' ");
	foreach($myPictures as $data)
	{
		if(file_exists( public_path().'/picture/'.$data->Code.'.jpg' ))
		{
			DB::connection('Eros')->update("UPDATE `Patient` set `PictureLink` =  '".$data->Code.".jpg' where `Id` = '".$data->Id."'  ");
		}
		else
		{
			DB::connection('Eros')->update("UPDATE `Patient` set `PictureLink` =  'no-image.jpg' where `Id` = '".$data->Id."'  ");
		}
	}
   }
    
    
    public function updateItemPriceStatusSync()
    {
	$datas =  DB::connection('Eros')->select("
		SELECT *
		FROM `ItemPrice` 
		WHERE
		`PackageUpdate` IS NULL  and `PriceGroup` LIKE 'Package'
	");
	foreach($datas as $data)
	{
		$count = DB::connection('Eros')->select("SELECT * FROM `Package` WHERE `ItemPriceId` =  '".$data->Id."' ");
		if( count($count) != 0)
		{
			DB::connection('Eros')->update("UPDATE `ItemPrice` SET `PackageUpdate` = 'Updated' WHERE `Id` =  '".$data->Id."'  ");
		}
		else
		{
			$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
			$conn = oci_connect("hclab", "hclab", $cstr, 'AL32UTF8');
			
			$sql = "SELECT * FROM item_masterh tb1 LEFT JOIN item_masterd tb2 ON (tb1.IMH_CODE = tb2.IMD_PKG_CODE) WHERE tb1.IMH_CODE = '".$data->LISCode."' ";
			$stid = oci_parse($conn, $sql);
			oci_execute($stid);
			while (oci_fetch($stid))
			{
				DB::connection('Eros')->insert("INSERT INTO Package (`ItemPriceId`, `ItemCode`) VALUES( '".$data->Id."', '".oci_result($stid, 'IMD_PKG_ITEM')."') ");
			}
			oci_close($conn);
			DB::connection('Eros')->update("UPDATE `ItemPrice` SET `PackageUpdate` = 'Updated' WHERE `Id` =  '".$data->Id."'  ");
		}
	
	}
    
    }
    
    public function updatePatientMaster()
    {
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
	
	$sql = "select  TO_CHAR(PM_DOB,  'YYYY-MM-DD')  AS RPM_DOB,
			      TO_CHAR(PM_CREATED_ON, 'YYYY-MM-DD') AS RPM_CREATED_ON,	
			      TO_CHAR(PM_UPDATE_ON, 'YYYY-MM-DD') AS RPM_UPDATE_ON,	
			      TO_CHAR(PM_LASTVISIT, 'YYYY-MM-DD') AS RPM_LASTVISIT,
		tb1.* from patient_master  tb1 WHERE (tb1.PM_GENDER LIKE 'M' OR  tb1.PM_GENDER LIKE 'F') and PM_FIRSTNAME IS NOT NULL
		ORDER BY tb1.PM_PID ASC
		OFFSET 400000 ROWS FETCH NEXT 200000 ROWS ONLY
		";
		//ORDER BY tb1.PM_PID 
		//OFFSET 600000 ROWS FETCH NEXT 100000 ROWS ONLY
	$stid = oci_parse($conn, $sql);
	oci_execute($stid);
	echo "<table>";
		while (oci_fetch($stid))
		{
			
			//$checkDataIf  = DB::connection('Eros')->select("SELECT * FROM Patient WHERE `Code` LIKE '".oci_result($stid, 'PM_PID')."'  ");
			//if(count($checkDataIf) ==0)
			//{ 
				
				//echo  oci_result($stid, 'PM_FULLNAME');
			
				//$dob = \Carbon\Carbon::createFromFormat('Y-m-d', oci_result($stid, 'PM_DOB')))->format('Y-m-d');
				//$input = \Carbon\Carbon::createFromFormat('d-M-Y', oci_result($stid, 'PM_CREATED_ON')))->format('Y-m-d');
				//$update = \Carbon\Carbon::createFromFormat('d-M-Y', oci_result($stid, 'PM_UPDATE_ON')))->format('Y-m-d');
				 //die();
				$dataPatient = 
				[
				    [
					'Code'		=> oci_result($stid, 'PM_PID')
					,'FullName'	=> htmlentities(oci_result($stid, 'PM_FULLNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'LastName'	=>  htmlentities(oci_result($stid, 'PM_LASTNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'FirstName'	=> htmlentities(oci_result($stid, 'PM_FIRSTNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'MiddleName'	=>  htmlentities(oci_result($stid, 'PM_MIDNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'Prefix'		=> oci_result($stid, 'PM_PREFIX')
					,'Suffix'		=> oci_result($stid, 'PM_SUFFIX')
					,'Gender'		=> oci_result($stid, 'PM_GENDER')
					,'DOB'		=> oci_result($stid, 'RPM_DOB')
					,'Religion'		=>  htmlentities(oci_result($stid, 'PM_RELIGION'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'PhilHealth'	=> oci_result($stid, 'PM_PHILHEALTH')
					,'Address'		=> htmlentities(oci_result($stid, 'PM_ADDRESS'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'City'		=> htmlentities(oci_result($stid, 'PM_CITY'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'State'		=> oci_result($stid, 'PM_STATE')	
					,'ZipCode'		=> oci_result($stid, 'PM_ZIPCODE')	
					,'ContactNo'	=> oci_result($stid, 'PM_TELNO')
					,'FaxNo'		=> oci_result($stid, 'PM_FAXNO')
					,'Email'		=> oci_result($stid, 'PM_EMAIL')
					,'Moblie'		=> oci_result($stid, 'PM_MOBILENO')
					,'InputDate'	=> oci_result($stid, 'RPM_CREATED_ON')
					,'InputBy'		=> oci_result($stid, 'PM_CREATED_BY')
					,'UpdateDate'	=> oci_result($stid, 'RPM_UPDATE_ON')
					,'UpdateBy'	=> oci_result($stid, 'PM_UPDATE_BY')
					,'Status'		=> oci_result($stid, 'PM_ACTIVE')
					,'Country'		=> htmlentities(oci_result($stid, 'PM_COUNTRY'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'LastVisit'		=> oci_result($stid, 'RPM_LASTVISIT')
					,'Barangay'	=>  htmlentities(oci_result($stid, 'PM_BARANGAY'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'Nationality'	=> htmlentities(oci_result($stid, 'PM_NATIONALITY'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'PassPortNo'	=> oci_result($stid, 'PM_PASSPORTNO')
					,'RDOB'		=> oci_result($stid, 'PM_DOB')
		
				    ]
				];
				
				DB::connection('Eros')->table('PatientEros')->insert($dataPatient);
			//}
			//else
			//{
			/*	DB::connection('Eros')->table('Patient')->where('Code',oci_result($stid, 'PM_PID'))
				->lockForUpdate()
				->update([
					'FullName'	=> htmlentities(oci_result($stid, 'PM_FULLNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'LastName'	=>  htmlentities(oci_result($stid, 'PM_LASTNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'FirstName'	=> htmlentities(oci_result($stid, 'PM_FIRSTNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'MiddleName'	=>  htmlentities(oci_result($stid, 'PM_MIDNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'Prefix'		=> oci_result($stid, 'PM_PREFIX')
					,'Suffix'		=> oci_result($stid, 'PM_SUFFIX')
					,'Gender'		=> oci_result($stid, 'PM_GENDER')
					,'DOB'		=> oci_result($stid, 'RPM_DOB')
					,'Religion'		=>  htmlentities(oci_result($stid, 'PM_RELIGION'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'PhilHealth'	=> oci_result($stid, 'PM_PHILHEALTH')
					,'Address'		=> htmlentities(oci_result($stid, 'PM_ADDRESS'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'City'		=> htmlentities(oci_result($stid, 'PM_CITY'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'State'		=> oci_result($stid, 'PM_STATE')	
					,'ZipCode'		=> oci_result($stid, 'PM_ZIPCODE')	
					,'ContactNo'	=> oci_result($stid, 'PM_TELNO')
					,'FaxNo'		=> oci_result($stid, 'PM_FAXNO')
					,'Email'		=> oci_result($stid, 'PM_EMAIL')
					,'Moblie'		=> oci_result($stid, 'PM_MOBILENO')
					,'InputDate'	=> oci_result($stid, 'RPM_CREATED_ON')
					,'InputBy'		=> oci_result($stid, 'PM_CREATED_BY')
					,'UpdateDate'	=> oci_result($stid, 'RPM_UPDATE_ON')
					,'UpdateBy'	=> oci_result($stid, 'PM_UPDATE_BY')
					,'Status'		=> oci_result($stid, 'PM_ACTIVE')
					,'Country'		=> htmlentities(oci_result($stid, 'PM_COUNTRY'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'LastVisit'		=> oci_result($stid, 'RPM_LASTVISIT')
					,'Barangay'	=>  htmlentities(oci_result($stid, 'PM_BARANGAY'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'Nationality'	=> htmlentities(oci_result($stid, 'PM_NATIONALITY'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'PassPortNo'	=> oci_result($stid, 'PM_PASSPORTNO')
					,'RDOB'		=> oci_result($stid, 'PM_DOB')
					
				]); 
			
			
				
			}*/
		
			echo "<tr>";
			echo "<td>" .oci_result($stid, 'PM_PID') . "</td>";
			echo "<td>" .oci_result($stid, 'PM_FULLNAME') . "</td>";
			echo "</tr>";
		}
		echo "</table>";
		oci_close($conn);
	
    echo "Done";
    }
	
    public function getDataErosByDate()
   {
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
	
	$sql = "select tb1.*, tb2.*, tb3.*, tb5.* from billing_trx_hdr tb1 
LEFT JOIN clinician_details tb2 ON (tb1.bth_clinician = tb2.cd_code)
LEFT JOIN patient_master tb3 ON (tb1.BTH_PID =  tb3.PM_PID)
LEFT JOIN billing_trx_dtl tb4 ON (tb1.BTH_TRXNO = tb4.BTD_TRXNO)
LEFT JOIN item_master tb5 ON (tb4.BTD_ITEM_CODE = tb5.IM_CODE)
where tb1.bth_trxdt  >= '01-OCT-22' AND tb1.bth_trxdt  <= '31-OCT-22' and tb1.BTH_SOURCE = 10 ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		
		echo "<table>";
		while (oci_fetch($stid))
		{
			echo "<tr>";
			echo "<td>" .oci_result($stid, 'CD_CODE') . "</td>";
			echo "<td>" .oci_result($stid, 'CD_NAME') . "</td>";
			echo "<td>" .oci_result($stid, 'BTH_TRXNO') . "</td>";
			echo "<td>" .oci_result($stid, 'BTH_TRXDT') . "</td>";
			echo "<td>" .oci_result($stid, 'BTH_SOURCE') . "</td>";
			echo "<td>" .oci_result($stid, 'PM_FULLNAME') . "</td>";
			echo "<td>" .oci_result($stid, 'IM_DESCRIPTION') . "</td>";
			echo "</tr>";
		}
		echo "</table>";
		oci_close($conn);
		
		
	

   }
	
/*Get and Upaddate All Item Price in Eros to CMS */	
   public function getDataItemPrice()
   {
	//DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
	
	$sql = "SELECT tb1.*, tb2.* FROM ITEM_PRICE tb1 LEFT JOIN ITEM_MASTER tb2 ON (tb1.IP_ITEM_CODE = tb2.IM_CODE)";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		
		echo "<table>";
		while (oci_fetch($stid))
		{
			$id = "";
			$checkData = DB::connection('Eros')->select("SELECT * FROM `ItemPrice` WHERE `CompanyCode` = '".oci_result($stid, 'IP_COMPANY')."' and `Code` = '".oci_result($stid, 'IP_ITEM_CODE')."'  ");
			if(count($checkData) !=0)
			{
				$company = DB::connection('Eros')->select("SELECT * FROM `Company` WHERE `ErosCode` LIKE  '".oci_result($stid, 'IP_COMPANY')."' LIMIT 1  ");
				$status = 'Update';
				if(count($company) !=0)
				{
					DB::connection('Eros')->update("UPDATE `ItemPrice` SET  `LISCode` = '".oci_result($stid, 'IM_LIS_CODES')."' ,`CompanyCode` = '".$company[0]->Code."' , `Price` = '".oci_result($stid, 'IP_PRICE')."' WHERE `Id` = '".$checkData[0]->Id."'  ");
				}
				$id =  $checkData[0]->Id;
			}else{
				DB::connection('Eros')->insert("INSERT INTO `ItemPrice` (`ClinicCode`, `Code`, `Description`, `CompanyCode`, `Price`, `PriceGroup`, `ErosStatus`,`InputDate`, `InputBy`, `LISCode` ) VALUES
					( 
					'ALL'
					,'".oci_result($stid, 'IP_ITEM_CODE')."'
					,'".htmlentities(oci_result($stid, 'IM_DESCRIPTION'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."'
					,'".oci_result($stid, 'IP_COMPANY')."'
					,'".oci_result($stid, 'IP_PRICE')."'
					,'Item'
					,'UPDATED'
					,'".date('Y-m-d')."'
					,'Admin'
					,'".oci_result($stid, 'IM_LIS_CODES')."'
					)				
				");
			}
			
			
			
			
			echo "<tr>";
			echo "<td>" .oci_result($stid, 'IP_COMPANY') . "</td>";
			echo "<td>" .oci_result($stid, 'IP_ITEM_CODE') . "</td>";
			echo "<td>" .oci_result($stid, 'IM_DESCRIPTION') . "</td>";
			echo "<td>" .oci_result($stid, 'IM_TG') . "</td>";
			echo "<td>" .oci_result($stid, 'IP_ENABLED') . "</td>";
			echo "<td>" .oci_result($stid, 'IP_UPDATE_ON') . "</td>";
			echo "<td>" .oci_result($stid, 'IP_UPDATE_BY') . "</td>";
			echo "<td>" .oci_result($stid, 'IP_PRICE') . "</td>";
			echo "<td>" .oci_result($stid, 'IP_OLD_PRICE') . "</td>";
			echo "<td>" .oci_result($stid, 'IP_REGULAR_PRICE') . "</td>";
			echo "<td>" .$id. "</td>";
			echo "</tr>";
		}
		echo "</table>";
		oci_close($conn);
	//DB::connection('Eros')->commit();  
   }
   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
     public function passwordResert()
    {
	 DB::connection('mysql')->update("UPDATE users SET password = '".Hash::make('imd@2023')."' WHERE id = 8 ");

    }    
     /*Update Item Price from CMS to Eros*/
     public function updateItemPrice()
     {
	echo 'Start';
	DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');

	$sql = "INSERT INTO ITEM_PRICE (IP_COMPANY, IP_ITEM_CODE, IP_ENABLED, IP_UPDATE_ON, IP_UPDATE_BY, IP_PRICE, IP_OLD_PRICE, IP_REGULAR_PRICE) VALUES
	(:IP_COMPANY, :IP_ITEM_CODE, :IP_ENABLED, :IP_UPDATE_ON, :IP_UPDATE_BY, :IP_PRICE, :IP_OLD_PRICE, :IP_REGULAR_PRICE)";
		
	$datas =  DB::connection('Eros')->select("
		SELECT tb1.*, tb2.`ErosCode`, tb3.`ShortName`, tb3.`LISCode`
		FROM `ItemPrice` tb1
		LEFT JOIN `Company` tb2 ON (tb1.`CompanyCode` = tb2.`ErosCode`)
		LEFT JOIN `ItemMaster` tb3 ON (tb1.`Code` = tb3.`Code`)
		WHERE
		(tb1.`ErosStatus` LIKE 'reUpdate' OR  tb1.`ErosStatus` IS NULL) LIMIT 5000
	");
	
	foreach($datas as $data)
	{
		//ITEM MASTER
		$masterlSelect = "SELECT  * FROM ITEM_MASTER WHERE  IM_CODE LIKE  '".$data->Code."'  ";
		$itemMasterSelect = oci_parse($conn, $masterlSelect);
		oci_execute($itemMasterSelect);
		
		$withItemMaster = '';
		while (oci_fetch($itemMasterSelect)) {
			$withItemMaster = oci_result($itemMasterSelect, 'IM_CODE');
		}
		
		if(empty($withItemMaster))
		{
			$insertMaster = "INSERT INTO ITEM_MASTER (IM_CODE, IM_DESCRIPTION, IM_SHORT_DESC, IM_LIS_CODES, IM_CREATED_ON, IM_CREATED_BY ) 
			VALUES 
			(:IM_CODE, :IM_DESCRIPTION, :IM_SHORT_DESC, :IM_LIS_CODES, :IM_CREATED_ON, :IM_CREATED_BY ) ";
			$compiledMaster = oci_parse($conn, $insertMaster);
			$IM_CODE = $data->Code;
			$IM_DESCRIPTION = $data->Description;
			$IM_SHORT_DESC = $data->ShortName;
			$IM_LIS_CODES = $data->LISCode;
			$IM_CREATED_ON = date('d-M-Y');
			$IM_CREATED_BY = 'CMS';
			oci_bind_by_name($compiledMaster, ":IM_CODE", $IM_CODE);
			oci_bind_by_name($compiledMaster, ":IM_DESCRIPTION", $IM_DESCRIPTION);
			oci_bind_by_name($compiledMaster, ":IM_SHORT_DESC", $IM_SHORT_DESC);
			oci_bind_by_name($compiledMaster, ":IM_LIS_CODES", $IM_LIS_CODES);
			oci_bind_by_name($compiledMaster, ":IM_CREATED_ON", $IM_CREATED_ON);
			oci_bind_by_name($compiledMaster, ":IM_CREATED_BY", $IM_CREATED_BY);

			$result = oci_execute($compiledMaster);
			if (!$result){
			    $e = oci_error($result);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}
			
		}
		
	
		/// ITEM PRICE
		$sqlSelect = "SELECT  * FROM ITEM_PRICE WHERE  IP_COMPANY LIKE  '".$data->ErosCode."' AND  IP_ITEM_CODE LIKE '".$data->Code."' ";
		$itemSelect = oci_parse($conn, $sqlSelect);
		oci_execute($itemSelect);
		
		$withItem = '';
		while (oci_fetch($itemSelect)) {
			$withItem = oci_result($itemSelect, 'IP_ITEM_CODE');
		}
		
		if(empty($withItem))
		{
	
			echo "<br>";
			$compiled = oci_parse($conn, $sql);
			$IP_COMPANY = $data->ErosCode;
			echo $IP_ITEM_CODE = $data->Code; echo "<br>";
			$IP_ENABLED = 'Y';
			$IP_UPDATE_ON = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y'); //"27-JUN-2022";
			$IP_UPDATE_BY = 'RICKY';
			$IP_PRICE = $data->Price;
			$IP_OLD_PRICE = $data->Price;
			$IP_REGULAR_PRICE = $data->Price;
			
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
				DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`ErosStatus` = 'APPEND' WHERE tb1.Id = '".$data->Id."'  ");
			}
			oci_close($conn);
		}
		else
		{
			$itemUpdate = oci_parse($conn, "UPDATE ITEM_PRICE SET IP_PRICE='" . $data->Price . "'  WHERE  IP_COMPANY LIKE  '".$data->ErosCode."' AND  IP_ITEM_CODE LIKE '".$data->Code."'  ");
			$result = oci_execute($itemUpdate); 
			if (!$result){
			    $e = oci_error($result);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}else{
				DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`ErosStatus` = 'UPDATED' WHERE tb1.Id = '".$data->Id."'  ");
			}
			oci_close($conn);
			//DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`ErosStatus` = 'ERROR' WHERE tb1.Id = '".$data->Id."'  ");
		}
	}
	DB::connection('Eros')->commit();  
	echo '<br>End';
     }
     
     public function getPhysician()
    {
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
	
		$sql = "SELECT * FROM clinician_details";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		$i =1;
		echo "<table>";
		while (oci_fetch($stid))
		{
			$statusIs = (oci_result($stid, 'CD_ACTIVE') == 'Y')?'Active':'Inactive';
			echo "<tr>";
			echo "<td>" .oci_result($stid, 'CD_CODE') . "</td>";
			echo "<td>" .oci_result($stid, 'CD_NAME') . "</td>";
			echo "<td>" .oci_result($stid, 'CD_LICENSE') . "</td>";
			echo "<td>" .oci_result($stid, 'CD_SPECIALIZATON') . "</td>";
			echo "</tr>";
		}
		echo "</table>";
		oci_close($conn);
    }


    public function updatePhysician()
    {
    echo "updatePhysician";
    echo "<br>\n";
	// insert all physician details
	DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');

		$sql = "SELECT * FROM clinician_details WHERE CD_UPDATED_ON = '".strtoupper(date('d-M-').'22')."'  ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		$i =1;
		while (oci_fetch($stid)) {
		
		
		
		    $statusIs = (oci_result($stid, 'CD_ACTIVE') == 'Y')?'Active':'Inactive';
		    echo oci_result($stid, 'CD_CODE') . " is ";
		    echo oci_result($stid, 'CD_NAME') . "<br>\n";
		
		  DB::connection('Eros')->update("UPDATE Physician set 
			`FullName`  = '".htmlentities(oci_result($stid, 'CD_NAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."'
			,`PRCNo` = '".trim(oci_result($stid, 'CD_LICENSE'))."'
			,`Description` = '".trim(oci_result($stid, 'CD_SPECIALIZATON'))."'
			,`ErosCode` = '".trim(oci_result($stid, 'CD_CODE'))."'
			,`Status` = '".$statusIs."'
			,`UpdateBy` = 'Admin'
			,`UpdateDate` = '".date('Y-m-d')."'
			WHERE `ErosCode` LIKE '".trim(oci_result($stid, 'CD_CODE'))."'
		    ");
		}
		oci_close($conn);
	
	DB::connection('Eros')->commit();

	
    }
    // Get Physician and insert if not exist in local DB
    public function insertPhysician()
    {
    //echo strtoupper(date('d-M-').'22'); die();
    echo "insertPhysician";
    echo "<br>\n";
	// insert all physician details
	DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');

		$sql = "SELECT * FROM clinician_details WHERE CD_CREATED_ON = '".strtoupper(date('d-M-').'22')."' AND CD_CREATED_BY NOT LIKE 'RAV' ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		$i =1;
		while (oci_fetch($stid)) {
			$erosCode = ErosDB::getErosByCode(oci_result($stid, 'CD_CODE'));
			
			if(count($erosCode) == 0)
			{
				$statusIs = (oci_result($stid, 'CD_ACTIVE') == 'Y')?'Active':'Inactive';
				echo oci_result($stid, 'CD_CODE') . " is ";
				echo oci_result($stid, 'CD_NAME') . "<br>\n";
				DB::connection('Eros')->insert("INSERT INTO Physician (`FullName`, `PRCNo`, `Description`, `InputDate`, `InputBy`, `Status`, `ErosCode`, `Code` )  VALUES 
					(
					'".htmlentities(oci_result($stid, 'CD_NAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."'
					,'".oci_result($stid, 'CD_LICENSE')."'
					,'".oci_result($stid, 'CD_SPECIALIZATON')."'
					, '".date('Y-m-d')."'
					,'".oci_result($stid, 'CD_CREATED_BY')."'
					,'".$statusIs."'
					,'".oci_result($stid, 'CD_CODE')."'
					,'".oci_result($stid, 'CD_CODE')."'
					)
				"); 
			}
		}

		oci_close($conn);
	
	DB::connection('Eros')->commit();

	
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
     // Air China 
    public function index()
    { echo 'Start';
	
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8'); 

	$sql = "INSERT into PATIENT_MASTER (PM_PID, PM_FULLNAME, PM_LASTNAME, PM_FIRSTNAME,PM_MIDNAME, PM_GENDER, PM_DOB, PM_ADDRESS, PM_MOBILENO, PM_PASSPORTNO, PM_NATIONALITY, 
	PM_CREATED_ON, PM_CREATED_BY, PM_UPDATE_ON, PM_UPDATE_BY, PM_LASTVISIT, PM_EMAIL, PM_BARANGAY, PM_CITY) VALUES (
	:PM_PID, :PM_FULLNAME, :PM_LASTNAME, :PM_FIRSTNAME, :PM_MIDNAME,:PM_GENDER, :PM_DOB, :PM_ADDRESS, :PM_MOBILENO, :PM_PASSPORTNO, :PM_NATIONALITY,
	:PM_CREATED_ON, :PM_CREATED_BY, :PM_UPDATE_ON, :PM_UPDATE_BY, :PM_LASTVISIT, :PM_EMAIL, :PM_BARANGAY, :PM_CITY
	)";
		
	$datas =  DB::connection('DnCMS')->select("
		SELECT *
		FROM `patient_airchina` tb1
		WHERE
		tb1.`erosPatientId` IS NULL
	");
	$max = DB::connection('DnCMS')->select("SELECT SUBSTR(MAX(erosPatientId),-4) as iMax from patient_airchina where erosPatientId like 'U".date('Ymd')."%' " );

	$x = ($max[0]->iMax != 0)?$max[0]->iMax+1:1;
	
	foreach($datas as $data)
	{
		DB::connection('DnCMS')->beginTransaction();
		
		echo "<br>";
		$compiled = oci_parse($conn, $sql);
		echo $PM_PID = "U".date('Ymd').sprintf('%04d', $x++);//'U202206250002';
		$PM_FULLNAME = strtoupper(trim($data->lastname) . ", ".  trim($data->firstname));// "VALMORES, RICKY TEST";
		$PM_LASTNAME = strtoupper(trim($data->lastname));  //"VALMORES";
		$PM_FIRSTNAME = strtoupper(trim($data->firstname)) ;  //"RICKY TEST";
		$PM_MIDNAME = strtoupper(trim($data->middlename)) ;  //"RICKY TEST";
		$PM_GENDER =  substr($data->gender, 0, 1);//"M";
		$PM_DOB = \Carbon\Carbon::createFromFormat('Y-m-d', $data->dob)->format('d-M-Y');    //date_format($data['dob'], "Y-m-d"); //"01-AUG-1983";
		$PM_ADDRESS = strtoupper($data->address);//"32 KALAYAAN C BATASAN HILLS Q.C";
		$PM_MOBILENO = $data->mobile;//"09610060876";
		$PM_PASSPORTNO =  strtoupper($data->passportNo);//"PA12345678Z";
		$PM_NATIONALITY = strtoupper($data->nationality); //"FILIPINO";
		$PM_CREATED_ON = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y');//"27-JUN-2022";
		$PM_CREATED_BY = "RICKY";
		$PM_UPDATE_ON = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y'); //"27-JUN-2022";
		$PM_UPDATE_BY = "RICKY";
		$PM_LASTVISIT = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y');//"27-JUN-2022";
		$PM_EMAIL =  $data->email; // $data['email']; //"ricky.valmores@gmail.com";
		$PM_BARANGAY = ".";
		$PM_CITY = ".";

		oci_bind_by_name($compiled, ":PM_PID", $PM_PID);
		oci_bind_by_name($compiled, ":PM_FULLNAME", $PM_FULLNAME);
		oci_bind_by_name($compiled, ":PM_LASTNAME", $PM_LASTNAME);
		oci_bind_by_name($compiled, ":PM_FIRSTNAME", $PM_FIRSTNAME);
		oci_bind_by_name($compiled, ":PM_MIDNAME", $PM_MIDNAME);
		oci_bind_by_name($compiled, ":PM_GENDER", $PM_GENDER);
		oci_bind_by_name($compiled, ":PM_DOB", $PM_DOB);
		oci_bind_by_name($compiled, ":PM_ADDRESS", $PM_ADDRESS);
		oci_bind_by_name($compiled, ":PM_MOBILENO", $PM_MOBILENO );
		oci_bind_by_name($compiled, ":PM_PASSPORTNO", $PM_PASSPORTNO);
		oci_bind_by_name($compiled, ":PM_NATIONALITY", $PM_NATIONALITY);
		oci_bind_by_name($compiled, ":PM_CREATED_ON", $PM_CREATED_ON);
		oci_bind_by_name($compiled, ":PM_CREATED_BY", $PM_CREATED_BY);
		oci_bind_by_name($compiled, ":PM_UPDATE_ON", $$PM_UPDATE_ON);
		oci_bind_by_name($compiled, ":PM_UPDATE_BY", $PM_UPDATE_BY);
		oci_bind_by_name($compiled, ":PM_LASTVISIT", $PM_LASTVISIT);
		oci_bind_by_name($compiled, ":PM_EMAIL", $PM_EMAIL);
		oci_bind_by_name($compiled, ":PM_BARANGAY", $PM_BARANGAY);
		oci_bind_by_name($compiled, ":PM_CITY", $PM_CITY);
		
		file_put_contents("/mnt/share/".$PM_PID.".jpg", fopen($data->photoLink, 'r'));

		$result = oci_execute($compiled);
		
		if (!$result) {
		    $e = oci_error($query);  // For oci_execute errors pass the statement handle
		    print htmlentities($e['message']);
		    print "\n<pre>\n";
		    print htmlentities($e['sqltext']);
		    printf("\n%".($e['offset']+1)."s", "^");
		    print  "\n</pre>\n";
		    oci_close($conn);
		}else{
			print "<br>Connected and Inserted to Oracle! PROD EROS";  
			DB::connection('DnCMS')->update("UPDATE `patient_airchina` tb1  SET tb1.`erosPatientId` = '".$PM_PID."' WHERE tb1.id = '".$data->id."'  ");
			
			DB::connection('DnCMS')->commit();
			oci_close($conn);
		}
		
	} 
	  
     echo '<BR>End';
    }
    
    // Eros Push Data IMD with new patient insertErosDB
   public function insertPatient()
  { echo 'Start';
	
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8'); 

	$sql = "INSERT into PATIENT_MASTER (PM_PID, PM_FULLNAME, PM_LASTNAME, PM_FIRSTNAME, PM_MIDNAME, PM_GENDER, PM_DOB, PM_ADDRESS, PM_MOBILENO, PM_PASSPORTNO, PM_NATIONALITY, 
	PM_CREATED_ON, PM_CREATED_BY, PM_UPDATE_ON, PM_UPDATE_BY, PM_LASTVISIT, PM_EMAIL, PM_BARANGAY, PM_CITY) VALUES (
	:PM_PID, :PM_FULLNAME, :PM_LASTNAME, :PM_FIRSTNAME, :PM_MIDNAME, :PM_GENDER, :PM_DOB, :PM_ADDRESS, :PM_MOBILENO, :PM_PASSPORTNO, :PM_NATIONALITY,
	:PM_CREATED_ON, :PM_CREATED_BY, :PM_UPDATE_ON, :PM_UPDATE_BY, :PM_LASTVISIT, :PM_EMAIL, :PM_BARANGAY, :PM_CITY
	)";
		
	$datas =  DB::connection('Eros')->select("
		SELECT *
		FROM `Patient` tb1
		WHERE
		tb1.`Status` like 'NEW'
	");
	
	foreach($datas as $data)
	{
		DB::connection('Eros')->beginTransaction();
		
		echo "<br>";
		$compiled = oci_parse($conn, $sql);
		echo $PM_PID = strtoupper(trim($data->Code));
		$PM_FULLNAME = strtoupper(trim($data->FullName));
		$PM_LASTNAME = strtoupper(trim($data->LastName));  //"VALMORES";
		$PM_FIRSTNAME = strtoupper(trim($data->FirstName)) ;  //"RICKY TEST";
		$PM_MIDNAME = strtoupper(trim($data->MiddleName)) ;  //"AVENIDO TEST";
		$PM_GENDER =  substr($data->Gender, 0, 1);//"M";
		$PM_DOB = \Carbon\Carbon::createFromFormat('Y-m-d', $data->DOB)->format('d-M-Y');    //date_format($data['dob'], "Y-m-d"); //"01-AUG-1983";
		$PM_ADDRESS = strtoupper($data->Address);//"32 KALAYAAN C BATASAN HILLS Q.C";
		$PM_MOBILENO = $data->ContactNo;//"09610060876";
		$PM_PASSPORTNO =  "";//"PA12345678Z";
		$PM_NATIONALITY = strtoupper($data->Nationality); //"FILIPINO";
		$PM_CREATED_ON = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y');//"27-JUN-2022";
		$PM_CREATED_BY = "RICKY";
		$PM_UPDATE_ON = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y'); //"27-JUN-2022";
		$PM_UPDATE_BY = "RICKY";
		$PM_LASTVISIT = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y');//"27-JUN-2022";
		$PM_EMAIL =  "."; // $data['email']; //"ricky.valmores@gmail.com";
		$PM_BARANGAY = ".";
		$PM_CITY = ".";

		oci_bind_by_name($compiled, ":PM_PID", $PM_PID);
		oci_bind_by_name($compiled, ":PM_FULLNAME", $PM_FULLNAME);
		oci_bind_by_name($compiled, ":PM_LASTNAME", $PM_LASTNAME);
		oci_bind_by_name($compiled, ":PM_FIRSTNAME", $PM_FIRSTNAME);
		oci_bind_by_name($compiled, ":PM_MIDNAME", $PM_MIDNAME);
		oci_bind_by_name($compiled, ":PM_GENDER", $PM_GENDER);
		oci_bind_by_name($compiled, ":PM_DOB", $PM_DOB);
		oci_bind_by_name($compiled, ":PM_ADDRESS", $PM_ADDRESS);
		oci_bind_by_name($compiled, ":PM_MOBILENO", $PM_MOBILENO );
		oci_bind_by_name($compiled, ":PM_PASSPORTNO", $PM_PASSPORTNO);
		oci_bind_by_name($compiled, ":PM_NATIONALITY", $PM_NATIONALITY);
		oci_bind_by_name($compiled, ":PM_CREATED_ON", $PM_CREATED_ON);
		oci_bind_by_name($compiled, ":PM_CREATED_BY", $PM_CREATED_BY);
		oci_bind_by_name($compiled, ":PM_UPDATE_ON", $$PM_UPDATE_ON);
		oci_bind_by_name($compiled, ":PM_UPDATE_BY", $PM_UPDATE_BY);
		oci_bind_by_name($compiled, ":PM_LASTVISIT", $PM_LASTVISIT);
		oci_bind_by_name($compiled, ":PM_EMAIL", $PM_EMAIL);
		oci_bind_by_name($compiled, ":PM_BARANGAY", $PM_BARANGAY);
		oci_bind_by_name($compiled, ":PM_CITY", $PM_CITY);
		
		//file_put_contents("/mnt/share/".$PM_PID.".jpg", fopen($data->photoLink, 'r'));

		$result = oci_execute($compiled);
		
		if (!$result) {
		    $e = oci_error($query);  // For oci_execute errors pass the statement handle
		    print htmlentities($e['message']);
		    print "\n<pre>\n";
		    print htmlentities($e['sqltext']);
		    printf("\n%".($e['offset']+1)."s", "^");
		    print  "\n</pre>\n";
		    oci_close($conn);
		}else{
			print "<br>Connected and Inserted to Oracle! PROD EROS";  
			DB::connection('Eros')->update("UPDATE `Patient` tb1  SET tb1.`Status` = 'Append' WHERE tb1.Id = '".$data->Id."'  ");
			DB::connection('Eros')->commit();
			oci_close($conn);
		}
		
	} 
	  
     echo '<BR>End';
    } 
    // push data to Bizbox apps IMD and Bizbox
   public function insertPatientBizBox()
  { echo 'Start';    
//echo phpinfo();die();	
	//DB::connection('Eros')->beginTransaction();
	$datas =  DB::connection('Eros')->select("
		SELECT *
		FROM `Patient_temp` tb1
		WHERE
		tb1.`StatusBizbox` IS NULL  and tb1.APEDate IS NOT NULL and `Status` NOT LIKE 'toBizBox' 
	");
	
	foreach($datas as $data)
	{
	
		$suffix = (empty($data->Suffix))?'' :$data->Suffix;
		
		//DB::connection('BizBox')->beginTransaction();
	

		$dataAPE = 
		[
		    [
			'lastname'		=> $data->LastName,
			'firstname'		=> $data->FirstName,
			'middlename'	=> $data->MiddleName,
			'gender'		=> $data->Gender,
			'suffix'		=> $suffix,
			'birthdate'		=> \Carbon\Carbon::createFromFormat('Y-m-d', $data->DOB)->format('m/d/Y'),
			'address'		=> $data->Address,
			'test'			=> 'APE',
			'status'		=> 'PENDING',
			'dateTime'		=> $data->APEDate
			
		    ]
		];
		DB::connection('BizBox')->table('uploading')->insert($dataAPE);
		
		$dataXRAY = 
		[
		    [
			'lastname'		=> $data->LastName,
			'firstname'		=> $data->FirstName,
			'middlename'	=> $data->MiddleName,
			'gender'		=> $data->Gender,
			'suffix'		=> $suffix,
			'birthdate'		=> \Carbon\Carbon::createFromFormat('Y-m-d', $data->DOB)->format('m/d/Y'),
			'address'		=> $data->Address,
			'test'			=> 'XRAY',
			'status'		=> 'PENDING',
			'dateTime'		=> $data->APEDate
			
		    ]
		];
		
		DB::connection('BizBox')->table('uploading')->insert($dataXRAY);

		/*
		DB::connection('BizBox')->insert("INSERT INTO uploading (lastname , firstname, middlename, gender, suffix, birthdate, address, test, status, dateTime) VALUES  
			(
			'".$data->LastName."'
			,'".$data->FirstName."'
			,'".$data->MiddleName."'
			,'".$data->Gender."'
			,'".$suffix."'
			,'".\Carbon\Carbon::createFromFormat('Y-m-d', $data->DOB)->format('m/d/Y')."'
			,'".$data->Address."'
			,'APE'
			,'PENDING'
			,'".$data->APEDate."'
			)
		");
		DB::connection('BizBox')->insert("INSERT INTO uploading (lastname , firstname, middlename, gender, suffix ,birthdate, address, test, status, dateTime) VALUES  
			(
			'".$data->LastName."'
			,'".$data->FirstName."'
			,'".$data->MiddleName."'
			,'".$data->Gender."'
			,'".$suffix."'
			,'".\Carbon\Carbon::createFromFormat('Y-m-d', $data->DOB)->format('m/d/Y')."'
			,'".$data->Address."'
			,'XRAY'
			,'PENDING'
			,'".$data->APEDate."'
			)
		");
		*/
		// ECG 
		if($data->Address == "MANILA NORTH HARBOUR PORT, INC.")
		{
			DB::connection('BizBox')->insert("INSERT INTO uploading (lastname , firstname, middlename, gender, suffix, birthdate, address, test, status, dateTime) VALUES  
				(
				'".$data->LastName."'
				,'".$data->FirstName."'
				,'".$data->MiddleName."'
				,'".$data->Gender."'
				,'".$suffix."'
				,'".\Carbon\Carbon::createFromFormat('Y-m-d', $data->DOB)->format('m/d/Y')."'
				,'".$data->Address."'
				,'ECG'
				,'PENDING'
				,'".$data->APEDate."'
				)
			");
		}
		
		DB::connection('Eros')->update("UPDATE Patient_temp SET StatusBizbox = 'APPEND' WHERE `Id` = '".$data->Id."' ");
		//DB::connection('BizBox')->commit();
	}
	//DB::connection('Eros')->commit();
    
    
   } 
   //////ECG BIZBOX
    public function insertECGPatientBizBox()
  { echo 'Start';    
//echo phpinfo();die();	
	//DB::connection('Eros')->beginTransaction();
	$datas =  DB::connection('Eros')->select("
		SELECT *
		FROM `Patient_temp` tb1
		WHERE
		tb1.`WithTestStatus` IS NULL  and tb1.APEDate IS NOT NULL and `WithTest` LIKE 'ECG' 
	");
	
	foreach($datas as $data)
	{
	
		$suffix = (empty($data->Suffix))?'' :$data->Suffix;
		
		//DB::connection('BizBox')->beginTransaction();
		$dataECG = 
		[
		    [
			'lastname'		=> $data->LastName,
			'firstname'		=> $data->FirstName,
			'middlename'	=> $data->MiddleName,
			'gender'		=> $data->Gender,
			'suffix'		=> $suffix,
			'birthdate'		=> \Carbon\Carbon::createFromFormat('Y-m-d', $data->DOB)->format('m/d/Y'),
			'address'		=> $data->Address,
			'test'			=> 'ECG',
			'status'		=> 'PENDING',
			'dateTime'		=> $data->APEDate
			
		    ]
		];
		
		DB::connection('BizBox')->table('uploading')->insert($dataECG);
		DB::connection('Eros')->update("UPDATE Patient_temp SET WithTestStatus = 'APPEND' WHERE `Id` = '".$data->Id."' ");
		//DB::connection('BizBox')->commit();
	}
	//DB::connection('Eros')->commit();
    
    
   } 
   
   // push data to Bizbox
   public function insertPatientEros2BizBox()
  { echo 'Start';    
//echo phpinfo();die();	
	//DB::connection('Eros')->beginTransaction();
	$datas =  DB::connection('Eros')->select("
		SELECT *
		FROM `Patient_temp` tb1
		WHERE
		tb1.`StatusBizbox` IS NULL  and tb1.APEDate IS NOT NULL and tb1.Status like 'toBizBox'
	");
	
	foreach($datas as $data)
	{
		$suffix = (empty($data->Suffix))?'' :$data->Suffix;
	
		//DB::connection('BizBox')->beginTransaction();
		DB::connection('BizBox')->insert("INSERT INTO uploading (lastname , firstname, middlename, suffix, gender, birthdate, address, test, status, dateTime) VALUES  
			(
			'".$data->LastName."'
			,'".$data->FirstName."'
			,'".$data->MiddleName."'
			,'".$suffix."'
			,'".$data->Gender."'
			,'".\Carbon\Carbon::createFromFormat('Y-m-d', $data->DOB)->format('m/d/Y')."'
			,'".$data->Address."'
			,'".$data->UploadID."'
			,'PENDING'
			,'".$data->APEDate."'
			)
		");
		DB::connection('BizBox')->insert("INSERT INTO uploading (lastname , firstname, middlename, suffix, gender, birthdate, address, test, status, dateTime) VALUES  
			(
			'".$data->LastName."'
			,'".$data->FirstName."'
			,'".$data->MiddleName."'
			,'".$suffix."'
			,'".$data->Gender."'
			,'".\Carbon\Carbon::createFromFormat('Y-m-d', $data->DOB)->format('m/d/Y')."'
			,'".$data->Address."'
			,'XRAY'
			,'PENDING'
			,'".$data->APEDate."'
			)
		");
		DB::connection('Eros')->update("UPDATE Patient_temp SET StatusBizbox = 'APPEND' WHERE `Id` = '".$data->Id."' ");
		//DB::connection('BizBox')->commit();
	}
	//DB::connection('Eros')->commit();
    
    
   }
//update BizBox date to correct
	public function bizboxDateCorrection()
	{
		
		//DB::connection('Eros')->beginTransaction();
		$toCorrect = DB::connection('Eros')->select("SELECT * FROM Patient_temp WHERE  `StatusBizboxDate` IS NULL AND `APEDate` IS NOT NULL AND  `StatusBizbox` like 'APPEND'  and `Status` not like 'toBizBox'");
		
		foreach($toCorrect as $correct)
		{
			//DB::connection('BizBox')->beginTransaction();
			DB::connection('BizBox')->update("
				  update TB3
				  set TB3.rendate = '".$correct->APEDate." 00:00:00'  /*date = actual APE */
				  FROM uploading TB1,
				  patinv TB2, patitem TB3
				  where 
				  (TB2.dcno = TB3.dcno AND TB2.trackno = TB3.trackno)  and
				  TB1.test LIKE 'APE' AND
				  TB2.payorname like '".str_replace("'", "''",$correct->LastName).", ".str_replace("'", "''",$correct->FirstName)."%' and
				  TB1.dateTime = '".$correct->APEDate."' and /*date = actual APE */
				  TB2.rendate =  '".date('Y-m-d')."' and /*date = uploaded*/
				  TB1.address like '".str_replace("'", "''",$correct->Address)."%' and /*guarantor name*/
				  TB2.spclareacode like 'IT' AND
				  TB2.spclareawarecode like 'DEP000000000078'
			
			");
			DB::connection('BizBox')->update("
				  UPDATE TB2
				  SET TB2.rendate = '".$correct->APEDate." 00:00:00',  /*date = actual APE */
				  TB2.reqdate = '".$correct->APEDate." 00:00:00'  /*date = actual APE */
				  FROM uploading TB1,
				  patinv TB2
				  where 
				  TB1.test LIKE 'APE' AND
				  TB2.payorname like '".str_replace("'", "''",$correct->LastName).", ".str_replace("'", "''",$correct->FirstName)."%' and
				  TB1.dateTime = '".$correct->APEDate."' and /*date = actual APE */
				  TB2.rendate =  '".date('Y-m-d')."' and /*date = uploaded*/
				  TB1.address like '".str_replace("'", "''",$correct->Address)."%' and /*guarantor name*/
				  TB2.spclareacode like 'IT' AND
				  TB2.spclareawarecode like 'DEP000000000078'
			");
			DB::connection('Eros')->update("UPDATE Patient_temp SET StatusBizboxDate = 'UPDATED' WHERE `Id` = '".$correct->Id."' ");
			//DB::connection('BizBox')->commit();
		}
		//DB::connection('Eros')->commit();
		echo "Done";
	}
	
	/*public function erosTransaction()
	{
		
	DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');

		$sql = "SELECT 
		BTH_TRXNO,
		BTH_PID,
		TO_CHAR(BTH_TRXDT, 'YYYY-MM-DD') as BTH_TRXDT,
		BTD_ITEM_CODE,
		PM_FULLNAME,
		BTH_COMPANY,
		BTH_SOURCE
		FROM billing_trx_hdr TB1
		left join billing_trx_dtl TB2 ON (TB1.BTH_TRXNO = TB2.BTD_TRXNO)
		left join patient_master tb3 ON (tb1.BTH_PID = tb3.PM_PID)
		where tb1.bth_trxdt >= '26-MAY-22' AND TB1.bth_trxdt <= '25-JUL-22'
		and TB1.BTH_BILLING_STATUS NOT like 'C'
		and tb2.BTD_ITEM_GROUP NOT LIKE 'MISC' ";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		$i =1;
		while (oci_fetch($stid)) {
			//'".htmlentities(oci_result($stid, 'CD_NAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."'
			
			$dateT = \Carbon\Carbon::createFromFormat('Y-m-d', oci_result($stid, 'BTH_TRXDT'))->format('Y-m-d');
			DB::connection('hclab')->insert("INSERT INTO trans_eros (`TransactionNo`,`PatientId`, `Date`, `TestCode`, `FullName`, `CompanyName`, Clinic )  VALUES 
				(
				'".oci_result($stid, 'BTH_TRXNO')."'
				,'".oci_result($stid, 'BTH_PID')."'
				,'".$dateT."'
				,'".oci_result($stid, 'BTD_ITEM_CODE')."'
				,'".htmlentities(oci_result($stid, 'PM_FULLNAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."'
				,'".oci_result($stid, 'BTH_COMPANY')."'
				,'".oci_result($stid, 'BTH_SOURCE')."'
				)
			"); 
			
		}

		oci_close($conn);
	
	DB::connection('Eros')->commit();
		
	echo "Done";	
	
	}*/

   
    
}
