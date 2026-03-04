<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\eros\ErosDB;

class QueuePageTransactionTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	$companys = DB::connection('Eros')->table('Company')
					->where('Status', 'LIKE', 'Active') // active
					->get(array('Id','Code','ErosCode','Name','Group as Category'));
	$doctors = DB::connection('Eros')->table('Physician')
					->where('Status', 'NOT LIKE', 'Inactive') // active
					->get(array('Id','FullName','Description as Category'));
	$transactionType = DB::connection('Eros')->table('TransactionType')
					->where('Status','1') // active
					->get(array('*'));
	return view('cms/pages.queueTempCreateAdd', ['doctors' => $doctors, 'companys' => $companys, 'transactionType' => $transactionType ] );
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
	$user = Auth::user();
		// delete
		DB::connection('CMS')->table('TransactionTemp')->where(function($q) use ($request,$user)
		{
			if( $request->input('Id') != 0)
			{
				$q->where('Id', $request->input('Id'));	
			}
			else
			{
				$q->where('Date',date('Y-m-d'));
				$q->where('InputId',$user->id);
				$q->where('IdCompany',$request->input('CompanyId'));
				$q->where('IdPatient',$request->input('PatientId'));
				$q->where('IdDoctor',$request->input('DoctorId'));
				$q->where('Token',$request->input('_token'));
			}
		})
		->delete();
		// insert 
		if( is_array($request->input('itemSelected')) )
		{
			$doctor = DB::connection('Eros')->table('Physician')->where('Id', $request->input('DoctorId') )->get(array('Id','FullName'));
			$company = DB::connection('Eros')->table('Company')->where('Id', $request->input('CompanyId') )->get(array('Id','Code','Name','UsedPriceDefault','UsedPercentDefault','UsedPercentItemGroup','UsedPercentItemCode','UsedLessReadersFee'))[0];
			foreach($request->input('itemSelected') as $item)
			{
				$origAmount = 0;
				$readersFee = 0;
				$itemPrice = DB::connection('Eros')->table('ItemPrice')->where('Id', $item['Id'] )->get(array('Id', 'Code', 'Description', 'PriceGroup', 'Price','PriceType'))[0];
				
				$origAmount = floatval($itemPrice->Price);
				if( $itemPrice->PriceGroup != "Package" && $itemPrice->PriceType == "Default"  && $company->UsedPriceDefault == "Yes" && $company->UsedPercentDefault !=0 && ($company->UsedPercentItemGroup != "" || $company->UsedPercentItemCode != "") && $company->UsedLessReadersFee == "No"  )
				{
					$itemMaster =  DB::connection('Eros')->table('ItemMaster')->where('Code', 'LIKE', $itemPrice->Code )->get(array('Id', 'Code', 'Group','AllowDiscount','ReadersFee','Rebates', 'ReApply'))[0];
					if( (strpos( $company->UsedPercentItemGroup, $itemMaster->Group) !== false || strpos( $company->UsedPercentItemCode, $itemMaster->Code) !== false) && $itemMaster->AllowDiscount == 1  ) 
					{
						if($itemMaster->ReApply == 1)
						{
							$itemPrice_price = floatval($itemPrice->Price) - ( (floatval($company->UsedPercentDefault)/100) * (floatval($itemPrice->Price) - floatval($itemMaster->ReadersFee) - floatval($itemMaster->Rebates)) );
						}
						else
						{
							$itemPrice_price = floatval($itemPrice->Price) - ( (floatval($company->UsedPercentDefault)/100) * floatval($itemPrice->Price) );
						}
					}
					else
					{
						$itemPrice_price = floatval($itemPrice->Price);
					}
					
				}
				elseif( $itemPrice->PriceGroup != "Package" && $itemPrice->PriceType == "Default"  && $company->UsedPriceDefault == "Yes" && $company->UsedPercentDefault !=0 && ($company->UsedPercentItemGroup == "" || $company->UsedPercentItemCode == "") && $company->UsedLessReadersFee == "Yes"  )
				{
					$itemMaster =  DB::connection('Eros')->table('ItemMaster')->where('Code', 'LIKE', $itemPrice->Code )->get(array('Id', 'Code', 'Group','AllowDiscount','ReadersFee'))[0];
					$readersFee = floatval($itemMaster->ReadersFee);
					if( $itemMaster->AllowDiscount == 1)
					{
						$itemPrice_price = ((floatval($itemPrice->Price) - floatval($itemMaster->ReadersFee) ) - ( (floatval($company->UsedPercentDefault)/100) * (floatval($itemPrice->Price)  - floatval($itemMaster->ReadersFee))  )) +floatval($itemMaster->ReadersFee) ;
					}
					else
					{
						$itemPrice_price = floatval($itemPrice->Price);
					}
				}
				elseif( $itemPrice->PriceGroup != "Package" && $itemPrice->PriceType == "Fixed"  && $company->UsedPriceDefault == "Yes" && $company->UsedPercentDefault !=0 && ($company->UsedPercentItemGroup == "" || $company->UsedPercentItemCode == ""))
				{
					$itemPrice_price = floatval($itemPrice->Price);
				}
				else
				{
					$itemPrice_price = $itemPrice->Price;
				}
				
				DB::connection('CMS')->table('TransactionTemp')->insertGetId([
					'IdPatient' 				=> $request->input('PatientId')
					,'Date' 				=> date('Y-m-d')
					,'IdDoctor' 			=> (count($doctor) != 0)?$doctor[0]->Id:null
					,'NameDoctor' 			=> (count($doctor) != 0)?$doctor[0]->FullName:null
					,'IdCompany'			=> $company->Id
					,'NameCompany'		=> $company->Name
					,'TransactionType'		=> $request->input('TransactionTypeId')
					,'IdItemPrice'			=> $item['Id']
					,'ItemUsedItemPrice'		=> $item['ItemUsed']  
					,'CodeItemPrice'			=> $itemPrice->Code
					,'DescriptionItemPrice'	=> $itemPrice->Description
					,'PriceGroupItemPrice'	=> $itemPrice->PriceGroup
					,'AmountItemPrice'		=> number_format($itemPrice_price ,2, '.', '')
					,'AmountRemaining'		=> number_format($itemPrice_price ,2, '.', '')
					,'ReadersFee'			=> $readersFee
					,'OrigAmount'			=> $origAmount
					,'GroupItemMaster' 		=> isset($item['Group']) ? $item['Group'] : null
					,'UsedPercentDefault'		=> $company->UsedPercentDefault
					,'InputBy'				=> $user->username
					,'InputId'				=> $user->id
					,'Status'				=> '50' //for saving 
					,'Token'				=> $request->input('_token')
				]);
			}
			DB::connection('CMS')->table('Queue')->where('Id', '=', $request->input('queueId'))->update(['Status' => 201]);
		}
		return DB::connection('CMS')->table('TransactionTemp')->where('Date',date('Y-m-d'))
				->leftjoin('QueueStatus', 'QueueStatus.Id', '=', 'TransactionTemp.Status')
				->where('InputId',$user->id)
				->where('IdPatient',$request->input('PatientId'))
				->where('Token', $request->input('_token'))
				//->groupBy('IdCompany','IdItemPrice') // double for multi item, allowed only card item
				//->where('IdCompany',$request->input('CompanyId'))
				//->where('IdDoctor',$request->input('DoctorId'))
				->get(array('TransactionTemp.*', 'QueueStatus.Name as QueueStatus'));
		
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
	//get Company Code to search in ItemPrice table
	$companyCode = DB::connection('Eros')->table('Company')->where('Id', $id)->get(array('Code'))[0];;
	return DB::connection('Eros')->table('ItemPrice')
			->where('CompanyCode',$companyCode->Code)
			//->where('Status','Active') // active
			->get(array('Id','Code','Description'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
	DB::connection('CMS')->beginTransaction();
	//delete queue id on temp table before insert 
	DB::connection('CMS')->table('TransactionTemp')->where('IdQueue', $id)->delete();
	//echo "insert back to temp table";
	$qTrans =  DB::connection('CMS')->table('Transactions')
			->leftjoin('Queue', 'Queue.Id', '=', 'Transactions.IdQueue')
			->where('IdQueue', $id)
			->where('Transactions.Status', '<=', 201)
			->get(array('Transactions.*','Queue.IdPatient'));
	
	foreach($qTrans as $trans)
	{
		DB::connection('CMS')->table('TransactionTemp')->insertGetId([
			'IdQueue'				=> $trans->IdQueue
			,'IdPatient'				=> $trans->IdPatient
			,'Date' 				=> $trans->Date
			,'IdDoctor' 			=> $trans->IdDoctor
			,'NameDoctor' 			=> $trans->NameDoctor
			,'IdCompany'			=> $trans->IdCompany
			,'NameCompany'		=> $trans->NameCompany
			,'TransactionType'		=> $trans->TransactionType
			,'IdItemPrice'			=> $trans->IdItemPrice
			,'CodeItemPrice'			=> $trans->CodeItemPrice
			,'DescriptionItemPrice'	=> $trans->DescriptionItemPrice
			,'PriceGroupItemPrice'	=> $trans->PriceGroupItemPrice
			,'AmountItemPrice'		=> $trans->AmountItemPrice
			,'AmountRemaining'		=> $trans->AmountRemaining
			,'ReadersFee'			=> $trans->ReadersFee
			,'OrigAmount'			=> $trans->OrigAmount
			,'HCardNumber'			=> $trans->HCardNumber
			,'GroupItemMaster'		=> $trans->GroupItemMaster
			,'UsedPercentDefault'		=> $trans->UsedPercentDefault
			,'InputBy'				=> $trans->InputBy
			,'InputId'				=> $trans->InputId
			,'Status'				=> $trans->Status
			,'Token'				=> $_GET['_ntoken']
		]);
		DB::connection('CMS')->table('Transactions')->where('Id', $trans->Id)->delete();
	}
	
	DB::connection('CMS')->commit(); 
	
	$companys = DB::connection('Eros')->table('Company')
					->where('Status','Active') // active
					->get(array('Id','Code','ErosCode','Name','Group as Category'));
	$doctors = DB::connection('Eros')->table('Physician')
					->where('Status', 'NOT LIKE', 'Inactive') // all status show
					->get(array('Id','FullName','Description as Category'));
	$transactionType = DB::connection('Eros')->table('TransactionType')
					->where('Status','1') // active
					->get(array('*'));
	return view('cms/pages.queueTempCreateEdit', ['doctors' => $doctors, 'companys' => $companys, 'transactionType' => $transactionType] );
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
