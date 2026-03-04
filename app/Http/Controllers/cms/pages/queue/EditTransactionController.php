<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;
use App\Models\cms\Transactions;
use App\Models\cms\Queue;

class EditTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     
	// $companys = DB::connection('Eros')->table('Company')
	// 				->where('Status','Active') // active
	// 				->get(array('Id','Code','ErosCode','Name','Group as Category'));
	// $doctors = DB::connection('Eros')->table('Physician')
	// 				->where('Status','Active') // active
	// 				->get(array('Id','FullName','Description as Category'));
	// $transactionType = DB::connection('Eros')->table('TransactionType')
	// 				->where('Status','1') // active
	// 				->get(array('*'));
	// return view('cms/pages.editTransaction', ['doctors' => $doctors, 'companys' => $companys, 'transactionType' => $transactionType ] );
    }
    
    public function edit($id)
    {
        // echo "pageId";
		$datas = Transactions::getTransactionById($id);
		//dd($datas[0]->IdCompany);
        $companys = DB::connection('Eros')->table('Company')
            ->where('Status','Active') // active
            ->get(array('Id','Code','ErosCode','Name','Group as Category'));
        $doctors = DB::connection('Eros')->table('Physician')
            ->where('Status','Active') // active
            ->get(array('Id','FullName','Description as Category'));
        $transactionType = DB::connection('Eros')->table('TransactionType')
            ->where('Status','1') // active
            ->get(array('*'));
	
       
        return view('cms/pages.editTransaction', ['datas' => $datas, 'doctors' => $doctors, 'companys' => $companys, 'transactionType' => $transactionType ]);	
    }
   
	public function store(Request $request)
    {
	
		
     }


public function update(Request $request, $id)
{
	$user = Auth::user();
    // Debug statement to check received data
    //  dd($request->all());

    // Update
    if (is_array($request->input('itemSelected')) && count($request->input('itemSelected')) > 0) {
        $doctor = DB::connection('Eros')->table('Physician')->where('Id', $request->input('DoctorId'))->first(['Id', 'FullName']);
        $company = DB::connection('Eros')->table('Company')->where('Id', $request->input('CompanyId'))->first(['Id', 'Code', 'Name', 'UsedPriceDefault', 'UsedPercentDefault']);
        $item = $request->input('itemSelected')[0];

        $itemPrice = DB::connection('Eros')->table('ItemPrice')->where('Id', $item['IdItem'])->first(['Id', 'Code', 'Description', 'PriceGroup', 'Price', 'PriceType']);
		
        if ($itemPrice) {
            if ($itemPrice->PriceGroup != "Package" && $itemPrice->PriceType == "Default" && $company->UsedPriceDefault == "Yes" && $company->UsedPercentDefault != 0) {
                $itemPrice_price = floatval($itemPrice->Price) - ((floatval($company->UsedPercentDefault) / 100) * floatval($itemPrice->Price));
            } elseif ($itemPrice->PriceGroup != "Package" && $itemPrice->PriceType == "Fixed" && $company->UsedPriceDefault == "Yes" && $company->UsedPercentDefault != 0) {
                $itemPrice_price = floatval($itemPrice->Price);
            } else {
                $itemPrice_price = $itemPrice->Price;
            }
	
          $table =   DB::connection('CMS')->table('Transactions')
		  	->where('Id', $id)->update([
                'Date'                 => date('Y-m-d'),
                'IdDoctor'             => $doctor->Id,
                'NameDoctor'           => $doctor->FullName,
                'IdCompany'            => $company->Id,
                'NameCompany'          => $company->Name,
                'TransactionType'      => $request->input('TransactionTypeId'),
                'IdItemPrice'          => $item['IdItem'],
                'ItemUsedItemPrice'    => $item['ItemUsed'],
                'CodeItemPrice'        => $itemPrice->Code,
                'DescriptionItemPrice' => $itemPrice->Description,
                'PriceGroupItemPrice'  => $itemPrice->PriceGroup,
                'AmountItemPrice'      => number_format($itemPrice_price, 2, '.', ''),
                'AmountRemaining'      => number_format($itemPrice_price, 2, '.', ''),
                'GroupItemMaster'      => isset($item['Group']) ? $item['Group'] : null,
                'UsedPercentDefault'   => $company->UsedPercentDefault,
                'InputBy'              => $user->username,
                'InputId'              => $user->id,
                'Status'               => '50',
                'Token'                => $request->input('_token')
            ]);
			
        }
    }
	
    return DB::connection('CMS')->table('Transactions')
		->where('Date', date('Y-m-d'))
        ->leftJoin('QueueStatus', 'QueueStatus.Id', '=', 'Transactions.Status')
        ->where('InputId', $user->id)
        ->where('Token', $request->input('_token'))
        ->get(['Transactions.*', 'QueueStatus.Name as QueueStatus']);
}

    
    
    public function show($id)
    {
		// $companyCode = DB::connection('Eros')->table('Company')->where('Id', $id)->get(array('Code'))[0];;
		// return DB::connection('Eros')->table('ItemPrice')
		// 		->where('CompanyCode',$companyCode->Code)
		// 		//->where('Status','Active') // active
		// 		->get(array('Id','Code','Description'));
    }
    
    public function approvalTransaction(Request $request)
    {
		$queueId = $request->input('queueId');

	DB::connection('CMS')->beginTransaction();	
		$newQueue =  DB::connection('CMS')->table('Queue')->where('Id', $queueId)->get(array('AnteDateTime', 'AnteDate', 'Code', 'AnteDateStatus'))[0];

		DB::connection('CMS')
		->table('UpdateQueue')
		->updateOrInsert(
			[ 'QueueCode' => $newQueue->Code,
			  'Module'	=> 'Transaction'
			],
			['ModuleId' => $queueId,
			 'Status'	=> 1]
		    );
	
		DB::connection('CMS')
		->table('Transactions')
		->where('IdQueue', $queueId)
		->update([
		    
		    'Date' => $newQueue->AnteDate,
		    'Status' => 300
		]);

		DB::connection('CMS')
		->table('Queue')
		->where('Id', $queueId)
		->update([
		    'DateTime' => $newQueue->AnteDateTime,
		    'Date' => $newQueue->AnteDate,
		    'Status' => $newQueue->AnteDateStatus
            // 'AnteDateApprovedDate'		=> date('Y-m-d H:i:s'), FOR UPDATE 01092025
		    // 'AnteDateApprovedBy'		=> Auth::user()->username
		]); 
		
		DB::connection('CMS')
		->table('Queue')
		->where('Code', $newQueue->Code)
		->where('Status', '=', 650)
		->update([
		    'Status' 				=> '900' ,
		    'AnteDateApprovedDate'		=> date('Y-m-d H:i:s'),
		    'AnteDateApprovedBy'		=> Auth::user()->username
		]); 
		
	DB::connection('CMS')->commit(); 	
	
    }
}
