<?php

namespace App\Http\Controllers\hl7;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DTUHL7Controller extends Controller
{
	public function HL7() // change to push data from CMS to EROS
	{
		$BUCode = 'DTU';
		
		if (DB::connection('CMS')->getDatabaseName()) 
		{
			DB::connection('CMS')->beginTransaction();
	
			$qTrans =  DB::connection('CMS')->table('Queue')
									//->leftJoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
									->where('Queue.Status', '=','210')
									->where('Queue.IdBU', 'LIKE', $BUCode)
									->where('Queue.Date', '=', date('Y-m-d'))
									//->where('Queue.ErosStatus', 'NOT LIKE', 'reUpdate')
									//->where('Queue.Id', '=', '674183')
									->limit(1)
									->orderBy('Queue.Id', 'DESC')
									->get(array('Queue.Id as QId'));
									
			foreach($qTrans as $que)
			{
				echo $que->QId. "<br>";
				
				$trans = DB::connection('CMS')->table('Transactions')->where('IdQueue', $que->QId)->where('Status','210')->groupBy('IdDoctor', 'IdCompany')->get(array('*'));
				//echo "<pre>";
				//print_r($trans);
				//echo "</pre>";
				//die();
				foreach($trans as $trx)
				{
					
					//$alphaCount =  DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->get(array('*'));
					//$AccessionData =  DB::connection('CMS')->table('AccessionNo')->where('Type', 'LIKE', 'LAB')->where('IdQueue', $que->QId)->where('IdCompany', $trx->IdCompany)->where('IdDoctor', $trx->IdDoctor)->get(array('*'));
					
					//if( count($alphaCount) == 0 )
					//{ 
						echo "ITEM<br>";
						$ItemTrans = DB::connection('CMS')->table('Transactions')
						->join('Eros.ItemMaster', function($q)
						{
							$q->ON('Transactions.CodeItemPrice', '=', 'Eros.ItemMaster.Code')
							->where('Eros.ItemMaster.Group', 'LIKE', 'CLINIC');
						})
						->where('Transactions.IdQueue', $que->QId)->where('Transactions.Status','210')->where('Transactions.IdCompany', $trx->IdCompany)->where('Transactions.IdDoctor', $trx->IdDoctor)->where('Transactions.PriceGroupItemPrice', 'LIKE', 'Item')
						->get(array('Transactions.Id','Transactions.CodeItemPrice as AItemCode', 'Transactions.DescriptionItemPrice as AItemDescription', 'Transactions.Stat',  'Eros.ItemMaster.Group as AItemGroup', 'Eros.ItemMaster.SubGroup as AItemSubGroup', 'Eros.ItemMaster.LISCode as ALISCode'));
						
						foreach($ItemTrans as $itemT)
						{
							//remove subgroup
							if( $itemT->AItemGroup == 'CLINIC')
							{
								DB::connection('CMS')->table('AccessionNo')->insert(['IdBU' => $BUCode, 'IdTransaction' => $itemT->Id,  'IdQueue' => $que->QId, 'IdCompany' => $trx->IdCompany, 'IdDoctor' =>$trx->IdDoctor, 'Type' => $itemT->AItemGroup , 'ItemGroup' => $itemT->AItemGroup ,  'Stat' => $itemT->Stat, 'LISCode' => $itemT->ALISCode,  'ItemCode' => $itemT->AItemCode, 'ItemDescription' => $itemT->AItemDescription, 'Status' => 280 ]);
							}
							
							DB::connection('CMS')->table('Transactions')->where('Id', $itemT->Id)->update(['Status' => 280]);
						}
					
						echo "ITEM<br>";
						$ItemTrans = DB::connection('CMS')->table('Transactions')
						->join('Eros.ItemMaster', function($q)
						{
							$q->ON('Transactions.CodeItemPrice', '=', 'Eros.ItemMaster.Code')
							->where('Eros.ItemMaster.Group', 'LIKE', 'LABORATORY')
							->ORON('Transactions.CodeItemPrice', '=', 'Eros.ItemMaster.Code')
							->where('Eros.ItemMaster.Group', 'LIKE', 'IMAGING');
						})
						->where('Transactions.IdQueue', $que->QId)->where('Transactions.Status','210')->where('Transactions.IdCompany', $trx->IdCompany)->where('Transactions.IdDoctor', $trx->IdDoctor)->where('Transactions.PriceGroupItemPrice', 'LIKE', 'Item')
						->get(array('Transactions.Id','Transactions.CodeItemPrice as AItemCode', 'Transactions.DescriptionItemPrice as AItemDescription', 'Transactions.Stat',  'Eros.ItemMaster.Group as AItemGroup', 'Eros.ItemMaster.SubGroup as AItemSubGroup', 'Eros.ItemMaster.LISCode as ALISCode'));
						
						foreach($ItemTrans as $itemT)
						{
							//remove subgroup
							if( $itemT->AItemGroup == 'LABORATORY')
							{
								DB::connection('CMS')->table('AccessionNo')->insert(['IdBU' => $BUCode, 'IdTransaction' => $itemT->Id,  'IdQueue' => $que->QId, 'IdCompany' => $trx->IdCompany, 'IdDoctor' =>$trx->IdDoctor, 'Type' => $itemT->AItemGroup , 'ItemGroup' => $itemT->AItemGroup ,  'Stat' => $itemT->Stat, 'LISCode' => $itemT->ALISCode,  'ItemCode' => $itemT->AItemCode, 'ItemDescription' => $itemT->AItemDescription, 'Status' => 250 ]);
							}
							else
							{
								DB::connection('CMS')->table('AccessionNo')->insert(['IdBU' => $BUCode, 'IdTransaction' => $itemT->Id,  'IdQueue' => $que->QId, 'IdCompany' => $trx->IdCompany, 'IdDoctor' =>$trx->IdDoctor, 'Type' => $itemT->AItemGroup , 'ItemGroup' => $itemT->AItemGroup , 'ItemSubGroup' => $itemT->AItemSubGroup, 'Stat' => $itemT->Stat, 'LISCode' => $itemT->ALISCode,  'ItemCode' => $itemT->AItemCode, 'ItemDescription' => $itemT->AItemDescription, 'Status' => 250 ]);
							}
							DB::connection('CMS')->table('Transactions')->where('Id', $itemT->Id)->update(['Status' => 250]);
						}
						
						
						echo "STANDARD PACKAGE<br>";
						//Eros.ItemMaster.StandardPackage =  1'
						$PackageTrans = DB::connection('CMS')->table('Transactions')
						->join('Eros.ItemMaster', function($q)
						{
							$q->ON('Transactions.CodeItemPrice', '=', 'Eros.ItemMaster.Code')	
							->where('Eros.ItemMaster.StandardPackage', '=', 1);
						})
						->join('Eros.StandardPackage', function($q)
						{
							$q->ON('Transactions.CodeItemPrice', '=',  'Eros.StandardPackage.ItemMasterPackageCode')
							->where('Eros.StandardPackage.ItemMasterGroup', 'LIKE', 'LABORATORY')
							->ORON('Transactions.CodeItemPrice', '=',  'Eros.StandardPackage.ItemMasterPackageCode')
							->where('Eros.StandardPackage.ItemMasterGroup', 'LIKE', 'IMAGING')
							->ORON('Transactions.CodeItemPrice', '=',  'Eros.StandardPackage.ItemMasterPackageCode')
							->where('Eros.StandardPackage.ItemMasterGroup', 'LIKE', 'CLINIC');
						})		
						->where('Transactions.IdQueue', $que->QId)->where('Transactions.Status','210')->where('Transactions.IdCompany', $trx->IdCompany)->where('Transactions.IdDoctor', $trx->IdDoctor)
						->where('Transactions.PriceGroupItemPrice', 'LIKE', 'Package')
						->get(array('Transactions.Id','Transactions.CodeItemPrice', 'Transactions.DescriptionItemPrice', 'Eros.StandardPackage.ItemMasterItemCode as AItemCode' ,'Eros.StandardPackage.ItemMasterDescription as AItemDescription', 'Eros.StandardPackage.ItemMasterGroup as AItemGroup', 'Eros.ItemMaster.SubGroup as AItemSubGroup'));
						
						$standID = 0;
						foreach($PackageTrans as $packT)
						{
							//reGet Item Sub Group 
							$ItemMasterData = DB::connection('Eros')->table('ItemMaster')->where('Code', $packT->AItemCode)->get(array('SubGroup','LISCode'))[0];
						
							if( $packT->AItemGroup == 'LABORATORY')
							{
								DB::connection('CMS')->table('AccessionNo')->insert(['IdBU' => $BUCode, 'IdTransaction' => $packT->Id ,'IdQueue' => $que->QId, 'IdCompany' => $trx->IdCompany, 'IdDoctor' =>$trx->IdDoctor, 'Type' => $packT->AItemGroup , 'ItemGroup' => $packT->AItemGroup ,  'LISCode' => $ItemMasterData->LISCode, 'ItemCode' => $packT->AItemCode, 'ItemDescription' => $packT->AItemDescription, 'Status' => 250 ]);
							}
							else
							{
								DB::connection('CMS')->table('AccessionNo')->insert(['IdBU' => $BUCode, 'IdTransaction' => $packT->Id ,'IdQueue' => $que->QId, 'IdCompany' => $trx->IdCompany, 'IdDoctor' =>$trx->IdDoctor, 'Type' => $packT->AItemGroup , 'ItemGroup' => $packT->AItemGroup ,  'ItemSubGroup' => $ItemMasterData->SubGroup,  'LISCode' => $ItemMasterData->LISCode, 'ItemCode' => $packT->AItemCode, 'ItemDescription' => $packT->AItemDescription, 'Status' => 250 ]);
							}
							
							if(  $standID != $packT->Id)
							{
								DB::connection('CMS')->table('Transactions')->where('Id', $packT->Id)->update(['Status' => 250]);
								$standID = $packT->Id;
							}
						}

						echo "REGULAR PACKAGE with Standandard Package<br>";
						//Eros.ItemMaster.StandardPackage =  1'
						$PackageTrans = DB::connection('CMS')->table('Transactions')
						->join('Eros.Package', function($q)
						{
							$q->ON('Transactions.IdItemPrice', '=',  'Eros.Package.ItemPriceId');
						})
						->join('Eros.ItemMaster', function($q)
						{
							$q->ON('Eros.Package.ItemCode', '=', 'Eros.ItemMaster.Code')
							->where('Eros.ItemMaster.StandardPackage', '=', 1);
						})
						->join('Eros.StandardPackage', function($q)
						{
							$q->ON('Eros.ItemMaster.Code', '=',  'Eros.StandardPackage.ItemMasterPackageCode')
							->where('Eros.StandardPackage.ItemMasterGroup', 'LIKE', 'LABORATORY')
							->ORON('Eros.ItemMaster.Code', '=',  'Eros.StandardPackage.ItemMasterPackageCode')
							->where('Eros.StandardPackage.ItemMasterGroup', 'LIKE', 'IMAGING')
							->ORON('Eros.ItemMaster.Code', '=',  'Eros.StandardPackage.ItemMasterPackageCode')
							->where('Eros.StandardPackage.ItemMasterGroup', 'LIKE', 'CLINIC');
						})	
						
						->where('Transactions.IdQueue', $que->QId)->where('Transactions.Status','210')->where('Transactions.IdCompany', $trx->IdCompany)->where('Transactions.IdDoctor', $trx->IdDoctor)
						->where('Transactions.PriceGroupItemPrice', 'LIKE', 'Package')
						->get(array('Transactions.Id','Transactions.CodeItemPrice', 'Transactions.DescriptionItemPrice', 'Eros.StandardPackage.ItemMasterItemCode as AItemCode' ,'Eros.StandardPackage.ItemMasterDescription as AItemDescription', 'Eros.StandardPackage.ItemMasterGroup as AItemGroup', 'Eros.ItemMaster.SubGroup as AItemSubGroup'));
						
						$standID = 0;
						foreach($PackageTrans as $packT)
						{
							//reGet Item Sub Group 
							$ItemMasterData = DB::connection('Eros')->table('ItemMaster')->where('Code', $packT->AItemCode)->get(array('SubGroup','LISCode'))[0];
							if( $packT->AItemGroup == 'LABORATORY')
							{
								DB::connection('CMS')->table('AccessionNo')->insert(['IdBU' => $BUCode, 'IdTransaction' => $packT->Id ,'IdQueue' => $que->QId, 'IdCompany' => $trx->IdCompany, 'IdDoctor' =>$trx->IdDoctor, 'Type' => $packT->AItemGroup ,  'ItemGroup' => $packT->AItemGroup ,  'LISCode' => $ItemMasterData->LISCode, 'ItemCode' => $packT->AItemCode, 'ItemDescription' => $packT->AItemDescription, 'Status' => 250 ]);
							}
							else
							{
									DB::connection('CMS')->table('AccessionNo')->insert(['IdBU' => $BUCode, 'IdTransaction' => $packT->Id ,'IdQueue' => $que->QId, 'IdCompany' => $trx->IdCompany, 'IdDoctor' =>$trx->IdDoctor, 'Type' => $packT->AItemGroup ,  'ItemGroup' => $packT->AItemGroup ,  'ItemSubGroup' => $ItemMasterData->SubGroup, 'LISCode' => $ItemMasterData->LISCode, 'ItemCode' => $packT->AItemCode, 'ItemDescription' => $packT->AItemDescription, 'Status' => 250 ]);
							}
							
						}
						
						echo "REGULAR PACKAGE<br>";
						//Eros.ItemMaster.StandardPackage =  1'
						$PackageTrans = DB::connection('CMS')->table('Transactions')
						->join('Eros.Package', function($q)
						{
							$q->ON('Transactions.IdItemPrice', '=',  'Eros.Package.ItemPriceId');
						})
						->join('Eros.ItemMaster', function($q)
						{
							$q->ON('Eros.Package.ItemCode', '=', 'Eros.ItemMaster.Code')	
							->where('Eros.ItemMaster.StandardPackage', '=', 0)
							->where('Eros.ItemMaster.Group', 'LIKE', 'LABORATORY')
							->ORON('Eros.Package.ItemCode', '=',  'Eros.ItemMaster.Code')
							->where('Eros.ItemMaster.StandardPackage', '=', 0)
							->where('Eros.ItemMaster.Group', 'LIKE', 'IMAGING')
							->ORON('Eros.Package.ItemCode', '=',  'Eros.ItemMaster.Code')
							->where('Eros.ItemMaster.StandardPackage', '=', 0)
							->where('Eros.ItemMaster.Group', 'LIKE', 'CLINIC');
						})						
						->where('Transactions.IdQueue', $que->QId)->where('Transactions.Status','210')->where('Transactions.IdCompany', $trx->IdCompany)->where('Transactions.IdDoctor', $trx->IdDoctor)
						->where('Transactions.PriceGroupItemPrice', 'LIKE', 'Package')
						->get(array('Transactions.Id','Transactions.CodeItemPrice', 'Transactions.DescriptionItemPrice', 'Eros.ItemMaster.Code as AItemCode' ,'Eros.ItemMaster.Description as AItemDescription', 'Eros.ItemMaster.Group as AItemGroup', 'Eros.ItemMaster.SubGroup as AItemSubGroup'));
						
						$standID = 0;
						foreach($PackageTrans as $packT)
						{
							//reGet Item Sub Group 
							$ItemMasterData = DB::connection('Eros')->table('ItemMaster')->where('Code', $packT->AItemCode)->get(array('SubGroup','LISCode'))[0];
							if( $packT->AItemGroup == 'LABORATORY')
							{
								DB::connection('CMS')->table('AccessionNo')->insert(['IdBU' => $BUCode, 'IdTransaction' => $packT->Id ,'IdQueue' => $que->QId, 'IdCompany' => $trx->IdCompany, 'IdDoctor' =>$trx->IdDoctor, 'Type' => $packT->AItemGroup ,  'ItemGroup' => $packT->AItemGroup ,  'LISCode' => $ItemMasterData->LISCode, 'ItemCode' => $packT->AItemCode, 'ItemDescription' => $packT->AItemDescription, 'Status' => 250 ]);
							}
							else
							{
									DB::connection('CMS')->table('AccessionNo')->insert(['IdBU' => $BUCode, 'IdTransaction' => $packT->Id ,'IdQueue' => $que->QId, 'IdCompany' => $trx->IdCompany, 'IdDoctor' =>$trx->IdDoctor, 'Type' => $packT->AItemGroup ,  'ItemGroup' => $packT->AItemGroup ,  'ItemSubGroup' => $ItemMasterData->SubGroup, 'LISCode' => $ItemMasterData->LISCode, 'ItemCode' => $packT->AItemCode, 'ItemDescription' => $packT->AItemDescription, 'Status' => 250 ]);
							}
							if(  $standID != $packT->Id)
							{
								DB::connection('CMS')->table('Transactions')->where('Id', $packT->Id)->update(['Status' => 250]);
								$standID = $packT->Id;
							}
						}
						
				}
				
				DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 250]);
			
			}
			
			DB::connection('CMS')->commit();  
			
			DB::connection('CMS')->beginTransaction();
			$alpha = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' );
			$ATrans =  DB::connection('CMS')->table('Queue')
									//->leftJoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
									->where('Queue.Status', '=','250')
									->where('Queue.IdBU', 'LIKE', $BUCode)
									->where('Queue.Date', '=', date('Y-m-d'))
									//->where('Queue.ErosStatus', 'NOT LIKE', 'reUpdate')
									//->where('Queue.Id', '=', '674183')
									->limit(1)
									->orderBy('Queue.Id', 'DESC')
									->get(array('Queue.Id as QId', 'Queue.Code as QCode'));
			
			foreach($ATrans as $que)
			{
				$x = 0;
				$gbDocComLab = DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->where('Status','250')->where('Type', 'LIKE', 'LABORATORY')->groupBy('IdDoctor', 'IdCompany', 'ItemSubGroup')->get(array('*'));
				
				foreach($gbDocComLab as $gbDC)
				{
					DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->where('Status','250')->where('Type', 'LIKE', 'LABORATORY')
					->where('IdCompany', $gbDC->IdCompany)->where('IdDoctor', $gbDC->IdDoctor)->where('ItemSubGroup', $gbDC->ItemSubGroup )
					->update(['Status' => 260, 'AccessionNo' => $que->QCode.$alpha[$x]]);
					$x++;
				}
				
				$gbDocComImaging = DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->where('Status','250')->where('Type', 'LIKE', 'IMAGING')->groupBy('IdDoctor', 'IdCompany', 'IdTransaction','ItemCode')->get(array('*'));
				
				foreach($gbDocComImaging as $gbDC)
				{
					DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->where('Status','250')->where('Type', 'LIKE', 'IMAGING')->where('IdTransaction', $gbDC->IdTransaction)
					->where('IdCompany', $gbDC->IdCompany)->where('IdDoctor', $gbDC->IdDoctor)->where('ItemCode', 'LIKE', $gbDC->ItemCode)
					->update(['Status' => 260, 'AccessionNo' => $que->QCode.$alpha[$x]]);
					$x++;
				}
				
				DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 260]);
			}
			
			
			
			DB::connection('CMS')->commit();  
			
						
			echo '<br>Done';
		}
	}
	
	public function cmsCAHL7File()
	{	
		$BUCode = "DTU";
		$accDatas = DB::connection('CMS')->table('AccessionNo')->where('IdBU', 'LIKE', $BUCode)->where('Status', 850)->groupBy('AccessionNo')->get(array('*'));
		//dd($accDatas);
	
		foreach($accDatas as $acc)
		{
			echo "<br>QueueId => ". $acc->IdQueue;

			$labGroup 	= "LABORATORY"; 
			$radGroup 	= "IMAGING"; 
		
			$labResult  = DB::select('CALL CMS.HL7LABCA(?,?,?)', [$acc->AccessionNo, $acc->IdQueue, $labGroup]);
			$radResult  = DB::select('CALL CMS.HL7RADCA(?,?,?)', [$acc->AccessionNo, $acc->IdQueue, $radGroup]);
		
			if (!empty($labResult)) {

				$generatedHL7Message = $labResult[0]->HL7LABCA;
				
				if (strpos($generatedHL7Message, 'OBR') !== false) {
					
					$generatedHL7Message = str_replace("\n", "\r\n", $generatedHL7Message);
					
					$filePathLab = public_path('HL7_LAB') . '/' .$acc->AccessionNo.'-CA-LAB.hl7';
					file_put_contents($filePathLab, $generatedHL7Message);
		
					$filePathBackup = public_path('HL7_FILES/BU_HL7LAB') . '/' .$acc->AccessionNo.'-CA-LAB.hl7';
					file_put_contents($filePathBackup, $generatedHL7Message);

					$logFileDate = date('ymd');
					$logFilePath = public_path('HL7_FILES/HL7_LOGS/' . $logFileDate . '.log');
					$logMessage = "[" . date('Y-m-d H:i:s') . "] AccessionNo: " . $acc->AccessionNo . " - HL7 Message:\n" . $generatedHL7Message . "\n\n";
					file_put_contents($logFilePath, $logMessage, FILE_APPEND);

					echo "<br>Successfully generated! => LABORATORY";
				} 
			}

			if (!empty($radResult)) {

				$generatedHL7Message = $radResult[0]->HL7RADCA;
				$subGroups = array('2DECHO', 'ABPM', 'BMD', 'CTSCAN', 'ECG', 'FIBROSCAN', 'GEN UTZ', 'HOLTER', 'MAMMO', 'OB UTZ', 'PFT', 'TREADMILL', 'VASCULAR UTZ', 'XRAY');

				if (strpos($generatedHL7Message, 'OBR') !== false) {

					$generatedHL7Message = str_replace("\n", "\r\n", $generatedHL7Message);

					foreach ($subGroups as $sGroup) {
						if (strpos($generatedHL7Message, $sGroup) !== false) {
							$filePathRad = public_path("HL7_RAD/{$sGroup}") . '/' . $acc->AccessionNo . '-CA-RAD.hl7';
							file_put_contents($filePathRad, $generatedHL7Message);
						}
					}

					$filePathBackup = public_path('HL7_FILES/BU_HL7RAD') . '/' . $acc->AccessionNo . '-CA-RAD.hl7';
					file_put_contents($filePathBackup, $generatedHL7Message);

					$logFileDate = date('ymd');
					$logFilePath = public_path('HL7_FILES/HL7_LOGS/' . $logFileDate . '.log');
					$logMessage = "[" . date('Y-m-d H:i:s') . "] AccessionNo: " . $acc->AccessionNo . " - HL7 Message:\n" . $generatedHL7Message . "\n\n";
					file_put_contents($logFilePath, $logMessage, FILE_APPEND);
					
					echo "<br>Successfully generated! => IMAGING";
				}
			}
			
			DB::connection('CMS')->table('AccessionNo')->where('IdBU', 'LIKE', $BUCode)->where('Status', '=', 850)->where('IdQueue', '=', $acc->IdQueue )->where('AccessionNo', 'like', $acc->AccessionNo )->update(['Status' => 900]);
			
		}
		

		return "<br>DONE => ";
	}

	public function cmsMakeHL7File()
	{	
		$BUCode = "DTU";
		$qTrans =  DB::connection('CMS')->table('Queue')
					->where('Queue.Date', '=', date('Y-m-d'))
					->where('Queue.Status', '=', 260) //Processing HL7
					->where('Queue.IdBU', 'LIKE', $BUCode)
					//->where('Queue.Id', '=', '12599258')
					->limit(1)
					->orderBy('Queue.Id', 'DESC')
					->get(array('Queue.Id as QId','DateTime'));

					//dd($qTrans);

		foreach ($qTrans as $que)
		{
			//check if payment made has been paid
			$accCount = DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->get(array('Id'));
			$selects  = DB::connection('CMS')->table('Transactions')->where('IdQueue', '=', $que->QId)->get(array('Status','SystemUpdateTime'));
			$forConsultation = 0;
			$specimen = 0;
			$fullyPaid = 0;
			//print_r($selects);
			//die();
			foreach($selects as $select)
			{
				if( $select->Status == '300' ) //For Specimen
				{
					$specimen = 1;
				}
				else if( $select->Status == '280' ) //For Consultation
				{
					$forConsultation = 1;
				}
				else if( $select->Status == '210' ) //Fully Paid
				{
					$fullyPaid = 1;
				}
			}
			
			$mins = (strtotime(date('Y-m-d H:i:s')) - strtotime($que->DateTime)) / 60;
			
			if( $specimen == 1  && $forConsultation == 0 )
			{
				DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 300]);
			}
			else if( $specimen == 0  && $forConsultation == 1 )
			{
				DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 280]);
			}
			else if( $fullyPaid == 1 && count($accCount) == 0 )
			{
				DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 500]); // Completed
			}
			else if( count($selects) == 0 &&  $mins > 30 )
			{
				DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 900]);
			}
			
			
			//consultation only
			$accDatas = DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->where('Status', 280)->groupBy('AccessionNo')->get(array('*'));
			foreach($accDatas as $acc)
			{
				DB::connection('CMS')->table('Queue')->where('Status', 260)->where('Id', $que->QId)->update(['Status' => 280]);
			}
			
			$accDatas = DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->where('Status', 260)->groupBy('AccessionNo')->get(array('*'));
			//dd($accDatas);
		
			foreach($accDatas as $acc)
			{
				echo "<br>QueueId => ". $que->QId;

				$labGroup 	= "LABORATORY"; 
				$radGroup 	= "IMAGING"; 
			
				$labResult  = DB::select('CALL CMS.HL7LAB(?,?,?)', [$acc->AccessionNo, $acc->IdQueue, $labGroup]);
				$radResult  = DB::select('CALL CMS.HL7RAD(?,?,?)', [$acc->AccessionNo, $acc->IdQueue, $radGroup]);
			
				if (!empty($labResult)) {

					$generatedHL7Message = $labResult[0]->HL7LAB;
					
					if (strpos($generatedHL7Message, 'OBR') !== false) {
						
						$generatedHL7Message = str_replace("\n", "\r\n", $generatedHL7Message);
						
						$filePathLab = public_path('HL7_LAB') . '/' .$acc->AccessionNo.'-ORM^O01-LAB.hl7';
						file_put_contents($filePathLab, $generatedHL7Message);
			
						$filePathBackup = public_path('HL7_FILES/BU_HL7LAB') . '/' .$acc->AccessionNo.'-ORM^O01-LAB.hl7';
						file_put_contents($filePathBackup, $generatedHL7Message);

						$logFileDate = date('ymd');
						$logFilePath = public_path('HL7_FILES/HL7_LOGS/' . $logFileDate . '.log');
						$logMessage = "[" . date('Y-m-d H:i:s') . "] AccessionNo: " . $acc->AccessionNo . " - HL7 Message:\n" . $generatedHL7Message . "\n\n";
						file_put_contents($logFilePath, $logMessage, FILE_APPEND);

						echo "<br>Successfully generated! => LABORATORY";
					} 
				}

				if (!empty($radResult)) {

					$generatedHL7Message = $radResult[0]->HL7RAD;
					$subGroups = array('2DECHO', 'ABPM', 'BMD', 'CTSCAN', 'ECG', 'FIBROSCAN', 'GEN UTZ', 'HOLTER', 'MAMMO', 'OB UTZ', 'PFT', 'TREADMILL', 'VASCULAR UTZ', 'XRAY');

					if (strpos($generatedHL7Message, 'OBR') !== false) {

						$generatedHL7Message = str_replace("\n", "\r\n", $generatedHL7Message);

						foreach ($subGroups as $sGroup) {
							if (strpos($generatedHL7Message, $sGroup) !== false) {
								$filePathRad = public_path("HL7_RAD/{$sGroup}") . '/' . $acc->AccessionNo . '-ORM^O01-RAD.hl7';
								file_put_contents($filePathRad, $generatedHL7Message);
							}
						}

						$filePathBackup = public_path('HL7_FILES/BU_HL7RAD') . '/' . $acc->AccessionNo . '-ORM^O01-RAD.hl7';
						file_put_contents($filePathBackup, $generatedHL7Message);

						$logFileDate = date('ymd');
						$logFilePath = public_path('HL7_FILES/HL7_LOGS/' . $logFileDate . '.log');
						$logMessage = "[" . date('Y-m-d H:i:s') . "] AccessionNo: " . $acc->AccessionNo . " - HL7 Message:\n" . $generatedHL7Message . "\n\n";
						file_put_contents($logFilePath, $logMessage, FILE_APPEND);
						
						echo "<br>Successfully generated! => IMAGING";
					}
				}
				
				DB::connection('CMS')->table('AccessionNo')->where('Status', 260)->where('AccessionNo', 'like', $acc->AccessionNo )->update(['Status' => 270]);
				
			}
		}

		return "<br>DONE => ";
	}
	
	public function updateCMSHL7QueueStatus()
	{	
		$BUCode = "DTU";
		$qTrans =  DB::connection('CMS')->table('AccessionNo')
					->where('AccessionNo.Status', '=', 270)
					->where('AccessionNo.IdBU', 'LIKE', $BUCode)
					->groupBy('AccessionNo.IdQueue')
					->get(array('AccessionNo.IdQueue as QId'));

		foreach ($qTrans as $que)
		{
			DB::connection('CMS')->beginTransaction();
				DB::connection('CMS')->table('AccessionNo')->where('Status', 270)->where('AccessionNo.IdQueue', $que->QId )->update(['Status' => 300]);
				DB::connection('CMS')->table('Queue')->where('Status', 260)->where('Id', $que->QId )->update(['Status' => 300]);
				DB::connection('CMS')->table('Transactions')->where('Status', 250)->where('IdQueue', $que->QId )->update(['Status' => 300]);
			DB::connection('CMS')->commit();  
		}
		echo "Done";
	}
	
	public function UpdateForPaymentQueue()
	{
		$BUCode = "DTU";

		$queDatas =  DB::connection('CMS')->table('Queue')
									->where('Queue.Status', '=','201')
									->where('Queue.IdBU', 'LIKE', $BUCode)
									->where('Queue.Date', date('Y-m-d'))
									->get(array('*'));
		foreach($queDatas as $que)
		{
			echo $que->Code ."<br>";
			
				$selects  = DB::connection('CMS')->table('Transactions')->where('IdQueue', '=', $que->Id)->get(array('Status','SystemUpdateTime'));
				$forBilling = 0;
				$paid = 0;
				//print_r($selects);
				//die();
				foreach($selects as $select)
				{
					if( ($select->Status == '201' || $select->Status == '205')   )
					{
						$forBilling = 1;
					}
					else if( $select->Status >= '210' &&  $select->Status <= '600' )
					{
						$paid = 1;
					}
				}
				echo "For Billing = ".$forBilling."<br>";
				echo "Paid = ".$forBilling."<br>";;
			
				
				$mins = (strtotime(date('Y-m-d H:i:s')) - strtotime($queDatas[0]->DateTime)) / 60;
				
				if( $forBilling == 0  && $paid == 1 )
				{
					DB::connection('CMS')->table('Queue')->where('Id', $que->Id)->update(['Status' => 210]);
				}
				else if( count($selects) == 0 )
				{
					DB::connection('CMS')->table('Queue')->where('Id', $que->Id)->update(['Status' => 900]);
				}
		}
	
	}
	//#############################################
	static public function createJsonFile4HCpdf($IdQueue = NULL)
	{
		echo $BUCode = "ALL";
		echo "<br>AccessionNo";
		
		//DB::connection('CMS')->beginTransaction();
		$accessionDatas =  DB::connection('CMS')->table('AccessionNo')
					->Join('Queue', 'AccessionNo.IdQueue', '=', 'Queue.Id')
					->Join('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
					//->whereIN('Queue.IdBU', ['DTU','DTU','BIN','DAG','LPI','MAK','MAL','PCS','SRL','TAG','IMD','HOM','ORT','NOV','MIR'] )
					->where(function($q) use ($IdQueue)
					{
						if( !empty($IdQueue) )
						{
							$q->where('AccessionNo.IdQueue','=', $IdQueue);
						}
						else
						{
							$q->where('AccessionNo.Status', '=', 410);
						}
					})
					->where('AccessionNo.Type', 'LIKE', 'LABORATORY')
					->limit(100)
					//->orderBy('Queue.Id', 'ASC')
					->groupBy('AccessionNo.AccessionNo')
					->get(array('AccessionNo.AccessionNo','AccessionNo.AccessionMap','Queue.Code','Queue.Date','Eros.Patient.Code as PatientCode'));
		
		foreach ($accessionDatas as $aData)
		{
			echo "<br>From Queue Code => ". $aData->Code;
			echo "<br>From Accession Map => ". $aData->AccessionMap;
			$filePath = public_path('HCLAB') . '/' .$aData->Code.'_'.date('Ymd').'.json';
			file_put_contents($filePath, 
'{
"LabNo":"'.$aData->AccessionMap.'",
"Month":"'.date("m", strtotime($aData->Date)).'",
"Day":"'.date("d", strtotime($aData->Date)).'",
"Year":"'.date("Y", strtotime($aData->Date)).'",
"AccessionNo":"'.$aData->AccessionNo.'",
"PatientCode":"'.$aData->PatientCode.'"
}');
			echo "<br>Update Status => ";
			if(empty($IdQueue))
			{
				DB::connection('CMS')->table('AccessionNo')->where('AccessionNo.Type', 'LIKE', 'LABORATORY')->where('AccessionMap', $aData->AccessionMap)->update(['Status' => 420]);
			}
		}
		//DB::connection('CMS')->commit();  
	
		return "<br>DONE ";
		
	
	}
	public function cenGetAccessionMapUpdate($iDate = NULL)
	{
		//$today =  "2024-09-15%";
		$today =  date('Y-m-d')."%";
		echo "<br>Join Data from Queueu and AccessionNo";
		//$date = (!empty($iDate)) ? $iDate : date('Y-m-d');
		//DB::connection('CMS')->beginTransaction();
		$accessionDatas =  DB::connection('CMS')->table('AccessionNo')
					->where('AccessionNo.SystemTimeCreated', 'LIKE', $today)
					->where('AccessionNo.Status', '=', 300)
					->whereIN('AccessionNo.IdBU', ['DTU'] ) // latest branch as of Sep 13 2024
					->whereNull('AccessionNo.AccessionMap' )
					->where('AccessionNo.Type', 'LIKE', 'LABORATORY')
					//->limit(25)
					//->orderBy('Queue.Id', 'ASC')
					->groupBy('AccessionNo.AccessionNo')
					->get(array('AccessionNo.AccessionNo', 'AccessionNo.IdQueue'));
		
		foreach ($accessionDatas as $aData)
		{
			echo "<br>Fetch data from ORA DB where => ". $aData->AccessionNo;
			if (DB::connection('oraCENh')->getDatabaseName()) 
			{	// get Labno and Update AccessionMap
				$ordHRD = DB::connection('oraCENh')->table('ORD_HDR')->where('OH_ONO', 'LIKE', $aData->AccessionNo)->groupBy('OH_TNO')->get(array('OH_TNO'));
				if(count($ordHRD) != 0)
				{
					echo "<br>Update Accession Map => ". $ordHRD[0]->oh_tno;
					DB::connection('CMS')->table('AccessionNo')
					->whereIN('AccessionNo.IdBU', ['DTU'] ) // latest branch as of Sep 13 2024
					->where('AccessionNo.Type', 'LIKE', 'LABORATORY')->where('AccessionNo', $aData->AccessionNo)->update(['AccessionMap' => $ordHRD[0]->oh_tno]);

				}elseif(count($ordHRD) == 0) {

					$labGroup 	= "LABORATORY"; 
				
					$labResult  = DB::select('CALL CMS.HL7LAB(?,?,?)', [$aData->AccessionNo, $aData->IdQueue, $labGroup]);

					if (!empty($labResult)) {

						$generatedHL7Message = $labResult[0]->HL7LAB;
						
						if (strpos($generatedHL7Message, 'OBR') !== false) {
							
							$generatedHL7Message = str_replace("\n", "\r\n", $generatedHL7Message);
							
							$filePathLab = public_path('HL7_LAB') . '/' .$aData->AccessionNo.'-ORM^O01-LAB.hl7';
							file_put_contents($filePathLab, $generatedHL7Message);
				
							$filePathBackup = public_path('HL7_FILES/BU_HL7LAB') . '/' .$aData->AccessionNo.'-ORM^O01-LAB.hl7';
							file_put_contents($filePathBackup, $generatedHL7Message);

							$logFileDate = date('ymd');
							$logFilePath = public_path('HL7_FILES/HL7_LOGS/' . $logFileDate . '.log');
							$logMessage = "[" . date('Y-m-d H:i:s') . "] AccessionNo: " . $aData->AccessionNo . " - HL7 Message:\n" . $generatedHL7Message . "\n\n";
							file_put_contents($logFilePath, $logMessage, FILE_APPEND);

							echo "<br>Successfully generated! => LABORATORY";
						} 
					}
				}
				
			
			}
		}
		//DB::connection('CMS')->commit();  
	
		return "<br>DONE ";
			
	}
	static public function onCenUpdateAccessionMapStatus($lab = NULL, $acc = NULL, $bu = NULL, $status = NULL, $IdQueue = NULL)
	{
		if(!empty($_GET['labno']))
		{
			$labNo =  $_GET['labno']; //echo "<br>";
		}
		elseif(!empty($lab) )
		{
			echo $labNo =  $lab; echo "<br>";
		}
		else
		{
			return 'Missing Param Lab No.';
		}
		
	
		//echo "<br>Get released Lab No.";
		//echo "<br>";
		$cstr = '(DESCRIPTION = (ADDRESS =  (PROTOCOL = TCP)  (HOST = 10.30.154.158) (PORT = 1521)  ) (CONNECT_DATA = (SID = hclab) ) )';

		// Connect to Oracle
		$conn = oci_connect("hclab", "hclab", $cstr, 'AL32UTF8');

		// Check if the connection was successful
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		
		//$date = strtoupper(date("d-M-y", strtotime($date)));
		
		//echo "Current Date Time " . $date = date("Y-m-d H:i:s"); echo "<br>";
		//$time = strtotime($date);
		//$time = $time - (1 * 60);
		//$date = date("Y-m-d H:i%", $time);
		//$date = "2024-07-16%";
		
		$sqlSelect = "  SELECT 
		extract(DAY from (systimestamp - to_timestamp(to_char(tb1.OH_TRX_DT, 'dd/mm/rr HH24:MI:SS'), 'dd/mm/rr HH24:MI:SS'))) AS " . '"NewDay"' . ",
		extract(HOUR from (systimestamp - to_timestamp(to_char(tb1.OH_TRX_DT, 'dd/mm/rr HH24:MI:SS'), 'dd/mm/rr HH24:MI:SS'))) AS " . '"NewHour"' . ",
		extract(MINUTE from (systimestamp - to_timestamp(to_char(tb1.OH_TRX_DT, 'dd/mm/rr HH24:MI:SS'), 'dd/mm/rr HH24:MI:SS'))) / 60  AS " . '"NewMin"' . ",
		
		extract(DAY from (systimestamp - to_timestamp(to_char(tb3.OS_SPL_RCVDT, 'dd/mm/rr HH24:MI:SS'), 'dd/mm/rr HH24:MI:SS'))) AS " . '"PendingDay"' . ",
		extract(HOUR from (systimestamp - to_timestamp(to_char(tb3.OS_SPL_RCVDT, 'dd/mm/rr HH24:MI:SS'), 'dd/mm/rr HH24:MI:SS'))) AS " . '"PendingHour"' . ",
		extract(MINUTE from (systimestamp - to_timestamp(to_char(tb3.OS_SPL_RCVDT, 'dd/mm/rr HH24:MI:SS'), 'dd/mm/rr HH24:MI:SS'))) / 60  AS " . '"PendingMin"' . ",
	
		extract(DAY from (systimestamp - to_timestamp(to_char(tb1.OH_UPDATE_ON, 'dd/mm/rr HH24:MI:SS'), 'dd/mm/rr HH24:MI:SS'))) AS " . '"QueueDay"' . ",
		extract(HOUR from (systimestamp - to_timestamp(to_char(tb1.OH_UPDATE_ON, 'dd/mm/rr HH24:MI:SS'), 'dd/mm/rr HH24:MI:SS'))) AS " . '"QueueHour"' . ",
		extract(MINUTE from (systimestamp - to_timestamp(to_char(tb1.OH_UPDATE_ON, 'dd/mm/rr HH24:MI:SS'), 'dd/mm/rr HH24:MI:SS'))) / 60  AS " . '"QueueMin"' . ",
	
	
		tb1.OH_PCMT, tb1.OH_TNO, tb1.OH_LAST_NAME, to_char(tb1.OH_TRX_DT, 'yyyy-mm-dd HH24:MI:SS') as OH_TRX_DT, tb1.OH_CLINIC_CODE, tb1.OH_ORD_STATUS, to_char(tb1.OH_UPDATE_ON, 'yyyy-mm-dd HH24:MI:SS') as OH_UPDATE_ON, to_char(tb1.OH_COMPLETED_DT, 'yyyy-mm-dd HH24:MI:SS') as OH_COMPLETED_DT,
		tb2.OD_TEST_GRP,  tb2.OD_STATUS, tb2.OD_ACTION_FLAG, tb2.OD_ITEM_TYPE, tb2.OD_UPDATE_ON, to_char(tb2.OD_VALIDATE_ON, 'yyyy-mm-dd HH24:MI:SS') as OD_VALIDATE_ON, tb2.OD_TR_COMMENT, tb2.OD_CTL_FLAG2,
		tb3.OS_SPL_RCVD_FLAG, to_char(tb3.OS_SPL_RCVDT, 'yyyy-mm-dd HH24:MI:SS') as OS_SPL_RCVDT , tb3.OS_REC_FLAG, tb3.OS_SNO , tb3.OS_SPL_RJ_FLAG, to_char(tb3.OS_SPL_RJ_DT, 'yyyy-mm-dd HH24:MI:SS') as OS_SPL_RJ_DT,
		tb4.TI_NAME, tb4.TI_LONG_NAME, tb4.TI_CODE,
		tb5.CMT_CODE, tb5.CMT_DESC
		FROM 
		ORD_HDR tb1 
		LEFT JOIN ORD_DTL tb2 ON(tb1.OH_TNO = tb2.OD_TNO)
		LEFT JOIN ORD_SPL tb3 ON(tb2.OD_TNO= tb3.OS_TNO AND tb2.OD_SPL_TYPE = tb3.OS_SPL_TYPE)
		LEFT JOIN TEST_ITEM tb4 ON(tb2.OD_TESTCODE = tb4.TI_CODE)
		LEFT JOIN CMT_TBL tb5 ON(tb4.TI_CODE = tb5.CMT_CODE)
		where tb1.OH_TNO LIKE '$labNo' AND tb2.OD_SPL_TYPE != 99 AND tb2.OD_ORDER_ITEM LIKE 'Y' AND tb2.OD_TEST_GRP != 'BAC' AND tb2.OD_TEST_GRP != 'SO' AND tb2.OD_TEST_GRP != 'HC' AND tb2.OD_TEST_GRP != 'HIS'";
	
//tb1.OH_CLINIC_CODE LIKE '10' and 
		//DB::connection('CMS')->beginTransaction();
		
		$itemSelect = oci_parse($conn, $sqlSelect);
		oci_execute($itemSelect);
	
		while (oci_fetch($itemSelect)) 
		{

			//get item code TI_CODE
			$hItemCode =  oci_result($itemSelect, 'TI_CODE'); 
			
			$itemMasterInfo = DB::connection('Eros')->table('ItemMaster')->where('Group', 'LIKE', 'LABORATORY')->where('OrderStatus', 'LIKE', 'Y')->where('LISCode', 'LIKE', $hItemCode)->get(array('Code'));
			if(count($itemMasterInfo) !=0 )
			{
				$mapCode = DB::connection('Eros')->table('Item')->where('IdBU', 'LIKE', $bu)->where('MapCode', 'LIKE', $itemMasterInfo[0]->Code)->get(array('LISCode','ItemCode'));
				
				if(count($mapCode) !=0 )
				{
					$checkifLIS = DB::connection('CMS')->table('AccessionNo')->where('AccessionNo.Type', 'LIKE', 'LABORATORY')->where('AccessionNo.AccessionNo', 'LIKE', $acc)->where('AccessionMap', '=', $labNo)->where('ItemCode', 'LIKE', $mapCode[0]->LISCode)->get(array('LISCode'));
					if(count($checkifLIS) !=0 )
					{
						$cItemCode = $mapCode[0]->LISCode; //echo "<br>";
					}
					else
					{
						$cItemCode = $mapCode[0]->ItemCode;
					}
				}
				else
				{
					$cItemCode = $itemMasterInfo[0]->Code; //echo "<br>";
				}
			}
			else
			{
				$mapCode = DB::connection('Eros')->table('Item')->where('MapCode', 'LIKE', $hItemCode)->get(array('ItemCode'));
				if(count($mapCode) !=0 )
				{
					$cItemCode = $mapCode[0]->ItemCode;
				}
				else
				{
					continue;
				}
			}
			
			
			
			if ((oci_result($itemSelect, 'OH_ORD_STATUS') == '1' || oci_result($itemSelect, 'OH_ORD_STATUS') == '9')  &&  oci_result($itemSelect, 'OD_STATUS') == 'N' && oci_result($itemSelect, 'OS_SPL_RCVD_FLAG') == 'Y'  )  
			{
				echo $currentStatus = "301"; echo "<br>";//Received Specimen
				$getStatus = DB::connection('CMS')->table('AccessionNo')->where('AccessionNo.IdBU', 'LIKE', $bu)->where('AccessionNo.AccessionNo', 'LIKE', $acc)->where('AccessionMap', '=', $labNo)->where('ItemCode', 'LIKE', $cItemCode)->get(array('Status'));
				
				if( count($getStatus) != 0 && $getStatus[0]->Status != "301")
				{
					DB::connection('CMS')->table('AccessionNo')->where('AccessionNo.IdBU', 'LIKE', $bu)->where('AccessionNo.AccessionNo', 'LIKE', $acc)->where('AccessionMap', '=', $labNo)->where('ItemCode', 'LIKE', $cItemCode)->update(['Status' => '301']);
					DB::connection('CMS')->table('AccessionNo')
					->Join('Transactions', 'AccessionNo.IdQueue', '=', 'Transactions.IdQueue')
					->where('AccessionNo.AccessionNo', 'LIKE', $acc)
					->where('AccessionNo.IdBU', 'LIKE', $bu)
					->where('AccessionNo.Type', 'LIKE', 'LABORATORY')
					->where('AccessionNo.AccessionMap', '=', $labNo)->where('Transactions.CodeItemPrice', 'LIKE', $cItemCode)->update(['Transactions.Status' => '301']);
				}
			}
			elseif (oci_result($itemSelect, 'OH_ORD_STATUS') == '5' &&  oci_result($itemSelect, 'OD_STATUS') == 'N' && oci_result($itemSelect, 'OS_SPL_RCVD_FLAG') == 'Y')
			{
				echo $currentStatus = "400"; echo "<br>";	//Processing
				$getStatus = DB::connection('CMS')->table('AccessionNo')->where('AccessionNo.IdBU', 'LIKE', $bu)->where('AccessionNo.AccessionNo', 'LIKE', $acc)->where('AccessionMap', '=', $labNo)->where('ItemCode', 'LIKE', $cItemCode)->get(array('Status'));
				
				if( count($getStatus) != 0 && $getStatus[0]->Status != "400")
				{
					DB::connection('CMS')->table('AccessionNo')->where('AccessionNo.IdBU', 'LIKE', $bu)->where('AccessionNo.AccessionNo', 'LIKE', $acc)->where('AccessionMap', '=', $labNo)->where('ItemCode', 'LIKE', $cItemCode)->update(['Status' => '400']);
					DB::connection('CMS')->table('AccessionNo')
					->Join('Transactions', 'AccessionNo.IdQueue', '=', 'Transactions.IdQueue')
					->where('AccessionNo.AccessionNo', 'LIKE', $acc)
					->where('AccessionNo.IdBU', 'LIKE', $bu)
					->where('AccessionNo.Type', 'LIKE', 'LABORATORY')
					->where('AccessionNo.AccessionMap', '=', $labNo)->where('Transactions.CodeItemPrice', 'LIKE', $cItemCode)->update(['Transactions.Status' => '400']);
				}
			}
			elseif ((oci_result($itemSelect, 'OH_ORD_STATUS') == '5' || oci_result($itemSelect, 'OH_ORD_STATUS') == '9')  && oci_result($itemSelect, 'OD_STATUS') == 'Y'  && oci_result($itemSelect, 'OS_SPL_RCVD_FLAG') == 'Y')
			{
				echo $currentStatus = "500"; echo "<br>"; //Completed
				$getStatus = DB::connection('CMS')->table('AccessionNo')->where('AccessionNo.IdBU', 'LIKE', $bu)->where('AccessionNo.AccessionNo', 'LIKE', $acc)->where('AccessionMap', '=', $labNo)->where('ItemCode', 'LIKE', $cItemCode)->get(array('Status'));
				
				if( count($getStatus) != 0 && $getStatus[0]->Status != "500")
				{
					DB::connection('CMS')->table('AccessionNo')->where('AccessionNo.IdBU', 'LIKE', $bu)->where('AccessionNo.AccessionNo', 'LIKE', $acc)->where('AccessionMap', '=', $labNo)->where('ItemCode', 'LIKE', $cItemCode)->update(['Status' => ($status != "") ? $status: '500']);
					DB::connection('CMS')->table('AccessionNo')
					->Join('Transactions', 'AccessionNo.IdQueue', '=', 'Transactions.IdQueue')
					->where('AccessionNo.AccessionNo', 'LIKE', $acc)
					->where('AccessionNo.IdBU', 'LIKE', $bu)
					->where('AccessionNo.Type', 'LIKE', 'LABORATORY')
					->where('AccessionNo.AccessionMap', '=', $labNo)->where('Transactions.CodeItemPrice', 'LIKE', $cItemCode)->update(['Transactions.Status' => '500']);
					if( !empty($IdQueue) )
					{
						DTUHL7Controller::createJsonFile4HCpdf($IdQueue);
					}
				}
			}
		}
		

		oci_close($conn);    
		//DB::connection('CMS')->commit();  
	
		//return "<br>DONE ";
			
	}
	public function Acc300OnCENStatusAsc()
	{
		$date = date('Y-m-d')."%";
		//$date = "2024-09-25%";
		$accessionData = DB::connection('CMS')->table('AccessionNo')
		->whereIN('AccessionNo.IdBU', ['DTU'] ) // latest branch as of Sep 13 2024
		->where('AccessionNo.Type', 'LIKE', 'LABORATORY')
		->where('AccessionNo.SystemTimeCreated', 'LIKE', $date)
		//->where('AccessionNo.IdQueue', '=', '11141491')
		->whereNotNull('AccessionNo.AccessionMap')
		->where('AccessionNo.Status', '=', 300)
		->where('AccessionNo.ItemCode', 'NOT LIKE', 'LD005') // drugtest
		//->limit(50)
		->groupBy('AccessionNo.AccessionNo')
		->orderBy('AccessionNo.SystemTimeCreated', 'ASC')
		->get(array('AccessionNo.IdBU','AccessionNo.AccessionNo','AccessionNo.AccessionMap','AccessionNo.IdQueue'));
		
		foreach($accessionData as $accData)
		{
			DTUHL7Controller::onCenUpdateAccessionMapStatus($accData->AccessionMap,$accData->AccessionNo,$accData->IdBU, 500, $accData->IdQueue);
		}
	}
	public function Acc301OnCENStatusAsc()
	{
		$date = date('Y-m-d')."%";
		$accessionData = DB::connection('CMS')->table('AccessionNo')
		->whereIN('AccessionNo.IdBU', ['DTU'] ) // latest branch as of Sep 13 2024
		->where('AccessionNo.Type', 'LIKE', 'LABORATORY')
		->where('AccessionNo.SystemTimeCreated', 'LIKE', $date)
		->whereNotNull('AccessionNo.AccessionMap')
		->where('AccessionNo.Status', '=', 301)
		->where('AccessionNo.ItemCode', 'NOT LIKE', 'LD005') // drugtest
		//->limit(50)
		->groupBy('AccessionNo.AccessionNo')
		->orderBy('AccessionNo.SystemTimeCreated', 'ASC')
		->get(array('AccessionNo.IdBU','AccessionNo.AccessionNo','AccessionNo.AccessionMap','AccessionNo.IdQueue'));
		
		foreach($accessionData as $accData)
		{
			DTUHL7Controller::onCenUpdateAccessionMapStatus($accData->AccessionMap,$accData->AccessionNo,$accData->IdBU, 500, $accData->IdQueue);
		}
	}
	public function Acc400OnCENStatusAsc()
	{
		$date = date('Y-m-d')."%";
		$accessionData = DB::connection('CMS')->table('AccessionNo')
		->whereIN('AccessionNo.IdBU', ['DTU'] ) // latest branch as of Sep 13 2024
		->where('AccessionNo.Type', 'LIKE', 'LABORATORY')
		->where('AccessionNo.SystemTimeCreated', 'LIKE', $date)
		->whereNotNull('AccessionNo.AccessionMap')
		->where('AccessionNo.Status', '=', 400)
		->where('AccessionNo.ItemCode', 'NOT LIKE', 'LD005') // drugtest
		//->where('AccessionNo.IdQueue', '=', '11098362')
		//->limit(50)
		->groupBy('AccessionNo.AccessionNo')
		->orderBy('AccessionNo.SystemTimeCreated', 'ASC')
		->get(array('AccessionNo.IdBU','AccessionNo.AccessionNo','AccessionNo.AccessionMap','AccessionNo.IdQueue'));
		
		foreach($accessionData as $accData)
		{
			DTUHL7Controller::onCenUpdateAccessionMapStatus($accData->AccessionMap,$accData->AccessionNo,$accData->IdBU, 500, $accData->IdQueue);
		}
	}
	public function Acc420OnCENStatusAsc()
	{
		$date = date('Y-m-d')."%";
		$accessionData = DB::connection('CMS')->table('AccessionNo')
		->whereIN('AccessionNo.IdBU', ['DTU'] ) // latest branch as of Sep 13 2024
		->where('AccessionNo.Type', 'LIKE', 'LABORATORY')
		->where('AccessionNo.SystemTimeCreated', 'LIKE', $date)
		->whereNotNull('AccessionNo.AccessionMap')
		->where('AccessionNo.Status', '=', 420)
		->where('AccessionNo.ItemCode', 'NOT LIKE', 'LD005') // drugtest
		//->where('AccessionNo.IdQueue', '=', '11085087')
		//->limit(50)
		->groupBy('AccessionNo.AccessionNo')
		->orderBy('AccessionNo.SystemTimeCreated', 'ASC')
		->get(array('AccessionNo.IdBU','AccessionNo.AccessionNo','AccessionNo.AccessionMap','AccessionNo.IdQueue'));
		
		foreach($accessionData as $accData)
		{
			DTUHL7Controller::onCenUpdateAccessionMapStatus($accData->AccessionMap,$accData->AccessionNo,$accData->IdBU, 500, $accData->IdQueue);
		}
	}
	
}


   
    
