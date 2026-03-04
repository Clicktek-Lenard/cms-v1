<?php

namespace App\Http\Controllers\hl7;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ICTHL7Controller extends Controller
{
	public function HL7() // change to push data from CMS to EROS
	{
		$BUCode = 'ICT';
		
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

	public function cmsMakeHL7File()
	{	
		$BUCode = "ICT";
		$qTrans =  DB::connection('CMS')->table('Queue')
					->where('Queue.Date', '=', date('Y-m-d'))
					->where('Queue.Status', '=', 260) //Processing HL7
					->where('Queue.IdBU', 'LIKE', $BUCode)
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
						
						$filePath = public_path('HL7_LAB') . '/' .$acc->AccessionNo.'-ORM^O01-LAB.hl7';
						file_put_contents($filePath, $generatedHL7Message);
			
						$filePath = public_path('HL7_FILES/BU_HL7LAB') . '/' .$acc->AccessionNo.'-ORM^O01-LAB.hl7';
						file_put_contents($filePath, $generatedHL7Message);

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
								$filePath = public_path("HL7_RAD/{$sGroup}") . '/' . $acc->AccessionNo . '-ORM^O01-RAD.hl7';
								file_put_contents($filePath, $generatedHL7Message);
							}
						}

						$filePath = public_path('HL7_FILES/BU_HL7RAD') . '/' . $acc->AccessionNo . '-ORM^O01-RAD.hl7';
						file_put_contents($filePath, $generatedHL7Message);
						
						echo "<br>Successfully generated! => IMAGING";
					}
				}
				
				DB::connection('CMS')->table('AccessionNo')->where('Status', 260)->where('AccessionNo', 'like', $acc->AccessionNo )->update(['Status' => 270]);
				
			}
		}

		return "<br>DONE => ";
	}

}


   
    
