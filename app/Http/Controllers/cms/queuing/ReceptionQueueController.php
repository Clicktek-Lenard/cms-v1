<?php

namespace App\Http\Controllers\cms\queuing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use App\Models\cms\Queue;
use App\Models\cms\Kiosk;
use App\Models\eros\ErosDB;
use App\Models\cms\KioskPatient;
use App\Models\cms\KioskLog;
use App\Models\cms\ErosPatient;
use App\Models\cms\Counter;

use Carbon\Carbon;

class ReceptionQueueController extends Controller
{
    public function index(Request $request)
    {
        $station = 'reception'; // Replace with the desired station
        $statuses = ['completed', 'in_progress','waiting','startQueue', 'on_hold', 'resume_queue'];     // Replace with the desired status

        $queue = Kiosk::getQueue($station, $statuses);

        $ipAddress = $request->getClientIp();
        $counter = Counter::where('IPv4', $ipAddress)->where('Department', 'Reception')->first();

        if ($counter) {
            $stationNumber = $counter->StationNumber;
            $department = $counter->Department;
    
            if ($department !== 'Reception') {
                $message = 'Reception Queue';
                $message2 = 'Access Denied: Department not authorized';
                return view('cms.error', ['message' => $message, 'message2' => $message2]);
            }
        } else {
            $stationNumber = null;
            $message = 'Reception Queue';
            return view('cms.error', ['message' => $message]);
        }
    
        return view('cms.queuing.receptionQueue', ['queue' => $queue, 'counter' => $counter, 'queCount' => count($queue)]);
    }

    //TESTING AUTO FETCH FOR TABLE
    public function getQueueData(Request $request)
    {
        $station = $request->input('station');
        $statuses = ['completed', 'in_progress','waiting','startQueue', 'on_hold', 'resume_queue', 'next_room'];

        $queue = Kiosk::getQueue($station, $statuses);

        return response()->json($queue);
    }
    public function getQueueConsultationData(Request $request)
    {
        $station = $request->input('station');
        $statuses = ['completed', 'in_progress','waiting','startQueue', 'on_hold', 'resume_queue', 'next_room'];

        $docIn = DB::connection('Eros')->table('Physician')->where('EmpId', '=', Auth::user()->AccessMapId)->get(array('Id'));
	
        if(count($docIn) == 0)
        {
		echo "Missing Params";die();
        }

        $queue = Kiosk::getQueueConsultation($station, $statuses, $docIn[0]->Id );

        return response()->json($queue);
    }
    public function getQueueVitalsData(Request $request)
    {
        $station = $request->input('station');
        $statuses = ['completed', 'in_progress','waiting','startQueue', 'on_hold', 'resume_queue', 'next_room'];

        $queue = Kiosk::getQueueVitalSigns($station, $statuses);

        return response()->json($queue);
    }
    public function getQueueDataPaid(Request $request)
    {
        $station = $request->input('station');
        $statuses = ['completed', 'in_progress','waiting','startQueue', 'on_hold', 'resume_queue', 'next_room'];

        $queue = Kiosk::getQueuePaid($station, $statuses);

        return response()->json($queue);
    }

    public function getQueueImagingData(Request $request)
    {
        $stations = $request->input('stations');  // Receive the array of subgroups
        $statuses = ['completed', 'in_progress', 'waiting', 'startQueue', 'on_hold', 'resume_queue', 'next_room'];

        $queue = Kiosk::getImagingQueue($stations, $statuses);

        return response()->json($queue);
    }

    public function exitStation(Request $request)
    {
        $queueID = $request->input('queueID');
        $station = $request->input('station'); 
    
        // Find the record with the given queueID
        $kiosk = Kiosk::where('IdQueueCMS', $queueID)->first();
    
        if ($kiosk) {
            // Retrieve the current stations
            $stations = explode(', ', $kiosk->Station);
            
            // Remove the specified station from the list
            $stations = array_filter($stations, function($value) use ($station) {
                return $value !== $station;
            });
    
            // Check if there's only one station left
            if (count($stations) === 0) {
                // Update the record as 'exit' and status as 'complete'
                $kiosk->update([
                    'Station' => 'exit',
                    'Status' => 'complete',
                    'CurrentRoom' => 'Lobby'
                ]);
            } else {
                // Convert the list back to a comma-separated string
                $updatedStations = implode(', ', $stations);
        
                // Update the record with the modified stations
                $kiosk->update([
                    'Station' => $updatedStations, 
                    'Status' => 'next_room', 
                    'CurrentRoom' => 'Lobby',
                    'numOfCall' => '0'
                ]);
            }
            
            return response()->json(['success' => true]);
        }
    
        return response()->json(['success' => false, 'message' => 'Record not found']);
    }
    
    
    public function updateStatus(Request $request)
    {
        $queueID = $request->input('queueID');
        $status = $request->input('status'); // 'waiting' or your desired status
        $counter = $request->input('counter');
        $calledTo = $request->input('calledTo');

        $data = [
            'Status' => $status,
            'lastClick' => Auth::user()->username,
        ];

        // Only add 'CalledTo' if it is present in the request
        if (!is_null($calledTo)) {
            $data['CalledTo'] = $calledTo;
        }

        // Only add 'Counter' if it is present in the request
        if (!is_null($counter)) {
            $data['Counter'] = $counter;
        }

        if (!in_array($status, ['on_hold', 'resume_queue', 'in_progress'])) {
            Kiosk::where('Id', $queueID)->increment('numOfCall');
        }

        Kiosk::where('Id', $queueID)->update($data);
        // Kiosk::where('Id', $queueID)->update([
        //     'Status' => $status, 
        //     'lastClick' => Auth::user()->username,
        //     'Counter' => $counter
        // ]);
        return response()->json(['success' => true]);
    }

    public function updateCurrentRoom(Request $request)
    {
        $queueID = $request->input('queueID');
        $roomName = $request->input('roomName'); 

        Kiosk::where('Id', $queueID)->update([
            'CurrentRoom' => $roomName
        ]);
        return response()->json(['success' => true]);
    }
    
    public function insertPatientCode(Request $request)
    {
        $patientId = $request->input('IdPatient');
        $myDBId = Controller::getMyDBID();

        $newCode =  $this->genPatientCode();
            if( isset($newCode->PNum) )
        {
            $max = $newCode->PNum;
        }
        else
        {
            return  $newCode;
            die();
        }
        $Code = session('userClinicCode').$myDBId. date('ymd') . sprintf('%04d', $max++);
        
        $patient = KioskPatient::find($patientId);
        if (!empty($patient->Code)) {
            return response()->json(['success' => false, 'message' => 'Code already exists in KioskPatient.']);
        }
    
        // Update the status in KioskPatient
        KioskPatient::where('Id', $patientId)
            ->where(function ($query) {
                $query->whereNull('Code')  // Check if Code is null
                      ->orWhere('Code', ''); // or if Code is an empty string
            })
            ->update(['Code' => $Code]);
    
        $patient = KioskPatient::find($patientId);
    
        // Check if ErosPatient with the same FullName and DOB but different Code exists
        $erosPatient = ErosPatient::where('FullName', $patient->FullName)
            ->where('DOB', $patient->DOB)
            ->first();
    
            if ($erosPatient) {
                // dd('existing na bro');
                // If exists, return success without adding a new record
                $newerosPatient = new ErosPatient();
                $newerosPatient->fill($patient->toArray());
                $newerosPatient->Code = $Code; // Ensure Code is set correctly
                $newerosPatient->save();
                return response()->json(['success' => true, 'Id' => $newerosPatient->Id]);
            } else {
                // If does not exist, create a new record
                $erosPatient = new ErosPatient();
                $erosPatient->fill($patient->toArray());
                $erosPatient->Code = $Code; // Ensure Code is set correctly
                $erosPatient->save();
                
                // dd('ayan ni-write ko bro');
                return response()->json(['success' => true, 'Id' => $erosPatient->Id]);
            }
        }

    public function logAction(Request $request)
    {
        $actionby      = $request->input('actionBy');
        $room          = $request->input('room');
        $erosidpatient = $request->input('erosidpatient');
        $action        = $request->input('action');
        $queueno       = $request->input('queueno');
        $idpatient     = $request->input('idpatient');
        $kioskid       = $request->input('kioskid');
        
        $log = new KioskLog();
        $log -> KioskId       = $kioskid;
        $log -> IdPatient     = $idpatient;
        $log -> ErosPatientId = $erosidpatient;
        $log -> QueueNo       = $queueno;
        $log -> DateTime      = Carbon::now();
        $log -> Action        = $action;
        $log -> ActionBy      = $actionby;
        $log -> Room          = $room;
        $log -> save();

        return response()->json(['success' => true]);
    }

    // FOR VITAL TO CONSULT
    public function vitalToConsult(Request $request)
    {
        $queueID = $request->input('queueID');
    
        $kiosk = Kiosk::where('IdQueueCMS', $queueID)->first();
    
        if ($kiosk) {
            $stationArray = explode(', ', $kiosk->Station);
            
            foreach ($stationArray as &$value) {
                if (strtoupper($value) === 'VITAL') { // Check for 'VITAL' in uppercase
                    $value = 'CONSULTATION';
                }
            }
            unset($value); // Unset the reference
    
            // Update the station list
            $kiosk->Station = implode(', ', $stationArray);
    
            $kiosk->Status = 'next_room'; 
            $kiosk->CurrentRoom = 'Lobby'; 
            $kiosk->numOfCall = 0; 
    
            $kiosk->save();
    
            return response()->json(['success' => true, 'message' => 'Station updated', 'station' => $kiosk->Station]);
        }
    
        return response()->json(['success' => false, 'message' => 'Record not found']);
    }

}
