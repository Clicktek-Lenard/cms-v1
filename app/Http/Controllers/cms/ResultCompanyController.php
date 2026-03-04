<?php
//
/*
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
*/

namespace App\Http\Controllers\cms;

use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Controller;


use DataTables;

class ResultCompanyController extends Controller
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
	// return route('erosserver.PatientList');
	$lCompa = DB::connection('Eros')->table('Company')->where('ResultUploading', 'LIKE', 'Yes')->get(array('Id', 'ErosCode', 'Name'));
	
	$sid = (isset($_GET['sid']))? $_GET['sid'] : 'ALL';
	
       return view('cms.resultCompanyList',  ['fCompany' =>$lCompa, 'sid' => $sid  ]);
	
    }
    
    public function edit($id)
    { 
	$lCompa = DB::connection('Eros')->table('Company')->where('ResultUploading', 'LIKE', 'Yes')->where('Id', '=', $id)->get(array('Id', 'Name'));
	if( count($lCompa) != 0)
	{
		return view('cms.resultsCompany',  ['compaName' => $lCompa[0]->Name, 'sid' => $id  ]);
	}
	else
	{
		return "Missing Param!";
	}
    }
    
     public function resultDeleted(Request $request)
    {
	$listOfDeleted = DB::connection('CMS')->table('Results')->where('Action', 'LIKE', 'Delete-%')->WhereNull('SourceAction')->get(array('Id','SourceId','SourceCode','Action'));
	foreach($listOfDeleted as $delete)
	{
		$action = explode(".", $delete->Action);
		$getPrev = DB::connection('CMS')->table('Results')->where('SourceId',  $delete->SourceId)->where('SourceCode', 'LIKE', $delete->SourceCode)->where('Action', 'LIKE', 'Upload-%')->where('Action', 'LIKE', '%.'.$action[1])
		->orderBy('Id', 'ASC')->get(array('Id','SourceId'));
		foreach($getPrev as $prev)
		{
			if( $prev->Id < $delete->Id )
			{
				DB::connection('CMS')->table('Results')->where('Id', $prev->Id)->update(['SourceAction' => 'Deleted']);
			}
		}
		DB::connection('CMS')->table('Results')->where('Id', $delete->Id)->update(['SourceAction' => 'Deleted']);
	}
    } 
    
     public function getCompanyList(Request $request)
    {
    
	$search_arr = $request->get('search');
	$searchValue = $search_arr['value'];
	
	$sid = $request->get('sid');

	if ($request->ajax()) {
		$model = $this->getCompany(array('fullname'=>$searchValue));
		return DataTables::of($model)->toJson();
	}
    
    }
    
     public  function getCompany($params = array())
    {
		$lCompa = DB::connection('Eros')->table('Company')
			->where(function($q) use ($params ) {
				$q->where('ResultUploading', 'LIKE', 'Yes');
				if( !empty($params['sid']) && $params['sid'] != "ALL" )
				{
					$q->where('Id', '=', $params['sid'] );
				}
			})
			->get(array('Id','Code','Name','ErosCode','ResultUploading'));

		$return =  array();
		
		foreach($lCompa as $data)
		{
			array_push($return, array(
				'Id'			=> $data->Id
				,'Code'		=> $data->Code
				,'Name'		=> $data->Name
				,'ErosCode'	=> $data->ErosCode
				,'ResultUploading' => $data->ResultUploading
			));
		
		}
		
		return $return;	
	
	}
	
	public function getPatientList(Request $request)
	{

		$search_arr = $request->get('search');
		$searchValue = $search_arr['value'];

		$sid = $request->get('sid');

		if ($request->ajax()) {
			$model = $this->getCompanyPatient(array('fullname'=>$searchValue, 'year' => '2025', 'sid'=> $sid));
			return DataTables::of($model)->toJson();
		}

	}
	
	public  function getCompanyPatient($params = array())
	{
		$lCompa = DB::connection('Eros')->table('Company')
			->where(function($q) use ($params ) {
				$q->where('ResultUploading', 'LIKE', 'Yes');
				if( !empty($params['sid']) && $params['sid'] != "ALL" )
				{
					$q->where('Id', '=', $params['sid'] );
				}
			})
			->get(array('Id','ErosCode'));
			
		$sCompa = "and IdCompany IN (";
		$x= 0;
		foreach($lCompa as $compa)
		{
			if($x == 0)
			{
				$sCompa .= "'".$compa->Id."'";
			}
			else
			{
				$sCompa .= ",'".$compa->Id."'";
			}
			$x++;
			
		}
		
		
		$sql = "SELECT tb1.`Id` as QID,  tb2.`Fullname` ,  tb2.`Id` as PID, tb2.`Code` as PCode , tb1.`Code` as QCode , tb1.`Date` as QDate , tb2.`DOB` as PDOB, tb3.`NameCompany`,tb2.`FullAddress`, tb2.`Email`, tb2.`Moblie`,
				tb2.`LastName`, tb2.`FirstName`, tb2.`MiddleName`, tb2.`Suffix`, tb2.`Gender`
		FROM `CMS`.`Queue` tb1
		INNER JOIN `Eros`.`Patient` tb2 on (tb1.`IdPatient` = tb2.`Id`)
		LEFT JOIN `CMS`.`Transactions` tb3 on (tb1.`Id` = tb3.`IdQueue`)
		WHERE   tb3.`PriceGroupItemPrice` LIKE 'Package' and  tb3.`TransactionType` IN ('APE', 'PEME') and tb1.`Status` >= 210 and tb1.`Status` <= 650  ";
		
		if(!empty($params['fullname']))
		{
			$sql = $sql . "and tb2.`Fullname` like  '%".strtoupper($params['fullname'])."%' ";
		}
		if(!empty($params['byId']))
		{
			$sql = $sql . "and tb1.`Id` =  '".$params['byId']."' ";
		}
		
		if( count($lCompa) != 0)
		{
			$sql .=  $sCompa.")";
		}
		$sql .= " GROUP BY tb1.`Id` ORDER BY tb1.`Date` DESC LIMIT 1000";
		
		$datas =  DB::connection('CMS')->select($sql);
		$return =  array();
		
		foreach($datas as $data)
		{
			$xrayImage = DB::connection('CMS')->table('Results')->where('IdQueue', '=', $data->QID)->where('SourceCode', 'like', 'XRAY')->where('Action', 'like', 'Upload-XRAY-image.%')->where('SourceAction','NOT LIKE', 'Deleted')->get(array('Id'));
			$xrayResult = DB::connection('CMS')->table('Results')->where('IdQueue', '=', $data->QID)->where('SourceCode', 'like', 'XRAY')->where('Action', 'like', 'Upload-XRAY.%')->where('SourceAction','NOT LIKE', 'Deleted')->get(array('Id'));
			$ecgImage = DB::connection('CMS')->table('Results')->where('IdQueue', '=', $data->QID)->where('SourceCode', 'like', 'ECG')->where('Action', 'like', 'Upload-ECG-image.%')->where('SourceAction','NOT LIKE', 'Deleted')->get(array('Id'));
			$ecgResult = DB::connection('CMS')->table('Results')->where('IdQueue', '=', $data->QID)->where('SourceCode', 'like', 'ECG')->where('Action', 'like', 'Upload-ECG.%')->where('SourceAction','NOT LIKE', 'Deleted')->get(array('Id'));
			$labResult = DB::connection('CMS')->table('Results')->where('IdQueue', '=', $data->QID)->where('SourceCode', 'like', 'LAB')->where('Action', 'like', 'Upload-LAB.%')->where('SourceAction','NOT LIKE', 'Deleted')->get(array('Id'));
		
			array_push($return, array(
				'id'			=> $data->QID
				,'FullName'	=> $data->Fullname
				,'or_no'		=> ''
				,'patient_id'	=> $data->PID
				,'trans_no'		=> $data->QCode
				,'order_date'	=> $data->QDate
				,'birthdate' 	=> date('M-d-Y', strtotime($data->PDOB))
				,'created_at'	=> $data->QDate
				,'Company'	=> $data->NameCompany
				,'Address'		=> $data->FullAddress
				,'City'		=> ''
				,'Email'		=> $data->Email
				,'Phone'		=> $data->Moblie
				,'xray_image'	=> (isset($xrayImage[0]->Id))?"Yes":''
				,'xray_result'	=> (isset($xrayResult[0]->Id))?"Yes":''
				,'ecg_image'	=> (isset($ecgImage[0]->Id))?"Yes":''
				,'ecg_result'	=> (isset($ecgResult[0]->Id))?"Yes":''
				,'lab_result'	=> (isset($labResult[0]->Id))?"Yes":''
				));
		
		}
		
		return $return;	
	
	}
	
	
	/*
	Part of Show function
	
	*/
	
	public  function getPatientServer($params = array())
   {
	$lCompa = DB::connection('Eros')->table('Company')
		->where(function($q) use ($params ) {
			$q->where('ResultUploading', 'LIKE', 'Yes');
			if( !empty($params['sid']) && $params['sid'] != "ALL" )
			{
				$q->where('Id', '=', $params['sid'] );
			}
		})
		->get(array('Id', 'ErosCode'));
		
		$sCompa = "and IdCompany IN (";
		$x= 0;
		foreach($lCompa as $compa)
		{
			if($x == 0)
			{
				$sCompa .= "'".$compa->Id."'";
			}
			else
			{
				$sCompa .= ",'".$compa->Id."'";
			}
			$x++;
			
		}
		/*
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.154.8)(PORT = 1521))  (CONNECT_DATA= (SID = erosprod)) )';
		$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
		
		$sql = "SELECT PM_PID,  PM_FULLNAME , BTH_NPI ,  BTH_PID , BTH_TRXNO , BTH_TRXDT , to_char(PM_DOB, 'yyyy-mm-dd') as PM_DOB, BTH_TRXDT, CD_NAME,CD_CODE, PM_ADDRESS, PM_CITY, PM_EMAIL, PM_MOBILENO,
				PM_LASTNAME, PM_FIRSTNAME, PM_MIDNAME, PM_SUFFIX, PM_GENDER,
				( select
				listagg(sub2.IM_DESCRIPTION,';') within group ( order by sub1.BTD_ITEM_CODE )
				from BILLING_TRX_DTL sub1
				join item_master sub2 ON (sub1.BTD_ITEM_CODE = sub2.IM_CODE)
				where BTD_TRXNO = billing_trx_hdr.BTH_TRXNO  ) as ITEMLIST
		FROM billing_trx_hdr
		INNER JOIN patient_master on bth_pid = pm_pid
		LEFT JOIN company_details on bth_company = company_details.cd_code
		WHERE  billing_trx_hdr.bth_billing_status LIKE 'P' AND ROWNUM <= 1000 ";
		//to_char(BTH_TRXDT, 'yyyy-mm-dd')  = '".date('Y-m-d')."' and 
		if(!empty($params['fullname']))
		{
			$sql = $sql . "and PM_FULLNAME like  '%".strtoupper($params['fullname'])."%' ";
		}
		if(!empty($params['byId']))
		{
			$sql = $sql . "and BTH_TRXNO =  '".$params['byId']."' ";
		}
		
		if( count($lCompa) != 0)
		{
			$sql .=  $sCompa.")";
		}
		$sql .= " ORDER BY billing_trx_hdr.BTH_TRXDT DESC";
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		$return =  array();
		while (oci_fetch($stid)) {
		//PM_FULLNAME like '%".$param."%'  AND 
		
			array_push($return, array(
				'id'			=> oci_result($stid, 'BTH_TRXNO')
				,'FullName'	=> oci_result($stid, 'PM_FULLNAME')
				,'or_no'		=> oci_result($stid, 'BTH_NPI')
				,'patient_id'	=> oci_result($stid, 'BTH_PID')
				,'trans_no'		=> oci_result($stid, 'BTH_TRXNO')
				,'order_date'	=> oci_result($stid, 'BTH_TRXDT')
				,'DOB' 		=> date('Y-m-d', strtotime(oci_result($stid, 'PM_DOB')))
				,'birthdate' 	=> date('M-d-Y', strtotime(oci_result($stid, 'PM_DOB')))
				,'created_at'	=> date('M-d-Y', strtotime(oci_result($stid, 'BTH_TRXDT')))
				,'Company'	=> oci_result($stid, 'CD_NAME')
				,'CompanyCode'	=> oci_result($stid, 'CD_CODE')
				,'Address'		=> oci_result($stid, 'PM_ADDRESS')
				,'City'		=> oci_result($stid, 'PM_CITY')
				,'Email'		=> oci_result($stid, 'PM_EMAIL')
				,'Phone'		=> oci_result($stid, 'PM_MOBILENO')
				,'ItemList'		=> oci_result($stid, 'ITEMLIST')
				));
				
		}
		oci_close($conn);
		*/
		$yearFrom = $params['year'].'-01-01';
		$yearTo = $params['year'].'-12-31';
		
		//DB::connection('oraTARe')->commit();
		$sql = "SELECT tb1.`Id` as QID,  tb2.`Fullname` ,  tb2.`Id` as PID, tb2.`Code` as PCode , tb1.`Code` as QCode , tb1.`Date` as QDate , tb2.`DOB` as PDOB, tb4.`ErosCode`,tb3.`NameCompany`,tb2.`FullAddress`, tb2.`Email`, tb2.`Moblie`,
				tb2.`LastName`, tb2.`FirstName`, tb2.`MiddleName`, tb2.`Suffix`, tb2.`Gender`,  tb1.`InputBy` as QInputBy ,
				( select
				GROUP_CONCAT(sub1.DescriptionItemPrice,';') 
				from `CMS`.`Transactions` sub1
				where sub1.`Id` = tb3.`Id`  
				group by sub1.`CodeItemPrice` ) as ITEMLIST
		FROM `CMS`.`Queue` tb1
		INNER JOIN `Eros`.`Patient` tb2 on (tb1.`IdPatient` = tb2.`Id`)
		LEFT JOIN `CMS`.`Transactions` tb3 on (tb1.`Id` = tb3.`IdQueue`)
		LEFT JOIN `Eros`.`Company` tb4 on (tb3.`IdCompany` = tb4.`Id`)
		WHERE  tb3.`PriceGroupItemPrice` LIKE 'Package' and tb3.`TransactionType` IN ('APE', 'PEME') and tb1.`Status` >= 210 and tb1.`Status` <= 650 and tb1.`Date` >= '".$yearFrom."'  and tb1.`Date` <= '".$yearTo."' ";
		
		if(!empty($params['fullname']))
		{
			$sql = $sql . "and tb2.`Fullname` like  '%".strtoupper($params['fullname'])."%' ";
		}
		if(!empty($params['byId']))
		{
			$sql = $sql . "and tb1.`Id` =  '".$params['byId']."' ";
		}
		
		if( count($lCompa) != 0)
		{
			$sql .=  $sCompa.")";
		}
		$sql .= " ORDER BY tb1.`Date` DESC LIMIT 1000";
		
		$return =  DB::connection('CMS')->select($sql);
		
		
		
		return $return;	
	
	}
	public function getResultsStatus($id, $name = NULL)
    {
	 $status = DB::connection('CMS')->table('ResultsStatus')->where('TransactionNo', $id)->get(array('StatusId', 'Description','UpdateBy','SystemTimeCreated'));
	if( is_null($name))
	{
		return (isset($status[0]->StatusId) && $status[0]->StatusId == '1')?'Error':'Accepted';
	}
	else
	{
		return $status;
	}
    }
	
	public  function show($id)
	{
		$datas = $this->getPatientServer(array('byId'=>$id, 'year'=> '2025'));
		$submitStatus = $this->getResultsStatus($datas[0]->QCode, 'withname');
		$disableButton = true;
		
		if(count($submitStatus) !=0 &&  $submitStatus[0]->StatusId ==1)
		{
			$disableButton = false;
		}
		return view('cms.erosPatientListServerEdit', ['disableButton' => $disableButton , 'submitStatus' => $submitStatus, 'datas' => $datas, 'postLink' => url(session('userBUCode').'/erosPatient/'.$id)]);    
	}
	
 
    
   
    
 
    
  
  
    
    

}
