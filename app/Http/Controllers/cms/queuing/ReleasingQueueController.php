<?php

namespace App\Http\Controllers\cms\queuing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\cms\Queue;
use App\Models\cms\Kiosk;
use App\Models\eros\ErosDB;
use App\Models\cms\KioskPatient;
use App\Models\cms\ErosPatient;
use App\Models\cms\Counter;

class ReleasingQueueController extends Controller
{
    public function index(Request $request)
    {
        $station = 'releasing'; // Replace with the desired station
        $statuses = ['completed', 'in_progress','waiting','startQueue', 'on_hold', 'resume_queue'];     // Replace with the desired status
    
        $queue = Kiosk::getQueue($station, $statuses);
    
        $ipAddress = $request->getClientIp();
        $counter = Counter::where('IPv4', $ipAddress)->where('Department', 'Releasing')->first();
    
        if ($counter) {
            $stationNumber = $counter->StationNumber;
            $department = $counter->Department;
    
            if ($department !== 'Releasing') {
                $message = 'Releasing Queue';
                $message2 = 'Access Denied: Department not authorized';
                return view('cms.error', ['message' => $message, 'message2' => $message2]);
            }
        } else {
            $stationNumber = null;
            $message = 'Releasing Queue';
            return view('cms.error', ['message' => $message]);
        }
    
        return view('cms.queuing.releasingQueue', ['queue' => $queue, 'counter' => $counter]);
    }
    
    //FOR RELEASING ONLY EXIT AFTER CLICKING IN BUTTON
    public function exitQueue(Request $request)
    {
        $queueID = $request->input('queueID');

        Kiosk::where('Id', $queueID)->update([
            'Station' => 'exit'
        ]);
        return response()->json(['success' => true]);
    }
    

}
