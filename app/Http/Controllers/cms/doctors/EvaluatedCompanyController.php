<?php

namespace App\Http\Controllers\cms\doctors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;
use Carbon\Carbon;
use DataTables;

class EvaluatedCompanyController extends Controller
{
	public function edit($id)
	{
		 
		$lCompa = DB::connection('Eros')->table('Company')->where('ResultUploading', 'LIKE', 'Yes')->where('Id', '=', $id)->get(array('Id', 'Name'));
		if( count($lCompa) != 0)
		{
			return view('cms.doctor.evaluatedCompany',  ['compaName' => $lCompa[0]->Name, 'sid' => $id  ]);
		}
		else
		{
			return "Missing Param!";
		}
	}
	
	public function getPatientList(Request $request)
	{

		$search_arr = $request->get('search');
		$searchValue = $search_arr['value'];

		$sid = $request->get('sid');

		if ($request->ajax()) {
			$model = $this->getCompanyPatient(array('fullname'=>$searchValue, 'year' => '2025', 'sid'=> $sid));
			return DataTables::of($model)->toJson();
		}

	}
	
	public  function getCompanyPatient($params = array())
	{
		$lCompa = DB::connection('Eros')->table('Company')
			->where(function($q) use ($params ) {
				$q->where('ResultUploading', 'LIKE', 'Yes');
				if( !empty($params['sid']) && $params['sid'] != "ALL" )
				{
					$q->where('Id', '=', $params['sid'] );
				}
			})
			->get(array('Id','ErosCode'));
			
		$sCompa = "and IdCompany IN (";
		$x= 0;
		foreach($lCompa as $compa)
		{
			if($x == 0)
			{
				$sCompa .= "'".$compa->Id."'";
			}
			else
			{
				$sCompa .= ",'".$compa->Id."'";
			}
			$x++;
			
		}
		
		
		$sql = "SELECT tb1.`Id` as QID,  tb2.`Fullname` ,  tb2.`Id` as PID, tb2.`Code` as PCode , tb1.`Code` as QCode , tb1.`Date` as QDate , tb2.`DOB` as PDOB, tb3.`NameCompany`,tb2.`FullAddress`, tb2.`Email`, tb2.`Moblie`,
				tb2.`LastName`, tb2.`FirstName`, tb2.`MiddleName`, tb2.`Suffix`, tb2.`Gender`, tb4.`Name` as 'StatusName'
		FROM `CMS`.`Queue` tb1
		INNER JOIN `Eros`.`Patient` tb2 on (tb1.`IdPatient` = tb2.`Id`)
		LEFT JOIN `CMS`.`Transactions` tb3 on (tb1.`Id` = tb3.`IdQueue`)
		LEFT JOIN `CMS`.`QueueStatus` tb4 on (tb1.`Status` = tb4.`Id`)
		WHERE   tb3.`PriceGroupItemPrice` LIKE 'Package' and  tb3.`TransactionType` IN ('APE', 'PEME') and tb1.`Status` >= 210 and tb1.`Status` <= 650  ";
		
		if(!empty($params['fullname']))
		{
			$sql = $sql . "and tb2.`Fullname` like  '%".strtoupper($params['fullname'])."%' ";
		}
		if(!empty($params['byId']))
		{
			$sql = $sql . "and tb1.`Id` =  '".$params['byId']."' ";
		}
		
		if( count($lCompa) != 0)
		{
			$sql .=  $sCompa.")";
		}
		$sql .= " GROUP BY tb1.`Id` ORDER BY tb1.`Date` DESC LIMIT 1000";
		
		$datas =  DB::connection('CMS')->select($sql);
		$return =  array();
		
		foreach($datas as $data)
		{
			//$xrayImage = DB::connection('CMS')->table('Results')->where('IdQueue', '=', $data->QID)->where('SourceCode', 'like', 'XRAY')->where('Action', 'like', 'Upload-XRAY-image.%')->where('SourceAction','NOT LIKE', 'Deleted')->get(array('Id'));
			//$xrayResult = DB::connection('CMS')->table('Results')->where('IdQueue', '=', $data->QID)->where('SourceCode', 'like', 'XRAY')->where('Action', 'like', 'Upload-XRAY.%')->where('SourceAction','NOT LIKE', 'Deleted')->get(array('Id'));
			//$ecgImage = DB::connection('CMS')->table('Results')->where('IdQueue', '=', $data->QID)->where('SourceCode', 'like', 'ECG')->where('Action', 'like', 'Upload-ECG-image.%')->where('SourceAction','NOT LIKE', 'Deleted')->get(array('Id'));
			//$ecgResult = DB::connection('CMS')->table('Results')->where('IdQueue', '=', $data->QID)->where('SourceCode', 'like', 'ECG')->where('Action', 'like', 'Upload-ECG.%')->where('SourceAction','NOT LIKE', 'Deleted')->get(array('Id'));
			//$labResult = DB::connection('CMS')->table('Results')->where('IdQueue', '=', $data->QID)->where('SourceCode', 'like', 'LAB')->where('Action', 'like', 'Upload-LAB.%')->where('SourceAction','NOT LIKE', 'Deleted')->get(array('Id'));
		
			array_push($return, array(
				'id'			=> $data->QID
				,'FullName'	=> $data->Fullname
				,'or_no'		=> ''
				,'patient_id'	=> $data->PID
				,'trans_no'		=> $data->QCode
				,'order_date'	=> $data->QDate
				,'birthdate' 	=> date('M-d-Y', strtotime($data->PDOB))
				,'created_at'	=> $data->QDate
				,'Company'	=> $data->NameCompany
				,'Address'		=> $data->FullAddress
				,'City'		=> ''
				,'Email'		=> $data->Email
				,'Phone'		=> $data->Moblie
				,'Status'		=> $data->StatusName
				//,'xray_image'	=> (isset($xrayImage[0]->Id))?"Yes":''
				//,'xray_result'	=> (isset($xrayResult[0]->Id))?"Yes":''
				//,'ecg_image'	=> (isset($ecgImage[0]->Id))?"Yes":''
				//,'ecg_result'	=> (isset($ecgResult[0]->Id))?"Yes":''
				//,'lab_result'	=> (isset($labResult[0]->Id))?"Yes":''
				));
		
		}
		
		return $return;	
	
	}
	
}