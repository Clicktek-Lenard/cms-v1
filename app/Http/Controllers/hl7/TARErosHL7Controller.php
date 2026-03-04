<?php

namespace App\Http\Controllers\hl7;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

use Aranyasen\HL7\Message; // If Message is used
use Aranyasen\HL7\Segment; // If Segment is used
use Aranyasen\HL7\Segments\MSH; // If MSH is used

class TARErosHL7Controller extends Controller
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
    
	public function AuditRollBack()
	{
		/*
		$auditQueue =  DB::connection('CMS')->select(" SELECT * FROM `Audit`.`CMSQueue` tb1  ORDER BY tb1.`Id` ASC  ");
		
		foreach($auditQueue as $que)
		{
			echo $que->QueueId; echo "<br>";
			$logs = json_decode($que->Logs, true); echo "<br>";
			
			
			 DB::connection('CMS')->table('Queue')->updateOrInsert
			(
				['Id' 					=> $que->QueueId],
				['IdBU'				=> $logs['IdBU']
				,'Code'				=> $logs['Code']
				,'Date'				=> $logs['Date']
				,'DateTime'			=> $logs['DateTime']
				,'IdPatient'				=> $logs['IdPatient']
				,'AgePatient'			=> $logs['AgePatient']
				,'Status'				=> $logs['Status']
				,'AccessionNo'			=> $logs['AccessionNo']
				,'Notes'				=> $logs['Notes']
				,'PatientType'			=> $logs['PatientType']
				,'Picture'				=> $logs['Picture']
				,'InputBy'				=> $logs['InputBy']
				,'Lab2LabId'			=> $logs['Lab2LabId']
				,'LabBarcode'			=> $logs['LabBarcode']
				,'LabId'				=> $logs['LabId']
				,'UpdateDate'			=> $logs['UpdateDate']
				,'UpdateBy'			=> $logs['UpdateBy']
				,'ErosStatus'			=> $logs['ErosStatus']
				,'SystemUpdateTime'		=> $logs['SystemUpdateTime']
				]
			);
		
			echo $que->Id; echo "<br>";
		
		}
		*/
		
		/*
		$auditTransaction =  DB::connection('CMS')->select(" SELECT * FROM `Audit`.`CMSTransactions` tb1  ORDER BY tb1.`Id` ASC  ");
		
		foreach($auditTransaction as $trans)
		{
			echo $trans->TransactionsId; echo "<br>";
			$logs = json_decode($trans->Logs, true); echo "<br>";
			
			
			 DB::connection('CMS')->table('Transactions')->updateOrInsert
			(
				['Id' 						=> $trans->TransactionsId],
				['IdQueue'					=> $logs['IdQueue']
				,'Date'					=> $logs['Date']
				,'IdDoctor'					=> $logs['IdDoctor']
				,'NameDoctor'				=> $logs['NameDoctor']
				,'IdCompany'				=> $logs['IdCompany']
				,'NameCompany'			=> $logs['NameCompany']
				,'TransactionType'			=> $logs['TransactionType']
				,'IdItemPrice'				=> $logs['IdItemPrice']
				//,'ItemUsedItemPrice'			=> $logs['ItemUsedItemPrice']
				,'CodeItemPrice'				=> $logs['CodeItemPrice']
				,'DescriptionItemPrice'		=> $logs['DescriptionItemPrice']
				,'PriceGroupItemPrice'		=> $logs['PriceGroupItemPrice']
				,'AmountItemPrice'			=> $logs['AmountItemPrice']
				,'AmountRemaining'			=> $logs['AmountRemaining']
				,'HCardNumber'				=> $logs['HCardNumber']
				,'GroupItemMaster'			=> $logs['GroupItemMaster']
				//,'UsedPercentDefault'			=> $logs['UsedPercentDefault']
				//,'ShowCompanyId'			=> $logs['ShowCompanyId']
				,'InputBy'					=> $logs['InputBy']
				,'InputId'					=> $logs['InputId']
				,'Status'					=> $logs['Status']
				,'Stat'					=> $logs['Stat']
				,'Token'					=> $logs['Token']
				,'SystemUpdateTime'			=> $logs['SystemUpdateTime']
				]
			);
		
			echo $trans->Id; echo "<br>";
		
		}
		*/
		
		$auditPaymentHis =  DB::connection('CMS')->select(" SELECT * FROM `Audit`.`CMSPaymentHistory` where `Id` IN( '1067633','1067708', '1067783', '1067858')  ");
		
		foreach($auditPaymentHis as $his)
		{
			echo $his->PaymentHistoryId; echo "<br>";
			$logs = json_decode($his->Logs, true); echo "<br>";
			
			
			 DB::connection('CMS')->table('PaymentHistory')->updateOrInsert
			(
				['Id' 						=> $his->PaymentHistoryId],
				['IdQueue'					=> $logs['IdQueue']
				,'IdTransaction'				=> $logs['IdTransaction']
				,'CurrentItemAmount'			=> $logs['CurrentItemAmount']
				,'ItemAmount'				=> $logs['ItemAmount']
				,'BalanceAmount'			=> $logs['BalanceAmount']
				,'RemainingAmount'			=> $logs['RemainingAmount']
				,'ProviderType'				=> $logs['ProviderType']
				,'BillTo'					=> $logs['BillTo']
				,'ORNum'					=> $logs['ORNum']
				,'CoverageType'				=> $logs['CoverageType']
				,'CoverageAmount'			=> $logs['CoverageAmount']
				,'PaymentType'				=> $logs['PaymentType']
				,'RefNo'					=> $logs['RefNo']
				,'PayAmount'				=> $logs['PayAmount']
				,'BankName'				=> $logs['BankName']
				,'doWhileInsert'				=> $logs['doWhileInsert']
				,'DiscType'					=> $logs['DiscType']
				,'DiscId'					=> $logs['DiscId']
				,'DiscAmount'				=> $logs['DiscAmount']
				,'LoyaltyId'				=> $logs['LoyaltyId']
				,'LoyaltyPoint'				=> $logs['LoyaltyPoint']
				//,'AgentCode'				=> $logs['AgentCode']
				//,'AgentName'				=> $logs['AgentName']
				,'Status'					=> $logs['Status']
				,'InputBy'					=> $logs['InputBy']
				,'InputDate'				=> $logs['InputDate']
				,'UpdateBy'				=> $logs['UpdateBy']
				,'UpdateDate'				=> $logs['UpdateDate']
				,'DeletedReason'			=> $logs['DeletedReason']
				
				]
			);
		
			echo $his->Id; echo "<br>";
		
		}
	
	
	}
    
	

	
	public function HCLABUpdateEmail()
	{
		echo "Update HCLAB Email"; ///->whereNull('RDOB')
		$CMSEmails = DB::connection('Eros')->select("select * from `Patient` where (`InputDate` = '".date('Y-m-d')."' or `UpdateDate` = '".date('Y-m-d')."') and `Email` NOT LIKE '' and (`RDOB` is null or `RDOB` LIKE 'reUpdate') ");
	
		foreach($CMSEmails as $email)
		{
			echo "<br>Updated Email for ".$email->Code . "  =>  "  . $email->Email;
			DB::connection('oraCENh')->table('CUST_MASTER')->where('DBCODE', $email->Code)->update(['DBEMAIL' => $email->Email]);
			 DB::connection('Eros')->table('Patient')->where('Id', $email->Id)->update(['RDOB' => 'Updated']);
		}
			//DB::connection('oraCENe')->table('PATIENT_MASTER')->updateOrInsert
		echo "<br>Done";
  
	}   
	public function CMSQueueUpdateForPayment()
	{
		$BUCode = "TAR";
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.55.154.25)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
	
		$queDatas =  DB::connection('CMS')->table('Queue')
									->where('Queue.Status', '=','201')
									->where('Queue.IdBU', 'LIKE', $BUCode)
									//->where('Queue.Id', '723683')
									->where('Queue.Date', date('Y-m-d'))
									->limit(1)
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
					else if( ($select->Status == '300' || $select->Status == '210') )
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
				else if( count($selects) == 0 &&  $mins > 15 )
				{
					DB::connection('CMS')->table('Queue')->where('Id', $que->Id)->update(['Status' => 900]);
					
					 $AccessionDelete = DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->Id)->get(array('*'));
					
					foreach($AccessionDelete as $acc)
					{
						DB::connection('oraTARe')->table('BILLING_TRX_DTL')->where('BTD_TRXNO', '=', $acc->AccessionNo)->delete();
						DB::connection('oraTARe')->table('BILLING_TRX_HDR')->where('BTH_TRXNO', '=', $acc->AccessionNo)->update(['bth_billing_status' => 'C', 'BTH_CLIN_INFO' => 'Cancelled Transaction' ]);
						
						$PKGsql = "BEGIN PKG_000.ADD_MESSAGE_QUEUE( message_type => 'ORM^O01', reference_no => '".$acc->AccessionNo."', order_status => 'CA' ); END; ";
						$exSql = oci_parse($conn, $PKGsql);
						oci_execute($exSql);
					
					}
				}
		}
		echo "--Done";
	}
	public function ErosForHL7() // change to push data from CMS to EROS
	{  
		$BUCode = "TAR";
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.55.154.25)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
		
		//insert hdr 210000001034
		
		DB::connection('CMS')->beginTransaction();
		DB::connection('oraTARe')->beginTransaction();
		
		$qTrans =  DB::connection('CMS')->table('Queue')
								->leftJoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
								->where('Queue.Status', '=','210')
								->where('Queue.IdBU', 'LIKE', $BUCode)
								->where('Queue.Date', '=', date('Y-m-d'))
								//->where('Queue.ErosStatus', 'NOT LIKE', 'reUpdate')
								//->where('Queue.Id', '=', '1356244')
								->limit(1)
								->orderBy('Queue.Id', 'DESC')
								->get(array('Patient.Code as PCode', 'Patient.FullName', 'Patient.LastName', 'Patient.FirstName', 'Patient.MiddleName', 'Patient.Gender', 'Patient.DOB', 'Patient.Address', 'Patient.Moblie', 'Suffix', 'Prefix', 'Patient.Moblie',  'Patient.Email', 'Patient.Barangay', 'Patient.City',
								'Queue.Code as QCode', 'Queue.Id as QId', 'Queue.Notes as QNotes', 'Queue.AccessionNo'));
			
		foreach($qTrans as $que)
		{
			$trans = DB::connection('CMS')->table('Transactions')->where('IdQueue', $que->QId)->where('Status','210')->groupBy('IdDoctor', 'IdCompany')->get(array('*'));
			
			foreach($trans as $trx)
			{
				echo $que->QId. "<br>";
				$AccessionData =  DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->where('IdCompany', $trx->IdCompany)->where('IdDoctor', $trx->IdDoctor)->get(array('*'));
				
				if(count($AccessionData) == 0)
				{
					$hdrTrx = DB::connection('oraTARe')->table('AUTONO')->where('AUTO_TYPE', 'LIKE', 'TRX')->get(array('auto_number'))[0]->auto_number;
					$NEWhdrTrx = $hdrTrx + 1;
					//echo $nCode = date('y').sprintf('%10d', $NEWhdrTrx); echo "<br>";
					echo $nCode = $BUCode.date('y').str_pad($NEWhdrTrx, 10, '0', STR_PAD_LEFT);
					DB::connection('oraTARe')->table('AUTONO')->where('AUTO_TYPE', 'LIKE', 'TRX')->update(['auto_number' => $NEWhdrTrx ]);
					
					DB::connection('CMS')->table('AccessionNo')->insert(['AccessionNo' =>$nCode ,'IdQueue' => $que->QId, 'IdCompany' => $trx->IdCompany, 'IdDoctor' =>$trx->IdDoctor ]);
				}
				elseif(count($AccessionData) != 0 )
				{
					$nCode = $AccessionData[0]->AccessionNo;
					DB::connection('oraTARe')->table('BILLING_TRX_DTL')->where('BTD_TRXNO', 'LIKE', $nCode)->delete();
				}
				
				echo $que->FullName; echo "<br>";
				/////// check patient record
				$barangayD = DB::connection('CMS')->table('zip')->where('zip_code', $que->Barangay)->get(array('zip_name'));
				$barangay = (count($barangayD) != 0)?$barangayD[0]->zip_name:'';
				
				$cityD = DB::connection('CMS')->table('city')->where('city_id', $que->City)->get(array('city_name'));
				$city = (count($cityD) != 0)?$cityD[0]->city_name:'';
				 DB::connection('oraTARe')->table('PATIENT_MASTER')->updateOrInsert
							(
								['PM_PID' 				=> $que->PCode],
								['PM_FULLNAME'		=> $que->FullName
								,'PM_LASTNAME'		=> $que->LastName
								,'PM_FIRSTNAME'		=> $que->FirstName
								,'PM_MIDNAME'			=> $que->MiddleName
								,'PM_GENDER'			=> $que->Gender
								,'PM_DOB'				=> $que->DOB
								,'PM_ADDRESS'			=> $que->Address
								,'PM_MOBILENO'		=> $que->Moblie
								,'PM_PREFIX'			=> $que->Prefix
								,'PM_SUFFIX'			=> $que->Suffix
								,'PM_PASSPORTNO'		=> ''
								,'PM_NATIONALITY'		=> ''
								,'PM_CREATED_ON'		=> date('Y-m-d')
								,'PM_CREATED_BY'		=> 'CMS-'.$BUCode
								,'PM_UPDATE_ON'		=> date('Y-m-d')
								,'PM_UPDATE_BY'		=> 'CMS-'.$BUCode
								,'PM_LASTVISIT'		=> ''
								,'PM_EMAIL'			=> $que->Email
								,'PM_BARANGAY'		=> $barangay
								,'PM_CITY'				=> $city
								]
							);
				
				
				$insList =  DB::connection('CMS')->table('Transactions')->where('Transactions.IdQueue', $que->QId)->where('Transactions.Status','210')->where('Transactions.IdDoctor',$trx->IdDoctor)->where('Transactions.IdCompany',$trx->IdCompany)
						->leftJoin('Eros.ItemMaster' , 'Transactions.CodeItemPrice', '=', 'Eros.ItemMaster.Code')
						->leftJoin('Eros.Company' , 'Transactions.IdCompany', '=', 'Eros.Company.Id')
						->leftJoin('Eros.Physician' , 'Transactions.IdDoctor', '=', 'Eros.Physician.Id')
						->orderBy('Transactions.CodeItemPrice', 'ASC')
						//->where('ItemMaster.Group', 'LAB')
						->get(array('Transactions.Id as TransID','Eros.ItemMaster.OldCode','Transactions.CodeItemPrice','Transactions.AmountItemPrice','Transactions.PriceGroupItemPrice','ItemMaster.Group as IGroup', 'Company.ErosCode as ComCode', 'Physician.ErosCode as PhyCode'));
				$total = 0;
				$dateSet = date('Y-m-d H:i:s');//\Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y');
			
			
				foreach($insList as $iList)
				{
					echo "TRX = ". $iList->TransID;
					echo "<br>";
					echo "CodeItemPrice = ". $iList->CodeItemPrice;
					echo "<br>";
					echo "NewCode = ".$itemMasterNewCode =  DB::connection('Eros')->table('ItemMaster')->wherein('DepartmentGroup', ['IMAGING','RAD'] )->where('Code', 'like', $iList->CodeItemPrice)->get(array('Code','OldCode', 'OldGroup', 'DepartmentGroup', 'AllowQty'));
					echo "<br>";
					echo "OldCode = ".$itemMasterOldCode =  DB::connection('Eros')->table('ItemMaster')->whereNotIn('DepartmentGroup', ['IMAGING','RAD'] )->where('Code', 'like', $iList->CodeItemPrice)->get(array('Code','OldCode', 'OldGroup', 'DepartmentGroup', 'AllowQty'));
					echo "<br>";
					
					$allowedQty = 0;
					$DepartmentGroup = "";
					
					if($iList->PriceGroupItemPrice == "Package" )
					{
						$BGroup = "PACK";
						if( $iList->CodeItemPrice == "SF" && count($itemMasterNewCode) != 0)
						{
							echo "ItemCode = ".$CodeItem =  $itemMasterNewCode[0]->OldCode;
						}
						elseif( $iList->CodeItemPrice == "SF" && count($itemMasterOldCode) != 0)
						{
							echo "ItemCode = ".$CodeItem =  $itemMasterOldCode[0]->OldCode;
						}
						else
						{
							echo "ItemCode = ".$CodeItem =  $iList->CodeItemPrice;
						}
					
					}
					elseif($iList->PriceGroupItemPrice == "Item"  && count($itemMasterNewCode) != 0)
					{
						$BGroup = $itemMasterNewCode[0]->OldGroup;
						echo "ItemCode = ". $CodeItem = ($itemMasterNewCode[0]->DepartmentGroup ==  'IMAGING' || $itemMasterNewCode[0]->DepartmentGroup ==  'RAD' || $itemMasterNewCode[0]->DepartmentGroup == 'LABORATORY'  ) ? $itemMasterNewCode[0]->OldCode : $itemMasterNewCode[0]->Code;
						$allowedQty = $itemMasterNewCode[0]->AllowQty;
						$DepartmentGroup = $itemMasterNewCode[0]->DepartmentGroup;
					
						
					}
					elseif($iList->PriceGroupItemPrice == "Item"  && count($itemMasterOldCode) != 0)
					{
					
						$BGroup = $itemMasterOldCode[0]->OldGroup;
						echo "ItemCode = ". $CodeItem = $itemMasterOldCode[0]->OldCode;
						$allowedQty = $itemMasterOldCode[0]->AllowQty;
						$DepartmentGroup = $itemMasterOldCode[0]->DepartmentGroup;
						
						
					}
					
					
					if($allowedQty != 0 && ($DepartmentGroup != "CONSULTATION"))
					{
						$haveItem = DB::connection('CMS')->table('AccessionNo')->where('AccessionNo', 'LIKE', $nCode)->where('IdQueue', $que->QId)->where('IdCompany', $trx->IdCompany)->where('IdDoctor', $trx->IdDoctor)->where('ItemCode', 'LIKE', $iList->CodeItemPrice)->get(array('*'));	
						if(count($haveItem) == 0)
						{
							$DDhdrTrx = DB::connection('oraTARe')->table('AUTONO')->where('AUTO_TYPE', 'LIKE', 'TRX')->get(array('auto_number'))[0]->auto_number;
							$DDNEWhdrTrx = $DDhdrTrx + 1;
							//echo $nCode = date('y').sprintf('%10d', $NEWhdrTrx); echo "<br>";
							echo $DDnCode = $BUCode.date('y').str_pad($DDNEWhdrTrx, 10, '0', STR_PAD_LEFT);
							DB::connection('oraTARe')->table('AUTONO')->where('AUTO_TYPE', 'LIKE', 'TRX')->update(['auto_number' => $DDNEWhdrTrx ]);
							DB::connection('CMS')->table('AccessionNo')->updateOrInsert(['AccessionNo' => $DDnCode, 'IdQueue' =>$que->QId, 'IdDoctor' =>$trx->IdDoctor, 'IdCompany' =>$trx->IdCompany,'ItemCode' =>$iList->CodeItemPrice ]);
							
							echo "<br>";
							DB::connection('oraTARe')->table('BILLING_TRX_DTL')->insert(
								[
									'BTD_TRXNO'			=> $DDnCode
									,'BTD_ITEM_CODE'		=> $CodeItem
									,'BTD_ICD_FLAG'		=> 'N'
									,'BTD_ITEM_PRICE'		=> $iList->AmountItemPrice
									,'BTD_UPDATE_ON'		=> $dateSet
									,'BTD_UPDATE_BY'		=> 'CMS-'.$BUCode
									,'BTD_AMOUNT'			=> $iList->AmountItemPrice
									//,'BTD_HIDE'		
									//,'BTD_ICDCODE'
									//,'BTD_MODIFIER'
									,'BTD_QTY'			=> '1'
									,'BTD_ITEM_GROUP'		=> $BGroup
								]
							);
							DB::connection('oraTARe')->table('BILLING_TRX_HDR')->updateOrInsert( 
								['BTH_TRXNO'			=> $DDnCode],
								['BTH_TRXDT'			=> $dateSet
								,'BTH_PID'				=> $que->PCode
								,'BTH_SOURCE'			=> '230'
								,'BTH_COMPANY'		=> $insList[0]->ComCode
								,'BTH_CLINICIAN'		=> $insList[0]->PhyCode
								,'BTH_ICD'				=> $que->QCode    //  default null
								,'BTH_CLIN_INFO'		=> $que->QNotes   //queue id code
								,'BTH_CREATED_ON'		=> $dateSet
								,'BTH_CREATED_BY'		=> 'CMS-'.$BUCode
								,'BTH_UPDATE_ON'		=>  $dateSet
								,'BTH_UPDATE_BY'		=> 'CMS-'.$BUCode
								,'BTH_BILLING_STATUS'	=> 'P'
								,'BTH_PRIORITY'		=> 'R'
								//BTH_ACCOUNT_ID default null
								,'BTH_DISCOUNT'		=> ''
								,'BTH_GRAND_TOTAL'		=> $total
								,'BTH_PAYMENT_TYPE'	=> 'C'
								//BTH_UNDO default null
								,'BTH_CANCEL'			=> ''
								,'BTH_BRANCH_CODE'	=> 'TAR'
								,'BTH_PAYCASH'			=> ''
								,'BTH_PAYDEBIT'		=> ''
								,'BTH_PAYCREDIT'		=> ''
								,'BTH_PAYCHECK'		=> ''
								,'BTH_CODECREDIT'		=> ''
								,'BTH_COPAYDUE'		=> ''
								,'BTH_COPAY_PERCENT'	=> ''
								//BTH_SF
								//BTH_ACCOUNT_ID2
								//BTH_ACCOUNT_ID3
								//BTH_ACCOUNT_ID4
								//BTH_ACCOUNT_NAME1
								//BTH_ACCOUNT_NAME2
								//BTH_ACCOUNT_NAME3
								//BTH_ACCOUNT_NAME4
								//BTH_ADJUSTMENT
								//BTH_BALANCE
								//BTH_CLAIMNO
								//BTH_COLDT
								//BTH_ELECTRONIC_CLAIM
								//BTH_FASTING
								,'BTH_NPI'				=> '' // ornumber
								//BTH_PAPER_CLAIM
								//BTH_PAYOR
								//BTH_PHLEB
								//BTH_WROTEOFF
								//BTH_CREDIT_NO
								//BTH_CHECK_NO
								,'BTH_SC_FLAG'			=> 'F'
								//BTH_SC_NO
								//BTH_SC_DISCOUNT
								]
							);
							$PKGsql = "BEGIN PKG_000.ADD_MESSAGE_QUEUE( message_type => 'ORM^O01', reference_no => '".$DDnCode."', order_status => 'NW' ); END; ";
							$exSql = oci_parse($conn, $PKGsql);
							oci_execute($exSql);	
						}
						
					} // end Allowed Qty
					elseif($CodeItem != 'HPLUS')
					{
						echo "<br>";
						DB::connection('oraTARe')->table('BILLING_TRX_DTL')->insert(
							[
								'BTD_TRXNO'			=> $nCode
								,'BTD_ITEM_CODE'		=> $CodeItem
								,'BTD_ICD_FLAG'		=> 'N'
								,'BTD_ITEM_PRICE'		=> $iList->AmountItemPrice
								,'BTD_UPDATE_ON'		=> $dateSet
								,'BTD_UPDATE_BY'		=> 'CMS-'.$BUCode
								,'BTD_AMOUNT'			=> $iList->AmountItemPrice
								//,'BTD_HIDE'		
								//,'BTD_ICDCODE'
								//,'BTD_MODIFIER'
								,'BTD_QTY'			=> '1'
								,'BTD_ITEM_GROUP'		=> $BGroup
							]
						);
						
						DB::connection('CMS')->table('AccessionNo')->updateOrInsert(['AccessionNo' => $nCode, 'IdQueue' =>$que->QId, 'IdCompany' =>$trx->IdDoctor, 'ItemCode' =>$iList->CodeItemPrice ]);
					}
					$total += $iList->AmountItemPrice;
					DB::connection('CMS')->table('Transactions')->where('Id', $iList->TransID)->update(['Status' => 300]);
					
					
					
				}
				// HDR   01/17/2023 10:15 am
				DB::connection('oraTARe')->table('BILLING_TRX_HDR')->updateOrInsert( 
					['BTH_TRXNO'			=> $nCode],
					['BTH_TRXDT'			=> $dateSet
					,'BTH_PID'				=> $que->PCode
					,'BTH_SOURCE'			=> '230'
					,'BTH_COMPANY'		=> $insList[0]->ComCode
					,'BTH_CLINICIAN'		=> $insList[0]->PhyCode
					,'BTH_ICD'				=> $que->QCode    //  default null
					,'BTH_CLIN_INFO'		=> $que->QNotes   //queue id code
					,'BTH_CREATED_ON'		=> $dateSet
					,'BTH_CREATED_BY'		=> 'CMS-'.$BUCode
					,'BTH_UPDATE_ON'		=>  $dateSet
					,'BTH_UPDATE_BY'		=> 'CMS-'.$BUCode
					,'BTH_BILLING_STATUS'	=> 'P'
					,'BTH_PRIORITY'		=> 'R'
					//BTH_ACCOUNT_ID default null
					,'BTH_DISCOUNT'		=> ''
					,'BTH_GRAND_TOTAL'		=> $total
					,'BTH_PAYMENT_TYPE'	=> 'C'
					//BTH_UNDO default null
					,'BTH_CANCEL'			=> ''
					,'BTH_BRANCH_CODE'	=> 'TAR'
					,'BTH_PAYCASH'			=> ''
					,'BTH_PAYDEBIT'		=> ''
					,'BTH_PAYCREDIT'		=> ''
					,'BTH_PAYCHECK'		=> ''
					,'BTH_CODECREDIT'		=> ''
					,'BTH_COPAYDUE'		=> ''
					,'BTH_COPAY_PERCENT'	=> ''
					//BTH_SF
					//BTH_ACCOUNT_ID2
					//BTH_ACCOUNT_ID3
					//BTH_ACCOUNT_ID4
					//BTH_ACCOUNT_NAME1
					//BTH_ACCOUNT_NAME2
					//BTH_ACCOUNT_NAME3
					//BTH_ACCOUNT_NAME4
					//BTH_ADJUSTMENT
					//BTH_BALANCE
					//BTH_CLAIMNO
					//BTH_COLDT
					//BTH_ELECTRONIC_CLAIM
					//BTH_FASTING
					,'BTH_NPI'				=> '' // ornumber
					//BTH_PAPER_CLAIM
					//BTH_PAYOR
					//BTH_PHLEB
					//BTH_WROTEOFF
					//BTH_CREDIT_NO
					//BTH_CHECK_NO
					,'BTH_SC_FLAG'			=> 'F'
					//BTH_SC_NO
					//BTH_SC_DISCOUNT
					]
				);
				
				if(count($AccessionData) == 0)
				{
					$PKGsql = "BEGIN PKG_000.ADD_MESSAGE_QUEUE( message_type => 'ORM^O01', reference_no => '".$nCode."', order_status => 'NW' ); END; ";
					$exSql = oci_parse($conn, $PKGsql);
					oci_execute($exSql);	
				}
				else // update 
				{ // RP
					$PKGsql = "BEGIN PKG_000.ADD_MESSAGE_QUEUE( message_type => 'ORM^O01', reference_no => '".$nCode."', order_status => 'NW' ); END; ";
					$exSql = oci_parse($conn, $PKGsql);
					oci_execute($exSql);	
				}
				
				
				
			}
			//echo $que->QId;
			// update status
			$selects  = DB::connection('CMS')->table('Transactions')->where('IdQueue', '=', $que->QId)->get(array('Status'));
			$forBilling = 0;
			$paid = 0;
			//print_r($selects);
			//die();
			foreach($selects as $select)
			{
				if( $select->Status == '201' || $select->Status == '205' )
				{
					$forBilling = 1;
				}
				else if( $select->Status == '300')
				{
					$paid = 1;
				}
			}
			//echo $forBilling;
			//echo "<br>";
			//echo $paid;
			if( $forBilling == 0  && $paid == 1 )
			{
				DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 300]);
			}
			
		
		}
		
		
		
		
		DB::connection('CMS')->commit();  
		DB::connection('oraTARe')->commit();  
		oci_close($conn);
		
		
		echo '<br>Done';
	}
	##########EROS Billing HDR SYNC END######## 
   
   
   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
	//public function erosMakeHL7File() // soon
	//{
		//$msg = new Message("MSH|^~\&|1|\rPID|||abcd|\r"); // Either \n or \r can be used as segment endings
		//$pid = $msg->getSegmentByIndex(1);
		//echo $pid->getField(2); // prints 'abcd'
		//echo $msg->toString(true); // Prints entire HL7 string
		
	//	$txt = "MSH|^~\&|EROSBS|NWD|HCLAB|NWD|20221114094612||ADT^A04|EBS0000163588|P|2.3\n";
	//	$txt .= "PID|||L220000060138||^DUMMY, RICKY  VALMORES|6|20220101|F|9|10|NEW 32 KALAYAAN C^BATASAN HILLS^Q.C^NCR^|12|09610060876^^PH~12345678^^CP^ricky.valmores@gmailc.om~12345678^^FX|09610060876|ENGLISH|MARRIED|||SSS|20|21|ETHNIC|MANILA|24|25|\n";
	//	$txt .= "PV1||O|||||||||||||||||||||||||||||||||||||||||||";
	//	Storage::disk('local')->put('file.txt', 'Your content here');
	
	//}


   
    
}
