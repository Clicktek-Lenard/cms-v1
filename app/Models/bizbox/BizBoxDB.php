<?php

namespace App\Models\bizbox;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class BizBoxDB extends Model
{

	public function getDataCenter($params = array())
	{
		if(empty($params))
		{
			return "Missing Param";
		}
		else
		{
			return DB::connection('BizBox')->select("SELECT TOP(1) * FROM datacenter 
					WHERE LTRIM(RTRIM(fname)) = LTRIM(RTRIM('".$params['fname']."')) 
					and LTRIM(RTRIM(lname)) = LTRIM(RTRIM('".$params['lname']."')) 
					and LTRIM(RTRIM(mname)) = LTRIM(RTRIM('".$params['mname']."')) 
					and birthdate = '".$params['dob']."' ");
		}
	}
	
	
	
}
