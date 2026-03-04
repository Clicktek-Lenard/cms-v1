<?php

namespace App\Http\Controllers\cms\pages\payment;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\cms\Transactions;
use App\Models\eros\DiscountType;
use App\Models\eros\AgentEmpName;
use App\Models\cms\Queue;

class PaymentPageTransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	
	//return view('cms/pages.payment', ['doctors' => array(), 'companys' => array() ] );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	//post transaction and history
	$QueueID = $_POST['QueueID'];
	$Id = $_POST['_Id'];
		
	if( $QueueID === $Id  )
	{
		$itemSelected 		= $_POST['itemSelected'];
		$modalProviderType	= $_POST['modalProviderType'];
		$BillTo 			= $_POST['BillTo'];
		$AgentId			= $_POST['Agent'];
		$AgentName			= $_POST['AgentName'];
		$ExpiryDatePWD 		= $_POST['ExpiryDatePWD'];
		$discountID 		= $_POST['PWD'];
		$Cardnumber			= $_POST['Cardnumber'];
		$discType			= (isset($_POST['DiscType']) && !empty($_POST['DiscType']))?$_POST['DiscType']:'';
		$discId			= (isset($_POST['DiscId']) && !empty($_POST['DiscId']))?$_POST['DiscId']:'';
		$discAmount		= (isset($_POST['DiscAmount']) && !empty($_POST['DiscAmount']))? number_format($_POST['DiscAmount'], 2, '.', '') : 0.00;
		$CoverageType 		=  ($modalProviderType == 'PATIENT')?'':( (isset($_POST['CoverageType']) && !empty($_POST['CoverageType']))?$_POST['CoverageType']:'');
		$coPayAmount		= (isset($_POST['coPayAmount']) && !empty($_POST['coPayAmount']))? number_format($_POST['coPayAmount'], 2, '.', '') : 0.00;
		$hmoId			= (isset($_POST['hmoId']) && !empty($_POST['hmoId']))?$_POST['hmoId']:'';
		$cardName		= (isset($_POST['cardName']) && !empty($_POST['cardName']))?$_POST['cardName']:''; 
		$PaymentType		= (isset($_POST['modalSelect']) && !empty($_POST['modalSelect']))?$_POST['modalSelect']:''; 
		$ORnumber		= (isset($_POST['ORnumber']) && !empty($_POST['ORnumber']))?$_POST['ORnumber']:'';
		$iCashAmount = $cashAmount		= (isset($_POST['cashAmount']) && !empty($_POST['cashAmount']))? number_format($_POST['cashAmount'], 2, '.', '') : 0.00;
		$gcashRefNo		= (isset($_POST['gcashRefNo']) && !empty($_POST['gcashRefNo']))?$_POST['gcashRefNo']:''; 
		$iGcashAmount = $gcashAmount		= (isset($_POST['gcashAmount']) && !empty($_POST['gcashAmount']))? number_format($_POST['gcashAmount'], 2, '.', '') : 0.00; 
		$modalCreditBank	= (isset($_POST['modalCreditBank']) && !empty($_POST['modalCreditBank']))?$_POST['modalCreditBank']:'';
		$creditRefNo		= (isset($_POST['creditRefNo']) && !empty($_POST['creditRefNo']))?$_POST['creditRefNo']:''; 
		$iCreditAmount = $creditAmount		= (isset($_POST['creditAmount']) && !empty($_POST['creditAmount']))? number_format($_POST['creditAmount'], 2, '.', '') : 0.00; 
		$modalChequeBank	= (isset($_POST['modalChequeBank']) && !empty($_POST['modalChequeBank']))?$_POST['modalChequeBank']:'';
		$chequeRefNo		= (isset($_POST['chequeRefNo']) && !empty($_POST['chequeRefNo']))?$_POST['chequeRefNo']:''; 
		$iChequeAmount 	= $chequeAmount		= (isset($_POST['chequeAmount']) && !empty($_POST['chequeAmount']))? number_format($_POST['chequeAmount'], 2, '.', '') :0.00;
		$modalOnlineBank	= (isset($_POST['modalOnlineBank']) && !empty($_POST['modalOnlineBank']))?$_POST['modalOnlineBank']:'';
		$onlineRefNo		= (isset($_POST['onlineRefNo']) && !empty($_POST['onlineRefNo']))?$_POST['onlineRefNo']:''; 
		$iOnlineAmount 	= $onlineAmount		= (isset($_POST['onlineAmount']) && !empty($_POST['onlineAmount']))? number_format($_POST['onlineAmount'], 2, '.', '') : 0.00;
		$totalAmount		= (isset($_POST['totalAmount']) && !empty($_POST['totalAmount']))? number_format($_POST['totalAmount'], 2, '.', '') : 0.00;
		$BalanceAmount = 0.00;
		$iBalanceAmount = 0.00;
		$GRemaining = 0.00;
		$arrayPostPayment = (isset($_POST['PaymentType']) && !empty($_POST['PaymentType']))?$_POST['PaymentType']:array(array('Id'=>''));
		$AdvancePay = 0.00;
		$itemSelectedCount = 0;

		DB::beginTransaction();  // added 2023-07-25 1:48pm
		if( is_array($itemSelected) )
		{
			$itemSelectedLast = end($itemSelected)['Id'];
			$arrayPostPaymentLast = end($arrayPostPayment)['Id'];
		
			foreach($itemSelected as $item) // item selected
			{
				$antechecking = DB::connection('CMS')
					->table('Queue')
					->where('Id', $QueueID)
					->get();

				if($_POST['pwdId'] == '8')
				{
					DB::connection('Eros')->table('Patient')
						->leftjoin('CMS.Queue', 'Eros.Patient.Id', '=', 'CMS.Queue.IdPatient' )
						->where('Queue.Id', $QueueID)
						->update([
							'PWD' => $discountID,
							'ExpiryDatePWD'=> date('Y-m-d', strtotime($ExpiryDatePWD))
					]);
				}elseif($_POST['pwdId'] == '6')
				{
					DB::connection('Eros')->table('Patient')
					->leftjoin('CMS.Queue', 'Eros.Patient.Id', '=', 'CMS.Queue.IdPatient' )
					->where('Queue.Id', $QueueID)
					->update([
						'SeniorId' => $discountID,
				]);
				}
				//post free APE  for card availment
				//update status only
				if( $item['toggle-group'] == "CARD" && floatval($item['toggle-itemused']) != 0 )
				{	//$histLastInsertID = 0;
					DB::connection('CMS')->table('Transactions')
							->where('IdQueue', $QueueID)
							->where('Id',  $item['Id'])
							->update([
								'Status'=> '210'
							]);
							
					continue;
				}
				if( $item['toggle-group'] != "CARD" && floatval($item['toggle-itemused']) == 0 && $item['toggle-compasubgroup'] == "CARD")
				{	//$histLastInsertID = 0;
						DB::connection('CMS')->table('Transactions')
							->where('IdQueue', $QueueID)
							->where('Id',  $item['Id'])
							->update([
								'HCardNumber'=> str_replace('-', '',$Cardnumber)
							]);
				}

				$currentItemAmount = number_format(DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->get(array('AmountItemPrice'))[0]->AmountItemPrice, 2, '.', '');

						
					$BillStatus = 0;	
					
					if($GRemaining == 0.00 )
					{
						$GRemaining = number_format(DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->get(array('AmountRemaining'))[0]->AmountRemaining , 2, '.', '');
						
						if($GRemaining == 0.00)
						{
							continue;
						}
						else
						{
							$itemSelectedCount = floatval($itemSelectedCount) + 1;
						}
						
						$itemType = DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->get(array('PriceGroupItemPrice'))[0]->PriceGroupItemPrice;
						$allowdiscount = DB::connection('CMS')->table('Transactions')->leftjoin('Eros.ItemMaster', 'CMS.Transactions.CodeItemPrice', '=', 'Eros.ItemMaster.Code')->where('Transactions.Id', $item['Id'])->get(array('AllowDiscount'))[0]->AllowDiscount;
						$itemAmount = number_format($GRemaining, 2, '.', '');
					}
					$setItem = 0;
					
					
					foreach($arrayPostPayment as $Payitem) // payment type
					{
						if( $AdvancePay !== 0.00 && $item['Id'] === $setItem  )
						{
							continue;
						}
						
					
						$iBalanceAmount = number_format($BalanceAmount, 2, '.', ''); //get last balance
						$iAdvancePay = number_format($AdvancePay,2, '.', ''); // get last balance
						
						if( $Payitem['Id'] == 'Discounted'  )
						{
							
							$remaining =  number_format( floatval($GRemaining) - (floatval(abs($discAmount)) + floatval($AdvancePay) ),2, '.', '');
							if($remaining <= 0.00)
							{
								$AdvancePay = number_format(abs($remaining), 2, '.', '');
								$BillStatus = '210'; // Fully Paid
								$GRemaining = 0.00;
								$BalanceAmount = 0.00;
								$setItem = $item['Id'];
								$iDiscAmount = number_format(abs($remaining), 2, '.', '');
								$payAmount = number_format(abs($discAmount), 2, '.', '');
								unset($itemSelected[0]);
							}
							else
							{
								$AdvancePay = 0.00; // error with advance pays
								$BalanceAmount = number_format($remaining, 2, '.', '');
								$GRemaining = number_format($remaining, 2, '.', '');
								$BillStatus = '205';  // Partially Paid
								$remaining = 0.00;
								$iDiscAmount = 0.00;
								$payAmount = number_format(abs($discAmount), 2, '.', '');
								unset($arrayPostPayment[0]);
								//$payAmount = $cashAmount;
							}
							
							
							$histLastInsertID = DB::connection('CMS')->table('PaymentHistory')->insertGetId([
								'IdQueue'			=> $QueueID
								,'IdTransaction'		=> $item['Id']
								,'CurrentItemAmount' => number_format($currentItemAmount, 2, '.', '')
								,'ItemAmount'		=> ($iBalanceAmount != 0.00) ? number_format($iBalanceAmount, 2, '.', '') : number_format($itemAmount, 2, '.', '')
								,'BalanceAmount'	=> number_format($BalanceAmount, 2, '.', '')
								,'RemainingAmount'	=> number_format($remaining, 2, '.', '')
								,'ProviderType'		=> $modalProviderType 
								,'ORNum'			=> $ORnumber
								,'BillTo'			=> $BillTo
								,'CoverageType'		=> $Payitem['discountType']
								,'CoverageAmount'	=>  (abs($discAmount) !=0.00)? number_format(abs($discAmount), 2, '.', '') : 0.00
								,'PaymentType'		=> 'Discount'
								//,'RefNo'			=> $gcashRefNo
								,'PayAmount'		=> (abs($discAmount) !=0.00)? number_format(abs($discAmount), 2, '.', '') :   (($iAdvancePay !=0.00) ? number_format($iAdvancePay, 2, '.', '') : number_format($itemAmount, 2, '.', '') )
								//,'BankName'		=> $modalCreditBank
								,'InputBy'			=> Auth::user()->username
								,'Status'			=> $BillStatus
								,'doWhileInsert'		=> 'D'
								,'DiscType'			=> ($discType != 0 && $itemType == 'Item' && $allowdiscount == '1') ? number_format(floatval($currentItemAmount) * (floatval($discType) / 100 ), 2, '.', ''): 0.00
								,'DiscId'			=> $discId
								,'DiscAmount'		=> number_format($discAmount, 2, '.', '')
								,'AgentCode'		=> $AgentId
								,'AgentName'		=>$AgentName

							]);
							
							
							DB::connection('CMS')->table('Transactions')
							->where('IdQueue', $QueueID)
							->where('Id',  $item['Id'])
							->update([
								'AmountRemaining'	=> number_format($BalanceAmount, 2, '.', '')
								,'Status'			=> $BillStatus
								
							]);
							
							if( $discAmount != 0.00)
							{
								$firstHistInsertID = $histLastInsertID;
							}
							
							$discAmount = 0.00;
							array_values($arrayPostPayment);
							array_values($itemSelected);
						
						}
						// HMO PAY ALL
						else if( $coPayAmount != 0.00 || ($iAdvancePay != 0.00 && ($Payitem['Id'] != 'Cash'  || $Payitem['Id'] != 'GCash' || $Payitem['Id'] != 'Credit'  || $Payitem['Id'] != 'Cheque' || $Payitem['Id'] != 'Online')) )
						{
							$remaining =  number_format( floatval($GRemaining) - (floatval($coPayAmount) + floatval($AdvancePay) ), 2, '.', '' );
							if($remaining <= 0.00)
							{
								$AdvancePay = number_format(abs($remaining), 2, '.', '' );
								$BillStatus = '210'; // Fully Paid
								$GRemaining = 0.00;
								$BalanceAmount = 0.00;
								$setItem = $item['Id'];
								unset($itemSelected[0]);
								$payAmount = number_format($coPayAmount, 2, '.', '' );
							}
							else
							{
								$AdvancePay = 0.00; // error with advance pays
								$BalanceAmount = number_format($remaining, 2, '.', '' );
								$GRemaining = number_format($remaining, 2, '.', '' );
								$BillStatus = '205';  // Partially Paid
								$remaining = 0.00;
								$payAmount = number_format($coPayAmount,  2, '.', '' );
							}
							
							$histLastInsertID = DB::connection('CMS')->table('PaymentHistory')->insertGetId([
								'IdQueue'			=> $QueueID
								,'IdTransaction'		=> $item['Id']
								,'CurrentItemAmount' => number_format($currentItemAmount,  2, '.', '' )
								,'ItemAmount'		=> ($iBalanceAmount !=0.00) ? number_format($iBalanceAmount, 2, '.', '') : number_format($itemAmount, 2, '.', '')
								,'BalanceAmount'	=> number_format($BalanceAmount, 2, '.', '')
								,'RemainingAmount'	=> number_format($remaining, 2, '.', '')
								,'ProviderType'		=> ($CoverageType != '') ? $CoverageType : $modalProviderType
								,'ORNum'			=> $ORnumber // enabled to get ORnumber in OR printing
								,'BillTo'			=> $BillTo
								,'CoverageType'		=> $modalProviderType
								,'CoverageAmount'	=> number_format($coPayAmount, 2, '.', '')
								,'PaymentType'		=> 'CoPay'
								//,'RefNo'			=> $gcashRefNo
								,'PayAmount'		=> ($coPayAmount != 0.00)? number_format($coPayAmount, 2, '.', '') : (($iAdvancePay !=0.00) ? number_format($iAdvancePay, 2, '.', '') : number_format($itemAmount, 2, '.', ''))
								//,'BankName'		=> $modalCreditBank
								,'InputBy'			=> Auth::user()->username
								,'Status'			=> $BillStatus
								,'doWhileInsert'		=> 'C'
								,'DiscType'			=> ($discType != 0 && $itemType == 'Item' && $allowdiscount == '1') ? number_format(floatval($currentItemAmount) * (floatval($discType) / 100 ), 2, '.', '') : 0.00 
								,'DiscId'			=> $discId
								,'DiscAmount'		=> number_format($discAmount, 2, '.', '')
								,'AgentCode'		=> $AgentId
								,'AgentName'		=>$AgentName
							]);
							
							if( $coPayAmount != 0.00)
							{
								$firstHistInsertID = $histLastInsertID;
							}
							
							$coPayAmount = 0.00; 
							array_values($itemSelected);
							DB::connection('CMS')->table('Transactions')
								->where('IdQueue', $QueueID)
								->where('Id',  $item['Id'])
								->update([
									'AmountRemaining'	=> number_format($BalanceAmount, 2, '.', '')
									,'Status'			=> $BillStatus
									
							]);
							
							array_values($arrayPostPayment);
							array_values($itemSelected);
						
						}
						
						else if( $Payitem['Id'] == 'Cash' && $iCashAmount != 0.00   && ($BillStatus == '0' || $BillStatus == '205'  || $AdvancePay != 0.00) )
						{
						
							$remaining =  number_format( floatval($GRemaining)  - (floatval($cashAmount) + floatval($AdvancePay) ) , 2, '.', '');
							if($remaining <= 0.00)
							{
								$AdvancePay = number_format(abs($remaining), 2, '.', '');
								$BillStatus = '210'; // Fully Paid
								$GRemaining = 0.00;
								$BalanceAmount = 0.00;
								$setItem = $item['Id'];
								$iCashAmount = number_format(abs($remaining), 2, '.', '');
								unset($itemSelected[0]);
							}
							else
							{
								$AdvancePay = 0.00; // error with advance pays
								$BalanceAmount = number_format($remaining, 2, '.', '');
								$GRemaining = number_format($remaining, 2, '.', '');
								$BillStatus = '205';  // Partially Paid
								$remaining = 0.00;
								$iCashAmount = 0.00;
								unset($arrayPostPayment[0]);
								//$payAmount = $cashAmount;
							}
							
							
							
							if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount ==0.00 && $iAdvancePay == 0.00 && $cashAmount != 0.00){
								$payAmount = $cashAmount; $doWhileInsert = '1';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount != 0.00 ){
								$payAmount = $iBalanceAmount; $doWhileInsert = '2';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay != 0.00 ){
								$payAmount = $iAdvancePay; $doWhileInsert = '3';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay == 0.00){
								$payAmount = $itemAmount; $doWhileInsert = '4';
							}else if( $BalanceAmount  !=0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '5';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount ==0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '6';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount ==0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '7';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay == 0.00 && $cashAmount != 0.00){
								$payAmount = $cashAmount;  $doWhileInsert = '8';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay == 0.00 && $cashAmount != 0.00){
								$payAmount = $cashAmount;  $doWhileInsert = '9';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay == 0.00 ){
								$payAmount = $cashAmount; $doWhileInsert = '10';
							}else{
								$payAmount = -1;  $doWhileInsert = '11';
							}
							
							
							
							$histLastInsertID = DB::connection('CMS')->table('PaymentHistory')->insertGetId([
								'IdQueue'			=> $QueueID
								,'IdTransaction'		=> $item['Id']
								,'CurrentItemAmount' => number_format($currentItemAmount, 2, '.', '') 
								,'ItemAmount'		=> ($iBalanceAmount !=0.00) ? number_format($iBalanceAmount, 2, '.', '')  : number_format($itemAmount, 2, '.', '') 
								,'BalanceAmount'	=> number_format($BalanceAmount, 2, '.', '') 
								,'RemainingAmount'	=> number_format($remaining, 2, '.', '') 
								,'ProviderType'		=> 'PATIENT' //$modalProviderType
								,'ORNum'			=> $ORnumber
								,'BillTo'			=> $BillTo
								,'CoverageType'		=> $CoverageType
								,'CoverageAmount'	=> ($cashAmount !=0.00)? number_format($cashAmount, 2, '.', '') : 0.00
								,'PaymentType'		=> $Payitem['Id']
								//,'RefNo'			=> $gcashRefNo
								,'PayAmount'		=> ($cashAmount !=0.00)? number_format($cashAmount, 2, '.', '') :   (($iAdvancePay !=0.00) ? number_format($iAdvancePay, 2, '.', '') : number_format($itemAmount, 2, '.', ''))
								//,'BankName'		=> $modalCreditBank
								,'InputBy'			=> Auth::user()->username
								,'Status'			=> $BillStatus
								,'doWhileInsert'		=> $doWhileInsert
								,'DiscType'			=> ($discType != 0 && $itemType == 'Item' && $allowdiscount == '1') ? number_format(floatval($currentItemAmount) * (floatval($discType) / 100 ),  2, '.', ''): 0.00
								,'DiscId'			=> $discId
								,'DiscAmount'		=> number_format($discAmount,  2, '.', '')
								,'AgentCode'		=> $AgentId
								,'AgentName'		=>$AgentName
							]);
							
							DB::connection('CMS')->table('Transactions')
							->where('IdQueue', $QueueID)
							->where('Id',  $item['Id'])
							->update([
								'AmountRemaining'	=> number_format($BalanceAmount,  2, '.', '')
								,'Status'			=> $BillStatus
							
								
							]);
							
							if( $cashAmount != 0.00)
							{
								$firstHistInsertID = $histLastInsertID;
							}
							
							$cashAmount = 0.00; 
							array_values($arrayPostPayment);
							array_values($itemSelected);
							
							//DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->update(['AmountRemaining' => $AmountRemaining  ]);
						}
						else if( $Payitem['Id'] == 'GCash' && $iGcashAmount != 0.00 && ($BillStatus == '0' || $BillStatus == '205' || $AdvancePay != 0.00 ) )
						{ 
							$remaining =  number_format( floatval($GRemaining) - (floatval($gcashAmount) + floatval($AdvancePay) ), 2, '.', '');
							if($remaining <= 0.00)
							{
								$AdvancePay = number_format(abs($remaining), 2, '.', '');
								$BillStatus = '210'; // Fully Paid
								$GRemaining = 0.00;
								$BalanceAmount = 0.00;
								$setItem = $item['Id'];
								$iGcashAmount = number_format(abs($remaining), 2, '.', '');
								unset($itemSelected[0]);
							}
							else
							{
								$AdvancePay = 0.00;
								$BalanceAmount = number_format($remaining, 2, '.', '');
								$GRemaining = number_format($remaining, 2, '.', '');
								$BillStatus = '205';  // Partially Paid
								$remaining = 0.00;
								$iGcashAmount = 0.00;
								unset($arrayPostPayment[0]);
							}
							if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount ==0.00 && $iAdvancePay == 0.00 && $gcashAmount != 0.00){
								$payAmount = $gcashAmount; $doWhileInsert = '1';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount != 0.00){
								$payAmount = $iBalanceAmount; $doWhileInsert = '2';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '3';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay == 0.00){
								$payAmount = $itemAmount; $doWhileInsert = '4';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '5';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount ==0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '6';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount ==0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '7';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay == 0.00 && $gcashAmount != 0.00){
								$payAmount = $gcashAmount; $doWhileInsert = '8';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay == 0.00 && $gcashAmount != 0.00){
								$payAmount = $gcashAmount; $doWhileInsert = '9';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay == 0.00 ){
								$payAmount = $gcashAmount; $doWhileInsert = '10';
							}else{
								$payAmount = -1;  $doWhileInsert = '11';
							}
							
							
							$histLastInsertID = DB::connection('CMS')->table('PaymentHistory')->insertGetId([
								'IdQueue'			=> $QueueID
								,'IdTransaction'		=> $item['Id']
								,'CurrentItemAmount' => number_format($currentItemAmount, 2, '.', '')
								,'ItemAmount'		=> ($iBalanceAmount != 0.00)? number_format($iBalanceAmount, 2, '.', '') : number_format($itemAmount, 2, '.', '')
								,'BalanceAmount'	=> number_format($BalanceAmount, 2, '.', '')
								,'RemainingAmount'	=> number_format($remaining, 2, '.', '')
								,'ProviderType'		=> 'PATIENT' //$modalProviderType
								,'ORNum'			=> $ORnumber
								,'BillTo'			=> $BillTo
								,'CoverageType'		=> $CoverageType
								,'CoverageAmount'	=> ($gcashAmount !=0.00)? number_format($gcashAmount, 2, '.', '') : 0.00 
								,'PaymentType'		=> $Payitem['Id']
								,'RefNo'			=> $gcashRefNo
								,'PayAmount'		=> ($gcashAmount !=0.00)? number_format($gcashAmount, 2, '.', '') :   (($iAdvancePay !=0.00) ? number_format($iAdvancePay, 2, '.', '') : number_format($itemAmount, 2, '.', '') )
								//,'BankName'		=> $modalCreditBank
								,'InputBy'			=> Auth::user()->username
								,'Status'			=> $BillStatus
								,'doWhileInsert'		=> $doWhileInsert
								,'DiscType'			=> ($discType != 0 && $itemType == 'Item' && $allowdiscount == '1') ? number_format(floatval($currentItemAmount) * (floatval($discType) / 100 ), 2, '.', '') : 0.00
								,'DiscId'			=> $discId
								,'DiscAmount'		=> number_format($discAmount, 2, '.', '')
								,'AgentCode'		=> $AgentId
								,'AgentName'		=>$AgentName
							]);
							
							DB::connection('CMS')->table('Transactions')
							->where('IdQueue', $QueueID)
							->where('Id',  $item['Id'])
							->update([
								'AmountRemaining'	=> number_format($BalanceAmount, 2, '.', '')
								,'Status'			=> $BillStatus
				
							]);
							
							if( $gcashAmount != 0.00)
							{
								$firstHistInsertID = $histLastInsertID;
							}
							
							$gcashAmount = 0.00; 
							array_values($arrayPostPayment);
							array_values($itemSelected);
							//DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->update(['AmountRemaining' => $AmountRemaining  ]);
						}
						else if( $Payitem['Id'] == 'Credit' && $iCreditAmount != 0.00 &&  ($BillStatus == '0' || $BillStatus == '205' || $AdvancePay != 0.00 ) )
						{
							$remaining =  number_format( floatval($GRemaining) - (floatval($creditAmount) + floatval($AdvancePay) ), 2, '.', '');
							if($remaining <= 0.00)
							{
								$AdvancePay = number_format(abs($remaining), 2, '.', '');
								$BillStatus = '210'; // Fully Paid
								$GRemaining = 0.00;
								$BalanceAmount = 0.00;
								$setItem = $item['Id'];
								$iCreditAmount = number_format(abs($remaining), 2, '.', '');
								unset($itemSelected[0]);
							}
							else
							{
								$AdvancePay = 0.00;
								$BalanceAmount = number_format($remaining, 2, '.', '');
								$GRemaining = number_format($remaining, 2, '.', '');
								$BillStatus = '205';  // Partially Paid
								$remaining = 0.00;
								$iCreditAmount = 0.00;
								unset($arrayPostPayment[0]);
							}
							if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount ==0.00 && $iAdvancePay == 0.00 && $creditAmount != 0.00){
								$payAmount = $creditAmount; $doWhileInsert = '1';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount != 0.00){
								$payAmount = $iBalanceAmount; $doWhileInsert = '2';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '3';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay == 0.00){
								$payAmount = $itemAmount; $doWhileInsert = '4';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '5';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount ==0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '6';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount ==0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '7';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay == 0.00 && $creditAmount != 0.00){
								$payAmount = $creditAmount; $doWhileInsert = '8';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay == 0.00 && $creditAmount != 0.00){
								$payAmount = $creditAmount; $doWhileInsert = '9';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay == 0.00 ){
								$payAmount = $creditAmount; $doWhileInsert = '10';
							}else{
								$payAmount = -1;  $doWhileInsert = '11';
							}
							
							
							$histLastInsertID = DB::connection('CMS')->table('PaymentHistory')->insertGetId([
								'IdQueue'			=> $QueueID
								,'IdTransaction'		=> $item['Id']
								,'CurrentItemAmount' => number_format($currentItemAmount, 2, '.', '')
								,'ItemAmount'		=> ($iBalanceAmount != 0.00)? number_format($iBalanceAmount, 2, '.', ''): number_format($itemAmount, 2, '.', '')
								,'BalanceAmount'	=> number_format($BalanceAmount, 2, '.', '')
								,'RemainingAmount'	=> number_format($remaining, 2, '.', '')
								,'ProviderType'		=> 'PATIENT' //$modalProviderType
								,'ORNum'			=> $ORnumber
								,'BillTo'			=> $BillTo
								,'CoverageType'		=> $CoverageType
								,'CoverageAmount'	=> ($creditAmount !=0.00)? number_format($creditAmount, 2, '.', '') : 0.00
								,'PaymentType'		=> $Payitem['Id']
								,'RefNo'			=> $creditRefNo
								,'PayAmount'		=>  ($creditAmount !=0.00)? number_format($creditAmount, 2, '.', '') :   (($iAdvancePay !=0.00) ? number_format($iAdvancePay, 2, '.', '') : number_format($itemAmount, 2, '.', ''))
								,'BankName'		=> $modalCreditBank
								,'InputBy'			=> Auth::user()->username
								,'Status'			=> $BillStatus
								,'doWhileInsert'		=> $doWhileInsert
								,'DiscType'			=> ($discType != 0 && $itemType == 'Item' && $allowdiscount == '1') ? number_format(floatval($currentItemAmount) * (floatval($discType) / 100 ), 2, '.', ''): 0.00
								,'DiscId'			=> $discId
								,'DiscAmount'		=> number_format($discAmount, 2, '.', '')
								,'AgentCode'		=> $AgentId
								,'AgentName'		=>$AgentName
							]);
							
							DB::connection('CMS')->table('Transactions')
							->where('IdQueue', $QueueID)
							->where('Id',  $item['Id'])
							->update([
								'AmountRemaining'	=> number_format($BalanceAmount, 2, '.', '')
								,'Status'			=> $BillStatus
						
							]);
							
							if( $creditAmount != 0.00)
							{
								$firstHistInsertID = $histLastInsertID;
							}
							
							$creditAmount = 0.00; 
							array_values($arrayPostPayment);
							array_values($itemSelected);
							//DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->update(['AmountRemaining' => $AmountRemaining  ]);
						}
						else if( $Payitem['Id'] == 'Cheque' && $iChequeAmount != 0.00 &&  ($BillStatus == '0' || $BillStatus == '205' || $AdvancePay != 0.00 ) )
						{
							$remaining =  number_format( floatval($GRemaining) - (floatval($chequeAmount) + floatval($AdvancePay) ), 2, '.', '');
							if($remaining <= 0.00)
							{
								$AdvancePay = number_format(abs($remaining), 2, '.', '');
								$BillStatus = '210'; // Fully Paid
								$GRemaining = 0.00;
								$BalanceAmount = 0.00;
								$setItem = $item['Id'];
								$iChequeAmount = number_format(abs($remaining), 2, '.', '');
								unset($itemSelected[0]);
							}
							else
							{
								$AdvancePay = 0.00;
								$BalanceAmount = number_format($remaining, 2, '.', '');
								$GRemaining = number_format($remaining, 2, '.', '');
								$BillStatus = '205';  // Partially Paid
								$remaining = 0.00;
								$iChequeAmount = 0.00;
								unset($arrayPostPayment[0]);
							}
							if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount ==0.00 && $iAdvancePay == 0.00 && $chequeAmount !=0.00 ){
								$payAmount = $chequeAmount; $doWhileInsert = '1';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount != 0.00){
								$payAmount = $iBalanceAmount; $doWhileInsert = '2';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '3';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay == 0.00){
								$payAmount = $itemAmount; $doWhileInsert = '4';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '5';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount ==0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '6';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount ==0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '7';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay == 0.00 && $chequeAmount !=0.00 ){
								$payAmount = $chequeAmount;  $doWhileInsert = '8';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay == 0.00 && $chequeAmount !=0.00 ){
								$payAmount = $chequeAmount; $doWhileInsert = '9';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay == 0.00 ){
								$payAmount = $chequeAmount; $doWhileInsert = '10';
							}else{
								$payAmount = -1;  $doWhileInsert = '11';
							}
							
							
							$histLastInsertID = DB::connection('CMS')->table('PaymentHistory')->insertGetId([
								'IdQueue'			=> $QueueID
								,'IdTransaction'		=> $item['Id']
								,'CurrentItemAmount' => number_format($currentItemAmount, 2, '.', '')
								,'ItemAmount'		=> ($iBalanceAmount != 0.00)? number_format($iBalanceAmount, 2, '.', '') : number_format($itemAmount, 2, '.', '')
								,'BalanceAmount'	=> number_format($BalanceAmount, 2, '.', '')
								,'RemainingAmount'	=> number_format($remaining, 2, '.', '')
								,'ProviderType'		=> 'PATIENT' //$modalProviderType
								,'ORNum'			=> $ORnumber
								,'BillTo'			=> $BillTo
								,'CoverageType'		=> $CoverageType
								,'CoverageAmount'	=> ($chequeAmount !=0.00)? number_format($chequeAmount, 2, '.', '') : 0.00
								,'PaymentType'		=> $Payitem['Id']
								,'RefNo'			=> $chequeRefNo
								,'PayAmount'		=> ($chequeAmount !=0.00)? number_format($chequeAmount, 2, '.', '') :   (($iAdvancePay !=0.00) ? number_format($iAdvancePay, 2, '.', '') : number_format($itemAmount, 2, '.', ''))
								,'BankName'		=> $modalChequeBank
								,'InputBy'			=> Auth::user()->username
								,'Status'			=> $BillStatus
								,'doWhileInsert'		=> $doWhileInsert
								,'DiscType'			=> ($discType != 0 && $itemType == 'Item' && $allowdiscount == '1') ? number_format(floatval($currentItemAmount) * (floatval($discType) / 100 ), 2, '.', '') : 0.00
								,'DiscId'			=> $discId
								,'DiscAmount'		=> number_format($discAmount, 2, '.', '')
								,'AgentCode'		=> $AgentId
								,'AgentName'		=>$AgentName
							]);
							
							DB::connection('CMS')->table('Transactions')
							->where('IdQueue', $QueueID)
							->where('Id',  $item['Id'])
							->update([
								'AmountRemaining'	=> number_format($BalanceAmount, 2, '.', '')
								,'Status'			=> $BillStatus
				
							]);
							
							if( $chequeAmount != 0.00)
							{
								$firstHistInsertID = $histLastInsertID;
							}
							
							$chequeAmount = 0.00;
							array_values($arrayPostPayment);
							array_values($itemSelected);
							//DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->update(['AmountRemaining' => $AmountRemaining  ]);
						}
						else if( $Payitem['Id'] == 'Online' && $iOnlineAmount != 0.00 &&  ($BillStatus == '0' || $BillStatus == '205' || $AdvancePay != 0.00 ) )
						{
							$remaining =  number_format( floatval($GRemaining) - (floatval($onlineAmount) + floatval($AdvancePay) ), 2, '.', '');
							if($remaining <= 0.00)
							{
								$AdvancePay = number_format(abs($remaining), 2, '.', '');
								$BillStatus = '210'; // Fully Paid
								$GRemaining = 0.00;
								$BalanceAmount = 0.00;
								$setItem = $item['Id'];
								$iOnlineAmount = number_format(abs($remaining), 2, '.', '');
								unset($itemSelected[0]);
							}
							else
							{
								$AdvancePay = 0.00;
								$BalanceAmount = number_format($remaining, 2, '.', '');
								$GRemaining = number_format($remaining, 2, '.', '');
								$BillStatus = '205';  // Partially Paid
								$remaining = 0.00;
								$iOnlineAmount = 0.00;
								unset($arrayPostPayment[0]);
							}
							if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount ==0.00 && $iAdvancePay == 0.00 && $onlineAmount !=0.00 ){
								$payAmount = $onlineAmount; $doWhileInsert = '1';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount != 0.00){
								$payAmount = $iBalanceAmount; $doWhileInsert = '2';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '3';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay == 0.00){
								$payAmount = $itemAmount; $doWhileInsert = '4';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '5';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay != 0.00 && $iBalanceAmount ==0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '6';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount ==0.00 && $iAdvancePay != 0.00){
								$payAmount = $iAdvancePay; $doWhileInsert = '7';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay == 0.00 && $onlineAmount !=0.00 ){
								$payAmount = $onlineAmount;  $doWhileInsert = '8';
							}else if( $BalanceAmount  != 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount !=0.00 && $iAdvancePay == 0.00 && $onlineAmount !=0.00 ){
								$payAmount = $onlineAmount; $doWhileInsert = '9';
							}else if( $BalanceAmount  == 0.00 &&  $AdvancePay == 0.00 && $iBalanceAmount == 0.00 && $iAdvancePay == 0.00 ){
								$payAmount = $onlineAmount; $doWhileInsert = '10';
							}else{
								$payAmount = -1;  $doWhileInsert = '11';
							}
							
							
							$histLastInsertID = DB::connection('CMS')->table('PaymentHistory')->insertGetId([
								'IdQueue'			=> $QueueID
								,'IdTransaction'		=> $item['Id']
								,'CurrentItemAmount' => number_format($currentItemAmount, 2, '.', '')
								,'ItemAmount'		=> ($iBalanceAmount != 0.00)? number_format($iBalanceAmount, 2, '.', '') : number_format($itemAmount, 2, '.', '')
								,'BalanceAmount'	=> number_format($BalanceAmount, 2, '.', '')
								,'RemainingAmount'	=> number_format($remaining, 2, '.', '')
								,'ProviderType'		=> 'PATIENT' //$modalProviderType
								,'ORNum'			=> $ORnumber
								,'BillTo'			=> $BillTo
								,'CoverageType'		=> $CoverageType
								,'CoverageAmount'	=> ($onlineAmount !=0.00)? number_format($onlineAmount, 2, '.', '') : 0.00
								,'PaymentType'		=> $Payitem['Id']
								,'RefNo'			=> $onlineRefNo
								,'PayAmount'		=> ($onlineAmount !=0.00)? number_format($onlineAmount, 2, '.', '') :   (($iAdvancePay !=0.00) ? number_format($iAdvancePay, 2, '.', '') : number_format($itemAmount, 2, '.', '') )
								,'BankName'		=> $modalOnlineBank
								,'InputBy'			=> Auth::user()->username
								,'Status'			=> $BillStatus
								,'doWhileInsert'		=> $doWhileInsert
								,'DiscType'			=> ($discType != 0 && $itemType == 'Item' && $allowdiscount == '1') ? number_format(floatval($currentItemAmount) * (floatval($discType) / 100 ), 2, '.', '') : 0.00
								,'DiscId'			=> $discId
								,'DiscAmount'		=> number_format($discAmount, 2, '.', '')
								,'AgentCode'		=> $AgentId
								,'AgentName'		=>$AgentName
							]);
							
							DB::connection('CMS')->table('Transactions')
							->where('IdQueue', $QueueID)
							->where('Id',  $item['Id'])
							->update([
								'AmountRemaining'	=> number_format($BalanceAmount, 2, '.', '')
								,'Status'			=> $BillStatus
					
							]);
							
							if( $onlineAmount != 0.00)
							{
								$firstHistInsertID = $histLastInsertID;
							}
							
							$onlineAmount = 0.00;
							array_values($arrayPostPayment);
							array_values($itemSelected);
							//DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->update(['AmountRemaining' => $AmountRemaining  ]);
						}
						$iBalanceAmount = number_format($BalanceAmount, 2, '.', ''); ///set current balance
						$iAdvancePay = number_format($AdvancePay, 2, '.', ''); // set current balance
					}
					
					
						
					//DB::connection('CMS')->commit();
				//} // all type na
			}
			
			// update last inserted id if  remaining amount is not zero
			//for  single item only with amount
			if(isset($histLastInsertID)) // fixed if not defined
			{			
				$lastInsert = DB::connection('CMS')->table('PaymentHistory')->where('Id', $histLastInsertID )->get(array('RemainingAmount','CoverageAmount'))[0];
				if( abs($lastInsert->RemainingAmount) != 0.00  )
				{
					$newCoverageAmount = (floatval($lastInsert->CoverageAmount) - floatval(abs($lastInsert->RemainingAmount)) );
					 DB::connection('CMS')->table('PaymentHistory')->where('Id', $histLastInsertID )->update(['CoverageAmount' => abs($newCoverageAmount) ]);
				}
				//for multiple item with amount
				$updateLastInsert = DB::connection('CMS')->table('PaymentHistory')->where('Id', $histLastInsertID )->get(array('RemainingAmount','CoverageAmount'))[0];
				if(  $itemSelectedCount != 1 && abs($updateLastInsert->RemainingAmount) != 0.00  &&  $updateLastInsert->CoverageAmount !=0.00 &&  ( abs($updateLastInsert->RemainingAmount) == $updateLastInsert->CoverageAmount  )  )
				{
					$firstCoverageAmount = DB::connection('CMS')->table('PaymentHistory')->where('Id', $firstHistInsertID )->get(array('CoverageAmount'))[0];
					
					$updateCoverageAmount = (floatval($firstCoverageAmount->CoverageAmount) - floatval(abs($updateLastInsert->RemainingAmount)) );
					DB::connection('CMS')->table('PaymentHistory')->where('Id', $firstHistInsertID )->update(['CoverageAmount' => abs($updateCoverageAmount) ]);
					DB::connection('CMS')->table('PaymentHistory')->where('Id', $histLastInsertID )->update(['CoverageAmount' => 0.00 ]);
				
				}
			}
			//NWDITE
			//NPEMEOR
		}
	
		// update  queue status
		
		$selects  = DB::connection('CMS')->table('Transactions')->where('IdQueue', $QueueID)->lockForUpdate()->get(array('Status'));
		$forBilling = 0.00;
		$paid = 0.00;
		foreach($selects as $select)
		{
			if( $select->Status == '201' || $select->Status == '205' )
			{
				$forBilling = 1;
			}
			else if( $select->Status == '210')
			{
				$paid = 1;
			}
		}
		
		if( $forBilling == 0.00  && $paid == 1 )
		{
			if(!empty($antechecking[0]->AnteDateCode)){
					
				DB::connection('CMS')->table('Queue')->where('Id', $QueueID)->update(['Status' => 203]);
			}else{

				DB::connection('CMS')->table('Queue')->where('Id', $QueueID)->update(['Status' => 210]);
			}

		}
		
		DB::commit();
		return "for billing - ".$forBilling." === paid ". $paid;
	}

		
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) // get list of  Bill To
    {
		return DB::connection('Eros')->table('Company')
			->where('Status', 'like', 'Active')
			->where('BillingType', 'like', $id)
		->get(array('Id','Name'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
	$discountlist = DiscountType::getList(array('1'))->get(array('Id','Description','IdRequired','Percentage'));
	$trans = Transactions::getTransactionByQueue($id);
	
	$seniorIdData = DB::connection('CMS')->table('CMS.Queue')
	->leftJoin('Eros.Patient','CMS.Queue.IdPatient','=','Eros.Patient.Id')->where('CMS.Queue.Id', $id)->get(array('Eros.Patient.SeniorId'));
	$seniorId =  (count($seniorIdData) !=0) ? $seniorIdData[0]->SeniorId: "";

	$seniorAge = DB::connection('CMS')->table('Queue')->select('AgePatient')->where('Id', $id)->get();
	$AgeSinior =  (count($seniorAge) !=0) ? $seniorAge[0]->AgePatient: "";

	$pwdIdData = DB::connection('CMS')->table('CMS.Queue')
	->leftJoin('Eros.Patient','CMS.Queue.IdPatient','=','Eros.Patient.Id')->where('CMS.Queue.Id', $id)->get(array('Eros.Patient.PWD'));
	$pwdid =  (count($pwdIdData) !=0) ? $pwdIdData[0]->PWD: "";

	$PwdExpiryDate = DB::connection('CMS')->table('CMS.Queue')
	->leftJoin('Eros.Patient','CMS.Queue.IdPatient','=','Eros.Patient.Id')->where('CMS.Queue.Id', $id)->get(array('Eros.Patient.ExpiryDatePWD'));
	$Expired =  (count($PwdExpiryDate) !=0) ? $PwdExpiryDate[0]->ExpiryDatePWD: "";
	$dates = !empty($Expired) ? date('m/d/Y', strtotime($Expired)) : '';
 	//dd($dates);
	$Agent = AgentEmpName::AgentName();
	$cardnumber = DB::connection('CMS')->table('Transactions')->where('IdQueue', $id)->where('GroupItemMaster', '<>' ,'CARD')->where('Status', '>=', '205')->groupBy('IdQueue')->get();
	// dd($cardnumber);
	$paymentResult = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $id)->where('Status', '>=', '201')->get(array('AgentCode', 'AgentName'));
	if ($paymentResult->isNotEmpty()) {
		$Payment = $paymentResult->first();
	} else {
		$Payment = null;
	}
// dd($trans);
	return view('cms/pages.payment', ['SeniorId' => $seniorId, 'PWD' =>  $pwdid, 'PwdExpiredDate' => $dates, 'seniorAge' => $AgeSinior, 'QID' => $id, 'itemData' => json_encode($trans), 'Card' => $cardnumber, 'discountlist' => $discountlist, 'agents' => $Agent, 'Payment' => $Payment ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
	//put here for temp update
	
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
