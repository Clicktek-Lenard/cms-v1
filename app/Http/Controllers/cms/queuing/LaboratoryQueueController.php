<?php

namespace App\Http\Controllers\cms\queuing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\cms\Transactions;
use App\Models\eros\ItemMasterList;
use App\Models\cms\Queue;
use App\Models\cms\Kiosk;
use App\Models\eros\ErosDB;
use App\Models\cms\KioskPatient;
use App\Models\cms\Receiving;
use App\Models\cms\ErosPatient;
use App\Models\cms\Counter;

class LaboratoryQueueController extends Controller
{
    public function index(Request $request)
    {
        $station = ['laboratory', 'drug test']; // Replace with the desired station
        $statuses = ['completed', 'in_progress', 'waiting', 'startQueue', 'on_hold', 'resume_queue', 'next_room']; // Replace with the desired status
    
        $queue = Kiosk::getQueuePaid($station, $statuses);

        $ipAddress = $request->getClientIp();
        $counter = Counter::where('IPv4', $ipAddress)->where('Department', 'Extraction')->first();
            
        if ($counter) {
            $stationNumber = $counter->StationNumber;
            $department = $counter->Department;
    
            if ($department !== 'Extraction') {
                $message = 'Extraction Queue';
                $message2 = 'Access Denied: Department not authorized';
                return view('cms.error', ['message' => $message, 'message2' => $message2]);
            }
        } else {
            $stationNumber = null;
            $message = 'Extraction Queue';
            return view('cms.error', ['message' => $message]);
        }
    
        return view('cms.queuing.laboratoryQueue', ['queue' => $queue, 'counter' => $counter, 'station' => $station]);
    }

    public function edit(Request $request, $id)
    {
        $department = $request->query('department');

        $clinics = ErosDB::getClinicData();
        $station = ($department === 'EXTRACTION') ? 'LABORATORY' : $department;
        
        $queue = Queue::todaysQueueID($id)->get(array('Queue.Id','Queue.Code','Eros.Patient.FullName','QueueStatus.Name as QueueStatus','QueueStatus.Id as QueueStatusId','Queue.InputBy','Queue.Notes','Queue.IdPatient','Patient.DOB as DOB',
            'Patient.Gender','Queue.IdBU as IdClinic','Queue.DateTime','Patient.Code as PatientCode','Queue.IdBU', 'Queue.AgePatient', 'Queue.PatientType'))[0];
        $trans = Transactions::getTransactionForRooms($id, $station);
        $kiosk = Kiosk::where('IdQueueCMS', $id)->first();

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
	    return view('cms.queuing.laboratoryQueueEdit', ['clinics' => $clinics, 'defaultClinic' => session('userClinicCode'), 'datas'=>$queue, 'postLink'=>'',  'trans' =>json_encode($trans), 'canceltransaction'=>$canceltransaction, 'queueStatus'=>$queueStatus, 'medication' => $medication, 'lastDose' => $lastDose, 'lastPeriod' => $lastPeriod, 'packageItems' => json_encode($packageItems), 'kioskData' => $kiosk, 'department' => $department, 'receivingStatus' => json_encode($receivingData) ]);    
    }

    
    public function receiveSpecimen(Request $request)
    {
        $idQueue = $request->input('idQueue');
        $selectedItems = $request->input('selectedItems');
        $receivedBy = Auth::user()->username;
    
        foreach ($selectedItems as $item) {
            $receiving = new Receiving();
            $receiving->IdQueue      = $idQueue;
            $receiving->IdBUFrom     = session('userClinicCode');
            $receiving->IdBUTo       = 'WALA';
            $receiving->IdTransaction= $item['id'];
            $receiving->ItemCode     = $item['code'];
            $receiving->PackageCode  = $item['packageCode'] ?? null; // Use packageCode if available, otherwise null
            $receiving->DateReceived = now();
            $receiving->ReceivedBy   = $receivedBy;
            $receiving->Notes        = $item['notes'] ?? '';
            $receiving->Status       = 'Received';
            $receiving->save();

            DB::connection('CMS')->table('AccessionNo')
                                 ->where('IdTransaction', $item['id'])
                                 ->where('ItemCode', $item['code'])
                                 ->update(['Status' => 301]);
            
            $allReceived = DB::connection('CMS')->table('AccessionNo')
                                                ->where('IdTransaction', $item['id'])  // Match the IdTransaction from selectedItems
                                                ->where('Status', '<>', 301)  // Check if any Status is not 301
                                                ->whereNotIn('ItemGroup', ['CLINIC'])
                                                ->doesntExist();  // If no entries with Status other than 301, all are 301

            // Update Transactions Status based on the result
            DB::connection('CMS')->table('Transactions')
                                 ->where('Id', $item['id'])
                                 ->update(['Status' => $allReceived ? 301 : 303]);
        }
    
        return response()->json(['message' => 'Specimens received successfully']);
    }
    public function holdReceivingRooms(Request $request)
    {
        $queueID = $request->input('queueID');
        $roomName = $request->input('roomName');
        $action = $request->input('action');

        $subgroupList = ItemMasterList::getItem(array('1'))->where('Group', 'IMAGING')->pluck('SubGroup')->values();
    
        $queue = Kiosk::where('Id', $queueID)->first();

        if (!$queue) {
            return response()->json(['success' => false, 'message' => 'Queue not found']);
        }
    
        // Ensure existing value is not null
        $onHoldRooms = $queue->OnHold ? explode(', ', $queue->OnHold) : [];
    
        if ($action === 'hold') {
            // Add room if not already in the list
            if (!in_array($roomName, $onHoldRooms)) {
                $onHoldRooms[] = $roomName;
            }

            // if ($queue->Status === 'in_progress') {
            //     $queue->Status = 'next_room';
            // }

            if ($queue->CurrentRoom === $roomName && $queue->Status === 'in_progress') {
                // dd('1');
                $queue->CurrentRoom = 'Lobby';
                $queue->Status = 'next_room';
            } else if ($roomName === 'IMAGING' && $subgroupList->contains($queue->CurrentRoom) && $queue->Status === 'in_progress') {
                // dd('2');
                $queue->CurrentRoom = 'Lobby';
                $queue->Status = 'next_room';
            } 
            
        } elseif ($action === 'resume') {
            // Remove the room if it exists
            $onHoldRooms = array_filter($onHoldRooms, fn($room) => $room !== $roomName);
        }
    
        // Convert array to string, remove unnecessary spaces
        $updatedOnHold = implode(', ', array_filter($onHoldRooms));
    
        // Update the database with the modified OnHold value
        $queue->OnHold = $updatedOnHold ?: null; // Set to null if empty
        $queue->save();
    
        return response()->json(['success' => true, 'OnHold' => $updatedOnHold]);
    }
}

