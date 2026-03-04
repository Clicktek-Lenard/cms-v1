<?php

namespace App\Http\Controllers\cms;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\cms\Transactions;
use App\Models\cms\PaymentHistory;
use App\Models\eros\ErosDB;

class PastPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
	$queue = Queue::todaysQueueStatus(array('201','205','210','300', '400', '500','230','280'))->get(array('CMS.Queue.Id','CMS.Queue.Code','Eros.Patient.FullName','CMS.QueueStatus.Name as QueueStatus','CMS.Queue.InputBy','CMS.Queue.Notes'));
	return view('cms.payment', ['queue' => $queue]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
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
	$queue = Queue::pastQueueIDStatus($id, array('201','205','210', '280' ,'300', '400', '500','230','280'))->get(array('Queue.Id','Queue.Code','Eros.Patient.FullName', 'Queue.QFullName','QueueStatus.Name as QueueStatus','Queue.InputBy','Queue.Notes','Queue.IdPatient','Patient.DOB',
		'Patient.Gender','Queue.IdBU as IdClinic','Queue.DateTime','Patient.Code as PatientCode','Queue.IdBU', 'Queue.AgePatient', 'Queue.PatientType', 'Queue.Status' ))[0];
	$trans = Transactions::getTransactionByQueue($id);
	$OR = count(PaymentHistory::getTransactionByStatus($id, 'PATIENT', 'LIKE'));
	$CS = count(PaymentHistory::getTransactionByStatus($id, 'PATIENT', 'NOT LIKE'));

	//return view('cms.pastPaymentEdit', [ 'OR' => $OR, 'CS' => $CS, 'clinics' => $clinics, 'defaultClinic' => session('userClinicCode'), 'datas'=>$queue, 'postLink'=>'',  'trans' =>json_encode($trans), 'forPU' => $forPU, 'forEmail' => $forEmail]);    
	//die();

	// $clinics = ErosDB::getClinicData();
	// $queue = Queue::todaysQueueIDStatus($id, array('201','205','210','300', '400', '500'))->get(array('Queue.Id','Queue.Code','Eros.Patient.FullName','QueueStatus.Name as QueueStatus','Queue.InputBy','Queue.Notes','Queue.IdPatient','Patient.DOB',
	// 	'Patient.Gender','Queue.IdBU as IdClinic','Queue.DateTime','Patient.Code as PatientCode','Queue.IdBU', 'Queue.AgePatient', 'Queue.PatientType'))[0];
	// $trans = Transactions::getTransactionByQueue($id);
	
	// $OR = count(PaymentHistory::getTransactionByStatus($id, 'PATIENT', 'LIKE'));
	// $CS = count(PaymentHistory::getTransactionByStatus($id, 'PATIENT', 'NOT LIKE'));

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
    
	return view('cms.pastPaymentEdit', [ 'OR' => $OR, 'CS' => $CS, 'clinics' => $clinics, 'defaultClinic' => session('userClinicCode'), 'datas'=>$queue, 'postLink'=>'',  'trans' =>json_encode($trans), 'forPU' => $forPU, 'forEmail' => $forEmail]);    
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
        //
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
