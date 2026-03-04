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

class ReportsLABController extends Controller
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
		return view('cms.reportslab',['Clinics' => $clinic, 'clinicName' => $clinicName, 'ClinicCode' => $clinicCode]);
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
	if($request->input('_repType')  == "labAPEPEME" )
	{
		$dateFrom = $request->input('dateFrom');
		$dateTo = $request->input('dateTo');
	
		$ymd = date("FjYgia");
		
		$workbook = new \Spreadsheet_Excel_Writer();
		$workbook->send('LAB APE and PEME-'.$ymd.'.xls');
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
		
		$worksheet->setColumn(0,0,18); //Order DT
		$worksheet->setColumn(1,1,12.57); //Lab No
		$worksheet->setColumn(2,2, 10); // Priority
		$worksheet->setColumn(3,3,10);  // Source
		$worksheet->setColumn(4,4,20);  // PID
		$worksheet->setColumn(5,5,48.57); // Patient Name
  		$worksheet->setColumn(6,6,10);  // Gender
		$worksheet->setColumn(7,7,14); // DOB
		$worksheet->setColumn(8,8,10);  // Age
		$worksheet->setColumn(9,9,14.58);  // Status
		$worksheet->setColumn(10,10, 14.57);  //CBC
		$worksheet->setColumn(11,11,14.57);  // FECA
		$worksheet->setColumn(12,12,14.57);  // URINE
		$worksheet->setColumn(13,13,21.14);  //
		
		$worksheet->writeRow(0,0,array('ORDER SUMMARY LIST')); 
		$worksheet->writeRow(1,0,array('Date From: '.date('m/d/Y H:i:s', strtotime($dateFrom)) . 'To: '. date('m/d/Y H:i:s', strtotime($dateTo)) ) );
		
			$x = 5;
			
			$header = array('Order Date', 'Lab No', 'Priority', 'Source', 'PID', 'Patient Name', 'Gender', 'DOB (mm/dd/yyyy)' ,'Age', 'Status', 'CBC', 'FECA', 'URINE');
			$worksheet->writeRow($x++,0,$header, $format_top_center);
			
			
			
			$QueData = DB::connection('CMS')->table('Queue')
				->leftjoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id')
				->leftjoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
				->leftjoin('Transactions', 'Queue.Id', '=', 'Transactions.IdQueue')
				->whereIn('Transactions.TransactionType', ['APE', 'PEME'])
				->where('Transactions.PriceGroupItemPrice', 'LIKE', 'Package')
				->where('Queue.Date', '>=', $dateFrom)
				->where('Queue.Date', '<=', $dateTo)
				->where('Queue.Status', '>=', '210')
				->where('Queue.Status', '<=', '600');
				if (!empty($clinicCode)) {
					$request->input('Clinic') != "ALL"
						? $QueData->where('Queue.IdBU', $clinicCode)
						: $QueData->whereIn('Queue.IdBU', $clinicCode);
				}
				//->where('Queue.Id', '2404')
				$QueData = $QueData
				->groupBy('Queue.Id', 'Transactions.Id')
				->orderBy('Queue.Code')
				->get(array('Queue.Id as QueId','Queue.Code as QCode', 'Queue.IdBU','Queue.Date as QDate', 'Queue.IdPatient', 'Patient.FullName as PName', 'Patient.Code as PCode', 'Patient.Gender', 'Patient.DOB', 'Queue.AgePatient' , 'Queue.IdBU as ClinicCode', 'Transactions.Id as TransId'));
			$myArray = array();
			foreach($QueData as $Qdata)
			{
				$AccData = DB::connection('CMS')->select("
					SELECT tb1.*,
					(
						SELECT a1.`Status`
						FROM `AccessionNo` a1  
						LEFT JOIN `QueueStatus` b2 ON(a1.`Status` = b2.`Id`)
						WHERE a1.`Type` LIKE 'LABORATORY' 
						AND a1.`Status` >= '210' 
						AND a1.`Status` <= '600'
						AND a1.`ItemCode` IN ('LH002', 'ACBC')
						AND a1.`IdQueue` = '".$Qdata->QueId."'
						AND a1.`IdTransaction` = '".$Qdata->TransId."'
					) as CBCStatus
					,
					(
						SELECT a1.`Status`
						FROM `AccessionNo` a1  
						LEFT JOIN `QueueStatus` b2 ON(a1.`Status` = b2.`Id`)
						WHERE a1.`Type` LIKE 'LABORATORY' 
						AND a1.`Status` >= '210' 
						AND a1.`Status` <= '600'
						AND a1.`ItemCode` IN ('LM001', 'AFECA')
						AND a1.`IdQueue` = '".$Qdata->QueId."'
						AND a1.`IdTransaction` = '".$Qdata->TransId."'
					) as FECAStatus
					,
					(
						SELECT  a1.`Status`
						FROM `AccessionNo` a1  
						LEFT JOIN `QueueStatus` b2 ON(a1.`Status` = b2.`Id`)
						WHERE a1.`Type` LIKE 'LABORATORY' 
						AND a1.`Status` >= '210' 
						AND a1.`Status` <= '600'
						AND a1.`ItemCode` IN ('LM007', 'AURINE')
						AND a1.`IdQueue` = '".$Qdata->QueId."'
						AND a1.`IdTransaction` = '".$Qdata->TransId."'
					) as URINEStatus
					FROM `AccessionNo` tb1  
					
					WHERE tb1.`Type` LIKE 'LABORATORY' 
					AND tb1.`Status` >= '210' 
					AND tb1.`Status` <= '600'
					AND tb1.`ItemCode` IN ('LH002', 'ACBC', 'LM001', 'AFECA', 'LM007', 'AURINE')
					AND tb1.`IdQueue` = '".$Qdata->QueId."'
					AND tb1.`IdTransaction` = '".$Qdata->TransId."'
					GROUP BY tb1.`Id`
					LIMIT 1
				");
				
				//dd($AccData);
				/*DB::connection('CMS')->table('AccessionNo')
				->leftjoin('QueueStatus', 'AccessionNo.Status', '=', 'QueueStatus.Id')
				->where('IdQueue', $Qdata->QueId)->where('AccessionNo.Type', 'LIKE', 'LABORATORY')->where('AccessionNo.Status', '>=','210')->where('AccessionNo.Status', '<=','600')->get(array('AccessionNo.*','QueueStatus.Name as AStatus'));
				*/
				foreach($AccData as $AData)
				{
					
				
					$AllStatus = "";
					$CBCStatus = (is_null($AData->CBCStatus)) ? -1 : 0 ; 
					$FECAStatus = (is_null($AData->FECAStatus)) ? -1 : 0 ; 
					$URINEStatus = (is_null($AData->URINEStatus)) ? -1 : 0 ; 
					if($AData->CBCStatus <=  '300' && $AData->FECAStatus <=  '300' && $AData->URINEStatus <= '300')
					{
						$AllStatus = "PENDING"; //echo '1';
						
					}
					else
					{
						
						if($AData->CBCStatus >=  '500' && $AData->CBCStatus !=  '900'  )
						{
							$CBCStatus = 1; //echo '2=>'.$AData->CBCStatus."<br>";
						}
						
						if($AData->FECAStatus >=  '500' && $AData->FECAStatus !=  '900'  )
						{
							$FECAStatus = 1; //echo '3=>'.$AData->FECAStatus."<br>";
						}
						
						if($AData->URINEStatus >=  '500' && $AData->URINEStatus !=  '900'   )
						{
							$URINEStatus = 1;  //echo '4=>'.$AData->URINEStatus."<br>";
						}
						
						if( $CBCStatus == 0 && $FECAStatus == 0 &&  $URINEStatus == 0)
						{
							$AllStatus = "PENDING"; //echo '5';
						}
						else if( ($CBCStatus == 1 || $CBCStatus == -1) && ($FECAStatus == 1 || $FECAStatus == -1) &&  ($URINEStatus == 1 || $URINEStatus == -1))
						{
							$AllStatus = "COMPLETED"; //echo '9';
						}
						//////////////////
						else if( $CBCStatus == 0 || $FECAStatus == 0 ||  $URINEStatus == 0)
						{
							$AllStatus = "PARTIAL"; //echo '6';
						}
						
						
					}
					//die();
					$worksheet->writeRow($x++,0,
						array(
							 $AData->SystemTimeCreated // Order DT
							,$AData->AccessionMap // LabNo
							,($AData->Stat == "Yes")? "P": "R" // Priority
							,$AData->IdBU //Source
							,$Qdata->PCode //Patient Code
							,$Qdata->PName //Patient Name
							,$Qdata->Gender //Patient Gender
							,date('m/d/Y', strtotime($Qdata->DOB))  //Patient DOB
							,$Qdata->AgePatient."Y" //Queue AgePatient
							, $AllStatus  //$AData->AStatus //Accession Status
							,($CBCStatus == 0) ? 'PENDING' : (($CBCStatus == 1) ? 'COMPLETED' : '')   //Accession CBC
							,($FECAStatus == 0) ? 'PENDING' : (($FECAStatus == 1) ? 'COMPLETED' : '')   //Accession FECA
							,($URINEStatus == 0) ? 'PENDING' : (($URINEStatus == 1) ? 'COMPLETED' : '')  //Accession URINE
						)
					);
				
				}
			}
			
			
			
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
