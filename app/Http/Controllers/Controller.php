<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function CheckModuleAccess()
    {
	if( strpos(session('userRole') , '"ldap_role":"['.strtoupper(\Request::segment(2)).']"') !== false  )
		return true;
	else
		return false;
    }
    public function CheckBranchAccess($buCode = NULL)
   {
	if(empty($buCode)) return false;
	
	
	if( strpos(session('userRole') , '"ldap_role":"['.$buCode.'-BRANCH]"') !== false  )
		return true;
	else
		return false;
    }

    static public function genQueCode()
    {
	//get date today
	$today = date('Y-m-d');
	//get server id
	$mySVRid = DB::connection('CMS')->select('SHOW VARIABLES LIKE "server_id" ')[0]->Value;
	//get session bu
	$myIdBU = session('userClinicCode');
	//check if date = todate
	$buDate =  DB::connection('CMS')->table('ControlNum')->where('IdBU', 'LIKE', $myIdBU)->where('SVRID','=',$mySVRid)->get(array('Date'))[0]->Date;
	if($buDate != $today)
	{
		DB::connection('CMS')->table('TransactionTemp')->where('Date' , '!=' , $today)->delete();
		DB::connection('CMS')->table('ControlNum')->where('IdBU', 'LIKE', $myIdBU)->where('SVRID','=',$mySVRid)->update(['Date' => $today, 'Num' => 0]);
	}
	//check if data available in table
	$numCode = DB::connection('CMS')->table('ControlNum')->where('IdBU', 'LIKE', $myIdBU)->where('SVRID','=',$mySVRid)->where('Date', '=', $today)->get(array('Num'));
	if(count($numCode) != 0)
	{
		return tap(DB::connection('CMS')->table('ControlNum')->where('IdBU', 'LIKE', $myIdBU)->where('SVRID','=',$mySVRid)->where('Date', '=', $today)->select('Num') )->update(['Num' => DB::raw('Num+1') ])->first();
		//$notify = tap(Model::where('id',$params['id'])->select($fields))->update(['status' => 1  ])->first();
	}
	else
	{
		return "Server and Clinic code not match, please use assigned server to create a queue!";
	}
	
    }
    static public function genPatientCode()
    {
	//get date today
	$today = date('Y-m-d');
	//get server id
	$mySVRid = DB::connection('CMS')->select('SHOW VARIABLES LIKE "server_id" ')[0]->Value;
	//get session bu
	$myIdBU = session('userClinicCode');
	//check if date = todate
	$buDate =  DB::connection('CMS')->table('ControlNum')->where('IdBU', 'LIKE', $myIdBU)->where('SVRID','=',$mySVRid)->get(array('PDate'))[0]->PDate;
	if($buDate != $today)
	{
		DB::connection('CMS')->table('ControlNum')->where('IdBU', 'LIKE', $myIdBU)->where('SVRID','=',$mySVRid)->update(['PDate' => $today, 'PNum' => 0]);
	}
	//check if data available in table
	$numCode = DB::connection('CMS')->table('ControlNum')->where('IdBU', 'LIKE', $myIdBU)->where('SVRID','=',$mySVRid)->where('PDate', '=', $today)->get(array('PNum'));
	if(count($numCode) != 0)
	{
		return tap(DB::connection('CMS')->table('ControlNum')->where('IdBU', 'LIKE', $myIdBU)->where('SVRID','=',$mySVRid)->where('PDate', '=', $today)->select('PNum') )->update(['PNum' => DB::raw('PNum+1') ])->first();
		//$notify = tap(Model::where('id',$params['id'])->select($fields))->update(['status' => 1  ])->first();
	}
	else
	{
		return "Server and Clinic code not match, please use assigned server to create a queue!";
	}
	
    }
    static public function getMyDBID($kioskId = NULL)
    {
	$myServerId = DB::connection('CMS')->select('SHOW VARIABLES LIKE "server_id" ')[0]->Value;
	$dbArray = array(
		"AA" 		=> "1"
		,"AB"		=> "2"
		,"AC"	=> "3"
		,"AD"	=> "4"
		,"AE"		=> "5"
		,"AF"		=> "6"
		,"AG"	=> "7"
		,"AH"	=> "8"
		,"AI"		=> "9"
		,"AJ"		=> "10"
		,"AK"	=> "11"
		,"AL"		=> "12"
		,"AM"	=> "13"
		,"AN"	=> "14"
		,"AO"	=> "15"
		,"AP"		=> "16"
		,"AQ"	=> "17"
		,"AR"	=> "18"
		,"AS"		=> "19"
		,"AT"		=> "20"
		,"AU"	=> "21"
		,"AV"		=> "22"
		,"AW"	=> "23"
		,"AX"		=> "24"
		,"AY"		=> "25"
		,"AZ"		=> "26"
		,"BA"		=> "27"
		,"BB"	=> "28"
		,"BC"	=> "29"
		,"BD"	=> "30"
		,"BE"		=> "31"
		,"BF"		=> "32"
		,"BG"	=> "33"
		,"BH"	=> "34"
		,"BI"		=> "35"
		,"BJ"		=> "36"
		,"BK"	=> "37"
		,"BL"		=> "38"
		,"BM"	=> "39"
		,"BN"	=> "40"
		,"BO"	=> "41"
		,"BP"		=> "42"
		,"BQ"	=> "43"
		,"BR"	=> "44"
		,"BS"		=> "45"
		,"BT"		=> "46"
		,"BU"	=> "47"
		,"BV"		=> "48"
		,"BW"	=> "49"
		,"BX"		=> "50"
		,"BY"		=> "51"
		,"BZ"		=> "52"
		,"CA"	=> "53"
		,"CB"	=> "54"
		,"CC"	=> "55"
		,"CD"	=> "56"
		,"CE"		=> "57"
		,"CF"		=> "58"
		,"CG"	=> "59"
		,"CH"	=> "60"
		,"CI"		=> "61"
		,"CJ"		=> "62"
		,"CK"	=> "63"
		,"CL"		=> "64"
		,"CM"	=> "65"
		,"CN"	=> "66"
		,"CO"	=> "67"
		,"CP"		=> "68"
		,"CQ"	=> "69"
		,"CR"	=> "70"
		,"CS"	=> "71"
		,"CT"		=> "72"
		,"CU"	=> "73"
		,"CV"	=> "74"
		,"CW"	=> "75"
		,"CX"	=> "76"
		,"CY"		=> "77"
		,"CZ"	=> "78"
		,"DA"	=> "79"
		,"DB"	=> "80"
		,"DC"	=> "81"
		,"DD"	=> "82"
		,"DE"	=> "83"
		,"DF"		=> "84"
		,"DG"	=> "85"
		,"DH"	=> "86"
		,"DI"		=> "87"
		,"DJ"		=> "88"
		,"DK"	=> "89"
		,"DL"		=> "90"
		,"DM"	=> "91"
		,"DN"	=> "92"
		,"DO"	=> "93"
		,"DP"	=> "94"
		,"DQ"	=> "95"
		,"DR"	=> "96"
		,"DS"	=> "97"
		,"DT"		=> "98"
		,"DU"	=> "99"
		,"DV"	=> "100"
		,"DW"	=> "DTU"
		//,"DX"	=> "BAE"
		
	);
	
	return array_search($myServerId, $dbArray);
	
    }
}
