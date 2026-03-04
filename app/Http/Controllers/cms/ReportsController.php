<?php

namespace App\Http\Controllers\cms;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\eros\ErosDB;

use App\Http\Controllers\Controller;

use App\Exports\cms\DailySalesExport;

 require_once dirname(__FILE__).'/../../../../vendor/Spreadsheet/Excel/Writer.php'; 

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
	//echo session('userDepartmentCode'); die();
	$userRole = json_decode(session('userRole'));
	$matchedRoles = [];
		foreach ($userRole as $role) {
			$user = trim($role->ldap_role);
			if (strpos($user, '-BRANCH') !== false) {
				$matchedRoles[] = $role->ldap_role;
			}
		}
		$transformedRoles = array_map(function($role) {
			if (preg_match('/\[(.*?)\-BRANCH\]/', $role, $matches)) {
				return $matches[1];
			}
			return $role; 
		}, $matchedRoles);
		
		
		
		if (!empty($transformedRoles)) {
			
			$clinicCode = $transformedRoles;
		}
		
	$clinic = ErosDB::getClinicData(NULL,NULL,$clinicCode);
	$clinicName = ErosDB::getClinicData(session('userClinicCode'));
		return view('cms.reports',['Clinics' => $clinic, 'clinicName' => $clinicName, 'ClinicCode' => $clinicCode]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
		$clinicRole = '[' . $request->input('Clinic') . '-' . 'BRANCH' . ']';
		$userRole = json_decode(session('userRole'));
		$matchedRoles = [];
		//dd($request->input('Clinic'));
		foreach ($userRole as $role) {
			$user = trim($role->ldap_role);
			if (trim($user) == trim($clinicRole)) {
				$clinicCode = $request->input('Clinic');
			}
			if ($request->input('Clinic') == "ALL") {
				if (strpos($user, '-BRANCH') !== false) {
					$matchedRoles[] = $role->ldap_role;
				}
			}
		}
		$transformedRoles = array_map(function($role) {
			if (preg_match('/\[(.*?)\-BRANCH\]/', $role, $matches)) {
				return $matches[1];
			}
			return $role; 
		}, $matchedRoles);
		if (!empty($transformedRoles)) {
			$clinicCode = $transformedRoles;
		}
	if($request->input('_repType')  == "bookkeeper" )
	{
		$ymd = date("FjYgia");
		
		$workbook = new \Spreadsheet_Excel_Writer();
		$workbook->send('BOOKKEEPER-DAILY-SALES-'.$ymd.'.xls');
		$workbook->setVersion(8);
		$worksheet = $workbook->addWorksheet('ALL');
		$worksheet->setInputEncoding('UTF-8');
		$numTextformat = $workbook->addFormat(array('setNumFormat'=>'@'));
		
		$number_format = $workbook->addFormat();
		$number_format->setNumFormat('0');
		
		// put text at the top and center it horizontally
		$format_top_center = $workbook->addFormat();
		$format_top_center->setAlign('top');
		$format_top_center->setAlign('center');
		$format_top_center->setVAlign('vjustify');
		$format_top_center->setVAlign('vcenter');
		$format_top_center->setBold (1);
		$format_top_center->setTextWrap(1);
		
		$worksheet->freezePanes(array(6, 7,NULL,NULL));
		
		$worksheet->setColumn(0,0,18); //BRANCH   A
		$worksheet->setColumn(1,1,12.57); //DATE B
		$worksheet->setColumn(2,2, 30); // TRX NO C
		$worksheet->setColumn(3,3,10);  // PATIENT ID D
		$worksheet->setColumn(4,4,20);  // OR NUMBER E
		$worksheet->setColumn(5,5,48.57); // CHARGE SLIP  F
  		$worksheet->setColumn(6,6,30);  // PATIENT NAME  G
		$worksheet->setColumn(7,7,10); // PHYSICIAN NAME   H
		$worksheet->setColumn(8,8,40);  // GUARANTOR NAME  I
		$worksheet->setColumn(9,9,40);  // ITEM/EXAMINATION J
		$worksheet->setColumn(10,10, 10.43);  // CATEGORY   K
		$worksheet->setColumn(11,11,14.57);  // PAYMENT TYPE    L
		$worksheet->setColumn(12,12,10.86);  // ITEM PRICE   M
		$worksheet->setColumn(13,13,21.14);  //DISC. TYPE'  N
		$worksheet->setColumn(14,14,8);  // 'LOYALTY POINTS   O
		$worksheet->setColumn(1,15,9.86);  //,'DISCOUNT'   P
		$worksheet->setColumn(16,16,15);  //,'MISCELLANEOUS FEE   Q
		$worksheet->setColumn(17,17,15);  //,'NET   R
		$worksheet->setColumn(18,18,15);  //,'CASH   S
		$worksheet->setColumn(19,19,15);  //,'CHECK   T
		$worksheet->setColumn(20,20,15);  //,'CREDIT / DEBIT CARD   U
		$worksheet->setColumn(21,21,15);  //,'GCASH   V
		$worksheet->setColumn(22,22,15); //ONLINE  W
		$worksheet->setColumn(23,23,15); //HMO/CORPORATE  X
		$worksheet->setColumn(24,24,15); //USER ID   Y
		
		
			$x = 5;
			
			$header = array('BRANCH', 'DATE', 'TRX NO', 'PATIENT ID', 'OR NUMBER', 'CHARGE PROCEDURE', 'PATIENT NAME', 'PHYSICIAN NAME', 'COMPANY NAME' ,'GUARANTOR NAME', 'PAYMENT TYPE', 'GROSS SALES', 'DISC. TYPE', 'LOYALTY POINTS', 'DISCOUNT',  'NET', 'CASH', 'CHECK', 'CREDIT / DEBIT CARD',  'GCASH', 'ONLINE', 'HMO/CORPORATE');
			$worksheet->writeRow($x++,0,$header, $format_top_center);
			
			$dateFrom = $request->input('dateFrom');
			$dateTo = $request->input('dateTo');
			
			$HPLUSQueData = DB::connection('CMS')->table('Queue')
				->leftjoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id')
				->leftjoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
				->leftjoin('Transactions', 'Queue.Id', '=', 'Transactions.IdQueue')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '640');
				if (!empty($clinicCode)) {
					$request->input('Clinic') != "ALL"
						? $HPLUSQueData->where('Queue.IdBU', $clinicCode)
						: $HPLUSQueData->whereIn('Queue.IdBU', $clinicCode);
				}
				//->where('Queue.Id', '2404')
				$HPLUSQueData = $HPLUSQueData
				->groupBy('Queue.Id')
				->orderBy('Queue.Code')
				->get(array('Queue.Id as QueId','Queue.Code as QCode', 'Queue.Date as QDate', 'Queue.IdPatient', 'Patient.FullName as PName', 'Patient.Code as PCode', 'Transactions.IdDoctor', 'Transactions.IdCompany', 'Queue.IdBU as ClinicCode' ));
			
			foreach($HPLUSQueData as $data)
			{
				$healthPlus = DB::connection('CMS')->table('Transactions')->where('IdQueue', $data->QueId)->where('IdCompany', '38251')->where('CodeItemPrice', 'LIKE', 'PCK231010')->where('Status', '>=','210')->where('Status', '<=','640')->get(array('*'));
				foreach($healthPlus as $hPlus)
				{
					$worksheet->writeRow($x++,0,
						array(
							 $data->ClinicCode // Branch
							,$data->QDate // date
							,$data->QCode // tranx no
							,$data->PCode //patient id
							, ''//$hPlus[0]->ORNum //or num
							,'HEALTH + BASIC 5 PACKAGE' // CHARGE SLIP N/A
							,$data->PName // patient name
							,$hPlus->NameDoctor  // physician name
							,$hPlus->NameCompany // company name
							,''  //$hPlus->NameGuarantor // company name
							,'Package Availed' //$hPlus->PaymentType// payment Type
							,'0'//number_format(floatval($hPlus[0]->Gross),2)   //  item amount to become Gross Item Amount
							,'0'//$hPlus[0]->DiscountType//DISC. TYPE
							,'' //LOYALTY POINTS N/A
							,'0' //number_format(floatval($hPlus[0]->Discount),2)    // Discount
							,'0' //number_format(floatval($hPlus[0]->Cash) + floatval($hPlus[0]->GCash) + floatval($hPlus[0]->Credit) + floatval($hPlus[0]->Online) + floatval($hPlus[0]->HMOCorp) + floatval($hPlus[0]->Cheque), 2) // $Inet //NET
							,'0' //number_format(floatval($hPlus[0]->Cash),2) //CASH
							,'0' //number_format(floatval($hPlus[0]->Cheque),2) //CHECK
							,'0' //number_format(floatval($hPlus[0]->Credit),2) //CREDIT / DEBIT CARD
							,'0' //number_format(floatval($hPlus[0]->GCash),2)  // GCASH
							,'0' //number_format(floatval($hPlus[0]->Online),2) //ONLINE
							,'0' //number_format(floatval($hPlus[0]->HMOCorp),2) // HMO/CORPORATE
						)
					);
				
				}
			}
			
			$QueData = DB::connection('CMS')->table('Queue')
				->leftjoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id')
				->leftjoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
				->leftjoin('Transactions', 'Queue.Id', '=', 'Transactions.IdQueue')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '640');
				//->where('Queue.IdBU',  session('userClinicCode'))
				if (!empty($clinicCode)) {
					$request->input('Clinic') != "ALL"
						? $QueData->where('Queue.IdBU', $clinicCode)
						: $QueData->whereIn('Queue.IdBU', $clinicCode);
				}
				//->where('Queue.Id', '2404')
				$QueData = $QueData->groupBy('Queue.Id', 'Transactions.IdDoctor', 'Transactions.IdCompany')
				->orderBy('Queue.Code')
				->get(array('Queue.Id as QueId','Queue.Code as QCode', 'Queue.Date as QDate', 'Queue.IdPatient', 'Patient.FullName as PName', 'Patient.Code as PCode', 'Transactions.IdDoctor', 'Transactions.IdCompany', 'Queue.IdBU as ClinicCode' ));
			
			foreach($QueData as $data)
			{
				
				// group into bill to
				$billGroup = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $data->QueId)->groupBy('BillTo', 'ORNum')->get(array('BillTo', 'ORNum'));
				foreach($billGroup as $billTo)
				{ 
					$trans = DB::connection('CMS')->select("
						SELECT
						( select sum(CurrentItemAmount) from
							(select tb1a.`CurrentItemAmount` 
							from CMS.`PaymentHistory` tb1a 
							join CMS.`Transactions` tb2a on (tb1a.`IdTransaction` = tb2a.`Id`)
							where
							tb1a.`Status` != '2' and
							tb1a.`IdQueue` = '".$data->QueId."' and
							tb1a.`BillTo` = '".$billTo->BillTo."'  and
							tb1a.`ORNum` = '".$billTo->ORNum."'  and
							tb2a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb2a.`IdCompany`  = '".$data->IdCompany."' 
							GROUP BY tb1a.`IdTransaction`
							) as tb1aA
						) as Gross
						,( select sum(CoverageAmount) from
							(select tb1a.`CoverageAmount` 
							from CMS.`PaymentHistory` tb1a  
							join CMS.`Transactions` tb2a on (tb1a.`IdTransaction` = tb2a.`Id`)
							where
							tb1a.`Status` != '2' and
							tb1a.`IdQueue` = '".$data->QueId."' and
							tb1a.`BillTo` = '".$billTo->BillTo."'  and
							tb1a.`ORNum` = '".$billTo->ORNum."'  and
							tb2a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb2a.`IdCompany`  = '".$data->IdCompany."' and
							tb1a.`PaymentType` LIKE 'Cash'
							) as tb1aA
						) as Cash
						,( select sum(CoverageAmount) from
							(select tb1a.`CoverageAmount` 
							from CMS.`PaymentHistory` tb1a  
							join CMS.`Transactions` tb2a on (tb1a.`IdTransaction` = tb2a.`Id`)
							where
							tb1a.`Status` != '2' and
							tb1a.`IdQueue` = '".$data->QueId."' and
							tb1a.`BillTo` = '".$billTo->BillTo."'  and
							tb1a.`ORNum` = '".$billTo->ORNum."'  and
							tb2a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb2a.`IdCompany`  = '".$data->IdCompany."' and
							tb1a.`PaymentType` LIKE 'GCash'
							) as tb1aA
						) as GCash
						,( select sum(CoverageAmount) from
							(select tb1a.`CoverageAmount` 
							from CMS.`PaymentHistory` tb1a  
							join CMS.`Transactions` tb2a on (tb1a.`IdTransaction` = tb2a.`Id`)
							where
							tb1a.`Status` != '2' and
							tb1a.`IdQueue` = '".$data->QueId."' and
							tb1a.`BillTo` = '".$billTo->BillTo."'  and
							tb1a.`ORNum` = '".$billTo->ORNum."'  and
							tb2a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb2a.`IdCompany`  = '".$data->IdCompany."' and
							tb1a.`PaymentType` LIKE 'Credit'
							) as tb1aA
						) as Credit
						,( select sum(CoverageAmount) from
							(select tb1a.`CoverageAmount` 
							from CMS.`PaymentHistory` tb1a  
							join CMS.`Transactions` tb2a on (tb1a.`IdTransaction` = tb2a.`Id`)
							where
							tb1a.`Status` != '2' and
							tb1a.`IdQueue` = '".$data->QueId."' and
							tb1a.`BillTo` = '".$billTo->BillTo."'  and
							tb1a.`ORNum` = '".$billTo->ORNum."'  and
							tb2a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb2a.`IdCompany`  = '".$data->IdCompany."' and
							tb1a.`PaymentType` LIKE 'Cheque'
							) as tb1aA
						) as Cheque
						,( select sum(CoverageAmount) from
							(select tb1a.`CoverageAmount` 
							from CMS.`PaymentHistory` tb1a  
							join CMS.`Transactions` tb2a on (tb1a.`IdTransaction` = tb2a.`Id`)
							where
							tb1a.`Status` != '2' and
							tb1a.`IdQueue` = '".$data->QueId."' and
							tb1a.`BillTo` = '".$billTo->BillTo."'  and
							tb1a.`ORNum` = '".$billTo->ORNum."'  and
							tb2a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb2a.`IdCompany`  = '".$data->IdCompany."' and
							tb1a.`PaymentType` LIKE 'Online'
							) as tb1aA
						) as Online
						,( select sum(CoverageAmount) from
							(select tb1a.`CoverageAmount` 
							from CMS.`PaymentHistory` tb1a  
							join CMS.`Transactions` tb2a on (tb1a.`IdTransaction` = tb2a.`Id`)
							where
							tb1a.`Status` != '2' and
							tb1a.`IdQueue` = '".$data->QueId."' and
							tb1a.`BillTo` = '".$billTo->BillTo."'  and
							tb1a.`ORNum` = '".$billTo->ORNum."'  and
							tb2a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb2a.`IdCompany`  = '".$data->IdCompany."' and
							tb1a.`PaymentType` LIKE 'Discount'
							) as tb1aA
						) as Discount
						,( select CONCAT_WS('/', tb1a.`CoverageType`)
							from CMS.`PaymentHistory` tb1a  
							join CMS.`Transactions` tb2a on (tb1a.`IdTransaction` = tb2a.`Id`)
							where
							tb1a.`Status` != '2' and
							tb1a.`IdQueue` = '".$data->QueId."' and
							tb1a.`BillTo` = '".$billTo->BillTo."'  and
							tb1a.`ORNum` = '".$billTo->ORNum."'  and
							tb2a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb2a.`IdCompany`  = '".$data->IdCompany."' and
							tb1a.`PaymentType` LIKE 'Discount'
							GROUP BY tb1a.`PaymentType`
						) as DiscountType
						,( select sum(CoverageAmount) from
							(select tb1a.`CoverageAmount` 
							from CMS.`PaymentHistory` tb1a  
							join CMS.`Transactions` tb2a on (tb1a.`IdTransaction` = tb2a.`Id`)
							where
							tb1a.`Status` != '2' and
							tb1a.`IdQueue` = '".$data->QueId."' and
							tb1a.`BillTo` = '".$billTo->BillTo."'  and
							tb1a.`ORNum` = '".$billTo->ORNum."'  and
							tb2a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb2a.`IdCompany`  = '".$data->IdCompany."' and
							tb1a.`ProviderType` IN('Corporate','HMO')  and
							tb1a.`PaymentType` LIKE 'CoPay'
							) as tb1aA
						) as HMOCorp
						,( select GROUP_CONCAT(tb1A.`ORNum` SEPARATOR '/') from (select tb1a.`ORNum`
							from CMS.`PaymentHistory` tb1a  
							join CMS.`Transactions` tb2a on (tb1a.`IdTransaction` = tb2a.`Id`)
							where
							tb1a.`Status` != '2' and
							tb1a.`IdQueue` = '".$data->QueId."' and
							tb1a.`BillTo` = '".$billTo->BillTo."'  and
							tb1a.`ORNum` = '".$billTo->ORNum."'  and
							tb2a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb2a.`IdCompany`  = '".$data->IdCompany."' and
							tb1a.`ORNum` NOT LIKE ''
							GROUP BY tb1a.`ORNum`) as tb1A
						) as ORNum
						,(select GROUP_CONCAT(tb1A.`NameCompany` SEPARATOR '/') from (select tb1a.`NameCompany` 
							from CMS.`Transactions` tb1a  
							where
							tb1a.`IdQueue` = '".$data->QueId."'  and
							tb1a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb1a.`IdCompany`  = '".$data->IdCompany."' 
							GROUP BY tb1a.`NameCompany`) as tb1A
						) as NameCompany
						,(select GROUP_CONCAT(tb1A.`Name` SEPARATOR '/') from (select tb3a.`Name`
							from CMS.`PaymentHistory` tb1a  
							join CMS.`Transactions` tb2a on (tb1a.`IdTransaction` = tb2a.`Id`)
							join Eros.`Company` tb3a on (tb1a.`BillTo` = tb3a.`Id`)
							where
							tb1a.`Status` != '2' and
							tb1a.`IdQueue` = '".$data->QueId."' and
							tb1a.`BillTo` = '".$billTo->BillTo."'  and
							tb2a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb2a.`IdCompany`  = '".$data->IdCompany."' 
							GROUP BY tb1a.`BillTo`) as tb1A
						)  as NameGuarantor
						,(select GROUP_CONCAT(tb1A.`NameDoctor` SEPARATOR '/') from (select tb1a.`NameDoctor` 
							from CMS.`Transactions` tb1a  
							where
							tb1a.`IdQueue` = '".$data->QueId."'  and
							tb1a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb1a.`IdCompany`  = '".$data->IdCompany."' 
							GROUP BY tb1a.`NameDoctor`) as tb1A
						) as NameDoctor
						,(select GROUP_CONCAT(tb1A.`PaymentType` SEPARATOR '/') from (select tb1a.`PaymentType`
							from CMS.`PaymentHistory` tb1a  
							join CMS.`Transactions` tb2a on (tb1a.`IdTransaction` = tb2a.`Id`)
							where
							tb1a.`Status` != '2' and
							tb1a.`IdQueue` = '".$data->QueId."' and
							tb1a.`BillTo` = '".$billTo->BillTo."'  and
							tb1a.`ORNum` = '".$billTo->ORNum."'  and
							tb2a.`IdDoctor` = '".$data->IdDoctor."'  and
							tb2a.`IdCompany`  = '".$data->IdCompany."' and
							tb1a.`PaymentType` IN('Cash','GCash','Credit','Cheque','Online')
							GROUP BY tb1a.`PaymentType`) as tb1A
						) as PaymentType
						
					");
					
					if(number_format(floatval($trans[0]->Gross),2) != 0)
					{
						$worksheet->writeRow($x++,0,
							array(
								 $data->ClinicCode // Branch
								,$data->QDate // date
								,$data->QCode // tranx no
								,$data->PCode //patient id
								,$trans[0]->ORNum //or num
								,'' // CHARGE SLIP N/A
								,$data->PName // patient name
								,$trans[0]->NameDoctor  // physician name
								,$trans[0]->NameCompany // company name
								,$trans[0]->NameGuarantor // company name
								,$trans[0]->PaymentType// payment Type
								,number_format(floatval($trans[0]->Gross),2)   //  item amount to become Gross Item Amount
								,$trans[0]->DiscountType//DISC. TYPE
								,'' //LOYALTY POINTS N/A
								,number_format(floatval($trans[0]->Discount),2)    // Discount
								,number_format(floatval($trans[0]->Cash) + floatval($trans[0]->GCash) + floatval($trans[0]->Credit) + floatval($trans[0]->Online) + floatval($trans[0]->HMOCorp) + floatval($trans[0]->Cheque), 2) // $Inet //NET
								,number_format(floatval($trans[0]->Cash),2) //CASH
								,number_format(floatval($trans[0]->Cheque),2) //CHECK
								,number_format(floatval($trans[0]->Credit),2) //CREDIT / DEBIT CARD
								,number_format(floatval($trans[0]->GCash),2)  // GCASH
								,number_format(floatval($trans[0]->Online),2) //ONLINE
								,number_format(floatval($trans[0]->HMOCorp),2) // HMO/CORPORATE
							)
						);
					}
				}
			}
		$workbook->close();
		die();
	
	}
	######################## perItem ############
	else if($request->input('_repType')  == "perItem" )
	{
		$ymd = date("FjYgia");
		$workbook = new \Spreadsheet_Excel_Writer();
		$workbook->send('ALL-DAILY-SALES-'.$ymd.'.xls');
		$workbook->setVersion(8);
		$worksheet = $workbook->addWorksheet('ALL');
		$worksheet->setInputEncoding('UTF-8');
		$numTextformat = $workbook->addFormat(array('setNumFormat'=>'@'));
		
		$number_format = $workbook->addFormat();
		$number_format->setNumFormat('0');
		
		// put text at the top and center it horizontally
		$format_top_center = $workbook->addFormat();
		$format_top_center->setAlign('top');
		$format_top_center->setAlign('center');
		$format_top_center->setVAlign('vjustify');
		$format_top_center->setVAlign('vcenter');
		$format_top_center->setBold (1);
		$format_top_center->setTextWrap(1);

		$worksheet->freezePanes(array(7, 7,NULL,NULL));
		
		$worksheet->setColumn(0,0,20); //BRANCH   NAME A
		$worksheet->setColumn(1,1,12); //BRANCH CODE  B
		$worksheet->setColumn(2,2,12.57); //DATE C
		$worksheet->setColumn(3,3, 30); // TRX NO D
		$worksheet->setColumn(4,4,15);  // PATIENT ID E
		$worksheet->setColumn(5,5,15);  // OR NUMBER F
		$worksheet->setColumn(6,6,30);  // PATIENT NAME  G
		$worksheet->setColumn(7,7,30); // PHYSICIAN NAME   H
		$worksheet->setColumn(8,8,15); // PHYSICIAN CODE   I
		$worksheet->setColumn(9,9,40);  // COMPANY NAME  J
		$worksheet->setColumn(10,10,15);  // COMPANY CODE  K
		$worksheet->setColumn(11,11,40);  // GUARANTOR NAME  L
		$worksheet->setColumn(12,12,15);  // GUARANTOR CODE  M
		$worksheet->setColumn(13,13,40);  // ITEM/EXAMINATION N
		$worksheet->setColumn(14,14,15);  // ITEM/EXAMINATION CODE O
		$worksheet->setColumn(15,15, 10.43);  // CATEGORY   P
		$worksheet->setColumn(16,16,14.57);  // PAYMENT TYPE    Q
		$worksheet->setColumn(17,17,10.86);  // ITEM PRICE   R
		$worksheet->setColumn(18,18,21.14);  //DISC. TYPE'  S
		$worksheet->setColumn(19,19,9.86);  //,'DISCOUNT'   T
		$worksheet->setColumn(20,20,15);  //,'NET   U
		$worksheet->setColumn(21,21,15);  //,'CASH   V
		$worksheet->setColumn(22,22,15);  //,'CHECK   W
		$worksheet->setColumn(23,23,15);  //,'CREDIT / DEBIT CARD   X
		$worksheet->setColumn(24,24,15);  //,'GCASH   Y
		$worksheet->setColumn(25,25,15); //ONLINE  Z
		$worksheet->setColumn(26,26,15); //COPAY AB
		$worksheet->setColumn(27,27,15); //HMO/CORPORATE  AC
		$worksheet->setColumn(28,28,15); //TRANSACTION TYPE AD
		$worksheet->setColumn(29,29,15); //USER ID   AE
		
		
			$x = 6;
			
			$header = array('BRANCH NAME', 'BRANCH CODE', 'DATE', 'TRX NO', 'PATIENT ID', 'OR NUMBER',  'PATIENT NAME', 'PHYSICIAN NAME', 'PHYSICIAN CODE', 'COMPANY NAME', 'COMPANY CODE', 'GUARANTOR NAME', 'GUARANTOR CODE', 'ITEM/EXAMINATION', 'ITEM/EXAMINATION CODE', 'CATEGORY', 'PAYMENT TYPE', 'ITEM GROSS', 'DISC. TYPE',  'DISCOUNT',  'ITEM NET', 'CASH', 'CHECK', 'CREDIT / DEBIT CARD',  'GCASH', 'ONLINE', 'COPAY', 'HMO/CORPORATE', 'TRANSACTION TYPE', 'USER ID' );
			$worksheet->writeRow($x++,0,$header, $format_top_center);
			
			 
			$dateFrom = $request->input('dateFrom');
			$dateTo = $request->input('dateTo');
			// remove due to ante-date $fromDate = date('Y-m-d H:i:s', strtotime($dateFrom));
			//remove due to ante-date  $Todate = date('Y-m-d H:i:s', strtotime($dateTo));
			// dd($fromDate);
			$QueData = DB::connection('CMS')->table('Queue')
				->leftjoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id')
				->leftjoin('Eros.BusinessUnits', 'Queue.IdBU', '=', 'Eros.BusinessUnits.Code')
				->leftjoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
				->leftjoin('Transactions', 'Queue.Id', '=', 'Transactions.IdQueue')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '640');
				//->where('Queue.IdBU',  session('userClinicCode'))
				if (!empty($clinicCode)) {
					$request->input('Clinic') != "ALL"
						? $QueData->where('Queue.IdBU', $clinicCode)
						: $QueData->whereIn('Queue.IdBU', $clinicCode);
				}
				$QueData = $QueData->groupBy('Queue.Id')
				->orderBy('Queue.Code')
				->get(array('Queue.Id as QueId','Queue.Code as QCode', 'Queue.Date as QDate', 'Queue.IdPatient', 'Patient.FullName as PName', 'Patient.Code as PCode', 'Transactions.TransactionType as Ttype', 'Queue.IdBU as ClinicCode', 'Eros.BusinessUnits.Description as ClinicName'));
			
			$grossAmount = 0;
			$discountAmount = 0;
			$netAmount = 0;
			$cash = 0;
			$Gcash = 0;
			$credit = 0;
			$cheque = 0;
			$online = 0;
			$hmo = 0;
			$type = '';

			foreach($QueData as $data)
			{
				$healthPlus = DB::connection('CMS')->table('Transactions')
				->leftjoin('Eros.Physician', 'Transactions.IdDoctor', '=', 'Eros.Physician.Id')
				->leftjoin('Eros.Company', 'Transactions.IdCompany', '=', 'Eros.Company.Id')
				->where('IdQueue', $data->QueId)->where('Transactions.IdCompany', '38251')->where('Transactions.CodeItemPrice', 'LIKE', 'PCK231010')->where('Transactions.Status', '>=','210')->where('Transactions.Status', '<=','640')->get(array('Transactions.*','Eros.Physician.Code as CodeDoctor', 'Eros.Company.Code as CodeCompany'));
				foreach($healthPlus as $hPlus)
				{ 
					$worksheet->writeRow($x++,0,array(
						 $data->ClinicName // Branch Name
						,$data->ClinicCode // Branch Code
						,$data->QDate // date
						,$data->QCode // tranx no
						,$data->PCode //patient id
						,'' //or num
						,$data->PName // patient name
						,$hPlus->NameDoctor  // physician name
						,$hPlus->CodeDoctor  // physician code
						,$hPlus->NameCompany // company name
						,$hPlus->CodeCompany // company code
						,'NWD HEALTH +' // guarantor name
						,'NWD003507' // guarantor code
						,'HEALTH + BASIC 5 PACKAGE' // item name / item description
						,'PCK231010'
						,'Package'  // item category
						,'Package Availed' //($hisT->CoverageAmount == 0)? '':$hisT->PaymentType  // payment Type
						,'0'  //  item amount to become Gross Item Amount
						,'0'//($hisT->PaymentType == "Discount" && $hisT->CoverageAmount != 0 )? $hisT->CoverageType: ''  //DISC. TYPE
						,'0' //$discount  //($hisT->PaymentType == "Discount")?$hisT->CoverageAmount:0   // Discount
						,'0' //$net  //$Inet //NET
						,'0' //$Icash //CASH
						,'0' //$Icheque //CHECK
						,'0' //$Icredit //CREDIT / DEBIT CARD
						,'0' //$Igcash // GCASH
						,'0' //$Ionline //ONLINE
						,'0' //$Ihmo // HMO
						,'0' //($hisT->ProviderType == "PATIENT")? '' : $hisT->ProviderType // HMO/CORPORATE
						,$data->Ttype
						,$hPlus->InputBy  //USER ID
						
						//,$hisT->CodeItemPrice
						//,$hisT->ProviderType
					));
				}
			
			
			
				// group into bill to
				$billGroup = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $data->QueId)->groupBy('BillTo')->get(array('BillTo'));
				
				
				foreach($billGroup as $billTo)
				{ 
					$trans = DB::connection('CMS')->select("
						SELECT 
						    tb2.`CodeItemPrice`
						   ,tb2.`DescriptionItemPrice`
						   ,tb2.`CodeItemPrice`
						   ,tb2.`NameCompany`
						   ,tb2.`TransactionType`
						   ,'1' as ItemQty
						   ,tb1.ProviderType
						   ,tb1.`CurrentItemAmount` as CurrentAmount
						   ,tb1.CoverageType
						   ,tb1.`CoverageAmount`
						   ,tb1.PaymentType
						   ,tb1.ORNum
						   ,tb2.NameDoctor as Dname
						   ,tb2.PriceGroupItemPrice
						   ,tb1.InputBy as BilledBy
						   ,tb1.BillTo
						   ,tb1.IdTransaction
						   ,tb1.DiscType
						   ,tb3.`Code` as CodeCampany
						   ,tb4.`Code` as CodeDoctor
						      FROM 
						    `PaymentHistory` tb1
						    JOIN `Transactions` tb2 ON (tb1.`IdTransaction` = tb2.`Id`)
						    LEFT JOIN `Eros`.`Company` tb3 ON (tb2.`IdCompany` = tb3.`Id`)
						    LEFT JOIN `Eros`.`Physician` tb4 ON (tb2.`IdDoctor` = tb4.`Id`)
						WHERE tb1.IdQueue  = '".$data->QueId."' and tb1.`BillTo` =  '".$billTo->BillTo."'  
					");
					//and tb1.`PaymentType` NOT LIKE 'Discount'
					//echo count($trans);
					//echo "<pre>";
					//print_r($trans);
					//echo "</pre>";
					//die();
					
					$pType  = '';
					$transIdCheck = 0;
					foreach($trans as $hisT)
					{
					
						$Guarantor = ($billTo->BillTo == 0)?'': DB::connection('Eros')->table('Company')->where('Id', $billTo->BillTo)->get(array('Code','Name'));
						$GuarantorCode = ($billTo->BillTo == 0)?'':$Guarantor[0]->Code;
						$GuarantorName = ($billTo->BillTo == 0)?'':$Guarantor[0]->Name;
						$itemGroupNew = DB::connection('Eros')->table('ItemMaster')->where('Code', $hisT->CodeItemPrice)->get(array('DepartmentGroup')); 
						$itemGroupOld = DB::connection('Eros')->table('ItemMaster')->where('OldCode', $hisT->CodeItemPrice)->get(array('DepartmentGroup')); 
						$iDiscountType = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $data->QueId)->get(['CoverageType']);
						
						if( count($itemGroupNew) != 0)
						{
							$itemGroup = $itemGroupNew[0]->DepartmentGroup;
						}
						elseif ( count($itemGroupOld) != 0)
						{
								$itemGroup = $itemGroupOld[0]->DepartmentGroup;
						}
						
						$category = ($hisT->PriceGroupItemPrice != "Item")?$hisT->PriceGroupItemPrice : $itemGroup;
						
						$grossAmount += $hisT->CoverageAmount;
						$Inet = 0; $Icash = 0; $Igcash = 0; $Icredit = 0; $Icheque = 0; $Ionline = 0; $Ihmo = 0; $Itransactiontype = '';
					
						
						if ($hisT->CoverageAmount != 0 && $hisT->PaymentType != "Discount") {
							$Inet = $hisT->CoverageAmount;
							$netAmount += $hisT->CoverageAmount;
							//$iDiscountTypes = $hisT->CoverageType;
							switch ($hisT->PaymentType) {
								case "Cash":
									$pType = 'Cash';
									$cash += $hisT->CoverageAmount;
									$Icash = $hisT->CoverageAmount;
									break;
								case "GCash":
									$pType = 'GCash';
									$Gcash += $hisT->CoverageAmount;
									$Igcash = $hisT->CoverageAmount;
									break;
								case "Credit":
									$pType = 'Credit';
									$credit += $hisT->CoverageAmount;
									$Icredit = $hisT->CoverageAmount;
									break;
								case "Cheque":
									$pType = 'Cheque';
									$cheque += $hisT->CoverageAmount;
									$Icheque = $hisT->CoverageAmount;
									break;
								case "Online":
									$pType = 'Online';
									$online += $hisT->CoverageAmount;
									$Ionline = $hisT->CoverageAmount;
									break;
								case "CoPay":
									$pType = 'Charge';
									$hmo += $hisT->CoverageAmount;
									$Ihmo = $hisT->CoverageAmount;
									break;
								default:
									$pType = 'Unknown';
									break;
							}
						}
					
						if($hisT->CoverageAmount != 0 && $hisT->PaymentType == "Discount" )
						{ 
							$discountAmount += $hisT->CoverageAmount;
							$typeofPayment = DB::connection('CMS')->table('PaymentHistory')->whereIn('PaymentType', ['Cash', 'Gcash', 'Credit', 'Cheque', 'Online', 'CoPay'])->where('IdQueue', $data->QueId)->get();
							if($typeofPayment[0]->PaymentType == 'CoPay')
							{
								$typeofPayment[0]->PaymentType = 'Charge';
							}
							$pType = (!empty($typeofPayment[0]->PaymentType))? $typeofPayment[0]->PaymentType : '';	
						}					
						// Gross Sale display
						if(  $transIdCheck == $hisT->IdTransaction )
						{	
							$grossItemSales =  0;//($hisT->PaymentType == "Discount") ? 0 : $hisT->CurrentAmount;
							$transIdCheck =  $hisT->IdTransaction;
							$category =  "";
							$itemDescription = "";
							$itemCode = "";
							$discount = 0;
							$net =0;
						}
						else
						{
							$grossItemSales =  $hisT->CurrentAmount;
							$transIdCheck =  $hisT->IdTransaction;
							$itemDescription = $hisT->DescriptionItemPrice;
							$itemCode = $hisT->CodeItemPrice;
							$discount = ($hisT->DiscType == 0 || $hisT->DiscType == "" )? 0 : $hisT->DiscType;
							$net = ($hisT->DiscType == 0 || $hisT->DiscType == "" )? $grossItemSales : ($grossItemSales - $hisT->DiscType);
						}

						########For Discount Type#####
						$iDiscountTypes = ($hisT->PaymentType == 'Discount')? '':$iDiscountType[0]->CoverageType;
						if($iDiscountTypes == 'HMO' ||  $iDiscountTypes == 'Corporate' || $iDiscountTypes == 'PATIENT' || $iDiscountTypes == '')
						{
							$iDiscountTypes = 'NONE';
						}
						$discountDiscription = DB::connection('Eros')->table('DiscountType')
						->where('Description', $hisT->CoverageType)->get(array('Description'));
						$discountType = '';
						foreach($discountDiscription as $dType)
						{
							$iDiscountType = DB::connection('CMS')->table('PaymentHistory')->where('CoverageType', $dType->Description)->where('IdQueue', $data->QueId)->get(['CoverageType']);
							$discountType = (!empty($iDiscountType)? $iDiscountType[0]->CoverageType : '');		
						}
						#######End Discount Type######

						$worksheet->writeRow($x,0,array(
							 $data->ClinicName // Branch Name
							,$data->ClinicCode // Branch Code
							,$data->QDate // date
							,$data->QCode // tranx no
							,$data->PCode //patient id
							,$hisT->ORNum //or num
							,$data->PName // patient name
							,$hisT->Dname  // physician name
							,$hisT->CodeDoctor// physician code
							,$hisT->NameCompany // company name
							,$hisT->CodeCampany // company code
							,$GuarantorName // guarantor name
							,$GuarantorCode // guarantor code
							,$itemDescription // item name / item description
							,$itemCode // item name / item description
							,$category  // item category
							,$pType // payment Type
							,$grossItemSales  //  item amount to become Gross Item Amount
							,(!empty($discountType))? $discountType: $iDiscountTypes// DISC. TYPE
							, $discount  //($hisT->PaymentType == "Discount")?$hisT->CoverageAmount:0   // Discount
							,$net  //$Inet //NET
							,$Icash //CASH
							,$Icheque //CHECK
							,$Icredit //CREDIT / DEBIT CARD
							,$Igcash // GCASH
							,$Ionline //ONLINE
							,$Ihmo // HMO
							,($hisT->ProviderType == "PATIENT")? '' : $hisT->ProviderType // HMO/CORPORATE
							,$data->Ttype
							,$hisT->BilledBy  //USER ID
							
							//,$hisT->CodeItemPrice
							//,$hisT->ProviderType
							
							
						));
						
						
						$x++;
					}
				}
			}
			$TotalPatient = DB::connection('CMS')->table('Queue')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '640');
				if (!empty($clinicCode)) {
					$request->input('Clinic') != "ALL"
						? $TotalPatient->where('Queue.IdBU', $clinicCode)
						: $TotalPatient->whereIn('Queue.IdBU', $clinicCode);
				}
				$TotalPatient = $TotalPatient->groupBy('Queue.IdPatient')
				->get(array('Queue.IdPatient'));
			
			$worksheet->writeRow(0,0,array('Total Patient Count', count($TotalPatient), '', 'Cash Amount', number_format($cash, 2,'.', ',')));
			$worksheet->writeRow(1,0,array('Total Gross Amount', number_format($grossAmount, 2,'.', ','), '', 'G-Cash Amount' , number_format($Gcash, 2,'.', ',')));
			$worksheet->writeRow(2,0,array('Total Discount Amount', number_format($discountAmount, 2,'.', ',') , '', 'Credit Amount', number_format($credit, 2,'.', ',')));
			$worksheet->writeRow(3,0,array('Total Net Amount', number_format($netAmount, 2,'.', ','), '', 'Online Amount', number_format($online, 2,'.', ',')));
			$worksheet->writeRow(4,0,array('',  '','', 'Cheque Amount', number_format($cheque, 2,'.', ',')));
			$worksheet->writeRow(5,0,array('Date and Time:', date('m/d/Y H:i:s', strtotime($dateFrom)). ' - ' . date('m/d/Y H:i:s', strtotime($dateTo)),'', 'CoPay Amount', number_format($hmo, 2,'.', ',')));
			$workbook->close();
			die();
	}
######################### Summary############################################
	else if($request->input('_repType')  == "summary")
	{ 

		$x = 0;
		
			$ymd = date("FjYgia");
			
			$workbook = new \Spreadsheet_Excel_Writer();
			$workbook->send('DAILY-SALES-SUMMARY-'.$ymd.'.xls');
			$workbook->setVersion(8);
			$worksheet = $workbook->addWorksheet('ALL');
			$worksheet->setInputEncoding('UTF-8');
			$numTextformat = $workbook->addFormat(array('setNumFormat'=>'@'));
			
			$number_format = $workbook->addFormat();
			$number_format->setNumFormat('0');
			
	
			$format_top_center = $workbook->addFormat();
			$format_top_center->setAlign('top');
			$format_top_center->setAlign('center');
			$format_top_center->setVAlign('vjustify');
			$format_top_center->setVAlign('vcenter');
			$format_top_center->setBold (1);
			$format_top_center->setTextWrap(1);
			
			$worksheet->freezePanes(array(2, 6,NULL,NULL));
			
			$header = array('', 'MONTH TO DATE', '', 'FORECAST', 'TARGET', 'VARIANCE', '1', '', '2','', '3','','4','','5','','6','','7','','8','','9','','10','','11','','12','','13','','14','','15','','16','','17','','18','','19','','20','','21','','22','','23','','24','','25','','26','','27','','28','','29','','30','','31','' );
			$worksheet->writeRow($x++,0,$header, $format_top_center);
			
			$subHeader = array('NET','Census','Revenue','','','','Census','Revenue','Census',' Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue','Census','Revenue' );
			$worksheet->writeRow($x++,0,$subHeader, $format_top_center);
			
			$worksheet->setColumn(0,0,25); //MONTH
			$worksheet->setColumn(1,1,12); //CENSUS
			$worksheet->setColumn(2,2, 12); //Revenue
			$worksheet->setColumn(3,3,15);  // FORECAST
			$worksheet->setColumn(4,4,15);  // TARGET
			$worksheet->setColumn(5,5,15);  // VARIANCE
			
			
			$worksheet->mergeCells( 0, 1,0, 2); // merge MONTH TO DATE
			$worksheet->mergeCells( 0, 6,0, 7); // 1
			$worksheet->mergeCells( 0, 8,0, 9); // 2
			$worksheet->mergeCells( 0, 10,0, 11); // 3
			$worksheet->mergeCells( 0, 12,0, 13); // 4
			$worksheet->mergeCells( 0, 14,0, 15); // 5
			$worksheet->mergeCells( 0, 16,0, 17); // 6
			$worksheet->mergeCells( 0, 18,0, 19); // 7
			$worksheet->mergeCells( 0, 20,0, 21); // 8
			$worksheet->mergeCells( 0, 22,0, 23); // 9
			$worksheet->mergeCells( 0, 24,0, 25); // 10
			$worksheet->mergeCells( 0, 26,0, 27); // 11
			$worksheet->mergeCells( 0, 28,0, 29); // 12
			$worksheet->mergeCells( 0, 30,0, 31); // 13
			$worksheet->mergeCells( 0, 32,0, 33); // 14
			$worksheet->mergeCells( 0, 34,0, 35); // 15
			$worksheet->mergeCells( 0, 36,0, 37); // 16
			$worksheet->mergeCells( 0, 38,0, 39); // 17
			$worksheet->mergeCells( 0, 40,0, 41); // 18
			$worksheet->mergeCells( 0, 42,0, 43); // 19
			$worksheet->mergeCells( 0, 44,0, 45); // 20
			$worksheet->mergeCells( 0, 46,0, 47); // 21
			$worksheet->mergeCells( 0, 48,0, 49); // 22
			$worksheet->mergeCells( 0, 50,0, 51); // 23
			$worksheet->mergeCells( 0, 52,0, 53); // 24
			$worksheet->mergeCells( 0, 54,0, 55); // 25
			$worksheet->mergeCells( 0, 56,0, 57); // 26
			$worksheet->mergeCells( 0, 58,0, 59); // 28
			$worksheet->mergeCells( 0, 60,0, 61); // 29
			$worksheet->mergeCells( 0, 62,0, 63); // 30
			$worksheet->mergeCells( 0, 64,0, 65); // 31
			$worksheet->mergeCells( 0, 66,0, 67); // 32
			
			 
			$dateFrom = $request->input('dateFrom');
			$dateTo = $request->input('dateTo');
			
		//	$worksheet->writeRow($x++,0,array(date('F', strtotime($dateFrom))), $format_top_center);
		//	$worksheet->writeRow($x++,0,array('Cash'), $format_top_center);
			////
			$labCountGtotal['01'] = $labCountGtotal['02'] = $labCountGtotal['03'] = $labCountGtotal['04'] = $labCountGtotal['05'] = $labCountGtotal['06'] = $labCountGtotal['07'] = $labCountGtotal['08'] = $labCountGtotal['09'] = $labCountGtotal['10'] = $labCountGtotal['11'] = $labCountGtotal['12'] = $labCountGtotal['13'] = $labCountGtotal['14'] = $labCountGtotal['15'] = $labCountGtotal['16'] = $labCountGtotal['17'] = $labCountGtotal['18'] = $labCountGtotal['19'] = $labCountGtotal['20'] = $labCountGtotal['21'] = $labCountGtotal['22'] = $labCountGtotal['23']= $labCountGtotal['24']= $labCountGtotal['25']= $labCountGtotal['26']= $labCountGtotal['27']= $labCountGtotal['28']= $labCountGtotal['29']= $labCountGtotal['30']= $labCountGtotal['31']="";
			$radCountGtotal['01'] = $radCountGtotal['02'] = $radCountGtotal['03'] = $radCountGtotal['04'] = $radCountGtotal['05'] = $radCountGtotal['06'] = $radCountGtotal['07'] = $radCountGtotal['08'] = $radCountGtotal['09'] = $radCountGtotal['10'] = $radCountGtotal['11'] = $radCountGtotal['12'] = $radCountGtotal['13'] = $radCountGtotal['14'] = $radCountGtotal['15'] = $radCountGtotal['16'] = $radCountGtotal['17'] = $radCountGtotal['18'] = $radCountGtotal['19'] = $radCountGtotal['20'] = $radCountGtotal['21'] = $radCountGtotal['22'] = $radCountGtotal['23']= $radCountGtotal['24']= $radCountGtotal['25']= $radCountGtotal['26']= $radCountGtotal['27']= $radCountGtotal['28']= $radCountGtotal['29']= $radCountGtotal['30']= $radCountGtotal['31']="";
			$conCountGtotal['01'] = $conCountGtotal['02'] = $conCountGtotal['03'] = $conCountGtotal['04'] = $conCountGtotal['05'] = $conCountGtotal['06'] = $conCountGtotal['07'] = $conCountGtotal['08'] = $conCountGtotal['09'] = $conCountGtotal['10'] = $conCountGtotal['11'] = $conCountGtotal['12'] = $conCountGtotal['13'] = $conCountGtotal['14'] = $conCountGtotal['15'] = $conCountGtotal['16'] = $conCountGtotal['17'] = $conCountGtotal['18'] = $conCountGtotal['19'] = $conCountGtotal['20'] = $conCountGtotal['21'] = $conCountGtotal['22'] = $conCountGtotal['23']= $conCountGtotal['24']= $conCountGtotal['25']= $conCountGtotal['26']= $conCountGtotal['27']= $conCountGtotal['28']= $conCountGtotal['29']= $conCountGtotal['30']= $conCountGtotal['31']="";
			$pckCountGtotal['01'] = $pckCountGtotal['02'] = $pckCountGtotal['03'] = $pckCountGtotal['04'] = $pckCountGtotal['05'] = $pckCountGtotal['06'] = $pckCountGtotal['07'] = $pckCountGtotal['08'] = $pckCountGtotal['09'] = $pckCountGtotal['10'] = $pckCountGtotal['11'] = $pckCountGtotal['12'] = $pckCountGtotal['13'] = $pckCountGtotal['14'] = $pckCountGtotal['15'] = $pckCountGtotal['16'] = $pckCountGtotal['17'] = $pckCountGtotal['18'] = $pckCountGtotal['19'] = $pckCountGtotal['20'] = $pckCountGtotal['21'] = $pckCountGtotal['22'] = $pckCountGtotal['23']= $pckCountGtotal['24']= $pckCountGtotal['25']= $pckCountGtotal['26']= $pckCountGtotal['27']= $pckCountGtotal['28']= $pckCountGtotal['29']= $pckCountGtotal['30']= $pckCountGtotal['31']="";
			$misCountGtotal['01'] = $misCountGtotal['02'] = $misCountGtotal['03'] = $misCountGtotal['04'] = $misCountGtotal['05'] = $misCountGtotal['06'] = $misCountGtotal['07'] = $misCountGtotal['08'] = $misCountGtotal['09'] = $misCountGtotal['10'] = $misCountGtotal['11'] = $misCountGtotal['12'] = $misCountGtotal['13'] = $misCountGtotal['14'] = $misCountGtotal['15'] = $misCountGtotal['16'] = $misCountGtotal['17'] = $misCountGtotal['18'] = $misCountGtotal['19'] = $misCountGtotal['20'] = $misCountGtotal['21'] = $misCountGtotal['22'] = $misCountGtotal['23']= $misCountGtotal['24']= $misCountGtotal['25']= $misCountGtotal['26']= $misCountGtotal['27']= $misCountGtotal['28']= $misCountGtotal['29']= $misCountGtotal['30']= $misCountGtotal['31']="";
			$CashSumCountGtotal['01'] = $CashSumCountGtotal['02'] = $CashSumCountGtotal['03'] = $CashSumCountGtotal['04'] = $CashSumCountGtotal['05'] = $CashSumCountGtotal['06'] = $CashSumCountGtotal['07'] = $CashSumCountGtotal['08'] = $CashSumCountGtotal['09'] = $CashSumCountGtotal['10'] = $CashSumCountGtotal['11'] = $CashSumCountGtotal['12'] = $CashSumCountGtotal['13'] = $CashSumCountGtotal['14'] = $CashSumCountGtotal['15'] = $CashSumCountGtotal['16'] = $CashSumCountGtotal['17'] = $CashSumCountGtotal['18'] = $CashSumCountGtotal['19'] = $CashSumCountGtotal['20'] = $CashSumCountGtotal['21'] = $CashSumCountGtotal['22'] = $CashSumCountGtotal['23']= $CashSumCountGtotal['24']= $CashSumCountGtotal['25']= $CashSumCountGtotal['26']= $CashSumCountGtotal['27']= $CashSumCountGtotal['28']= $CashSumCountGtotal['29']= $CashSumCountGtotal['30']= $CashSumCountGtotal['31']="";
			//SUM
			$labSumGtotal['01'] = $labSumGtotal['02'] = $labSumGtotal['03'] = $labSumGtotal['04'] = $labSumGtotal['05'] = $labSumGtotal['06'] = $labSumGtotal['07'] = $labSumGtotal['08'] = $labSumGtotal['09'] = $labSumGtotal['10'] = $labSumGtotal['11'] = $labSumGtotal['12'] = $labSumGtotal['13'] = $labSumGtotal['14'] = $labSumGtotal['15'] = $labSumGtotal['16'] = $labSumGtotal['17'] = $labSumGtotal['18'] = $labSumGtotal['19'] = $labSumGtotal['20'] = $labSumGtotal['21'] = $labSumGtotal['22'] = $labSumGtotal['23']= $labSumGtotal['24']= $labSumGtotal['25']= $labSumGtotal['26']= $labSumGtotal['27']= $labSumGtotal['28']= $labSumGtotal['29']= $labSumGtotal['30']= $labSumGtotal['31']="";
			$radSumGtotal['01'] = $radSumGtotal['02'] = $radSumGtotal['03'] = $radSumGtotal['04'] = $radSumGtotal['05'] = $radSumGtotal['06'] = $radSumGtotal['07'] = $radSumGtotal['08'] = $radSumGtotal['09'] = $radSumGtotal['10'] = $radSumGtotal['11'] = $radSumGtotal['12'] = $radSumGtotal['13'] = $radSumGtotal['14'] = $radSumGtotal['15'] = $radSumGtotal['16'] = $radSumGtotal['17'] = $radSumGtotal['18'] = $radSumGtotal['19'] = $radSumGtotal['20'] = $radSumGtotal['21'] = $radSumGtotal['22'] = $radSumGtotal['23']= $radSumGtotal['24']= $radSumGtotal['25']= $radSumGtotal['26']= $radSumGtotal['27']= $radSumGtotal['28']= $radSumGtotal['29']= $radSumGtotal['30']= $radSumGtotal['31']="";
			$conSumGtotal['01'] = $conSumGtotal['02'] = $conSumGtotal['03'] = $conSumGtotal['04'] = $conSumGtotal['05'] = $conSumGtotal['06'] = $conSumGtotal['07'] = $conSumGtotal['08'] = $conSumGtotal['09'] = $conSumGtotal['10'] = $conSumGtotal['11'] = $conSumGtotal['12'] = $conSumGtotal['13'] = $conSumGtotal['14'] = $conSumGtotal['15'] = $conSumGtotal['16'] = $conSumGtotal['17'] = $conSumGtotal['18'] = $conSumGtotal['19'] = $conSumGtotal['20'] = $conSumGtotal['21'] = $conSumGtotal['22'] = $conSumGtotal['23']= $conSumGtotal['24']= $conSumGtotal['25']= $conSumGtotal['26']= $conSumGtotal['27']= $conSumGtotal['28']= $conSumGtotal['29']= $conSumGtotal['30']= $conSumGtotal['31']="";
			$pckSumGtotal['01'] = $pckSumGtotal['02'] = $pckSumGtotal['03'] = $pckSumGtotal['04'] = $pckSumGtotal['05'] = $pckSumGtotal['06'] = $pckSumGtotal['07'] = $pckSumGtotal['08'] = $pckSumGtotal['09'] = $pckSumGtotal['10'] = $pckSumGtotal['11'] = $pckSumGtotal['12'] = $pckSumGtotal['13'] = $pckSumGtotal['14'] = $pckSumGtotal['15'] = $pckSumGtotal['16'] = $pckSumGtotal['17'] = $pckSumGtotal['18'] = $pckSumGtotal['19'] = $pckSumGtotal['20'] = $pckSumGtotal['21'] = $pckSumGtotal['22'] = $pckSumGtotal['23']= $pckSumGtotal['24']= $pckSumGtotal['25']= $pckSumGtotal['26']= $pckSumGtotal['27']= $pckSumGtotal['28']= $pckSumGtotal['29']= $pckSumGtotal['30']= $pckSumGtotal['31']="";
			$misSumGtotal['01'] = $misSumGtotal['02'] = $misSumGtotal['03'] = $misSumGtotal['04'] = $misSumGtotal['05'] = $misSumGtotal['06'] = $misSumGtotal['07'] = $misSumGtotal['08'] = $misSumGtotal['09'] = $misSumGtotal['10'] = $misSumGtotal['11'] = $misSumGtotal['12'] = $misSumGtotal['13'] = $misSumGtotal['14'] = $misSumGtotal['15'] = $misSumGtotal['16'] = $misSumGtotal['17'] = $misSumGtotal['18'] = $misSumGtotal['19'] = $misSumGtotal['20'] = $misSumGtotal['21'] = $misSumGtotal['22'] = $misSumGtotal['23']= $misSumGtotal['24']= $misSumGtotal['25']= $misSumGtotal['26']= $misSumGtotal['27']= $misSumGtotal['28']= $misSumGtotal['29']= $misSumGtotal['30']= $misSumGtotal['31']="";
			$CashTotalSumGtotal['01'] = $CashTotalSumGtotal['02'] = $CashTotalSumGtotal['03'] = $CashTotalSumGtotal['04'] = $CashTotalSumGtotal['05'] = $CashTotalSumGtotal['06'] = $CashTotalSumGtotal['07'] = $CashTotalSumGtotal['08'] = $CashTotalSumGtotal['09'] = $CashTotalSumGtotal['10'] = $CashTotalSumGtotal['11'] = $CashTotalSumGtotal['12'] = $CashTotalSumGtotal['13'] = $CashTotalSumGtotal['14'] = $CashTotalSumGtotal['15'] = $CashTotalSumGtotal['16'] = $CashTotalSumGtotal['17'] = $CashTotalSumGtotal['18'] = $CashTotalSumGtotal['19'] = $CashTotalSumGtotal['20'] = $CashTotalSumGtotal['21'] = $CashTotalSumGtotal['22'] = $CashTotalSumGtotal['23']= $CashTotalSumGtotal['24']= $CashTotalSumGtotal['25']= $CashTotalSumGtotal['26']= $CashTotalSumGtotal['27']= $CashTotalSumGtotal['28']= $CashTotalSumGtotal['29']= $CashTotalSumGtotal['30']= $CashTotalSumGtotal['31']="";
			//// 
			$labCount['01'] = $labCount['02'] = $labCount['03'] = $labCount['04'] = $labCount['05'] = $labCount['06'] = $labCount['07'] = $labCount['08'] = $labCount['09'] = $labCount['10'] = $labCount['11'] = $labCount['12'] = $labCount['13'] = $labCount['14'] = $labCount['15'] = $labCount['16'] = $labCount['17'] = $labCount['18'] = $labCount['19'] = $labCount['20'] = $labCount['21'] = $labCount['22'] = $labCount['23']= $labCount['24']= $labCount['25']= $labCount['26']= $labCount['27']= $labCount['28']= $labCount['29']= $labCount['30']= $labCount['31']="";
			$radCount['01'] = $radCount['02'] = $radCount['03'] = $radCount['04'] = $radCount['05'] = $radCount['06'] = $radCount['07'] = $radCount['08'] = $radCount['09'] = $radCount['10'] = $radCount['11'] = $radCount['12'] = $radCount['13'] = $radCount['14'] = $radCount['15'] = $radCount['16'] = $radCount['17'] = $radCount['18'] = $radCount['19'] = $radCount['20'] = $radCount['21'] = $radCount['22'] = $radCount['23']= $radCount['24']= $radCount['25']= $radCount['26']= $radCount['27']= $radCount['28']= $radCount['29']= $radCount['30']= $radCount['31']="";
			$conCount['01'] = $conCount['02'] = $conCount['03'] = $conCount['04'] = $conCount['05'] = $conCount['06'] = $conCount['07'] = $conCount['08'] = $conCount['09'] = $conCount['10'] = $conCount['11'] = $conCount['12'] = $conCount['13'] = $conCount['14'] = $conCount['15'] = $conCount['16'] = $conCount['17'] = $conCount['18'] = $conCount['19'] = $conCount['20'] = $conCount['21'] = $conCount['22'] = $conCount['23']= $conCount['24']= $conCount['25']= $conCount['26']= $conCount['27']= $conCount['28']= $conCount['29']= $conCount['30']= $conCount['31']="";
			$pckCount['01'] = $pckCount['02'] = $pckCount['03'] = $pckCount['04'] = $pckCount['05'] = $pckCount['06'] = $pckCount['07'] = $pckCount['08'] = $pckCount['09'] = $pckCount['10'] = $pckCount['11'] = $pckCount['12'] = $pckCount['13'] = $pckCount['14'] = $pckCount['15'] = $pckCount['16'] = $pckCount['17'] = $pckCount['18'] = $pckCount['19'] = $pckCount['20'] = $pckCount['21'] = $pckCount['22'] = $pckCount['23']= $pckCount['24']= $pckCount['25']= $pckCount['26']= $pckCount['27']= $pckCount['28']= $pckCount['29']= $pckCount['30']= $pckCount['31']="";
			$misCount['01'] = $misCount['02'] = $misCount['03'] = $misCount['04'] = $misCount['05'] = $misCount['06'] = $misCount['07'] = $misCount['08'] = $misCount['09'] = $misCount['10'] = $misCount['11'] = $misCount['12'] = $misCount['13'] = $misCount['14'] = $misCount['15'] = $misCount['16'] = $misCount['17'] = $misCount['18'] = $misCount['19'] = $misCount['20'] = $misCount['21'] = $misCount['22'] = $misCount['23']= $misCount['24']= $misCount['25']= $misCount['26']= $misCount['27']= $misCount['28']= $misCount['29']= $misCount['30']= $misCount['31']="";
			$CashSumCount['01'] = $CashSumCount['02'] = $CashSumCount['03'] = $CashSumCount['04'] = $CashSumCount['05'] = $CashSumCount['06'] = $CashSumCount['07'] = $CashSumCount['08'] = $CashSumCount['09'] = $CashSumCount['10'] = $CashSumCount['11'] = $CashSumCount['12'] = $CashSumCount['13'] = $CashSumCount['14'] = $CashSumCount['15'] = $CashSumCount['16'] = $CashSumCount['17'] = $CashSumCount['18'] = $CashSumCount['19'] = $CashSumCount['20'] = $CashSumCount['21'] = $CashSumCount['22'] = $CashSumCount['23']= $CashSumCount['24']= $CashSumCount['25']= $CashSumCount['26']= $CashSumCount['27']= $CashSumCount['28']= $CashSumCount['29']= $CashSumCount['30']= $CashSumCount['31']="";
			//SUM
			$labSum['01'] = $labSum['02'] = $labSum['03'] = $labSum['04'] = $labSum['05'] = $labSum['06'] = $labSum['07'] = $labSum['08'] = $labSum['09'] = $labSum['10'] = $labSum['11'] = $labSum['12'] = $labSum['13'] = $labSum['14'] = $labSum['15'] = $labSum['16'] = $labSum['17'] = $labSum['18'] = $labSum['19'] = $labSum['20'] = $labSum['21'] = $labSum['22'] = $labSum['23']= $labSum['24']= $labSum['25']= $labSum['26']= $labSum['27']= $labSum['28']= $labSum['29']= $labSum['30']= $labSum['31']="";
			$radSum['01'] = $radSum['02'] = $radSum['03'] = $radSum['04'] = $radSum['05'] = $radSum['06'] = $radSum['07'] = $radSum['08'] = $radSum['09'] = $radSum['10'] = $radSum['11'] = $radSum['12'] = $radSum['13'] = $radSum['14'] = $radSum['15'] = $radSum['16'] = $radSum['17'] = $radSum['18'] = $radSum['19'] = $radSum['20'] = $radSum['21'] = $radSum['22'] = $radSum['23']= $radSum['24']= $radSum['25']= $radSum['26']= $radSum['27']= $radSum['28']= $radSum['29']= $radSum['30']= $radSum['31']="";
			$conSum['01'] = $conSum['02'] = $conSum['03'] = $conSum['04'] = $conSum['05'] = $conSum['06'] = $conSum['07'] = $conSum['08'] = $conSum['09'] = $conSum['10'] = $conSum['11'] = $conSum['12'] = $conSum['13'] = $conSum['14'] = $conSum['15'] = $conSum['16'] = $conSum['17'] = $conSum['18'] = $conSum['19'] = $conSum['20'] = $conSum['21'] = $conSum['22'] = $conSum['23']= $conSum['24']= $conSum['25']= $conSum['26']= $conSum['27']= $conSum['28']= $conSum['29']= $conSum['30']= $conSum['31']="";
			$pckSum['01'] = $pckSum['02'] = $pckSum['03'] = $pckSum['04'] = $pckSum['05'] = $pckSum['06'] = $pckSum['07'] = $pckSum['08'] = $pckSum['09'] = $pckSum['10'] = $pckSum['11'] = $pckSum['12'] = $pckSum['13'] = $pckSum['14'] = $pckSum['15'] = $pckSum['16'] = $pckSum['17'] = $pckSum['18'] = $pckSum['19'] = $pckSum['20'] = $pckSum['21'] = $pckSum['22'] = $pckSum['23']= $pckSum['24']= $pckSum['25']= $pckSum['26']= $pckSum['27']= $pckSum['28']= $pckSum['29']= $pckSum['30']= $pckSum['31']="";
			$misSum['01'] = $misSum['02'] = $misSum['03'] = $misSum['04'] = $misSum['05'] = $misSum['06'] = $misSum['07'] = $misSum['08'] = $misSum['09'] = $misSum['10'] = $misSum['11'] = $misSum['12'] = $misSum['13'] = $misSum['14'] = $misSum['15'] = $misSum['16'] = $misSum['17'] = $misSum['18'] = $misSum['19'] = $misSum['20'] = $misSum['21'] = $misSum['22'] = $misSum['23']= $misSum['24']= $misSum['25']= $misSum['26']= $misSum['27']= $misSum['28']= $misSum['29']= $misSum['30']= $misSum['31']="";
			$CashTotalSum['01'] = $CashTotalSum['02'] = $CashTotalSum['03'] = $CashTotalSum['04'] = $CashTotalSum['05'] = $CashTotalSum['06'] = $CashTotalSum['07'] = $CashTotalSum['08'] = $CashTotalSum['09'] = $CashTotalSum['10'] = $CashTotalSum['11'] = $CashTotalSum['12'] = $CashTotalSum['13'] = $CashTotalSum['14'] = $CashTotalSum['15'] = $CashTotalSum['16'] = $CashTotalSum['17'] = $CashTotalSum['18'] = $CashTotalSum['19'] = $CashTotalSum['20'] = $CashTotalSum['21'] = $CashTotalSum['22'] = $CashTotalSum['23']= $CashTotalSum['24']= $CashTotalSum['25']= $CashTotalSum['26']= $CashTotalSum['27']= $CashTotalSum['28']= $CashTotalSum['29']= $CashTotalSum['30']= $CashTotalSum['31']="";


			$labCountHC['01'] = $labCountHC['02'] = $labCountHC['03'] = $labCountHC['04'] = $labCountHC['05'] = $labCountHC['06'] = $labCountHC['07'] = $labCountHC['08'] = $labCountHC['09'] = $labCountHC['10'] = $labCountHC['11'] = $labCountHC['12'] = $labCountHC['13'] = $labCountHC['14'] = $labCountHC['15'] = $labCountHC['16'] = $labCountHC['17'] = $labCountHC['18'] = $labCountHC['19'] = $labCountHC['20'] = $labCountHC['21'] = $labCountHC['22'] = $labCountHC['23']= $labCountHC['24']= $labCountHC['25']= $labCountHC['26']= $labCountHC['27']= $labCountHC['28']= $labCountHC['29']= $labCountHC['30']= $labCountHC['31']="";
			$radCountHC['01'] = $radCountHC['02'] = $radCountHC['03'] = $radCountHC['04'] = $radCountHC['05'] = $radCountHC['06'] = $radCountHC['07'] = $radCountHC['08'] = $radCountHC['09'] = $radCountHC['10'] = $radCountHC['11'] = $radCountHC['12'] = $radCountHC['13'] = $radCountHC['14'] = $radCountHC['15'] = $radCountHC['16'] = $radCountHC['17'] = $radCountHC['18'] = $radCountHC['19'] = $radCountHC['20'] = $radCountHC['21'] = $radCountHC['22'] = $radCountHC['23']= $radCountHC['24']= $radCountHC['25']= $radCountHC['26']= $radCountHC['27']= $radCountHC['28']= $radCountHC['29']= $radCountHC['30']= $radCountHC['31']="";
			$conCountHC['01'] = $conCountHC['02'] = $conCountHC['03'] = $conCountHC['04'] = $conCountHC['05'] = $conCountHC['06'] = $conCountHC['07'] = $conCountHC['08'] = $conCountHC['09'] = $conCountHC['10'] = $conCountHC['11'] = $conCountHC['12'] = $conCountHC['13'] = $conCountHC['14'] = $conCountHC['15'] = $conCountHC['16'] = $conCountHC['17'] = $conCountHC['18'] = $conCountHC['19'] = $conCountHC['20'] = $conCountHC['21'] = $conCountHC['22'] = $conCountHC['23']= $conCountHC['24']= $conCountHC['25']= $conCountHC['26']= $conCountHC['27']= $conCountHC['28']= $conCountHC['29']= $conCountHC['30']= $conCountHC['31']="";
			$pckCountHC['01'] = $pckCountHC['02'] = $pckCountHC['03'] = $pckCountHC['04'] = $pckCountHC['05'] = $pckCountHC['06'] = $pckCountHC['07'] = $pckCountHC['08'] = $pckCountHC['09'] = $pckCountHC['10'] = $pckCountHC['11'] = $pckCountHC['12'] = $pckCountHC['13'] = $pckCountHC['14'] = $pckCountHC['15'] = $pckCountHC['16'] = $pckCountHC['17'] = $pckCountHC['18'] = $pckCountHC['19'] = $pckCountHC['20'] = $pckCountHC['21'] = $pckCountHC['22'] = $pckCountHC['23']= $pckCountHC['24']= $pckCountHC['25']= $pckCountHC['26']= $pckCountHC['27']= $pckCountHC['28']= $pckCountHC['29']= $pckCountHC['30']= $pckCountHC['31']="";
			$misCountHC['01'] = $misCountHC['02'] = $misCountHC['03'] = $misCountHC['04'] = $misCountHC['05'] = $misCountHC['06'] = $misCountHC['07'] = $misCountHC['08'] = $misCountHC['09'] = $misCountHC['10'] = $misCountHC['11'] = $misCountHC['12'] = $misCountHC['13'] = $misCountHC['14'] = $misCountHC['15'] = $misCountHC['16'] = $misCountHC['17'] = $misCountHC['18'] = $misCountHC['19'] = $misCountHC['20'] = $misCountHC['21'] = $misCountHC['22'] = $misCountHC['23']= $misCountHC['24']= $misCountHC['25']= $misCountHC['26']= $misCountHC['27']= $misCountHC['28']= $misCountHC['29']= $misCountHC['30']= $misCountHC['31']="";
			$CashSumCountHC['01'] = $CashSumCountHC['02'] = $CashSumCountHC['03'] = $CashSumCountHC['04'] = $CashSumCountHC['05'] = $CashSumCountHC['06'] = $CashSumCountHC['07'] = $CashSumCountHC['08'] = $CashSumCountHC['09'] = $CashSumCountHC['10'] = $CashSumCountHC['11'] = $CashSumCountHC['12'] = $CashSumCountHC['13'] = $CashSumCountHC['14'] = $CashSumCountHC['15'] = $CashSumCountHC['16'] = $CashSumCountHC['17'] = $CashSumCountHC['18'] = $CashSumCountHC['19'] = $CashSumCountHC['20'] = $CashSumCountHC['21'] = $CashSumCountHC['22'] = $CashSumCountHC['23']= $CashSumCountHC['24']= $CashSumCountHC['25']= $CashSumCountHC['26']= $CashSumCountHC['27']= $CashSumCountHC['28']= $CashSumCountHC['29']= $CashSumCountHC['30']= $CashSumCountHC['31']="";
			//SUM
			$labSumHC['01'] = $labSumHC['02'] = $labSumHC['03'] = $labSumHC['04'] = $labSumHC['05'] = $labSumHC['06'] = $labSumHC['07'] = $labSumHC['08'] = $labSumHC['09'] = $labSumHC['10'] = $labSumHC['11'] = $labSumHC['12'] = $labSumHC['13'] = $labSumHC['14'] = $labSumHC['15'] = $labSumHC['16'] = $labSumHC['17'] = $labSumHC['18'] = $labSumHC['19'] = $labSumHC['20'] = $labSumHC['21'] = $labSumHC['22'] = $labSumHC['23']= $labSumHC['24']= $labSumHC['25']= $labSumHC['26']= $labSumHC['27']= $labSumHC['28']= $labSumHC['29']= $labSumHC['30']= $labSumHC['31']="";
			$radSumHC['01'] = $radSumHC['02'] = $radSumHC['03'] = $radSumHC['04'] = $radSumHC['05'] = $radSumHC['06'] = $radSumHC['07'] = $radSumHC['08'] = $radSumHC['09'] = $radSumHC['10'] = $radSumHC['11'] = $radSumHC['12'] = $radSumHC['13'] = $radSumHC['14'] = $radSumHC['15'] = $radSumHC['16'] = $radSumHC['17'] = $radSumHC['18'] = $radSumHC['19'] = $radSumHC['20'] = $radSumHC['21'] = $radSumHC['22'] = $radSumHC['23']= $radSumHC['24']= $radSumHC['25']= $radSumHC['26']= $radSumHC['27']= $radSumHC['28']= $radSumHC['29']= $radSumHC['30']= $radSumHC['31']="";
			$conSumHC['01'] = $conSumHC['02'] = $conSumHC['03'] = $conSumHC['04'] = $conSumHC['05'] = $conSumHC['06'] = $conSumHC['07'] = $conSumHC['08'] = $conSumHC['09'] = $conSumHC['10'] = $conSumHC['11'] = $conSumHC['12'] = $conSumHC['13'] = $conSumHC['14'] = $conSumHC['15'] = $conSumHC['16'] = $conSumHC['17'] = $conSumHC['18'] = $conSumHC['19'] = $conSumHC['20'] = $conSumHC['21'] = $conSumHC['22'] = $conSumHC['23']= $conSumHC['24']= $conSumHC['25']= $conSumHC['26']= $conSumHC['27']= $conSumHC['28']= $conSumHC['29']= $conSumHC['30']= $conSumHC['31']="";
			$pckSumHC['01'] = $pckSumHC['02'] = $pckSumHC['03'] = $pckSumHC['04'] = $pckSumHC['05'] = $pckSumHC['06'] = $pckSumHC['07'] = $pckSumHC['08'] = $pckSumHC['09'] = $pckSumHC['10'] = $pckSumHC['11'] = $pckSumHC['12'] = $pckSumHC['13'] = $pckSumHC['14'] = $pckSumHC['15'] = $pckSumHC['16'] = $pckSumHC['17'] = $pckSumHC['18'] = $pckSumHC['19'] = $pckSumHC['20'] = $pckSumHC['21'] = $pckSumHC['22'] = $pckSumHC['23']= $pckSumHC['24']= $pckSumHC['25']= $pckSumHC['26']= $pckSumHC['27']= $pckSumHC['28']= $pckSumHC['29']= $pckSumHC['30']= $pckSumHC['31']="";
			$misSumHC['01'] = $misSumHC['02'] = $misSumHC['03'] = $misSumHC['04'] = $misSumHC['05'] = $misSumHC['06'] = $misSumHC['07'] = $misSumHC['08'] = $misSumHC['09'] = $misSumHC['10'] = $misSumHC['11'] = $misSumHC['12'] = $misSumHC['13'] = $misSumHC['14'] = $misSumHC['15'] = $misSumHC['16'] = $misSumHC['17'] = $misSumHC['18'] = $misSumHC['19'] = $misSumHC['20'] = $misSumHC['21'] = $misSumHC['22'] = $misSumHC['23']= $misSumHC['24']= $misSumHC['25']= $misSumHC['26']= $misSumHC['27']= $misSumHC['28']= $misSumHC['29']= $misSumHC['30']= $misSumHC['31']="";
			$CashTotalSumHC['01'] = $CashTotalSumHC['02'] = $CashTotalSumHC['03'] = $CashTotalSumHC['04'] = $CashTotalSumHC['05'] = $CashTotalSumHC['06'] = $CashTotalSumHC['07'] = $CashTotalSumHC['08'] = $CashTotalSumHC['09'] = $CashTotalSumHC['10'] = $CashTotalSumHC['11'] = $CashTotalSumHC['12'] = $CashTotalSumHC['13'] = $CashTotalSumHC['14'] = $CashTotalSumHC['15'] = $CashTotalSumHC['16'] = $CashTotalSumHC['17'] = $CashTotalSumHC['18'] = $CashTotalSumHC['19'] = $CashTotalSumHC['20'] = $CashTotalSumHC['21'] = $CashTotalSumHC['22'] = $CashTotalSumHC['23']= $CashTotalSumHC['24']= $CashTotalSumHC['25']= $CashTotalSumHC['26']= $CashTotalSumHC['27']= $CashTotalSumHC['28']= $CashTotalSumHC['29']= $CashTotalSumHC['30']= $CashTotalSumHC['31']="";

///Summary 
			$QueData = DB::connection('CMS')->table('Queue')
				->leftjoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id')
				->leftjoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
				->leftjoin('Transactions', 'Queue.Id', '=', 'Transactions.IdQueue')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '640');
				//->where('Queue.IdBU',  session('userClinicCode'))
				//->where('Queue.Id', '2404')
				if (!empty($clinicCode)) {
					$request->input('Clinic') != "ALL"
						? $QueData->where('Queue.IdBU', $clinicCode)
						: $QueData->whereIn('Queue.IdBU', $clinicCode);
				}
				$QueData = $QueData->groupBy('Queue.Id')
				->orderBy('Queue.Code')
				->get(array('Queue.Id as QueId','Queue.Code as QCode', 'Queue.Date as QDate', 'Queue.IdPatient', 'Patient.FullName as PName', 'Patient.Code as PCode', 'Queue.IdBU as ClinicCode'));
			
			foreach($QueData as $cashTrans)
			{
				$labCountGtotal[date('d', strtotime($cashTrans->QDate))]  = $labCount[date('d', strtotime($cashTrans->QDate))]  = 0;
				$radCountGtotal[date('d', strtotime($cashTrans->QDate))]  = $radCount[date('d', strtotime($cashTrans->QDate))]  = 0;
				$conCountGtotal[date('d', strtotime($cashTrans->QDate))]  = $conCount[date('d', strtotime($cashTrans->QDate))]  = 0;
				$pckCountGtotal[date('d', strtotime($cashTrans->QDate))]  = $pckCount[date('d', strtotime($cashTrans->QDate))]  = 0;
				$misCountGtotal[date('d', strtotime($cashTrans->QDate))]  = $misCount[date('d', strtotime($cashTrans->QDate))]  = 0;
				$CashSumCountGtotal[date('d', strtotime($cashTrans->QDate))]  = $CashSumCount[date('d', strtotime($cashTrans->QDate))]  = 0;
				//SUM
				$labSumGtotal[date('d', strtotime($cashTrans->QDate))]  = $labSum[date('d', strtotime($cashTrans->QDate))]  = 0;
				$radSumGtotal[date('d', strtotime($cashTrans->QDate))]  = $radSum[date('d', strtotime($cashTrans->QDate))]  = 0;
				$conSumGtotal[date('d', strtotime($cashTrans->QDate))]  = $conSum[date('d', strtotime($cashTrans->QDate))]  = 0;
				$pckSumGtotal[date('d', strtotime($cashTrans->QDate))]  = $pckSum[date('d', strtotime($cashTrans->QDate))]  = 0;
				$misSumGtotal[date('d', strtotime($cashTrans->QDate))]  = $misSum[date('d', strtotime($cashTrans->QDate))]  = 0;
				$CashTotalSumGtotal[date('d', strtotime($cashTrans->QDate))]  = $CashTotalSum[date('d', strtotime($cashTrans->QDate))]  = 0;
				
				$labCountHC[date('d', strtotime($cashTrans->QDate))]  = 0;
				$radCountHC[date('d', strtotime($cashTrans->QDate))]  = 0;
				$conCountHC[date('d', strtotime($cashTrans->QDate))]  = 0;
				$pckCountHC[date('d', strtotime($cashTrans->QDate))]  = 0;
				$misCountHC[date('d', strtotime($cashTrans->QDate))]  = 0;
				$CashSumCountHC[date('d', strtotime($cashTrans->QDate))]  = 0;
				//SUM
				$labSumHC[date('d', strtotime($cashTrans->QDate))]  = 0;
				$radSumHC[date('d', strtotime($cashTrans->QDate))]  = 0;
				$conSumHC[date('d', strtotime($cashTrans->QDate))]  = 0;
				$pckSumHC[date('d', strtotime($cashTrans->QDate))]  = 0;
				$misSumHC[date('d', strtotime($cashTrans->QDate))]  = 0;
				$CashTotalSumHC[date('d', strtotime($cashTrans->QDate))]  = 0;
				
				
				
			}
			
			$grossAmount = 0;
			$discountAmount = 0;
			$netAmount = 0;
			$cash = 0;
			$Gcash = 0;
			$credit = 0;
			$cheque = 0;
			$online = 0;
			foreach($QueData as $data)
			{
				// group into bill to
				$billGroup = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $data->QueId)->groupBy('BillTo')->get(array('BillTo'));
				
				
				foreach($billGroup as $billTo)
				{ 
					$trans = DB::connection('CMS')->select("
						SELECT 
						    tb2.`CodeItemPrice`
						   ,tb2.`DescriptionItemPrice`
						   ,'1' as ItemQty
						   ,tb1.ProviderType
						   ,tb1.`CurrentItemAmount` as CurrentAmount
						   ,tb1.CoverageType
						   ,tb1.`CoverageAmount`
						   ,tb1.PaymentType
						   ,tb1.ORNum
						   ,tb2.NameDoctor as Dname
						   ,tb2.PriceGroupItemPrice
						   ,tb1.InputBy as BilledBy
						   ,tb1.BillTo
						   ,tb1.IdTransaction
						   ,tb1.DiscType
						      FROM 
						    `PaymentHistory` tb1
						    JOIN `Transactions` tb2 ON (tb1.`IdTransaction` = tb2.`Id`)
						WHERE tb1.IdQueue  = '".$data->QueId."' and tb1.`BillTo` =  '".$billTo->BillTo."'  
					");
					
					$transIdCheck = 0;
					foreach($trans as $hisT)
					{
						$Guarantor = ($billTo->BillTo == 0)?'': DB::connection('Eros')->table('Company')->where('Id', $billTo->BillTo)->get(array('Code','Name'));
						$GuarantorCode = ($billTo->BillTo == 0)?'':$Guarantor[0]->Code;
						$GuarantorName = ($billTo->BillTo == 0)?'':$Guarantor[0]->Name;
						$category = ($hisT->PriceGroupItemPrice != "Item")?$hisT->PriceGroupItemPrice : DB::connection('Eros')->table('ItemMaster')->where('Code', $hisT->CodeItemPrice)->get(array('DepartmentGroup'))[0]->DepartmentGroup;
						
						
						$grossAmount += $hisT->CoverageAmount;
						$Inet = 0; $Icash = 0; $Igcash = 0; $Icredit = 0; $Icheque = 0; $Ionline = 0;
						
						if($hisT->CoverageAmount != 0 && $hisT->PaymentType != "Discount" )
						{
							$Inet = $hisT->CoverageAmount;
							$netAmount += $hisT->CoverageAmount;
							if( $hisT->PaymentType == "Cash" )
							{
								$cash += $hisT->CoverageAmount;
								$Icash =  $hisT->CoverageAmount;
							}
							else if ($hisT->PaymentType == "GCash" )
							{
								$Gcash += $hisT->CoverageAmount;
								$Igcash = $hisT->CoverageAmount;
							}
							else if ($hisT->PaymentType == "Credit" )
							{
								$credit += $hisT->CoverageAmount;
								$Icredit = $hisT->CoverageAmount;
							}
							else if ($hisT->PaymentType == "Cheque" )
							{
								$cheque += $hisT->CoverageAmount;
								$Icheque = $hisT->CoverageAmount;
							}
							else if ($hisT->PaymentType == "Online" )
							{
								$online += $hisT->CoverageAmount;
								$Ionline = $hisT->CoverageAmount;
							}
						}
						
						if($hisT->CoverageAmount != 0 && $hisT->PaymentType == "Discount" )
						{
							$discountAmount += $hisT->CoverageAmount;
						}
						// Gross Sale display
						if(  $transIdCheck == $hisT->IdTransaction )
						{	
							$grossItemSales =  0;//($hisT->PaymentType == "Discount") ? 0 : $hisT->CurrentAmount;
							$transIdCheck =  $hisT->IdTransaction;
							$category =  "";
							$itemDescription = "";
							$discount = 0;
							$net =0;
							
							
						}
						else
						{
							$grossItemSales =  $hisT->CurrentAmount;
							$transIdCheck =  $hisT->IdTransaction;
							$itemDescription = $hisT->DescriptionItemPrice;
							$discount = ($hisT->DiscType == 0 || $hisT->DiscType == "" )? 0 : $hisT->DiscType;
							$net = ($hisT->DiscType == 0 || $hisT->DiscType == "" )? $grossItemSales : ($grossItemSales - $hisT->DiscType);
							//summary
							$labCountGtotal[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType == "PATIENT")?1:0; $labCount[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType == "PATIENT")?1:0;
							$radCountGtotal[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType == "PATIENT")?1:0; $radCount[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType == "PATIENT")?1:0;
							$conCountGtotal[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType == "PATIENT")?1:0; $conCount[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType == "PATIENT")?1:0;
							$pckCountGtotal[date('d', strtotime($data->QDate))] += ($category == "Package" && $hisT->ProviderType == "PATIENT")?1:0; $pckCount[date('d', strtotime($data->QDate))] += ($category == "Package" && $hisT->ProviderType == "PATIENT")?1:0;
							$misCountGtotal[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType == "PATIENT")?1:0; $misCount[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType == "PATIENT")?1:0;
							$CashSumCountGtotal[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType == "PATIENT")?1:0;
							$CashSumCountGtotal[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType == "PATIENT")?1:0;
							$CashSumCountGtotal[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType == "PATIENT")?1:0;
							$CashSumCountGtotal[date('d', strtotime($data->QDate))] += ($category == "Package" && $hisT->ProviderType == "PATIENT")?1:0; 
							$CashSumCountGtotal[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType == "PATIENT")?1:0; 
							$CashSumCount[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType == "PATIENT")?1:0;
							$CashSumCount[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType == "PATIENT")?1:0; 
							$CashSumCount[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType == "PATIENT")?1:0; 
							$CashSumCount[date('d', strtotime($data->QDate))] +=($category == "Package" && $hisT->ProviderType == "PATIENT")?1:0; 
							$CashSumCount[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType == "PATIENT")?1:0;
						
							$labSumGtotal[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0; $labSum[date('d', strtotime($data->QDate))]  += ($category == "LABORATORY" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0;
							$radSumGtotal[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0; $radSum[date('d', strtotime($data->QDate))]  += ($category == "IMAGING" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0;
							$conSumGtotal[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0; $conSum[date('d', strtotime($data->QDate))]  +=  ($category == "CONSULTATION" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0;
							$pckSumGtotal[date('d', strtotime($data->QDate))] += ($category == "Package" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0; $pckSum[date('d', strtotime($data->QDate))]  +=($category == "Package" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0;
							$misSumGtotal[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0; $misSum[date('d', strtotime($data->QDate))]  += ($category == "MISC" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0;
							$CashTotalSumGtotal[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0; 
							$CashTotalSumGtotal[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0; 
							$CashTotalSumGtotal[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0; 
							$CashTotalSumGtotal[date('d', strtotime($data->QDate))] +=  ($category == "Package" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0;  
							$CashTotalSumGtotal[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0; 
							$CashTotalSum[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0; 
							$CashTotalSum[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0;  
							$CashTotalSum[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0; 
							$CashTotalSum[date('d', strtotime($data->QDate))] += ($category == "Package" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0;  
							$CashTotalSum[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType == "PATIENT")?floatval($grossItemSales):0; 
						///////////
							$labCountGtotal[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType != "PATIENT")?1:0; $labCountHC[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType != "PATIENT")?1:0;
							$radCountGtotal[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType != "PATIENT")?1:0; $radCountHC[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType != "PATIENT")?1:0; 
							$conCountGtotal[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType != "PATIENT")?1:0; $conCountHC[date('d', strtotime($data->QDate))] +=($category == "CONSULTATION" && $hisT->ProviderType != "PATIENT")?1:0;
							$pckCountGtotal[date('d', strtotime($data->QDate))] += ($category == "Package" && $hisT->ProviderType != "PATIENT")?1:0; $pckCountHC[date('d', strtotime($data->QDate))] += ($category == "Package" && $hisT->ProviderType != "PATIENT")?1:0;
							$misCountGtotal[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType != "PATIENT")?1:0; $misCountHC[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType != "PATIENT")?1:0;
							$CashSumCountGtotal[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType != "PATIENT")?1:0; 
							$CashSumCountGtotal[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType != "PATIENT")?1:0; 
							$CashSumCountGtotal[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType != "PATIENT")?1:0; 
							$CashSumCountGtotal[date('d', strtotime($data->QDate))] += ($category == "Package" && $hisT->ProviderType != "PATIENT")?1:0; 
							$CashSumCountGtotal[date('d', strtotime($data->QDate))] +=($category == "MISC" && $hisT->ProviderType != "PATIENT")?1:0;
							$CashSumCountHC[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType != "PATIENT")?1:0;  
							$CashSumCountHC[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType != "PATIENT")?1:0;  
							$CashSumCountHC[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType != "PATIENT")?1:0;  
							$CashSumCountHC[date('d', strtotime($data->QDate))] += ($category == "Package" && $hisT->ProviderType != "PATIENT")?1:0;  
							$CashSumCountHC[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType != "PATIENT")?1:0;
							
							$labSumGtotal[date('d', strtotime($data->QDate))] +=  ($category == "LABORATORY" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0; $labSumHC[date('d', strtotime($data->QDate))]  += ($category == "LABORATORY" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0;
							$radSumGtotal[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0; $radSumHC[date('d', strtotime($data->QDate))]  += ($category == "IMAGING" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0;
							$conSumGtotal[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0; $conSumHC[date('d', strtotime($data->QDate))]  += ($category == "CONSULTATION" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0;
							$pckSumGtotal[date('d', strtotime($data->QDate))] += ($category == "Package" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0;  $pckSumHC[date('d', strtotime($data->QDate))]  += ($category == "Package" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0; 
							$misSumGtotal[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0; $misSumHC[date('d', strtotime($data->QDate))]  += ($category == "MISC" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0;
							$CashTotalSumGtotal[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0; 
							$CashTotalSumGtotal[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0; 
							$CashTotalSumGtotal[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0; 
							$CashTotalSumGtotal[date('d', strtotime($data->QDate))] += ($category == "Package" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0;  
							$CashTotalSumGtotal[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0;

							$CashTotalSumHC[date('d', strtotime($data->QDate))] += ($category == "LABORATORY" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0;  
							$CashTotalSumHC[date('d', strtotime($data->QDate))] += ($category == "IMAGING" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0;  
							$CashTotalSumHC[date('d', strtotime($data->QDate))] += ($category == "CONSULTATION" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0;  
							$CashTotalSumHC[date('d', strtotime($data->QDate))] += ($category == "Package" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0;   
							$CashTotalSumHC[date('d', strtotime($data->QDate))] += ($category == "MISC" && $hisT->ProviderType != "PATIENT")?floatval($grossItemSales):0;
									
						
						
						}
						/*
						$worksheet->writeRow($x,0,array(
							session('userClinicCode') // Branch
							,$data->QDate // date
							,$data->QCode // tranx no
							,$data->PCode //patient id
							,$hisT->ORNum //or num
							,'' // CHARGE SLIP N/A
							,$data->PName // patient name
							,$hisT->Dname  // physician name
							,$GuarantorName // guarantor name
							,$itemDescription // item name / item description
							,$category  // item category
							,($hisT->CoverageAmount == 0)? '':$hisT->PaymentType  // payment Type
							,$grossItemSales  //  item amount to become Gross Item Amount
							,($hisT->PaymentType == "Discount" && $hisT->CoverageAmount != 0 )? $hisT->CoverageType: ''  //DISC. TYPE
							,'' //LOYALTY POINTS N/A
							, $discount  //($hisT->PaymentType == "Discount")?$hisT->CoverageAmount:0   // Discount
							,'' //MISCELLANEOUS FEE
							,$net  //$Inet //NET
							,$Icash //CASH
							,$Icheque //CHECK
							,$Icredit //CREDIT / DEBIT CARD
							,$Igcash // GCASH
							,$Ionline //ONLINE
							,($hisT->ProviderType == "PATIENT")? '' : $hisT->ProviderType // HMO/CORPORATE
							,$hisT->BilledBy  //USER ID
							//,$hisT->CodeItemPrice
							//,$hisT->ProviderType
							
							
						));
						$x++;
						*/
					}
				}
			}
			
			$worksheet->writeRow($x++,0,array('Laboratory','','','','','',$labCount['01'],$labSum['01'],$labCount['02'],$labSum['02'],$labCount['03'],$labSum['03'],$labCount['04'],$labSum['04'],$labCount['05'],$labSum['05'],$labCount['06'],$labSum['06'],$labCount['07'],$labSum['07'],$labCount['08'],$labSum['08'],$labCount['09'],$labSum['09'],$labCount['10'],$labSum['10'],$labCount['11'],$labSum['11'],$labCount['12'],$labSum['12'],$labCount['13'],$labSum['13'],$labCount['14'],$labSum['14'],$labCount['15'],$labSum['15'],$labCount['16'],$labSum['16'],$labCount['17'],$labSum['17'],$labCount['18'],$labSum['18'],$labCount['19'],$labSum['19'],$labCount['20'],$labSum['20'],$labCount['21'],$labSum['21'],$labCount['22'],$labSum['22'],$labCount['23'],$labSum['23'],$labCount['24'],$labSum['24'],$labCount['25'],$labSum['25'],$labCount['26'],$labSum['26'],$labCount['27'],$labSum['27'],$labCount['28'],$labSum['28'],$labCount['29'],$labSum['29'],$labCount['30'],$labSum['30'],$labCount['31'],$labSum['31']));
			$worksheet->writeRow($x++,0,array('Imaging','','','','','',$radCount['01'],$radSum['01'],$radCount['02'],$radSum['02'],$radCount['03'],$radSum['03'],$radCount['04'],$radSum['04'],$radCount['05'],$radSum['05'],$radCount['06'],$radSum['06'],$radCount['07'],$radSum['07'],$radCount['08'],$radSum['08'],$radCount['09'],$radSum['09'],$radCount['10'],$radSum['10'],$radCount['11'],$radSum['11'],$radCount['12'],$radSum['12'],$radCount['13'],$radSum['13'],$radCount['14'],$radSum['14'],$radCount['15'],$radSum['15'],$radCount['16'],$radSum['16'],$radCount['17'],$radSum['17'],$radCount['18'],$radSum['18'],$radCount['19'],$radSum['19'],$radCount['20'],$radSum['20'],$radCount['21'],$radSum['21'],$radCount['22'],$radSum['22'],$radCount['23'],$radSum['23'],$radCount['24'],$radSum['24'],$radCount['25'],$radSum['25'],$radCount['26'],$radSum['26'],$radCount['27'],$radSum['27'],$radCount['28'],$radSum['28'],$radCount['29'],$radSum['29'],$radCount['30'],$radSum['30'],$radCount['31'],$radSum['31']));
			$worksheet->writeRow($x++,0,array('Consultation','','','','','',$conCount['01'],$conSum['01'],$conCount['02'],$conSum['02'],$conCount['03'],$conSum['03'],$conCount['04'],$conSum['04'],$conCount['05'],$conSum['05'],$conCount['06'],$conSum['06'],$conCount['07'],$conSum['07'],$conCount['08'],$conSum['08'],$conCount['09'],$conSum['09'],$conCount['10'],$conSum['10'],$conCount['11'],$conSum['11'],$conCount['12'],$conSum['12'],$conCount['13'],$conSum['13'],$conCount['14'],$conSum['14'],$conCount['15'],$conSum['15'],$conCount['16'],$conSum['16'],$conCount['17'],$conSum['17'],$conCount['18'],$conSum['18'],$conCount['19'],$conSum['19'],$conCount['20'],$conSum['20'],$conCount['21'],$conSum['21'],$conCount['22'],$conSum['22'],$conCount['23'],$conSum['23'],$conCount['24'],$conSum['24'],$conCount['25'],$conSum['25'],$conCount['26'],$conSum['26'],$conCount['27'],$conSum['27'],$conCount['28'],$conSum['28'],$conCount['29'],$conSum['29'],$conCount['30'],$conSum['30'],$conCount['31'],$conSum['31']));
			$worksheet->writeRow($x++,0,array('Package','','','','','',$pckCount['01'],$pckSum['01'],$pckCount['02'],$pckSum['02'],$pckCount['03'],$pckSum['03'],$pckCount['04'],$pckSum['04'],$pckCount['05'],$pckSum['05'],$pckCount['06'],$pckSum['06'],$pckCount['07'],$pckSum['07'],$pckCount['08'],$pckSum['08'],$pckCount['09'],$pckSum['09'],$pckCount['10'],$pckSum['10'],$pckCount['11'],$pckSum['11'],$pckCount['12'],$pckSum['12'],$pckCount['13'],$pckSum['13'],$pckCount['14'],$pckSum['14'],$pckCount['15'],$pckSum['15'],$pckCount['16'],$pckSum['16'],$pckCount['17'],$pckSum['17'],$pckCount['18'],$pckSum['18'],$pckCount['19'],$pckSum['19'],$pckCount['20'],$pckSum['20'],$pckCount['21'],$pckSum['21'],$pckCount['22'],$pckSum['22'],$pckCount['23'],$pckSum['23'],$pckCount['24'],$pckSum['24'],$pckCount['25'],$pckSum['25'],$pckCount['26'],$pckSum['26'],$pckCount['27'],$pckSum['27'],$pckCount['28'],$pckSum['28'],$pckCount['29'],$pckSum['29'],$pckCount['30'],$pckSum['30'],$pckCount['31'],$pckSum['31']));
			$worksheet->writeRow($x++,0,array('Miscellaneous','','','','','',$misCount['01'],$misSum['01'],$misCount['02'],$misSum['02'],$misCount['03'],$misSum['03'],$misCount['04'],$misSum['04'],$misCount['05'],$misSum['05'],$misCount['06'],$misSum['06'],$misCount['07'],$misSum['07'],$misCount['08'],$misSum['08'],$misCount['09'],$misSum['09'],$misCount['10'],$misSum['10'],$misCount['11'],$misSum['11'],$misCount['12'],$misSum['12'],$misCount['13'],$misSum['13'],$misCount['14'],$misSum['14'],$misCount['15'],$misSum['15'],$misCount['16'],$misSum['16'],$misCount['17'],$misSum['17'],$misCount['18'],$misSum['18'],$misCount['19'],$misSum['19'],$misCount['20'],$misSum['20'],$misCount['21'],$misSum['21'],$misCount['22'],$misSum['22'],$misCount['23'],$misSum['23'],$misCount['24'],$misSum['24'],$misCount['25'],$misSum['25'],$misCount['26'],$misSum['26'],$misCount['27'],$misSum['27'],$misCount['28'],$misSum['28'],$misCount['29'],$misSum['29'],$misCount['30'],$misSum['30'],$misCount['31'],$misSum['31']));
			$worksheet->writeRow($x++,0,array('Sub-Total','','','','','',$CashSumCount['01'],$CashTotalSum['01'],$CashSumCount['02'],$CashTotalSum['02'],$CashSumCount['03'],$CashTotalSum['03'],$CashSumCount['04'],$CashTotalSum['04'],$CashSumCount['05'],$CashTotalSum['05'],$CashSumCount['06'],$CashTotalSum['06'],$CashSumCount['07'],$CashTotalSum['07'],$CashSumCount['08'],$CashTotalSum['08'],$CashSumCount['09'],$CashTotalSum['09'],$CashSumCount['10'],$CashTotalSum['10'],$CashSumCount['11'],$CashTotalSum['11'],$CashSumCount['12'],$CashTotalSum['12'],$CashSumCount['13'],$CashTotalSum['13'],$CashSumCount['14'],$CashTotalSum['14'],$CashSumCount['15'],$CashTotalSum['15'],$CashSumCount['16'],$CashTotalSum['16'],$CashSumCount['17'],$CashTotalSum['17'],$CashSumCount['18'],$CashTotalSum['18'],$CashSumCount['19'],$CashTotalSum['19'],$CashSumCount['20'],$CashTotalSum['20'],$CashSumCount['21'],$CashTotalSum['21'],$CashSumCount['22'],$CashTotalSum['22'],$CashSumCount['23'],$CashTotalSum['23'],$CashSumCount['24'],$CashTotalSum['24'],$CashSumCount['25'],$CashTotalSum['25'],$CashSumCount['26'],$CashTotalSum['26'],$CashSumCount['27'],$CashTotalSum['27'],$CashSumCount['28'],$CashTotalSum['28'],$CashSumCount['29'],$CashTotalSum['29'],$CashSumCount['30'],$CashTotalSum['30'],$CashSumCount['31'],$CashTotalSum['31']));
		
		$x++;
		
			
			$worksheet->writeRow($x++,0,array('HMO/Corporate'), $format_top_center);
			
			$worksheet->writeRow($x++,0,array('Laboratory','','','','','',$labCountHC['01'],$labSumHC['01'],$labCountHC['02'],$labSumHC['02'],$labCountHC['03'],$labSumHC['03'],$labCountHC['04'],$labSumHC['04'],$labCountHC['05'],$labSumHC['05'],$labCountHC['06'],$labSumHC['06'],$labCountHC['07'],$labSumHC['07'],$labCountHC['08'],$labSumHC['08'],$labCountHC['09'],$labSumHC['09'],$labCountHC['10'],$labSumHC['10'],$labCountHC['11'],$labSumHC['11'],$labCountHC['12'],$labSumHC['12'],$labCountHC['13'],$labSumHC['13'],$labCountHC['14'],$labSumHC['14'],$labCountHC['15'],$labSumHC['15'],$labCountHC['16'],$labSumHC['16'],$labCountHC['17'],$labSumHC['17'],$labCountHC['18'],$labSumHC['18'],$labCountHC['19'],$labSumHC['19'],$labCountHC['20'],$labSumHC['20'],$labCountHC['21'],$labSumHC['21'],$labCountHC['22'],$labSumHC['22'],$labCountHC['23'],$labSumHC['23'],$labCountHC['24'],$labSumHC['24'],$labCountHC['25'],$labSumHC['25'],$labCountHC['26'],$labSumHC['26'],$labCountHC['27'],$labSumHC['27'],$labCountHC['28'],$labSumHC['28'],$labCountHC['29'],$labSumHC['29'],$labCountHC['30'],$labSumHC['30'],$labCountHC['31'],$labSumHC['31']));
			$worksheet->writeRow($x++,0,array('Imaging','','','','','',$radCountHC['01'],$radSumHC['01'],$radCountHC['02'],$radSumHC['02'],$radCountHC['03'],$radSumHC['03'],$radCountHC['04'],$radSumHC['04'],$radCountHC['05'],$radSumHC['05'],$radCountHC['06'],$radSumHC['06'],$radCountHC['07'],$radSumHC['07'],$radCountHC['08'],$radSumHC['08'],$radCountHC['09'],$radSumHC['09'],$radCountHC['10'],$radSumHC['10'],$radCountHC['11'],$radSumHC['11'],$radCountHC['12'],$radSumHC['12'],$radCountHC['13'],$radSumHC['13'],$radCountHC['14'],$radSumHC['14'],$radCountHC['15'],$radSumHC['15'],$radCountHC['16'],$radSumHC['16'],$radCountHC['17'],$radSumHC['17'],$radCountHC['18'],$radSumHC['18'],$radCountHC['19'],$radSumHC['19'],$radCountHC['20'],$radSumHC['20'],$radCountHC['21'],$radSumHC['21'],$radCountHC['22'],$radSumHC['22'],$radCountHC['23'],$radSumHC['23'],$radCountHC['24'],$radSumHC['24'],$radCountHC['25'],$radSumHC['25'],$radCountHC['26'],$radSumHC['26'],$radCountHC['27'],$radSumHC['27'],$radCountHC['28'],$radSumHC['28'],$radCountHC['29'],$radSumHC['29'],$radCountHC['30'],$radSumHC['30'],$radCountHC['31'],$radSumHC['31']));
			$worksheet->writeRow($x++,0,array('Consultation','','','','','',$conCountHC['01'],$conSumHC['01'],$conCountHC['02'],$conSumHC['02'],$conCountHC['03'],$conSumHC['03'],$conCountHC['04'],$conSumHC['04'],$conCountHC['05'],$conSumHC['05'],$conCountHC['06'],$conSumHC['06'],$conCountHC['07'],$conSumHC['07'],$conCountHC['08'],$conSumHC['08'],$conCountHC['09'],$conSumHC['09'],$conCountHC['10'],$conSumHC['10'],$conCountHC['11'],$conSumHC['11'],$conCountHC['12'],$conSumHC['12'],$conCountHC['13'],$conSumHC['13'],$conCountHC['14'],$conSumHC['14'],$conCountHC['15'],$conSumHC['15'],$conCountHC['16'],$conSumHC['16'],$conCountHC['17'],$conSumHC['17'],$conCountHC['18'],$conSumHC['18'],$conCountHC['19'],$conSumHC['19'],$conCountHC['20'],$conSumHC['20'],$conCountHC['21'],$conSumHC['21'],$conCountHC['22'],$conSumHC['22'],$conCountHC['23'],$conSumHC['23'],$conCountHC['24'],$conSumHC['24'],$conCountHC['25'],$conSumHC['25'],$conCountHC['26'],$conSumHC['26'],$conCountHC['27'],$conSumHC['27'],$conCountHC['28'],$conSumHC['28'],$conCountHC['29'],$conSumHC['29'],$conCountHC['30'],$conSumHC['30'],$conCountHC['31'],$conSumHC['31']));
			$worksheet->writeRow($x++,0,array('Package','','','','','',$pckCountHC['01'],$pckSumHC['01'],$pckCountHC['02'],$pckSumHC['02'],$pckCountHC['03'],$pckSumHC['03'],$pckCountHC['04'],$pckSumHC['04'],$pckCountHC['05'],$pckSumHC['05'],$pckCountHC['06'],$pckSumHC['06'],$pckCountHC['07'],$pckSumHC['07'],$pckCountHC['08'],$pckSumHC['08'],$pckCountHC['09'],$pckSumHC['09'],$pckCountHC['10'],$pckSumHC['10'],$pckCountHC['11'],$pckSumHC['11'],$pckCountHC['12'],$pckSumHC['12'],$pckCountHC['13'],$pckSumHC['13'],$pckCountHC['14'],$pckSumHC['14'],$pckCountHC['15'],$pckSumHC['15'],$pckCountHC['16'],$pckSumHC['16'],$pckCountHC['17'],$pckSumHC['17'],$pckCountHC['18'],$pckSumHC['18'],$pckCountHC['19'],$pckSumHC['19'],$pckCountHC['20'],$pckSumHC['20'],$pckCountHC['21'],$pckSumHC['21'],$pckCountHC['22'],$pckSumHC['22'],$pckCountHC['23'],$pckSumHC['23'],$pckCountHC['24'],$pckSumHC['24'],$pckCountHC['25'],$pckSumHC['25'],$pckCountHC['26'],$pckSumHC['26'],$pckCountHC['27'],$pckSumHC['27'],$pckCountHC['28'],$pckSumHC['28'],$pckCountHC['29'],$pckSumHC['29'],$pckCountHC['30'],$pckSumHC['30'],$pckCountHC['31'],$pckSumHC['31']));
			$worksheet->writeRow($x++,0,array('Miscellaneous','','','','','',$misCountHC['01'],$misSumHC['01'],$misCountHC['02'],$misSumHC['02'],$misCountHC['03'],$misSumHC['03'],$misCountHC['04'],$misSumHC['04'],$misCountHC['05'],$misSumHC['05'],$misCountHC['06'],$misSumHC['06'],$misCountHC['07'],$misSumHC['07'],$misCountHC['08'],$misSumHC['08'],$misCountHC['09'],$misSumHC['09'],$misCountHC['10'],$misSumHC['10'],$misCountHC['11'],$misSumHC['11'],$misCountHC['12'],$misSumHC['12'],$misCountHC['13'],$misSumHC['13'],$misCountHC['14'],$misSumHC['14'],$misCountHC['15'],$misSumHC['15'],$misCountHC['16'],$misSumHC['16'],$misCountHC['17'],$misSumHC['17'],$misCountHC['18'],$misSumHC['18'],$misCountHC['19'],$misSumHC['19'],$misCountHC['20'],$misSumHC['20'],$misCountHC['21'],$misSumHC['21'],$misCountHC['22'],$misSumHC['22'],$misCountHC['23'],$misSumHC['23'],$misCountHC['24'],$misSumHC['24'],$misCountHC['25'],$misSumHC['25'],$misCountHC['26'],$misSumHC['26'],$misCountHC['27'],$misSumHC['27'],$misCountHC['28'],$misSumHC['28'],$misCountHC['29'],$misSumHC['29'],$misCountHC['30'],$misSumHC['30'],$misCountHC['31'],$misSumHC['31']));
			$worksheet->writeRow($x++,0,array('Sub-Total','','','','','',$CashSumCountHC['01'],$CashTotalSumHC['01'],$CashSumCountHC['02'],$CashTotalSumHC['02'],$CashSumCountHC['03'],$CashTotalSumHC['03'],$CashSumCountHC['04'],$CashTotalSumHC['04'],$CashSumCountHC['05'],$CashTotalSumHC['05'],$CashSumCountHC['06'],$CashTotalSumHC['06'],$CashSumCountHC['07'],$CashTotalSumHC['07'],$CashSumCountHC['08'],$CashTotalSumHC['08'],$CashSumCountHC['09'],$CashTotalSumHC['09'],$CashSumCountHC['10'],$CashTotalSumHC['10'],$CashSumCountHC['11'],$CashTotalSumHC['11'],$CashSumCountHC['12'],$CashTotalSumHC['12'],$CashSumCountHC['13'],$CashTotalSumHC['13'],$CashSumCountHC['14'],$CashTotalSumHC['14'],$CashSumCountHC['15'],$CashTotalSumHC['15'],$CashSumCountHC['16'],$CashTotalSumHC['16'],$CashSumCountHC['17'],$CashTotalSumHC['17'],$CashSumCountHC['18'],$CashTotalSumHC['18'],$CashSumCountHC['19'],$CashTotalSumHC['19'],$CashSumCountHC['20'],$CashTotalSumHC['20'],$CashSumCountHC['21'],$CashTotalSumHC['21'],$CashSumCountHC['22'],$CashTotalSumHC['22'],$CashSumCountHC['23'],$CashTotalSumHC['23'],$CashSumCountHC['24'],$CashTotalSumHC['24'],$CashSumCountHC['25'],$CashTotalSumHC['25'],$CashSumCountHC['26'],$CashTotalSumHC['26'],$CashSumCountHC['27'],$CashTotalSumHC['27'],$CashSumCountHC['28'],$CashTotalSumHC['28'],$CashSumCountHC['29'],$CashTotalSumHC['29'],$CashSumCountHC['30'],$CashTotalSumHC['30'],$CashSumCountHC['31'],$CashTotalSumHC['31']));
		
		$x++;
			
			$worksheet->writeRow($x++,0,array('TOTAL DAILY SALES'), $format_top_center);
			
			$clinicCodeALL = $request->input('Clinic') != 'ALL';
			$clinicCodeArray = is_array($clinicCode) ? $clinicCode : [$clinicCode];

			$QueDataPatient = DB::connection('CMS')->select(" SELECT tb1.`Id`, tb1.`Date`	
					,( select count(*) from
						(select tb1a.`IdPatient` from CMS.`Queue` tb1a  where
						tb1a.`Status` >= '210' and
						tb1a.`Status` <= '640'  and
						tb1a.`Date` = tb1.`Date` and
						(CASE WHEN '".!empty($clinicCodeArray)."' AND '".$clinicCodeALL."' THEN tb1a.`IdBU` = '".$request->input('Clinic')."' ELSE tb1a.`IdBU` IN ('".implode("','", $clinicCodeArray)."') END)
						GROUP BY tb1a.IdPatient) as tb1aA
					) as pCountPerDay 
					FROM CMS.`Queue` tb1
					where  
					tb1.`Date` >= '".$dateFrom."' and
					tb1.`Date` <= '".$dateTo."' and
					tb1.`Status` >= '210' and
					tb1.`Status` <= '640' and
					(CASE WHEN '".!empty($clinicCodeArray)."' AND '".$clinicCodeALL."' THEN tb1.`IdBU` = '".$request->input('Clinic')."' ELSE tb1.`IdBU` IN ('".implode("','", $clinicCodeArray)."') END)
					GROUP BY tb1.`Date`
				");

			$pCountPerDay['01'] = $pCountPerDay['02'] = $pCountPerDay['03'] = $pCountPerDay['04'] = $pCountPerDay['05'] = $pCountPerDay['06'] = $pCountPerDay['07'] = $pCountPerDay['08'] = $pCountPerDay['09'] = $pCountPerDay['10'] = $pCountPerDay['11'] = $pCountPerDay['12'] = $pCountPerDay['13'] = $pCountPerDay['14'] = $pCountPerDay['15'] = $pCountPerDay['16'] = $pCountPerDay['17'] = $pCountPerDay['18'] = $pCountPerDay['19'] = $pCountPerDay['20'] = $pCountPerDay['21'] = $pCountPerDay['22'] = $pCountPerDay['23']= $pCountPerDay['24']= $pCountPerDay['25']= $pCountPerDay['26']= $pCountPerDay['27']= $pCountPerDay['28']= $pCountPerDay['29']= $pCountPerDay['30']= $pCountPerDay['31']="";
			
			foreach($QueDataPatient as $pDayCount)
			{
				$pCountPerDay[date('d', strtotime($pDayCount->Date))]  = 0;
			}
			foreach($QueDataPatient as $pDayCount)
			{
				$pCountPerDay[date('d', strtotime($pDayCount->Date))] += floatval($pDayCount->pCountPerDay);
			}
			
			$worksheet->writeRow($x++,0,array('Patient Count','','','','','',$pCountPerDay['01'],'',$pCountPerDay['02'],'',$pCountPerDay['03'],'',$pCountPerDay['04'],'',$pCountPerDay['05'],'',$pCountPerDay['06'],'',$pCountPerDay['07'],'',$pCountPerDay['08'],'',$pCountPerDay['09'],'',$pCountPerDay['10'],'',$pCountPerDay['11'],'',$pCountPerDay['12'],'',$pCountPerDay['13'],'',$pCountPerDay['14'],'',$pCountPerDay['15'],'',$pCountPerDay['16'],'',$pCountPerDay['17'],'',$pCountPerDay['18'],'',$pCountPerDay['19'],'',$pCountPerDay['20'],'',$pCountPerDay['21'],'',$pCountPerDay['22'],'',$pCountPerDay['23'],'',$pCountPerDay['24'],'',$pCountPerDay['25'],'',$pCountPerDay['26'],'',$pCountPerDay['27'],'',$pCountPerDay['28'],'',$pCountPerDay['29'],'',$pCountPerDay['30'],'',$pCountPerDay['31'],''));
			
			$worksheet->writeRow($x++,0,array('Laboratory','','','','','',$labCountGtotal['01'],$labSumGtotal['01'],$labCountGtotal['02'],$labSumGtotal['02'],$labCountGtotal['03'],$labSumGtotal['03'],$labCountGtotal['04'],$labSumGtotal['04'],$labCountGtotal['05'],$labSumGtotal['05'],$labCountGtotal['06'],$labSumGtotal['06'],$labCountGtotal['07'],$labSumGtotal['07'],$labCountGtotal['08'],$labSumGtotal['08'],$labCountGtotal['09'],$labSumGtotal['09'],$labCountGtotal['10'],$labSumGtotal['10'],$labCountGtotal['11'],$labSumGtotal['11'],$labCountGtotal['12'],$labSumGtotal['12'],$labCountGtotal['13'],$labSumGtotal['13'],$labCountGtotal['14'],$labSumGtotal['14'],$labCountGtotal['15'],$labSumGtotal['15'],$labCountGtotal['16'],$labSumGtotal['16'],$labCountGtotal['17'],$labSumGtotal['17'],$labCountGtotal['18'],$labSumGtotal['18'],$labCountGtotal['19'],$labSumGtotal['19'],$labCountGtotal['20'],$labSumGtotal['20'],$labCountGtotal['21'],$labSumGtotal['21'],$labCountGtotal['22'],$labSumGtotal['22'],$labCountGtotal['23'],$labSumGtotal['23'],$labCountGtotal['24'],$labSumGtotal['24'],$labCountGtotal['25'],$labSumGtotal['25'],$labCountGtotal['26'],$labSumGtotal['26'],$labCountGtotal['27'],$labSumGtotal['27'],$labCountGtotal['28'],$labSumGtotal['28'],$labCountGtotal['29'],$labSumGtotal['29'],$labCountGtotal['30'],$labSumGtotal['30'],$labCountGtotal['31'],$labSumGtotal['31']));
			$worksheet->writeRow($x++,0,array('Imaging','','','','','',$radCountGtotal['01'],$radSumGtotal['01'],$radCountGtotal['02'],$radSumGtotal['02'],$radCountGtotal['03'],$radSumGtotal['03'],$radCountGtotal['04'],$radSumGtotal['04'],$radCountGtotal['05'],$radSumGtotal['05'],$radCountGtotal['06'],$radSumGtotal['06'],$radCountGtotal['07'],$radSumGtotal['07'],$radCountGtotal['08'],$radSumGtotal['08'],$radCountGtotal['09'],$radSumGtotal['09'],$radCountGtotal['10'],$radSumGtotal['10'],$radCountGtotal['11'],$radSumGtotal['11'],$radCountGtotal['12'],$radSumGtotal['12'],$radCountGtotal['13'],$radSumGtotal['13'],$radCountGtotal['14'],$radSumGtotal['14'],$radCountGtotal['15'],$radSumGtotal['15'],$radCountGtotal['16'],$radSumGtotal['16'],$radCountGtotal['17'],$radSumGtotal['17'],$radCountGtotal['18'],$radSumGtotal['18'],$radCountGtotal['19'],$radSumGtotal['19'],$radCountGtotal['20'],$radSumGtotal['20'],$radCountGtotal['21'],$radSumGtotal['21'],$radCountGtotal['22'],$radSumGtotal['22'],$radCountGtotal['23'],$radSumGtotal['23'],$radCountGtotal['24'],$radSumGtotal['24'],$radCountGtotal['25'],$radSumGtotal['25'],$radCountGtotal['26'],$radSumGtotal['26'],$radCountGtotal['27'],$radSumGtotal['27'],$radCountGtotal['28'],$radSumGtotal['28'],$radCountGtotal['29'],$radSumGtotal['29'],$radCountGtotal['30'],$radSumGtotal['30'],$radCountGtotal['31'],$radSumGtotal['31']));
			$worksheet->writeRow($x++,0,array('Consultation','','','','','',$conCountGtotal['01'],$conSumGtotal['01'],$conCountGtotal['02'],$conSumGtotal['02'],$conCountGtotal['03'],$conSumGtotal['03'],$conCountGtotal['04'],$conSumGtotal['04'],$conCountGtotal['05'],$conSumGtotal['05'],$conCountGtotal['06'],$conSumGtotal['06'],$conCountGtotal['07'],$conSumGtotal['07'],$conCountGtotal['08'],$conSumGtotal['08'],$conCountGtotal['09'],$conSumGtotal['09'],$conCountGtotal['10'],$conSumGtotal['10'],$conCountGtotal['11'],$conSumGtotal['11'],$conCountGtotal['12'],$conSumGtotal['12'],$conCountGtotal['13'],$conSumGtotal['13'],$conCountGtotal['14'],$conSumGtotal['14'],$conCountGtotal['15'],$conSumGtotal['15'],$conCountGtotal['16'],$conSumGtotal['16'],$conCountGtotal['17'],$conSumGtotal['17'],$conCountGtotal['18'],$conSumGtotal['18'],$conCountGtotal['19'],$conSumGtotal['19'],$conCountGtotal['20'],$conSumGtotal['20'],$conCountGtotal['21'],$conSumGtotal['21'],$conCountGtotal['22'],$conSumGtotal['22'],$conCountGtotal['23'],$conSumGtotal['23'],$conCountGtotal['24'],$conSumGtotal['24'],$conCountGtotal['25'],$conSumGtotal['25'],$conCountGtotal['26'],$conSumGtotal['26'],$conCountGtotal['27'],$conSumGtotal['27'],$conCountGtotal['28'],$conSumGtotal['28'],$conCountGtotal['29'],$conSumGtotal['29'],$conCountGtotal['30'],$conSumGtotal['30'],$conCountGtotal['31'],$conSumGtotal['31']));
			$worksheet->writeRow($x++,0,array('Package','','','','','',$pckCountGtotal['01'],$pckSumGtotal['01'],$pckCountGtotal['02'],$pckSumGtotal['02'],$pckCountGtotal['03'],$pckSumGtotal['03'],$pckCountGtotal['04'],$pckSumGtotal['04'],$pckCountGtotal['05'],$pckSumGtotal['05'],$pckCountGtotal['06'],$pckSumGtotal['06'],$pckCountGtotal['07'],$pckSumGtotal['07'],$pckCountGtotal['08'],$pckSumGtotal['08'],$pckCountGtotal['09'],$pckSumGtotal['09'],$pckCountGtotal['10'],$pckSumGtotal['10'],$pckCountGtotal['11'],$pckSumGtotal['11'],$pckCountGtotal['12'],$pckSumGtotal['12'],$pckCountGtotal['13'],$pckSumGtotal['13'],$pckCountGtotal['14'],$pckSumGtotal['14'],$pckCountGtotal['15'],$pckSumGtotal['15'],$pckCountGtotal['16'],$pckSumGtotal['16'],$pckCountGtotal['17'],$pckSumGtotal['17'],$pckCountGtotal['18'],$pckSumGtotal['18'],$pckCountGtotal['19'],$pckSumGtotal['19'],$pckCountGtotal['20'],$pckSumGtotal['20'],$pckCountGtotal['21'],$pckSumGtotal['21'],$pckCountGtotal['22'],$pckSumGtotal['22'],$pckCountGtotal['23'],$pckSumGtotal['23'],$pckCountGtotal['24'],$pckSumGtotal['24'],$pckCountGtotal['25'],$pckSumGtotal['25'],$pckCountGtotal['26'],$pckSumGtotal['26'],$pckCountGtotal['27'],$pckSumGtotal['27'],$pckCountGtotal['28'],$pckSumGtotal['28'],$pckCountGtotal['29'],$pckSumGtotal['29'],$pckCountGtotal['30'],$pckSumGtotal['30'],$pckCountGtotal['31'],$pckSumGtotal['31']));
			$worksheet->writeRow($x++,0,array('Miscellaneous','','','','','',$misCountGtotal['01'],$misSumGtotal['01'],$misCountGtotal['02'],$misSumGtotal['02'],$misCountGtotal['03'],$misSumGtotal['03'],$misCountGtotal['04'],$misSumGtotal['04'],$misCountGtotal['05'],$misSumGtotal['05'],$misCountGtotal['06'],$misSumGtotal['06'],$misCountGtotal['07'],$misSumGtotal['07'],$misCountGtotal['08'],$misSumGtotal['08'],$misCountGtotal['09'],$misSumGtotal['09'],$misCountGtotal['10'],$misSumGtotal['10'],$misCountGtotal['11'],$misSumGtotal['11'],$misCountGtotal['12'],$misSumGtotal['12'],$misCountGtotal['13'],$misSumGtotal['13'],$misCountGtotal['14'],$misSumGtotal['14'],$misCountGtotal['15'],$misSumGtotal['15'],$misCountGtotal['16'],$misSumGtotal['16'],$misCountGtotal['17'],$misSumGtotal['17'],$misCountGtotal['18'],$misSumGtotal['18'],$misCountGtotal['19'],$misSumGtotal['19'],$misCountGtotal['20'],$misSumGtotal['20'],$misCountGtotal['21'],$misSumGtotal['21'],$misCountGtotal['22'],$misSumGtotal['22'],$misCountGtotal['23'],$misSumGtotal['23'],$misCountGtotal['24'],$misSumGtotal['24'],$misCountGtotal['25'],$misSumGtotal['25'],$misCountGtotal['26'],$misSumGtotal['26'],$misCountGtotal['27'],$misSumGtotal['27'],$misCountGtotal['28'],$misSumGtotal['28'],$misCountGtotal['29'],$misSumGtotal['29'],$misCountGtotal['30'],$misSumGtotal['30'],$misCountGtotal['31'],$misSumGtotal['31']));
			$worksheet->writeRow($x++,0,array('Sub-Total','','','','','',$CashSumCountGtotal['01'],$CashTotalSumGtotal['01'],$CashSumCountGtotal['02'],$CashTotalSumGtotal['02'],$CashSumCountGtotal['03'],$CashTotalSumGtotal['03'],$CashSumCountGtotal['04'],$CashTotalSumGtotal['04'],$CashSumCountGtotal['05'],$CashTotalSumGtotal['05'],$CashSumCountGtotal['06'],$CashTotalSumGtotal['06'],$CashSumCountGtotal['07'],$CashTotalSumGtotal['07'],$CashSumCountGtotal['08'],$CashTotalSumGtotal['08'],$CashSumCountGtotal['09'],$CashTotalSumGtotal['09'],$CashSumCountGtotal['10'],$CashTotalSumGtotal['10'],$CashSumCountGtotal['11'],$CashTotalSumGtotal['11'],$CashSumCountGtotal['12'],$CashTotalSumGtotal['12'],$CashSumCountGtotal['13'],$CashTotalSumGtotal['13'],$CashSumCountGtotal['14'],$CashTotalSumGtotal['14'],$CashSumCountGtotal['15'],$CashTotalSumGtotal['15'],$CashSumCountGtotal['16'],$CashTotalSumGtotal['16'],$CashSumCountGtotal['17'],$CashTotalSumGtotal['17'],$CashSumCountGtotal['18'],$CashTotalSumGtotal['18'],$CashSumCountGtotal['19'],$CashTotalSumGtotal['19'],$CashSumCountGtotal['20'],$CashTotalSumGtotal['20'],$CashSumCountGtotal['21'],$CashTotalSumGtotal['21'],$CashSumCountGtotal['22'],$CashTotalSumGtotal['22'],$CashSumCountGtotal['23'],$CashTotalSumGtotal['23'],$CashSumCountGtotal['24'],$CashTotalSumGtotal['24'],$CashSumCountGtotal['25'],$CashTotalSumGtotal['25'],$CashSumCountGtotal['26'],$CashTotalSumGtotal['26'],$CashSumCountGtotal['27'],$CashTotalSumGtotal['27'],$CashSumCountGtotal['28'],$CashTotalSumGtotal['28'],$CashSumCountGtotal['29'],$CashTotalSumGtotal['29'],$CashSumCountGtotal['30'],$CashTotalSumGtotal['30'],$CashSumCountGtotal['31'],$CashTotalSumGtotal['31']));
			$workbook->close();
			die();
			

	
	}
	######################### END Summary############################################
	######################### Cash sales ############################################
	else if($request->input('_repType')  == "cash")
	{
		$ymd = date("FjYgia");
		
		$workbook = new \Spreadsheet_Excel_Writer();
		$workbook->send('CASH-SALES-'.$ymd.'.xls');
		$workbook->setVersion(8);
		$worksheet = $workbook->addWorksheet('ALL');
		$worksheet->setInputEncoding('UTF-8');
		$numTextformat = $workbook->addFormat(array('setNumFormat'=>'@'));
		
		$number_format = $workbook->addFormat();
		$number_format->setNumFormat('0');
		
		// put text at the top and center it horizontally
		$format_top_center = $workbook->addFormat();
		$format_top_center->setAlign('top');
		$format_top_center->setAlign('center');
		$format_top_center->setVAlign('vjustify');
		$format_top_center->setVAlign('vcenter');
		$format_top_center->setBold (1);
		$format_top_center->setTextWrap(1);
		
		$worksheet->freezePanes(array(7, 5,NULL,NULL));
		
		$worksheet->setColumn(0,0,18); //BRANCH   A
		$worksheet->setColumn(1,1,12); //DATE B
		$worksheet->setColumn(2,2,20); // OR NUMBER C
		$worksheet->setColumn(3,3,30);  // PATIENT NAME D
		$worksheet->setColumn(4,4,20);  //  PAYMENT TYPE E
		$worksheet->setColumn(5,5,20); // CASH  F
  		$worksheet->setColumn(6,6,20);  // CHECK  G
		$worksheet->setColumn(7,7,20); // CREDIT / DEBIT CARD    H
		$worksheet->setColumn(8,8,20);  // GCASH  I
		$worksheet->setColumn(9,9,20);  // ONLINE J
		
		
			$x = 6;
			
			$header = array('BRANCH', 'DATE', 'OR NUMBER', 'PATIENT NAME', 'PAYMENT TYPE', 'CASH AMOUNT', 'CHECK AMOUNT', 'CREDIT / DEBIT CARD AMOUNT',  'GCASH AMOUNT', 'ONLINE AMOUNT');
			$worksheet->writeRow($x++,0,$header, $format_top_center);
			
			$dateFrom = $request->input('dateFrom');
			$dateTo = $request->input('dateTo');
			$QueData = DB::connection('CMS')->table('Queue')
				->leftjoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id')
				->leftjoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '640');
				//->where('Queue.IdBU',  session('userClinicCode'))
				if (!empty($clinicCode)) {
					$request->input('Clinic') != "ALL"
						? $QueData->where('Queue.IdBU', $clinicCode)
						: $QueData->whereIn('Queue.IdBU', $clinicCode);
				}
				$QueData = $QueData->groupBy('Queue.Id')
				->orderBy('Queue.Code')
				->get(array('Queue.Id as QueId','Queue.Code as QCode', 'Queue.Date as QDate', 'Queue.IdPatient', 'Eros.Patient.FullName as PName', 'Patient.Code as PCode', 'Queue.IdBU as Branch', 'Queue.IdBU as ClinicCode'));
		
			$grossAmount = 0;
			$discountAmount = 0;
			$netAmount = 0;
			$cash = 0;
			$Gcash = 0;
			$credit = 0;
			$cheque = 0;
			$online = 0;
			$PatientCount = 0;
			foreach($QueData as $data)
			{
				// group into bill to
				$billGroup = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $data->QueId)->groupBy('BillTo')->get(array('BillTo'));
				foreach($billGroup as $billTo)
				{ 
					$ORNumbers = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $data->QueId)->groupBy('ORNum')->get(array('ORNum')); 
					foreach($ORNumbers as $OR) {
					$trans = DB::connection('CMS')->select("
					SELECT
						tb1a.`IdQueue`,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'Cash' THEN tb1a.`CoverageAmount` ELSE 0 END) AS Cash,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'GCash' THEN tb1a.`CoverageAmount` ELSE 0 END) AS GCash,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'Credit' THEN tb1a.`CoverageAmount` ELSE 0 END) AS Credit,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'Cheque' THEN tb1a.`CoverageAmount` ELSE 0 END) AS Cheque,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'Online' THEN tb1a.`CoverageAmount` ELSE 0 END) AS Online,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'Discount' THEN tb1a.`CoverageAmount` ELSE 0 END) AS Discount,
						GROUP_CONCAT(DISTINCT CASE WHEN tb1a.`PaymentType` != 'Discount' THEN tb1a.`ORNum` END SEPARATOR '/') AS ORNum,
						GROUP_CONCAT(DISTINCT CASE WHEN tb1a.`PaymentType` != 'Discount' THEN tb1a.`PaymentType` END SEPARATOR '/') AS PaymentType
					FROM CMS.`PaymentHistory` tb1a  
					WHERE
						tb1a.`Status` != '2' AND
						tb1a.`IdQueue` = '".$data->QueId."' AND
						tb1a.`BillTo` = '".$billTo->BillTo."' AND
						tb1a.`ORNum` = '".$OR->ORNum."' AND
						tb1a.`PaymentType` IN ('Cash', 'GCash', 'Credit', 'Cheque', 'Online', 'Discount')
					GROUP BY tb1a.`IdQueue`
					");
					foreach ($trans as $datas) {
						$cash += $datas->Cash;
						$Gcash += $datas->GCash;
						$credit += $datas->Credit;
						$online += $datas->Online;
						$cheque += $datas->Cheque;
						$discountAmount += $datas->Discount;
					
						$grossAmount = $cash + $Gcash + $credit + $online + $cheque + $discountAmount;
						$netAmount = $cash + $Gcash + $credit + $online + $cheque;

						$worksheet->writeRow($x++, 0, array(
							$data->ClinicCode, // Branch
							$data->QDate, // Date
							$datas->ORNum, // OR Number
							$data->PName, // Patient name
							$datas->PaymentType, //
							round($datas->Cash,2), // CASH
							round($datas->Cheque,2), // CHECK
							round($datas->Credit,2), // CREDIT / DEBIT CARD
							round($datas->GCash,2), // GCASH
							round($datas->Online,2), // ONLINE
						));
					}
					
				}
						
				}
			}
			$TotalPatient = DB::connection('CMS')->table('Queue')
				->leftjoin('PaymentHistory', 'Queue.Id', '=', 'PaymentHistory.IdQueue')
				->where('PaymentHistory.Status', '!=', '2')
				->where('PaymentHistory.ProviderType', 'PATIENT')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '640');
				if (!empty($clinicCode)) {
					$request->input('Clinic') != "ALL"
						? $TotalPatient->where('Queue.IdBU', $clinicCode)
						: $TotalPatient->whereIn('Queue.IdBU', $clinicCode);
				}
				$TotalPatient = $TotalPatient->groupBy('Queue.IdPatient')
				->get(array('Queue.IdPatient'));
				$worksheet->writeRow(0,0,array('Total Patient Count', count($TotalPatient), '', 'Cash Amount', number_format($cash, 2)));
				$worksheet->writeRow(1,0,array('Total Gross Cash Amount', number_format($grossAmount, 2), '', 'G-Cash Amount' , number_format($Gcash, 2)));
				$worksheet->writeRow(2,0,array('Total Discount Cash Amount', number_format($discountAmount, 2) , '', 'Credit Amount', number_format($credit, 2)));
				$worksheet->writeRow(3,0,array('Total Net Cash Amount', number_format($netAmount, 2), '', 'Online Amount', number_format($online, 2)));
				$worksheet->writeRow(4,0,array('','','', 'Cheque Amount', number_format($cheque, 2)));
				$worksheet->writeRow(5,0,array('Date From: '.date('m/d/Y', strtotime($dateFrom)), 'To: '. date('m/d/Y', strtotime($dateTo))));
				$workbook->close();
				die();
	}
	######################### Cash sales END############################################
	######################### Cashier summary ############################################
	else if($request->input('_repType')  == "cashier")
	{
		$ymd = date("FjYgia");
		
		$workbook = new \Spreadsheet_Excel_Writer();
		$workbook->send('CASHIER-SUMMARY-REPORT-'.$ymd.'.xls');
		$workbook->setVersion(8);
		$worksheet = $workbook->addWorksheet('ALL');
		$worksheet->setInputEncoding('UTF-8');
		$numTextformat = $workbook->addFormat(array('setNumFormat'=>'@'));
		
		$number_format = $workbook->addFormat();
		$number_format->setNumFormat('0');

	
		$format_top_center = $workbook->addFormat();
		$format_top_center->setAlign('top');
		$format_top_center->setAlign('center');
		$format_top_center->setVAlign('vjustify');
		$format_top_center->setVAlign('vcenter');
		$format_top_center->setBold (1);
		$format_top_center->setTextWrap(1);
		//$worksheet->getActiveSheet()->setAutoFilter('A1:E20');
		$worksheet->freezePanes(array(8, 5,NULL,NULL));
		
		$worksheet->setColumn(0,0,19); //BRANCH   A
		$worksheet->setColumn(1,1,12); //DATE B
		$worksheet->setColumn(2,2,20); // TRX NO. C
		$worksheet->setColumn(3,3,20);  // OR NUMBER D
		$worksheet->setColumn(4,4,40);  //  PATIENT NAME E
		$worksheet->setColumn(5,5,50); // COMPANY NAME F
		$worksheet->setColumn(6,6,50);  // GUARANTOR NAME G
		$worksheet->setColumn(7,7,20); // PAYMENT TYPE H
		$worksheet->setColumn(8,8,20);  // CASH AMOUNT I
		$worksheet->setColumn(9,9,20);  // CHECK AMOUNT J
		$worksheet->setColumn(10,10,20);  // CREDIT/DEBIT CARD AMOUNT K
		$worksheet->setColumn(11,11,20);  // GCASH AMOUNT L
		$worksheet->setColumn(12,12,20);  // REF NO. M
		$worksheet->setColumn(13,13,20);  // ONLINE AMOUNT N
		$worksheet->setColumn(14,14,20);  // HMO/CORPORATE AMOUNT O
		$worksheet->setColumn(15,15,20);  // USER NAME P
		
		
			$x = 7;
			
			$header = array('BRANCH', 'DATE', 'TRX NO.', 'OR NUMBER', 'PATIENT NAME', 'COMPANY NAME', 'GUARANTOR NAME', 'PAYMENT TYPE', 'CASH AMOUNT', 'CHECK AMOUNT', 'CREDIT / DEBIT CARD AMOUNT',  'GCASH AMOUNT', 'REFERENCE NO','ONLINE AMOUNT', 'HMO/CORPORATE AMOUNT', 'USER NAME (CASHIER)');
			$worksheet->writeRow($x++,0,$header, $format_top_center);
		
			$dateFrom = $request->input('dateFrom');
			$dateTo = $request->input('dateTo');
			$QueData = DB::connection('CMS')->table('Queue')
				->leftjoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id')
				->leftjoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '640');
				//->where('Queue.IdBU',  session('userClinicCode'))
				if (!empty($clinicCode)) {
					$request->input('Clinic') != "ALL"
						? $QueData->where('Queue.IdBU', $clinicCode)
						: $QueData->whereIn('Queue.IdBU', $clinicCode);
				}
				$QueData = $QueData->groupBy('Queue.Id')
				->orderBy('Queue.Code')
				->get(array('Queue.Id as QueId','Queue.Code as QCode', 'Queue.Date as QDate', 'Queue.IdPatient', 'Eros.Patient.FullName as PName', 'Patient.Code as PCode', 'Queue.IdBU as Branch', 'Queue.IdBU as ClinicCode'));
		
			$grossAmount = 0;
			$discountAmount = 0;
			$netAmount = 0;
			$cash = 0;
			$Gcash = 0;
			$credit = 0;
			$cheque = 0;
			$online = 0;
			$PatientCount = 0;
			$copay = 0;
			foreach($QueData as $data)
			{
				// group into bill to
				$transIdCheck = 0;
				$billGroup = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $data->QueId)->groupBy('BillTo')->get(array('BillTo'));
				foreach($billGroup as $billTo)
				{ 
					$ORNumbers = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $data->QueId)->groupBy('ORNum')->get(array('ORNum')); 
					$GuarantorName = DB::connection('Eros')->table('Company')->where('Id', $billTo->BillTo)->get(array('Name'));
					$companyName = DB::connection('CMS')->table('Transactions')->where('IdQueue', $data->QueId)->groupBy('IdQueue')->get(array('NameCompany'));
					$refNo = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $data->QueId)->whereIn('PaymentType',['Credit','GCash', 'Cheque', 'Online'])->groupBy('IdQueue')->get(array('RefNo'));
					foreach($ORNumbers as $OR) {
					$trans = DB::connection('CMS')->select("
					SELECT
						tb1a.`IdQueue`,
						tb1a.`InputBy`,
						tb1a.`BillTo`,
						tb1a.`RefNo`,
						tb1a.`CoverageAmount`,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'Cash' THEN tb1a.`CoverageAmount` ELSE 0 END) AS Cash,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'GCash' THEN tb1a.`CoverageAmount` ELSE 0 END) AS GCash,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'Credit' THEN tb1a.`CoverageAmount` ELSE 0 END) AS Credit,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'Cheque' THEN tb1a.`CoverageAmount` ELSE 0 END) AS Cheque,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'Online' THEN tb1a.`CoverageAmount` ELSE 0 END) AS Online,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'Discount' THEN tb1a.`CoverageAmount` ELSE 0 END) AS Discount,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'CoPay' THEN tb1a.`CoverageAmount` ELSE 0 END) AS CoPay,
						GROUP_CONCAT(DISTINCT CASE WHEN tb1a.`PaymentType` != 'Discount' AND tb1a.`PaymentType` != 'CoPay' THEN tb1a.`ORNum` END SEPARATOR '/') AS ORNum,
						GROUP_CONCAT(DISTINCT CASE WHEN tb1a.`PaymentType` != 'Discount' AND tb1a.`PaymentType` != 'CoPay' THEN tb1a.`PaymentType` WHEN tb1a.`PaymentType` = 'Copay' AND tb1a.`CoverageAmount` != '0' THEN 'Charge' END SEPARATOR '/') AS PaymentType
					FROM CMS.`PaymentHistory` tb1a
					WHERE
						tb1a.`Status` != '2' AND
						tb1a.`IdQueue` = '".$data->QueId."' AND
						tb1a.`BillTo` = '".$billTo->BillTo."' AND
						tb1a.`ORNum` = '".$OR->ORNum."' AND
						tb1a.`PaymentType` IN ('Cash', 'GCash', 'Credit', 'Cheque', 'Online', 'CoPay', 'Discount')
					GROUP BY tb1a.`IdQueue`
					");
					
					foreach ($trans as $datas) {
						$cash += $datas->Cash;
						$Gcash += $datas->GCash;
						$credit += $datas->Credit;
						$online += $datas->Online;
						$cheque += $datas->Cheque;
						$copay += $datas->CoPay;
						$discountAmount += $datas->Discount;
					
						$grossAmount = $cash + $Gcash + $credit + $online + $cheque + $discountAmount + $copay;
						$netAmount = $cash + $Gcash + $credit + $online + $cheque + $copay;

						$worksheet->writeRow($x++, 0, array(
							$data->ClinicCode, // BRANCH
							$data->QDate, // DATE
							$data->QCode, //TRANSACTION NO
							$datas->ORNum, // OR NUMBER
							$data->PName, // PATIENT NAME
							(!empty($companyName))? $companyName[0]->NameCompany : '',   //COMPANY NAME
							($GuarantorName->isNotEmpty())? $GuarantorName[0]->Name : '',//GUARANTOR NAME\
							$datas->PaymentType, // PAYMENT TYPE
							round($datas->Cash, 2), // CASH AMOUNT
							round($datas->Cheque, 2), // CHECK AMOUNT
							round($datas->Credit, 2), // CREDIT / DEBIT CARD
							round($datas->GCash, 2), // GCASH
							(!empty($refNo[0]->RefNo))? $refNo[0]->RefNo : '',//GCASH REF NO
							round($datas->Online, 2), // ONLINE AMOUNT
							round($datas->CoPay, 2), // HMO/CORPORATE AMOUNT
							$datas->InputBy//USER NAME
						));
					}
					
				}
						
				}
			}
			$TotalPatient = DB::connection('CMS')->table('Queue')
				->leftjoin('PaymentHistory', 'Queue.Id', '=', 'PaymentHistory.IdQueue')
				->where('PaymentHistory.Status', '!=', '2')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '640');
				if (!empty($clinicCode)) {
					$request->input('Clinic') != "ALL"
						? $TotalPatient->where('Queue.IdBU', $clinicCode)
						: $TotalPatient->whereIn('Queue.IdBU', $clinicCode);
				}
				$TotalPatient = $TotalPatient->groupBy('Queue.IdPatient')
				->get(array('Queue.IdPatient'));
				$worksheet->writeRow(0,0,array('Total Patient Count', count($TotalPatient), '', 'Cash Amount', number_format($cash, 2,'.', ',')));
				$worksheet->writeRow(1,0,array('Total Gross Amount', number_format($grossAmount, 2,'.', ','), '', 'G-Cash Amount' , number_format($Gcash, 2,'.', ',')));
				$worksheet->writeRow(2,0,array('Total Discount Amount', number_format($discountAmount, 2,'.', ',') , '', 'Credit Amount', number_format($credit, 2,'.', ',')));
				$worksheet->writeRow(3,0,array('Total Net Amount', number_format($netAmount, 2,'.', ','), '', 'Online Amount', number_format($online, 2,'.', ',')));
				$worksheet->writeRow(4,0,array('','','', 'Cheque Amount', number_format($cheque, 2,'.', ',')));
				$worksheet->writeRow(5,0,array('Date From: '.date('m/d/Y', strtotime($dateFrom)), 'To: '. date('m/d/Y', strtotime($dateTo)), '', 'HMO/Corporate Amount', number_format($copay, 2, '.', ',')));
				$workbook->close();
				die();
	}
	######################### Cashier summary END############################################
	######################### HMO and Corporate ############################################
	else if($request->input('_repType') == 'HmoCorporate')
	{
			$ymd = date("FjYgia");
		
		$workbook = new \Spreadsheet_Excel_Writer();
		$workbook->send('HMO-Corporate-SALES-'.$ymd.'.xls');
		$workbook->setVersion(8);
		$worksheet = $workbook->addWorksheet('ALL');
		$worksheet->setInputEncoding('UTF-8');
		$numTextformat = $workbook->addFormat(array('setNumFormat'=>'@'));
		
		$number_format = $workbook->addFormat();
		$number_format->setNumFormat('0');
		
		// put text at the top and center it horizontally
		$format_top_center = $workbook->addFormat();
		$format_top_center->setAlign('top');
		$format_top_center->setAlign('center');
		$format_top_center->setVAlign('vjustify');
		$format_top_center->setVAlign('vcenter');
		$format_top_center->setBold (1);
		$format_top_center->setTextWrap(1);
		
		$worksheet->freezePanes(array(7, 5,NULL,NULL));
		
		$worksheet->setColumn(0,0,18); //BRANCH   A
		$worksheet->setColumn(1,1,12); //DATE B
		$worksheet->setColumn(2,2,20); // PATIENT ID C
		$worksheet->setColumn(3,3,30);  // PATIENT NAME D
		$worksheet->setColumn(4,4,40);  //  GUARANTOR NAME E
		$worksheet->setColumn(5,5,20); // PAYMENT TYPE  F
  		$worksheet->setColumn(6,6,20);  // GROSS AMOUNT  G
		$worksheet->setColumn(7,7,20);  // DISCOUNT AMOUNT  H
		$worksheet->setColumn(8,8,20);  // DISCOUNT TYPE  I
		$worksheet->setColumn(9,9,20);  // NET AMOUNT  G

			$x = 6;
			
			$header = array('BRANCH', 'DATE', 'TRANSACTION NO.', 'PATIENT ID', 'PATIENT NAME', 'GUARANTOR NAME', 'PAYMENT TYPE', 'GROSS AMOUNT', 'DISCOUNT TYPE', 'DISCOUNT AMOUNT', 'NET AMOUNT');
			$worksheet->writeRow($x++,0,$header, $format_top_center);
			
			$dateFrom = $request->input('dateFrom');
			$dateTo = $request->input('dateTo');
			$QueData = DB::connection('CMS')->table('Queue')
				->leftjoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
				->leftjoin('Transactions', 'Queue.Id', '=', 'Transactions.IdQueue')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '640');
				//->where('Queue.IdBU',  session('userClinicCode'))
				if (!empty($clinicCode)) {
					$request->input('Clinic') != "ALL"
						? $QueData->where('Queue.IdBU', $clinicCode)
						: $QueData->whereIn('Queue.IdBU', $clinicCode);
				}
				$QueData = $QueData->groupBy('Queue.Id')
				->orderBy('Queue.Code')
				->get(array('Queue.Id as QueId', 'Queue.Date as QDate','Eros.Patient.FullName as PName', 'Eros.Patient.Id as PID', 'Queue.Notes as Notes', 'Queue.Code as trxno', 'Queue.IdBU as ClinicCode' )); 
				
				$GrossAmount = 0;
				$DiscountAmount = 0;
				$NetAmount = 0;
				$hmo = 0;
				$Corporate = 0;
				foreach($QueData as $data)
				{
					// group into bill to
					$transIdCheck = 0;
					$billGroup = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $data->QueId)->whereIn('ProviderType', ['HMO', 'Corporate'])->groupBy('BillTo')->get();
					$ihmo = 0; $iCorporate = 0; $iGross = 0; $iDiscountAmount = 0; $iNetAmount = 0;
					foreach($billGroup as $billTo)
					{ 	
					$trans = DB::connection('CMS')->select("
					SELECT
						tb1a.`IdQueue`,
						tb1a.`CoverageAmount`,
						tb1a.`ProviderType`,
						tb2.`Name` AS ProviderName,
						CONCAT(CASE WHEN tb1a.`CoverageType` NOT IN ('HMO', 'Corporate') THEN tb1a.`CoverageType` ELSE '' END) AS DiscountType,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'CoPay' AND tb1a.`ProviderType` IN ('Corporate', 'HMO') THEN tb1a.`CoverageAmount` ELSE 0 END) AS NetAmount,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'Discount' AND tb1a.`ProviderType` IN ('Corporate', 'HMO') THEN tb1a.`CoverageAmount` ELSE 0 END) AS Discount,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'CoPay' AND tb1a.`ProviderType` LIKE 'Corporate' THEN tb1a.`CoverageAmount` ELSE 0 END) AS Corporate,
						SUM(CASE WHEN tb1a.`PaymentType` LIKE 'CoPay' AND tb1a.`ProviderType` LIKE 'HMO' THEN tb1a.`CoverageAmount` ELSE 0 END) AS HMO,
						GROUP_CONCAT(DISTINCT CASE WHEN tb1a.`PaymentType` != 'Discount' AND tb1a.`PaymentType` != 'CoPay' THEN tb1a.`PaymentType` END SEPARATOR '/') AS PaymentType
					FROM CMS.`PaymentHistory` tb1a
					LEFT JOIN Eros.Company tb2 ON tb1a.`BillTo` = tb2.`Id`
					WHERE
						tb1a.`Status` != '2' AND
						tb1a.`IdQueue` = '".$data->QueId."' AND
						tb1a.`BillTo` = '".$billTo->BillTo."'
					GROUP BY tb1a.`IdQueue`;
					");	
					foreach($trans as $hisT)
						{
							$iGross = $hisT->NetAmount + $hisT->Discount;
							$DiscountAmount += $hisT->Discount;
							$NetAmount += $hisT->NetAmount;
							$GrossAmount += $iGross;
							$Corporate += $hisT->Corporate;
							$hmo += $hisT->HMO;
							$worksheet->writeRow($x++, 0, array(
								$data->ClinicCode, // BRANCH
								$data->QDate, // DATE
								$data->trxno, // TRANSACTION NO.
								$data->PID, // PATIENT ID
								$data->PName, // PATIENT NAME
								$hisT->ProviderName, // GUARANTOR NAME
								$hisT->ProviderType, //PAYMENT TYPE
								round($iGross,2), //GROSS
								$hisT->DiscountType, //DISCOUNT TYPE
								round($hisT->Discount,2),//$iDiscountAmount, //DISCOUNT AMOUNT
								round($hisT->NetAmount,2)//$iNetAmount, //NET AMOUNT
							));
						
						}
					}
				}

				$TotalPatient = DB::connection('CMS')->table('Queue')
				->leftjoin('PaymentHistory', 'Queue.Id', '=', 'PaymentHistory.IdQueue')
				->whereIn('PaymentHistory.BillTo', function($query) {
					$query->select('BillTo')
						  ->from('PaymentHistory')
						  ->where('ProviderType', 'HMO')
						  ->orWhere('ProviderType', 'Corporate')
						  ->groupBy('BillTo');
				})
				->where('PaymentHistory.Status', '!=', '2')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '640');
				if (!empty($clinicCode)) {
					$request->input('Clinic') != "ALL"
						? $TotalPatient->where('Queue.IdBU', $clinicCode)
						: $TotalPatient->whereIn('Queue.IdBU', $clinicCode);
				}
				$TotalPatient = $TotalPatient->groupBy('Queue.IdPatient')
				->get(array('Queue.IdPatient'));
					
					$worksheet->writeRow(0, 0, array('Total Patient Count', count($TotalPatient), '', 'HMO Amount: ' . number_format($hmo, 2, '.', ',')));
					$worksheet->writeRow(1,0,array('Total Gross Amount', number_format($GrossAmount, 2,'.', ','), '', 'Corporate Amount: ' . number_format($Corporate, 2,'.', ',')));
					$worksheet->writeRow(2,0,array('Total Discount Amount', number_format($DiscountAmount, 2,'.', ',')));
					$worksheet->writeRow(3,0,array('Total Net Amount', number_format($NetAmount, 2,'.', ',')));
					$worksheet->writeRow(4,0,array('Date and Time:',   date('m/d/Y H:i:s', strtotime($dateFrom)). ' - ' . date('m/d/Y H:i:s', strtotime($dateTo))));
					$workbook->close();
					die();
	}
	else if($request->input('_repType') == 'sendout')
	{
			$ymd = date("FjYgia");
		
		$workbook = new \Spreadsheet_Excel_Writer();
		$workbook->send('Sendout-Report-'.$ymd.'.xls');
		$workbook->setVersion(8);
		$worksheet = $workbook->addWorksheet('ALL');
		$worksheet->setInputEncoding('UTF-8');
		$numTextformat = $workbook->addFormat(array('setNumFormat'=>'@'));
		
		$number_format = $workbook->addFormat();
		$number_format->setNumFormat('0');
		
		// put text at the top and center it horizontally
		$format_top_center = $workbook->addFormat();
		$format_top_center->setAlign('top');
		$format_top_center->setAlign('center');
		$format_top_center->setVAlign('vjustify');
		$format_top_center->setVAlign('vcenter');
		$format_top_center->setBold (1);
		$format_top_center->setTextWrap(1);
		
		$worksheet->freezePanes(array(6, 5,NULL,NULL));
		
		$worksheet->setColumn(0,0,20); // BRANCH  A
		$worksheet->setColumn(1,1,12); // DATE B
		$worksheet->setColumn(2,2,20); // PATIENT ID  C
		$worksheet->setColumn(3,3,30); // PATIENT NAME D
		$worksheet->setColumn(4,4,40); // ITEM/EXAMINATION E
		$worksheet->setColumn(5,5,40); // INTERNAL NOTES F

			$x = 5;
			$header = array('BRANCH', 'DATE', 'PATIENT ID', 'PATIENT NAME', 'ITEM/EXAMINATION');
			$worksheet->writeRow($x++,0,$header, $format_top_center);
			
			$dateFrom = $request->input('dateFrom');
			$dateTo = $request->input('dateTo');
			$QueData = DB::connection('CMS')->table('Queue')
				->leftjoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
				->leftjoin('Transactions', 'Queue.Id', '=', 'Transactions.IdQueue')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '640')
				//->where('Queue.IdBU',  session('userClinicCode'))
				->groupBy('Queue.Id')
				->orderBy('Queue.Code')
				->get(array('Queue.Id as QueId', 'Queue.Date as QDate','Eros.Patient.FullName as PName', 'Eros.Patient.Id as PID', 'Queue.Notes as Notes', 'Transactions.CodeItemPrice as Code', 'Queue.IdBU as ClinicCode')); 
			$grossAmount = 0;
			$discountAmount = 0;
			$netAmount = 0;
			$cash = 0;
			$Gcash = 0;
			$credit = 0;
			$cheque = 0;
			$online = 0;
				foreach($QueData as $data)
				{
					//sendout item
					$sendoutItem = DB::connection('Eros')->table('ItemMaster')
					->leftjoin('CMS.Transactions', 'Transactions.CodeItemPrice','=','ItemMaster.Code')
					->where('ItemMaster.OldSubGroup', 'SO')
					->where('ItemMaster.SubGroup', 'NOT LIKE', '%MOL%')
					->where('Transactions.IdQueue', $data->QueId)
					->get(array('Description'));
					$QueuId = array($data->PName);
					foreach($sendoutItem as $itemDescription)
					{
							$worksheet->writeRow($x++, 0, array(
								$data->ClinicCode, // Branch
								$data->QDate, // Date
								$data->PID, // PATIENT ID
								$data->PName, // Patient name
								$itemDescription->Description, // Test Name
							//	$data->Notes, //Internal Notes
							));
					}
							
				}
				// $TotalPatient = DB::connection('CMS')->table('Queue')
				// ->leftjoin('Transactions', 'Queue.Id', '=', 'Transactions.IdQueue')
				// ->leftjoin('Eros.ItemMaster', 'Transactions.CodeItemPrice', '=','Eros.ItemMaster.Code')
				// ->where('Queue.Date', '>=', $dateFrom)
				// ->where('Queue.Date', '<=', $dateTo)
				// ->where('CMS.Queue.Status', '>=', '210')
				// ->where('CMS.Queue.Status', '<>', '640')
				// ->where('Eros.ItemMaster.OldSubGroup', 'SO')
				// ->where('Eros.ItemMaster.SubGroup', 'NOT LIKE', '%MOL%')
				// ->groupBy('Queue.IdPatient')
				// ->get(array('Queue.IdPatient'));

				// $worksheet->writeRow(0,0,array('Total Patient Count', count($TotalPatient), '', 'Cash Amount', number_format($cash, 2,'.', ',')));
				// $worksheet->writeRow(1,0,array('Total Gross Amount', number_format($grossAmount, 2,'.', ','), '', 'G-Cash Amount' , number_format($Gcash, 2,'.', ',')));
				// $worksheet->writeRow(2,0,array('Total Discount Amount', number_format($discountAmount, 2,'.', ',') , '', 'Credit Amount', number_format($credit, 2,'.', ',')));
				// $worksheet->writeRow(3,0,array('Total Net Amount', number_format($netAmount, 2,'.', ','), '', 'Online Amount', number_format($online, 2,'.', ',')));
				// $worksheet->writeRow(4,0,array('Date and Time:',   date('m/d/Y H:i:s', strtotime($dateFrom)). ' - ' . date('m/d/Y H:i:s', strtotime($dateTo)),'', 'Cheque Amount', number_format($cheque, 2,'.', ',')));
				$workbook->close();
				die();
	}
	
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
