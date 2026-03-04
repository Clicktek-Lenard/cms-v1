<?php

namespace App\Http\Controllers\lin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

class LINController extends Controller
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
    
	public function HCLABUpdateEmail()
	{
		echo "Update HCLAB Email"; ///->whereNull('RDOB')
		$CMSEmails = DB::connection('Eros')->table('Patient')->where('UpdateDate', date('Y-m-d'))->where('Email', 'NOT LIKE', '')->get(array('*'));
		
		foreach($CMSEmails as $email)
		{
			echo "<br>Updated Email for ".$email->Code . "  =>  "  . $email->Email;
			DB::connection('oraLINh')->table('CUST_MASTER')->where('DBCODE', $email->Code)->update(['DBEMAIL' => $email->Email]);
			 DB::connection('Eros')->table('Patient')->where('Id', $email->Id)->update(['RDOB' => 'Email Updated '.date('Y-m-d H:i:s')]);
		}
			//DB::connection('oraLINe')->table('PATIENT_MASTER')->updateOrInsert
		echo "<br>Done";
  
	}   
    
    
    public function CMSQueueUpdateForPayment()
    {
	$queDatas =  DB::connection('CMSlin')->table('Queue')
								->where('Queue.Status', '=','201')
								->where('Queue.IdBU', 'LIKE', 'LIN')
								->limit(1)
								->get(array('*'));
	foreach($queDatas as $que)
	{
			$selects  = DB::connection('CMSlin')->table('Transactions')->where('IdQueue', '=', $que->Id)->get(array('Status'));
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
				else if( $select->Status == '300' || $select->Status == '210' )
				{
					$paid = 1;
				}
			}
			//echo $forBilling;
			//echo "<br>";
			//echo $paid;
			
			$mins = (strtotime(date('Y-m-d H:i:s')) - strtotime($queDatas[0]->DateTime)) / 60;
			
			if( $forBilling == 0  && $paid == 1 )
			{
				DB::connection('CMSlin')->table('Queue')->where('Id', $que->Id)->update(['Status' => 210]);
			}
			else if( count($selects) == 0 &&  $mins > 30 )
			{
				DB::connection('CMSlin')->table('Queue')->where('Id', $que->Id)->update(['Status' => 900]);
			}
			echo $que->Code;
	}
	echo "--Done";
    }
    
    public function LINErosForHL7() // change to push data from CMS to EROS
	{  
		//die();
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.52.154.20)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
		
		//insert hdr 210000001034
		
		DB::connection('CMSlin')->beginTransaction();
		
		$qTrans =  DB::connection('CMSlin')->table('Queue')
								->leftJoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
								->where('Queue.Status', '=','210')
								->where('Queue.IdBU', 'LIKE', 'LIN')
								//->where('Queue.Id', '194')
								->get(array('Patient.Code as PCode', 'Patient.FullName', 'Patient.LastName', 'Patient.FirstName', 'Patient.MiddleName', 'Patient.Gender', 'Patient.DOB', 'Patient.Address', 'Patient.Moblie', 'Suffix', 'Prefix', 'Patient.Moblie',  'Patient.Email', 'Patient.Barangay', 'Patient.City',
								'Queue.Code as QCode', 'Queue.Id as QId'));
//print_r($qTrans);
//die();				
		foreach($qTrans as $que)
		{
			$trans = DB::connection('CMSlin')->table('Transactions')->where('IdQueue', $que->QId)->where('Status','210')->groupBy('IdDoctor', 'IdCompany')->get(array('*'));
			foreach($trans as $trx)
			{
				/////// check patient record
				$barangayD = DB::connection('CMSlin')->table('zip')->where('zip_code', $que->Barangay)->get(array('zip_name'));
				$barangay = (count($barangayD) != 0)?$barangayD[0]->zip_name:'';
				
				$cityD = DB::connection('CMSlin')->table('city')->where('city_id', $que->City)->get(array('city_name'));
				$city = (count($cityD) != 0)?$cityD[0]->city_name:'';
				 DB::connection('oraLINe')->table('PATIENT_MASTER')->updateOrInsert
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
								,'PM_CREATED_BY'		=> 'CMS'
								,'PM_UPDATE_ON'		=> date('Y-m-d')
								,'PM_UPDATE_BY'		=> 'CMS'
								,'PM_LASTVISIT'		=> ''
								,'PM_EMAIL'			=> $que->Email
								,'PM_BARANGAY'		=> $barangay
								,'PM_CITY'				=> $city
								]
							);
				
				////// end check patient record
			
				$hdrTrx = DB::connection('oraLINe')->table('AUTONO')->where('AUTO_TYPE', 'LIKE', 'TRX')->get(array('auto_number'))[0]->auto_number;
				$NEWhdrTrx = $hdrTrx + 1;
				echo $nCode = date('y').sprintf('%08d', $NEWhdrTrx); echo "<br>";
				DB::connection('oraLINe')->table('AUTONO')->where('AUTO_TYPE', 'LIKE', 'TRX')->update(['auto_number' => $NEWhdrTrx ]);
			
				$insList =  DB::connection('CMSlin')->table('Transactions')->where('Transactions.IdQueue', $que->QId)->where('Transactions.Status','210')->where('Transactions.IdDoctor',$trx->IdDoctor)->where('Transactions.IdCompany',$trx->IdCompany)
						->leftJoin('Eros.ItemMaster' , 'Transactions.CodeItemPrice', '=', 'Eros.ItemMaster.Code')
						->leftJoin('Eros.Company' , 'Transactions.IdCompany', '=', 'Eros.Company.Id')
						->leftJoin('Eros.Physician' , 'Transactions.IdDoctor', '=', 'Eros.Physician.Id')
						//->where('ItemMaster.Group', 'LAB')
						->get(array('Transactions.Id as TransID','Transactions.CodeItemPrice','Transactions.AmountItemPrice','Transactions.PriceGroupItemPrice','ItemMaster.Group as IGroup', 'Company.ErosCode as ComCode', 'Physician.ErosCode as PhyCode'));
				$total = 0;
				$dateSet = date('Y-m-d H:i:s');//\Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-Y');
				foreach($insList as $iList)
				{
					$BGroup = ($iList->PriceGroupItemPrice =="Package") ? "PACK":$iList->IGroup;
					DB::connection('oraLINe')->table('BILLING_TRX_DTL')->insert(
					[
						'BTD_TRXNO'			=> $nCode
						,'BTD_ITEM_CODE'		=> $iList->CodeItemPrice
						,'BTD_ICD_FLAG'		=> 'N'
						,'BTD_ITEM_PRICE'		=> $iList->AmountItemPrice
						,'BTD_UPDATE_ON'		=> $dateSet
						,'BTD_UPDATE_BY'		=> 'CMS'
						,'BTD_AMOUNT'			=> $iList->AmountItemPrice
						//,'BTD_HIDE'		
						//,'BTD_ICDCODE'
						//,'BTD_MODIFIER'
						,'BTD_QTY'			=> '1'
						,'BTD_ITEM_GROUP'		=> $BGroup
					]
					);
					$total += $iList->AmountItemPrice;
					DB::connection('CMSlin')->table('Transactions')->where('Id', $iList->TransID)->update(['Status' => 300]);
					
				}
				// HDR   01/17/2023 10:15 am
				DB::connection('oraLINe')->table('BILLING_TRX_HDR')->insert( 
				[
					'BTH_TRXNO'			=> $nCode
					,'BTH_TRXDT'			=> $dateSet
					,'BTH_PID'				=> $que->PCode
					,'BTH_SOURCE'			=> '220'
					,'BTH_COMPANY'		=> $insList[0]->ComCode
					,'BTH_CLINICIAN'		=> $insList[0]->PhyCode
					//BTH_ICD  default null
					,'BTH_CLIN_INFO'		=> $que->QCode   //queue id code
					,'BTH_CREATED_ON'		=> $dateSet
					,'BTH_CREATED_BY'		=> 'CMS'
					,'BTH_UPDATE_ON'		=>  $dateSet
					,'BTH_UPDATE_BY'		=> 'CMS'
					,'BTH_BILLING_STATUS'	=> 'P'
					,'BTH_PRIORITY'		=> 'R'
					//BTH_ACCOUNT_ID default null
					,'BTH_DISCOUNT'		=> ''
					,'BTH_GRAND_TOTAL'		=> $total
					,'BTH_PAYMENT_TYPE'	=> 'C'
					//BTH_UNDO default null
					,'BTH_CANCEL'			=> ''
					,'BTH_BRANCH_CODE'	=> 'LIN'
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
				
				
			$PKGsql = "BEGIN PKG_000.ADD_MESSAGE_QUEUE( message_type => 'ORM^O01', reference_no => '".$nCode."', order_status => 'NW' ); END; ";
			$exSql = oci_parse($conn, $PKGsql);
			oci_execute($exSql);	
			
			}
			//echo $que->QId;
			// update status
			$selects  = DB::connection('CMSlin')->table('Transactions')->where('IdQueue', '=', $que->QId)->get(array('Status'));
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
				DB::connection('CMSlin')->table('Queue')->where('Id', $que->QId)->update(['Status' => 300]);
			}
			
		
		}
		
		
		
		
		DB::connection('CMSlin')->commit();  
		
		
		
		echo '<br>Done';
	}
	##########EROS Billing HDR SYNC END######## 
    
  
}
