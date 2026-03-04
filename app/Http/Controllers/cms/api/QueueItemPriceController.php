<?php

namespace App\Http\Controllers\cms\api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class QueueItemPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         return 'index';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return 'ricky';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
	if(empty($_GET['IdCompany'])){ return "Empty Company Id";}
    
	$Company  = DB::connection('Eros')->table('Company')->where('Id', $_GET['IdCompany'])->get(array('ErosCode','UsedPriceDefault','UsedPercentDefault','UsedPercentItemGroup','UsedPercentItemCode','UsedLessReadersFee'))[0]; 
	
	/*
	$default = DB::connection('Eros')->table('ItemPrice')->where ('CompanyCode', 'LIKE', "'".session('userPriceDefault')."'")
				->select('Id as IdItem','Code','Description','Price', 'PriceGroup'); // working 
	
	 $itemPrice =  DB::connection('Eros')->table('ItemPrice')  // working 
				->select('Id as IdItem','Code','Description','Price', 'PriceGroup')
				->where('CompanyCode', 'LIKE' , "'".$Company->ErosCode."'") // active
				->union($default);
				//->get(array('Id as IdItem','Code','Description','Price', 'PriceGroup'))
				//->toArray();
				
				
	$groupby = DB::connection('Eros')->query()->fromSub($itemPrice, 'p_pn') // not working
				->groupBy('p_pn.Code')
				->getQuery()
				->toArray();	
	*/			
	// will use native sql heheh
	
	/*$groupby = DB::connection('Eros')->select("
		select `IdItem`, `Code`, `Description`, `Price`, `PriceGroup` from 
		(
			(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup` from `ItemPrice` where `CompanyCode` LIKE '".$Company->ErosCode."')
		union 
			(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup` from `Eros`.`ItemPrice` where `CompanyCode` like '".session('userPriceDefault')."')
		) as `p_pn`
		GROUP BY `p_pn`.`Code` 
	");*/
	
	if($Company->UsedPriceDefault == "Yes" && $Company->UsedPercentDefault != "0" && $Company->UsedPercentItemGroup != "" && $Company->UsedPercentItemCode != ""  && $Company->UsedLessReadersFee == "No"   )
	{
		$groupby = DB::connection('Eros')->select("
			select `p_pn`.`IdItem`, `p_pn`.`Code`, `p_pn`.`Description`, `p_pn`.`Price`, `p_pn`.`PriceGroup`, `p_pn`.`PDefault`, p_pn.CompanyCode, p_pn.ItemUsed, Eros.ItemMaster.Group ,Eros.ItemMaster.SubGroup as IMSubGroup, Eros.ItemMaster.AllowQty as IMAllowQty, Eros.Company.SubGroup from 
			(
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE '".session('userClinicCode')."')
			union
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE 'ALL')
			
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, (tb1.`Price` - (".$Company->UsedPercentDefault."/100 * tb1.`Price`)) as 'Price', tb1.`PriceGroup`, '".session('userClinicCode')." default less ".$Company->UsedPercentDefault."%' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice` tb1 
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where  tb2.Group IN (".$Company->UsedPercentItemGroup.") and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item' and  tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, (tb1.`Price` - (".$Company->UsedPercentDefault."/100 * tb1.`Price`)) as 'Price', tb1.`PriceGroup`, '".session('userClinicCode')." default less ".$Company->UsedPercentDefault."%' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice` tb1 
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where  tb2.Code IN (".$Company->UsedPercentItemCode.") and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item' and  tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, tb1.`Price` , tb1.`PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice`  tb1
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where tb2.Group NOT IN (".$Company->UsedPercentItemGroup.") and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item'  and tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, tb1.`Price` , tb1.`PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice`  tb1
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where tb2.Code NOT IN (".$Company->UsedPercentItemCode.") and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item'  and tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select `Id` as `IdItem`, `Code`, `Description`, `Price` , `PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', `CompanyCode`, `ItemUsed` from `Eros`.`ItemPrice` where `Status` = 1 and `PriceGroup` LIKE 'Package'  and `CompanyCode` like '".session('userPriceDefault')."')
			) as `p_pn`
			LEFT JOIN Eros.ItemMaster ON p_pn.Code = Eros.ItemMaster.Code
			LEFT JOIN Eros.Company ON p_pn.CompanyCode = Eros.Company.Code
			GROUP BY `p_pn`.`Code` 
		");
	
	}
	elseif($Company->UsedPriceDefault == "Yes" && $Company->UsedPercentDefault != "0" && $Company->UsedPercentItemGroup == "" && $Company->UsedPercentItemCode != ""  && $Company->UsedLessReadersFee == "No"   )
	{
		$groupby = DB::connection('Eros')->select("
			select `p_pn`.`IdItem`, `p_pn`.`Code`, `p_pn`.`Description`, `p_pn`.`Price`, `p_pn`.`PriceGroup`, `p_pn`.`PDefault`, p_pn.CompanyCode, p_pn.ItemUsed, Eros.ItemMaster.Group ,Eros.ItemMaster.SubGroup as IMSubGroup, Eros.ItemMaster.AllowQty as IMAllowQty, Eros.Company.SubGroup from 
			(
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE '".session('userClinicCode')."')
			union
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE 'ALL')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, (tb1.`Price` - (".$Company->UsedPercentDefault."/100 * tb1.`Price`)) as 'Price', tb1.`PriceGroup`, '".session('userClinicCode')." default less ".$Company->UsedPercentDefault."%' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice` tb1 
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where  tb2.Code IN (".$Company->UsedPercentItemCode.") and tb2.`ReApply` = 0 and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item' and  tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, (tb1.`Price` - (".$Company->UsedPercentDefault."/100 * (tb1.`Price`- tb2.`ReadersFee` - tb2.`Rebates`))) as 'Price', tb1.`PriceGroup`, '".session('userClinicCode')." default less ".$Company->UsedPercentDefault."%' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice` tb1 
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where  tb2.Code IN (".$Company->UsedPercentItemCode.") and tb2.`ReApply` = 1 and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item' and  tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, tb1.`Price` , tb1.`PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice`  tb1
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where tb2.Code NOT IN (".$Company->UsedPercentItemCode.") and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item'  and tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select `Id` as `IdItem`, `Code`, `Description`, `Price` , `PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', `CompanyCode`, `ItemUsed` from `Eros`.`ItemPrice` where `Status` = 1 and `PriceGroup` LIKE 'Package'  and `CompanyCode` like '".session('userPriceDefault')."')
			) as `p_pn`
			LEFT JOIN Eros.ItemMaster ON p_pn.Code = Eros.ItemMaster.Code
			LEFT JOIN Eros.Company ON p_pn.CompanyCode = Eros.Company.Code
			GROUP BY `p_pn`.`Code` 
		");
	
	}
	elseif($Company->UsedPriceDefault == "Yes" && $Company->UsedPercentDefault != "0"  && $Company->UsedPercentItemGroup == "" && $Company->UsedPercentItemCode == ""  && $Company->UsedLessReadersFee == "Yes")
	{
		$groupby = DB::connection('Eros')->select
		 ("
			select `p_pn`.`IdItem`, `p_pn`.`Code`, `p_pn`.`Description`, `p_pn`.`Price`, `p_pn`.`PriceGroup`, `p_pn`.`PDefault`, p_pn.CompanyCode, p_pn.ItemUsed, Eros.ItemMaster.Group ,Eros.ItemMaster.SubGroup as IMSubGroup, Eros.ItemMaster.AllowQty as IMAllowQty, Eros.Company.SubGroup from 
			(
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE '".session('userClinicCode')."')
			union
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE 'ALL')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, (tb1.`Price` - (".$Company->UsedPercentDefault."/100 * tb1.`Price`)) as 'Price', tb1.`PriceGroup`, '".session('userClinicCode')." default less ".$Company->UsedPercentDefault."%' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice` tb1
				join `Eros`.`ItemMaster` tb2 ON (tb1.Code = tb2.`Code`)
				where tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item' and  tb1.`CompanyCode` like '".session('userPriceDefault')."' and
				tb2.`ReadersFee` = 0)
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, ( ((tb1.`Price` - tb2.`ReadersFee`) - (".$Company->UsedPercentDefault."/100 * (tb1.`Price` - tb2.`ReadersFee`))) + tb2.`ReadersFee` ) as 'Price', tb1.`PriceGroup`, '".session('userClinicCode')." default less ".$Company->UsedPercentDefault."%' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice` tb1
				join `Eros`.`ItemMaster` tb2 ON (tb1.Code = tb2.`Code`)
				where tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item' and  tb1.`CompanyCode` like '".session('userPriceDefault')."' and
				tb2.`ReadersFee` != 0)
			union 
				(select `Id` as `IdItem`, `Code`, `Description`, `Price` , `PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', `CompanyCode`, `ItemUsed` from `Eros`.`ItemPrice` where `Status` = 1 and `PriceGroup` LIKE 'Package'  and `CompanyCode` like '".session('userPriceDefault')."')
			) as `p_pn`
			LEFT JOIN Eros.ItemMaster ON p_pn.Code = Eros.ItemMaster.Code
			LEFT JOIN Eros.Company ON p_pn.CompanyCode = Eros.Company.Code
			GROUP BY `p_pn`.`Code` 
		");
	
	}
	else
	{
		$groupby = DB::connection('Eros')->select("
			select `p_pn`.`IdItem`, `p_pn`.`Code`, `p_pn`.`Description`, `p_pn`.`Price`, `p_pn`.`PriceGroup`, `p_pn`.`PDefault`, p_pn.CompanyCode, p_pn.ItemUsed, Eros.ItemMaster.Group, Eros.ItemMaster.SubGroup as IMSubGroup, Eros.ItemMaster.AllowQty as IMAllowQty, Eros.Company.SubGroup from 
			(
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE '".session('userClinicCode')."')
			union
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed`  from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE 'ALL')
			union 
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', `CompanyCode`, `ItemUsed`  from `Eros`.`ItemPrice` where `Status` = 1 and `CompanyCode` like '".session('userPriceDefault')."')
			) as `p_pn`
			LEFT JOIN Eros.ItemMaster ON p_pn.Code = Eros.ItemMaster.Code
			LEFT JOIN Eros.Company ON p_pn.CompanyCode = Eros.Company.Code
			GROUP BY `p_pn`.`Code` 
		");
	}
	
	
	$selectedItemPrice = DB::connection('CMS')->table('TransactionTemp')
			->where(function($query) use ($id) {
				$query->where('InputId', Auth::user()->id);
				$query->where('Date', date('Y-m-d'));
				$query->where('IdPatient', $_GET['IdPatient']);
				$query->where('IdCompany', $_GET['IdCompany']);
				$query->where('Token', $_GET['_token']);
				if(empty($_GET['IdDoctor']))
				{
					$query->whereNull('IdDoctor');
				}else{
					$query->where('IdDoctor', $_GET['IdDoctor']);
				}
				
			})
			->get(array('*', 'IdItemPrice as IdItem'))->toArray();
	
	
	
	return \Response::json(
			array(
				'listItemPrice'=>$groupby,
				'selectedItemPrice'=>$selectedItemPrice
			));
	
		
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Company  = DB::connection('Eros')->table('Company')->where('Id', $_GET['IdCompany'])->get(array('ErosCode','UsedPriceDefault','UsedPercentDefault','UsedPercentItemGroup','UsedPercentItemCode', 'UsedLessReadersFee'))[0]; 
	/*$groupby = DB::connection('Eros')->select("
		select `IdItem`, `Code`, `Description`, `Price`, `PriceGroup` from 
		(
			(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup` from `ItemPrice` where `CompanyCode` LIKE '".$Company->ErosCode."')
		union 
			(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup` from `Eros`.`ItemPrice` where `CompanyCode` like '".session('userPriceDefault')."')
		) as `p_pn`
		GROUP BY `p_pn`.`Code` 
	");*/
	if($Company->UsedPriceDefault == "Yes" && $Company->UsedPercentDefault != "0" && $Company->UsedPercentItemGroup != "" && $Company->UsedPercentItemCode != ""  && $Company->UsedLessReadersFee == "No"   )
	{
		$groupby = DB::connection('Eros')->select("
			select `p_pn`.`IdItem`, `p_pn`.`Code`, `p_pn`.`Description`, `p_pn`.`Price`, `p_pn`.`PriceGroup`, `p_pn`.`PDefault`, p_pn.CompanyCode, p_pn.ItemUsed, Eros.ItemMaster.Group ,Eros.ItemMaster.SubGroup as IMSubGroup, Eros.ItemMaster.AllowQty as IMAllowQty, Eros.Company.SubGroup from 
			(
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE '".session('userClinicCode')."')
			union
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE 'ALL')
			
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, (tb1.`Price` - (".$Company->UsedPercentDefault."/100 * tb1.`Price`)) as 'Price', tb1.`PriceGroup`, '".session('userClinicCode')." default less ".$Company->UsedPercentDefault."%' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice` tb1 
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where  tb2.Group IN (".$Company->UsedPercentItemGroup.") and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item' and  tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, (tb1.`Price` - (".$Company->UsedPercentDefault."/100 * tb1.`Price`)) as 'Price', tb1.`PriceGroup`, '".session('userClinicCode')." default less ".$Company->UsedPercentDefault."%' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice` tb1 
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where  tb2.Code IN (".$Company->UsedPercentItemCode.") and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item' and  tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, tb1.`Price` , tb1.`PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice`  tb1
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where tb2.Group NOT IN (".$Company->UsedPercentItemGroup.") and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item'  and tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, tb1.`Price` , tb1.`PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice`  tb1
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where tb2.Code NOT IN (".$Company->UsedPercentItemCode.") and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item'  and tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select `Id` as `IdItem`, `Code`, `Description`, `Price` , `PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', `CompanyCode`, `ItemUsed` from `Eros`.`ItemPrice` where `Status` = 1 and `PriceGroup` LIKE 'Package'  and `CompanyCode` like '".session('userPriceDefault')."')
			) as `p_pn`
			LEFT JOIN Eros.ItemMaster ON p_pn.Code = Eros.ItemMaster.Code
			LEFT JOIN Eros.Company ON p_pn.CompanyCode = Eros.Company.Code
			GROUP BY `p_pn`.`Code` 
		");
	
	}
	elseif($Company->UsedPriceDefault == "Yes" && $Company->UsedPercentDefault != "0" && $Company->UsedPercentItemGroup == "" && $Company->UsedPercentItemCode != ""  && $Company->UsedLessReadersFee == "No"   )
	{
		$groupby = DB::connection('Eros')->select("
			select `p_pn`.`IdItem`, `p_pn`.`Code`, `p_pn`.`Description`, `p_pn`.`Price`, `p_pn`.`PriceGroup`, `p_pn`.`PDefault`, p_pn.CompanyCode, p_pn.ItemUsed, Eros.ItemMaster.Group ,Eros.ItemMaster.SubGroup as IMSubGroup, Eros.ItemMaster.AllowQty as IMAllowQty, Eros.Company.SubGroup from 
			(
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE '".session('userClinicCode')."')
			union
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE 'ALL')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, (tb1.`Price` - (".$Company->UsedPercentDefault."/100 * tb1.`Price`)) as 'Price', tb1.`PriceGroup`, '".session('userClinicCode')." default less ".$Company->UsedPercentDefault."%' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice` tb1 
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where  tb2.Code IN (".$Company->UsedPercentItemCode.") and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item' and  tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, tb1.`Price` , tb1.`PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice`  tb1
				left join Eros.`ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
				where tb2.Code NOT IN (".$Company->UsedPercentItemCode.") and tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item'  and tb1.`CompanyCode` like '".session('userPriceDefault')."')
			union 
				(select `Id` as `IdItem`, `Code`, `Description`, `Price` , `PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', `CompanyCode`, `ItemUsed` from `Eros`.`ItemPrice` where `Status` = 1 and `PriceGroup` LIKE 'Package'  and `CompanyCode` like '".session('userPriceDefault')."')
			) as `p_pn`
			LEFT JOIN Eros.ItemMaster ON p_pn.Code = Eros.ItemMaster.Code
			LEFT JOIN Eros.Company ON p_pn.CompanyCode = Eros.Company.Code
			GROUP BY `p_pn`.`Code` 
		");
	
	}
	elseif($Company->UsedPriceDefault == "Yes" && $Company->UsedPercentDefault != "0"  && $Company->UsedPercentItemGroup == "" && $Company->UsedPercentItemCode == ""  && $Company->UsedLessReadersFee == "Yes")
	{
		$groupby = DB::connection('Eros')->select
		 ("
			select `p_pn`.`IdItem`, `p_pn`.`Code`, `p_pn`.`Description`, `p_pn`.`Price`, `p_pn`.`PriceGroup`, `p_pn`.`PDefault`, p_pn.CompanyCode, p_pn.ItemUsed, Eros.ItemMaster.Group ,Eros.ItemMaster.SubGroup as IMSubGroup, Eros.ItemMaster.AllowQty as IMAllowQty, Eros.Company.SubGroup from 
			(
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE '".session('userClinicCode')."')
			union
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE 'ALL')
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, (tb1.`Price` - (".$Company->UsedPercentDefault."/100 * tb1.`Price`)) as 'Price', tb1.`PriceGroup`, '".session('userClinicCode')." default less ".$Company->UsedPercentDefault."%' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice` tb1
				join `Eros`.`ItemMaster` tb2 ON (tb1.Code = tb2.`Code`)
				where tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item' and  tb1.`CompanyCode` like '".session('userPriceDefault')."' and
				tb2.`ReadersFee` = 0)
			union 
				(select tb1.`Id` as `IdItem`, tb1.`Code`, tb1.`Description`, ( ((tb1.`Price` - tb2.`ReadersFee`) - (".$Company->UsedPercentDefault."/100 * (tb1.`Price` - tb2.`ReadersFee`))) + tb2.`ReadersFee` ) as 'Price', tb1.`PriceGroup`, '".session('userClinicCode')." default less ".$Company->UsedPercentDefault."%' as 'PDefault', tb1.`CompanyCode`, tb1.`ItemUsed` 
				from `Eros`.`ItemPrice` tb1
				join `Eros`.`ItemMaster` tb2 ON (tb1.Code = tb2.`Code`)
				where tb1.`Status` = 1 and tb1.`PriceGroup` LIKE 'Item' and  tb1.`CompanyCode` like '".session('userPriceDefault')."' and
				tb2.`ReadersFee` != 0)
			union 
				(select `Id` as `IdItem`, `Code`, `Description`, `Price` , `PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', `CompanyCode`, `ItemUsed` from `Eros`.`ItemPrice` where `Status` = 1 and `PriceGroup` LIKE 'Package'  and `CompanyCode` like '".session('userPriceDefault')."')
			) as `p_pn`
			LEFT JOIN Eros.ItemMaster ON p_pn.Code = Eros.ItemMaster.Code
			LEFT JOIN Eros.Company ON p_pn.CompanyCode = Eros.Company.Code
			GROUP BY `p_pn`.`Code` 
		");
	
	}
	else
	{
		$groupby = DB::connection('Eros')->select("
			select `p_pn`.`IdItem`, `p_pn`.`Code`, `p_pn`.`Description`, `p_pn`.`Price`, `p_pn`.`PriceGroup`, `p_pn`.`PDefault`, p_pn.CompanyCode, p_pn.ItemUsed, Eros.ItemMaster.Group, Eros.ItemMaster.SubGroup as IMSubGroup, Eros.ItemMaster.AllowQty as IMAllowQty, Eros.Company.SubGroup from 
			(
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed` from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE '".session('userClinicCode')."')
			union
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '' as 'PDefault', `CompanyCode`, `ItemUsed`  from `ItemPrice` where `Status` = 1 and `CompanyCode` LIKE '".$Company->ErosCode."' AND `ClinicCode` LIKE 'ALL')
			union 
				(select `Id` as `IdItem`, `Code`, `Description`, `Price`, `PriceGroup`, '".session('userClinicCode')." default' as 'PDefault', `CompanyCode`, `ItemUsed`  from `Eros`.`ItemPrice` where `Status` = 1 and `CompanyCode` like '".session('userPriceDefault')."')
			) as `p_pn`
			LEFT JOIN Eros.ItemMaster ON p_pn.Code = Eros.ItemMaster.Code
			LEFT JOIN Eros.Company ON p_pn.CompanyCode = Eros.Company.Code
			GROUP BY `p_pn`.`Code` 
		");
	}
	
	$selectedItemPrice = DB::connection('CMS')->table('Transactions')
			->leftjoin('Queue', 'Queue.Id', '=', 'Transactions.IdQueue' )
			->where(function($query) use ($id) {
				$query->where('Transactions.IdQueue', $id);
				$query->where('Transactions.Date', date('Y-m-d'));
				$query->where('Queue.IdPatient', $_GET['IdPatient']);
				//$query->where('Token', $_GET['_token']);
				if(empty($_GET['IdDoctor']))
				{
					$query->whereNull('IdDoctor');
				}else{
					$query->where('Transactions.IdDoctor', $_GET['IdDoctor']);
				}
				
			})
			->get(array('Transactions.*', 'IdItemPrice as IdItem'))->toArray();
	
	
	
	return \Response::json(
			array(
				'listItemPrice'=>$groupby,
				'selectedItemPrice'=>$selectedItemPrice
			));
	
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
        //
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
