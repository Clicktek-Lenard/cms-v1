<?php
namespace App\Http\Controllers\hclab;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Http\Controllers\Controller;
use App\Models\hclab\HclabDB;

//use Aws\S3\S3Client;
//use League\Flysystem\AwsS3v3\AwsS3V3Adapter;
//use League\Flysystem\Filesystem;

class ItemMasterController extends Controller
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

   
	public function index()
	{
	die('This code should be run once only');
	/*
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$conn = oci_connect("hclab", "hclab", $cstr, 'AL32UTF8');

		$sql = "SELECT * FROM TEST_ITEM";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		$i =1;
		
		while (oci_fetch($stid))
		{
			$checkIfExist =  DB::connection('Eros')->table('ItemMaster')->where('Code', 'like', oci_result($stid, 'TI_CODE'))->get(array('*'));
			
			if(count($checkIfExist) != 0)
			{
				DB::connection('Eros')->table('ItemMaster')->where('Id', $checkIfExist[0]->Id)->update(['SystemFrom' => 'HCLAB']);
			
			}
			else
			{
				 DB::connection('Eros')->insert("INSERT INTO ItemMaster (`Code`, `Description`, `ShortName`, `LongName`, `Group`,  `SubGroup`, `OrderStatus`,`SystemFrom`, `Price`, `ItemStatus`) VALUES
					(
						'".oci_result($stid, 'TI_CODE')."'
						,'".htmlentities(oci_result($stid, 'TI_NAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."'
						,'".htmlentities(oci_result($stid, 'TI_PRINT_NAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."'
						,'".htmlentities(oci_result($stid, 'TI_LONG_NAME'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')."'
						,'".oci_result($stid, 'TI_TEST_GRP')."'
						,'".oci_result($stid, 'TI_CATEGORY')."'
						,'".oci_result($stid, 'TI_ORDER_ENABLE')."'
						,'HCLAB'
						,'0'
						,'Active'
					)
				");
			}
			
		}
		
		oci_close($conn);
	*/
	}
    
	public function show($id)
	{


	}

}
