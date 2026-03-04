<?php

namespace App\Models\www;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;



class WWWPDF extends Model
{

	
	public function getPDFDataServer($name = NULL)
	{
		if(empty($name) ){
			$data = DB::connection('WWW')->select("SELECT `id`, CONCAT(`lastname`, ', ', `firstname`, ' ', `middlename`) as `FullName`, `or_no`, `patient_id`, `trans_no`,DATE_FORMAT(`order_date`, '%d-%b-%Y') as order_date,  `birthdate`, `created_at` FROM  pdf_results  WHERE  
			`patient_id` NOT IN ('**********','00','0000001468','0000001471','0000001495','0000001500','0000001583','0000001677','0000001688','0000002172','0926','') AND `order_date` LIKE '".date('Y-m-d')."%' LIMIT 1000");
		}else{
			$data = DB::connection('WWW')->select("SELECT `id`, CONCAT(`lastname`, ', ', `firstname`, ' ', `middlename`) as `FullName`, `or_no`, `patient_id`, `trans_no`,DATE_FORMAT(`order_date`, '%d-%b-%Y') as order_date, `birthdate`, `created_at` FROM  pdf_results  WHERE  
			`patient_id` NOT IN ('**********','00','0000001468','0000001471','0000001495','0000001500','0000001583','0000001677','0000001688','0000002172','0926','') and CONCAT(`lastname`, ', ', `firstname`, ' ', `middlename`) like '%".$name."%' LIMIT 1000");
		}
		return $data;
	}
	
	public function getPDFidDataServer($id = NULL)
	{
		if(empty($id) ){
			$data = 'Missing param';
		}else{
			$data = DB::connection('WWW')->select("SELECT `id`,  CONCAT(`lastname`, ', ', `firstname`, ' ', `middlename`) as `FullName`, `or_no`, `patient_id`, `trans_no`,`order_date`, `birthdate`, `created_at` FROM  pdf_results  WHERE  
			`patient_id` NOT IN ('**********','00','0000001468','0000001471','0000001495','0000001500','0000001583','0000001677','0000001688','0000002172','0926','') and id = '".$id."' ");
		}
		return $data;
	}
	
	public function getPDFbyDateDataServer()
	{
		
		$data = DB::connection('WWW')->select("SELECT `id`,  CONCAT(`lastname`, ', ', `firstname`, ' ', `middlename`) as `FullName`, `or_no`, `patient_id`, `trans_no`,`order_date`, `birthdate`, `created_at` FROM  pdf_results  WHERE  
			`patient_id` NOT IN ('**********','00','0000001468','0000001471','0000001495','0000001500','0000001583','0000001677','0000001688','0000002172','0926','') and 
		`order_date` >= '2022-05-26'  and `order_date` <= '2022-07-25' 
		");
		
		return $data;
	}
	
	
}
