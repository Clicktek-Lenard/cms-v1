<?php

namespace App\Http\Controllers\eros;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

class ItemController extends Controller
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
	
echo 'e2 ngas';
/*
$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = erosprod))     )';
$conn = oci_connect("erosbs", "erosbs", $cstr);

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
				DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`ErosStatus` = 'UPDATED' WHERE tb1.Id = '".$data->Id."'  ");
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
				DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`ErosStatus` = 'UPDATEDS' WHERE tb1.Id = '".$data->Id."'  ");
			}
			oci_close($conn);
			//DB::connection('Eros')->update("UPDATE `ItemPrice` tb1  SET tb1.`ErosStatus` = 'ERROR' WHERE tb1.Id = '".$data->Id."'  ");
		}
	}
	DB::connection('Eros')->commit();  

*/


//########################################
 // EXCEL UPLOADING NEW ITEMS


/*

	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('uploads/TagaytayHomeService.xlsx'));
	//  Get worksheet dimensions
	$sheet = $spreadsheet->getActiveSheet(); 
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	//  Loop through each row of the worksheet in turn
	
	
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = erosprod))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr); 
	
	$sql = "INSERT INTO ITEM_MASTER (IM_CODE, IM_DESCRIPTION, IM_SHORT_DESC, IM_CREATED_ON, IM_CREATED_BY, IM_ACTIVE, IM_CPTPANEL, IM_CPTPARTIAL, IM_ISPARENT, IM_GROUP, IM_PRICE, IM_TG) VALUES
	(:IM_CODE, :IM_DESCRIPTION, :IM_SHORT_DESC, :IM_CREATED_ON, :IM_CREATED_BY, :IM_ACTIVE, :IM_CPTPANEL, :IM_CPTPARTIAL, :IM_ISPARENT, :IM_GROUP, :IM_PRICE, :IM_TG)";

	
	for ($row = 2; $row <= $highestRow; $row++)
	{ 
		DB::connection('Eros')->beginTransaction();
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':I' . $row,NULL,TRUE,FALSE);
	
		if(empty(trim($rowData[0][4])))
		{
			continue;
		}
	
		echo trim($rowData[0][0]);
			//Description
		echo trim($rowData[0][4]); //Price
		echo "<br>";
		//echo trim($rowData[0][2]); //Item Code
		//die();
	
		$iMax = DB::connection('Eros')->select("select CONCAT('HS', (SELECT LPAD(ifnull(MAX(`ID`)+1,1), 6, '0') as iMAX FROM Item) ) AS newCode ");
		
			$compiled = oci_parse($conn, $sql);
			
			$IM_CODE = $iMax[0]->newCode;
			$IM_DESCRIPTION = 'HOME SERVICE - TAGAYTAY ('.strtoupper(trim($rowData[0][0])).')';
			$IM_SHORT_DESC = 'HOME SERVICE';
			$IM_CREATED_ON = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y'); //"27-JUN-2022";
			$IM_CREATED_BY = 'RAV';
			$IM_ACTIVE = 'Y';
			$IM_CPTPANEL = 'N';
			$IM_CPTPARTIAL = 'N';
			$IM_ISPARENT = 'N';
			$IM_GROUP = 'MISC';
			$IM_PRICE = trim($rowData[0][4]);
			$IM_TG = 'OTHERS';
			
			oci_bind_by_name($compiled, ":IM_CODE", $IM_CODE);
			oci_bind_by_name($compiled, ":IM_DESCRIPTION", $IM_DESCRIPTION);
			oci_bind_by_name($compiled, ":IM_SHORT_DESC", $IM_SHORT_DESC);
			oci_bind_by_name($compiled, ":IM_CREATED_ON", $IM_CREATED_ON);
			oci_bind_by_name($compiled, ":IM_CREATED_BY", $IM_CREATED_BY);
			oci_bind_by_name($compiled, ":IM_ACTIVE", $IM_ACTIVE);
			oci_bind_by_name($compiled, ":IM_CPTPANEL", $IM_CPTPANEL);
			oci_bind_by_name($compiled, ":IM_CPTPARTIAL", $IM_CPTPARTIAL);
			oci_bind_by_name($compiled, ":IM_ISPARENT", $IM_ISPARENT);
			oci_bind_by_name($compiled, ":IM_GROUP", $IM_GROUP);
			oci_bind_by_name($compiled, ":IM_PRICE", $IM_PRICE);
			oci_bind_by_name($compiled, ":IM_TG", $IM_TG);
			

			$result = oci_execute($compiled);
			
			if (!$result){
			    $e = oci_error($result);  // For oci_execute errors pass the statement handle
			    print htmlentities($e['message']);
			    print "\n<pre>\n";
			    print htmlentities($e['sqltext']);
			    printf("\n%".($e['offset']+1)."s", "^");
			    print  "\n</pre>\n";
			}else{
				DB::connection('Eros')->insert("INSERT INTO `Item`  (`Code`, `Name`, `Price`,`ErosCode`) VALUES
					(
						'".$iMax[0]->newCode."'
						,'HOME SERVICE - TAGAYTAY (".strtoupper(trim($rowData[0][0])).")'
						,'".trim($rowData[0][4])."'
						,'".$iMax[0]->newCode."'
					)
				");
				DB::connection('Eros')->commit();  	
			}
			oci_close($conn);

	}
	
*/

	//#####################################
    
    
	// insert all physician details
	
/*	
	$sql = "INSERT INTO ITEM_MASTER (IM_CODE, IM_DESCRIPTION, IM_SHORT_DESC, IM_LIS_CODES, IM_CREATED_ON, IM_CREATED_BY, IM_UPDATE_ON, IM_UPDATE_BY, IM_ACTIVE, IM_CPTPANEL, IM_CPTPARTIAL, IM_ISPARENT, IM_GROUP, IM_PRICE, IM_TG) VALUES
	(:IM_CODE, :IM_DESCRIPTION, :IM_SHORT_DESC, :IM_LIS_CODES, :IM_CREATED_ON, :IM_CREATED_BY, :IM_UPDATE_ON, :IM_UPDATE_BY, :IM_ACTIVE, :IM_CPTPANEL, :IM_CPTPARTIAL, :IM_ISPARENT, :IM_GROUP, :IM_PRICE, :IM_TG)";



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
