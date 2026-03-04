<?php

namespace App\Http\Controllers\cms\doctors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\eros\ErosDB;
use Carbon\Carbon;

class PEController extends Controller
{

	public function edit($id)
	{
		// less 3 days
		$lessDate=Date('Y-m-d', strtotime('-3 days'));
		$docIn = DB::connection('Eros')->table('Physician')->where('EmpId', '=', Auth::user()->AccessMapId)->get(array('Id'));

		if(count($docIn) == 0)
		{
			return "Missing Physician Mapping";
			die();
		}

		$Que = DB::connection('CMS')->table('Queue')->where('Id', $id)->first();
		$vital = DB::connection('CMS')->table('VitalSign')->where('QueueCode', 'LIKE', $Que->Code)->first();

		$queue = DB::connection('CMS')->table('Queue')
		->leftJoin('CMS.Transactions', 'CMS.Queue.Id', '=', 'CMS.Transactions.IdQueue')    
		->leftJoin('CMS.AccessionNo', 'CMS.Queue.Id', '=', 'CMS.AccessionNo.IdQueue')
		->leftJoin('CMS.QueueStatus', 'CMS.AccessionNo.Status', '=', 'CMS.QueueStatus.Id')
		->leftJoin('Eros.Physician', 'CMS.Transactions.IdDoctor', '=', 'Eros.Physician.Id')
		->leftJoin('Eros.Patient', 'CMS.Queue.IdPatient', 'Eros.Patient.Id')
		->where('CMS.Queue.date', '<=', date('Y-m-d'))
		->where('CMS.Queue.date', '>=', $lessDate)
		->where('CMS.Queue.Id', $id)
		->whereIn('CMS.AccessionNo.Status', ['280', '4', '500'])
		->where('CMS.AccessionNo.ItemCode', 'LIKE', 'CI002')
		->get(['Transactions.NameCompany','CMS.Queue.Code as QCode', 'Eros.Patient.FullName', 'CMS.QueueStatus.Name as QueueStatus', 'CMS.Queue.Id', 'CMS.AccessionNo.Status as AStatus', 'CMS.AccessionNo.ItemDescription', 'Eros.Patient.Code as PatientCode', 'CMS.Queue.IdPatient', 'Eros.Patient.DOB','Eros.Patient.Gender','CMS.Queue.AgePatient','CMS.Queue.DateTime' ,'Queue.Notes','Eros.Physician.Id as IdDoctor'])[0];

		
		$PEdata = DB::connection('CMS')->table('PhysicalExaminationReport')->where('QueueCode', $Que->Code)->get();
		// dd($PEdata)
		$GP = $PEdata[0]->GP ?? '';
		preg_match('/(\d)(\d)\((\d+-\d+-\d+-\d+)\)/', $GP, $matches);

		// Assign the values
		$g_value = $matches[1] ?? '';
		$p_value = $matches[2] ?? '';
		$p1_value = $matches[3]?? '';
		
		return view('cms.doctor.PEEdit', [ 'datas' => $queue, 'g_value' => $g_value, 'p_value' => $p_value, 'p1_value' => $p1_value,'PEdata' => $PEdata, 'vitals' => $vital ]);
	}
	
	public function update(Request $request, $id)
	{
	
		DB::connection('CMS')->beginTransaction();
		$Que = DB::connection('CMS')->table('Queue')->where('Id', $id)->first();
		$docIn = DB::connection('Eros')->table('Physician')->where('EmpId', '=', Auth::user()->AccessMapId)->get(array('Id', 'FullName'));
		DB::connection('CMS')->table('PhysicalExaminationReport')->updateOrInsert(
			['QueueCode'       =>      $Que->Code],
			[
			'IdQueue'					=>    $id
			,'LiverGallbladderDisease' 		=> 	$request->input('liverglad')
			,'Heartdisease'               		=> 	$request->input('heartDisease')
			,'AsthmaAllergy'             		=>  	$request->input('asthmaAllergy')
			,'Tuberculosis'               		=>   $request->input('Tuberculosis')
			,'EarNoseThroatDisorder'    		=>   $request->input('EarNoseThroat')
			,'EyeDisorder'                		=>   $request->input('EyeDisorder')
			,'DiabetesMellitus'           		=>   $request->input('diabetisM')
			,'ChronicHeadacheMigraine'		=>   $request->input('ChronicHeadache')
			,'Hypertension'               		=>   $request->input('Hypertension')
			,'KidneyDisease'              		=>   $request->input('KidneyDisease')
			,'Cancer'                     		=>   $request->input('Cancer')
			,'SexuallyTransmittedDisease' 	=>   $request->input('SexuallyTransmitted')
			,'PastMedOthers'             	 	=>   $request->input('pastMedOthers')
			,'PresentSmoker'              		=>   $request->input('presentSmoker')
			,'PresentSmokerSticksPerDay'  	=>   $request->input('presentSmokerSD')
			,'PresentSmokerYears'         	=>   $request->input('presentSmokerYears')
			,'PreviousSmoker'             		=>   $request->input('prevSmoker')
			,'PreviousSmokerSticksPerDay' 	=>   $request->input('prevSD')
			,'PreviousSmokerYears'        	=>   $request->input('prevYears')
			,'PresentAlcoholDrinker'     		=>   $request->input('PresentAlcoholDrinker')
			,'PrevAlcoholDrinker'         		=>   $request->input('PresDrinkerN')
			,'PersonalSocialOther'       		=>   $request->input('PersonalOthers')
			,'Menarche'                  		=>   $request->input('Menarche')
			,'MenopausalAge'              		=>   $request->input('MenopausalAge')
			,'FirstDayofLastMenstruation' 	=>   $request->input('fDayofMendtruation')
			,'PastMenstrualPeriod'       		=>   $request->input('PastMenstrualPeriod')
			,'GP'                         			=>   $request->input('gp')
			,'Regular'                    		=>   $request->input('Regular')
			,'OBGYNEOthers'               		=>   $request->input('OBGYNEOthers')
			,'BronchialAsthma'           		=>   $request->input('BronchialAsthma')
			,'FDiabetesMellitus'         		=>   $request->input('DiabetesMellitusF')
			,'Goiter'                     		=>   $request->input('Goiter')
			,'PTB'                        		=>   $request->input('PTB')
			,'FHeartDisease'              		=>   $request->input('HeartDiseaseF')
			,'FHypertension'              		=>   $request->input('HypertensionF')
			,'KedneyDisease'              		=>   $request->input('KidneyDiseaseF')
			,'FamilyOthers'               		=>   $request->input('FamilyOthers') 
			,'Skin'                       		=>   $request->input('Skin')
			,'HeadScalp'                  		=>   $request->input('HeadScalp')
			,'Eyes'                       		=>   $request->input('Eyes')
			,'EarsHearing'                		=>   $request->input('EarsHearing')
			,'NoseSinuses'                		=>   $request->input('NoseSinuses')
			,'MouthThroat'                		=>   $request->input('MouthThroat')
			,'NeckThyroid'                		=>   $request->input('NeckThyroid')
			,'ChestBreastAxilla'          		=>   $request->input('ChestBreastAxilla')
			,'Lungs'                      		=>   $request->input('Lungs')
			,'Heart'                      		=>   $request->input('Heart')
			,'Abdomen'                    		=>   $request->input('Abdomen')
			,'BlackFlanks'                		=>   $request->input('BackFlanks')
			,'Extremities'               		=>   $request->input('Extremities')
			,'Neurological'               		=>   $request->input('Neurological')
			,'GenitalsUrinary'            		=>   $request->input('GenitalsUrinary')
			,'AnusRectum'                 		=>   $request->input('AnusRectum')
			//,'InputBy'				=>	Auth::user()->username
			,'UpdateBy'				=> 	Auth::user()->username
			,'UpdateDate'				=> 	date('Y-m-d H:i:s')
			]
		);
		// VS and VA
		DB::connection('CMS')->table('VitalSign')->updateOrInsert(
			[ 
				'IdQueue'          	=>      $id,
				'QueueCode'          	=>      $Que->Code
			],
			[
				'PulseRate'       	 	=>      $request->input('PulseRate')
				,'RespiratoryRate'  	=>      $request->input('Respiraroty')
				,'BloodPresure'     	=>      $request->input('BloodPresure')
				,'BloodPresureOver' 	=>      $request->input('BloodPresureOver')
				,'BloodPresure2'   	=>      $request->input('BloodPresure2')
				,'BloodPresureOver2'	=>      $request->input('BloodPresureOver2')
				,'BloodPresure3'    	=>      $request->input('BloodPresure3')
				,'BloodPresureOver3'	=>      $request->input('BloodPresureOver3')
				,'Temperature'      	=>      $request->input('Temperature')
				,'Height'           		=>      $request->input('Height')
				,'Weight'           		=>      $request->input('Weight')
				,'BMI'              		=>      $request->input('BMI')
				,'BMICategory'      	=>      $request->input('BMIcategory')
				,'UcorrectedOD'     	=>      $request->input('unCorOD')
				,'UcorrectedOS'     	=>      $request->input('unCorOS')
				,'CorrectedOD'      	=>      $request->input('CorOD')
				,'CorrectedOS'      	=>      $request->input('CorOS')
				,'UncorrectedNearOD'	=>      $request->input('UncorrectedNearOD')
				,'UncorrectedNearOS'	=>      $request->input('UncorrectedNearOS')
				,'CorrectedNearOD'  	=>      $request->input('CorrectedNearOD')
				,'CorrectedNearOS'  	=>      $request->input('CorrectedNearOS')
				//,'Deficient'        		=>      $request->input('Deficient')
				//,'ColorVision'      	=>      $request->input('ColorVision')
				,'WithContactLens'  	=>      $request->input('WithLense')
				,'WithEyeGlass'     	=>      $request->input('WithEyeglass')
				,'UpdateBy'          	=>      Auth::user()->username
			]
		);
		
		
		
		DB::connection('CMS')->table('AccessionNo')->where('QueueCode', $Que->Code)->whereIn('Status', ['280', '4'])->where('ItemGroup', 'LIKE', 'CLINIC')->where('ItemCode', 'LIKE','CI002')->update(['Status' => '500', 'IdDoctor' => $docIn[0]->Id]);
		DB::connection('CMS')->commit(); 
	return $id;      
	}

}