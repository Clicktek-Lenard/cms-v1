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

class VitalSignsQueueController extends Controller
{
    public function index(Request $request)
    {
        $station = 'VITAL'; // Replace with the desired station
        $statuses = ['completed', 'in_progress', 'waiting', 'startQueue', 'on_hold', 'resume_queue']; // Replace with the desired status
    
        $queue = Kiosk::getQueueVitalSigns($station, $statuses);
    
        $ipAddress = $request->getClientIp();
        $counter = Counter::where('IPv4', $ipAddress)->where('Department', 'Vital Signs')->first();
    
        if ($counter) {
            $stationNumber = $counter->StationNumber;
            $department = $counter->Department;
    
            if ($department !== 'Vital Signs') {
                $message = 'Vital Signs Queue';
                $message2 = 'Access Denied: Department not authorized';
                return view('cms.error', ['message' => $message, 'message2' => $message2]);
            }
        } else {
            $stationNumber = null;
            $message = 'Vital Signs Queue';
            return view('cms.error', ['message' => $message]);
        }
    
        return view('cms.queuing.vitalSignsQueue', ['queue' => $queue, 'counter' => $counter]);
    }

    public function edit($id)
    {
        $clinics = ErosDB::getClinicData();
        $station = 'CONSULTATION';
        $queue = Queue::todaysQueueID($id)->get(array('Queue.Id','Queue.Code','Eros.Patient.FullName','QueueStatus.Name as QueueStatus','QueueStatus.Id as QueueStatusId','Queue.InputBy','Queue.Notes','Queue.IdPatient','Patient.DOB as DOB',
            'Patient.Gender','Queue.IdBU as IdClinic','Queue.DateTime','Patient.Code as PatientCode','Queue.IdBU', 'Queue.AgePatient', 'Queue.PatientType'))[0];
        $trans = Transactions::getTransactionForRooms($id, $station);
        $canceltransaction = strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') !== false;

        $queueStatus = $queue->QueueStatusId; 
        //dd($queueStatus);
	    return view('cms.doctor.vitalSignEdit', ['clinics' => $clinics, 'defaultClinic' => session('userClinicCode'), 'datas'=>$queue, 'postLink'=>'',  'trans' =>json_encode($trans), 'canceltransaction'=>$canceltransaction, 'queueStatus'=>$queueStatus]);    
    }

    
}
