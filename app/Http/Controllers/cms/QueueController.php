<?php

namespace App\Http\Controllers\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\cms\Kiosk;
use App\Models\cms\Transactions;
use App\Models\cms\QueNumber;
use App\Models\eros\ErosDB;

class QueueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {	//
          // return session('userClinicDefault');
		$queue = Queue::todaysQueue()->get(array('CMS.Queue.Id','CMS.Queue.Code','CMS.Queue.QFullName','CMS.QueueStatus.Name as QueueStatus','CMS.Queue.InputBy','CMS.Queue.Notes'));
		return view('cms.queue', ['queue' => $queue]);
		//return view('cms.queue')->withErrors([ 'error' => 'my error']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
	{
		// Retrieve the patient ID from the URL query parameter
		if ($request->has('kioskPID')) {
			$IdPatient = $request->input('kioskPID');
			// Retrieve patient information based on the patient ID
			$patient = Kiosk::find($IdPatient);
			$QRCode = $patient->QRCode;
			// Retrieve the queue data by querying the database
			// dd($patient);
			$datas = Kiosk::todaysQueueID($IdPatient)
			->leftJoin('Queuing.Patient', 'Kiosk.IdPatient', '=', 'Patient.Id')
			->leftJoin('Eros.Patient as ErosPatient', 'Queuing.Patient.Code', '=', 'ErosPatient.Code')
			->get([
				'Patient.Id',
				'Kiosk.IdPatient',
				'Patient.Code as QueuingPatientCode',
				'Patient.FullName',
				'Patient.DOB',
				'Patient.Gender',
				'Kiosk.IdBU',
				'Kiosk.SysDateTime',
				'ErosPatient.Code as ErosPatientCode',
				'ErosPatient.Id as ErosPatientId' // Adjust this line to fetch fields from Eros.Patient as needed
			])[0];
		
			// dd($datas);
			$clinics = ErosDB::getClinicData();
			// dd($datas);
			return view('cms.receptionQueueCreate', ['clinics' => $clinics,'defaultClinic' => session('userClinicCode'), 'queueno' => $QRCode , 'idpatient' => $IdPatient,'datas' => $datas]);
		}
		else{
			$clinics = ErosDB::getClinicData();
			return view('cms.queueCreate', ['clinics' => $clinics, 'defaultClinic' => session('userClinicCode') ]);   
		}
    }

	
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    { 

	DB::connection('CMS')->beginTransaction();
	$dataTime = date('Y-m-d H:i:s');
	$myDBId = Controller::getMyDBID();
	$dataTime = date('Y-m-d H:i:s');
	//$max = DB::connection('CMS')->select("SELECT SUBSTR(MAX(Code),-4) as iMax from Queue where Code like '".session('userClinicCode').$myDBId.date('ymd')."%' " );
	//$xMax = ($max[0]->iMax != 0)?$max[0]->iMax+1:1;

	$newCode =  $this->genQueCode();
    	if( isset($newCode->Num) )
	{
		$xMax = $newCode->Num;
	}
	else
	{
		return  $newCode;
		die();
	}

	$IdQueue =  $request->input('IdPatient');

	// dd($IdQueue);
	$QPatientInfo = DB::connection('Eros')->table('Patient')
		->where('Eros.Patient.Id', $IdQueue)
		->get(array('Eros.Patient.FirstName as QFirstName', 'Eros.Patient.LastName as QLastName', 'Eros.Patient.MiddleName as QMiddleName', 'Eros.Patient.Gender as QGender', 'Eros.Patient.DOB as QDOB', 'Eros.Patient.FullAddress as QFullAddress', 'Eros.Patient.Email as QEmail', 'Eros.Patient.Moblie as QMoblie'))[0];
	// dd($QPatientInfo);
	$cardNumbers = str_replace('-', '', $request->input('CardNumber'));
	$itemSelected = $request->input('itemSelected');
	$queueId = DB::connection('CMS')->table('Queue')->insertGetId([
		'IdBU'		=> session('userClinicCode'),
		'Code'		=> session('userClinicCode').$myDBId.date('ymd').sprintf('%04d', $xMax),
		'Date' 		=> date('Y-m-d'),
		'DateTime' 	=> $dataTime,
		'IdPatient'		=> $request->input('IdPatient'),
		'QFullName'		=> $request->input('QFullName'),
		'QLastName'     => $QPatientInfo->QLastName,
		'QFirstName'    => $QPatientInfo->QFirstName,
		'QMiddleName'	=> $QPatientInfo->QMiddleName,
		'QGender'		=> $QPatientInfo->QGender,
		'QDOB'			=> $QPatientInfo->QDOB,
		'QFullAddress'	=> $QPatientInfo->QFullAddress,
		'AgePatient'	=>  $request->input('Age'),
		'Notes'		=> $request->input('Notes'),
		'PatientType'	=> $request->input('PatientType'),
		//'AccessionNo'	=> $request->input('accession'),
		'Status'		=> 201,
		'InputBy'		=> Auth::user()->username
	]);

	/*PICKUP & EMAIL*/

	if( (!empty($request->input('forPU'))  || !empty($request->input('forEmail'))  ) )
	{
		DB::connection('Notification')->table('Info')->insertGetId([
			'Module'        => 'Queue',
			'ModuleId'      => $queueId,
			'PickUp'        => $request->input('forPU'), 
			'SMS'           => !empty($QPatientInfo->QMoblie) ? 1 : 0,
			'SMSNumber'     => $QPatientInfo->QMoblie,
			'SMSStatus'     => !empty($QPatientInfo->QMoblie) ? 1 : 0,
			'Email'         => $request->input('forEmail'),
			'EmailAdd'      => $QPatientInfo->QEmail,
			'EmailMessage'  => 'N/A',
			'EmailStatus'   => !empty($QPatientInfo->QEmail) ? 1 : 0
		]);
	}

	/*PICKUP & EMAIL*/
	
	if( (!empty($request->input('Medication'))  || !empty($request->input('LastDose'))  || !empty($request->input('LastPeriod'))  ) )
	{
		DB::connection('CMS')->table('Vitals')->insertGetId([
			'IdQueue'		=> $queueId,
			'Medication'	=> $request->input('Medication'),
			'LastDose'		=> $request->input('LastDose'),
			'LastPeriod'	=> (!empty($request->input('LastPeriod')) ) ? date('Y-m-d',strtotime($request->input('LastPeriod'))) : NULL,
			'InputBy'		=> Auth::user()->username,
			'InputDateTime'	=> date('Y-m-d h:i:s')
		]);
	}
	$deptGroupsString = '';
	// insert Temp to actual
	if( is_array($request->input('itemSelected')) )
	{
		$idItemCodes = [];
		foreach($_POST['itemSelected'] as $itemId)
		{
			$index = array_search($itemId, $itemSelected);
			$cardNumber = isset($cardNumbers[$index]) ? $cardNumbers[$index] : null;
			$tempData = DB::connection('CMS')->table('TransactionTemp')->where('Id', $itemId)->get(array('*'))[0];
			
			$idItemCodes[] = $tempData->CodeItemPrice;

			DB::connection('CMS')->table('Transactions')->insertGetId([
				'IdQueue' 				=> $queueId,
				'Date' 				=> $tempData->Date,
				'IdDoctor'				=> $tempData->IdDoctor,
				'NameDoctor'			=> $tempData->NameDoctor,
				'IdCompany'			=> $tempData->IdCompany,
				'NameCompany'			=> $tempData->NameCompany,
				'TransactionType'		=> $tempData->TransactionType,
				'IdItemPrice'			=> $tempData->IdItemPrice,
				'ItemUsedItemPrice'		=> $tempData->ItemUsedItemPrice,
				'CodeItemPrice'			=> $tempData->CodeItemPrice,
				'DescriptionItemPrice'		=> $tempData->DescriptionItemPrice,
				'PriceGroupItemPrice'		=> $tempData->PriceGroupItemPrice,
				'AmountItemPrice'		=> $tempData->AmountItemPrice,
				'AmountRemaining'		=> $tempData->AmountRemaining,
				'ReadersFee'			=> $tempData->ReadersFee,
				'OrigAmount'			=> $tempData->OrigAmount,
				'HCardNumber'			=> $cardNumber,
				'GroupItemMaster' 		=> isset($tempData->GroupItemMaster) ? $tempData->GroupItemMaster : '',
				'InputBy'				=> $tempData->InputBy,
				'InputId'				=>$tempData->InputId,
				'Status'				=> 201
			]);
			DB::connection('CMS')->table('TransactionTemp')->where('Id', $itemId)->delete();
		}

		$deptGroups = ErosDB::getItemMasterDeptGroup($idItemCodes);
		// dd($deptGroups);
		$packageItemCodes = DB::connection('Eros')
			->table('ItemMaster')
			->whereIn('Code', $idItemCodes)
			->where('Departmentgroup', 'Package')
			->pluck('Code')
			->merge(
				DB::connection('Eros')
					->table('ItemPrice')
					->whereIn('Code', $idItemCodes)
					->where('PriceGroup', 'Package')
					->pluck('Code')
			)
		->unique();

		if ($deptGroups->contains(fn($value) => strtolower($value) === 'package')) {
			$packageDeptGroups = ErosDB::getPackageDeptGroupMultiple($packageItemCodes);
			$deptGroups = $deptGroups->merge($packageDeptGroups);
		}
		
		// dd($deptGroups);
		$deptGroups = $deptGroups->reject(function ($value) {
			return $value === 'Package';
		});
		
		$deptGroupsArray = $deptGroups->map(function ($item) {
			if (is_string($item)) {
				return $item;
			}
			if (is_iterable($item)) {
				return collect($item)->map(function ($subItem) {
					return $subItem->DepartmentGroup ?? '';
				})->toArray();
			}
			if (is_object($item) && property_exists($item, 'DepartmentGroup')) {
				return $item->DepartmentGroup ?? '';
			}
			return '';
		})->flatten()->filter()->toArray();
		
		// Remove duplicates and convert to a comma-separated string
		$deptGroupsArray = array_unique($deptGroupsArray);

		if (($key = array_search('CONSULTATION', $deptGroupsArray)) !== false) {
			$deptGroupsArray[$key] = 'VITAL';
		}
		
		$deptGroupsString = implode(', ', $deptGroupsArray);
		// dd($deptGroupsString);
		
		
		// Kiosk::where('IdPatient', $request->input('QueueIdPatient'))->update([
		// 	'Station' => $deptGroupsString,
		// 	'numOfCall' => '0'
		// ]);
		
		
		// dd('ANDREI', $deptGroups);
	}
	
	if ($request->input('QueueIdPatient') === null)
	{
		$kiosk = new Kiosk;
	
		$kiosk->IdBU = session('userClinicCode');
		$kiosk->Date = date('Y-m-d');
		$kiosk->SysDateTime = now();
		$kiosk->QRCode = str_pad(QueNumber::getUpdatedNumber(session('userClinicCode')), 4, '0', STR_PAD_LEFT);
		$kiosk->IdPatient = $request->input('IdPatient');
		$kiosk->IdQueueCMS = $queueId;
		$kiosk->Status = 'startQueue';
		$kiosk->Priority = $request->input('Priority');
		$kiosk->CurrentRoom = 'Lobby';
		$kiosk->numOfCall = '0';
		$kiosk->Station = $deptGroupsString; // Assign the $deptGroupsString value here
	
		$kiosk->save();
	}
	
	DB::connection('CMS')->commit(); 
	// Updating the Station column in the Kiosk to exit the Queue
	Kiosk::where('IdPatient', $request->input('QueueIdPatient'))->update([
		'Station' => $deptGroupsString ?: '',
		'numOfCall' => '0',
		'Status' => 'next_room',
		'CurrentRoom' => 'Lobby',
		'IdQueueCMS' => $queueId
	]);
	
	$queueCode = DB::connection('CMS')->table('Queue')->where('Id', $queueId)->value('Code');

	DB::connection('Queuing')
		->table('Logs')
		->where('IdPatient', $request->input('QueueIdPatient'))
		->update(['CMSQueueCode' => $queueCode]);

	return ($queueId);
	
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    { 
        $queue = Queue::todaysQueueID($id)->get();
        return view('cms.queue', ['queue' => $queue]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
	$clinics = ErosDB::getClinicData();
	$queue = Queue::todaysQueueID($id)->get(array('Queue.Id','Queue.Code','Queue.AnteDateQueueID','Queue.AnteDate','Eros.Patient.FullName','Queue.Status as QStatusId','QueueStatus.Name as QueueStatus','Queue.InputBy','Queue.Notes','Queue.IdPatient','Patient.DOB',
		'Patient.Gender','Queue.IdBU as IdClinic','Queue.Date','Queue.DateTime','Patient.Code as PatientCode','Queue.IdBU', 'Queue.AgePatient', 'Queue.PatientType','Queue.Status', 'CMS.Queue.AccessionNo'))[0];
	$trans = Transactions::getTransactionByQueue($id);
	
	// FOR WRITING ON MSG QUEUE RESEND HL7 ITEMS 10/29/2025
	$itemIds = $trans->where('PriceGroupItemPrice', 'Item')->pluck('Id')->toArray();

	$accessions = DB::connection('CMS')->table('AccessionNo')->whereIn('IdTransaction', $itemIds)->get();

	$accessionMap = $accessions->groupBy('IdTransaction');

	$trans = $trans->map(function($t) use ($accessionMap) {
		if ($t->PriceGroupItemPrice === 'Item' && $accessionMap->has($t->Id)) {
			$firstAccession = $accessionMap[$t->Id]->first();
			$t->AccessionId = $firstAccession->Id;
			$t->ItemGroup = $firstAccession->ItemGroup;
			$t->AccessionNo = $firstAccession->AccessionNo;
			$t->ReceivedBU = $firstAccession->ReceivedBU;
		}
		return $t;
	});
	
	$msgQueued = DB::connection('CMS')->table('msg_queue')->where('IdQueue', $id)->where('Status', 1)->get();

	$payHistory = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $id)->where('Status', 2)->get(array('IdQueue'));	
	
	$transDeleted = DB::connection('CMS')->table('TransactionsDeleted')->where('IdQueue', $id)->get(array('IdQueue'));
        	
	$queueStatus = DB::connection('CMS')->table('Queue')->where('Id', $id)->where('Status', 203)->value('Status');

	$forSpecimenStatus = DB::connection('CMS')->table('Queue')->where('Id', $id)->where('Status', 300)->value('Status');

	$transactionStatus = DB::connection('CMS')->table('Transactions')->where('IdQueue', $id)->where('Status', 50)->value('Status');
			
	$disableButton = true;
	
	$saveButton = true;

	$approveButtonDisabled = true; 

	$paymentButtonDisabled = false; 

	$hl7Btn = true; 


	if (count($payHistory) != 0)
	{
		$disableButton = false; //for view btn
	}
	if(count($transDeleted) !=0 )
	{
		$disableButton = false; //for view btn
	}
	if ($queue->Status == 203) {

		$approveButtonDisabled = false; // for the approve button
		$saveButton =  false;		
	}
	else if( $queueStatus == 204)
	{
		$saveButton =  false;
	}
	if ($transactionStatus == 50)
	{
		$paymentButtonDisabled = true;
	}
	if ($forSpecimenStatus == 300)
	{
		$hl7Btn = false;
	}

	//vitals 
	$vitalsData = DB::connection('CMS')->table('Vitals')->where('IdQueue', $id)->get(array('*'));
	//notif
	//$notifView = DB::connection('Notification')->table('Info')->where('ModuleId', $id)->get(array('PickUp', 'Email'));

	//dd($notifView);

	if( count($vitalsData) !=0 )
	{
		$medication = $vitalsData[0]->Medication;
		$lastDose = $vitalsData[0]->LastDose;
		$lastPeriod = $vitalsData[0]->LastPeriod;
	}
	else
	{
		$medication = "";
		$lastDose = "";
		$lastPeriod = "";
	}

	// if( count($notifView) !=0 )
	// {
	// 	$forPU = $notifView[0]->PickUp;
	// 	$forEmail = $notifView[0]->Email;
	// }
	// else
	// {
	// 	$forPU = "";
	// 	$forEmail = "";
	// }
	$forPU = "";
		$forEmail = "";
	
	return view('cms.queueEdit', ['clinics' => $clinics, 'defaultClinic' => session('userClinicCode'), 'datas'=>$queue, 'postLink'=>'',  'trans' =>json_encode($trans), 'saveButton' => $saveButton, 'disableButton' => $disableButton, 'approveButtonDisabled' => $approveButtonDisabled, 'paymentButtonDisabled' => $paymentButtonDisabled , 'medication' => $medication, 'lastDose' => $lastDose, 'lastPeriod' => $lastPeriod, 'hl7Btn' => $hl7Btn, 'forPU' => $forPU, 'forEmail' => $forEmail, 'msgQueue' => $msgQueued ]);     
	
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {  
	DB::connection('CMS')->beginTransaction();

	$IdQueue =  $request->input('IdPatient');
	$antedate = DB::connection('CMS')->table('Queue')->where('Id', $id)->get(array('AnteDate', 'Status'));
	
	$Questatus = (!empty($antedate[0]->AnteDate) )? 202 : 201;

	$QPatientInfo = DB::connection('CMS')->table('Queue')
		->leftJoin('Eros.Patient', 'CMS.Queue.IdPatient', '=', 'Eros.Patient.Id')
		->where('CMS.Queue.Id', $id)
		->get(array('Eros.Patient.FullName as QFullName', 'Eros.Patient.FirstName as QFirstName', 'Eros.Patient.LastName as QLastName', 'Eros.Patient.MiddleName as QMiddleName', 'Eros.Patient.Gender as QGender', 'Eros.Patient.DOB as QDOB', 'Eros.Patient.FullAddress as QFullAddress'))[0];
	
	$queueId = DB::connection('CMS')->table('Queue')
		->where('Id', $id)
		->update([
			'IdPatient'		=> $request->input('IdPatient'),
			'QFullName'		=> $QPatientInfo->QFullName,
			'QLastName'     => $QPatientInfo->QLastName,
			'QFirstName'    => $QPatientInfo->QFirstName,
			'QMiddleName'	=> $QPatientInfo->QMiddleName,
			'QGender'		=> $QPatientInfo->QGender,
			'QDOB'			=> $QPatientInfo->QDOB,
			'QFullAddress'	=> $QPatientInfo->QFullAddress
			,'AgePatient'	=> $request->input('Age')
			,'Notes'		=> $request->input('Notes')
			,'PatientType'	=> $request->input('PatientType')
			,'UpdateDate'	=> date('Y-m-d')
			,'Status'		=> $Questatus
			,'UpdateBy'	=> Auth::user()->username
		]);

	/*PICKUP & EMAIL*/
	
		$notif = DB::connection('Notification')->table('Info')
		->updateOrInsert(
			['ModuleId' => $id ],
			[	
				'Module'		=> 'Queue',
				'PickUp'		=> $request->input('forPU'),
				'Email'			=> $request->input('forEmail')
			]
			);

	/*PICKUP & EMAIL*/
		
	if( (!empty($request->input('Medication'))  || !empty($request->input('LastDose'))  || !empty($request->input('LastPeriod'))  ) )
	{
		DB::connection('CMS')->table('Vitals')
			->updateOrInsert(
			['IdQueue' => $id ],
			[
				'Medication'	=> $request->input('Medication'),
				'LastDose'		=> $request->input('LastDose'),
				'LastPeriod'	=>  (!empty($request->input('LastPeriod')) ) ? date('Y-m-d',strtotime($request->input('LastPeriod'))) : NULL,
				'UpdateBy'		=> Auth::user()->username,
				'UpdateDateTime'	=> date('Y-m-d h:i:s')
			]
			);
	}
	
		$itemSelected = $request->input('itemSelected');
	
		//dd($itemSelected);
		// insert Temp to actual
		if( is_array($request->input('itemSelected')) )
		{
			$insertAll  = DB::connection('CMS')->table('Transactions')->where('IdQueue', $id)->where('Status','<=', 201)->get(array('*'));
			// dd($insertAll);
			foreach($insertAll as $data)
			{
				DB::connection('CMS')->table('TransactionTemp')->insertGetId([
					'Id'					=> $data->Id
					,'IdQueue' 			=> $id
					,'Date' 				=> $data->Date
					,'IdDoctor'				=> $data->IdDoctor
					,'NameDoctor'			=> $data->NameDoctor
					,'IdCompany'			=> $data->IdCompany
					,'NameCompany'			=> $data->NameCompany
					,'TransactionType'		=> $data->TransactionType
					,'IdItemPrice'			=> $data->IdItemPrice
					,'ItemUsedItemPrice'		=> $data->ItemUsedItemPrice
					,'CodeItemPrice'			=> $data->CodeItemPrice
					,'DescriptionItemPrice'	=> $data->DescriptionItemPrice
					,'PriceGroupItemPrice'	=> $data->PriceGroupItemPrice
					,'AmountItemPrice'		=> $data->AmountItemPrice
					,'AmountRemaining'		=> $data->AmountRemaining
					,'ReadersFee'			=> $data->ReadersFee
					,'OrigAmount'			=> $data->OrigAmount
					,'HCardNumber' 			=> $data->HCardNumber
					,'GroupItemMaster'		=> $data->GroupItemMaster
					,'InputBy'				=> $data->InputBy
					,'InputId'				=> $data->InputId
					,'Status'				=> $data->Status
					,'Stat'					=> $data->Stat
				]);
			}
		
			// delete transaction where status < = 201
			DB::connection('CMS')->table('Transactions')->where('IdQueue',$id)->where('Status','<=', 201)->delete();
			// insert from temp transaction
			$statStatus = 0;
			$statAllItemAmount =0;
			// dd(isset($itemSelected['isId']));
			$arrayStat = array();
			$idItemCodes = [];
			foreach($request->input('itemSelected') as $itemId)
			{
				//dd($request->input('itemSelected'));
				$tempData = DB::connection('CMS')->table('TransactionTemp')->where('Id', $itemId['isId'])->get(array('*'));
				$index = array_search($itemId['isId'], $itemSelected);
				$cardNumbers = str_replace('-', '', $itemId['CardNumber']);
				$cardNumber = $cardNumbers;
				
				foreach($tempData as $tempData)
				{	
				
					$idItemCodes[] = $tempData->CodeItemPrice;

					if( $itemId['isStat'] == "on" )
					{
					
						$isStat = "Yes";
						$statStatus = 1;
						$statAllItemAmount += $tempData->AmountItemPrice;
						array_push($arrayStat, array('isComDoc' =>$tempData->IdCompany.$tempData->IdDoctor, 'isDoctorName' =>$tempData->NameDoctor ,'isCompanyName' => $tempData->NameCompany  ,'isCompany' => $tempData->IdCompany, 'isDoctor' => $tempData->IdDoctor ,  'isAmount' =>$tempData->AmountItemPrice ));
					}
					else
					{
						$isStat = "";
					}
					
					
					$pop = DB::connection('CMS')->table('Transactions')->insertGetId([
						'IdQueue' 				=> $id
						,'Date' 				=> $tempData->Date
						,'IdDoctor'				=> $tempData->IdDoctor
						,'NameDoctor'			=> $tempData->NameDoctor
						,'IdCompany'			=> $tempData->IdCompany
						,'NameCompany'		=> $tempData->NameCompany
						,'TransactionType'		=> $tempData->TransactionType
						,'IdItemPrice'			=> $tempData->IdItemPrice
						,'ItemUsedItemPrice'		=> $tempData->ItemUsedItemPrice
						,'CodeItemPrice'			=> $tempData->CodeItemPrice
						,'DescriptionItemPrice'	=> $tempData->DescriptionItemPrice
						,'PriceGroupItemPrice'	=> $tempData->PriceGroupItemPrice
						,'AmountItemPrice'		=> $tempData->AmountItemPrice
						,'AmountRemaining'		=> $tempData->AmountRemaining
						,'ReadersFee'			=> $tempData->ReadersFee
						,'OrigAmount'			=> $tempData->OrigAmount
						,'HCardNumber' 		=> $tempData->HCardNumber ? $tempData->HCardNumber : $cardNumber
						,'GroupItemMaster'		=> $tempData->GroupItemMaster
						,'InputBy'				=> $tempData->InputBy
						,'InputId'				=> $tempData->InputId
						,'Status'				=> '201' // set as for billing status
						,'Stat'				=> $isStat
					]);
					
					DB::connection('CMS')->table('TransactionTemp')->where('Id', $itemId['isId'])->delete();
				}
			}

	
			$QtempData = DB::connection('CMS')->table('TransactionTemp')->where('IdQueue', $id)->get(array('*'));

		
			
			foreach($QtempData as $tempData)
			{
					DB::connection('CMS')->table('Transactions')->insertGetId([
						'IdQueue' 				=> $tempData->IdQueue
						,'Date' 				=> $tempData->Date
						,'IdDoctor'				=> $tempData->IdDoctor
						,'NameDoctor'			=> $tempData->NameDoctor
						,'IdCompany'			=> $tempData->IdCompany
						,'NameCompany'		=> $tempData->NameCompany
						,'TransactionType'		=> $tempData->TransactionType
						,'IdItemPrice'			=> $tempData->IdItemPrice
						,'ItemUsedItemPrice'		=> $tempData->ItemUsedItemPrice
						,'CodeItemPrice'			=> $tempData->CodeItemPrice
						,'DescriptionItemPrice'	=> $tempData->DescriptionItemPrice
						,'PriceGroupItemPrice'	=> $tempData->PriceGroupItemPrice
						,'AmountItemPrice'		=> $tempData->AmountItemPrice
						,'AmountRemaining'		=> $tempData->AmountRemaining
						,'HCardNumber' 		=> $tempData->HCardNumber
						,'GroupItemMaster'		=> $tempData->GroupItemMaster
						,'InputBy'				=> $tempData->InputBy
						,'InputId'				=> $tempData->InputId
						,'Status'				=> '201' // set as for billing stauts
						,'Stat'				=> $tempData->Stat
					]);
					DB::connection('CMS')->table('TransactionTemp')->where('Id', $tempData->Id)->delete();
			}
			
			if( $statStatus == 1) // found 
			{

				$isGroup = DB::connection('CMS')->table('Transactions')->where('IdQueue', $id)->groupBy('IdDoctor', 'IdCompany')->get(array('IdQueue', 'IdDoctor', 'IdCompany'));

				
				
				foreach($isGroup as $statGroup)
				{


					$checkSF  = DB::connection('CMS')->table('Transactions')->where('IdQueue', $id)->where('IdDoctor', $statGroup->IdDoctor)->where('IdCompany', $statGroup->IdCompany)->where('CodeItemPrice', 'LIKE', 'SF')->get(array('Id'));
					$totalSF = 0;
					$isAmount = 0;
					$isCompanyName = "";
					$isDoctorName = "";
					$isCompanyId = 0;
					$isDoctorId = 0;
			
					foreach($arrayStat as $aStat)
					{ 
						if( $aStat['isDoctor'] == $statGroup->IdDoctor && $aStat['isCompany'] == $statGroup->IdCompany   )
						{
							$isAmount += $aStat['isAmount'];
							$isCompanyName = $aStat['isCompanyName'];
							$isDoctorName = $aStat['isDoctorName'];
							$isCompanyId = $aStat['isCompany'];
							$isDoctorId = $aStat['isDoctor'];
						}


					}
					$totalSF = (20 / 100) * $isAmount;
					
					if( count($checkSF) != 0 ) //update
					{	
						DB::connection('CMS')->table('Transactions')->where('Id', $checkSF[0]->Id)->update(['AmountItemPrice' =>$totalSF, 'Status' => '201']);
					}elseif($isCompanyId !=0 && $isDoctorId !=0)
					{
						$locaCompa = DB::connection('Eros')->table('Company')->where('Id', session('userClinicDefault'))->get(array('Code','Name'));
						$itemPriceId =  DB::connection('Eros')->table('ItemPrice')->where('CompanyCode', $locaCompa[0]->Code)->where('Code','LIKE','SF')->get(array('Id','Code','Description'));

						//dd($itemPriceId);
						DB::connection('CMS')->table('Transactions')->insertGetId([
							'IdQueue' 				=> $id
							,'Date' 				=> date('Y-m-d')
							,'IdDoctor'				=> $isDoctorId
							,'NameDoctor'			=> $isDoctorName
							,'IdCompany'			=> $isCompanyId
							,'NameCompany'		=> $isCompanyName
							,'TransactionType'		=> 'Walk-In'
							,'IdItemPrice'			=> $itemPriceId[0]->Id
							,'CodeItemPrice'			=> $itemPriceId[0]->Code
							,'DescriptionItemPrice'	=> $itemPriceId[0]->Description
							,'PriceGroupItemPrice'	=> 'Item'
							,'AmountItemPrice'		=> $totalSF
							,'AmountRemaining'		=> $totalSF
							,'InputBy'				=> 'CMS-Queue-Udate'
							,'InputId'				=> '0'
							,'Status'				=> '201' // set as for billing stauts
						]);
					}
				}
			}
			else	// delete 
			{
				DB::connection('CMS')->table('Transactions')->where('IdQueue', $id)->where('CodeItemPrice', 'LIKE', 'SF')->where('Status','<=', 201)->delete();
			}
			
			$deptGroups = ErosDB::getItemMasterDeptGroup($idItemCodes);

			$packageItemCodes = DB::connection('Eros')
			->table('ItemMaster')
			->whereIn('Code', $idItemCodes)
			->where('Departmentgroup', 'Package')
			->pluck('Code')
			->merge(
				DB::connection('Eros')
					->table('ItemPrice')
					->whereIn('Code', $idItemCodes)
					->where('PriceGroup', 'Package')
					->pluck('Code')
			)
			->unique();
	
			if ($deptGroups->contains(fn($value) => strtolower($value) === 'package')) {
				$packageDeptGroups = ErosDB::getPackageDeptGroupMultiple($packageItemCodes);
				$deptGroups = $deptGroups->merge($packageDeptGroups);
			}
				
			
			$deptGroups = $deptGroups->reject(function ($value) {
				return $value === 'Package';
			});
			
			$deptGroupsArray = $deptGroups->map(function ($item) {
				if (is_string($item)) {
					return $item;
				}
				if (is_iterable($item)) {
					return collect($item)->map(function ($subItem) {
						return $subItem->DepartmentGroup ?? '';
					})->toArray();
				}
				if (is_object($item) && property_exists($item, 'DepartmentGroup')) {
					return $item->DepartmentGroup ?? '';
				}
				return '';
			})->flatten()->filter()->toArray();
	
			$existingStations = array_filter(
				explode(', ', Kiosk::where('IdQueueCMS', $id)->value('Station') ?: '')
			);
			
			$deptGroupsArray = array_unique(array_merge($existingStations, $deptGroupsArray));
			
			if (($key = array_search('CONSULTATION', $deptGroupsArray)) !== false) {
				$deptGroupsArray[$key] = 'VITAL';
			}
			
			$deptGroupsString = implode(', ', $deptGroupsArray);
			// dd($deptGroupsString);
			Kiosk::where('IdQueueCMS', $id)->update([
				'Station' => $deptGroupsString
			]);

		}
		DB::connection('CMS')->commit(); 
		return $id;
		}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
