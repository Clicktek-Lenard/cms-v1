<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QueuePagePackageInclusionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {

    // }

    
    public function edit(Request $request, $id)
    {
    	//dd($_GET['qid']);
	$isStandard = DB::connection('Eros')->table('ItemMaster')->where('Code', $id)->get(array('StandardPackage'));
	$msgQueued = DB::connection('CMS')->table('msg_queue')->where('IdQueue', $_GET['qid'])->where('Status', 1)->get();

	if( count($isStandard) !=0 && $isStandard[0]->StandardPackage == 1 )
	{ // look for StandardPackage table
		$compositions = DB::connection('Eros')->table('StandardPackage')->where('ItemMasterPackageCode', $id)->get(array('ItemMasterItemCode as Code', 'ItemMasterDescription as Description'));
		$composition = array();
		foreach($compositions as $comdata)
		{
			$accession = DB::connection('CMS')->table('AccessionNo')
			->join('QueueStatus', 'AccessionNo.Status', '=', 'QueueStatus.Id')
			->join('Transactions', 'AccessionNo.IdTransaction', '=', 'Transactions.Id')
			->where('Transactions.IdQueue', '=', $_GET['qid'])
			->where('Transactions.IdItemPrice', '=', $_GET['id'])
			->where('AccessionNo.IdQueue', '=', $_GET['qid'])
			->where('AccessionNo.ItemCode', 'LIKE', $comdata->Code)
			->get(array('QueueStatus.Name as TransStatus', 'AccessionNo.Id', 'AccessionNo.ReceivedBU', 'AccessionNo.ItemGroup', 'AccessionNo.AccessionNo'))[0];
			array_push($composition, array('Code' => $comdata->Code, 'Description' => $comdata->Description, 'Status' => $accession->TransStatus, 'Id' => $accession->Id, 'ReceivedBU' => $accession->ReceivedBU, 'ItemGroup' => $accession->ItemGroup, 'AccessionNo' => $accession->AccessionNo));
			
		}
	}
	else
	{ // look for package table
		$compositions = DB::connection('Eros')->table('Package')
		->join('ItemMaster', 'Package.ItemCode', '=', 'ItemMaster.Code')
		->where('Package.ItemPriceId', $_GET['id'])->get(array('Package.ItemCode as Code', 'ItemMaster.Description as Description','Type'));
		
		$composition = array();
		foreach($compositions as $comdata)
		{
			if($comdata->Type == 'Package')
			{
				$cData = DB::connection('Eros')->table('StandardPackage')->where('ItemMasterPackageCode', $comdata->Code)->get(array('ItemMasterItemCode as Code', 'ItemMasterDescription as Description'));
				
				foreach($cData as $item)
				{
					$accession = DB::connection('CMS')->table('AccessionNo')
					->join('QueueStatus', 'AccessionNo.Status', '=', 'QueueStatus.Id')
					->join('Transactions', 'AccessionNo.IdTransaction', '=', 'Transactions.Id')
					->where('Transactions.IdQueue', '=', $_GET['qid'])
					->where('Transactions.IdItemPrice', '=', $_GET['id'])
					->where('AccessionNo.IdQueue', '=', $_GET['qid'])
					->where('AccessionNo.ItemCode', 'LIKE', $item->Code)
					->get(array('QueueStatus.Name as TransStatus', 'AccessionNo.Id', 'AccessionNo.ReceivedBU', 'AccessionNo.ItemGroup', 'AccessionNo.AccessionNo'))[0];
					array_push($composition, array('Code' => $item->Code, 'Description' => $item->Description, 'Status' => $accession->TransStatus, 'Id' => $accession->Id, 'ReceivedBU' => $accession->ReceivedBU, 'ItemGroup' => $accession->ItemGroup, 'AccessionNo' => $accession->AccessionNo));
				}
			}
			else
			{
				$queue = DB::connection('CMS')->table('Queue')->where('Id', '=', $_GET['qid'])->get(array('Status'))[0];
			
				if($queue->Status == 100)
				{					
					array_push($composition, array('Code' => $comdata->Code, 'Description' => $comdata->Description, 'Status' => 'Uploaded' ));
				}
				else
				{
					$accession = DB::connection('CMS')->table('AccessionNo')
					->join('QueueStatus', 'AccessionNo.Status', '=', 'QueueStatus.Id')
					->join('Transactions', 'AccessionNo.IdTransaction', '=', 'Transactions.Id')
					->where('Transactions.IdQueue', '=', $_GET['qid'])
					->where('Transactions.IdItemPrice', '=', $_GET['id'])
					->where('AccessionNo.IdQueue', '=', $_GET['qid'])
					->where('AccessionNo.ItemCode', 'LIKE', $comdata->Code)
					->get(array('QueueStatus.Name as TransStatus', 'AccessionNo.Id', 'AccessionNo.ReceivedBU', 'AccessionNo.ItemGroup', 'AccessionNo.AccessionNo'))[0];
					array_push($composition, array('Code' => $comdata->Code, 'Description' => $comdata->Description, 'Status' => $accession->TransStatus, 'Id' => $accession->Id, 'ReceivedBU' => $accession->ReceivedBU, 'ItemGroup' => $accession->ItemGroup, 'AccessionNo' => $accession->AccessionNo));
				}
			}
		}
		
	}

        return view('cms/pages.queuePackageInclusion', ['Pack' => json_encode($composition), 'msgQueue' => $msgQueued ]);

    }
    public function show()
    {
        //
    }
   
	public function room(Request $request, $id, $room)
    {
        $Pack = DB::connection('Eros')->table('StandardPackage')
        ->leftJoin('ItemMaster', 'ItemMaster.NewCode', '=', 'ItemMasterItemCode')
        ->where('ItemMasterPackageCode', $id)
        ->where('ItemMasterGroup', $room)
        ->get(array('StandardPackage.ItemMasterItemCode as Code', 'ItemMaster.Description'));

        $Package = null;
        $TransactionsIdItemPrice = DB::connection('CMS')->table('Transactions')->where('CodeItemPrice', $id)->get(array('IdItemPrice'))[0];
        if ($Pack->isEmpty()) {
        $Package = DB::connection('Eros')->table('ItemPrice')
            ->leftJoin('Package', 'ItemPrice.Id', '=', 'ItemPriceId')
            ->leftJoin('ItemMaster', 'Package.ItemCode', '=', 'ItemMaster.Code')
            ->where('ItemPrice.Code', $id)
            ->where('ItemPrice.Id', $TransactionsIdItemPrice->IdItemPrice)
            ->where('ItemPrice.PriceGroup', 'Package')
            ->where('ItemMaster.DepartmentGroup', $room)
            ->groupby('ItemMaster.Code')
            ->get(array('ItemMaster.Code as Code', 'ItemMaster.Description'));
        }
        return view('cms/pages.queuePackageInclusion', ['Pack' => $Pack->isNotEmpty() ? $Pack : $Package ]);

    }

	public function writeHL7MessageRequest(Request $request)
	{
		$id = $request->id;
		$type = $request->type;

		if ($type == "Package") {
			$row = DB::connection('CMS')
				->table('AccessionNo')
				->where('Id', $id)
				->first();

			DB::connection('CMS')
				->table('msg_queue')
				->updateOrInsert(
					[
						'IdQueue'    => $row->IdQueue,
						'ItemGroup'  => $row->ItemGroup,
						'AccessionNo' => $row->AccessionNo,
						'ReceivedBU'  => $row->ReceivedBU,
					],
					[
						'QueueCode'   => $row->QueueCode,
						'IdBU'        => $row->IdBU,
						'ReceivedBU'  => $row->ReceivedBU,
						'Status'      => 1,
					]
				);

			return response()->json([
				'status' => 'success',
				'message' => 'HL7 Request Queued Successfully!',
				'ItemGroup' => $row->ItemGroup,
				'AccessionNo' => $row->AccessionNo,
				'ReceivedBU' => $row->ReceivedBU,
			]);
		}
		else if ($type == "Item") {
			$row = DB::connection('CMS')
				->table('AccessionNo')
				->where('IdTransaction', $id)
				->first();
			
			DB::connection('CMS')
				->table('msg_queue')
				->updateOrInsert(
					[
						'IdQueue'    => $row->IdQueue,
						'ItemGroup'  => $row->ItemGroup,
						'AccessionNo' => $row->AccessionNo,
						'ReceivedBU'  => $row->ReceivedBU,
					],
					[
						'QueueCode'   => $row->QueueCode,
						'IdBU'        => $row->IdBU,
						'ReceivedBU'  => $row->ReceivedBU,
						'Status'      => 1,
					]
				);

			return response()->json([
				'status' => 'success',
				'message' => 'HL7 Request Queued Successfully!',
				'ItemGroup' => $row->ItemGroup,
				'AccessionNo' => $row->AccessionNo,
				'ReceivedBU' => $row->ReceivedBU,
			]);
		}
	}
}
