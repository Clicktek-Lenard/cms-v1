<?php

namespace App\Http\Controllers\eros;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

class ItemPriceController extends Controller
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
/*  // GET ALL DATA
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = erosprod))     )';
		$conn = oci_connect("erosbs", "erosbs", $cstr, 'UTF-8');

		$sqlSelect = "select tb2.im_description,tb2.im_group,tb2.im_tg, tb1.* from item_price tb1
		LEFT JOIN item_master tb2 ON (tb1.ip_item_code = tb2.im_code) 
		where ip_company like '03' and tb2.im_group NOT like 'MISC' ";
		$itemSelect = oci_parse($conn, $sqlSelect);
		oci_execute($itemSelect);
		
		DB::connection('Eros')->beginTransaction();
		while (oci_fetch($itemSelect)) {
		
			DB::connection('Eros')->insert(" INSERT INTO ItemPrice (`ClinicCode`, `ItemCode`, `ItemDescription`, `CompanyCode`, `Price`, `InputDate`, `InputBy` ) VALUES 
				(
					'LIN'
					,'".oci_result($itemSelect, 'IP_ITEM_CODE')."'
					,'".htmlentities(oci_result($itemSelect, 'IM_DESCRIPTION'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."'
					,'05'
					,'".oci_result($itemSelect, 'IP_PRICE')."'
					,'".date('Y-m-d')."'
					,'RAV'
				)
			");
			
		}
		DB::connection('Eros')->commit();  
		oci_close($conn);
*/

/*

echo 'Start';
	DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = erosprod))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr); 

	$sql = "INSERT INTO ITEM_PRICE (IP_COMPANY, IP_ITEM_CODE, IP_ENABLED, IP_UPDATE_ON, IP_UPDATE_BY, IP_PRICE, IP_OLD_PRICE, IP_REGULAR_PRICE) VALUES
	(:IP_COMPANY, :IP_ITEM_CODE, :IP_ENABLED, :IP_UPDATE_ON, :IP_UPDATE_BY, :IP_PRICE, :IP_OLD_PRICE, :IP_REGULAR_PRICE)";
		
	$datas =  DB::connection('Eros')->select("
		SELECT *
		FROM `ItemPrice` tb1
		WHERE
		tb1.`ErosStatus` IS NULL
	");
	
	foreach($datas as $data)
	{
		
		$sqlSelect = "SELECT  * FROM ITEM_PRICE WHERE  IP_COMPANY LIKE  '".$data->CompanyCode."' AND  IP_ITEM_CODE LIKE '".$data->ItemCode."' ";
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
			$IP_COMPANY = $data->CompanyCode;
			echo $IP_ITEM_CODE = $data->ItemCode; echo "<br>";
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
			$itemUpdate = oci_parse($conn, "UPDATE ITEM_PRICE SET IP_PRICE='" . $data->Price . "'  WHERE  IP_COMPANY LIKE  '".$data->CompanyCode."' AND  IP_ITEM_CODE LIKE '".$data->ItemCode."'  ");
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

*/

//########################################
/*
	DB::connection('Eros')->beginTransaction();
	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('uploads/KAIROS.xlsx'));
	//  Get worksheet dimensions
	$sheet = $spreadsheet->getActiveSheet(); 
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	//  Loop through each row of the worksheet in turn
	for ($row = 3; $row <= $highestRow; $row++)
	{ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':I' . $row,NULL,TRUE,FALSE);
	
		//echo trim($rowData[0][0]); //Description
		//echo trim($rowData[0][1]); //Price
		echo trim($rowData[0][2]); //Item Code
		echo "<br>";
		//die();
		if(!empty(trim($rowData[0][2])) && !empty(trim($rowData[0][1])) )
		$patients = DB::connection('Eros')->insert("INSERT INTO `ItemPrice`  (`ClinicCode`, `ItemCode`, `CompanyCode`, `Price`, `InputDate`, `InputBy`) VALUES
			(
				'ALL'
				,'".strtoupper(trim($rowData[0][2]))."'
				,'KAI002187'
				,'".strtoupper(trim($rowData[0][1]))."'
				,'2022-08-15'
				,'RICKY'
			)
		");
	}
	DB::connection('Eros')->commit();  
		

	
	//#####################################
 /*   
    
	// insert all physician details
	DB::connection('Eros')->beginTransaction();
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = erosprod))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr);

			$sql = "select * from item_price 
				LEFT JOIN item_master ON (item_price.ip_item_code = item_master.im_code)
				where ip_company like '00'
				ORDER by ip_item_code ASC";
			$stid = oci_parse($conn, $sql);
			$result = oci_execute($stid);
		
			if (!$result) {
			    $e = oci_error($query);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}else{
				echo "<table>";
					echo "<tr>";
					echo "<td>Item Code</td>";
					echo "<td>Item Description</td>";
					echo "<td>Item Price</td>";
					echo "<td>Regular Price</td>";
					echo "<td>Main Price</td>";
					echo "</tr>";	
				
				while (oci_fetch($stid)) {
				
					echo "<tr>";
					echo "<td>".oci_result($stid, 'IM_CODE') . "</td>";
					echo "<td>".oci_result($stid, 'IM_DESCRIPTION') . "</td>";
					echo "<td>".oci_result($stid, 'IP_PRICE') . "</td>";
					echo "<td>".oci_result($stid, 'IP_REGULAR_PRICE') . "</td>";
					echo "<td>".oci_result($stid, 'IM_PRICE') . "</td>";
					echo "</tr>";
				}
				echo "</table>";
			}
			
			oci_close($conn);
	
	

	
	
	DB::connection('Eros')->commit();
	// end insert all physician details
*/
	
}
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
    
    }
}
