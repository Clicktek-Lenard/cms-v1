<?php

namespace App\Http\Controllers\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\cms\Transactions;
use App\Models\cms\PaymentHistory;
use App\Models\eros\ErosDB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
	$queue = Queue::todaysQueueStatus(array('201','202','203','204','205','210','211','212','213','214','230','280','250','260','270','280','300','301', '400', '410','420','500','600', '650'))->get(array('CMS.Queue.Id','CMS.Queue.Code','Eros.Patient.FullName','CMS.QueueStatus.Name as QueueStatus','CMS.Queue.InputBy','CMS.Queue.Notes'));
	return view('cms.payment', ['queue' => $queue]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return 'create';
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
     return 'show';
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
	$queue = Queue::todaysQueueIDStatus($id, array('201','202','203','204','205','210','211','212','213','214','230','280','250','260','270','280','300','301', '400', '410','420','500','600', '650'))->get(array('Queue.Id','Queue.Code','Eros.Patient.FullName','QueueStatus.Name as QueueStatus','Queue.InputBy','Queue.Notes','Queue.IdPatient','Patient.DOB',
		'Queue.Status as Status', 'Patient.Gender','Queue.IdBU as IdClinic','Queue.DateTime','Patient.Code as PatientCode','Queue.IdBU', 'Queue.AgePatient', 'Queue.PatientType'))[0];
	$trans = Transactions::getTransactionByQueue($id);
	
	$OR = count(PaymentHistory::getTransactionByStatus($id, 'PATIENT', 'LIKE'));
	$CS = count(PaymentHistory::getTransactionByStatus($id, 'PATIENT', 'NOT LIKE'));
	
	$queueStatus = DB::connection('CMS')->table('Queue')->where('Id', $id)->value('Status');

	$receiptButton = true; 

	if ($queueStatus >= 210 && $queueStatus <= 600) {

		$receiptButton = false; // for the approve button
	}
	
	return view('cms.paymentEdit', [ 'OR' => $OR, 'CS' => $CS, 'clinics' => $clinics, 'defaultClinic' => session('userClinicCode'), 'datas'=>$queue, 'receiptButton' => $receiptButton,  'postLink'=>'',  'trans' =>json_encode($trans)  ]);    
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
