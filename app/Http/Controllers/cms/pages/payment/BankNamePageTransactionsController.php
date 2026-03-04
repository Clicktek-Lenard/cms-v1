<?php

namespace App\Http\Controllers\cms\pages\payment;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\eros\BankNames;

class BankNamePageTransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	
	return BankNames::getNames();
	
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
     /*
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
		$CoverageType 		= (isset($_POST['CoverageType']) && !empty($_POST['CoverageType']))?$_POST['CoverageType']:'';
		$coPayAmount		= (isset($_POST['coPayAmount']) && !empty($_POST['coPayAmount']))?$_POST['coPayAmount']:0;
		$hmoId			= (isset($_POST['hmoId']) && !empty($_POST['hmoId']))?$_POST['hmoId']:'';
		$cardName		= (isset($_POST['cardName']) && !empty($_POST['cardName']))?$_POST['cardName']:''; 
		$PaymentType		= (isset($_POST['modalSelect']) && !empty($_POST['modalSelect']))?$_POST['modalSelect']:''; 
		$ORnumber		= (isset($_POST['ORnumber']) && !empty($_POST['ORnumber']))?$_POST['ORnumber']:'';
		$iCashAmount = $cashAmount		= (isset($_POST['cashAmount']) && !empty($_POST['cashAmount']))?$_POST['cashAmount']:0;
		$gcashRefNo		= (isset($_POST['gcashRefNo']) && !empty($_POST['gcashRefNo']))?$_POST['gcashRefNo']:''; 
		$iGcashAmount = $gcashAmount		= (isset($_POST['gcashAmount']) && !empty($_POST['gcashAmount']))?$_POST['gcashAmount']:0; 
		$modalCreditBank	= (isset($_POST['modalCreditBank']) && !empty($_POST['modalCreditBank']))?$_POST['modalCreditBank']:'';
		$creditRefNo		= (isset($_POST['creditRefNo']) && !empty($_POST['creditRefNo']))?$_POST['creditRefNo']:''; 
		$iCreditAmount = $creditAmount		= (isset($_POST['creditAmount']) && !empty($_POST['creditAmount']))?$_POST['creditAmount']:0; 
		$modalChequeBank	= (isset($_POST['modalChequeBank']) && !empty($_POST['modalChequeBank']))?$_POST['modalChequeBank']:'';
		$chequeRefNo		= (isset($_POST['chequeRefNo']) && !empty($_POST['chequeRefNo']))?$_POST['chequeRefNo']:''; 
		$iChequeAmount = $chequeAmount		= (isset($_POST['chequeAmount']) && !empty($_POST['chequeAmount']))?$_POST['chequeAmount']:0;
		$totalAmount		= (isset($_POST['totalAmount']) && !empty($_POST['totalAmount']))?$_POST['totalAmount']:0;
		$BalanceAmount = 0;
		$iBalanceAmount = 0;
		$GRemaining = 0;
		$arrayPostPayment = (isset($_POST['PaymentType']) && !empty($_POST['PaymentType']))?$_POST['PaymentType']:array();
		$AdvancePay = 0;		
		if( is_array($itemSelected) )
		{
			//DB::connection('CMS')->beginTransaction();

				foreach($itemSelected as $item) // item selected
				{
					$BillStatus = 0;	
					
					if($GRemaining == 0 )
					{
						$GRemaining = DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->get(array('AmountRemaining'))[0]->AmountRemaining;
						$itemAmount = $GRemaining;
					}
					$setItem = 0;
					
					
					foreach($arrayPostPayment as $Payitem) // payment type
					{
						if( $AdvancePay !== 0 && $item['Id'] === $setItem  )
						{
							continue;
						}
						
					
						$iBalanceAmount = $BalanceAmount; //get last balance
						$iAdvancePay = $AdvancePay; // get last balance
						if( $Payitem['Id'] == 'Cash' && $iCashAmount != 0   && ($BillStatus == '0' || $BillStatus == '205'  || $AdvancePay != 0) )
						{
						
							$remaining =  ( floatval($GRemaining) - (floatval($cashAmount) + floatval($AdvancePay) ));
							if($remaining <= 0)
							{
								$AdvancePay = abs($remaining);
								$BillStatus = '210'; // Fully Paid
								$GRemaining = 0;
								$BalanceAmount = 0;
								$setItem = $item['Id'];
								$iCashAmount = abs($remaining);
								unset($itemSelected[0]);
							}
							else
							{
								$AdvancePay = 0; // error with advance pays
								$BalanceAmount = $remaining;
								$GRemaining = $remaining;
								$BillStatus = '205';  // Partially Paid
								$remaining = 0;
								$iCashAmount = 0;
								unset($arrayPostPayment[0]);
								//$payAmount = $cashAmount;
							}
							
							if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount ==0 && $iAdvancePay == 0 && $cashAmount != 0){
								$payAmount = $cashAmount; $cashAmount = 0; $doWhileInsert = '1';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay != 0 && $iBalanceAmount != 0 ){
								$payAmount = $iBalanceAmount; $doWhileInsert = '2';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay == 0 && $iBalanceAmount == 0 && $iAdvancePay != 0 ){
								$payAmount = $iAdvancePay; $doWhileInsert = '3';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay != 0 && $iBalanceAmount == 0 && $iAdvancePay == 0){
								$payAmount = $itemAmount; $doWhileInsert = '4';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount !=0 && $iAdvancePay != 0){
								$payAmount = $iAdvancePay; $doWhileInsert = '5';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay != 0 && $iBalanceAmount ==0){
								$payAmount = $iAdvancePay; $doWhileInsert = '6';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount ==0 && $iAdvancePay != 0){
								$payAmount = $iAdvancePay; $doWhileInsert = '7';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay == 0 && $iBalanceAmount !=0 && $iAdvancePay == 0 && $cashAmount != 0){
								$payAmount = $cashAmount;  $cashAmount = 0; $doWhileInsert = '8';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount !=0 && $iAdvancePay == 0 && $cashAmount != 0){
								$payAmount = $cashAmount;  $cashAmount = 0; $doWhileInsert = '9';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay == 0 && $iBalanceAmount == 0 && $iAdvancePay == 0 ){
								$payAmount = $cashAmount; $doWhileInsert = '10';
							}else{
								$payAmount = -1;  $doWhileInsert = '11';
							}
							
							
							 $cashAmount = 0;
							DB::connection('CMS')->table('PaymentHistory')->insertGetId([
								'IdQueue'			=> $QueueID
								,'IdTransaction'		=> $item['Id']
								,'ItemAmount'		=> $itemAmount
								,'BalanceAmount'	=> $BalanceAmount
								,'RemainingAmount'	=> $remaining
								,'ProviderType'		=> $modalProviderType
								,'ORNum'			=> $ORnumber
								,'BillTo'			=> $BillTo
								,'CoverageType'		=> $CoverageType
								//,'CoverageAmount'	=> $coPayAmount
								,'PaymentType'		=> $Payitem['Id']
								//,'RefNo'			=> $gcashRefNo
								,'PayAmount'		=> $payAmount
								//,'BankName'		=> $modalCreditBank
								,'InputBy'			=> Auth::user()->username
								,'Status'			=> $BillStatus
								,'doWhileInsert'		=> $doWhileInsert
							]);
							
							DB::connection('CMS')->table('Transactions')
							->where('IdQueue', $QueueID)
							->where('Id',  $item['Id'])
							->update([
								'AmountRemaining'	=> $BalanceAmount
								,'Status'			=> $BillStatus
							]);
							
							
							array_values($arrayPostPayment);
							array_values($itemSelected);
							
							//DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->update(['AmountRemaining' => $AmountRemaining  ]);
						}
						else if( $Payitem['Id'] == 'GCash' && $iGcashAmount != 0 && ($BillStatus == '0' || $BillStatus == '205' || $AdvancePay != 0 ) )
						{ echo $AdvancePay;
							$remaining =  ( floatval($GRemaining) - (floatval($gcashAmount) + floatval($AdvancePay) ));
							if($remaining <= 0)
							{
								$AdvancePay = abs($remaining);
								$BillStatus = '210'; // Fully Paid
								$GRemaining = 0;
								$BalanceAmount = 0;
								$setItem = $item['Id'];
								$iGcashAmount = abs($remaining);
								unset($itemSelected[0]);
							}
							else
							{
								$AdvancePay = 0;
								$BalanceAmount = $remaining;
								$GRemaining = $remaining;
								$BillStatus = '205';  // Partially Paid
								$remaining = 0;
								$iGcashAmount = 0;
								unset($arrayPostPayment[0]);
							}
							if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount ==0 && $iAdvancePay == 0 && $gcashAmount != 0){
								$payAmount = $gcashAmount; $gcashAmount = 0; $doWhileInsert = '1';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay != 0 && $iBalanceAmount != 0){
								$payAmount = $iBalanceAmount; $doWhileInsert = '2';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay == 0 && $iBalanceAmount == 0 && $iAdvancePay != 0){
								$payAmount = $iAdvancePay; $doWhileInsert = '3';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay != 0 && $iBalanceAmount == 0 && $iAdvancePay == 0){
								$payAmount = $itemAmount; $doWhileInsert = '4';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount !=0 && $iAdvancePay != 0){
								$payAmount = $iAdvancePay; $doWhileInsert = '5';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay != 0 && $iBalanceAmount ==0){
								$payAmount = $iAdvancePay; $doWhileInsert = '6';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount ==0 && $iAdvancePay != 0){
								$payAmount = $iAdvancePay; $doWhileInsert = '7';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay == 0 && $iBalanceAmount !=0 && $iAdvancePay == 0 && $gcashAmount != 0){
								$payAmount = $gcashAmount; $gcashAmount = 0; $doWhileInsert = '8';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount !=0 && $iAdvancePay == 0 && $gcashAmount != 0){
								$payAmount = $gcashAmount; $gcashAmount = 0; $doWhileInsert = '9';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay == 0 && $iBalanceAmount == 0 && $iAdvancePay == 0 ){
								$payAmount = $gcashAmount; $doWhileInsert = '10';
							}else{
								$payAmount = -1;  $doWhileInsert = '11';
							}
							
							$gcashAmount = 0;
							DB::connection('CMS')->table('PaymentHistory')->insertGetId([
								'IdQueue'			=> $QueueID
								,'IdTransaction'		=> $item['Id']
								,'ItemAmount'		=> $itemAmount
								,'BalanceAmount'	=> $BalanceAmount
								,'RemainingAmount'	=> $remaining
								,'ProviderType'		=> $modalProviderType
								,'ORNum'			=> $ORnumber
								,'BillTo'			=> $BillTo
								,'CoverageType'		=> $CoverageType
								//,'CoverageAmount'	=> $coPayAmount
								,'PaymentType'		=> $Payitem['Id']
								,'RefNo'			=> $gcashRefNo
								,'PayAmount'		=> $payAmount
								//,'BankName'		=> $modalCreditBank
								,'InputBy'			=> Auth::user()->username
								,'Status'			=> $BillStatus
								,'doWhileInsert'		=> $doWhileInsert
							]);
							
							DB::connection('CMS')->table('Transactions')
							->where('IdQueue', $QueueID)
							->where('Id',  $item['Id'])
							->update([
								'AmountRemaining'	=> $BalanceAmount
								,'Status'			=> $BillStatus
							]);
							
							array_values($arrayPostPayment);
							array_values($itemSelected);
							//DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->update(['AmountRemaining' => $AmountRemaining  ]);
						}
						else if( $Payitem['Id'] == 'Credit' && $iCreditAmount != 0 &&  ($BillStatus == '0' || $BillStatus == '205' || $AdvancePay != 0 ) )
						{
							$remaining =  ( floatval($GRemaining) - (floatval($creditAmount) + floatval($AdvancePay) ));
							if($remaining <= 0)
							{
								$AdvancePay = abs($remaining);
								$BillStatus = '210'; // Fully Paid
								$GRemaining = 0;
								$BalanceAmount = 0;
								$setItem = $item['Id'];
								$iCreditAmount = abs($remaining);
								unset($itemSelected[0]);
							}
							else
							{
								$AdvancePay = 0;
								$BalanceAmount = $remaining;
								$GRemaining = $remaining;
								$BillStatus = '205';  // Partially Paid
								$remaining = 0;
								$iCreditAmount = 0;
								unset($arrayPostPayment[0]);
							}
							if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount ==0 && $iAdvancePay == 0 && $creditAmount != 0){
								$payAmount = $creditAmount; $creditAmount = 0; $doWhileInsert = '1';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay != 0 && $iBalanceAmount != 0){
								$payAmount = $iBalanceAmount; $doWhileInsert = '2';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay == 0 && $iBalanceAmount == 0 && $iAdvancePay != 0){
								$payAmount = $iAdvancePay; $doWhileInsert = '3';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay != 0 && $iBalanceAmount == 0 && $iAdvancePay == 0){
								$payAmount = $itemAmount; $doWhileInsert = '4';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount !=0 && $iAdvancePay != 0){
								$payAmount = $iAdvancePay; $doWhileInsert = '5';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay != 0 && $iBalanceAmount ==0){
								$payAmount = $iAdvancePay; $doWhileInsert = '6';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount ==0 && $iAdvancePay != 0){
								$payAmount = $iAdvancePay; $doWhileInsert = '7';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay == 0 && $iBalanceAmount !=0 && $iAdvancePay == 0 && $creditAmount != 0){
								$payAmount = $creditAmount; $creditAmount = 0; $doWhileInsert = '8';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount !=0 && $iAdvancePay == 0 && $creditAmount != 0){
								$payAmount = $creditAmount; $creditAmount = 0; $doWhileInsert = '9';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay == 0 && $iBalanceAmount == 0 && $iAdvancePay == 0 ){
								$payAmount = $creditAmount; $doWhileInsert = '10';
							}else{
								$payAmount = -1;  $doWhileInsert = '11';
							}
							
							$creditAmount = 0;
							DB::connection('CMS')->table('PaymentHistory')->insertGetId([
								'IdQueue'			=> $QueueID
								,'IdTransaction'		=> $item['Id']
								,'ItemAmount'		=> $itemAmount
								,'BalanceAmount'	=> $BalanceAmount
								,'RemainingAmount'	=> $remaining
								,'ProviderType'		=> $modalProviderType
								,'ORNum'			=> $ORnumber
								,'BillTo'			=> $BillTo
								,'CoverageType'		=> $CoverageType
								//,'CoverageAmount'	=> $coPayAmount
								,'PaymentType'		=> $Payitem['Id']
								,'RefNo'			=> $creditRefNo
								,'PayAmount'		=> $payAmount
								,'BankName'		=> $modalCreditBank
								,'InputBy'			=> Auth::user()->username
								,'Status'			=> $BillStatus
								,'doWhileInsert'		=> $doWhileInsert
							]);
							
							DB::connection('CMS')->table('Transactions')
							->where('IdQueue', $QueueID)
							->where('Id',  $item['Id'])
							->update([
								'AmountRemaining'	=> $BalanceAmount
								,'Status'			=> $BillStatus
							]);
							
							array_values($arrayPostPayment);
							array_values($itemSelected);
							//DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->update(['AmountRemaining' => $AmountRemaining  ]);
						}
						else if( $Payitem['Id'] == 'Cheque' && $iChequeAmount != 0 &&  ($BillStatus == '0' || $BillStatus == '205' || $AdvancePay != 0 ) )
						{
							$remaining =  ( floatval($GRemaining) - (floatval($chequeAmount) + floatval($AdvancePay) ));
							if($remaining <= 0)
							{
								$AdvancePay = abs($remaining);
								$BillStatus = '210'; // Fully Paid
								$GRemaining = 0;
								$BalanceAmount = 0;
								$setItem = $item['Id'];
								$iChequeAmount = abs($remaining);
								unset($itemSelected[0]);
							}
							else
							{
								$AdvancePay = 0;
								$BalanceAmount = $remaining;
								$GRemaining = $remaining;
								$BillStatus = '205';  // Partially Paid
								$remaining = 0;
								$iChequeAmount = 0;
								unset($arrayPostPayment[0]);
							}
							if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount ==0 && $iAdvancePay == 0 && $chequeAmount !=0 ){
								$payAmount = $chequeAmount; $chequeAmount = 0; $doWhileInsert = '1';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay != 0 && $iBalanceAmount != 0){
								$payAmount = $iBalanceAmount; $doWhileInsert = '2';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay == 0 && $iBalanceAmount == 0 && $iAdvancePay != 0){
								$payAmount = $iAdvancePay; $doWhileInsert = '3';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay != 0 && $iBalanceAmount == 0 && $iAdvancePay == 0){
								$payAmount = $itemAmount; $doWhileInsert = '4';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount !=0 && $iAdvancePay != 0){
								$payAmount = $iAdvancePay; $doWhileInsert = '5';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay != 0 && $iBalanceAmount ==0){
								$payAmount = $iAdvancePay; $doWhileInsert = '6';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount ==0 && $iAdvancePay != 0){
								$payAmount = $iAdvancePay; $doWhileInsert = '7';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay == 0 && $iBalanceAmount !=0 && $iAdvancePay == 0 && $chequeAmount !=0 ){
								$payAmount = $chequeAmount; $chequeAmount = 0; $doWhileInsert = '8';
							}else if( $BalanceAmount  != 0 &&  $AdvancePay == 0 && $iBalanceAmount !=0 && $iAdvancePay == 0 && $chequeAmount !=0 ){
								$payAmount = $chequeAmount; $chequeAmount = 0; $doWhileInsert = '9';
							}else if( $BalanceAmount  == 0 &&  $AdvancePay == 0 && $iBalanceAmount == 0 && $iAdvancePay == 0 ){
								$payAmount = $chequeAmount; $doWhileInsert = '10';
							}else{
								$payAmount = -1;  $doWhileInsert = '11';
							}
							
							$chequeAmount = 0;
							DB::connection('CMS')->table('PaymentHistory')->insertGetId([
								'IdQueue'			=> $QueueID
								,'IdTransaction'		=> $item['Id']
								,'ItemAmount'		=> $itemAmount
								,'BalanceAmount'	=> $BalanceAmount
								,'RemainingAmount'	=> $remaining
								,'ProviderType'		=> $modalProviderType
								,'ORNum'			=> $ORnumber
								,'BillTo'			=> $BillTo
								,'CoverageType'		=> $CoverageType
								//,'CoverageAmount'	=> $coPayAmount
								,'PaymentType'		=> $Payitem['Id']
								,'RefNo'			=> $chequeRefNo
								,'PayAmount'		=> $payAmount
								,'BankName'		=> $modalChequeBank
								,'InputBy'			=> Auth::user()->username
								,'Status'			=> $BillStatus
								,'doWhileInsert'		=> $doWhileInsert
							]);
							
							DB::connection('CMS')->table('Transactions')
							->where('IdQueue', $QueueID)
							->where('Id',  $item['Id'])
							->update([
								'AmountRemaining'	=> $BalanceAmount
								,'Status'			=> $BillStatus
							]);
							
							array_values($arrayPostPayment);
							array_values($itemSelected);
							//DB::connection('CMS')->table('Transactions')->where('Id', $item['Id'])->update(['AmountRemaining' => $AmountRemaining  ]);
						}
						$iBalanceAmount = $BalanceAmount; ///set current balance
						$iAdvancePay = $AdvancePay; // set current balance
					}
					
				}
			//DB::connection('CMS')->commit();
		}
		
	}
		
    }
    */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     /*
    public function show($id) // get list of  Bill To
    {
		return DB::connection('Eros')->table('Company')
			->where('Status', 'like', 'Active')
			->where('BillingType', 'like', $id)
		->get(array('Id','Name'));
    }
*/
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
	
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
