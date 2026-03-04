<?php

namespace App\Http\Controllers\eros;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

class ErosTestController extends Controller
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
	public function state_save()
	{
		echo $_POST['ClinicCode'];
	}
     
	public function insertBillingHead()
	{
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
		
		$sqlTRX = "UPDATE AUTONO SET AUTO_NUMBER = AUTO_NUMBER + 1 WHERE AUTO_TYPE like 'TRX' ";
		$compiledTRX = oci_parse($conn, $sqlTRX);
		$resultTRX = oci_execute($compiledTRX);
		
		$sqlCS = "UPDATE AUTONO SET AUTO_NUMBER = AUTO_NUMBER + 1 WHERE AUTO_TYPE like 'CS' ";
		$compiledCS = oci_parse($conn, $sqlCS);
		$resultCS = oci_execute($compiledCS);
		
		if (!$resultTRX && !$resultCS) {
		    $e = oci_error($query);  // For oci_execute errors pass the statement handle
		    print htmlentities($e['message']);
		    print "\n<pre>\n";
		    print htmlentities($e['sqltext']);
		    printf("\n%".($e['offset']+1)."s", "^");
		    print  "\n</pre>\n";
		    oci_close($conn);
		}else{
			
			$sqlSelectTRX = "SELECT AUTO_NUMBER FROM AUTONO  WHERE AUTO_TYPE like 'TRX' ";	
			$stidTRX = oci_parse($conn, $sqlSelectTRX);
			oci_execute($stidTRX);
			
			$sqlSelectCS = "SELECT AUTO_NUMBER FROM AUTONO  WHERE AUTO_TYPE like 'CS' ";	
			$stidCS = oci_parse($conn, $sqlSelectCS);
			oci_execute($stidCS);
			while (oci_fetch($stidCS))
			{
				$CSNO = oci_result($stidCS, 'AUTO_NUMBER')-1;
				$BTH_CS_NO = "DT".date('y').sprintf('%08d', $CSNO);//'221234567890';
				
			}
			
			
			while (oci_fetch($stidTRX))
			{
				$trxNo = oci_result($stidTRX, 'AUTO_NUMBER')-1;
				$BTH_TRXNO = date('y').sprintf('%010d', $trxNo);//'221234567890';
				$BTH_TRXDT = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y');//"27-JUN-2022";
				$BTH_PID = 'L220000060122';
				$BTH_SOURCE = '10';
				$BTH_COMPANY = '00';
				$BTH_CLINICIAN = 'NODOC';
				$BTH_CREATED_ON =  \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y');//"27-JUN-2022";
				$BTH_CREATED_BY = 'RICKY';
				$BTH_UPDATE_ON =  \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y');//"27-JUN-2022";
				$BTH_UPDATE_BY = 'RICKY';
				$BTH_BILLING_STATUS = 'P';
				$BTH_PRIORITY	= 'R';
				$BTH_DISCOUNT = '0';
				$BTH_GRAND_TOTAL = '540';
				$BTH_PAYMENT_TYPE = 'C';
				$BTH_BRANCH_CODE = 'DT';
				$BTH_PAYCASH	= '0';
				$BTH_PAYCREDIT = '0';
				$BTH_PAYCHECK = '0';
				$BTH_SC_FLAG = 'F';
				$BTH_SC_DISCOUNT = '0';
				$BTH_LOYAL_PAY = '0';
				$BTH_GCASH_PAY = '0';
				$BTH_PROV_TYPE = 'CORPORATE';
				$BTH_PROVIDER = 'NWDI EMPLOYEE WELLNESS';
				$BTH_COVER_PAY = '540';
				$BTH_ONLINE_PAY = '0';
				$BTH_COVER_TYPE = 'FULL';
				$BTH_DISCOUNT_TOTAL = '0';
				$BTH_DISCOUNT_TYPE = '00';
				
			
				$sqlInsert = "INSERT INTO BILLING_TRX_HDR (
				BTH_TRXNO, BTH_TRXDT, BTH_PID, BTH_SOURCE, BTH_COMPANY, BTH_CLINICIAN, BTH_CREATED_ON, BTH_CREATED_BY,
				BTH_UPDATE_ON, BTH_UPDATE_BY, BTH_BILLING_STATUS, BTH_PRIORITY, BTH_DISCOUNT, BTH_GRAND_TOTAL, BTH_PAYMENT_TYPE, BTH_BRANCH_CODE, BTH_PAYCASH,
				BTH_PAYCREDIT, BTH_PAYCHECK, BTH_SC_FLAG, BTH_SC_DISCOUNT, BTH_LOYAL_PAY, BTH_GCASH_PAY, BTH_PROV_TYPE, BTH_PROVIDER, BTH_COVER_PAY, BTH_ONLINE_PAY,
				BTH_COVER_TYPE, BTH_DISCOUNT_TOTAL, BTH_DISCOUNT_TYPE, BTH_CS_NO 
				) VALUES (
				:BTH_TRXNO, :BTH_TRXDT, :BTH_PID, :BTH_SOURCE, :BTH_COMPANY, :BTH_CLINICIAN, :BTH_CREATED_ON, :BTH_CREATED_BY,
				:BTH_UPDATE_ON, :BTH_UPDATE_BY, :BTH_BILLING_STATUS, :BTH_PRIORITY, :BTH_DISCOUNT, :BTH_GRAND_TOTAL, :BTH_PAYMENT_TYPE, :BTH_BRANCH_CODE, :BTH_PAYCASH,
				:BTH_PAYCREDIT, :BTH_PAYCHECK, :BTH_SC_FLAG, :BTH_SC_DISCOUNT, :BTH_LOYAL_PAY, :BTH_GCASH_PAY, :BTH_PROV_TYPE, :BTH_PROVIDER, :BTH_COVER_PAY, :BTH_ONLINE_PAY,
				:BTH_COVER_TYPE, :BTH_DISCOUNT_TOTAL, :BTH_DISCOUNT_TYPE, :BTH_CS_NO
				)";
				
				$compiledTRXhead = oci_parse($conn, $sqlInsert);
				
				oci_bind_by_name($compiledTRXhead, ":BTH_TRXNO", $BTH_TRXNO);
				oci_bind_by_name($compiledTRXhead, ":BTH_TRXDT",  $BTH_TRXDT);
				oci_bind_by_name($compiledTRXhead, ":BTH_PID", $BTH_PID);
				oci_bind_by_name($compiledTRXhead, ":BTH_SOURCE", $BTH_SOURCE);
				oci_bind_by_name($compiledTRXhead, ":BTH_COMPANY", $BTH_COMPANY);
				oci_bind_by_name($compiledTRXhead, ":BTH_CLINICIAN", $BTH_CLINICIAN);
				oci_bind_by_name($compiledTRXhead, ":BTH_CREATED_ON", $BTH_CREATED_ON);
				oci_bind_by_name($compiledTRXhead, ":BTH_CREATED_BY", $BTH_CREATED_BY);
				oci_bind_by_name($compiledTRXhead, ":BTH_UPDATE_ON", $BTH_UPDATE_ON);
				oci_bind_by_name($compiledTRXhead, ":BTH_UPDATE_BY", $BTH_UPDATE_BY);
				oci_bind_by_name($compiledTRXhead, ":BTH_BILLING_STATUS", $BTH_BILLING_STATUS);
				oci_bind_by_name($compiledTRXhead, ":BTH_PRIORITY", $BTH_PRIORITY);
				oci_bind_by_name($compiledTRXhead, ":BTH_DISCOUNT", $BTH_DISCOUNT);
				oci_bind_by_name($compiledTRXhead, ":BTH_GRAND_TOTAL", $BTH_GRAND_TOTAL);
				oci_bind_by_name($compiledTRXhead, ":BTH_PAYMENT_TYPE", $BTH_PAYMENT_TYPE);
				oci_bind_by_name($compiledTRXhead, ":BTH_BRANCH_CODE", $BTH_BRANCH_CODE);
				oci_bind_by_name($compiledTRXhead, ":BTH_PAYCASH", $BTH_PAYCASH);
				oci_bind_by_name($compiledTRXhead, ":BTH_PAYCREDIT", $BTH_PAYCREDIT);
				oci_bind_by_name($compiledTRXhead, ":BTH_PAYCHECK", $BTH_PAYCHECK);
				oci_bind_by_name($compiledTRXhead, ":BTH_SC_FLAG", $BTH_SC_FLAG);
				oci_bind_by_name($compiledTRXhead, ":BTH_SC_DISCOUNT", $BTH_SC_DISCOUNT);
				oci_bind_by_name($compiledTRXhead, ":BTH_LOYAL_PAY", $BTH_LOYAL_PAY);
				oci_bind_by_name($compiledTRXhead, ":BTH_GCASH_PAY", $BTH_GCASH_PAY);
				oci_bind_by_name($compiledTRXhead, ":BTH_PROV_TYPE", $BTH_PROV_TYPE);
				oci_bind_by_name($compiledTRXhead, ":BTH_PROVIDER", $BTH_PROVIDER);
				oci_bind_by_name($compiledTRXhead, ":BTH_COVER_PAY", $BTH_COVER_PAY);
				oci_bind_by_name($compiledTRXhead, ":BTH_ONLINE_PAY", $BTH_ONLINE_PAY);
				oci_bind_by_name($compiledTRXhead, ":BTH_COVER_TYPE", $BTH_COVER_TYPE);
				oci_bind_by_name($compiledTRXhead, ":BTH_DISCOUNT_TOTAL", $BTH_DISCOUNT_TOTAL);
				oci_bind_by_name($compiledTRXhead, ":BTH_DISCOUNT_TYPE", $BTH_DISCOUNT_TYPE);
				oci_bind_by_name($compiledTRXhead, ":BTH_CS_NO", $BTH_CS_NO);
				$resultHead = oci_execute($compiledTRXhead);
				
				$sqlInsertDT = "INSERT INTO BILLING_TRX_DTL (BTD_TRXNO, BTD_ITEM_CODE, BTD_ICD_FLAG, BTD_ITEM_PRICE, BTD_UPDATE_ON, BTD_UPDATE_BY, 
				BTD_QTY, BTD_ITEM_GROUP) VALUES (
				:BTD_TRXNO, :BTD_ITEM_CODE, :BTD_ICD_FLAG, :BTD_ITEM_PRICE, :BTD_UPDATE_ON, :BTD_UPDATE_BY, 
				:BTD_QTY, :BTD_ITEM_GROUP
				)  ";
				$compiledTRXdetails = oci_parse($conn, $sqlInsertDT);
				
				$BTD_ITEM_CODE = 'SPK001';
				$BTD_ICD_FLAG = 'N';
				$BTD_ITEM_PRICE = '540';
				$BTD_UPDATE_ON = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y');//"27-JUN-2022";
				$BTD_UPDATE_BY = 'RICKY';
				$BTD_QTY = '1';
				$BTD_ITEM_GROUP = 'PACK';
				
				
				oci_bind_by_name($compiledTRXdetails, ":BTD_TRXNO", $BTH_TRXNO);
				oci_bind_by_name($compiledTRXdetails, ":BTD_ITEM_CODE", $BTD_ITEM_CODE);
				oci_bind_by_name($compiledTRXdetails, ":BTD_ICD_FLAG", $BTD_ICD_FLAG);
				oci_bind_by_name($compiledTRXdetails, ":BTD_ITEM_PRICE", $BTD_ITEM_PRICE);
				oci_bind_by_name($compiledTRXdetails, ":BTD_UPDATE_ON", $BTD_UPDATE_ON);
				oci_bind_by_name($compiledTRXdetails, ":BTD_UPDATE_BY", $BTD_UPDATE_BY);
				oci_bind_by_name($compiledTRXdetails, ":BTD_QTY", $BTD_QTY);
				oci_bind_by_name($compiledTRXdetails, ":BTD_ITEM_GROUP", $BTD_ITEM_GROUP);
				
				
				$resultDetails = oci_execute($compiledTRXdetails);
				
				
			}
			oci_close($conn);
		}
		
	}    
     
     
	public function passwordResert()
	{
	 //DB::connection('mysql')->update("UPDATE users SET password = '".Hash::make('3')."' WHERE id = 20 ");

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
					,'ADMIN'
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
    public function index()
    { echo 'Start';
	
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8'); 

	$sql = "INSERT into PATIENT_MASTER (PM_PID, PM_FULLNAME, PM_LASTNAME, PM_FIRSTNAME, PM_GENDER, PM_DOB, PM_ADDRESS, PM_MOBILENO, PM_PASSPORTNO, PM_NATIONALITY, 
	PM_CREATED_ON, PM_CREATED_BY, PM_UPDATE_ON, PM_UPDATE_BY, PM_LASTVISIT, PM_EMAIL, PM_BARANGAY, PM_CITY) VALUES (
	:PM_PID, :PM_FULLNAME, :PM_LASTNAME, :PM_FIRSTNAME, :PM_GENDER, :PM_DOB, :PM_ADDRESS, :PM_MOBILENO, :PM_PASSPORTNO, :PM_NATIONALITY,
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
		$PM_EMAIL =  "oneworldswabbing@gmail.com"; // $data['email']; //"ricky.valmores@gmail.com";
		$PM_BARANGAY = ".";
		$PM_CITY = ".";

		oci_bind_by_name($compiled, ":PM_PID", $PM_PID);
		oci_bind_by_name($compiled, ":PM_FULLNAME", $PM_FULLNAME);
		oci_bind_by_name($compiled, ":PM_LASTNAME", $PM_LASTNAME);
		oci_bind_by_name($compiled, ":PM_FIRSTNAME", $PM_FIRSTNAME);
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
				  TB2.payorname like '".$correct->LastName.", ".$correct->FirstName."%' and
				  TB1.dateTime = '".$correct->APEDate."' and /*date = actual APE */
				  TB2.rendate =  '".date('Y-m-d')."' and /*date = uploaded*/
				  TB1.address like '".$correct->Address."%' and /*guarantor name*/
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
				  TB2.payorname like '".$correct->LastName.", ".$correct->FirstName."%' and
				  TB1.dateTime = '".$correct->APEDate."' and /*date = actual APE */
				  TB2.rendate =  '".date('Y-m-d')."' and /*date = uploaded*/
				  TB1.address like '".$correct->Address."%' and /*guarantor name*/
				  TB2.spclareacode like 'IT' AND
				  TB2.spclareawarecode like 'DEP000000000078'
			");
			DB::connection('Eros')->update("UPDATE Patient_temp SET StatusBizboxDate = 'UPDATED' WHERE `Id` = '".$correct->Id."' ");
			//DB::connection('BizBox')->commit();
		}
		//DB::connection('Eros')->commit();
		
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
