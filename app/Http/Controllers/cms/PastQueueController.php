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
use App\Models\cms\Transactions;
use App\Models\eros\ErosDB;

use DataTables;

class PastQueueController extends Controller
{

	public function getList(Request $request)
	{
	
		//print_r($request->get('search')); die();
		$search_arr = $request->get('search');
		$searchValue = $search_arr['value']; // Search value
		
		if ($request->ajax()) {
			$queue = Queue::pastQueue($searchValue)->get(array('CMS.Queue.Id','CMS.Queue.Code','CMS.Queue.Date as QDate','Eros.Patient.FullName','CMS.Queue.QFullName','CMS.QueueStatus.Name as QueueStatus','CMS.Queue.InputBy','CMS.Queue.Notes'));
			return DataTables::of($queue)
			->toJson();
		}
	
	}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {	
		//#$queue = Queue::pastQueue()->get(array('CMS.Queue.Id','CMS.Queue.Code','CMS.Queue.Date as QDate','Eros.Patient.FullName','CMS.QueueStatus.Name as QueueStatus','CMS.Queue.InputBy','CMS.Queue.Notes'));
		//#return view('cms.pastQueue', ['queue' => $queue]);
		return view('cms.pastQueue');
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
        return view('cms.pastQueue', ['queue' => $queue]);
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
	$queue = Queue::pastQueueID($id)->get(array('Queue.Id','Queue.Code','Queue.AnteDate','Eros.Patient.FullName','CMS.Queue.QFullName','QueueStatus.Name as QueueStatus','Queue.InputBy','Queue.Notes', 'Queue.CancelReason','Queue.IdPatient','Patient.DOB',
		'Patient.Gender','Queue.IdBU as IdClinic','Queue.Date','Queue.DateTime','Patient.Code as PatientCode','Queue.IdBU', 'Queue.AgePatient', 'Queue.PatientType','Queue.Status'))[0];
	$trans = Transactions::getTransactionByQueue($id);

	$payHistory = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $id)->where('Status', 2)->get(array('IdQueue'));

	$forSpecimenStatus = DB::connection('CMS')->table('Queue')->where('Id', $id)->where('Status', 300)->value('Status');

	//dd($forSpecimenStatus);
	
	$transDeleted = DB::connection('CMS')->table('TransactionsDeleted')->where('IdQueue', $id)->get(array('IdQueue'));
        
	$disableButton = true;
	 
	$approveButtonDisabled = true; 

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
	else if( $queue->Status == 204)
	{
		$saveButton =  false;
	}

	if ($forSpecimenStatus == 300)
	{
		$hl7Btn = false;
	}

	$notifView = DB::connection('Notification')->table('Info')->where('ModuleId', $id)->get(array('PickUp', 'Email'));

	if( count($notifView) !=0 )
	{
		$forPU = $notifView[0]->PickUp;
		$forEmail = $notifView[0]->Email;
	}
	else
	{
		$forPU = "";
		$forEmail = "";
	}

	$cancelBT = "show";
    //dd($queue->Status);
	if( session('userClinicCode')  != $queue->IdBU || (!$checking = $this->CheckBranchAccess($queue->IdBU) && strpos(session('userRole'), '"ldap_role":"[RECEPTION-OIC]"') !== false) )
	{
		$cancelBT = "hidden";
	}
    $editor = "show";
	if(!$this->CheckBranchAccess($queue->IdBU) && strpos(session('userRole'), '"ldap_role":"[RECEPTION-OIC]"') !== false ) 
	{
		$editor = "hide";
	}
    $AccessRole = !$this->CheckBranchAccess($queue->IdBU) && strpos(session('userRole'), '"ldap_role":"[RECEPTION-OIC]"') !== false; 
	return view('cms.pastQueueEdit', ['clinics' => $clinics, 'cancelbt' => $cancelBT, 'defaultClinic' => session('userClinicCode'), 'datas'=>$queue, 'postLink'=>'', 'trans' =>json_encode($trans), 'editor' => $editor, 'AccessRole', $AccessRole, 'disableButton' => $disableButton, 'approveButtonDisabled' => $approveButtonDisabled, 'hl7Btn' => $hl7Btn, 'forPU' => $forPU, 'forEmail' => $forEmail]);    

    }


  
}
