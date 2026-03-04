<?php

namespace App\Models\hclab;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\hclab\SSP;


class HclabDB extends Model
{

	public function getPDFData($id = NULL)
	{
		ini_set('memory_limit', '-1');
		if(empty($id) ){
			$data = DB::connection('hclab')->select("SELECT `id`, `FullName`, `or_no`, `patient_id`, `trans_no`,`order_date`,  `birthdate`, `created_at` FROM  pdf_results  WHERE  `created_at`  >=  '2022-04-01'   and  `created_at` <= '2022-07-25' and 
			`patient_id` NOT IN ('**********','00','0000001468','0000001471','0000001495','0000001500','0000001583','0000001677','0000001688','0000002172','0926','') ");
		}else{
			$data = DB::connection('hclab')->select("SELECT `id`, `FullName`, `or_no`, `patient_id`, `trans_no`,`order_date`, `birthdate`, `created_at` FROM  pdf_results  WHERE  `created_at`  >=  '2022-04-01'   and  `created_at` <= '2022-07-25' and 
			`patient_id` NOT IN ('**********','00','0000001468','0000001471','0000001495','0000001500','0000001583','0000001677','0000001688','0000002172','0926','') and id = '".$id."' ");
		}
		return $data;
	}
	
	public function getPDFDataServer($name = NULL)
	{
		if(empty($name) ){
			$data = DB::connection('hclab')->select("SELECT `id`, `FullName`, `or_no`, `patient_id`, `trans_no`,`order_date`,  `birthdate`, `created_at` FROM  pdf_results  WHERE  
			`patient_id` NOT IN ('**********','00','0000001468','0000001471','0000001495','0000001500','0000001583','0000001677','0000001688','0000002172','0926','') LIMIT 1000");
		}else{
			$data = DB::connection('hclab')->select("SELECT `id`, `FullName`, `or_no`, `patient_id`, `trans_no`,`order_date`, `birthdate`, `created_at` FROM  pdf_results  WHERE  
			`patient_id` NOT IN ('**********','00','0000001468','0000001471','0000001495','0000001500','0000001583','0000001677','0000001688','0000002172','0926','') and FullName like '%".$name."%' LIMIT 1000");
		}
		return $data;
	}
	
	public function getPDFidDataServer($id = NULL)
	{
		if(empty($id) ){
			$data = 'Missing param';
		}else{
			$data = DB::connection('hclab')->select("SELECT `id`, `FullName`, `or_no`, `patient_id`, `trans_no`,`order_date`, `birthdate`, `created_at` FROM  pdf_results  WHERE  
			`patient_id` NOT IN ('**********','00','0000001468','0000001471','0000001495','0000001500','0000001583','0000001677','0000001688','0000002172','0926','') and id = '".$id."' ");
		}
		return $data;
	}
	
	
	public function getItem($id = NULL)
	{
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$conn = oci_connect("hclab", "hclab", $cstr);
		
		$sql = "SELECT * FROM item_masterd";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		$i =1;
		while (oci_fetch($stid)) 
		{
			 $statusIs = (oci_result($stid, 'CD_ACTIVE') == 'Y')?'Active':'Inactive';
			echo oci_result($stid, 'CD_CODE') . " is ";
			echo oci_result($stid, 'CD_NAME') . "<br>\n";
		}
		oci_close($conn);
	
	}
	
}
