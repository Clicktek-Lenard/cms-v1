<?php

namespace App\Http\Controllers\eros;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

class ItemReloadController extends Controller
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

    public function ErosItemMasterReload()
    {
	$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = erosprod))     )';
	$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
	
	$sql = "SELECT * FROM ITEM_MASTER";
	$stid = oci_parse($conn, $sql);
	oci_execute($stid);
	
	$iFound = DB::connection('Eros')->table('ItemMaster')->where('Code', oci_result($stid, 'IM_CODE') )->get(array('*'));
	
	while (oci_fetch($stid)) 
	{
		//echo oci_result($stid, 'IM_CODE');
		$iFound = DB::connection('Eros')->table('ItemMaster')->where('Code', oci_result($stid, 'IM_CODE') )->get(array('*'));
		
		if( count($iFound) != 0 ) //update ItemMaster Table in CMS
		{
			DB::connection('Eros')->table('ItemMaster')->where('Id', $iFound[0]->Id)
			->update([
				'Description' 		=> oci_result($stid, 'IM_DESCRIPTION')
				,'LISCode'			=> oci_result($stid, 'IM_LIS_CODES')
				,'OrderStatus'		=> 'Y'
				,'Price'			=> '0'
				,'ItemStatus'		=> 'Active'
				,'LinkType'			=> oci_result($stid, 'IM_TG')
				,'Type'			=> (oci_result($stid, 'IM_GROUP') === "PACK")?'Package':'Item'
				,'Group'			=> oci_result($stid, 'IM_GROUP')
				,'SubGroup'		=> oci_result($stid, 'IM_TG')
			]);
		
		}
		else
		{
			DB::connection('Eros')->table('ItemMaster')->insert(
				[[
					'Code'			=> oci_result($stid, 'IM_CODE')
					,'Description' 		=> oci_result($stid, 'IM_DESCRIPTION')
					,'LISCode'			=> oci_result($stid, 'IM_LIS_CODES')
					,'OrderStatus'		=> 'Y'
					,'Price'			=> '0'
					,'ItemStatus'		=> 'Active'
					,'LinkType'			=> oci_result($stid, 'IM_TG')
					,'Type'			=> (oci_result($stid, 'IM_GROUP') === "PACK")?'Package':'Item'
					,'Group'			=> oci_result($stid, 'IM_GROUP')
					,'SubGroup'		=> oci_result($stid, 'IM_TG')
				]]
			);	
		
		}
	
	}
	
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    
}
