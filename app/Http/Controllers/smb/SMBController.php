<?php

namespace App\Http\Controllers\smb;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

class SMBController extends Controller
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
    ##########SMB START########
    public function SMBPhysicianUpdate()
    {
	echo 'Start';
	//DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');

	$sqlInsert = "INSERT into CLINICIAN_DETAILS (CD_CODE,CD_NAME,CD_ACTIVE,CD_CREATED_BY,CD_CREATED_ON,CD_LICENSE,CD_SPECIALIZATON, CD_SALESREP) VALUES (
		:CD_CODE, :CD_NAME, :CD_ACTIVE, :CD_CREATED_BY, :CD_CREATED_ON, :CD_LICENSE,:CD_SPECIALIZATON, :CD_SALESREP
		)";
		
	$datas =  DB::connection('Eros')->select("
		SELECT * FROM `Physician`
		WHERE
		(`SMBStatus` LIKE 'reUpdate' OR  `SMBStatus` IS NULL)
		LIMIT 5000 
	");
	
	foreach($datas as $data)
	{
		
		echo $data->Id . " - <br>";
		echo $CD_CODE = $data->ErosCode; echo "<br>";
		$CD_NAME = $data->FullName;
		$CD_ACTIVE = 'Y';
		$CD_SALESREP = ($data->SECode != '#N/A')?$data->SECode:'';
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
			oci_bind_by_name($compiled, ":CD_SALESREP", $CD_SALESREP);
			
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
			$itemUpdate = oci_parse($conn, "UPDATE CLINICIAN_DETAILS SET CD_NAME ='" . $CD_NAME . "', CD_LICENSE = '". $CD_LICENSE ."', CD_SPECIALIZATON = '". $CD_SPECIALIZATON ."', CD_SALESREP = '".$CD_SALESREP."'
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
	echo "<br>";
	echo $data->Code;
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
			$CD_ACTIVE = ($cebu->Status == "Active")?"Y":"I";
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
					,CD_ACTIVE =  '".$CD_ACTIVE."'
					,CD_UPDATE_ON = '".date('d-M-Y')."'
					,CD_UPDATE_BY =  'CMS'
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
}