<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;

class CancelTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }
    
    public function edit(Request $request, $id)
    {

        return view('cms/pages.cancelTransaction', ['postLink'=>url(session('userBUCode').'/cms/pastqueue/cancelTransaction/'.$id)]);
    }
   
    public function update(Request $request, $id)
    {
	// check access role
	//$userRole = strpos(session('userRole'), '"ldap_role":"[RECEPTION-OIC]"') !== false;
	
	if( ! $checking = $this->CheckBranchAccess(session('userClinicCode'))  && strpos(session('userRole'), '"ldap_role":"[QUEUE]"') !== false )
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
	DB::connection('CMS')->beginTransaction();
		$cancelReason = $request->input('reason');    
		$newQueueStatus = '650';
		$transactionStatus = '650';
		$dataTime = date('Y-m-d H:i:s');
		$date = date('Y-m-d');

		$getDateFromQueue = DB::connection('CMS')->table('Queue')->where('Id', $id)->get(array('Id','Code','AnteDateCode', 'Date', 'AnteDate', 'AnteDateTime', 'IdPatient', 'AgePatient', 'PatientType', 'DateTime', 'QFullName', 'QLastName','QFirstName', 'QMiddleName', 'QGender', 'QDOB' , 'QFullAddress','Status'))[0];
		
		DB::connection('CMS')->table('Queue')->where('Id', $id)->update(['Status' => $newQueueStatus,'AnteDateReason' => $cancelReason]);
		DB::connection('CMS')->table('Transactions')->where('IdQueue', $id)->update(['Status' => $transactionStatus, 'UpdateDateTime' => date('Y-m-d H:i:s'),  'UpdateBy' => Auth::user()->username]);   
		
		$getDateFromTransaction = DB::connection('CMS')->table('Transactions')->where('IdQueue', $id)->get();

		// $date	= date('Y-m-d');
		// $max = DB::connection('CMS')->select("SELECT SUBSTR(MAX(Code),-4) as iMax from Queue where Code like '".session('userClinicCode').date('Ymd')."%' " );
		// $xMax = ($max[0]->iMax != 0)?$max[0]->iMax+1:1;
		//FW
		
		
	
		$queueId = DB::connection('CMS')->table('Queue')->insertGetId([
			'AnteDateQueueID' => $getDateFromQueue->Id,
			'IdBU'		=> session('userClinicCode'),
			'Code'		=> $getDateFromQueue->Code,
			'AnteDateCode'	=> $getDateFromQueue->Code,
			'Date' 		=> $date,
			'AnteDate'		=> (!empty($getDateFromQueue->AnteDateCode) ) ? $getDateFromQueue->AnteDate : $getDateFromQueue->Date,
			'AnteDateTime'	=> (!empty($getDateFromQueue->AnteDateCode) ) ? $getDateFromQueue->AnteDateTime : $getDateFromQueue->DateTime,
			'AnteDateStatus' => $getDateFromQueue->Status,
			'DateTime' 	=> $dataTime,
			'IdPatient'		=> $getDateFromQueue->IdPatient,
			'QFullName'	=> $getDateFromQueue->QFullName,
			'QLastName'     	=> $getDateFromQueue->QLastName,
			'QFirstName'    	=> $getDateFromQueue->QFirstName,
			'QMiddleName'	=> $getDateFromQueue->QMiddleName,
			'QGender'		=> $getDateFromQueue->QGender,
			'QDOB'		=> $getDateFromQueue->QDOB,
			'QFullAddress'	=> $getDateFromQueue->QFullAddress,
			'AgePatient'	=> $getDateFromQueue->AgePatient,
			'Notes'		=> "Amendments Que",
			'PatientType'	=> $getDateFromQueue->PatientType,
			'Status'		=> 202,
			'InputBy'		=> Auth::user()->username
		]);
		
		foreach ($getDateFromTransaction as $transactiondata) {

		$transactionId = DB::connection('CMS')->table('Transactions')->insert([
			'IdQueue'		=>$queueId,
			'IdDoctor'		=>$transactiondata->IdDoctor,
			'NameDoctor'		=> $transactiondata->NameDoctor,
			'Date'			=>$date,
			'IdCompany'		=> $transactiondata->IdCompany,
			'NameCompany'	=>  $transactiondata->NameCompany,
			'TransactionType'	=> $transactiondata->TransactionType,
			'IdItemPrice'		=> $transactiondata->IdItemPrice,
			'ItemUsedItemPrice'		=> $transactiondata->ItemUsedItemPrice,			
			'CodeItemPrice' 	=> $transactiondata->CodeItemPrice,
			'DescriptionItemPrice'	=> $transactiondata->DescriptionItemPrice,
			'PriceGroupItemPrice'	=> $transactiondata->PriceGroupItemPrice,
			'AmountItemPrice'	=> $transactiondata->AmountItemPrice,
			'AmountRemaining'	=> $transactiondata->AmountItemPrice,
			'HCardNumber'		=> $transactiondata->HCardNumber,
			'GroupItemMaster'	=>$transactiondata->GroupItemMaster,
			'Stat'				=>$transactiondata->Stat,
			'Status'			=> 202,
			'UpdateDateTime'	=> $dataTime,
			'UpdateBy'			=> Auth::user()->username,
			'InputId'			=> $transactiondata->InputId,
			'InputBy'			=> Auth::user()->username
		]);
	}	
		
	DB::connection('CMS')->commit(); 
	return $queueId;
    }
    
    

    public function show()
    {

    }
    
    
}
