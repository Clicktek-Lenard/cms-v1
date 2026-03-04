<?php

namespace App\Http\Controllers\cms\doctors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;
use Carbon\Carbon;
use DataTables;

class MedicalEvaluationPatientController extends Controller
{
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		
	}
	public function edit($id)
	{
		$lCompa = DB::connection('Eros')->table('Company')->where('ResultUploading', 'LIKE', 'Yes')->where('Id', '=', $_GET['companyid'])->get(array('Id', 'Name'));
		if( count($lCompa) == 0)
		{
			return "Missing Param!";
		}
	
		$Que = DB::connection('CMS')->table('Queue')->where('Id', $id)->first();
		$clinics = ErosDB::getClinicData();
		$itemVitals = DB::connection('CMS')->table('PEAssesAndRec')->where('QueueCode', $Que->Code)->where('ItemCode', 'LIKE', 'VITALS')->get(['Assessment', 'Recommendation', 'PEAssesAndRec.ItemCode',  'PEAssesAndRec.Class']);
		$AssesAndRec = DB::connection('CMS')->table('PEAssesAndRec')
				->where('QueueCode', $Que->Code)
				->get(['Assessment', 'Recommendation', 'CMS.PEAssesAndRec.ItemCode',  'CMS.PEAssesAndRec.Class']);
		//dd($AssesAndRec);       
		$PhysicalExaminationReport = DB::connection('CMS')->table('PhysicalExaminationReport')->where('QueueCode', $Que->Code)->get('Class');
		$VitalSigns = DB::connection('CMS')->table('VitalSign')->where('QueueCode', $Que->Code)->get('IdQueue');
		//dd($VitalSigns);
		$queue = DB::connection('CMS')->table('Queue')
			->leftJoin('CMS.QueueStatus', 'CMS.Queue.Status', '=', 'CMS.QueueStatus.Id')
			->leftJoin('Eros.Patient', 'CMS.Queue.IdPatient', 'Eros.Patient.Id')
			->where('CMS.Queue.Id', $id)
			->get(['CMS.Queue.Code', 'Eros.Patient.FullName', 'CMS.Queue.Date', 'CMS.QueueStatus.Name as QueueStatus', 'CMS.Queue.InputBy', 'CMS.Queue.Id',  'Eros.Patient.Code as PatientCode', 'CMS.Queue.IdPatient', 'Eros.Patient.DOB','Eros.Patient.Gender','CMS.Queue.AgePatient','CMS.Queue.DateTime', 'Queue.IdBU as IdClinic','Queue.IdBU','Queue.Notes'])[0];

		$transactionType = DB::connection('CMS')->table('Transactions')
			    ->leftJoin('Eros.ItemMaster','Transactions.CodeItemPrice','=','Eros.ItemMaster.Code')
			    ->where('IdQueue', $id)
			// ->where('Eros.ItemMaster.Group', 'CLINIC') 
			    ->get(['TransactionType','NameCompany','Transactions.Id', 'Transactions.PriceGroupItemPrice', 'Eros.ItemMaster.StandardPackage']);

		$transactionDatas = DB::connection('CMS')->table('AccessionNo')->where('QueueCode', $Que->Code)
                    ->leftJoin('QueueStatus', 'AccessionNo.Status', '=', 'QueueStatus.Id')
                    ->where('ItemGroup', '!=', 'CLINIC')
                    ->get(['AccessionNo.IdQueue','AccessionNo.QueueCode','ItemDescription', 'IdTransaction', 'ItemCode', 'AccessionNo.Status as AStatus','QueueStatus.Name as Status']);
		
		$needReload = 0;
		foreach($transactionDatas as $withWaived)
		{
			if($withWaived->AStatus == "888")
			{
				$needReload = 1;
				DB::connection('CMS')->table('PEAssesAndRec')->updateOrInsert(
					[
						'QueueCode'	=> $withWaived->QueueCode,
						'ItemCode'		=> $withWaived->ItemCode
					],
					[
						'IdQueue'		=> $withWaived->IdQueue
						,'Findings'		=> '{"Findings1":""}'
						,'Assessment'	=> '{"Assessment1":"WAIVED"}'
						,'Recommendation'=> '{"Recommendation1":null}'
						,'Class'		=> '{"Class1":"WAIVED"}'
						,'InputBy'		=> 'CMS-ADMIN'
					]
				);
			}
		}
		
		if($needReload == 1)
		{
			$transactionData = DB::connection('CMS')->table('AccessionNo')->where('QueueCode', $Que->Code)
			->leftJoin('QueueStatus', 'AccessionNo.Status', '=', 'QueueStatus.Id')
			->where('ItemGroup', '!=', 'CLINIC')
			->get(['AccessionNo.IdQueue','AccessionNo.QueueCode','ItemDescription', 'IdTransaction', 'ItemCode', 'AccessionNo.Status as AStatus','QueueStatus.Name as Status']);
		}
		else
		{
			$transactionData = $transactionDatas;
		}

		$selectedClasses = (!empty($PhysicalExaminationReport) && isset($PhysicalExaminationReport[0]->Class)) 
		? explode(', ', $PhysicalExaminationReport[0]->Class) 
		: [];
		;
		return view('cms.doctor.medicalEvalEdit', [ 
		    'datas' => $queue, 
		    'clinics' => $clinics,
		    'postLink'=>'',
		    'transactionType' => $transactionType, 
		    'transactionData' => $transactionData,
		    'AssesAndRec'   =>  $AssesAndRec,
		    'PhysicalExaminationReport' => $PhysicalExaminationReport,
		    'selectedClasses'   => $selectedClasses,
		    'VitalSigns'    => $VitalSigns,
		    'ItemVitals'	=> $itemVitals,
		    'compaName' => $lCompa[0]->Name
		]);
	}
	
	public function update(Request $request, $id)
	{
		DB::connection('CMS')->beginTransaction();
		$Que = DB::connection('CMS')->table('Queue')->where('Id', $id)->first();
		
		 $AssesAndRec = DB::connection('CMS')->table('PEAssesAndRec')
				->where('QueueCode', $Que->Code)
				->get(['Assessment', 'Recommendation', 'CMS.PEAssesAndRec.ItemCode',  'CMS.PEAssesAndRec.Class']);
		
		$pending = 0;
		$A = 0;
		$B = 0;
		$C = 0;
		$D = 0;
		$MyClass = "";
		foreach($AssesAndRec as $item)
		{
			foreach(json_decode($item->Class ?? '{}', true) as $class)
			{
				if($class == "PENDING")
				{
					$pending = 1;
				}
				else if( $class == "A" )
				{
					$A = 1;
				}
				else if( $class == "B" )
				{
					$B = 1;
				}
				else if( $class == "C" )
				{
					$C = 1;
				}
				else if( $class == "D" )
				{
					$D = 1;
				}
			}
		}
		
		if( $pending == 1)
		{
			$MyClass = "Pending";
		}
		else
		{
			if($D == 1)
			{
				$MyClass = "Class D";
			}
			else
			{
				if($C == 1)
				{
					$MyClass = "Class C";
				}
				else
				{
					if($B == 1)
					{
						$MyClass = "Class B";
					}
					else
					{
						if($A == 1)
						{
							$MyClass = "Class A";
						}
					}
				}
			}
		}
		//dd($MyClass);
		$docIn = DB::connection('Eros')->table('Physician')->where('EmpId', '=', Auth::user()->AccessMapId)->get(array('Id','LastName','FirstName', 'MiddleName', 'Degree', 'PRCNo'))[0];
		$midName = ( $docIn->MiddleName != null && !empty($docIn->MiddleName)) ? substr($docIn->MiddleName, 0, 1) . ". " : "";
		//dd($ddd);
		$docName = $docIn->FirstName . " " . $midName . $docIn->LastName . " " . $docIn->Degree . " / " . $docIn->PRCNo;
		//dd($docName);
		DB::connection('CMS')->table('PhysicalExaminationReport')->where('QueueCode', $Que->Code)->update([
		    'Class' => $MyClass, 'Evaluator' => strtoupper(Str::of($docName)->replaceMatches('/ {2,}/', ' ')) , 'EvaluatorDateTime' => date('Y-m-d H:i:s'), 'Status' => 615
		]);
		
		DB::connection('CMS')->table('Queue')->where('Id', '=', $id )->update(['Status' => 615]);
		DB::connection('CMS')->commit(); 
		return $id;
	}
	
	public function store(Request $request)
	{
		DB::connection('CMS')->beginTransaction();
		$Que = DB::connection('CMS')->table('Queue')->where('Id', $request->input('idQueue'))->first();
		$docIn = DB::connection('Eros')->table('Physician')->where('EmpId', '=', Auth::user()->AccessMapId)->get(array('Id','LastName','FirstName', 'MiddleName', 'Degree', 'PRCNo'))[0];
		$midName = ( $docIn->MiddleName != null && !empty($docIn->MiddleName)) ? substr($docIn->MiddleName, 0, 1) . ". " : "";
		$docName = $docIn->FirstName . " " . $midName . $docIn->LastName . " " . $docIn->Degree . " / " . $docIn->PRCNo;
		DB::connection('CMS')->table('PhysicalExaminationReport')->where('QueueCode', $Que->Code)->update([
			'Evaluated' => strtoupper(Str::of($docName)->replaceMatches('/ {2,}/', ' ')) , 'EvaluatedDateTime' => date('Y-m-d H:i:s'), 'Status' => 620
		]);
		DB::connection('CMS')->table('Queue')->where('Id', '=', $request->input('idQueue') )->update(['Status' => 620]);
		
		DB::connection('CMS')->commit(); 
		return $request->input('idQueue');
	
	}
}