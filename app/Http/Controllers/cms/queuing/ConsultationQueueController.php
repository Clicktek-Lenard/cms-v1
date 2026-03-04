<?php

namespace App\Http\Controllers\cms\queuing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\cms\Transactions;

use App\Models\cms\Queue;
use App\Models\cms\Kiosk;
use App\Models\eros\ErosDB;
use App\Models\cms\KioskPatient;
use App\Models\cms\ErosPatient;
use App\Models\cms\Counter;

class ConsultationQueueController extends Controller
{
    public function index(Request $request)
    {
        $station = 'consultation'; // Replace with the desired station
        $statuses = ['completed', 'in_progress', 'waiting', 'startQueue', 'on_hold', 'resume_queue']; // Replace with the desired status
    
        $queue = Kiosk::getQueueConsultation($station, $statuses, Auth::user()->AccessMapId);
    
        $ipAddress = $request->getClientIp();
        $counter = Counter::where('IPv4', $ipAddress)->where('Department', 'Consultation')->first();
    
        if ($counter) {
            $stationNumber = $counter->StationNumber;
            $department = $counter->Department;
    
            if ($department !== 'Consultation') {
                $message = 'Consultation Queue';
                $message2 = 'Access Denied: Department not authorized';
                return view('cms.error', ['message' => $message, 'message2' => $message2]);
            }
        } else {
            $stationNumber = null;
            $message = 'Consultation Queue';
            return view('cms.error', ['message' => $message]);
        }
	
	$docIn = DB::connection('Eros')->table('Physician')->where('EmpId', '=', Auth::user()->AccessMapId)->get(array('Id'));
	
	if(count($docIn) == 0)
	{
		return view('cms.queuing.consultationQueue', ['queue' => $queue, 'counter' => $counter])->withErrors(['error' => "Missing Physician EmpId Mapping, please contact ICT helpdesk"]);
	}
    
        return view('cms.queuing.consultationQueue', ['queue' => $queue, 'counter' => $counter]);
    }

    public function edit($id)
    {
        $clinics = ErosDB::getClinicData();
        $station = ['CLINIC', 'CONSULTATION'];
        $queue = Queue::todaysQueueID($id)->get(array('Queue.Id','Queue.Code','Eros.Patient.FullName','QueueStatus.Name as QueueStatus','QueueStatus.Id as QueueStatusId','Queue.InputBy','Queue.Notes','Queue.IdPatient','Patient.DOB as DOB',
            'Patient.Gender','Queue.IdBU as IdClinic','Queue.DateTime','Patient.Code as PatientCode','Queue.IdBU', 'Queue.AgePatient', 'Queue.PatientType'))[0];
        $trans = Transactions::getTransactionForRooms($id, $station);

        $packageItems = [];
        foreach ($trans as $transaction) {
            if (in_array($transaction->PriceGroupItemPrice, ['Package'])) {
                // Get package sub-items from the transaction
                $packageItems[$transaction->CodeItemPrice] = ErosDB::getPackageDeptGroupMultiple([$transaction->CodeItemPrice]);
            }
        }

        $canceltransaction = strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') !== false;

        $queueStatus = $queue->QueueStatusId; 

        //vitals 
        $vitalsData = DB::connection('CMS')->table('Vitals')->where('IdQueue', $id)->get(array('*'));

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
        //dd($queueStatus);
	    return view('cms.queuing.consultationQueueEdit', ['clinics' => $clinics, 'defaultClinic' => session('userClinicCode'), 'datas'=>$queue, 'postLink'=>'',  'trans' =>json_encode($trans), 'canceltransaction'=>$canceltransaction, 'queueStatus'=>$queueStatus, 'medication' => $medication, 'lastDose' => $lastDose, 'lastPeriod' => $lastPeriod, 'packageItems' => json_encode($packageItems)      ]);    
    }

    
}
