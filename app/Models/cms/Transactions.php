<?php

namespace App\Models\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\eros\ErosDB;

class Transactions extends Model
{
	public static function getTransactionByQueue($queueId = null)
	{
		if( $queueId == null) return 'Missing Id';
		return DB::connection('CMS')->table('Transactions')
				->where('Transactions.IdQueue',$queueId)
				->leftJoin('QueueStatus','Transactions.Status','=','QueueStatus.Id')
				->leftJoin('Eros.ItemMaster','Transactions.CodeItemPrice','=','Eros.ItemMaster.Code')
				//->leftJoin('Items','Transactions.IdItem','=','Items.Id')
				//->leftJoin('Doctors','Transactions.IdDoctor','=','Doctors.Id')
				->leftJoin('Eros.Company','Transactions.IdCompany','=','Eros.Company.Id')
				//->leftJoin('PriceCode','Transactions.IdPriceCode','=','PriceCode.Id')
				->get(array('Transactions.*', 'QueueStatus.Name as QueueStatus','Eros.Company.SubGroup as CompaSubGroup', 'Eros.ItemMaster.AllowDiscount'));	
	}
	
	public static function getTransactionById($Id = null)
	{
		if( $Id == null) return 'Missing Id';
		return DB::connection('CMS')->table('Transactions')
				->leftJoin('QueueStatus','Transactions.Status','=','QueueStatus.Id')
				->where('Transactions.Id',$Id)
				->get(array('Transactions.*',  'QueueStatus.Name as QueueStatus'));	
	}

	public static function postInsert($request)
	{
		
	
	}

	public static function postUpdate($request,$id)
	{
	
	}
	
	public static function postAsDeleted($request,$id)
	{
		$QueId = DB::connection('CMS')->table('Transactions')->where('Id', $id)->get(array('IdQueue', 'Status'))[0];
		if(!empty($QueId))
		{
			$dataTime = date('Y-m-d H:i:s');
			if( $QueId->Status != '201') // Fully Paid Up only
			{
				DB::connection('CMS')->table('PaymentHistory')->where('IdTransaction', $id)
				->update(
				[ 
					'Status'		=> '2'	
					,'UpdateBy'	=> Auth::user()->username
					,'UpdateDate'	=> $dataTime
					,'DeletedReason'=> $request->input('reason')
				]);
			}
			DB::connection('CMS')->select("INSERT INTO TransactionsDeleted SELECT * FROM Transactions where Id = '".$id."' "); // added 07-27-23 1:56pm
			DB::connection('CMS')->table('TransactionsDeleted')->where('Id', $id)
			->update(
			[ 
				'UpdateDateTime'	=> $dataTime	
				,'UpdateBy'		=> Auth::user()->username
				,'Token'			=> $request->input('reason')
				
			]);

			if( $QueId->Status != '201') // Fully Paid Up only
			{
				DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $QueId->IdQueue )->where('Status', '!=', '2')->delete(); // all Payment History except with reason 
				// update transaction status back to for payment and rollback item price 
				DB::connection('CMS')->select("UPDATE  `Transactions` SET `Status` = '201', `InputBy` = '".Auth::user()->username."', `AmountRemaining` = `AmountItemPrice`   WHERE `IdQueue` = '".$QueId->IdQueue."' ");
				// update Queue Status and Eros Status back to for payment
				DB::connection('CMS')->table('Queue')->where('Id', $QueId->IdQueue)
				->update(
				[ 
					'Status'		=> '201'	
					,'ErosStatus'	=> 'reUpdate'
					,'UpdateBy'	=> Auth::user()->username
					,'UpdateDate'	=> date('Y-m-d')
				]);
			}
			
			DB::connection('CMS')->table('AccessionNo')->where('IdQueue', '=', $QueId->IdQueue)->where('IdTransaction', '=', $id)->update(['Status' => 850]);
			
			return DB::connection('CMS')->table('Transactions')->where('Id', $id)->delete();
		}
		else
		{
			return '0';
		}
		
	}
	
	public static function getTransactionForRooms($queueId = null, $station = [])
	{
		if ($queueId == null) return 'Missing Id';
	
		// Build the query
		$query = DB::connection('CMS')->table('Transactions')
			->where('Transactions.IdQueue', $queueId)
			->leftJoin('QueueStatus', 'Transactions.Status', '=', 'QueueStatus.Id')
			->leftJoin('Eros.ItemMaster', 'Transactions.CodeItemPrice', '=', 'Eros.ItemMaster.Code')
			->leftJoin('Eros.Company', 'Transactions.IdCompany', '=', 'Eros.Company.Id');
	
			if ($station !== null) {
				$query->where(function ($q) use ($station) {
					if ($station === 'DRUG TEST') {
						$q->whereIn('Eros.ItemMaster.SubGroup', (array) $station);
					} else {
						$q->whereIn('Transactions.GroupItemMaster', (array) $station);
					}
					$q->orWhereIn('Transactions.PriceGroupItemPrice', ['Package']);
				});
			}
	
		// Execute the query and get results
		$results = $query->get([
			'Transactions.*',
			'QueueStatus.Name as QueueStatus',
			'Eros.Company.SubGroup as CompaSubGroup',
			'Eros.ItemMaster.AllowDiscount',
			'Eros.ItemMaster.SubGroup as ItemSubGroup'
		]);

		// Collect CodeItemPrice if GroupItemMaster is 'PACK'
		$codeItemPrices = [];
		foreach ($results as $result) {
			if (in_array($result->PriceGroupItemPrice, ['Package'])) {
				$codeItemPrices[] = $result->CodeItemPrice;
			}
		}
	
		$packageDeptGroups = ErosDB::getPackageDeptGroupMultiple($codeItemPrices);
	
		$filteredResults = $results->filter(function ($result) use ($station, $packageDeptGroups) {
			$stations = (array) $station;
	
			if ($station === 'DRUG TEST' && $result->ItemSubGroup === 'DRUG TEST') {
				return true;
			}
	
			if ($station === 'LABORATORY' && $result->GroupItemMaster === 'LABORATORY' && $result->ItemSubGroup !== 'DRUG TEST') {
				return true;
			}
	
			if ($station !== 'DRUG TEST' && $station !== 'LABORATORY' && in_array($result->GroupItemMaster, $stations)) {
				return true;
			}
	
			if (in_array($result->PriceGroupItemPrice, ['Package']) && isset($packageDeptGroups[$result->CodeItemPrice])) {
				$subItems = $packageDeptGroups[$result->CodeItemPrice]->toArray();
	
				foreach ($subItems as $subItem) {
					if (in_array($subItem->DepartmentGroup, $stations)) {
						return true;
					}
				}
			}
	
			return false;
		});
	
		// Return the filtered results
		return $filteredResults->values();
	}

	public static function getImagingTransactionForRooms($queueId = null, $station = null)
	{
		if ($queueId == null) return 'Missing Id';
	
		// Build the query
		$query = DB::connection('CMS')->table('Transactions')
			->where('Transactions.IdQueue', $queueId)
			->leftJoin('QueueStatus', 'Transactions.Status', '=', 'QueueStatus.Id')
			->leftJoin('Eros.ItemMaster', 'Transactions.CodeItemPrice', '=', 'Eros.ItemMaster.Code')
			->leftJoin('Eros.Company', 'Transactions.IdCompany', '=', 'Eros.Company.Id');
			$query->where(function($q) use ($station) {
				$q->whereIn('Eros.ItemMaster.SubGroup', $station)
				->orWhereIn('Transactions.PriceGroupItemPrice', ['Package']);
			});
	
		// Execute the query and get results
		$results = $query->get([
			'Transactions.*',
			'QueueStatus.Name as QueueStatus',
			'Eros.Company.SubGroup as CompaSubGroup',
			'Eros.ItemMaster.AllowDiscount',
			'Eros.ItemMaster.SubGroup as SubGroup'
		]);
	
		// Collect CodeItemPrice if GroupItemMaster is 'PACK'
		$codeItemPrices = [];
		foreach ($results as $result) {
			if (in_array($result->PriceGroupItemPrice, ['Package'])) {
				$codeItemPrices[] = $result->CodeItemPrice;
			}
		}

		// Get sub-items (DepartmentGroup and Description) for items with GroupItemMaster = 'PACK'
		$packageDeptGroups = ErosDB::getPackageDeptGroupMultiple($codeItemPrices);
	
		// Filter to get only items that match the $station in GroupItemMaster
		$filteredResults = $results->filter(function($result) use ($station, $packageDeptGroups) {

			// Include items where SubGroup directly matches the station array
			if (in_array($result->SubGroup, $station)) {
				return true;
			}
		
			// Include 'PACK'/'PACKAGE' items with sub-items that match the station
			if (in_array($result->PriceGroupItemPrice, ['Package']) && isset($packageDeptGroups[$result->CodeItemPrice])) {
				$subItems = $packageDeptGroups[$result->CodeItemPrice]->toArray(); 
				// dd($subItems);
				// Check if any sub-item's DepartmentGroup matches any of the stations
				foreach ($subItems as $subItem) {
					if (in_array($subItem->DepartmentGroup, $station)) {
						// dd($subItem);
						return true;
					}
				}
			}
		
			return false;
		});
	
	// dd($filteredResults);
		// Return the filtered results
		return $filteredResults->values(); // Use values() to reset the array keys
	}
	
	public static function getIdQueue($id)
	{
		return DB::connection('CMS')
			->table('Transactions')
			->where('Id', $id)
			->value('IdQueue');
	}
	
	public static function getTransactionItemsByIdQueue($Id)
	{
		return DB::connection('CMS')
			->table('Transactions')
			->where('Transactions.IdQueue', $Id)
			->pluck('CodeItemPrice')
			->toArray();
	}

	
	
}
