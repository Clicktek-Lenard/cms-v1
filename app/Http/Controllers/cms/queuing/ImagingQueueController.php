<?php

namespace App\Http\Controllers\cms\queuing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\eros\ItemMasterList;
use App\Models\cms\Transactions;

use App\Models\cms\Queue;
use App\Models\cms\Kiosk;
use App\Models\eros\ErosDB;
use App\Models\cms\KioskPatient;
use App\Models\cms\Receiving;
use App\Models\cms\ErosPatient;
use App\Models\cms\Counter;

class ImagingQueueController extends Controller
{
    public function index(Request $request)
    {
        // $station = 'imaging'; // Replace with the desired station
        $statuses = ['completed', 'in_progress', 'waiting', 'startQueue', 'on_hold', 'resume_queue']; // Replace with the desired status
        $subgroupList = ItemMasterList::getItem(array('1'))->where('Group', 'IMAGING')->pluck('SubGroup')->values();

        $queue = Kiosk::getImagingQueue($subgroupList, $statuses);

        $ipAddress = $request->getClientIp();
        $counter = Counter::where('IPv4', $ipAddress)->where('Department', 'Imaging')->first();
    
        if ($counter) {
            $stationNumber = $counter->StationNumber;
            $department = $counter->Department;
    
            if ($department !== 'Imaging') {
                $message = 'Imaging Queue';
                $message2 = 'Access Denied: Department not authorized';
                return view('cms.error', ['message' => $message, 'message2' => $message2]);
            }
        } else {
            $stationNumber = null;
            $message = 'Imaging Queue';
            return view('cms.error', ['message' => $message]);
        }
    
        return view('cms.queuing.imagingQueue', ['queue' => $queue, 'counter' => $counter, 'subgroupList' => $subgroupList]);
    }
    
    public function edit(Request $request, $id)
    {
        $imagingDepartment = explode(',', $request->query('department'));

        $clinics = ErosDB::getClinicData();
        $ipAddress = $request->getClientIp();
        $counter = Counter::where('IPv4', $ipAddress)->where('Department', 'Imaging')->pluck('Location')->toArray();
        $station = $imagingDepartment;
        $queue = Queue::todaysQueueID($id)->get(array('Queue.Id','Queue.Code','Eros.Patient.FullName','QueueStatus.Name as QueueStatus','QueueStatus.Id as QueueStatusId','Queue.InputBy','Queue.Notes','Queue.IdPatient','Patient.DOB as DOB',
            'Patient.Gender','Queue.IdBU as IdClinic','Queue.DateTime','Patient.Code as PatientCode','Queue.IdBU', 'Queue.AgePatient', 'Queue.PatientType'))[0];
        $trans = Transactions::getImagingTransactionForRooms($id, $station);

        $packageItems = [];
        foreach ($trans as $transaction) {
            if (in_array($transaction->PriceGroupItemPrice, ['Package'])) {
                // Get package sub-items from the transaction
                $packageItems[$transaction->CodeItemPrice] = ErosDB::getPackageDeptGroupMultiple([$transaction->CodeItemPrice]);
            }
        }
        
        $canceltransaction = strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') !== false;

        $receivingData = Receiving::where('IdQueue', $id)->get();

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
	    return view('cms.queuing.imagingQueueEdit', ['clinics' => $clinics, 'defaultClinic' => session('userClinicCode'), 'datas'=>$queue, 'postLink'=>'',  'trans' =>json_encode($trans), 'canceltransaction'=>$canceltransaction, 'queueStatus'=>$queueStatus, 'medication' => $medication, 'lastDose' => $lastDose, 'lastPeriod' => $lastPeriod, 'packageItems' => json_encode($packageItems), 'imagingDepartment' => $imagingDepartment, 'stations' => $station, 'receivingStatus' => json_encode($receivingData)]);    
    }
}
